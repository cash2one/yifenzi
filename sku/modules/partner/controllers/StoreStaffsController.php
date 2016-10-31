<?php

/**
 * 超市员工管理
 * 操作(查看，添加，修改)
 * @author leo8705
 */
class StoreStaffsController extends SSController {

	public function init()
	{
		$this->curr_menu_name = '/partner/storeStaffs/index';
	}

	/**
	 * 检查当前员工是否属于当前商家
	 * @param unknown $model
	 */
	protected function _checkStaffsAccess($storeStaffs){
		if (empty($storeStaffs) || $storeStaffs->super_id != $this->super_id) {
			throw new CHttpException(403,Yii::t('partnerModule.storeStaffs','你没有权限修改别人的数据！'));
		}
	}
	
    /**
     * 申请、添加新的超市员工
     */
    public function actionAdd() {
        $this->pageTitle = Yii::t('partnerModule.storeStaffs','超市员工添加 _ ').$this->pageTitle;
        $model = new SuperStaffs();
        $model->super_id = $this->super_id;
        $model->scenario = 'add';
//        var_dump($_POST[get_class($model)]);
        $this->performAjaxValidation($model,'superStaffs-form');
//        var_dump($_POST[get_class($model)]);
        if (isset($_POST[get_class($model)])) {
        	$model->attributes = $this->getPost(get_class($model));
        	$model->super_id = $this->super_id;
        	$model->create_time = time();
                  $str = array_merge(range('a','z'),range('A','Z'));
                  shuffle($str);
                  $salt = implode('',array_slice($str,0,6)); 
                 $model->salt = $salt;
                 $pwd = $model->password;
                 $model->password = md5($pwd.$salt);
//                 $head =get_class($model);
//                 $model->head = $_FILES[$head]['name']['head'];
//        	//检查重复用户名
//        	if (SuperStaffs::model()->count(' name=:name  ',array(':name'=>$model->name))) {
//        		$this->setFlash('error', Yii::t('partnerModule.storeStaffs','用户名已存在！'));
//        		$this->render('add', array(
//        		'model' => $model,
//        ));return;
//
//        	} 
        	
        	$saveDir = 'superStaffs/' . date('Y/n/j');
        	$model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'));
        	
        	if ($model->save()) {
        		UploadedFile::saveFile('head', $model->head);
        		$this->setFlash('success', Yii::t('partnerModule.storeStaffs','添加超市员工成功'));
        		SuperLog::create(SuperLog::CAT_COMPANY,SuperLog::logTypeInsert,$model->id,'添加超市员工:'.$model->name.'| id->'.$model->id);
        		$this->redirect(array('index'));
        	} else {
        		$this->setFlash('error', Yii::t('partnerModule.storeStaffs','添加超市员工失败'));
        	}
        }
        
        $this->render('add', array(
        		'model' => $model,
        ));
        
    }
    
    /**
     * 修改
     */
    public function actionUpdate($id)
    {
    	$this->pageTitle = Yii::t('partnerModule.storeStaffs', '小微企业联盟') . $this->pageTitle;
    	$model = SuperStaffs::model()->findByPk($id);
//     	$model->scenario = 'update';
    	$this->_checkStaffsAccess($model);
    	$this->performAjaxValidation($model);
                $oldPassword = $model->password;
                $model->password = '';
//            var_dump($model->attributes);die;
    	if (isset($_POST[get_class($model)])) {
                
    		$oldFile = $model->head;
    		$model->attributes = $this->getPost(get_class($model));
                                    if(empty($model->password)){
                                        $model->password = $oldPassword;
                                    }else{
                                        $str = array_merge(range('a','z'),range('A','Z'));
                                        shuffle($str);
                                        $salt = implode('',array_slice($str,0,6)); 
                                       $model->salt = $salt;
                                       $pwd = $model->password;
                                       $model->password = md5($pwd.$salt);
                                    }       
//                                    var_dump($model->attributes);die;
    		$saveDir = 'superStaffs/' . date('Y/n/j');
    		$model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'),$oldFile);  // 上传图片
                                
    		if ($model->save()) {
    			UploadedFile::saveFile('head', $model->head, $oldFile, true);
    			$this->setFlash('success', Yii::t('partnerModule.storeStaffs','修改员工资料成功'));
    			SuperLog::create(SuperLog::CAT_COMPANY,SuperLog::logTypeUpdate,$model->id,'修改员工资料:'.$model->name);
    			$this->redirect(array('index'));
    		} else {
    			$this->setFlash('error', Yii::t('partnerModule.storeStaffs','修改员工资料失败'));
    		}
    	}
    
    	$this->render('update', array(
    			'model' => $model,
    	));
    }

    /**
     * 查看
     */
    public function actionView() {
        /** @var $model Store */
        $this->layout = 'seller';
        $model = Store::model()->findByPk($this->storeId);
        if (!$model) {
            $this->redirect(array('store/apply'));
        }
        $this->render('view', array('model' => $model));
    }
    

    /**
     * 员工列表
     */
    public function actionIndex()
    {
        $this->pageTitle = Yii::t('partnerModule.storeStaffs','超市门店员工管理 _ ').$this->pageTitle;
        $model = new SuperStaffs('search');
        $model->unsetAttributes(); // clear any default values
          if(empty($this->super_id)){
              $this->setFlash('error', Yii::t('partnerModule.storeStaffs','请先添加门店'));
             $this->redirect(array('store/add'));
        }
        $model->super_id= $this->super_id;
        
        if (isset($_GET[get_class($model)]))
        	$model->attributes = $this->getQuery(get_class($model));
        
        $lists = $model->superSearch();
        $datas = $lists->getData();
        $pager = $lists->pagination;

        $this->render('index', array(
            'model' => $model,
        	'datas'=>$datas,
        	'pager'=>$pager,
        ));
    }


    private function _checkStaffAccess($s_model){
    	if ($s_model->super_id!=$this->super_id) {
    		$this->setFlash('error', Yii::t('partnerModule.storeStaffs','不能修改别人的数据'));
    		$this->redirect(array('index'));
    		exit();
    	}
    }
    
/**
 * 超市门店员工启用
 */
public function actionEnable($id) {
	$this->pageTitle = Yii::t('partnerModule.storeStaffs', '小微企业联盟') . $this->pageTitle;
	$model = SuperStaffs::model()->findByPk($id);
	$this->_checkStaffAccess($model);

	$model->status = SuperStaffs::STATUS_ENABLE;
	
	if ($model->save()) {
			$this->setFlash('success', Yii::t('partnerModule.storeStaffs','启用超市员工启用'));
			SuperLog::create(SuperLog::CAT_COMPANY,SuperLog::logTypeInsert,$model->id,'启用超市员工：:'.$model->name.'| id->'.$model->id);
		} else {
			$this->setFlash('error', Yii::t('partnerModule.storeStaffs','启用超市员工'));
		}
		$this->redirect(array('index'));
}



/**
 * 超市门店员工禁用
 */
public function actionDisable($id) {
	$this->pageTitle = Yii::t('partnerModule.storeStaffs', '小微企业联盟') . $this->pageTitle;
	$model = SuperStaffs::model()->findByPk($id);
	$this->_checkStaffAccess($model);
	
	$model->status = SuperStaffs::STATUS_DISABLE;
	
	if ($model->save()) {
		$this->setFlash('success', Yii::t('partnerModule.storeStaffs','超市员工禁用成功'));
		SuperLog::create(SuperLog::CAT_COMPANY,SuperLog::logTypeInsert,$model->id,'设置超市员工禁用：:'.$model->name.'| id->'.$model->id);
	} else {
		$this->setFlash('error', Yii::t('partnerModule.storeStaffs','设置超市员工禁用'));
	}
	$this->redirect(array('index'));
}



}