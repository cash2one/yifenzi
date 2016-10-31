<?php

/**
 * token api使用类
 * 
 * @author leo8705
 */
class ApiToken {
	public $apiUrl;
	public $signkey;
	public $token;
	
	public function __construct(){
		$this->apiUrl = GAIFUTONG_API_URL;
	}
	
	
	/**
	 * 通过gai_number获取token
	 */
	function getTokenByGaiNumber($gai_number){
		$rsArray = array();
		$rsArray['status'] = false;
		$rsArray['msg'] = '系统错误';
		
		$api_path = $this->apiUrl.'/sku/getUserToken';
		$postData = array(
				'gai_number'=>$gai_number 
		);
                
		$rs = Tool::post($api_path,$postData);
		$rsArray = CJSON::decode($rs);
		
		if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
			Yii::log($api_path.' : $rs  ->'.$rs);
			var_dump($rsArray,$rs);
		}
		
		if (isset($rsArray['Response']['resultDesc'])) {
			return $rsArray['Response']['resultDesc'];
		}else {
			return false;
		}
	}
	
	
}
