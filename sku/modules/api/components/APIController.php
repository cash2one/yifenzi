<?php

/**
 * api模块控制器总父类
 * 
 * 需要验证对称加密
 * 
 * @author leo8705
 */
class APIController extends BaseController {


	function beforeAction($action){
		//设置params
		$comm_params = require(ConfigDir . DS . 'params.php');
		$params = require(ConfigDir . DS . 'params_a.php');
		$params = array_merge($comm_params,$params);
		Yii::app()->setParams($params);
        $language = isset($_POST['Language']) ? $this->getPost('Language'):'zh_cn';
        Yii::app()->language = $language;
		parent::beforeAction($action);
		
		return true;
	}
	

	
	
	/**
	 * 解密方法
	 * @param array $request 请求的数据数组
	 * @param array $requiredFields 必填字段数组
	 * @param array $decryptFields 需解密字段数组
	 * @return array
	 * @throws Exception
	 * @author wanyun.liu <wanyun_liu@163.com>
	 */
	public function decrypt($request, $requiredFields = array(), $decryptFields = array())
	{
		$result = array();
		$rsa = new RSA;
		foreach ($this->params as $field) {
			if (isset($request[$field])) {
				// 验证必填字段
				if ($requiredFields && in_array($field, $requiredFields)) {
					if (!trim($request[$field]))
						throw new Exception('提交数据不能为空');
				}
				// 解密字段值
				if ($decryptFields && in_array($field, $decryptFields)) {
					//var_dump($request[$field]);
					if (!$result[$field] = $rsa->decrypt($request[$field]))
						throw new Exception('数据解密失败');
				} else
					$result[$field] = $request[$field];
			} //else
			// throw new Exception('提交的数据不全');
		}
		return $this->magicQuotes($result);
	}
	
	
	/**
	 * 检验验证码是否正确
	 * @param string $phone 手机号
	 * @param string $code 验证码
	 * @return bool 是否正确
	 */
	public function checkVerifyCode($phone, $code)
	{
		$res = Yii::app()->gw->createCommand()->select()->from('{{checkcode}}')->where("phone=:phone", array(':phone' => $phone))->queryRow();

		if (empty($res) || $code != $res['checkcode']) {
			$this->_error('验证码错误');
		} elseif ($res['overtime']+300 < time()) {
			$this->_error(Yii::t('member', '验证码超时'));
		} else {
			Yii::app()->gw->createCommand()->delete('{{checkcode}}', "phone='{$phone}'");
			return true;
		}
	}
	
	
	/**
	 * 访问接口,以post方式传递参数
	 * @param $host            域名
	 * @param $url            地址
	 * @param $data            数据
	 * @param $type            传递方式
	 * return string
	 */
	public function visitHttp($host, $url, $data = array(), $type = 'post')
	{
		$http = new HttpClient($host);
		return $http::quickPost($url, $data);
	}
	
	
	/**
	 * 将对象类型里面的字符串强制转化为字符串
	 */
	public function _objtostring($string)
	{
		return (String)$string;
	}
	
	/**
	 * 写APP日志
	 * @param 需要转的数据
	 */
	public function _writeLog($msg, $step = '')
	{
		$step = $step == '' ? '' : '-' . $step;
		$msg = json_encode($msg);
		@Yii::log($msg, 'info', $this->getId() . '/' . $this->action->getId() . $step);
	}


	
	

}