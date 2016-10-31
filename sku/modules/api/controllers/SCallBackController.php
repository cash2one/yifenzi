<?php
/**
 * 订单后端处理回调接口控制器
 * 
 * 服务器间处理订单
 * 
 * @author leo8705
 *
 */

class SCallBackController extends APIController {

	public $data = '';
	public $sign = '';
	public $sign_key = CALLBACK_SIGN_KEY;
	
	function beforeAction($action){
		parent::beforeAction($action);
		$this->data = CJSON::decode(str_replace("\\\"", "\"",  $this->getParam('data')));
		if (empty($this->data)) {
			$this->_error('no data');
		}
		$this->sign = $this->data['sign']?$this->data['sign']:'';
		return true;
	}
	
	/**
	 * 运行成功返回json
	 * @param type $data
	 */
	protected function _success()
	{
		header("Content-type:text/html;charset=utf-8");
		echo 'success';
		Yii::app()->end();
	}
	

	/**
	 * 检验加密串
	 *
	 * 检验规则是各个参数按规定顺序排列，连成字符串，加上密文私钥，生成md5
	 *
	 */
	protected function _checkEncryption($json_data){
		if (empty($json_data)) {
			$this->_error(Yii::t('apiModule.order','数据字段不能为空！'),ErrorCode::COMMON_PARAMS_LESS);
		}

		if ($this->sign!==md5($json_data.$this->sign_key)) {
			$this->_error(Yii::t('apiModule.order','校验码错误！'),ErrorCode::COMMOM_ENCRYPT_CODE_ERROR);
		}
	}
	
	
	/**
	 * 订单支付流程
	 */
	public function actionPay(){
		
		
	}
	
	
	

	/**
	 * 订单签收
	 */
	public function actionOrderSign(){
		$code = $this->data['code'];
		$this->_checkEncryption($code);
		
		$order = Order::getByCode($code);
		if ($order->status==Order::STATUS_COMPLETE) {
			$this->_success();
		}
		
		
		$sign_rs = Order::orderSign($code,false);
		if ($sign_rs['success']!=true) {
			$this->_error(ErrorCode::getErrorStr($sign_rs['code']),$sign_rs['code']);
		}
	
		if ($sign_rs) {
			//首次充值 返回10%金额
			$modelOrder = new Order();
			$modelOrder->giveBackAmountFirstConsume($code,$order->member_id);
			$this->_success();
		}else{
			$this->_error(Yii::t('apiModule.order','失败'));
		}
	
	}
	
	
	/**
	 * 订单取消
	 */
	public function actionOrderCancel(){
		$code = $this->data['code'];
		$this->_checkEncryption($code);
	
		$order = Order::getByCode($code);
		if ($order->status==Order::STATUS_CANCEL) {
			$this->_success();
		}
	
	
		$sign_rs = Order::orderCancel($code,false);
		if ($sign_rs['success']!=true) {
			$this->_error(ErrorCode::getErrorStr($sign_rs['code']),$sign_rs['code']);
		}
	
		if ($sign_rs) {
			$this->_success();
		}else{
			$this->_error(Yii::t('apiModule.order','失败'));
		}
	
	}
	
	
	
    
}