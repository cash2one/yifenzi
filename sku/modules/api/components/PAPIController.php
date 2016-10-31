<?php
/**
 *  盖掌柜商家客户端api模块控制器父类
 * 
 * 
 * @author leo8705
 */
class PAPIController extends APIController {

	public $member;					//当前用户id
	public $partner;					//当前商家
	
	protected $key_path;
	protected $rsaObj;
	
	protected $gaiNumber;
	protected $userInfo;
	protected $partnerInfo;
	protected $rsaKey = '';
	protected $primaryKey = 'memberId';
	protected $params;
	protected $member_cache_path = 'PAPICACHE_MEMBER';
	protected $partner_cache_path = 'PAPICACHE_PARTNER';
	protected $partner_cache_by_partner_id_path = 'PAPICACHE_PARTNER_ID';
	
	protected $token;
	protected $store;					//门店
	protected $vendMachine;		//售货机
	protected $freshMachine;		//生鲜机
	
	public $isXiaoer = false;
	public $xiaoerMember = 0;
	
	function beforeAction($action){
		parent::beforeAction($action);

		$this->_setRsa();
		
		$this->token = $this->getParam('token');

		$no_token = $this->params('noTokenPartner')?$this->params('noTokenPartner'):array();
                          $rs = in_array($this->id . '/' . $this->action->id, $no_token);
                           Yii::log('before----token:'.$this->token.'----'.$this->id.'-----'.$this->action->id.'----rs:'.$rs);
		if (!$this->token && !in_array($this->id . '/' . $this->action->id, $no_token)) {
                          Yii::log('after----token:'.$this->token.'----'.$this->id.'-----'.$this->action->id.'----rs:'.$rs);
			$this->_error('token不能为空',ErrorCode::CLIENT_NO_TOKEN);
		}elseif(in_array($this->id . '/' . $this->action->id, $no_token)){
			return true;
		}
		
		//判断是否店小二
		if (substr($this->token, 0,2)==XiaoerClientToken::TOKEN_KEY_PREFIX) {
			$this->isXiaoer = true;
			
			//判断店小二权限
			$xiaoerRights = $this->params('xiaoerPartnerRights')?$this->params('xiaoerPartnerRights'):array();
			if (!in_array($this->id . '/' . $this->action->id, $xiaoerRights)) {
				$this->_error('没有权限',ErrorCode::CLIENT_NO_ACCESS);
				return false;
			}
			
			
			$token_info = XiaoerClientToken::getInfoByToken($this->token);
			
			$this->xiaoerMember = $token_info['member_id'];
			$this->partner = $token_info['partner_id'];
			$this->partnerInfo = Tool::cache($this->partner_cache_by_partner_id_path)->get($this->partner);
			if (empty($this->partnerInfo)) {
				$this->partnerInfo = Partners::model()->find('id=:id',array(':id'=>$this->partner));
				Tool::cache($this->partner_cache_by_partner_id_path)->set($this->partner,$this->partnerInfo,600);
			}
			$this->member = isset($this->partnerInfo['member_id'])?$this->partnerInfo['member_id']:null;
		}else{
			$this->member = PartnerToken::getMemberByToken($this->token);
			$this->partnerInfo = Tool::cache($this->partner_cache_path)->get($this->member);
			if (empty($this->partnerInfo)) {
				$this->partnerInfo = Partners::model()->find('member_id=:member_id',array(':member_id'=>$this->member));
				Tool::cache($this->partner_cache_path)->set($this->member,$this->partnerInfo,600);
			}
		}
		
		if (empty($this->member)) {
			$this->_error('token 无效',ErrorCode::CLIENT_NO_MEMBER);
		}
		
		$this->userInfo = Tool::cache($this->member_cache_path)->get($this->member);
		if (empty($this->userInfo)) {
			$this->userInfo = Member::model()->findByPk($this->member);
			if (empty($this->userInfo)) {

				$this->_error('获取用户信息失败');
			}else{
				Tool::cache($this->member_cache_path)->set($this->member,$this->userInfo,600);
			}
		}

		
		if (empty($this->partnerInfo)) {
			$this->_error('非合作商家');
		}else if($this->partnerInfo['status']!=Partners::STATUS_ENABLE){
			$this->_error('合作商家正在审核或已禁用！',ErrorCode::PARTNER_ACCOUNT_UNPASS);
		}
	
		$this->partner = $this->partnerInfo['id'];


        if(isset($_POST['Language'])){
            $lang = $this->getParam('Language');
            switch($lang){
                case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
                case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
                case HtmlHelper::LANG_EN : $lang= 'en';break;
            }
            $sql = "UPDATE {{partner_token}} SET lang = '$lang' WHERE token ='".$this->token."'";
            Yii::app()->db->createCommand($sql)->execute();
        }else{
            $result = Yii::app()->db->createCommand()
                ->select('lang')
                ->from('{{partner_token}}')
                ->where('token = :token ', array(':token' => $this->token))
                ->queryRow();
            if(empty($result['lang'])){
                $lang = HtmlHelper::LANG_ZH_CN;
                switch($lang){
                    case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
                    case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
                    case HtmlHelper::LANG_EN : $lang= 'en';break;
                }
            }else{
                $lang = $result['lang'];
            }
        }
        Yii::app()->language = $lang;
		
		$this->_getStore();
	
		return true;
	}
	
	protected function _setRsa(){
		$key_path = Yii::getPathOfAlias('keyPath') . DS . 'rsa_private_key.pem';
		$fp = fopen($key_path, "r");
		$this->rsaKey  = fread($fp, 8192);
		fclose($fp);
		
		$this->rsaObj = new RSA();
		$this->rsaObj->private_key = $this->rsaKey;
	}
	
	
	protected function _getStore(){
		$sid =  $this->rsaObj->decrypt($this->getParam('sid'))*1;
		if ($this->getParam('onlyTest')==1) {
			$sid = $this->getParam('sid')*1;
		}
		if (!empty($sid)) {
			$this->store = Supermarkets::model()->findByPk($sid);
			
			//检查店铺
			$this->_checkStore();
		}else{
			$this->store = new Supermarkets();
		}
		
	}
	
	protected function _checkStore(){
		if (empty($this->store['id'])) {
			$this->_error('门店不存在');
		}
		
		if ($this->store['member_id']!=$this->member) {
			$this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
		}
		
		if ($this->store['status']!=Supermarkets::STATUS_ENABLE) {
			$this->_error('当前门店状态为禁用或者未审核，禁止使用。');
		}
		
	}
	
	protected function _getFreshMachine(){
		$sid =  $this->rsaObj->decrypt($this->getParam('fmid'))*1;
		if ($this->getParam('onlyTest')==1) {
			$sid = $this->getParam('fmid')*1;
		}
		if (!empty($sid)) {
			$this->freshMachine = FreshMachine::model()->findByPk($sid);
				
			//检查店铺
			$this->_checkFreshMachine();
		}else{
			$this->freshMachine = new FreshMachine();
		}
	
	}
	
	protected function _checkFreshMachine(){
		if (empty($this->freshMachine['id'])) {
			$this->_error('生鲜机不存在');
		}
	
		if ($this->freshMachine['member_id']!=$this->member) {
			$this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
		}
	
		if ($this->freshMachine['status']!=FreshMachine::STATUS_ENABLE) {
			$this->_error('当前生鲜机状态为禁用或者未审核，禁止使用。');
		}
	
	}
	
	
	protected function _getVendingMachine(){
		$sid =  $this->rsaObj->decrypt($this->getParam('vmid'))*1;
		if ($this->getParam('onlyTest')==1) {
			$sid = $this->getParam('vmid')*1;
		}
		if (!empty($sid)) {
			$this->vendMachine = VendingMachine::model()->findByPk($sid);
	
			//检查店铺
			$this->_checkVendingMachine();
		}else{
			$this->vendMachine = new VendingMachine();
		}
	
	}
	
	protected function _checkVendingMachine(){
		if (empty($this->vendMachine['id'])) {
			$this->_error('售货机不存在');
		}
	
		if ($this->vendMachine['member_id']!=$this->member) {
			$this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
		}
	
		if ($this->vendMachine['status']!=VendingMachine::STATUS_ENABLE) {
			$this->_error('当前售货机状态为禁用或者未审核，禁止使用。');
		}
	
	}
	
	
	/**
	 * 运行成功返回json
	 * @param type $data
	 * $resultDesc type 说明
	 * $actionType type $data
	 *
	 */
	protected function _success($data,$actionType='',$resultDesc='成功')
	{
		header("Content-type:text/html;charset=utf-8");
		// 		$array['result'] = $data;
		// 		$array['resultCode'] = 1;
		$array = array();
		$array['actionType'] = $actionType;
		$array['Response']['resultDesc'] = $resultDesc;
                                        if($data != null){
		$array['Response']['resultData'] = $data;
                                        }
		$array['Response']['resultCode'] = 1;
	
		echo CJSON::encode($array);
		Yii::app()->end();
	}
	
	
	/**
	 * 运行错误返回json
	 * @param type $error
	 */
	protected function _error($data,$code=null,$actionType='',$resultDesc='失败')
	{
		header("Content-type:text/html;charset=utf-8");
		$array = array();
		$array['actionType'] = $actionType;
		$array['Response']['resultDesc'] = $resultDesc;
		$array['Response']['resultData'] = $data;
		$array['Response']['resultCode'] = !empty($code)?$code:ErrorCode::COMMOM_ERROR;
	
		echo CJSON::encode($array);
		Yii::app()->end();
	}
	
	
	
	/**
	 * 加密
	 */
	protected function encrypt($data)
	{
		return $this->rsaObj->encrypt($data);
	}
	

	
	/**
	 * 公用解密方法
	 * @param array|string $post
	 * @param unknown_type $is_public
	 * @return Ambigous <string, multitype:, string|array>|boolean
	 */
	public function decrypt($request, $requiredFields = array(), $decryptFields = array(),$one=false)
	{
		if(is_array($request))
		{
			$result = array();
			foreach ($this->params as $field)
			{
// 				if($field==$this->primaryKey)
// 				{
// 					if(empty($this->code))throw new Exception($this->primaryKey.'提交数据不能为空');
// 					$result[$this->primaryKey] = $this->code;//解密完毕之后，将shopId重新赋值为之前已经解密好的值
// 					continue;
// 				}
				if (isset($request[$field]))
				{
					// 验证必填字段
					if ($requiredFields && in_array($field, $requiredFields)) {
						if (!trim($request[$field]) && (string)$request[$field]!=='0')
							throw new Exception($field.'提交数据不能为空');
					}
					// 解密字段值
					if ($decryptFields && in_array($field, $decryptFields))
					{
						$result[$field] = $this->rsaObj->decrypt($request[$field]);
						if ($result[$field]===false)throw new Exception('数据解密失败');
					}else
						$result[$field] = $request[$field];
	
					//一机一密的处理
					if($field == $this->primaryKey && !empty($this->code))
						$result[$this->primaryKey] = $this->code;
	
				}elseif(in_array($field, $requiredFields)){
					throw new Exception($field.'是必填字段！');
				}else
					continue;
			}
			return $this->magicQuotes($result);
		}else{
			return false;
		}
	}
	
	/**
	 * 
	 */
	public function getParams(){
		
// 		$arr = array_merge($_GET,$_POST);
		$arr = array();
		foreach ($_REQUEST as $k=>$val){
			$arr[$k] = addslashes($val);
		}
		return $arr;
	}
    public function requestSku($params,$url,$project = '105',$api = DOMAIN_API)
    {
        $json = json_encode($params);
        $private_key = $this->_getApiKeys('gw_project',$project);
        $code = md5($json.$private_key);//校验
        $url = $api.'/'.$url;
        $data = array(
            'project'=>$project,
            'data'=>$json,
            'encryptCode'=>$code
        );
        $ch = curl_init() ;
        curl_setopt($ch, CURLOPT_URL,$url) ;
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data); // 在HTTP中的“POST”操作。如果要传送一个文件，需要一个@开头的文件名
        ob_start();
        curl_exec($ch);
        $response = ob_get_contents() ;
        ob_end_clean();
        curl_close($ch) ;
        $res = json_decode($response,true);
        if($res == null)
            throw new Exception($response);
        return $res;
    }
	
	
	
}