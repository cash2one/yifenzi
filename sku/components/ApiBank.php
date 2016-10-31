<?php

/**
 * 银行api使用类
 * 
 * @author leo8705
 */
class ApiBank {
    
	
	const CODE_SUCCESS = 200;
	public $apiUrl;
	public $signkey;
	public $tooken;
	public $banks = array(
			'102'=>'工商银行',
			'103'=>'农业银行',
			'104'=>'中国银行',
			'105'=>'建设银行',
			'301'=>'交通银行',
			'403'=>'邮储银行',
			'308'=>'招商银行',
			'303'=>'光大银行',
			'302'=>'中信银行',
			'304'=>'华夏银行',
			'310'=>'浦发银行',
			'305'=>'民生银行',
			'307'=>'平安银行',
			'306'=>'广发银行',
			'309'=>'兴业银行',
	);
	
	public function __construct(){
		$this->apiUrl = BANK_API_URL;
		$this->signkey = BANK_API_SIGN_KEY;
		$this->tooken = md5($this->signkey);
	}
	

	/**
	 * 获取能认证的银行
	 */
	static function getBank($key=null){
		$obj = new self();
		return  $key===null?$obj->banks:(isset($obj->banks[$key])?$obj->banks[$key]:'未知代码');
	}
	
	/**
	 * 根据银行获取代码
	 */
	static function getBankCodeByName($name){
		$obj = new self();
		return array_keys($obj->banks,$name);
	}
	
	
	/**
	 * 个人认证
	 */
	function auth($bankCode,$account,$accountName,$mobile,$cardno){
		$rsArray = array();
		$rsArray['status'] = false;
		$rsArray['msg'] = '系统错误';
		
		if (empty($bankCode) || empty($account) || empty($accountName) || empty($mobile) || empty($cardno)) {
			$rsArray['msg'] = '数据缺失';
			return $rsArray;
		}
		
		//先查本地数据库记录，有的话直接返回记录
		$rc = Yii::app()->db->createCommand()
		->from(BankAuthRecord::model()->tableName())
		->where(
				'bank_code=:bank_code AND account=:account AND account_name=:account_name AND mobile=:mobile AND cardno=:cardno',
				array(':bank_code'=>$bankCode,':account'=>$account,':account_name'=>$accountName,':mobile'=>$mobile,':cardno'=>$cardno)
		)
		->queryRow();
		
		if (!empty($rc)) {
			$rsArray['status'] = $rc['status']*1;
			if ($rsArray['status']) {
				$rsArray['msg'] = '验证成功';
			}else{
				$rsArray['msg'] = '验证失败';
			}
			
			return $rsArray;
		}
		
		$api_path = $this->apiUrl.'memberauth';
		$postData = array(
				'tooken'=>$this->tooken,
				'bc'=>$bankCode , 
				'ano' => $account,
				'aname' => $accountName,
				'mobile' => $mobile,
				'cardno' => $cardno,
		);
		
		$rs = Tool::post($api_path,$postData);
		
		$rsArray = CJSON::decode($rs);
		
		//保存记录
		$rc=  new BankAuthRecord();
		$rc->bank_code = $bankCode;
		$rc->account = $account;
		$rc->account_name = $accountName;
		$rc->mobile = $mobile;
		$rc->cardno = $cardno;
		$rc->status = $rsArray['status'];
		$rc->create_time = time();
		$rc->save();
		
		return $rsArray;
	}
	
	
}
