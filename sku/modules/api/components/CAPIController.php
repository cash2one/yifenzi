<?php

/**
 * Client 客户端api模块控制器父类
 * 
 * 需要验证token
 * 
 * @author leo8705
 */
class CAPIController extends APIController {

	public $token;						//token值
	public $member;					//当前用户
	
	function beforeAction($action){

		parent::beforeAction($action);
		$this->_checkToken();			//检验token
		$this->_getAdvertClick();		//获取广告点击
		return true;
	}
	
	/**
	 * 获取token，同时获取当前用户
	 * 
	 * 
	 */
	protected function _checkToken(){
		$this->token = $this->getParam('token');


		$no_token = $this->params('noToken');
		if (!$this->token && !in_array($this->id . '/' . $this->action->id, $no_token)) {
			$this->_error('token不能为空',ErrorCode::CLIENT_NO_TOKEN,'tokenError');
		}

		$this->member = ClientToken::getMemberByToken($this->token);
		
		if (empty($this->member)) {
			$this->_error('token 无效',ErrorCode::CLIENT_NO_MEMBER,'tokenError');
		}
        

        if(isset($_POST['Language'])){
            $lang = $this->getParam('Language');
            switch($lang){
                case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
                case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
                case HtmlHelper::LANG_EN : $lang= 'en';break;
            }
          $sql = "UPDATE {{client_token}} SET lang='{$lang}' WHERE token = '".$this->token."'";
            Yii::app()->db->createCommand($sql)->execute();

        }else{
            $result = Yii::app()->db->createCommand()
                ->select('lang')
                ->from('{{client_token}}')
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
		
	}
	
	/**
	 * 获取广告点击
	 *
	 */
	protected function _getAdvertClick(){
		$advertId = $this->getParam('advertId')*1;
		if (!empty($advertId)) {
			//过滤重复点击		同一操作重复点击不能小于2秒
			$cache_name = 'advertClickTimeC'.$this->token.$this->id . '/' . $this->action->id;
			$click_time = Tool::cache('advertClickC')->get($cache_name);
			
			if (time()-$click_time>2) {
				Tool::cache('advertClickC')->set($cache_name, time());
				//纳入统计
				Yii::app()->db->createCommand(' UPDATE '.AppAdvert::model()->tableName().' SET click=click+1 WHERE id='.$advertId)->execute();
			}
			
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
                                       if($data!=null){
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
		$array['Response']['resultDesc'] = is_string($data)?$data:$resultDesc;
		$array['Response']['resultData'] = is_string($data)?null:$data;
		$array['Response']['resultCode'] = !empty($code)?$code:ErrorCode::COMMOM_ERROR;
	
		echo CJSON::encode($array);
		Yii::app()->end();
	}

    /*
     * 权限检查
     */
    public function _chenck($memberId){
        if($memberId != $this->member)
            $this->_error(Yii::t('api','无权操作'),ErrorCode::CLIENT_TOKEN_ERROR);
    }
    
    
    public static function getStoreClass($stype){
    	$class='';
    	switch ($stype){
    		case Stores::SUPERMARKETS:
    			$class = 'Supermarkets';
    			break;
    		case Stores::MACHINE:
    			$class = 'VendingMachine';
    			break;
    		case Stores::FRESH_MACHINE:
    			$class = 'FreshMachine';
    			break;
    	}
    	
    	return $class;
    	
    }
    
    public static function getStoreGoodsClass($stype){
    	$class='';
    	switch ($stype){
    		case Stores::SUPERMARKETS:
    			$class = 'SuperGoods';
    			break;
    		case Stores::MACHINE:
    			$class = 'VendingMachineGoods';
    			break;
    		case Stores::FRESH_MACHINE:
    			$class = 'FreshMachineGoods';
    			break;
    	}
    	 
    	return $class;
    }
    
    public static function getStoreProjectId($stype){
    	$project_id='';
    	switch ($stype){
    		case Stores::SUPERMARKETS:
    			$project_id =API_PARTNER_SUPER_MODULES_PROJECT_ID;
    			break;
    		case Stores::MACHINE:
    			$project_id =API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID;
    			break;
    		case Stores::FRESH_MACHINE:
    			$project_id =API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID;
    			break;
    	}
    
    	return $project_id;
    }
    /**
     * 验证短信验证码是否正确
     * @author hhb
     * @param string $userphone
     * @param string $check_code
     * @param boolean $is_update	是否删除验证码，因为登录的时候也会验证验证码，而登录的时候只验证不删除
     */
    public function checkCheckCode($userphone,$check_code,$is_update=true){
        $checkCodeTable = Checkcode::model()->tableName();
        $row = Yii::app()->db->createCommand()->select('checkcode,create_time')
            ->from($checkCodeTable)
            ->where('phone = :phone',array(':phone'=>$userphone))
            ->queryRow();

        if(empty($row)){
           $this->_error(Yii::t('MachineForGt','请先获取验证码').'!');

        }

        if($check_code != $row['checkcode']){
            $this->_error(Yii::t('MachineForGt','验证码错误,请重新输入').'!');

        }

        if($row['create_time']>0)
        {
            $check_code_time = 300;
            $now = time();
            if ($row['create_time']+$check_code_time < $now){
               $this->_error(Yii::t('MachineForGt','验证码过期,请重新获取验证码').'!');

            }
        }

        if($is_update)
        {
            //如果需要更新，则删除对应的验证码
            Yii::app()->db->createCommand()->delete($checkCodeTable,'phone = :phone', array(':phone'=>$userphone));
        }
    }
}