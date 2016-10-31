<?php

/**
 * 流水api使用类
 * 
 * @author leo8705
 */
class ApiOrder {

	public $signkey = '';
	public $apiUrl = '';
	
	public $callbackSignkey = CALLBACK_SIGN_KEY;
	
	
	//事务类型
	const TRANSACTION_TYPE_CONSUME = 1;  //消费
	const TRANSACTION_TYPE_DISTRIBUTION = 2; //分配
	const TRANSACTION_TYPE_REFUND = 3; //退款
	const TRANSACTION_TYPE_RETURN = 4; //退货
	const TRANSACTION_TYPE_ORDER_CANCEL = 5; //取消订单
	const TRANSACTION_TYPE_COMMENT = 6; //评论
	const TRANSACTION_TYPE_RIGHTS = 7; //维权
	const TRANSACTION_TYPE_ORDER_CONFIRM = 8; //订单确认
	const TRANSACTION_TYPE_RECHARGE = 9;  //充值
	const TRANSACTION_TYPE_CASH = 10; //提现
	const TRANSACTION_TYPE_CASH_CANCEL = 11; //取消提现
	const TRANSACTION_TYPE_CASH_HONGBAO_APPLY = 12; //红包申请
	const TRANSACTION_TYPE_ASSIGN = 13;  //调拨
	const TRANSACTION_TYPE_CASH_HONGBAO_RECHARGE = 14;//红包充值
	const TRANSACTION_TYPE_OTHER_REFUND = 15;//其它退款
	const TRANSACTION_TYPE_TRANSFER = 16;//旧余额转账
	const TRANSACTION_TYPE_TIAOZHENG = 17;//调整
	
	
	//SKU订单支付
	const BUSINESS_NODE_SKU_PAY_PAY = '3501';  //SKU订单支付-消费支付
	const BUSINESS_NODE_SKU_PAY_FREEZE = '3511';  //SKU订单支付-消费冻结
	//SKU订单签收
	const BUSINESS_NODE_SKU_SIGN_CONFIRM = '3601'; //SKU订单签收-确认消费
	const BUSINESS_NODE_SKU_SIGN_PAYMENT = '3611'; //SKU订单签收-支付货款
	const BUSINESS_NODE_SKU_SIGN_PROFIT = '3612';  // SKU订单签收-利润
	const BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_MEMBER = '3613';  // SKU订单签收-会员消费奖励
	const BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_OTHER = '3614';  // SKU订单签收-收益分配 -其它角色
	//SKU订单取消
	const BUSINESS_NODE_SKU_CANCEL_REFUND = '3701';   //SKU订单取消-收回退款
	const BUSINESS_NODE_SKU_CANCEL_RETURN = '3711';		  //SKU订单取消-退还订单金额
	const BUSINESS_NODE_SKU_CANCEL_PAY_CHARGE = '3702';		  //SKU订单取消-支付手续费
	const BUSINESS_NODE_SKU_CANCEL_GET_CHARGE = '3712';		  //SKU订单取消-收取手续费
	
	//SKU订单
	const OPERATE_TYPE_SKU_PAY = 35;			//SKU订单支付
	const OPERATE_TYPE_SKU_SIGN = 36;			//SKU订单签收
	const OPERATE_TYPE_SKU_CANCEL = 37;			//SKU订单取消
	
	const CODE_SUCCESS = 200;
	
	
// 	public function ppp(){
// 		$addr = 'http://api.gaiwangsku.com/cOrder/create/';
		
// 		$postData['sid'] =4;
// 		$postData['siaddressIdd'] =8;
// 		$postData['type'] =2;
// 		$postData['shippingType'] =1;
// 		$postData['memberId'] =3;
// 		$postData['goods'] ='[{"id":25,"num":1}]';

		
// 		$rs = Tool::post($addr,$postData);
		
// 		var_dump($rs);
// 	}
	
	
	public function __construct(){
		$this->apiUrl = ORDER_API_URL;
		$this->signkey = ORDER_API_SIGN_KEY;
	}
	
	/**
	 * 生成加密串
	 *
	 * 检验规则是data参数值连成json字符串，加上密文私钥，生成md5
	 *
	 */
	private function _createEncryption($json_data){
		return substr(md5($json_data.$this->signkey),5,20);
	}
	
	private function _createCallbackEncryption($json_data){
		return md5($json_data.$this->callbackSignkey);
	}
	
	
	/**
	 * 订单支付 逻辑
	 */
	public function orderPay($code){
		$api_path = $this->apiUrl.'/balance/consume';
		$order = Order::getByCode($code);
		if (empty($order)) {
			return false;
		}

		$aMember = new ApiMember();
		//$member_info = $aMember->getInfo($order->member_id);
		$member_info = Member::model()->findByPk($order['member_id']);
		
		$data = array();
		$data['memberId'] = $order->member_id;
		$data['gwNumber'] = $member_info['gai_number'];
		$data['code'] = $order->code;
		$data['codeId'] = $order->id;
		$data['operateType'] = AccountFlow::BUSINESS_NODE_SKU_PAY_PAY;
		$data['transactionType'] = AccountFlow::TRANSACTION_TYPE_CONSUME;
		$data['money'] = $order->total_price;
		$data['freight'] = $order->shipping_fee;
		$data['remark'] = '用户'.$member_info['gai_number'].'支付sku订单'.$data['code'];
		$data['money'] = $order->total_price;
		$data['callback'] = API_MAIN.'sCallBack/pay';
		
		
		$data = CJSON::encode($data);
		$sign = ApiOrder::_createEncryption($data);
		
		$postData = array('data'=>$data , 'sign' => $sign);
		$rs = Tool::post($api_path,$postData);
		$rsArray = CJSON::decode($rs);
		
		if ($rsArray['status']=='200') {
			return $rsArray['data'];
		}else{
			return false;
		}
	}
	

	/**
	 * 订单签收 流水逻辑
	 */
	public function orderSign($code,$order=null){
		$api_path = $this->apiUrl.'/balance/sign';
		if ($order===null) $order = Order::getDetailByCode($code);
		if (empty($order)) {
			return false;
		}
	
		$aMember = new ApiMember();
// 		$member_info = $aMember->getInfo($order->member_id);					//会员信息
		$member_info = Member::model()->findByPk($order->member_id);
		
		$partner = Partners::model()->findByPk($order->partner_id);
		//计算供货价
		$total_supply_price = 0;
		foreach ($order->ordersGoods as $g){
			$total_supply_price += $g->supply_price*$g->num;
		}
		
		$distribution = Tool::getConfig('assign');
		$data = array();
		$data['memberId'] = $order->member_id;
		$data['gwNumber'] = $member_info['gai_number'];
		$data['code'] = $order->code;
		$data['codeId'] = $order->id;
		$data['operateType'] = AccountFlow::OPERATE_TYPE_SKU_SIGN;
		$data['transactionType'] = AccountFlow::TRANSACTION_TYPE_DISTRIBUTION;
		$data['merchantMemberId'] = $partner->member_id;
		$data['merchantGwNumber'] = $partner->gai_number;
		$data['money'] = $order->total_price;
		$data['freight'] = $order->shipping_fee;
		if ($distribution['isEnable']){
			$data['costPrice'] = $total_supply_price;
		}else{
			$data['costPrice'] = $data['money']-$data['freight'];
		}
		Yii::log('供货价' . var_dump($data['costPrice'], TRUE));
		if ($distribution['isEnable']) $data['distribution'] = json_encode($distribution);		//分配规则
		$type = Order::type($order['type']);
		$data['remark'] = '用户'.$member_info['gai_number'].'签收sku'.$type.'订单';
		$data['remark'] .= $code;
		
		//处理callback
		$callbackSign = $this->_createCallbackEncryption($code);
		$callbackData = array('code'=>$code,'sign'=>$callbackSign);
		$callback = API_MAIN.'sCallBack/OrderSign?data='.CJSON::encode($callbackData);
		$data['callback'] = $callback;
		
		$data = CJSON::encode($data);
		$sign = ApiOrder::_createEncryption($data);

		$postData = array('data'=>$data , 'sign' => $sign);
		$rs = Tool::post($api_path,$postData);
		$rsArray = CJSON::decode($rs);
Yii::log('balance/sign-rs:'.$rs);
Yii::log('balance/sign-key:'.$this->signkey.'-data-'.$data .'-'.$api_path.'-result-'.var_export($rsArray,true));
		$return = array();
		if ($rsArray['status']=='200') {
			$return['success']=true;
		}else{
			$return['success']=false;
			$return['msg'] = isset($rsArray['msg'])?$rsArray['msg']:'未知错误';
		}
		
		return $return;
	}
	
	
	/**
	 * 订单取消 流水逻辑
	 */
	public function orderCancel($code){
		$api_path = $this->apiUrl.'/balance/cancelOrder';
		$order = Order::getDetailByCode($code);
		if (empty($order)) {
			return false;
		}
	
		$aMember = new ApiMember();
		//$member_info = $aMember->getInfo($order->member_id);					//会员信息
		$member_info = Member::model()->findByPk($order['member_id']);
		$partner = Partners::model()->findByPk($order->partner_id);
	
		//计算供货价
		$total_supply_price = 0;
		foreach ($order->ordersGoods as $g){
			$total_supply_price += $g->supply_price*$g->num;
		}
	
		$data = array();
		$data['memberId'] = $order->member_id;
		$data['gwNumber'] = $member_info['gai_number'];
		$data['code'] = $order->code;
		$data['codeId'] = $order->id;
		$data['operateType'] = AccountFlow::OPERATE_TYPE_SKU_CANCEL;
		$data['transactionType'] = AccountFlow::TRANSACTION_TYPE_ORDER_CANCEL;
		$data['merchantMemberId'] = $partner->member_id;
		$data['merchantGwNumber'] = $partner->gai_number;
		$data['money'] = $order->total_price;
		$data['freight'] = $order->shipping_fee;
		$data['remark'] = '取消sku订单'.$data['code'];
	
		//处理callback
		$callbackSign = $this->_createCallbackEncryption($code);
		$callbackData = array('code'=>$code,'sign'=>$callbackSign);
		$callback = API_MAIN.'sCallBack/OrderCancel?data='.CJSON::encode($callbackData);
		$data['callback'] = $callback;
	
		$data = CJSON::encode($data);
		$sign = ApiOrder::_createEncryption($data);
	
		$postData = array('data'=>$data , 'sign' => $sign);
		$rs = Tool::post($api_path,$postData);
		if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
			Yii::log('orderCancel:'.$rs.' postData: '.serialize($postData));
			var_dump($rs);
		}
		
		
		$rsArray = CJSON::decode($rs);
	
		if ($rsArray['status']=='200') {
			return true;
		}else{
			return false;
		}
	}
	
}
