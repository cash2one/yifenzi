<?php

/**
 * 合作商家控制器
 * 操作(创建合作商家,修改合作商家,删除合作商家,合作商家列表)
 * @author leo8705
 */
class PartnersController extends MController {

    /**
     * 审核合作商家
     */
    public function actionApply($id) {
        $model = $this->loadModel($id);
        $this->performAjaxValidation($model);
        $bdgw = Partners::OperatorRelation($id);
        if ($this->getParam('apply') == 'pass') {
            $model->status = Partners::STATUS_ENABLE;
//             $trans = Yii::app()->db->beginTransaction();
            if ($model->save(false)) {
            	$is_need_sign = false;
            	if (!empty($model->bank_account)) {
            		$apiMember = new ApiMember();
            		$member_info = $apiMember->getInfo($model->member_id);
            		if ((empty($member_info['personalMerchant']) || !$member_info['personalMerchant']) && $member_info['enterprise_id'] == 0) {
            			$is_need_sign = true;
            		}
            	}

            	//api 发送网签请求
            	if ($is_need_sign==true) {
            		$signData = array();
            		$signData['memberId'] = $model->member_id;
            		$signData['accountName'] = $model->bank_account_name;
            		$signData['street'] = $model->bank_area;
            		$signData['bankName'] = $model->bank_name;
            		$signData['account'] = $model->bank_account;
            		
            		$signData['identityCard'] = ATTR_DOMAIN.DS.$model->idcard;
            		$signData['identityImage'] = ATTR_DOMAIN.DS.$model->idcard_img_font;
            		$signData['identityImage2'] = ATTR_DOMAIN.DS.$model->idcard_img_back;
            		
            		$sign_send_rs = $apiMember->netSign($signData);
            		
            		if ($sign_send_rs['success']==true) {
            			$this->setFlash('success', Yii::t('partner','商家通过审核成功'));
//             			$trans->commit();
            			$this->redirect($this->createAbsoluteUrl('partners/admin'));
            		}else{
            			$this->setFlash('error', Yii::t('partner','商家通过审核失败'));
//             			$trans->rollback();
            		}
            		 
            	}
            	
                @SystemLog::record(Yii::app()->user->name . "审核合作商家 {$model['name']} 通过");
                Tool::cache('partnerCache')->delete('partnerInfo_'.$model->member_id);
//                 $trans->commit();
                $this->setFlash('success', Yii::t('partners', "审核合作商家 {$model['name']} 通过"));
                $this->redirect($this->createAbsoluteUrl('partners/admin'));
            }
        }

        if ($this->getParam('apply') == 'unpass') {
            $model->status = Partners::STATUS_UNPASS;
            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name . "审核合作商家 {{$model['name']}} 不通过" );
                Tool::cache('partnerCache')->delete('partnerInfo_'.$model->member_id);
                $this->setFlash('success', Yii::t('partners', "审核合作商家 {$model['name']} 不通过") );
                $this->redirect($this->createAbsoluteUrl('partners/admin'));
            }
        }

        $this->render('apply', array(
            'model' => $model,
            'bdgw'=>$bdgw,
        ));
    }

    /**
     * 禁用合作商家
     */
    public function actionDisable($id) {
        $model = $this->loadModel($id);
        $model->status = Partners::STATUS_DISABLE;
        $model->save();
        @SystemLog::record(Yii::app()->user->name . "禁用合作商家：" . $id);

        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * 审核合作商家
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $this->performAjaxValidation($model);
        $is_update = true;
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
            $model->attributes = $_POST['Partners'];
            $post = $this->getParam('Partners');
            $saveDir = 'partners/' . date('Y/n/j');
            $signSaveDir = 'sellerSign/' . date('Y/n/j');
            $model->license_expired_time = strtotime($post ['license_expired_time']);
            $model->meat_inspection_expired_time = strtotime($post ['meat_inspection_expired_time']);
            $model->health_permit_expired_time = strtotime($post ['health_permit_expired_time']);
            $model->food_circulation_expired_time = strtotime($post ['food_circulation_expired_time']);
            $model->stock_source_expired_time = strtotime($post ['stock_source_expired_time']);
            if ($is_update) $model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'), $oldFileHead);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'license_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileLicense);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'meat_inspection_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileMeat);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'health_permit_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileHealth);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'food_circulation_permit_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileFood);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'stock_source_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileStock);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'bank_card_img', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileBankcard);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'idcard_img_font', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileBankfont);  // 上传图片
            if ($is_update) $model = UploadedFile::uploadFile($model, 'idcard_img_back', $signSaveDir, Yii::getPathOfAlias('att'), $oldFileBankback);  // 上传图片
//             $trans = Yii::app()->db->beginTransaction();
            if ($model->save()) {
                if ($is_update) UploadedFile::saveFile('head', $model->head, $oldFileHead, true);
                if ($is_update) UploadedFile::saveFile('license_img', $model->license_img, $oldFileLicense, true);
                if ($is_update) UploadedFile::saveFile('meat_inspection_certificate_img', $model->meat_inspection_certificate_img, $oldFileMeat, true);
                if ($is_update) UploadedFile::saveFile('health_permit_certificate_img', $model->health_permit_certificate_img, $oldFileHealth, true);
                if ($is_update) UploadedFile::saveFile('food_circulation_permit_certificate_img', $model->food_circulation_permit_certificate_img, $oldFileFood, true);
                if ($is_update) UploadedFile::saveFile('stock_source_certificate_img', $model->stock_source_certificate_img, $oldFileStock, true);
                if ($is_update) UploadedFile::saveFile('bank_card_img', $model->bank_card_img, $oldFileBankcard, true);
                if ($is_update) UploadedFile::saveFile('idcard_img_font', $model->idcard_img_font, $oldFileBankfont, true);
                if ($is_update) UploadedFile::saveFile('idcard_img_back', $model->idcard_img_back, $oldFileBankback, true);
            	$is_need_sign = false;
            	if (!empty($model->bank_account) && $model->status==Partners::STATUS_ENABLE) {
            		$apiMember = new ApiMember();
            		$member_info = $apiMember->getInfo($model->member_id);


            		if ((empty($member_info['personalMerchant']) || !$member_info['personalMerchant']) && $member_info['enterprise_id'] == 0) {
            			$is_need_sign = true;
            		}
            	}
            	
            	//api 发送网签请求
//             	if ($is_need_sign==true) {
//             		$signData = array();
//             		$signData['memberId'] = $model->member_id;
//             		$signData['accountName'] = $model->bank_account_name;
//             		$signData['street'] = $model->bank_area;
//             		$signData['bankName'] = $model->bank_name;
//             		$signData['account'] = $model->bank_account;
            	
//             		$signData['identityCard'] = $model->idcard;
//             		$signData['identityImage'] = ATTR_DOMAIN.DS.$model->idcard_img_font;
//             		$signData['identityImage2'] = ATTR_DOMAIN.DS.$model->idcard_img_back;

            	
//             		$sign_send_rs = $apiMember->netSign($signData);
            	
//             		if ($sign_send_rs['success']==true) {
//             			$this->setFlash('success', Yii::t('partner','编辑合作商家成功'));
// //             			$trans->commit();
//             			$this->redirect(array('admin'));
//             		}else{
//             			$this->setFlash('error', Yii::t('partner','编辑合作商家失败,申请个人商家失败'));
// //             			$trans->rollback();
//             		}
            		 
//             	}
            	 Tool::cache('partnerCache')->delete('partnerInfo_'.$model->member_id);
                 @SystemLog::record(Yii::app()->user->name . "编辑合作商家成功：" . $model->name);
                $this->setFlash('success', Yii::t('partners', '编辑成功'));
//                 $trans->commit();
                $this->redirect($this->createAbsoluteUrl('partners/admin'));
            }
        }
        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * 合作商家列表
     */
    public function actionAdmin() {
        $model = new Partners('search');
        $model->unsetAttributes();
        if (isset($_GET['Partners'])){
            $model->attributes = $_GET['Partners'];
        }
        $this->render('admin', array(
            'model' => $model,
        ));
    }

}
