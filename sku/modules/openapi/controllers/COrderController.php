<?php

/**
 * 盖付通接口控制器
 *
 * @author leo8705
 *
 */
class COrderController extends COpenAPIController {

    /**
     * 检查会员消费金额是否超过店铺每日限额
     */
    private function _getMemberToStoreAmount($sid=0,$add_amount=0,$type=null){

        $amount = Order::getMemberTodayAmount($this->member,$sid,$type);
        $limit_config = Tool::getConfig('amountlimit');

        $store = null;
        if ($type==Order::TYPE_SUPERMARK && !empty($sid)) {
            $store = Yii::app()->db->createCommand()->select('max_amount_preday')->from(Supermarkets::model()->tableName())->where('id=:id',array(':id'=>$sid))->queryRow();
        }

        if ($type==Order::TYPE_MACHINE && !empty($sid)) {
            $store = Yii::app()->db->createCommand()->select('max_amount_preday')->from(VendingMachine::model()->tableName())->where('id=:id',array(':id'=>$sid))->queryRow();
        }

        if ($type==Order::TYPE_FRESH_MACHINE && !empty($sid)) {
            $store = Yii::app()->db->createCommand()->select('max_amount_preday')->from(FreshMachine::model()->tableName())->where('id=:id',array(':id'=>$sid))->queryRow();
        }

//        $max_amount = isset($store['max_amount_preday'])&&$store['max_amount_preday']>0?$store['max_amount_preday']:$limit_config['memberTotalPayPreStoreLimit'];
        $max_amount = $limit_config['memberTotalPayPreStoreLimit'];

        //加上准备消费的金额 看是否超过
        if (!empty($add_amount)) {
            $amount +=  $add_amount;
        }

        $isOver=false;
        if ($amount>=$max_amount&&$max_amount>0) {
            $isOver = true;
        }

        $isPointOver = false;
        $point_amount = Order::getMemberTodayPointPayAmount($this->member,$sid,$type);
        if ($point_amount>$limit_config['memberPointPayPreStoreLimit']) {
            $isPointOver = true;
        }

        return  array('amount'=>$amount,'max_amount'=>$max_amount,'isOver'=>$isOver,'point_amount'=>$point_amount,'max_point_amount'=>$limit_config['memberPointPayPreStoreLimit'],'isPointOver'=>$isPointOver);
    }

    /**
     * 检查门店订单金额是否超过限额
     */
    public function actionCheckAmount(){
        try{
            $this->params = array('token','sid','type');
            $requiredFields = array('token','sid');
            $decryptFields = array('token','sid','type');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $sid = $post['sid'];				//门店id
            $type = isset($post['type'])?$post['type']:Order::TYPE_SUPERMARK;
            $amount_rs = $this->_getMemberToStoreAmount($sid,null,$type);
            $limit_config = Tool::getConfig('amountlimit');
                                   if($limit_config['isEnable']){
                                       $amount_rs['amountlimit'] = true;//后台限额开启
                                   }else{
                                       $amount_rs['amountlimit'] = false;//后台限额禁用
                                   }
            $this->_success($amount_rs);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }


    /**
     * 获取订单列表
     *
     *
     */
    public function actionList() {

        try{
            $this->params = array('token','orderType','page','pageSize','lastId');
            $requiredFields = array('token');
            $decryptFields = array('token','orderType');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $order_type = Order::TYPE_SUPERMARK;	//	开放接口仅支持超市门店
            $page = isset($post['page'])?$post['page']:1;
            $pageSize = isset($post['pageSize'])?$post['pageSize']:20;
            //lastId 上条记录id
            $lastId = isset($post['lastId'])?$post['lastId']*1:-1;
            $cri = new CDbCriteria();
            $cri->select = 't.id,t.code,t.goods_code,t.partner_id,t.member_id,t.store_id,t.machine_id,t.type,t.total_price,t.pay_price,t.address_id,t.shipping_type,t.pay_status,t.status,t.create_time,t.shipping_fee,t.shipping_time,t.remark,t.machine_take_type';
            $cri->with = array('machine','store','freshMachine');
            $cri->compare('t.member_id', $this->member);
            $cri->addCondition('t.father_id=0');
            if ($lastId>0) {
                $cri->addCondition('t.id<'.$lastId);
            }


            if (!empty($order_type)) {
                $cri->compare('t.type', $order_type);
            }

            //分页
            $cri->limit = $pageSize;
            $cri->offset = ($page - 1) * $pageSize;
            $cri->order = 't.id DESC';

            $list = Order::model()->findAll($cri);



            $order_ids = array();
            $list_data = array();

            if ($list) {
                foreach ($list as $o) {
                    $temp_arr = $o->attributes;
                    $temp_arr['machine_name'] =  isset($o->machine->name)?$o->machine->name:'';
                    $temp_arr['fresh_machine_name'] =  isset($o->freshMachine->name)?$o->freshMachine->name:'';
                    $temp_arr['store_name'] =  isset($o->store->name)?$o->store->name:'';
                    $temp_arr['type_name'] = Order::type($temp_arr['type']);
                    $temp_arr['pay_status_name'] = Order::payStatus($temp_arr['pay_status']);
                    $temp_arr['status_name'] = Order::status($temp_arr['status']);
                    $temp_arr['store_logo'] =  isset($o->store->logo)?ATTR_DOMAIN.DS.$o->store->logo:'';
                    $temp_arr['machine_logo'] =  isset($o->machine->thumb)?ATTR_DOMAIN.DS.$o->machine->thumb:'';
                    $temp_arr['machine_address']= isset($o->machine->address)?$o->machine->address:'';
                    $temp_arr['fresh_machine_logo'] =  isset($o->freshMachine->thumb)?ATTR_DOMAIN.DS.$o->freshMachine->thumb:'';
                    $temp_arr['fresh_machine_address']= isset($o->freshMachine->address)?$o->freshMachine->address:'';
                    $temp_arr['store_address']=isset($o->store->street)?$o->store->street:'';
                    $list_data[$o->id] = $temp_arr;
                    $order_ids[$o->id] = $o->id;
                }

                //查询订单商品数量
                $goods_rs = yii::app()->db->createCommand(' SELECT sum(num) as goods_count,order_id FROM '.OrdersGoods::model()->tableName().' WHERE order_id IN ('.implode(',', $order_ids).') GROUP BY order_id ')->queryAll();

                foreach ($goods_rs as $val){
                    $list_data[$val['order_id']]['goods_count'] = $val['goods_count']*1;
                }

                //补回商品数量
                foreach ($list_data as $k=> $val){
                    if (!isset($val['goods_count'])) {
                        $list_data[$k]['goods_count'] = 0;
                    }
                }

                $list_data = array_values($list_data);
            }
            $data = array();
            $data['list'] = $list_data;

            $count_con = 'member_id=:member_id';
            $count_prama = array(':member_id'=>$this->member);
            if (!empty($order_type)) {
                $count_con .= ' AND type=:type';
                $count_prama[':type'] = $order_type;
            }

            $total_count = Order::model()->count($count_con,$count_prama);
            $data['listCount'] = $total_count;
            $data['lastId'] = $lastId;
            $data['server_time'] = time();

            $this->_success($data);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }

    /**
     * 创建订单
     *
     * goods_id 用逗号分开
     *
     */
    public function actionCreate() {

        try{
            $this->params = array('token','goods','sid','addressId','shippingType','shippingTime','machineTakeType','remark');
            $requiredFields = array('token','goods','sid');
            $decryptFields = array('token','goods','sid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $sid = $post['sid'];        //门店id
            $type = Order::TYPE_SUPERMARK;       //订单类型  1为门店  2为售货机  3为生鲜机  4为售货机格子铺
            $goods = $post['goods'];     //商品id 多个商品用逗号分隔
            $address_id = isset($post['addressId'])?$post['addressId']:0;
            $shipping_type = isset($post['shippingType'])?$post['shippingType']:0;
            $shipping_time = isset($post['shippingTime'])?$post['shippingTime']:'';
            $machineTakeType = isset($post['machineTakeType'])?$post['machineTakeType']*1:0;
            $remark = isset($post['remark'])?$post['remark']:'';


            $goods_arr = CJSON::decode(str_replace('\"', '"', $goods));   //商品列表

//         if ($type==Order::TYPE_SUPERMARK) {
            $limit_config = Tool::getConfig('amountlimit');
            if ($limit_config['isEnable']) {
                $amount_rs = $this->_getMemberToStoreAmount($sid,null,$type);
                if ($amount_rs['isOver']==true) {
                    $this->_error(Yii::t('order','您的当日消费金额已超过每日最大限额，请明天再消费。'), ErrorCode::ORDER_OVER_MAX_AMOUNT_PREDAY_ERROR);
                }
            }

//         }
            //处理格仔铺数据格式
            if ($type==Order::TYPE_MACHINE_CELL_STORE) {
                $ids = array();
                $num_arr = array();
                foreach ($goods_arr as $val){
                    $ids[] = $val['id'];
                    $num_arr[$val['id']] = $val['num'];
                }
                $goods_list = Yii::app()->db->createCommand()
                    ->from(VendingMachineCellStore::model()->tableName())
                    ->where('machine_id=:machine_id AND  code IN ("'.implode('","', $ids).'")',array(":machine_id"=>$sid))
                    ->select('id,code')
                    ->queryAll();

                if (empty($goods_list)) {
                    $this->_error(Yii::t('order','商品无效,'));
                }

                $goods_arr = array();
                foreach ($goods_list as $val){
                    $temp_arr = array();
                    $temp_arr['id'] = $val['id'];
                    $temp_arr['num'] = $num_arr[$val['code']];
                    $goods_arr[] = $temp_arr;
                }

            }


            //下订单
            $order_rs = Order::model()->createOrder($type, $sid, $this->member, $goods_arr, $address_id, $shipping_type,$shipping_time,$machineTakeType,$remark);
            if ($order_rs['success']==true) {
                $order = $order_rs['data'];
                $msg = '您好，你的微小企订单['.$order['code'].']买家已下单，请留意最新情况。';
                //$this->sendJPush($order['code'],$msg);
                $this->_success(array('id' => $order['id'], 'code' => $order['code'],'goods_code' => $order['goods_code'],'create_time' => $order['create_time'],'amount' => $order['total_price'],'store_name'=>$order['store_name']));
            } else {

                $this->_error(Yii::t('order','下单失败,').ErrorCode::getErrorStr($order_rs['code']),$order_rs['code']);
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }

    /**
     * 订单信息
     *
     */
    public function actionDetail() {
        try{
            $this->params = array('token','code');
            $requiredFields = array('token','code');
            $decryptFields = array('token','code');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $code = $post['code'];        //订单编号
            $data = Order::model()->getDetailByCode($code);
            if (empty($data))
                $this->_error(Yii::t('order','订单不存在'));
            $this->_chenck($data->member_id);

            $rs['detail'] = $data->attributes;
            $rs['orderGoods'] = array();
            $rs['storeInfo'] = array();
            if (!empty($data->store)) {
                $rs['storeInfo']['id'] = $data->store->id;
                $rs['storeInfo']['store_name'] = $data->store->name;
                $rs['storeInfo']['mobile'] = $data->store->mobile;
                $rs['storeInfo']['logo'] = ATTR_DOMAIN.'/'.$data->store->logo;
                $rs['storeInfo']['type'] = $data->store->type;
                $rs['storeInfo']['mobile'] = $data->store->mobile;
                $rs['storeInfo']['category_id'] = $data->store->category_id;
                $rs['storeInfo']['category_name'] = StoreCategory::getCategoryName($data->store->category_id);
                $rs['storeInfo']['province_id'] = $data->store->province_id;
                $rs['storeInfo']['city_id'] = $data->store->city_id;
                $rs['storeInfo']['district_id'] = $data->store->district_id;
                $rs['storeInfo']['street'] = $data->store->street;
                $rs['storeInfo']['zip_code'] = $data->store->zip_code;
                $rs['storeInfo']['is_delivery'] = $data->store->is_delivery;
                $rs['storeInfo']['delivery_mini_amount'] = $data->store->delivery_mini_amount;
                $rs['storeInfo']['delivery_fee'] = $data->store->delivery_fee;
                $rs['storeInfo']['lng'] = $data->store->lng;
                $rs['storeInfo']['lat'] = $data->store->lat;
                $rs['storeInfo']['open_time'] = $data->store->open_time;
                $rs['storeInfo']['province_name'] = Region::getName($data->store->province_id);
                $rs['storeInfo']['city_name'] = Region::getName($data->store->city_id);
                $rs['storeInfo']['district_name'] = Region::getName($data->store->district_id);
            }


            if (!empty($data->machine)) {
                $rs['storeInfo']['id'] = $data->machine->id;
                $rs['storeInfo']['code'] = $data->machine->code;
                $rs['storeInfo']['thumb'] = ATTR_DOMAIN.'/'.$data->machine->thumb;
                $rs['storeInfo']['category_id'] = $data->machine->category_id;
                $rs['storeInfo']['category_name'] = StoreCategory::getCategoryName($data->machine->category_id);
                $rs['storeInfo']['machine_name'] = $data->machine->name;
                $rs['storeInfo']['mobile'] = $data->machine->mobile;
                $rs['storeInfo']['symbol'] = $data->machine->symbol;
                $rs['storeInfo']['country_id'] = $data->machine->country_id;
                $rs['storeInfo']['province_id'] = $data->machine->province_id;
                $rs['storeInfo']['city_id'] = $data->machine->city_id;
                $rs['storeInfo']['district_id'] = $data->machine->district_id;
                $rs['storeInfo']['address'] = $data->machine->address;
                $rs['storeInfo']['lng'] = $data->machine->lng;
                $rs['storeInfo']['lat'] = $data->machine->lat;
                $rs['storeInfo']['country_name'] = Region::getName($data->machine->country_id);
                $rs['storeInfo']['province_name'] = Region::getName($data->machine->province_id);
                $rs['storeInfo']['city_name'] = Region::getName($data->machine->city_id);
                $rs['storeInfo']['district_name'] = Region::getName($data->machine->district_id);

            }

            if (!empty($data->freshMachine)) {
                $rs['storeInfo']['id'] = $data->freshMachine->id;
                $rs['storeInfo']['code'] = $data->freshMachine->code;
                $rs['storeInfo']['thumb'] = ATTR_DOMAIN.'/'.$data->freshMachine->thumb;
                $rs['storeInfo']['category_id'] = $data->freshMachine->category_id;
                $rs['storeInfo']['category_name'] = StoreCategory::getCategoryName($data->freshMachine->category_id);
                $rs['storeInfo']['machine_name'] = $data->freshMachine->name;
                $rs['storeInfo']['mobile'] = $data->freshMachine->mobile;
                $rs['storeInfo']['symbol'] = $data->freshMachine->symbol;
                $rs['storeInfo']['country_id'] = $data->freshMachine->country_id;
                $rs['storeInfo']['province_id'] = $data->freshMachine->province_id;
                $rs['storeInfo']['city_id'] = $data->freshMachine->city_id;
                $rs['storeInfo']['district_id'] = $data->freshMachine->district_id;
                $rs['storeInfo']['address'] = $data->freshMachine->address;
                $rs['storeInfo']['lng'] = $data->freshMachine->lng;
                $rs['storeInfo']['lat'] = $data->freshMachine->lat;
                $rs['storeInfo']['country_name'] = Region::getName($data->freshMachine->country_id);
                $rs['storeInfo']['province_name'] = Region::getName($data->freshMachine->province_id);
                $rs['storeInfo']['city_name'] = Region::getName($data->freshMachine->city_id);
                $rs['storeInfo']['district_name'] = Region::getName($data->freshMachine->district_id);

            }

            $rs['storeInfo']['stype'] = $data['type'];

            if (!empty($data->ordersGoods)) {
                $gids = array();
                $garr = array();
                foreach ($data->ordersGoods as $g) {
                    if (is_object($g)) {
                        $gids[] = $g['gid'];
                        $garr[$g['gid']] = $g->attributes;
                    }


                }

                $gcri = new CDbCriteria();
                $gcri->select = 'id,thumb';
                $gcri->addInCondition('id', $gids);
                $goods_list = Goods::model()->findAll($gcri);

                foreach ($goods_list as $o) {
                    $o->thumb = ATTR_DOMAIN . DS . $o->thumb;
                    $temp_arr =$o->attributes;
                    $temp_arr['num'] = $garr[$o->id]['num'];
                    $temp_arr['price'] = $garr[$o->id]['price'];
                    $temp_arr['name'] = $garr[$o->id]['name'];
//                 $temp_arr['total_price'] = $garr[$o->id]['total_price'];
                    $rs['orderGoods'][] = $temp_arr;
                }
            }

            $rs['detail']['type_name'] = Order::type($rs['detail']['type']);
            $rs['detail']['pay_status_name'] = Order::payStatus($rs['detail']['pay_status']);
            $rs['detail']['status_name'] = Order::status($rs['detail']['status']);

            $rs['server_time'] = time();

            //收货地址
            $address = OrderAddress::model()->findByPk($data->address_id);
            if (!empty($address)) {
                $address = $address->attributes;
                $address['province_name'] = Region::getName($address['province_id']);
                $address['city_name'] = Region::getName($address['city_id']);
                $address['district_name'] = Region::getName($address['district_id']);
            }
            $rs['address'] = $address;

            $this->_success($rs);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }

    /**
     * 取消订单
     *
     * 如果已支付订单会有退款以及相关流水
     *
     * 未支付的订单直接回滚
     *
     *
     */
    public function actionCancel() {
        try{
            $this->params = array('token','code','remark');
            $requiredFields = array('token','code');
            $decryptFields = array('token','code');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $code = $post['code'];        //订单编号
            $remark = isset($post['remark'])?$post['remark']:'';
            $order = Order::model()->getByCode($code);
            if (empty($order)) {
                $this->_error(Yii::t('order','订单不存在'));
            }
            $this->_chenck($order->member_id);

            if ($order->status == Order::STATUS_FROZEN) {
                $this->_error(Yii::t('order','订单已冻结'));
            }

            $rs = Order::orderCancel($code,true,$remark);
            if ($rs['success'] != true) {
                $this->_error(ErrorCode::getErrorStr($rs['code']), $rs['code']);
            }
            $this->_success(Yii::t('order','取消成功'));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }

    /**
     * 签收订单
     *
     */
    public function actionComplete() {
        try{
            $this->params = array('token','code');
            $requiredFields = array('token','code');
            $decryptFields = array('token','code');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $code = $post['code'];        //订单编号
            $order = Order::model()->getByCode($code);
            if (empty($order)) {
                $this->_error(Yii::t('order','订单不存在'));
            }
            $this->_chenck($order->member_id);

            if ($order->status == Order::STATUS_FROZEN) {
                $this->_error(Yii::t('order','订单已冻结'));
            }

            if ($order->status != Order::STATUS_PAY  && $order->status != Order::STATUS_SEND ) {
                $this->_error(Yii::t('order','此时不能签收订单！'),ErrorCode::ORDER_STATUS_FAIL);
            }

            $sign_rs = Order::orderSign($code, true);

            if ($sign_rs['success'] != true) {
                $this->_error(ErrorCode::getErrorStr($sign_rs['code']), $sign_rs['code']);
            }
            $this->_success(Yii::t('order','签收成功'));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }

    /**
     * 退单
     * 申请退款
     *
     */
    public function actionRefund() {
        try{
            $this->params = array('token','code');
            $requiredFields = array('token','code');
            $decryptFields = array('token','code');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $code = $post['code'];        //订单编号
            $order = Order::model()->getDetailByCode($code);
            if (empty($order)) {
                $this->_error(Yii::t('order','订单不存在！'));
            }
            $this->_chenck($order->member_id);

            if ($order->status == Order::STATUS_FROZEN) {
                $this->_error(Yii::t('order','订单已冻结'));
            }

            if ($order->status != Order::STATUS_PAY) {
                $this->_error(Yii::t('order','此时不能申请退款'), ErrorCode::COMMON_NORMAL);
            }


            //处理流水
            $order->status = order::STATUS_REFUNDING;
            $order->save();
            $this->_success(Yii::t('order','申请退款成功,订单退款中'));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }

    /**
     * 完成订单后评价商品
     *
     */
    public function actionGoodsEvaluation() {
        try{
            $this->params = array('token','code','goodsId','content','score','serviceScore','qualityScore');
            $requiredFields = array('token','code','goodsId','content','score','serviceScore','qualityScore');
            $decryptFields = array('token','code');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $code = $post['code'];        //订单编号
            $sgid = $post['goodsId']*1;
            $content = $post['content'];
            $score = $post['score']*1;
            $service_score = $post['serviceScore']*1;
            $quality_score = $post['qualityScore']*1;

            if ($score>GoodsComment::MAX_SCORE || $score<GoodsComment::MIN_SCORE) {
                $this->_error(Yii::t('order','综合评分只能在{min}~{max}之间',array('{min}'=>GoodsComment::MIN_SCORE,'{max}'=>GoodsComment::MAX_SCORE)), ErrorCode::COMMOM_ERROR);
            }

            if ($service_score>GoodsComment::MAX_SCORE || $service_score<GoodsComment::MIN_SCORE) {
                $this->_error(Yii::t('order','服务评分只能在{min}~{max}之间',array('{min}'=>GoodsComment::MIN_SCORE,'{max}'=>GoodsComment::MAX_SCORE)), ErrorCode::COMMOM_ERROR);
            }

            if ($quality_score>GoodsComment::MAX_SCORE || $quality_score<GoodsComment::MIN_SCORE) {
                $this->_error(Yii::t('order','商品质量评分只能在{min}~{max}之间',array('{min}'=>GoodsComment::MIN_SCORE,'{max}'=>GoodsComment::MAX_SCORE)), ErrorCode::COMMOM_ERROR);
            }


            $order = Order::model()->getByCode($code);

            if (empty($order)) {
                $this->_error(Yii::t('order','订单不存在！'), ErrorCode::COMMOM_ERROR);
            }

            $this->_chenck($order->member_id);

            if ($order->status != Order::STATUS_COMPLETE) {
                $this->_error(Yii::t('order','订单未完成，不能评价!'));
            }

            $goods = OrdersGoods::model()->find('sgid=:sgid AND order_id=:order_id',array(':sgid'=>$sgid,':order_id'=>$order['id']));

            if (empty($goods)) {
                $this->_error(Yii::t('order','商品不对应！'), ErrorCode::COMMOM_ERROR);
            }

            if ($order->status != Order::STATUS_COMPLETE) {
                $this->_error(Yii::t('order','此时不能评价商品'), ErrorCode::COMMOM_ERROR);
            }

            $count = GoodsComment::model()->count('goods_id=:goods_id AND order_id=:order_id',array(':goods_id'=>$goods['gid'],':order_id'=>$order['id']));

            if ($count) {
                $this->_error(Yii::t('order','不能重复评价商品'), ErrorCode::COMMOM_ERROR);
            }

            $trans  = Yii::app()->db->beginTransaction();

            $commont = new GoodsComment();
            $commont->member_id = $this->member;
            $commont->partner_id = $order->partner_id;
            $commont->content = $content;
            $commont->score = $score;
            $commont->service_score = $service_score;
            $commont->quality_score = $quality_score;
            $commont->create_time = time();
            $commont->goods_id = $goods->gid;
            $commont->store_goods_id = $goods->sgid;
            $commont->order_id = $order->id;
            if ($commont->save()) {
                //计算商品综合评分
                $goods_rs = Yii::app()->db->createCommand(' UPDATE '.Goods::model()->tableName().' SET
					score=(select AVG(quality_score) FROM '.GoodsComment::model()->tableName().' WHERE goods_id='.$goods['gid'].' )
					WHERE id='.$goods['gid'])->execute();

                //计算店家综合评分
                if ($order->type==Order::TYPE_SUPERMARK) {
                    $partner_rs = Yii::app()->db->createCommand(' UPDATE '.Partners::model()->tableName().' SET
					score=(select AVG(t.service_score) FROM '.GoodsComment::model()->tableName().' AS t LEFT JOIN '.Goods::model()->tableName().' as g ON t.goods_id = g.id WHERE g.partner_id='.$order->partner_id.'  AND t.goods_id='.$goods['gid'].' )
					WHERE id='.$order['partner_id'])->execute();

                    //计算门店评分
                    $store_rs = Yii::app()->db->createCommand(' UPDATE '.Supermarkets::model()->tableName().' SET
					score=(select AVG(t.score) FROM '.GoodsComment::model()->tableName().' AS t LEFT JOIN '.Goods::model()->tableName().' as g ON t.goods_id = g.id WHERE g.partner_id='.$order->partner_id.'  AND t.goods_id='.$goods['gid'].' )
					WHERE id='.$order['store_id'])->execute();
                }

                $trans->commit();
                $this->_success(Yii::t('order','评价成功').$goods_rs);
            }else{
                $trans->rollback();
                $this->_error(Yii::t('order','评价失败'));
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };



    }

    /**
     * 检查商品
     *
     * 检查订单中是否包含已下架或已失效的商品
     *
     *
     */
    public function actionCheckGoods(){
        try{
            $this->params = array('token','code');
            $requiredFields = array('token','code');
            $decryptFields = array('token','code');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $code = $post['code'];        //订单编号
            $order = Order::getByCode($code);
            if (empty($order)) {
                $this->_error(Yii::t('order','订单不存在'));
            }

            if ($order['member_id']!==$this->member) {
                $this->_error(Yii::t('order','非法操作'));
            }

            $g_data = array();
            if ($order['type']==Order::TYPE_SUPERMARK) {
                $g_data = Yii::app()->db->createCommand()
                    ->select('count(1) as count')
                    ->from(SuperGoods::model()->tableName().' as sg')
                    ->leftJoin(Goods::model()->tableName().' as g', 'sg.goods_id=g.id')
                    ->leftJoin(OrdersGoods::model()->tableName().' as og', 'og.sgid=sg.id')
                    ->leftJoin(Order::model()->tableName().' as o', 'og.order_id=o.id')
                    ->where('o.code= "'.$code.'" AND sg.status!='.SuperGoods::STATUS_ENABLE )
                    ->queryRow();
            }elseif ($order['type']==Order::TYPE_MACHINE) {
                $g_data = Yii::app()->db->createCommand()
                    ->select('count(1) as count')
                    ->from(VendingMachineGoods::model()->tableName().' as sg')
                    ->leftJoin(Goods::model()->tableName().' as g', 'sg.goods_id=g.id')
                    ->leftJoin(OrdersGoods::model()->tableName().' as og', 'og.sgid=sg.id')
                    ->leftJoin(Order::model()->tableName().' as o', 'og.order_id=o.id')
                    ->where('o.code= "'.$code.'" AND sg.status!='.VendingMachineGoods::STATUS_ENABLE)
                    ->queryRow();
            }elseif ($order['type']==Order::TYPE_FRESH_MACHINE) {
                $g_data = Yii::app()->db->createCommand()
                    ->select('count(1) as count')
                    ->from(FreshMachineGoods::model()->tableName().' as sg')
                    ->leftJoin(Goods::model()->tableName().' as g', 'sg.goods_id=g.id')
                    ->leftJoin(OrdersGoods::model()->tableName().' as og', 'og.sgid=sg.id')
                    ->leftJoin(Order::model()->tableName().' as o', 'og.order_id=o.id')
                    ->where('o.code= "'.$code.'" AND sg.status!='.FreshMachineGoods::STATUS_ENABLE)
                    ->queryRow();
            }

            if (empty($g_data)) {
                $this->_error(Yii::t('order','数据错误'));
            }

            if (isset($g_data['count']) && $g_data['count']>0) {
                //只有新订单才取消
                if ($order['status']==Order::STATUS_NEW && $order['pay_status']!=Order::PAY_STATUS_YES) {
                    $cancel_rs = Order::orderCancel($code);
                    $this->_error(Yii::t('order','部分商品已下架,订单已自动取消'));
                }else{
                    $this->_error(Yii::t('order','部分商品已下架'));
                }

            }else{
                $this->_success(Yii::t('order','订单正常'));
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };


    }
// 订单积分支付
public function actionPointPay(){
    try{
        $this->params = array('token','code','codeId','amount','payPwd','GWnumber','freight');
        $requiredFields = array('token','code','codeId','amount','payPwd','GWnumber','freight');
        $decryptFields = array('token','code','codeId','payPwd','GWnumber','amount','freight');
        $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);

        $code = $post['code'];        //订单编号
        $codeId = $post['codeId'];
        $payPwd = $post['payPwd'];
        $GWnumber = $post['GWnumber'];
        $amount = $post['amount'];
        $freight = $post['freight'];
        $token = Yii::app()->gw->createCommand()
            ->select('token')
            ->from('{{member_token}}')
            ->where('target_id= '.$this->member)
            ->queryRow();
        if(!empty($token) && isset($token['token'])){
            $token = $token['token'];
        }else{
            $this->_error('token不存在');
        }
        $data = array(
            'code'=>$this->_encrypt($code),
            'codeId'=>$this->_encrypt($codeId),
            'payPwd'=>$this->_encrypt($payPwd),
            'GWnumber'=>$this->_encrypt($GWnumber),
            'amount'=>$this->_encrypt($amount),
            'freight'=>$this->_encrypt($freight),
            'token'=>$this->_encrypt($token),
            'ver'=>1
        );

        $url = GAIFUTONG_API_URL.'/skuPay/pointPay';

        $rs = Tool::post($url,$data);

        $rsArray = CJSON::decode($rs);

        if ($rsArray['Response']['resultCode'] == 1 ) {
            $msg = '您好，你的微小企订单['.$code.']买家已支付，请准备发货。';
            //$this->sendJPush($code,$msg);
            $this->_success($rsArray['Response']['resultDesc']);
        }else{
            $this->_error($rsArray['Response']['resultDesc']);
        }


    }catch (Exception $e){
        $this->_error($e->getMessage());
    };


}

    /**
     * 通联支付接口
     */
    public function actionTlianPay(){
        try{
            $this->params = array('token','code','codeId','amount','payPwd','GWnumber','freight','create_time');
            $requiredFields = array('token','code','codeId','amount','payPwd','GWnumber','freight','create_time');
            $decryptFields = array('token','code','codeId','payPwd','GWnumber','amount','freight','create_time');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $code = $post['code'];        //订单编号
            $codeId = $post['codeId'];
            $payPwd = $post['payPwd'];
            $GWnumber = $post['GWnumber'];
            $amount = $post['amount'];
            $freight = $post['freight'];
            $time = $post['create_time'];
            $token = Yii::app()->gw->createCommand()
                ->select('token')
                ->from('{{member_token}}')
                ->where('target_id= '.$this->member)
                ->queryRow();
            if(!empty($token) && isset($token['token'])){
                $token = $token['token'];
            }else{
            $this->_error('token不存在');
            }
            $data = array(
                'code'=>$this->_encrypt($code),
                'codeId'=>$this->_encrypt($codeId),
                'payPwd'=>$this->_encrypt($payPwd),
                'GWnumber'=>$this->_encrypt($GWnumber),
                'amount'=>$this->_encrypt($amount),
                'freight'=>$this->_encrypt($freight),
                'token'=>$this->_encrypt($token),
                'create_time'=>$this->_encrypt($time),
                'ver'=>1
            );
            $url = GAIFUTONG_API_URL.'/skuPay/tlianPay';
            $rs = Tool::post($url,$data);
            $rsArray = CJSON::decode($rs);
            if ($rsArray['Response']['resultCode'] == 1 ) {
                $msg = '您好，你的微小企订单['.$code.']买家已支付，请准备发货。';
                //$this->sendJPush($code,$msg);
                $this->_success($rsArray['Response']['resultDesc']);
            }else{
                $this->_error($rsArray['Response']['resultDesc']);
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }

    /**
     * 加密
     * 后台接口不加密数据,此函数作测试用
     */
    public function _encrypt($data)
    {
        $public = Yii::getPathOfAlias('keyPath') . DS . 'rsa_public_key.pem';
        $fp = fopen($public, "r");
    	$publicKey = fread($fp, 8192);
    	$res = openssl_get_publickey($publicKey);
    	openssl_public_encrypt($data, $encrypted, $res);
    	$encrypted = bin2hex($encrypted);  //转换成十六进制
    	return $encrypted;
    }

    /**
     * 极光推送操作
     * @param $code
     * @param string $msg
     * @return bool
     */
    protected function sendJPush($code,$msg = ''){
        $order = Order::getOrderInfo(array('code'=>$code),'partner_id');
        $partner = Partners::getOrderInfo(array('id'=>$order['partner_id']),'mobile');
        $status = JPushTool::tokenPush($partner['mobile'],$msg);//极光推送
        return $status;
    }

}
