<?php

/**
 * 对服务器用的 api模块控制器父类  
 * 
 * 需要验证对称加密
 * 
 * @author leo8705
 */
class SAPIController extends APIController {

	public $encryptCode;				//接收的校验码
	public $project;						//访问项目
	public $data;							//数据
	public $jsonData;					//json数据
	
	function beforeAction($action){
		parent::beforeAction($action);
		$this->project = $this->getParam('project');
		$this->jsonData = str_replace("\\\"", "\"",  $this->getParam('data'));//处理转义
		$this->data = CJSON::decode($this->jsonData);  
//                $this->encryptCode = md5($this->jsonData.'vvew@fjnc#sld!333iou^sddcxdd');
		$this->encryptCode = $this->getParam('encryptCode');
		if ($this->getParam('onlyTest')==1 && IS_DEVELOPMENT) {
			//测试动作
		}else{
			$this->_checkEncryption($this->jsonData);
		}
		
// 		$this->_checkEncryption($this->jsonData);
		  
		return true;
	}
	
	/**
	 * 检验加密串 
	 * 
	 * 检验规则是各个参数按规定顺序排列，连成字符串，加上密文私钥，生成md5
	 * 
	 */
	protected function _checkEncryption($json_data){
		if (empty($json_data)) {
			$this->_error('数据字段不能为空！',ErrorCode::COMMON_PARAMS_LESS);
		}
		$private_key = $this->_getPrivateKey($this->project);
		if ($this->encryptCode!==md5($json_data.$private_key)) {
			$this->_error('校验码错误！',ErrorCode::COMMOM_ENCRYPT_CODE_ERROR);
		}
	}
	
	/**
	 * 运行成功返回json
	 * @param type $data
	 */
	protected function _success($data)
	{
		header("Content-type:text/html;charset=utf-8");
		$array['result'] = $data;
		$array['resultCode'] = 1;
		echo CJSON::encode($array);
		Yii::app()->end();
	}
	
	
	/**
	 * 运行错误返回json
	 * @param type $error
	 */
	protected function _error($data,$code=null)
	{
		header("Content-type:text/html;charset=utf-8");
		$array = array('resultCode' =>  !empty($code)?$code:ErrorCode::COMMOM_ERROR);
		$array['resultDesc'] = $data;
		echo CJSON::encode($array);
		Yii::app()->end();
	}
	
	/**
	 * 获取项目秘钥
	 * 
	 */
	protected function _getPrivateKey($project){
		$key = $this->_getApiKeys('gw_project',$project);
		return $key;
	}

}