<?php
/**
 * 商家管理
 *
 * 操作(增删查改)
 * @author leo8705
 */
class PartnerController extends PController
{

    public function init()
    {
        $this->pageTitle = Yii::t('partnerModule.partner', '小微企业联盟') . Yii::app()->name;
    }

   

    /**
     * 申请
     */
    public function actionApply ()
    {
    	
    	if (!empty($this->partner_id)) {
    		$this->redirect(array('partner/view'));
    	}
    	
    	$enterprise_id = $this->getUser()->getState('enterprise_id');
    	
    	if (empty($enterprise_id)) {
    		$this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/sellerSign'));
    	}
    	
        $this->pageTitle = Yii::t('partnerModule.partner', '申请成为合作商家') . $this->pageTitle;
        $model = new Partners();
        $model->scenario = 'create';
        $this->performAjaxValidation($model);

        if (isset($_POST['Partners'])) {
            $model->attributes = $this->getPost('Partners');
            $model->member_id = $this->getUser()->id;
            
            $aMember = new ApiMember();
            //$member_info = $aMember->getInfo($model->member_id);					//会员信息
			$member_info = Member::model()->findByPk($model->member_id);
            if (empty($member_info)) {
            	$this->setFlash('error', Yii::t('partnerModule.partner','获取会员信息通讯失败，请重试！'));
            	$this->redirect(array('apply'));
            }
            $model->gai_number = $member_info['gai_number'];
            
            $model->status = Partners::STATUS_APPLY;
            $model->create_time = time();
            $saveDir = 'partners/' . date('Y/n/j');
            $model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'));
            if ($model->save()) {
                UploadedFile::saveFile('head', $model->head);
                $this->setFlash('success', Yii::t('partnerModule.partner','商家申请提交成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'申请成为商户:'.$model->name);
                $this->redirect(array('view'));
            } else {
                $this->setFlash('error', Yii::t('partnerModule.partner','商家申请提交失败'));
            }

        }

        $this->render('apply', array(
            'model' => $model,
        ));
    }

    /**
     * 更新资料
     */
    public function actionUpdate()
    {
    	
    	if (empty($this->partner_id)) {
    		$this->redirect(array('partner/apply'));
    	}
    	
        $this->pageTitle = Yii::t('partnerModule.partner','商家资料修改 _ ').$this->pageTitle;
        $model = Partners::model()->findByPk($this->partner_id);
        $model->scenario = 'update';
        $this->performAjaxValidation($model);
        $is_update = true;
        if (empty($model)) {
        	$this->setFlash('error', Yii::t('partnerModule.partner','请先申请成为合作商家！'));
        	$this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/apply'));
        }

        if (isset($_POST['Partners'])) {
        	$oldFileHead = $model->head;
            $oldFileLicense = $model->license_img;
            $oldFileMeat = $model->meat_inspection_certificate_img;
            $oldFileHealth = $model->health_permit_certificate_img;
            $oldFileFood = $model->food_circulation_permit_certificate_img;
            $oldFileStock = $model->stock_source_certificate_img;
            $oldFileBankcard = $model->bank_card_img;
            $oldFileBankfont = $model->idcard_img_font;
            $oldFileBankback = $model->idcard_img_back;
            $model->attributes = $this->getPost('Partners');
            $post = $this->getPost('Partners');
            $model->license_expired_time = strtotime($post ['license_expired_time']);
            $model->meat_inspection_expired_time = strtotime($post ['meat_inspection_expired_time']);
            $model->health_permit_expired_time = strtotime($post ['health_permit_expired_time']);
            $model->food_circulation_expired_time = strtotime($post ['food_circulation_expired_time']);
            $model->stock_source_expired_time = strtotime($post ['stock_source_expired_time']);
            $model->member_id = $this->getUser()->id;
            $model->status = Partners::STATUS_APPLY;
            $saveDir = 'partners/' . date('Y/n/j');
            $signSaveDir = 'sellerSign/' . date('Y/n/j');
//            $model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'),$oldFile);
            if ($is_update) $model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'), $oldFileHead);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'license_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileLicense);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'meat_inspection_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileMeat);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'health_permit_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileHealth);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'food_circulation_permit_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileFood);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'stock_source_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileStock);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'bank_card_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileBankcard);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'idcard_img_font', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileBankfont);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'idcard_img_back', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileBankback);  // 上传图片
            if ($model->save()) {
//                UploadedFile::saveFile('head', $model->head,$oldFile,true);
                 if ($is_update) UploadedFile::saveFile('head', $model->head, $oldFileHead, true);
                if ($is_update) UploadedFile::saveFile('license_img', $model->license_img, $oldFileLicense, true);
                if ($is_update) UploadedFile::saveFile('meat_inspection_certificate_img', $model->meat_inspection_certificate_img, $oldFileMeat, true);
                if ($is_update) UploadedFile::saveFile('health_permit_certificate_img', $model->health_permit_certificate_img, $oldFileHealth, true);
                if ($is_update) UploadedFile::saveFile('food_circulation_permit_certificate_img', $model->food_circulation_permit_certificate_img, $oldFileFood, true);
                if ($is_update) UploadedFile::saveFile('stock_source_certificate_img', $model->stock_source_certificate_img, $oldFileStock, true);
                if ($is_update) UploadedFile::saveFile('bank_card_img', $model->bank_card_img, $oldFileBankcard, true);
                if ($is_update) UploadedFile::saveFile('idcard_img_font', $model->idcard_img_font, $oldFileBankfont, true);
                if ($is_update) UploadedFile::saveFile('idcard_img_back', $model->idcard_img_back, $oldFileBankback, true);
                $this->setFlash('success', Yii::t('partnerModule.partner','商家申请提交成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'更新商户资料:'.$model->name);
                Tool::cache('partnerCache')->delete('partnerInfo_'.$this->getUser()->id);
                $this->redirect(array('view'));
            } else {
                 $model->head = $oldFileHead;
                $this->setFlash('error', Yii::t('partnerModule.partner','商家申请提交失败'));
            }

        }
    	
        $this->render('update', array(
            'model' => $model,
        ));
    }


    /**
     * 查看资料 
     */
    public function actionView()
    {
        $this->pageTitle = Yii::t('partnerModule.partner','商家资料 _ ').$this->pageTitle;
        $model = Partners::model()->findByPk($this->partner_id);
        if (!$model) {
            $this->redirect(array('partner/apply'));
        }
        $this->render('view', array('model' => $model));
    }
    
    
    /**
     * 网签申请  
     * 
     * 申请同时
     * 
     */
    public function actionSellerSign()
    {
    	 
    	$this->pageTitle = Yii::t('partnerModule.partner', '申请网签') .'-'. $this->pageTitle;
    	$model = new Partners();
    	$model->scenario = 'sellerSign';
    	$this->performAjaxValidation($model);
    	
    	$check = Partners::model()->find('member_id='.$this->getUser()->id);
    	
    	if (!empty($check)) {
    		$this->setFlash('error','请勿重复申请！');
    		$this->redirect('/partner/view');
    	}
    
    	if (isset($_POST['Partners'])) {
    		$model->attributes = $this->getPost('Partners');
    		$model->member_id = $this->getUser()->id;
            $post = $this->getPost('Partners');
            $model->license_expired_time = strtotime($post ['license_expired_time']);
            $model->meat_inspection_expired_time = strtotime($post ['meat_inspection_expired_time']);
            $model->health_permit_expired_time = strtotime($post ['health_permit_expired_time']);
            $model->food_circulation_expired_time = strtotime($post ['food_circulation_expired_time']);
            $model->stock_source_expired_time = strtotime($post ['stock_source_expired_time']);
    
//     		$aMember = new ApiMember();
//     		$member_info = $aMember->getInfo($model->member_id);					//会员信息
//     		if (empty($member_info)) {
//     			$this->setFlash('error', Yii::t('partnerModule.partner','获取会员信息通讯失败，请重试！'));
//     			$this->redirect(array('apply'));
//     		}
    		$model->gai_number = $this->getUser()->getState('gai_number');
    
    		$model->status = Partners::STATUS_APPLY;
    		$model->create_time = time();
    		$saveDir = 'partners/' . date('Y/n/j');
    		$signSaveDir = 'sellerSign/' . date('Y/n/j');
    		$model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'));
    		$model = UploadedFile::uploadFile($model, 'bank_card_img', $signSaveDir, Yii::getPathOfAlias('att'));
    		$model = UploadedFile::uploadFile($model, 'idcard_img_font', $signSaveDir, Yii::getPathOfAlias('att'));
    		$model = UploadedFile::uploadFile($model, 'idcard_img_back', $signSaveDir, Yii::getPathOfAlias('att'));
            $model = UploadedFile::uploadFile($model, 'license_img', $signSaveDir, Yii::getPathOfAlias('att'));
            $model = UploadedFile::uploadFile($model, 'meat_inspection_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
            $model = UploadedFile::uploadFile($model, 'health_permit_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
            $model = UploadedFile::uploadFile($model, 'food_circulation_permit_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
            $model = UploadedFile::uploadFile($model, 'stock_source_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
    		$model->bank_area = Region::getName($model->bank_province_id,$model->bank_city_id,$model->bank_district_id);
    		
    		
    		$trans = Yii::app()->db->beginTransaction();
    		
    		if ($model->save()) {
    			UploadedFile::saveFile('head', $model->head,null,true);
    			UploadedFile::saveFile('bank_card_img', $model->bank_card_img,null,true);
    			UploadedFile::saveFile('idcard_img_font', $model->idcard_img_font,null,true);
    			UploadedFile::saveFile('idcard_img_back', $model->idcard_img_back,null,true);
                UploadedFile::saveFile('license_img', $model->license_img,null,true);
                UploadedFile::saveFile('meat_inspection_certificate_img', $model->meat_inspection_certificate_img,null,true);
                UploadedFile::saveFile('health_permit_certificate_img', $model->health_permit_certificate_img,null,true);
                UploadedFile::saveFile('food_circulation_permit_certificate_img', $model->food_circulation_permit_certificate_img,null,true);
                UploadedFile::saveFile('stock_source_certificate_img', $model->stock_source_certificate_img,null,true);
    			
    			ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'申请成为网签商家:'.$model->name,true);
    			
    			
    			
//     			//api 发送网签请求
//     			$signData = array();
//     			$signData['memberId'] = $this->getUser()->id;
//     			$signData['accountName'] = $model->bank_account_name;
//     			$signData['street'] = $model->bank_area;
//     			$signData['bankName'] = $model->bank_name;
//     			$signData['account'] = $model->bank_account;
    			
//     			$signData['identityCard'] = ATTR_DOMAIN.DS.$model->idcard;
//     			$signData['identityImage'] = ATTR_DOMAIN.DS.$model->idcard_img_font;
//     			$signData['identityImage2'] = ATTR_DOMAIN.DS.$model->idcard_img_back;
    			
//     			$apiMember = new ApiMember();
//     			$sign_send_rs = $apiMember->netSign($signData);
    			
//     			if ($sign_send_rs['success']==true) {
//     				$this->setFlash('success', Yii::t('partnerModule.partner','商家申请提交成功'));
//     				$trans->commit();
//     				$this->redirect(array('view'));
//     			}else{
//     				$this->setFlash('error', Yii::t('partnerModule.partner','商家网签信息提交失败'));
//     				$trans->rollback();
//     			}
    			
    			$this->setFlash('success', Yii::t('partnerModule.partner','商家申请提交成功'));
    			$trans->commit();
    			$this->redirect(array('view'));
    			
    		} else {
    			$this->setFlash('error', Yii::t('partnerModule.partner','商家申请提交失败'));
    		}
    
    	}
    
    	$this->render('sellerSign', array(
    			'model' => $model,
    	));
    }
    
    /**
     * 设置当前操作商家
     * @param unknown $id
     */
    protected function _setPartner($id){
    	$id = $id*1;
    	
    	if ($id ==$this->partnerInfo['member_id']){
    		$this->setSession('curr_act_member_id',$this->partnerInfo['member_id']);
    		$this->setSession('curr_act_partner_id',$this->partnerInfo['id']);
    		return true;
    	}
    	
    	$check = Yii::app()->db->createCommand()
    	->from(OperatorRelation::model()->tableName())
    	->where('member_id=:member_id AND operator_member_id=:operator_member_id AND status='.OperatorRelation::STATUS_ENABLE,array(':operator_member_id'=>$this->partnerInfo['member_id'],':member_id'=>$id))
    	->queryRow();
    	
    	if (!empty($check)) {
    		$this->setSession('curr_act_member_id',$check['member_id']);
    		$this->setSession('curr_act_partner_id',$check['partner_id']);
    	}else{
    		$this->setFlash('error','没有权限！');
    		$this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/operChange'));
    	}
    	
    }
    
    /**
     * 切换当前超市门店
     */
    public function actionOperChange() {
    	$this->pageTitle = Yii::t('partnerModule.store', '切换商家 _ ') . $this->pageTitle;
    	$model = new OperatorRelation('search');
    	$model->unsetAttributes();
    
    	$criteria = new CDbCriteria();
    	$criteria->select = 't.member_id,m.gai_number';
    	$criteria->order = 't.id DESC';
    	$criteria->compare('t.operator_member_id', $this->user->id);     //根据条件查询
    	$criteria->compare('t.status', OperatorRelation::STATUS_ENABLE);     //根据条件查询
//     	$criteria->group = ' t.member_id ';
    	$count = OperatorRelation::model()->count($criteria);
    	$pager = new CPagination($count);
    	$pager->pageSize=10;
    	$pager->applyLimit($criteria);
    	$criteria->join = ' LEFT JOIN '.Partners::model()->tableName().' as m ON t.member_id=m.member_id ';
//     	$criteria->join .= ' LEFT JOIN '.Member::model()->tableName().' as om ON t.operator_member_id=m.id ';
    	$partner = OperatorRelation::model()->findAll($criteria);
    
    	if ($this->getParam('mid')) {
    		$this->_setPartner($this->getParam('mid'));
    		
    		//自动切换超市
    		$this->setSession('curr_super_store_id',null);
    		
    		$this->setFlash('success', Yii::t('partnerModule.store', '切换成功'));
    		$this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/operChange'));
    
    	}
    	
    	if (isset($_REQUEST['onlyTest'])) {
    		var_dump($partner);
    	}
    	
    	
    	$self[] = new OperatorRelation();
    	$self[0]['member_id']  = $this->partnerInfo['member_id'];
    	$self[0]['gai_number']  = $this->partnerInfo['gai_number'];
    	
    	$partner = array_merge($self,$partner);

    	$curr_partner = array();
    	foreach ($partner as $p){
    		if ($p['member_id']==$this->curr_act_member_id) {
    			$curr_partner = $p;
    		}
    	}
    	
    	$this->render('change', array(
    			'partners' => $partner,
    			'curr_partner'=>$curr_partner,
    			'pager'=>$pager,
    	));
    }
     /*
     * 列表页
     */
    public function actionXiaoEr(){
      $model  = new Xiaoer();
      $model->member_id = $this->partnerInfo['member_id'];
      $model->partner_id = $this->partnerInfo['id'];
      $xiao = $model->partnerSearch();
      $data = $xiao->getData();
      $pager = $xiao->pagination;
        
       $this->render('xiaoer',array('data'=>$data,'pager'=>$pager));
    }
    
    /*
     * 新增小二
     */
    public function actionCreateXiao(){
        $model = new Xiaoer();
        $model->member_id = $this->partnerInfo['member_id'];
        $model->partner_id = $this->partnerInfo['id'];
        $model->partner_gai_number = $this->partnerInfo['gai_number'];
        $model->scenario='create';
         $this->performAjaxValidation($model);
        if(isset($_POST['Xiaoer'])){
            $post = $this->getParam('Xiaoer');
            if(!empty($post['gai_number'])){
                $xiao = Member::getMemberInfoByGaiNumber($post['gai_number']);
               if(empty($xiao)){
                       $this->setFlash('error', Yii::t('partner', '请确认盖网号是否正确！'));
                       $this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/createXiao'));
               }       

               if($xiao['id']==$this->curr_act_member_id){
	               	$this->setFlash('error', Yii::t('partner', '不能添加店家为店小二！'));
	               	$this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/createXiao'));
               }
               
            }
            
//            var_dump($xiao);die;
            $model->xiaoer_member_id = $xiao['id'];
            $model->attributes = $post;
//            $model->member_id = $this->partnerInfo['member_id'];
            $model->create_time = time();
            if($model->save()){
                $this->setFlash('success', Yii::t('partnerModule.store', '添加小二成功'));
                $this->redirect('xiaoEr');
            }else{
                 $this->setFlash('error', Yii::t('partnerModule.store', '添加小二失败'));
            }
        }
        $this->render('createXiao',array('model'=>$model));
    }
    
    /*
     * 更新店小二
     */
    public function actionUpdateXiao($id){
        $model = Xiaoer::model()->findByPk($id);
        $xiao = Member::model()->findByPk($model->xiaoer_member_id);
        $model->gai_number = $xiao['gai_number'];
        $model->scenario='update';
         $this->performAjaxValidation($model);
         if(isset($_POST['Xiaoer'])){
            $post = $this->getParam('Xiaoer');
//            var_dump($post);
            $model->attributes = $post;
            $model->member_id = $this->partnerInfo['member_id'];
            $model->create_time = time();
//            var_dump($model->attributes);die;
            if($model->save()){
                $this->setFlash('success', Yii::t('partnerModule.store', '修改小二成功'));
                $this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/xiaoEr'));
            }else{
                 $this->setFlash('error', Yii::t('partnerModule.store', '修改小二失败'));
            }
        }
         $this->render('updateXiao',array('model'=>$model));
    }
    
    /*
     * 删除店小二
     */
    public function actionDelete($id){
        $model =Xiaoer::model()->findByPk($id);
        if($model->delete()){
            $this->setFlash('success', Yii::t('partnerModule.store', '删除小二成功'));
                $this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/xiaoEr'));
        }else{
             $this->setFlash('success', Yii::t('partnerModule.store', '删除店小二失败'));
        }
        
    }
    
    /*
     * 操作日志
     */
    public function actionLogView(){

        $partnerInfo = $this->partnerInfo;  
        if($partnerInfo['member_id']!=Yii::app()->user->id){
            $this->setFlash('error', Yii::t('partnerModule.partner','没有权限查看！'));
        }
      $logdata = new ParnetLog('search');
       $logdata->unsetAttributes();
       $logdata->member_id = $partnerInfo['member_id'];
        
        $lists = $logdata->search();
        $data = $lists->getData();
        $pager = $lists->pagination;

        $this->render('logView', array(
           'logdata'=>$data,
            'pager' => $pager,
        ));
    }
}
