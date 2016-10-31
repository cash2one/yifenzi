<?php
/**
 * 合作商家控制器
 * 商家的申请，店铺申请，店铺管理等
 * 添加、修改资料、禁用、启用商家、商品管理、订单管理、超市管理、售货机管理等功能。
 * 
 * @author leo8705
 *
 */

class PartnerController extends OpenAPIController {
	/**
	 * 添加合作商家及网签申请
	 */
	public function actionSellerSign(){

		try {
			
			$fields = array('token','mobile','head','province_id','city_id','district_id','street','zip_code','bank_account_name','bank_card_img','bank_name','bank_account_branch',
									'bank_area','idcard','idcard_img_font','idcard_img_back','bank_account','name','bank_province_id','bank_city_id');
			$this->params = $fields;
 			$requiredFields = array('token','mobile','head','province_id','city_id','district_id','street','zip_code','bank_account_name','bank_card_img','bank_name','bank_account_branch',
                'bank_area','idcard','idcard_img_font','idcard_img_back','bank_account','name','bank_province_id','bank_city_id');
 			$decryptFields = array('token');

			if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
				$post = $_REQUEST;
			}else{
				$post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);

			}
            $model = new Partners();
            $model->scenario = 'sellerSign';
            $model->attributes = $post;
            $model->member_id = $this->member;
            $model->gai_number = $post['gaiNumber'];
            $model->status = Partners::STATUS_APPLY;
            $model->create_time = time();
            $saveDir = 'partners/' . date('Y/n/j');
            $signSaveDir = 'sellerSign/' . date('Y/n/j');
            $model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'));
            $model = UploadedFile::uploadFile($model, 'bank_card_img', $signSaveDir, Yii::getPathOfAlias('att'));
            $model = UploadedFile::uploadFile($model, 'idcard_img_font', $signSaveDir, Yii::getPathOfAlias('att'));
            $model = UploadedFile::uploadFile($model, 'idcard_img_back', $signSaveDir, Yii::getPathOfAlias('att'));
            $model->bank_area = Region::getName($model->bank_province_id,$model->bank_city_id);
            $trans = Yii::app()->db->beginTransaction();
            if ($model->save()){
                UploadedFile::saveFile('head', $model->head);
                UploadedFile::saveFile('bank_card_img', $model->bank_card_img);
                UploadedFile::saveFile('idcard_img_font', $model->idcard_img_font);
                UploadedFile::saveFile('idcard_img_back', $model->idcard_img_back);
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'申请成为网签商家:'.$model->name.'| id->'.$model->id);
                $trans->commit();
                $this->_success( Yii::t('partner','商家申请提交成功'));
//
            }else{
                $trans->rollback();
                $this->_error(Yii::t('partner','商家申请提交失败'));
            }
		}catch (Exception $e){
	
			$this->_error($e->getMessage());
		}
	}

    /**
     * 添加合作商家申请
     */
    public function actionPartnerApply(){
        try{
            $fields = array('token','mobile','head','province_id','city_id','district_id','street','zip_code','name');
            $this->params = $fields;
            $requiredFields = array('token','mobile','head','province_id','city_id','district_id','street','zip_code','name');
            $decryptFields = array('token');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $model = new Partners();
            $model->scenario = 'create';
            $model->attributes = $post;
            $model->member_id = $this->member;
            $aMember = new ApiMember();
            //$member_info = $aMember->getInfo($model->member_id);					//会员信息
			$member_info = Member::model()->findByPk($this->member);
            if(empty($member_info)){
                $this->_error(Yii::t('partner','获取会员信息失败'));
            }
            $model->gai_number = $member_info['gai_number'];;
            $model->status = Partners::STATUS_APPLY;
            $model->create_time = time();
            $saveDir = 'partners/' . date('Y/n/j');
            $model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'));
            if($model->save()){
                UploadedFile::saveFile('head', $model->head);
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'申请成为商户:'.$model->name.'| id->'.$model->id);
                $this->_success( Yii::t('partner','商家申请提交成功'));
            }else{
                $this->_error(Yii::t('partner','商家申请提交失败'));
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }

    /**
     * 商家查看信息
     */
    public function actionView(){
        try{
            $model = Partners::model()->findByPk($this->partner);
            $this->_success($model);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }

    /**
     * 商家信息编辑
     */
    public function actionPartnerEdit(){
        try{
            $fields = array('token','mobile','head','province_id','city_id','district_id','street','zip_code','name');
            $this->params = $fields;
            $requiredFields = array('token','mobile','province_id','city_id','district_id','street','zip_code','name');
            $decryptFields = array('token');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);

            }
            $model = Partners::model()->findByPk($this->partner);
            if(empty($model)){
                $this->_error(Yii::t('partner','请先申请成为合作商家！'));
            }
            $model->scenario = 'update';
            $oldFile = $model->head;
            $model->attributes = $post;
            $model->member_id = $this->member;
            $aMember = new ApiMember();
            $member_info = $aMember->getInfo($model->member_id);					//会员信息
            if(empty($member_info)){
                $this->_error(Yii::t('partner','获取会员信息失败'));
            }
            $model->gai_number = $member_info['gai_number'];;
            $model->status = Partners::STATUS_APPLY;
            $model->create_time = time();
            $saveDir = 'partners/' . date('Y/n/j');
            $model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'), $oldFile);
           if($model->save()){
               UploadedFile::saveFile('head', $model->head, $oldFile, true);
               ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'更新商户资料:'.$model->name.'| id->'.$model->id);
               $this->_success(Yii::t('partner','商家资料编辑成功'));
           }else{
               $this->_error(Yii::t('partner','商家资料编辑失败'));
           }

        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }

    
}