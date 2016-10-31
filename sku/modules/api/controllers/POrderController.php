<?php

/**
 * 商家客户端专用接口控制器
 * 
 * @author leo8705
 *
 */
class POrderController extends PAPIController {

    /**
     * 获取订单列表
     *
     *
     */
    public function actionList() {
        $tag_name = 'Orders';
        $page = $this->getParam('page') ? $this->getParam('page') : 1;
        $pageSize = $this->getParam('pageSize') ? $this->getParam('pageSize') : 20;
        
//         $sid = $this->getParam('sid')*1;
//         $sid =  $this->rsaObj->decrypt($this->getParam('sid'));
//        $status = $this->getParam('status');


        if ($this->getParam('onlyTest')==1) {
//         	$sid = $this->getParam('sid')*1;
        	$status = $this->getParam('status');
        }else{
            $status = $this->rsaObj->decrypt($this->getParam('status')); //订单状态
        }
        
//         if (empty($sid)) {
//         	$this->_error('sid不能为空！');
//         }
        
//         $store = Supermarkets::model()->findByPk($sid);
//         if (empty($store)) {
//         	$this->_error(ErrorCode::getErrorStr(ErrorCode::COMMOM_ERROR),ErrorCode::COMMOM_ERROR);
//         }
        
//         if ($store->member_id != $this->member) {
//         	$this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
//         }
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
        
//         $list = Yii::app()->db->createCommand()
//         ->from(Order::model()->tableName())
//         ->select( 't.code,t.partner_id,t.member_id,t.store_id,t.type,t.total_price,t.pay_price,t.address_id,t.shipping_type,t.shipping_time,t.pay_status,t.status,t.create_time,t.shipping_fee,t.shipping_time,t.remark,t.seller_remark')
        
//         //超时未支付订单取消
//         $invalid_time = $this->params('orderUnpayInvalidTime');
//         $invaild_ids = array();
//         foreach ($list as $k=>$v){
//         	if ($v->status == Order::STATUS_NEW && $v->pay_status==Order::PAY_STATUS_NO && $v->create_time<time()-$invalid_time) {
//         		$v->status = Order::STATUS_INVALID;
//         		$list[$k] = $v;
//         		$invaild_ids[] = $v->id;
//         	}
//         }
//         if (!empty($invaild_ids)) {
//         	Order::model()->updateAll(array('status'=>Order::STATUS_INVALID),'id IN ('.implode(',', $invaild_ids).')');
//         }
        
        $list_data = array();
		if (!empty($list)) {
	        foreach ($list as $k => $o) {
	            $temp_arr = $o->attributes;
	            $temp_arr['store_name'] = isset($o->store->name) ? $o->store->name : 0;
                $temp_arr['dc_mobile']=isset($o->distribution_order->distribution->mobile)?$o->distribution_order->distribution->mobile:'';
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
	            	
// 	            	foreach ($o->address->attributes as $ak=>$av){
// 	            		$temp_arr[$ak] = $av;
// 	            	}
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
        /**
         * 已支付未完成的订单数
         */
//         $cris = new CDbCriteria();
//         $cris->select = 't.code,t.partner_id,t.member_id,t.store_id,t.type,t.total_price,t.pay_price,t.address_id,t.shipping_type,t.shipping_time,t.pay_status,t.status,t.create_time,t.shipping_fee,t.shipping_time,t.remark';
//         $cris->with = array('store','address');
        
//         $cris->compare('t.partner_id',$this->partnerInfo['id']);
//         $cris->compare('t.store_id', $store['id']);

//         $cris->compare('t.pay_status',  Order::PAY_STATUS_YES);
//         $cris->compare('t.status',  Order::STATUS_PAY);
//         $cris->addCondition('t.status='.Order::STATUS_SEND,'OR');
//         $counts = Order::model()->count($cris);
        
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
    }
    
    
    /**
     * 订单信息
     *
     */
    public function actionDetail() {
    	$tag_name = 'OrderDetails';
//     	$code = $this->getParam('code');
    	if ($this->getParam('onlyTest')) {
    		$code = $this->getParam('code');
    	}else{
            $code =  $this->rsaObj->decrypt($this->getParam('code'));
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
    }
    
    

    /**
     * 送货
     * @param code
     */

     public function actionSend(){
//          $code = $this->getParam('code');   //订单号

         if ($this->getParam('onlyTest')==1) {
         	$code = $this->getParam('code');    //订单号
         }else{
             $code =  $this->rsaObj->decrypt($this->getParam('code'));
         }
         
         $order = Order::model()->getByCode($code);
          if (empty($order)) {
            $this->_error(Yii::t('apiModule.order','订单不存在'));
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
	      	$this->_error(Yii::t('apiModule.order','该订单已发货,不允许重复发货'));
	      }
	      
	      if ($order->status == Order::STATUS_COMPLETE ) {
	      	$this->_error(Yii::t('apiModule.order','该订单已完成,不允许发货'));
	      }
	      
	      if ($order->status == Order::STATUS_CANCEL ) {
	      	$this->_error(Yii::t('apiModule.order','该订单已取消,不允许发货'));
	      }
	      
        if ($order->shipping_type !=Order::SHIPPING_TYPE_SEND ) {
        	 $this->_error(Yii::t('apiModule.order','该订单非送货订单,不允许发货'));
        }
        
        if ($order->status != Order::STATUS_PAY ) {
        	$this->_error(Yii::t('apiModule.order','该订单非已支付状态，不允许发货'));
        }

        $order->status = Order::STATUS_SEND;
        $order->send_time = time();
        $order->save();
        
        //发送短信
        $apiMember = new ApiMember();
        $mobile = $order['mobile'];
        if (empty($mobile)) {
        	//$memberInfo = $apiMember->getInfo($order['member_id']);
			$memberInfo = Member::model()->findByPk($order['member_id']);
        	$mobile = $memberInfo['mobile'];
        }
         
        if (!empty($mobile)) {
//        	$apiMember->sendSms($memberInfo['mobile'], '您好，你的微小企订单['.$order['code'].']已发货，请准备收货。');
            $msg = '您好，你的微小企订单['.$order['code'].']已发货，请准备收货。';
                $apiMember->sendSms($mobile, $msg,ApiMember::SMS_TYPE_ONLINE_ORDER,$target_id=0,$source=  ApiMember::SKU_SEND_SMS,array($order['code']),  ApiMember::SEND_SUCCESS);
        }
       
        $this->_success(Yii::t('apiModule.order','设置发货成功'));
     }
     
   
      /**
     * 签收订单
     *@param code
     * @param goods_code
     */
    public function actionComplete() {

        
        if ($this->getParam('onlyTest')==1) {
        	$code = $this->getParam('code');    //订单号
        	$goods_code = $this->getParam('goods_code');
        }else{
            $code =  $this->rsaObj->decrypt($this->getParam('code'));
            $goods_code =  $this->rsaObj->decrypt($this->getParam('goods_code'));
        }
        
        $order = Order::model()->getByCode($code);
        if (empty($order)) {
            $this->_error(Yii::t('apiModule.order','订单不存在'));
        }
        
        $this->store = Supermarkets::model()->findByPk($order['store_id']);
        $this->_checkStore();
        
        
        if ($order->partner_id !=$this->partnerInfo->id) {
        	$this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
        }
        if($goods_code != $order->goods_code){
            $this->_error(Yii::t('apiModule.order','提货码错误'));
        }
        
        if ($order->status == Order::STATUS_FROZEN) {
        	$this->_error(Yii::t('apiModule.order','订单已冻结'));
        }
        
        if ($order->status == Order::STATUS_COMPLETE) {
        	$this->_error(Yii::t('apiModule.order','订单已签收，不能重复签收'));
        }
        
//         if( $order->status ==Order::STATUS_REFUNDING ){
//             $this->_error('该订单已申请退款');
//         }
        if ($order->pay_status ==Order::PAY_STATUS_YES &&$order->status !=Order::STATUS_COMPLETE &&$order->status !=Order::STATUS_CANCEL ) {
            $sign_rs = Order::orderSign($code, true);
            if ($sign_rs['success'] != true) {
                $this->_error($sign_rs['error_msg']?$sign_rs['error_msg']:ErrorCode::getErrorStr($sign_rs['code']), $sign_rs['code']);
            }
            //首次充值 返回10%金额
            $modelOrder = new Order();
            $modelOrder->giveBackAmountFirstConsume($code,$order->member_id);
        }else{
            $this->_error(Yii::t('apiModule.order','签收失败，订单此时不能签收'));
        }
        $this->_success(Yii::t('apiModule.order','签收成功'));
    }
    
    /**
     * 取消订单(只能取消未支付的订单)
     * @params  code   
     */
     public function actionCancel(){
//          $code = $this->getParam('code');   //订单号

         if ($this->getParam('onlyTest')==1) {
             $code = $this->getParam('code');    //订单号
             $remark = $this->getParam('remark');
         }else{
             $code =  $this->rsaObj->decrypt($this->getParam('code'));
             $remark =  $this->rsaObj->decrypt($this->getParam('remark'));
         }
         $order = Order::model()->getByCode($code);
         if(empty($order)){
             $this->_error(Yii::t('apiModule.order','订单不存在'));
         }
         
         $this->store = Supermarkets::model()->findByPk($order['store_id']);
         $this->_checkStore();
         
          if ($order->partner_id !=$this->partnerInfo['id']) {
        	$this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
        }
        
        if ($order->status == Order::STATUS_FROZEN) {
        	$this->_error(Yii::t('apiModule.order','订单已冻结'));
        }
        
         if($order->pay_status ==Order::PAY_STATUS_YES || $order->status == Order::STATUS_PAY){
             $this->_error(Yii::t('apiModule.order','订单已支付'));
         }
         if($order->status == Order::STATUS_NEW && $order->pay_status == Order::PAY_STATUS_NO){
             $rs = Order::orderCancel($code,false,'店家取消订单');

             if($rs['success']==true){
                 $this->_success(Yii::t('apiModule.order','订单取消成功'));
             }else{
                 $this->_error(Yii::t('apiModule.order','订单取消失败'));
             }
         }else{
         		$this->_error(Yii::t('apiModule.order','该订单此时不能取消'));
         }
     }
     
     
     
     /**
      * 取消订单部分商品  用于已支付订单退款
      * 
      * 逻辑为取消当前订单，再下一张订单
      * 
      * @params  code
      */
     public function actionCancelPart(){
     	//          $code = $this->getParam('code');   //订单号

         if ($this->getParam('onlyTest')==1) {
             $code = $this->getParam('code');    //订单号
             $success_goods = $this->getParam('successGoods');
         }else{
             $code =  $this->rsaObj->decrypt($this->getParam('code'));
             $success_goods =  $this->rsaObj->decrypt($this->getParam('successGoods'));
         }
     	$order = Order::model()->getByCode($code);
     	if(empty($order)){
     		$this->_error(Yii::t('apiModule.order','订单不存在'));
     	}
     	
     	$this->store = Supermarkets::model()->findByPk($order['store_id']);
     	$this->_checkStore();
     	
     	
     	if ($order->partner_id !=$this->partnerInfo['id']) {
     		$this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
     	}
     
     	if ($order->status == Order::STATUS_FROZEN) {
     		$this->_error(Yii::t('apiModule.order','订单已冻结'));
     	}
     
     	if($order->pay_status !=Order::PAY_STATUS_YES || $order->status != Order::STATUS_PAY){
     		$this->_error(Yii::t('apiModule.order','订单未支付'));
     	}
     	$rs = Order::orderCancelPart($code,$success_goods);
     	if($rs['success']==true){
     			$this->_success(Yii::t('apiModule.order','订单取消成功'));
     	}else{
     			$this->_error(Yii::t('apiModule.order','订单取消失败'));
     	}
     	
     	
     }

    /**
     * 交易明细接口
     */
   public function actionTransactionDetails(){
       try{
           if ($this->getParam('onlyTest')==1) {
               $status = $this->getParam('status');    //订单号
           }else{
               $status = $this->rsaObj->decrypt($this->getParam('status'));//all 表示全部   in 表示收入   out表示支出
           }
           if(empty($status)) $status = "all";
           if($status != "all" && $status != "in" && $status != "out") $this->_error("参数错误");
           //查询会员积分余额
           $cash = AccountBalance::getShangJiaCashBalance($this->member);
           $fee = AccountBalance::getMemberXiaofeiAmount($this->member);
           $jiaoyi = AccountBalance::getPartnerGuadanScorePoolBalance($this->member);
           $accountBalance  = $cash + $fee + $jiaoyi;
           $cashData = array();
           $ConsumptionData = array();
           $PifaPointsMarketingData = array();
           $PointsMarketingData = array();
           $GoodsMarketingData = array();
           //查询会员提现记录   会员提现记录
           if($status == "out" || $status == "all"){
               $cashData = Yii::app()->db->createCommand()
                   ->select("money,apply_time create_time")
                   ->from("{{cash_history}}")
                   ->where("member_id =".$this->member." AND status = ".CashHistory::STATUS_TRANSFERED)
                   ->queryAll();
               if(!empty($cashData)){
                   foreach($cashData as $k =>$v){
                       $cashData[$k]['type'] = "提现";
                       $cashData[$k]['money'] = "-".$v['money'];
                       $cashData[$k]['original_money'] = $v['money'];
                   }
               }
//                //查询会员消费记录   会员消费支出
//                $ConsumptionData = Yii::app()->db->createCommand()
//                    ->select("total_price AS money,create_time")
//                    ->from("{{orders}}")
//                    ->where("member_id =".$this->member." AND status = ".Order::STATUS_PAY)
//                    ->queryAll();
//                if(!empty($ConsumptionData)){
//                    foreach($ConsumptionData as $k =>$v){
//                        $ConsumptionData[$k]['type'] = "消费";
//                        $ConsumptionData[$k]['money'] = "-".$v['money'];
//                        $ConsumptionData[$k]['original_money'] = $v['money'];
//                    }
//                }
//               //查询店铺批发积分商品记录   批发积分商品支出
//               $PifaPointsMarketingData = Yii::app()->db->createCommand()
//                   ->select("amount AS money,create_time")
//                   ->from("{{guadan_pifa_order}}")
//                   ->where("member_id =".$this->member." AND status = ".PifaOrder::STATUS_PAY)
//                   ->queryAll();
//               if(!empty($PifaPointsMarketingData)){
//                   foreach($PifaPointsMarketingData as $k =>$v){
//                       $PifaPointsMarketingData[$k]['type'] = "批发积分商品";
//                       $PifaPointsMarketingData[$k]['money'] = "-".$v['money'];
//                       $PifaPointsMarketingData[$k]['original_money'] = $v['money'];
//                   }
//               }
           }

              //查询店铺商品销售记录  商品销售收入
              $GoodsMarketingData = Yii::app()->db->createCommand()
                  ->select("og.supply_price,og.num,o.create_time")
                  ->from("{{orders}} o")
                  ->leftjoin("{{orders_goods}} og","o.id = og.order_id")
                  ->where("o.partner_id =".$this->partner." AND o.status = ".Order::STATUS_COMPLETE)
                  ->queryAll();

              if(!empty($GoodsMarketingData)){
                  foreach($GoodsMarketingData as $k =>$v){
                      $GoodsMarketingData[$k]['type'] = "商品销售";
                      $GoodsMarketingData[$k]['money'] = "+".$v['supply_price']*$v['num'];
                      $GoodsMarketingData[$k]['original_money'] = $GoodsMarketingData[$k]['money'];
                  }
              }

              //查询店铺积分商品销售记录  积分商品销售收入
              $PointsMarketingData = Yii::app()->db->createCommand()
                  ->select("total_price AS money,create_time")
                  ->from("{{guadan_jifen_order}}")
                  ->where("partner_member_id =".$this->member." AND status = ".GuadanJifenOrder::STATUS_PAY." AND type in(1,2)")
                  ->queryAll();
              if(!empty($PointsMarketingData)){
                  foreach($PointsMarketingData as $k =>$v){
                      $PointsMarketingData[$k]['type'] = "积分商品销售";
                      $PointsMarketingData[$k]['money'] = "+".$v['money'];
                      $PointsMarketingData[$k]['original_money'] = $v['money'];
                  }
              }
           $array = array();
           $marketingData =array();
           if($status == "all"){
               //合并交易明细
               $array = array_merge($cashData,$GoodsMarketingData,$PointsMarketingData,$PifaPointsMarketingData);
           }
           if($status == "out"){
               $array = array_merge($cashData,$PifaPointsMarketingData);
           }
           if($status == "in"){
               $array = array_merge($GoodsMarketingData,$PointsMarketingData);
           }
           $marketingData = array_merge($GoodsMarketingData,$PointsMarketingData);
           if(!empty($array)){
               foreach ($array as $k => $v) {
                   $createTime[$k]=$v['create_time'];
               }
           }
           if(empty($createTime)){
               $createTime =array();
           }
           array_multisort($createTime, SORT_DESC, $array);

           if(!empty($array)){
               foreach($array as $k=>$v){

                   $array[$k]['date_create_time'] = date("Y-m-d",$v['create_time']);
               }
           }
           $todayMoney = 0;
           $weekMoney = 0;
           $monthMoney = 0;
           $a = strtotime(date('Y-m'));//本月的时间戳
           $b = strtotime(date('Y-m-d'));//当日的时间戳
           $date = date("Y-m-d");
           $first=1;
           $w = date("w", strtotime($date));
           $d = $w ? $w - $first : 6;
           $now_start = date("Y-m-d", strtotime("$date -".$d." days"));
           $now =  strtotime($now_start);//本周的时间戳
           if(!empty($marketingData)){
              foreach($marketingData as $k =>$v){
                  if($v['create_time'] >= $b && ($v['type']== "积分商品销售" || $v['type']=="商品销售") ){
                      $todayMoney += $v['original_money'];
                  }
                  if($v["create_time"] >= $now && ($v['type']== "积分商品销售" || $v['type']=="商品销售") ){
                      $weekMoney += $v['original_money'];
                  }
                  if($v['create_time'] >= $a && ($v['type']== "积分商品销售" || $v['type']=="商品销售") ){

                      $monthMoney += $v['original_money'];
                  }
              }
           }

           //查询当日的销售收入

          $this->_success(array(
              'data'=>$array,
              'accountBalance'=>$accountBalance,
              'todayMoney'=>$todayMoney,
              "weekMoney"=>$weekMoney,
              'monthMoney'=>$monthMoney
          ));
       }catch (Exception $e){
           $this->_error($e->getMessage());
       }
   }

    /**
     * 销售收入详情接口
     */
    public function actionSaleIncomeInfo(){
         $startTime = $this->getParam('startTime');//查询开始时间
         $endTime = $this->getParam('endTime');//查询结束时间
         $num = $this->getParam('status');//1表示最近一月的销售收入  3表示最近3月的销售收入  6表示最近6月的销售收入
         $GoodsMarketingData = array();
         $PointsMarketingData = array();
        if($num){
            if($startTime || $endTime) $this->_error("参数错误");
        }else{
            if(empty($startTime) || empty($endTime)) $this->_error("参数错误");
        }

        if($num){
            $startTime = strtotime("-{$num} month");
            $dateTime = date("Y.m.d",$startTime)."--".date("Y.m.d");
            //查询店铺商品销售记录  商品销售收入
            $GoodsMarketingData = Yii::app()->db->createCommand()
                ->select("og.supply_price,og.num,o.create_time")
                ->from("{{orders}} o")
                ->leftjoin("{{orders_goods}} og","o.id = og.order_id")
                ->where("o.partner_id =".$this->partner." AND o.status = ".Order::STATUS_COMPLETE." AND o.create_time >=".$startTime)
                ->queryAll();
            if(!empty($GoodsMarketingData)){
                foreach($GoodsMarketingData as $k => $v){
                    $GoodsMarketingData[$k]['money'] = $v['supply_price']*$v['num'];
                }
            }


            //查询店铺积分商品销售记录  积分商品销售收入
            $PointsMarketingData = Yii::app()->db->createCommand()
                ->select("total_price AS money,create_time")
                ->from("{{guadan_jifen_order}}")
                ->where("partner_member_id =".$this->member." AND type in(1,2) AND status = ".GuadanJifenOrder::STATUS_PAY." AND create_time >=".$startTime)
                ->queryAll();

        }
        if(!empty($startTime) && !empty($endTime)){
            $dateTime = date("Y.m.d",$startTime)."--".date("Y.m.d",$endTime);
            //查询店铺商品销售记录  商品销售收入
            $GoodsMarketingData = Yii::app()->db->createCommand()
                ->select("og.supply_price,og.num,o.create_time")
                ->from("{{orders}} o")
                ->leftjoin("{{orders_goods}} og","o.id = og.order_id")
                ->where("o.partner_id =".$this->partner." AND o.status = ".Order::STATUS_COMPLETE." AND o.create_time >=".$startTime." AND o.create_time<=".$endTime)
                ->queryAll();

            //查询店铺积分商品销售记录  积分商品销售收入
            $PointsMarketingData = Yii::app()->db->createCommand()
                ->select("total_price AS money,create_time")
                ->from("{{guadan_jifen_order}}")
                ->where("partner_member_id =".$this->member." AND type in(1,2) AND status = ".GuadanJifenOrder::STATUS_PAY." AND create_time >=".$startTime." AND create_time<=".$endTime)
                ->queryAll();
        }
        if(!empty($GoodsMarketingData)){
            foreach($GoodsMarketingData as $k =>$v){
                $GoodsMarketingData[$k]['type'] = "商品销售";
                $GoodsMarketingData[$k]['money'] = $v['supply_price']*$v['num'];
            }
        }
        if(!empty($PointsMarketingData)){
            foreach($PointsMarketingData as $k =>$v){
                $PointsMarketingData[$k]['type'] = "积分商品销售";
            }
        }
        //合并交易明细
        $array = array_merge($GoodsMarketingData,$PointsMarketingData);
        if(!empty($array)){
            foreach ($array as $k => $v) {
                $createTime[$k]=$v['create_time'];
            }
        }
        if(empty($createTime)){
            $createTime =array();
        }
        array_multisort($createTime, SORT_DESC, $array);
        $totalMoney = 0;
        if(!empty($array)){
            foreach($array as $k => $v){
                $totalMoney += $v['money'];
            }
        }
        $this->_success(array(
            'totalMoney'=>$totalMoney,
           'dateTime'=>$dateTime,
            'data'=>$array
        ));


    }
    
    
     
}
