<?php

/**
 * 店铺分类控制器
 * 操作(创建店铺分类,修改店铺分类,删除店铺分类,店铺分类列表)
 * @author jianlin_lin <hayeslam@163.com>
 */
class StoreCategoryController extends MController {

    /**
     * 创建店铺分类
     */
    public function actionCreate() {
        $model = new StoreCategory;
        $model->scenario = 'create';
        $this->performAjaxValidation($model);

        if (isset($_POST['StoreCategory'])) {
            $model->attributes = $_POST['StoreCategory'];
            $saveDir = 'storeCategory/'. date('Y/n/j');
            $model  = UploadedFile::uploadFile($model, 'style', $saveDir, Yii::getPathOfAlias('att'));  // 上传图片

            if ($model->save()) {
                UploadedFile::saveFile('style', $model->style);
            	@SystemLog::record(Yii::app()->user->name."创建店铺分类：".$model->name);
                $this->setFlash('success', Yii::t('storeCategory', '添加分类') . $model->name . Yii::t('storeCategory', '成功'));
                $this->redirect(array('admin'));
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * 修改店铺分类
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $model->scenario = 'update';
        $this->performAjaxValidation($model);

        if (isset($_POST['StoreCategory'])) {
           
            $oldFile = $model->style;
            $model->attributes = $_POST['StoreCategory'];
            $saveDir = 'storeCategory/'. date('Y/n/j');
            $model = UploadedFile::uploadFile($model, 'style', $saveDir, Yii::getPathOfAlias('att'), $oldFile);  // 上传图片
            if ($model->save()) {
                UploadedFile::saveFile('style', $model->style, $oldFile, true);
            	@SystemLog::record(Yii::app()->user->name."修改店铺分类：".$model->name);
                $this->setFlash('success', Yii::t('storeCategory', '修改分类') . $model->name . Yii::t('storeCategory', '成功'));
//                 $this->redirect(array('manage/storeCategory/admin'));
                $this->redirect(array('/storeCategory/admin'));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * 删除店铺分类
     */
    public function actionDelete($id) {
           $super = Supermarkets::model()->findAll('category_id=:cid',array(':cid'=>$id));
           $machine = VendingMachine::model()->findAll('category_id=:cid',array(':cid'=>$id));
               if(empty($super)&&empty($machine)){
                     $this->loadModel($id)->delete();
                     StoreCategory::model()->afterDelete();
                    @SystemLog::record(Yii::app()->user->name."删除店铺分类：".$id);
                      $this->setFlash('success', Yii::t('storeCategory', '删除店铺分类') .Yii::t('storeCategory', '成功'));
                      $this->redirect(array('/storeCategory/admin'));

               }else{
                     $this->setFlash('error', Yii::t('storeCategory', '店铺分类') . Yii::t('storeCategory', '下存在售货机或门店，删除失败'));
                     $this->redirect(array('/storeCategory/admin'));
               }
    }

    /**
     * 店铺分类列表
     */
    public function actionAdmin() {
        $model = new StoreCategory('search');
        $model->unsetAttributes();
        if (isset($_GET['StoreCategory']))
            $model->attributes = $_GET['StoreCategory'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

}
