<?php
/**
 * token同步接口控制器
 * 
 * 与盖付通同步token
 * 
 * @author leo8705
 *
 */

class STokenController extends SAPIController {

	/**
	 * 同步token
	 */
	public function actionSync(){
		$member_id = $this->data['memberId']*1;
		$token = $this->data['token'];
		$version = $this->data['version'];
		$expir_time = $this->data['expirTime'];
        $lang = $this->data['Language'];
        if(empty($lang)){
            $lang = 'zh_cn';
        }else{
            switch($lang){
                case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
                case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
                case HtmlHelper::LANG_EN : $lang= 'en';break;
            }
        }
        if(!Member::syncFromGw($this->data['memberInfo'])){
            $this->_error(Yii::t('apiModule.order','同步失败'));
        }

        $skumemberInfo = Member::getMemberInfoByGaiId($member_id);
        if (empty($skumemberInfo)) {
        	$this->_error('用户不存在');
        }

		//删除旧的token
		$this->_destoryToken($skumemberInfo['id']);
		
		$client_token = new ClientToken();
		$client_token->member_id = $skumemberInfo['id'];
		$client_token->gai_number = $skumemberInfo['gai_number'];
		$client_token->token = $token;
		$client_token->version = $version;
		$client_token->create_time = time();
		$client_token->expir_time = $expir_time;
        $client_token->lang = $lang;
		$client_token->save();
		
		$this->_success(Yii::t('apiModule.order','同步成功'));
	}

	/**
	 * 清除token
	 */
	public function actionDestory(){
		$token = $this->data['token'];
		$memberId = ClientToken::getMemberByToken($token);
		$this->_destoryToken($memberId);
		
		$this->_success(Yii::t('apiModule.order','清除成功'));
	}
	
	/**
	 * 清除token
	 */
	private function _destoryToken($member_id){
		return ClientToken::destoryToken($member_id);
	}
	
	/**
	 * 更新用户信息
	 */
	public function actionUpdateInfo(){
		$gaiNumber = trim(strtoupper($this->data['gaiNumber']));
		$ApiMember = new ApiMember();
		$rs = $ApiMember->updateInfo($gaiNumber);
		if ($rs) {
			$skuMember = Member::model()->find('gai_number=:gai_number',array(':gai_number'=>$gaiNumber));
			
			if ($skuMember){
				ClientToken::clearTokenCache($skuMember['id']);
				PartnerToken::clearTokenCache($skuMember['id']);
			}
			
		}
		
		
		$this->_success(Yii::t('apiModule.order','同步完成'));
	}
	
    
}