<?php

/**
 * 盖付通接口控制器
 * 
 * @author leo8705
 *
 */
class COrderController extends CAPIController {

	/**
	 * 检查会员消费金额是否超过店铺每日限额
	 */
	private function _getMemberToStoreAmount($sid=0,$add_amount=0,$type=null){
// 		var_dump($this->member);exit();
		$amount = Order::getMemberTodayAmount($this->member,$sid,$type);
		$limit_config = Tool::getConfig('amountlimit');
		
		$store = null;
		if ($type==Order::TYPE_SUPERMARK && !empty($sid)) {
			$store = Yii::app()->db->createCommand()->select('max_amount_preday')->from(Supermarkets::model()->tableName())->where('id=:id',array(':id'=>$sid))->queryRow();
		}
		
		if ($type==Order::TYPE_MACHINE && !empty($sid)) {
			$store = Yii::app()->db->createCommand()->select('max_amount_preday')->from(VendingMachine::model()->tableName())->where('id=:id',array(':id'=>$sid))->queryRow();
		}
		
		if (($type==Order::TYPE_FRESH_MACHINE && !empty($sid))||($type==Order::TYPE_FRESH_MACHINE_SMALL && !empty($sid))) {
			$store = Yii::app()->db->createCommand()->select('max_amount_preday')->from(FreshMachine::model()->tableName())->where('id=:id',array(':id'=>$sid))->queryRow();
		}
		
//		$max_amount = isset($store['max_amount_preday'])&&$store['max_amount_preday']>0?$store['max_amount_preday']:$limit_config['memberTotalPayPreStoreLimit'];
                                    $max_amount = $limit_config['memberTotalPayPreStoreLimit'];
		     
		//加上准备消费的金额 看是否超过
		if (!empty($add_amount)) {
			$amount +=  $add_amount;
		}
		
		$isOver=false;
		if ($amount>=$max_amount && $max_amount>0) {
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
		$tag_name = 'checkAmount';
		$sid = $this->getParam('sid')*1;        		
		$type = $this->getParam('type')*1;
		$type = empty($type)?Order::TYPE_SUPERMARK:$type;
		$amount_rs = $this->_getMemberToStoreAmount($sid,null,$type);
		$limit_config = Tool::getConfig('amountlimit');
                                   if($limit_config['isEnable']){
                                       $amount_rs['amountlimit'] = true;//后台限额开启
                                   }else{
                                       $amount_rs['amountlimit'] = false;//后台限额禁用
                                   }
		$this->_success($amount_rs,$tag_name);
	}
	
	
    /**
     * 获取订单列表
     *
     *
     */
    public function actionList() {
    	$tag_name = 'OrderList';
        $page = $this->getParam('page') ? $this->getParam('page') :        1;
        $pageSize = $this->getParam('pageSize') ? $this->getParam('pageSize') : 10;
        
        $order_type = $this->getParam('orderType');
        
        //lastId 上条记录id
        $lastId = $this->getParam('lastId') ? $this->getParam('lastId')*1 : -1;

        $cri = new CDbCriteria();
        $cri->select = 't.id,t.code,t.goods_code,t.partner_id,t.member_id,t.store_id,t.machine_id,t.type,t.total_price,t.pay_price,t.address_id,t.shipping_type,t.pay_status,t.status,t.create_time,t.shipping_fee,t.shipping_time,t.remark,t.machine_take_type';
	    $cri->with = array('machine','store','freshMachine');
//         $cri->join = ' LEFT JOIN  '.Supermarkets::tableName().' as s ON t.store_id=s.id';
//         $cri->join .= ' LEFT JOIN  '.VendingMachine::tableName().' as m ON t.machine_id=m.id';
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
                $temp_arr['store_mobile']=isset($o->store->mobile)?$o->store->mobile:'';
//                $temp_arr['dc_mobile']=isset($o->distribution_order->distribution->mobile)?$o->distribution_order->distribution->mobile:'';
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
        
        $this->_success($data,$tag_name);
    }

    /**
     * 创建订单
     * 
     * goods_id 用逗号分开
     *
     */
    public function actionCreate() {
    	$tag_name = 'CreadOrder';
        $sid = $this->getParam('sid');        //门店id
        $type = $this->getParam('type');       //订单类型  1为门店  2为售货机  3为生鲜机  4为售货机格子铺
        $address_id = $this->getParam('addressId');
        $shipping_type = $this->getParam('shippingType');
        $shipping_time = $this->getParam('shippingTime');
        $machineTakeType = $this->getParam('machineTakeType')*1;
        $remark = $this->getParam('remark');
        $goods_arr = CJSON::decode(str_replace('\"', '"', $this->getParam('goods')));   //商品列表

        //判断店铺是否有配送员在线（人人配送）
//        if($type == Order::TYPE_SUPERMARK){
//            $store = Supermarkets::model()->findByPk($sid);
//            if($store->is_delivery == Supermarkets::PP_DELIVERY){
//            $lat = $store->lat;
//            $lng = $store->lng;
//            $select = 't.id';
//            $conditions ='';
//            	if (!empty($lat) && !empty($lng)) {
//            		$vicinity_rs = Tool::GetRange($lat, $lng, 2000);
//            		if ($vicinity_rs['maxLat'] > $vicinity_rs['minLat']) {
//            			$conditions .= ' ( t.lat  BETWEEN "'.$vicinity_rs['minLat'].'" AND "'.$vicinity_rs['maxLat'].'") ';
//            		} else {
//            			$conditions .= ' ( t.lat  BETWEEN "'.$vicinity_rs['maxLat'].'" AND "'.$vicinity_rs['minLat'].'") ';
//            		}
//            		if ($vicinity_rs['maxLng'] > $vicinity_rs['minLng']) {
//            	
//            			$conditions .= ' AND  ( t.lng  BETWEEN "'.$vicinity_rs['minLng'].'" AND "'.$vicinity_rs['maxLng'].'") ';
//            		} else {
//            			$conditions .= ' AND  ( t.lng  BETWEEN "'.$vicinity_rs['maxLng'].'" AND "'.$vicinity_rs['minLng'].'") ';
//            		}     	
//            	}
//             $conditions .= ' AND t.status ='.Stores::STATUS_ENABLE;
//            $conditions .= ' AND p.status ='.  Partners::STATUS_ENABLE;
////            var_dump($conditions);die;
//            $data = Yii::app()->db->createCommand()
//            	->select($select)
//            	->where($conditions)
//            	->from(Supermarkets::model()->tableName().' as t')
//                  ->leftJoin(Partners::model()->tableName().' as p', 't.partner_id=p.id ')
//                  ->queryAll();
//          $store_id = '';
//          foreach($data as $v){
//              $store_id.=$v['id'].',';
//          }
//          $store_id = rtrim($store_id, ',');
//          $select_dis = 't.id';
//          $conditions_dis = 'select_store_id in('.$store_id.') AND t.status ='.  Distribution::STATUS_OPEN;
//          $rs = Yii::app()->db->createCommand()
//            	->select($select_dis)
//            	->where($conditions_dis)
//            	->from(Distribution::model()->tableName().' as t')
//                  ->queryAll();
//          $rs = array_unique($rs) ;
//            if(empty($rs)){
//                    $this->_error('很抱歉，该商户暂时无法提供外送服务，请选择到店消费或取消订单提交，您亦可稍后尝试重新提交外送订单。',-1,$tag_name);
//            }
//            }
//        }

//         if ($type==Order::TYPE_SUPERMARK) {
        	$limit_config = Tool::getConfig('amountlimit');
        	if ($limit_config['isEnable']) {
        		$amount_rs = $this->_getMemberToStoreAmount($sid,null,$type);
        		if ($amount_rs['isOver']==true) {
        			$this->_error(Yii::t('apiModule.order','您的当日消费金额已超过每日最大限额，请明天再消费。'), ErrorCode::ORDER_OVER_MAX_AMOUNT_PREDAY_ERROR,$tag_name);
        		}
        	}
        	
//         }
// var_dump($goods_arr);exit();
//         	echo json_encode(array(0=>array('id'=>27,'num'=>1)));exit();
        	
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
        		$this->_error(Yii::t('apiModule.order','商品无效,'),null,$tag_name);
        	}
        	
        	$goods_arr = array();
        	foreach ($goods_list as $val){
        		$temp_arr = array();
        		$temp_arr['id'] = $val['id'];
        		$temp_arr['num'] = $num_arr[$val['code']];
        		$goods_arr[] = $temp_arr;
        	}
        	
        }
        
//         var_dump($sid,$goods_arr);exit();
        
        //下订单
        $order_rs = Order::model()->createOrder($type, $sid, $this->member, $goods_arr, $address_id, $shipping_type,$shipping_time,$machineTakeType,$remark);
        if ($order_rs['success']==true) {
        	$order = $order_rs['data'];
            //$this->sendJPush($order_rs['data']['partner_id'],$order_rs['data']['partner_id'].'已经下单成功');

            $this->_success(array('id' => $order['id'], 'code' => $order['code'],'goods_code' => $order['goods_code'],'create_time' => $order['create_time'],'amount' => $order['total_price'],'store_name'=>$order['store_name']),$tag_name);
        } else {
            $this->_error(Yii::t('apiModule.order','下单失败,').ErrorCode::getErrorStr($order_rs['code']),$order_rs['code'],$tag_name);
        }
    }

    /**
     * 订单信息
     *
     */
    public function actionDetail() {
    	$tag_name = 'OrderDetails';
        $code = $this->getParam('code');
        $data = Order::model()->getDetailByCode($code);

        if (empty($data))
            $this->_error(Yii::t('apiModule.order','订单不存在'),null,$tag_name);
        $this->_chenck($data->member_id);

        $rs['detail'] = $data->attributes;
//        $rs['detail']['dc_mobile']=isset($data->distribution_order->distribution->mobile)?$data->distribution_order->distribution->mobile:'';
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
             $rs['storeInfo']['delivery_time'] = $data->store->delivery_time;
              $rs['storeInfo']['delivery_start_amount'] = $data->store->delivery_start_amount;
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
   $rs['storeInfo']['stype'] = (isset($data->freshMachine)&&($data->freshMachine->type == FreshMachine::FRESH_MACHINE_SMALL))? Order::TYPE_FRESH_MACHINE_SMALL :$data->type;      
        
        
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
                $temp_arr['sgid'] = $garr[$o->id]['sgid'];
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
        
        $order_config = Tool::getConfig('orderExpireTime','orderExpireTime');
        $rs['orderCancelTime'] = $order_config;


        $this->_success($rs,$tag_name);
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
    	$tag_name = 'CancleOrder';
        $code = $this->getParam('code');
        $remark = $this->getParam('remark');
        $order = Order::model()->getByCode($code);
        if (empty($order)) {
            $this->_error(Yii::t('apiModule.order','订单不存在'),$tag_name);
        }
        $this->_chenck($order->member_id);
        
        if ($order->status == Order::STATUS_FROZEN) {
        	$this->_error(Yii::t('apiModule.order','订单已冻结'),$tag_name);
        }

        $rs = Order::orderCancel($code,true,$remark);
        if ($rs['success'] != true) {
        	$this->_error(ErrorCode::getErrorStr($rs['code']), $rs['code'],$tag_name);
        }
        
        
//         //发送短信
//         $m = new ApiMember();
//         $member_info = $m->getInfo($order['member_id']);
//         if (!empty($member_info)) {
//         	$m->sendSms($member_info['mobile'], '取消订单['.$order['code'].'] 成功，订单货款'.$order['total_price'].'元已退还到您的账户');
//         }
        
        $this->_success(Yii::t('apiModule.order','取消成功'),$tag_name);
    }

    /**
     * 签收订单
     *
     */
    public function actionComplete() {
    	$tag_name = 'CompleteOrder';
        $code = $this->getParam('code');

        $order = Order::model()->getByCode($code);

        if (empty($order)) {
            $this->_error(Yii::t('apiModule.order','订单不存在'),$tag_name);
        }

        $this->_chenck($order->member_id);
        
        if ($order->status == Order::STATUS_FROZEN) {
        	$this->_error(Yii::t('apiModule.order','订单已冻结'),$tag_name);
        }
        
        if ($order->status != Order::STATUS_PAY  && $order->status != Order::STATUS_SEND ) {
       		$this->_error(Yii::t('apiModule.order','此时不能签收订单！'),ErrorCode::ORDER_STATUS_FAIL ,$tag_name);
       	}

        $sign_rs = Order::orderSign($code, true);
        if ($sign_rs['success'] != true) {
            $this->_error($sign_rs['error_msg']?$sign_rs['error_msg']:ErrorCode::getErrorStr($sign_rs['code']), $sign_rs['code'],$tag_name);
        }
        //提货码提货扣除冻结库存
        if($order->machine_take_type ==Order::MACHINE_TAKE_TYPE_WITH_CODE){
            $gids = array();
            $nums = array();
            foreach($order->ordersGoods as $v){
                $gids[] = $v['sgid'];
                $nums[] =$v['num'];
            }
			$lines = Yii::app()->db->createCommand()
			->select('line_id,id,goods_id')
			->from(FreshMachineGoods::model()->tableName())
			->where('id IN ('.implode(',', $gids).')')
			->queryAll();
			
			
			$line_ids = array();
			foreach ($lines as $l){
				$line_ids[$l['id']] = $l['line_id'];
			}
			
			ksort($nums);
			ksort($line_ids);
			$line_ids = array_values($line_ids);

		$nums = array_values($nums);
		 $OKRS = ApiStock::stockFrozenOutList($store->id,$line_ids, $nums,API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
           }
        $modelOrder = new Order();
        $modelOrder->giveBackAmountFirstConsume($code,$order->member_id);
        $this->_success(Yii::t('apiModule.order','签收成功'),$tag_name);
    }

    /**
     * 退单
     * 申请退款
     *
     */
    public function actionRefund() {
        $code = $this->getParam('code');
        $order = Order::model()->getDetailByCode($code);

        if (empty($order)) {
            $this->_error(Yii::t('apiModule.order','订单不存在！'));
        }
        
        $this->_chenck($order->member_id);
        
        if ($order->status == Order::STATUS_FROZEN) {
        	$this->_error(Yii::t('apiModule.order','订单已冻结'));
        }

        if ($order->status != Order::STATUS_PAY) {
            $this->_error(Yii::t('apiModule.order','此时不能申请退款'), ErrorCode::COMMON_NORMAL);
        }


        //处理流水
        $order->status = order::STATUS_REFUNDING;
        $order->save();
        $this->_success(Yii::t('apiModule.order','申请退款成功,订单退款中'));
    }
    
    /**
     * 完成订单后评价商品
     *
     */
    public function actionGoodsEvaluation() {
    	$code = $this->getParam('code');
    	$sgid = $this->getParam('goodsId')*1;
    	$content = $this->getParam('content');
    	$score = $this->getParam('score')*1;
    	$service_score = $this->getParam('serviceScore')*1;
    	$quality_score = $this->getParam('qualityScore')*1;
    	
    	if ($score>GoodsComment::MAX_SCORE || $score<GoodsComment::MIN_SCORE) {
    		$this->_error(Yii::t('apiModule.order','综合评分只能在{min}~{max}之间',array('{min}'=>GoodsComment::MIN_SCORE,'{max}'=>GoodsComment::MAX_SCORE)), ErrorCode::COMMOM_ERROR);
    	}
    	
    	if ($service_score>GoodsComment::MAX_SCORE || $service_score<GoodsComment::MIN_SCORE) {
    		$this->_error(Yii::t('apiModule.order','服务评分只能在{min}~{max}之间',array('{min}'=>GoodsComment::MIN_SCORE,'{max}'=>GoodsComment::MAX_SCORE)), ErrorCode::COMMOM_ERROR);
    	}
    	
    	if ($quality_score>GoodsComment::MAX_SCORE || $quality_score<GoodsComment::MIN_SCORE) {
    		$this->_error(Yii::t('apiModule.order','商品质量评分只能在{min}~{max}之间',array('{min}'=>GoodsComment::MIN_SCORE,'{max}'=>GoodsComment::MAX_SCORE)), ErrorCode::COMMOM_ERROR);
    	}
    	
    	
    	$order = Order::model()->getByCode($code);
    
    	if (empty($order)) {
    		$this->_error(Yii::t('apiModule.order','订单不存在！'), ErrorCode::COMMOM_ERROR);
    	}
    	
    	$this->_chenck($order->member_id);
    	
    	if ($order->status != Order::STATUS_COMPLETE) {
    		$this->_error(Yii::t('apiModule.order','订单未完成，不能评价!'));
    	}
    	
    	$goods = OrdersGoods::model()->find('sgid=:sgid AND order_id=:order_id',array(':sgid'=>$sgid,':order_id'=>$order['id']));
    
    	if (empty($goods)) {
    		$this->_error(Yii::t('apiModule.order','商品不对应！'), ErrorCode::COMMOM_ERROR);
    	}
    
    	if ($order->status != Order::STATUS_COMPLETE) {
    		$this->_error(Yii::t('apiModule.order','此时不能评价商品'), ErrorCode::COMMOM_ERROR);
    	}
    	
    	$count = GoodsComment::model()->count('goods_id=:goods_id AND order_id=:order_id',array(':goods_id'=>$goods['gid'],':order_id'=>$order['id']));
    	
    	if ($count) {
    		$this->_error(Yii::t('apiModule.order','不能重复评价商品'), ErrorCode::COMMOM_ERROR);
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
			$this->_success(Yii::t('apiModule.order','评价成功').$goods_rs);
		}else{
			$trans->rollback();
			$this->_error(Yii::t('apiModule.order','评价失败'));
		}
    	
    	
    }
    
    /**
     * 检查商品
     * 
     * 检查订单中是否包含已下架或已失效的商品
     * 
     * 
     */
    public function actionCheckGoods(){
    	$tag = 'orderCheckGoods';
    	$code = $this->getParam('code');
    	$order = Order::getByCode($code);
    	if (empty($order)) {
    		$this->_error(Yii::t('apiModule.order','订单不存在'),null,$tag);
    	}
    	
    	if ($order['member_id']!==$this->member) {
    		$this->_error(Yii::t('apiModule.order','非法操作'),null,$tag);
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
    		$this->_error(Yii::t('apiModule.order','数据错误'),null,$tag);
    	}
    	
    	if (isset($g_data['count']) && $g_data['count']>0) {
    		//只有新订单才取消
    		if ($order['status']==Order::STATUS_NEW && $order['pay_status']!=Order::PAY_STATUS_YES) {
    			$cancel_rs = Order::orderCancel($code);
    			$this->_error(Yii::t('apiModule.order','部分商品已下架,订单已自动取消'),null,$tag);
    		}else{
    			$this->_error(Yii::t('apiModule.order','部分商品已下架'),null,$tag);
    		}
    		
    	}else{
    		$this->_success(Yii::t('apiModule.order','订单正常'),$tag);
    	}
    	
    }

    public function actionDemo(){
        $result = JPushTool::push(JPushTool::GZGAppKey,JPushTool::GZGMasterSecret,'123456',array('GW74952392A'),'','tag');
        var_dump($result);
    }

    /**
     * 极光推送操作
     * @param $id
     * @param string $msg
     * @return bool
     * @author jiawei.liao
     */
    protected function sendJPush($id,$msg = ''){
        $deviceArr = array();
        $partner = Partners::getPartnersInfo(array('id'=>$id),'gai_number');
        if(!empty($partner['gai_number'])){
            $deviceArr[] = $partner['gai_number'].'A';
            $deviceArr[] = $partner['gai_number'].'B';
        }

        $status = JPushTool::push(JPushTool::GZGAppKey,JPushTool::GZGMasterSecret,$msg,$deviceArr,'','tag');

        return $status;
    }

    /**
     * 呼叫功能接口
     * @param $orderCode 订单编号
     * @param $sid 门店id
     * @param $partnerId 商家id
     * @return json
     * @author yuanmei.chen
     */

    public function actionGetPhoneNumberByCall()
    {
        $orderCode = $this->getParam('orderCode');
        $sid       = $this->getParam('sid');
        $partnerId = $this->getParam('partnerId');

        $tag_name = 'OrderCall';
        $data = array();

        if(empty($orderCode))
        {
            $this->_error('订单编号缺失!',$tag_name);
        }

        //检测是否存在此订单
        $order = Order::model()->getByCode($orderCode);

        if(empty($order))
        {
            $this->_error('不存在此订单号!',$tag_name);
        }

        if($order->partner_id != $partnerId)
        {
            $this->_error('商家id有误!',$tag_name);
        }

        if($order->store_id != $sid)
        {
            $this->_error('门店id有误!',$tag_name);
        }
        //查询相关数据 得到商家手机号码和配送员的手机号码
        if(!empty($sid) && is_numeric($sid))
        {
            //查询商家是否支持人人配送的功能
            $params = array(
                'sid' => $sid,
                'partner_id' => $partnerId,
            );

            $ret = Supermarkets::model()->getBySidAndPartner($params);

            if(empty($ret))
                $this->_error('查不到对应商家的信息!',$tag_name);

            //获取商家的电话号码
            $data['store_mobile'] = $ret->mobile;

            if(!empty($ret->is_delivery) && $ret->is_delivery == 2) //该商家支持人人配送的方式
            {
                DistributionOrder::model()->scenario = 'getMobile';
                //获取人人配送的电话号码
                $rs = DistributionOrder::model()->getDistributionByCode($orderCode);

                if(empty($rs))
                    $this->_error('查不到配送人员的信息!',$tag_name);

                $data['dc_mobile'] = $rs['mobile'];
            }
        }
        else
        {
            $this->_error('门店id不能为空!',-1,$tag_name);
        }

        $this->_success($data,$tag_name);
    }

    /**
     * 待抢订单接口
     * @param int memberId 配送员的id
     * @param int lastId 当前页面最后一个id
     * @param int pageSize 当前页面显示的数量
     * @author yuanmei.chen
     */
    public function actionGetWaitingOrderList()
    {
        $memberId   = $this->member;
        //lastId 上条记录id
        $lastId = $this->getParam('lastId') ? $this->getParam('lastId')*1 : -1;
        $pageSize = $this->getParam('pageSize') ? $this->getParam('pageSize') : 10;

        $tag_name = 'GetWaitingOrderList';

        if(empty($lastId) || empty($pageSize))
        {
            $this->_error('缺少参数!',ErrorCode::COMMOM_ERROR);
        }

        $orderList = Order::model()->getWaitingOrderLists($memberId,$lastId,$pageSize);

        if(isset($orderList['result']) && $orderList['result'] == false)
        {
            $this->_error($orderList['msg'],-1,$tag_name);
        }
        else
        {
            $this->_success(array_values($orderList),$tag_name);
        }

    }

    /**
     * 抢单功能接口
     * @param int orderCode 订单编号
     * @param int store_id 商户的id
     * @author yuanmei.chen
     */
    public function actionGrabOrder()
    {
        $orderCode = $this->getParam('orderCode');
        $store_id  =  $this->getParam('store_id');
        $memberId  = $this->member;

        $tag_name = 'GrabOrder';

        if(empty($orderCode) || empty($store_id))
            $this->_error('缺少参数!',ErrorCode::COMMOM_ERROR);

        //查询订单id
        $order = Order::model()->find('code=:code',array(':code' => $orderCode));
        if(empty($order))
            $this->_error('没有此订单信息!',ErrorCode::GOOD_STOCK_NOT_EXIST);
        $orderId = $order->id;

        //查询当前配送员的序号
        $info = Distribution::model()->find('member_id=:member_id',array(':member_id' => intval($memberId)));

        if(empty($info))
            $this->_error('查不到配送人员的信息!',ErrorCode::GOOD_STOCK_NOT_EXIST);

        $memberOrderId = $info->id;

        $result = DistributionOrder::model()->grabOrderHandle($orderCode,$orderId,$memberOrderId,$store_id);

        if($result['result'] == false)
        {
            $this->_error($result['msg'],-1,$tag_name);
        }
        else
        {
            $this->_success($result,$tag_name);
        }

    }


    /**
     * 待取货功能接口
     * @param int memberId 配送员的id
     * @author yuanmei.chen
     */
    public function actionGetGoodsListByDistribution()
    {
        $memberId  = $this->member;

        $tag_name = 'GetGoodsListByDistribution';

        if(empty($memberId))
            $this->_error('缺少参数!',ErrorCode::COMMOM_ERROR);

        $data = Order::model()->getDealWithGoodsList($memberId,DistributionOrder::STATUS_PICK_UP);

        if(isset($data['result']) && $data['result'] == false)
        {
            $this->_error($data['msg'],-1,$tag_name);
        }
        else
        {
            $this->_success($data,$tag_name);
        }


    }

    /**
     * 待送货订单接口
     * @author yuanmei.chen
     */
    public function actionWaitingSendGoodsListByDistribution()
    {
        $memberId  = $this->member;

        $tag_name = 'WaitingSendGoodsListByDistribution';

        if(empty($memberId))
            $this->_error('缺少参数!',$tag_name);

        $data = Order::model()->getDealWithGoodsList($memberId,DistributionOrder::STATUS_WAITING_SEND);

        if(isset($data['result']) && $data['result'] == false)
        {
            $this->_error($data['msg'],$tag_name);
        }
        else
        {
            $this->_success($data,$tag_name);
        }

    }

    /**
     * 取货/收货码接口
     * @param int orderCode 订单编号
     * @params goods_code string 取货码
     * @param int type 1:用户收货 0：配送员取货
     * @author yuanmei.chen
     */
    public function actionScanTakeGoods()
    {
        $goods_code = $this->getParam('goods_code');
        $orderCode  = $this->getParam('orderCode');
        $type       = $this->getParam('type') > 0 ? true : false;

        $tag_name = 'ScanTakeGoods';
        if(empty($goods_code) || empty($orderCode))
            $this->_error('缺少参数!',$tag_name);

        $rs = Order::model()->scanTakeGoods($orderCode,$goods_code,$type);

        if(isset($rs['result']) && $rs['result'] == false) {
            $this->_error($rs['msg'],$tag_name);
        }
        else {
            $this->_success($rs,$tag_name);
        }

    }
    /**
     * 人人配送缴纳押金订单
     * @param  int  amount 支付金额
     * @param string token 
     */
    public function actionCreateDepositOrder(){
        $memberId = $this->member;
        $gai_memberId = Member::model()->findByPk($memberId);
        $amount = $this->getParam('amount'); 
        $symbol = $this->getParam('symbol');
        $token = $this->getParam('token');
        $dis_model = Distribution::model()->find('member_id=:mid',array(':mid'=>$memberId));

        $model = new DepositOrder();
       $data = array(
        'code' => Order::_createCode(Order::TYPE_SUPERMARK,$memberId),
        'member_id' => $memberId,
       'mobile' => $dis_model->mobile,
       'pay_price' =>$amount,
       'create_time' => time(),
           );
      $in_rs = Yii::app()->db->createCommand()->insert(DepositOrder::model()->tableName(), $data);
      $laId = Yii::app()->db->getLastInsertID();
 
        if(!empty($in_rs)){
            $rs = array(
             'orderNum'=>$data['code'],
                'amount'=>$amount,
                'symbol'=>$symbol,
                'GWnumber'=>$gai_memberId->gai_number,
                'callback'=>'skuDeposit',
                'exten'=>$laId,
                'remark'=> 'SKU人人配送押金',
                     );
        $this->_success($rs);
        }else{
          
                    
        }
    }
    /**
     * 检查订单
     */
    public function actionCheckOrder(){
        $tag_name = 'CheckOrder';
        $code = $this->getParam('code');
        $data = Order::model()->find('code=:code',array(':code'=>$code));
        if(empty($data)){
             $this->_error(Yii::t('apiModule.order','订单不存在'),-1,$tag_name);
        } 
        $memberId = $data['member_id'];
        //查询之前的消费记录
                $sql_order = 'SELECT id FROM {{orders}} WHERE member_id = ' . intval($memberId) . ' AND code != ' . $code . ' AND status = ' . Order::STATUS_COMPLETE . '  LIMIT 1';
                $ConsumeHistory_order = Yii::app()->db->createCommand($sql_order)->queryAll();
                
                $sql_guadan = 'SELECT id FROM {{guadan_jifen_order}} WHERE member_id = ' . intval($memberId) . ' AND type='.GuadanJifenOrder::TYPE_PARTNER . ' AND status = ' . GuadanJifenOrder::STATUS_PAY . '  LIMIT 1';
                $ConsumeHistory_guadan = Yii::app()->db->createCommand($sql_guadan)->queryAll();
        if(empty($ConsumeHistory_order) && empty($ConsumeHistory_guadan)){
            $msg = '感谢消费,首次订单完成后返回完成订单金额10%的积分至您的账户（请关注至优壹佰，获取更多优惠。）';
            $this->_success(Order::ZHIYOUYIBAI,$tag_name,$msg);
        }else{
            $this->_success(Order::NOW,$tag_name);
        }
    }
    
}
