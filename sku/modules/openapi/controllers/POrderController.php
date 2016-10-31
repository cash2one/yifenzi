<?php

/**
 * 商家客户端专用接口控制器
 * 
 * @author leo8705
 *
 */
class POrderController extends POpenAPIController {

    /**
     * 获取订单列表
     *
     *
     */
    public function actionList() {
        try{
            $tag_name = 'Orders';
            $this->params = array('token','sid','page','pageSize','status');
            $requiredFields = array('token','sid');
            $decryptFields = array('token','sid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $page = isset($post['page'])?$post['page']:1;
            $pageSize = isset($post['pageSize'])?$post['pageSize']:20;
            $status = isset($post['status'])?$post['status']:null;//订单状态
            if ($this->getParam('onlyTest')==1) {
//         	$sid = $this->getParam('sid')*1;
                $status = $this->getParam('status');
            }
            $this->_checkStore();
            $store = $this->store;

            $cri = new CDbCriteria();
            $cri->select = 't.code,t.partner_id,t.member_id,t.store_id,t.type,t.total_price,t.pay_price,t.address_id,t.shipping_type,t.shipping_time,t.pay_status,t.status,t.create_time,t.shipping_fee,t.shipping_time,t.remark,t.seller_remark';
            $cri->with = array('address');

            $cri->compare('t.partner_id',$this->partnerInfo['id']);
            $cri->compare('t.store_id', $store['id']);
            if($status!==null){
                $cri->compare('t.status',$status);
            }
            $cri->compare('type', Order::TYPE_SUPERMARK);
            $cri->addCondition('t.father_id=0');

            //分页
            $cri->limit = $pageSize;
            $cri->offset = ($page - 1) * $pageSize;
            $cri->order = 't.create_time DESC';
            $list = Order::model()->findAll($cri);
            $list_data = array();
            if (!empty($list)) {
                foreach ($list as $k => $o) {
                    $temp_arr = $o->attributes;
                    $temp_arr['store_name'] = isset($o->store->name) ? $o->store->name : 0;
                    $temp_arr['type_name'] = Order::type($temp_arr['type']);
                    $temp_arr['pay_status_name'] = Order::payStatus($temp_arr['pay_status']);
                    $temp_arr['status_name'] = Order::status($temp_arr['status']);
                    $temp_arr['shipping_type_name'] = Order::shippingType($o->shipping_type);
                    foreach ($temp_arr as $kk=>$v){
                        if ($v==null && $kk!='seller_remark') {
                            unset($temp_arr[$kk]);
                        }
                    }
                    if (!empty($o->address)) {
                        $temp_arr['real_name'] = $o->address->real_name;
                        $temp_arr['mobile'] = $o->address->mobile;
                        $temp_arr['province_id'] = $o->address->province_id;
                        $temp_arr['city_id'] = $o->address->city_id;
                        $temp_arr['district_id'] = $o->address->district_id;
                        $temp_arr['street'] = $o->address->street;
                        $temp_arr['zip_code'] = $o->address->zip_code;
                        $temp_arr['province'] = Region::getName($o->address->province_id);
                        $temp_arr['city'] = Region::getName($o->address->city_id);
                        $temp_arr['district'] = Region::getName($o->address->district_id);
                    }

                    $list_data[$k] = $temp_arr;
                }
            }
            $un_complete_count = Yii::app()->db->createCommand()
                ->select('count(1) as count')
                ->from(Order::model()->tableName())
                ->where('store_id=:store_id AND partner_id=:partner_id AND type=:type AND (status=:status_pay OR status=:status_send)',
                    array(':store_id'=>$store['id'],':partner_id'=>$this->partnerInfo['id'],':type'=>Order::TYPE_SUPERMARK,':status_pay'=>Order::STATUS_PAY,':status_send'=>Order::STATUS_SEND))
                ->queryRow();

            $rs = array();
            $rs['list_data'] = $list_data;
            $rs['un_complete_count'] = isset($un_complete_count['count'])?$un_complete_count['count']:0;
            $this->_success($rs,$tag_name);
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
            $tag_name = 'OrderDetails';
            $this->params = array('token','code');
            $requiredFields = array('token','code');
            $decryptFields = array('token','code');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $code = $post['code'];				//订单号

            if ($this->getParam('onlyTest')) {
                $code = $this->getParam('code');
            }
            $data = Order::model()->getDetailByCode($code);
            if (empty($data))
                $this->_error('订单不存在',$tag_name);
            if ($data->partner_id !=$this->partnerInfo['id']) {
                $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
            }

            $rs['detail'] = $data->attributes;
            unset($rs['detail']['goods_code']);
            $rs['orderGoods'] = array();
            $rs['storeInfo'] = array();
            if (!empty($data->store)) {
                $rs['storeInfo']['store_name'] = $data->store->name;
                $rs['storeInfo']['mobile'] = $data->store->mobile;
                $rs['storeInfo']['logo'] = $data->store->logo;
                $rs['storeInfo']['type'] = $data->store->type;
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


            if (!empty($data->ordersGoods)) {
                $gids = array();
                $garr = array();
                foreach ($data->ordersGoods as $g) {
                    $gids[] = $g->gid;
                    $garr[$g->gid] = $g->attributes;
                }

                $gcri = new CDbCriteria();
                $gcri->select = 'id,name,thumb,price';
                $gcri->addInCondition('id', $gids);
                $goods_list = Goods::model()->findAll($gcri);

                foreach ($goods_list as $o) {
                    $o->thumb = ATTR_DOMAIN . DS . $o->thumb;
                    $temp_arr =$o->attributes;
                    $temp_arr['num'] = $garr[$o->id]['num'];
                    $temp_arr['price'] = $garr[$o->id]['price'];
                    $temp_arr['name'] = $garr[$o->id]['name'];
                    $rs['orderGoods'][] = $temp_arr;
                }
            }

            $rs['detail']['type_name'] = Order::type($rs['detail']['type']);
            $rs['detail']['pay_status_name'] = Order::payStatus($rs['detail']['pay_status']);
            $rs['detail']['status_name'] = Order::status($rs['detail']['status']);


            //收货地址
            $address = OrderAddress::model()->findByPk($data->address_id);
            if (!empty($address)) {
                $address = $address->attributes;
                $address['province_name'] = Region::getName($address['province_id']);
                $address['city_name'] = Region::getName($address['city_id']);
                $address['district_name'] = Region::getName($address['district_id']);
            }
            $rs['address'] = $address;
            $this->_success($rs,$tag_name);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }
    
    

    /**
     * 送货
     * @param code
     */

     public function actionSend(){
         try{
             $this->params = array('token','code');
             $requiredFields = array('token','code');
             $decryptFields = array('token','code');
             $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);

             $code = $post['code'];				//订单号

             if ($this->getParam('onlyTest')==1) {
                 $code = $this->getParam('code');    //订单号
             }
             $order = Order::model()->getByCode($code);
             if (empty($order)) {
                 $this->_error(Yii::t('order','订单不存在'));
             }

             $this->store = Supermarkets::model()->findByPk($order['store_id']);
             $this->_checkStore();

             if ($order->partner_id !=$this->partnerInfo['id']) {
                 $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
             }

             if ($order->status == Order::STATUS_FROZEN) {
                 $this->_error(Yii::t('apiModule.order','订单已冻结'));
             }

             if ($order->status == Order::STATUS_SEND ) {
                 $this->_error(Yii::t('order','该订单已发货,不允许重复发货'));
             }

             if ($order->status == Order::STATUS_COMPLETE ) {
                 $this->_error(Yii::t('order','该订单已完成,不允许发货'));
             }

             if ($order->status == Order::STATUS_CANCEL ) {
                 $this->_error(Yii::t('order','该订单已取消,不允许发货'));
             }

             if ($order->shipping_type !=Order::SHIPPING_TYPE_SEND ) {
                 $this->_error(Yii::t('order','该订单非送货订单,不允许发货'));
             }

             if ($order->status != Order::STATUS_PAY ) {
                 $this->_error(Yii::t('order','该订单非已支付状态，不允许发货'));
             }

             $order->status = Order::STATUS_SEND;
             $order->save();

             //发送短信
             $apiMember = new ApiMember();
             $mobile = $order['mobile'];
             if (empty($mobile)) {
                 $memberInfo = $apiMember->getInfo($order['member_id']);
                 $mobile = $memberInfo['mobile'];
             }

             if (!empty($mobile)) {
//                 $apiMember->sendSms($memberInfo['mobile'], '您好，你的微小企订单['.$order['code'].']已发货，请准备收货。');
                 $msg =  '您好，你的微小企订单['.$order['code'].']已发货，请准备收货。';
                  $apiMember->sendSms($memberInfo['mobile'],$msg,  ApiMember::SMS_TYPE_ONLINE_ORDER, 0,  ApiMember::SKU_SEND_SMS,array($order['code']),  ApiMember::SEND_SUCCESS);
             //JPushTool::tokenPush($mobile,$msg);//极光推送
             }

             $this->_success(Yii::t('order','设置发货成功'));
         }catch (Exception $e){
             $this->_error($e->getMessage());
         };

     }
     
   
      /**
     * 签收订单
     *@param code
     * @param goods_code
     */
    public function actionComplete() {
        try{
            $this->params = array('token','code','goods_code');
            $requiredFields = array('token','code','goods_code');
            $decryptFields = array('token','code','goods_code');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $code = $post['code'];				//订单号
            $goods_code = $post['goods_code'];   // 提货码
            if ($this->getParam('onlyTest')==1) {
                $code = $this->getParam('code');    //订单号
                $goods_code = $this->getParam('goods_code');
            }

            $order = Order::model()->getByCode($code);
            if (empty($order)) {
                $this->_error(Yii::t('order','订单不存在'));
            }

            $this->store = Supermarkets::model()->findByPk($order['store_id']);
            $this->_checkStore();


            if ($order->partner_id !=$this->partnerInfo->id) {
                $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
            }
            if($goods_code != $order->goods_code){
                $this->_error(Yii::t('order','提货码错误'));
            }

            if ($order->status == Order::STATUS_FROZEN) {
                $this->_error(Yii::t('order','订单已冻结'));
            }

            if ($order->status == Order::STATUS_COMPLETE) {
                $this->_error(Yii::t('order','订单已签收，不能重复签收'));
            }
            if ($order->pay_status ==Order::PAY_STATUS_YES &&$order->status !=Order::STATUS_COMPLETE &&$order->status !=Order::STATUS_CANCEL ) {
                $sign_rs = Order::orderSign($code, true);
                if ($sign_rs['success'] != true) {
                    $this->_error(ErrorCode::getErrorStr($sign_rs['code']), $sign_rs['code']);
                }
            }else{
                $this->_error(Yii::t('order','签收失败，订单此时不能签收'));
            }
            $this->_success(Yii::t('order','签收成功'));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }
    
    /**
     * 取消订单(只能取消未支付的订单)
     * @params  code   
     */
     public function actionCancel(){
         try{
             $this->params = array('token','code','remark');
             $requiredFields = array('token','code');
             $decryptFields = array('token','code','remark');
             $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
             $code = $post['code'];				//订单号
             $remark = isset($post['remark'])?$post['remark']:'';   // 备注
             $order = Order::model()->getByCode($code);
             if(empty($order)){
                 $this->_error(Yii::t('order','订单不存在'));
             }

             $this->store = Supermarkets::model()->findByPk($order['store_id']);
             $this->_checkStore();

             if ($order->partner_id !=$this->partnerInfo['id']) {
                 $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
             }

             if ($order->status == Order::STATUS_FROZEN) {
                 $this->_error(Yii::t('order','订单已冻结'));
             }

             if($order->pay_status ==Order::PAY_STATUS_YES || $order->status == Order::STATUS_PAY){
                 $this->_error(Yii::t('order','订单已支付'));
             }
             if($order->status == Order::STATUS_NEW && $order->pay_status == Order::PAY_STATUS_NO){
                 $rs = Order::orderCancel($code,false,'店家取消订单');

                 if($rs['success']==true){
                     $this->_success(Yii::t('order','订单取消成功'));
                 }else{
                     $this->_error(Yii::t('order','订单取消失败'));
                 }
             }else{
                 $this->_error(Yii::t('order','该订单此时不能取消'));
             }
         }catch (Exception $e){
             $this->_error($e->getMessage());
         };

     }
     
     
     
     /**
      * 取消订单部分商品  用于已支付订单退款
      * 
      * 逻辑为取消当前订单，再下一张订单
      * 
      * @params  code
      */
     public function actionCancelPart(){
         try{
             $this->params = array('token','code','successGoods');
             $requiredFields = array('token','code','successGoods');
             $decryptFields = array('token','code','successGoods');
             $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
             $code = $post['code'];				//订单号
             $success_goods = $post['successGoods'];//保留商品
             $order = Order::model()->getByCode($code);
             if(empty($order)){
                 $this->_error(Yii::t('order','订单不存在'));
             }

             $this->store = Supermarkets::model()->findByPk($order['store_id']);
             $this->_checkStore();


             if ($order->partner_id !=$this->partnerInfo['id']) {
                 $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
             }

             if ($order->status == Order::STATUS_FROZEN) {
                 $this->_error(Yii::t('order','订单已冻结'));
             }

             if($order->pay_status !=Order::PAY_STATUS_YES || $order->status != Order::STATUS_PAY){
                 $this->_error(Yii::t('order','订单未支付'));
             }
             $rs = Order::orderCancelPart($code,$success_goods);
             if($rs['success']==true){
                 $this->_success(Yii::t('order','订单取消成功'));
             }else{
                 $this->_error(Yii::t('order','订单取消失败'));
             }
         }catch (Exception $e){
             $this->_error($e->getMessage());
         };

     }
     
}
