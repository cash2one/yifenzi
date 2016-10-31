<?php
/**
 * 商家店小二接口控制器
 * 
 * @author leo8705
 *
 */
class PXiaoerController extends PAPIController {
	/*
	 * 列表页
	*/
	public function actionList(){
		$page = $this->getParam('page',1)*1;
    	$pageSize = $this->getParam('pageSize',10)*1;
		
		$list = Yii::app()->db->createCommand()
		->select('t.id,m.gai_number,m.real_name,m.username,t.status,m.mobile')
		->from(Xiaoer::model()->tableName().' as t ')
		->leftJoin(Member::model()->tableName().' as m','t.xiaoer_member_id=m.id')
		->where('t.member_id='.$this->member)
		->limit($pageSize)
		->offset(($page-1)*$pageSize)
    	->order('t.id DESC')
		->queryAll();

		$this->_success($list,'xiaoerList');
		
	}
	
	/*
	 * 新增小二
	*/
	public function actionAddXiaoer(){
		
			if ($this->getParam('onlyTest')==1) {
				$gai_number = $this->getParam('gaiNumber');
			}else{
				$gai_number =  $this->rsaObj->decrypt($this->getParam('gaiNumber'));
			}
			
			
			$member_info = Member::getMemberInfoByGaiNumber($gai_number);
			
			if (empty($member_info)) {
				$this->_error('该盖网用户不存在');
			}
			
			if ($member_info['id']==$this->member) {
				$this->_error('不能添加自己为店小二');
			}
			
			
			$check_rs = Yii::app()->db->createCommand()
			->select('count(1) as c')
			->from(Xiaoer::model()->tableName().' as t ')
			->where('t.member_id='.$this->member.' AND t.xiaoer_member_id='.$member_info['id'])
			->queryRow();
			;
			
			if ($check_rs['c']>0) {
				$this->_error('小二已绑定，请勿重复绑定！');
			}
			
			$model = new Xiaoer();
			$model->member_id = $this->partnerInfo['member_id'];
			$model->partner_id = $this->partnerInfo['id'];
			$model->xiaoer_member_id = $member_info['id'];
			$model->create_time = time();
			$model->status = Xiaoer::STAYUS_Y;
			if($model->save(false)){
				$this->_success('添加成功！');
			}else{
				$this->_error('添加失败！');
			}
	}
	

	/*
	 * 更新店小二
	*/
	public function actionUpdateXiaoerStatus(){
		if ($this->getParam('onlyTest')==1) {
			$id = $this->getParam('id')*1;
			$status = $this->getParam('status')*1;
		}else{
			$id =  $this->rsaObj->decrypt($this->getParam('id'))*1;
			$status =  $this->rsaObj->decrypt($this->getParam('status'))*1;
		}
		
		$model = Xiaoer::model()->findByPk($id);
		
		if ($model->member_id!=$this->member) {
			$this->_error('没有权限！');
		}
		
		$model->status = $status==Xiaoer::STAYUS_Y?Xiaoer::STAYUS_Y:Xiaoer::STATUS_N;
		$model->save(false);
		
		$this->_success('设置成功');
		
	}
	
	/*
	 * 删除店小二
	*/
	public function actionDeleteXiaoer(){
		if ($this->getParam('onlyTest')==1) {
			$id = $this->getParam('id')*1;
		}else{
			$id =  $this->rsaObj->decrypt($this->getParam('id'))*1;
		}
		
		Yii::app()->db->createCommand()->delete(
			Xiaoer::model()->tableName(),
			'member_id='.$this->member.' AND id='.$id
		);
		
		$this->_success('删除成功');
	
	}
    
    
	/*
	 * 店小二登录
	*/
	public function actionLogin(){
		
			if ($this->getParam('onlyTest')==1) {
				$username = $this->getParam('userName');
				$password = $this->getParam('password');
				$partner_gai_number = $this->getParam('partnerGaiNumber');
			}else{
				$username =  $this->rsaObj->decrypt($this->getParam('userName'));
				$password =  $this->rsaObj->decrypt($this->getParam('password'));
				$partner_gai_number =  $this->rsaObj->decrypt($this->getParam('partnerGaiNumber'));
			}
			
			$partnerMemberInfo = Member::getMemberInfoByGaiNumber($partner_gai_number);
			if (empty($partnerMemberInfo)) {
				$this->_error('商家账号不存在！');
			}
			
			$rs = Partners::model()->find('member_id=:mid', array(':mid' => $partnerMemberInfo['id']));
			if (empty($rs) || $rs['status']!=Partners::STATUS_ENABLE) {
				$this->_error('商家账号不存在或者未审核通过！');
			}
			
			$model = new ApiMember();
			$memberRs = $model->login($username, $password);
			
			if ($memberRs['success']==true) {
				 
				$memberInfo = Member::getMemberInfoByGaiNumber($memberRs['memberInfo']['gai_number']);
				if (empty($memberInfo)) {
					$this->_error('用户不存在，或者用户资料不完善，禁止使用。');
				}
				
				//检查商家关系
				$check_rs = Yii::app()->db->createCommand()
				->select('count(1) as c')
				->from(Xiaoer::model()->tableName().' as t ')
				->where('t.member_id='.$partnerMemberInfo['id'].' AND t.xiaoer_member_id='.$memberInfo['id'].' AND t.status='.Xiaoer::STAYUS_Y)
				->queryRow();
				;
				
				if($check_rs['c']<=0){
					$this->_error('该商家与店小二非绑定关系！');
				}
				 
				//删除token
				XiaoerClientToken::destoryToken($memberInfo['id']);
				 
				$partnerInfo = Partners::model()->find('member_id=:mid', array(':mid' => $partnerMemberInfo['id']));
				
				$lang = isset($_POST['Language'])?$this->getParam('Language'):HtmlHelper::LANG_ZH_CN;
				switch($lang){
					case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
					case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
					case HtmlHelper::LANG_EN : $lang= 'en';break;
				}
				$token = XiaoerClientToken::createTokenCode($memberInfo['id']);
				$partner_token = new XiaoerClientToken();
				$partner_token->member_id = $memberInfo['id'];
				$partner_token->gai_number = $memberInfo['gai_number'];
				$partner_token->partner_id = $partnerInfo['id'];
				$partner_token->token = $token;
				$partner_token->create_time = time();
				$partner_token->expir_time = strtotime("1 month");
				$partner_token->lang = $lang;
                if(!empty($_POST['deviceId'])) {
                    $partner_token->device_id = $_POST['deviceId'];
                }
				if($partner_token->save(false)){
					$this->_success(array('token'=>$token,'gaiNumber'=>$memberInfo['gai_number'],'skuNumber'=>$memberInfo['sku_number'],'mobile'=>$memberInfo['mobile'],'username'=>$memberInfo['username']),'xiaoerLogin');
				}
			
			} else {
				$this->_error(isset($memberRs['error'])?$memberRs['error']:Yii::t('apiModule.member','用户名或密码错误'));
			}
			
	
	}
	
	
	/*
	 * 店小二登录
	*/
	public function actionLogout(){
		XiaoerClientToken::destoryToken($this->xiaoerMember);
		$this->_success('登出成功');
	}
    
    
}
