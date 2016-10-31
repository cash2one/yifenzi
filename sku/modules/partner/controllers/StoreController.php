<?php

/**
 * 超市门店管理
 * 操作(查看，店铺申请，修改)
 * @author leo8705
 */
class StoreController extends SSController {

    public function init() {
        $this->pageTitle = Yii::t('partnerModule.store', '小微企业联盟');
    }

    /**
     * 切换当前超市门店
     */
    public function actionChange() {
        $this->pageTitle = Yii::t('partnerModule.store', '切换门店 _ ') . $this->pageTitle;
        $model = new Supermarkets('search');
        $model->unsetAttributes();

        $criteria = new CDbCriteria();
        $criteria->order = 'id ASC';
        $criteria->compare('member_id', $this->curr_act_member_id);     //根据条件查询
        $count = Supermarkets::model()->count($criteria);
        $pager = new CPagination($count);
        $pager->pageSize=10;
        $pager->applyLimit($criteria); 
        $super = Supermarkets::model()->findAll($criteria);
        if ($this->getParam('super_id')) {
            $this->_setSuper($this->getParam('super_id'));
            $name = Supermarkets::model()->findByPk($this->getParam('super_id'));
            $this->setFlash('success', Yii::t('partnerModule.store', '切换成功'));
            ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$_REQUEST['super_id'],'切换门店:'.$name->name);
            //保存操作记录
           $this->_saveLog(ParnetLog::CAT_BIZ, ParnetLog::logTypeUpdate, $_REQUEST['super_id']);

        }

        $curr_super = 0;
        if (!empty($super)) {
            //默认取第一个为当前超市门店
            if (empty($this->super_id)) {
                $this->super_id = $super[0]['id'];
                $this->_setSuper($super[0]['id']);
            }
            $curr_super = $model->find("id={$this->super_id}");
        }

        $this->render('change', array(
            'supers' => $super,
            'curr_super' => $curr_super,
            'pager'=>$pager,
        ));
    }

    /**
     * 申请、添加新的超市门店
     */
    public function actionAdd() {
        $this->pageTitle = Yii::t('partnerModule.store', '超市门店申请 _ ') . $this->pageTitle;
        $model = new Supermarkets();
        $model->member_id = $this->curr_act_member_id;
        $model->scenario = 'create';
        $this->performAjaxValidation($model);
        if (isset($_POST['Supermarkets'])) {
            $model->attributes = $this->getPost('Supermarkets');
            $post = $this->getPost('Supermarkets');
            $referrals_id = Member::getByGwNumber($post['referrals_gai_number']);
            $model->referrals_id = count($referrals_id) == 0 ? '' : $referrals_id['id'];
            $model->member_id = $this->curr_act_member_id;
            $model->partner_id = $this->curr_act_partner_id;           
           $memberTotalPayPreStoreLimit = Tool::getConfig('amountlimit', 'memberTotalPayPreStoreLimit');
            $model->max_amount_preday = !empty($memberTotalPayPreStoreLimit)?$memberTotalPayPreStoreLimit:'';
            $model->create_time = time();
            $saveDir = 'Supermarkets/' . date('Y/n/j');
            $model = UploadedFile::uploadFile($model, 'logo', $saveDir, Yii::getPathOfAlias('att'));
            
            $fee_config = $this->getConfig('assign');
            $model->fee = isset($fee_config['storeDefaultFee'])?$fee_config['storeDefaultFee']:8;
            
            if ($model->save()) {
                UploadedFile::saveFile('logo', $model->logo);
                //已放到模型
//                                     $stores = new Stores();
//                                     $stores->stype = Stores::SUPERMARKETS;
//                                     $stores->target_id = $model->id;
//                                     $stores->create_time = $model->create_time;
//                                     $stores->save();
                $this->setFlash('success', Yii::t('partnerModule.store', '添加超市门店成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeInsert, $model->id, '添加超市门店:' . $model->name);
                $this->redirect(array('change'));
            } else {
                $this->setFlash('error', Yii::t('partnerModule.store', '添加超市门店失败'));
            }
        }

        $this->render('add', array(
            'model' => $model,
        ));
    }

    /**
     * 查看
     */
    public function actionView() {
        $this->pageTitle = Yii::t('partnerModule.store', '查看超市门店 _ ') . $this->pageTitle;
        $model = Supermarkets::model()->findByPk($this->super_id);
        $cate = StoreCategory::model()->findByPk($model->category_id);
        if (!$model) {
            $this->setFlash('error', Yii::t('partnerModule.store', '请先添加门店'));
            $this->redirect(array('store/add'));
        }
        $this->render('view', array('model' => $model,'cate'=>$cate));
    }

    /**
     * 更新
     */
    public function actionUpdate() {
        $this->pageTitle = Yii::t('partnerModule.store', '更新门店信息 _ ') . $this->pageTitle;
        $model = $this->store;
        $referrals = Member::model()->findByPk($model->referrals_id);
        $model->referrals_gai_number =$referrals['gai_number'];
        $model->scenario = 'update';
        $this->performAjaxValidation($model);
        if (empty($model)) {
            $this->setFlash('error', Yii::t('partnerModule.store', '请先添加门店'));
            $this->redirect(array('store/add'));
        }
        if (isset($_POST['Supermarkets'])) {
        	
        	$old_modle = clone $model;
        	$check_field = array('name','category_id','mobile','logo','type','province_id','city_id','district_id','street');
        	
            $oldFile = $model->logo;
            $model->attributes = $this->getPost('Supermarkets');
            $post = $this->getPost('Supermarkets');
             $referrals_id = Member::getByGwNumber($post['referrals_gai_number']);
            $model->referrals_id = count($referrals_id) == 0 ? '' : $referrals_id['id'];
            $saveDir = 'superStore/' . date('Y/n/j');
            $model = UploadedFile::uploadFile($model, 'logo', $saveDir, Yii::getPathOfAlias('att'), $oldFile);
            
            foreach ($check_field as $val){
            	if ($old_modle->$val!=$model->$val) {
            		$model->status = Supermarkets::STATUS_APPLY;
            		break;
            	}
            }
            
            
            if ($model->save()) {
                UploadedFile::saveFile('logo', $model->logo, $oldFile, true);
                if ($model->status == Supermarkets::STATUS_APPLY) Yii::app()->db->createCommand()->update(SuperGoods::model()->tableName(), array('status' => SuperGoods::STATUS_DISABLE), 'super_id=:id', array(':id' =>$model->id));  
                $this->setFlash('success', Yii::t('partnerModule.store', '修改超市门店信息成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '修改超市门店信息:' . $model->name);
                $this->refresh();
            } else {
                $model->logo = $oldFile;
                $this->setFlash('error', Yii::t('partnerModule.store', '修改超市门店信息失败'));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

}
