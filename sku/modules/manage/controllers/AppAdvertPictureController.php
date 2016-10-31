<?php

/**
 * 广告位图片控制器
 * @author qinghao.ye <qinghaoye@sina.com>
 */
class AppAdvertPictureController extends MController {

    public function filters() {
        return array(
            'rights',
        );
    }

    /**
     * 列表
     */
    public function actionAdmin($advert_id = null) {
        $model = new AppAdvertPicture('search');
        $model->unsetAttributes();
        if (isset($_GET['AppAdvertPicture']))
            $model->attributes = $_GET['AppAdvertPicture'];
        if(!empty($advert_id)){
            $model->advert_id = $this->getParam('advert_id');
        }else{
            $advert_id = $_GET['AppAdvertPicture']['advert_id'];
        }

        $this->render('admin', array(
            'model' => $model,
            'id'=> $advert_id
        ));
    }

    /**
     * 添加
     */
    public function actionCreate($advert_id) {
        $model = new AppAdvertPicture('create');
        $model->advert_id = $this->getParam('advert_id');
        $model->scenario = 'create';
        $this->performAjaxValidation($model);
        $advert = AppAdvert::model()->findByPk($model->advert_id);
        if (isset($_POST['AppAdvertPicture'])) {
            $model->attributes = $_POST['AppAdvertPicture'];
            $model->start_time = strtotime($model->start_time);
            $model->end_time = strtotime($model->end_time);
            if (1 == $advert->type || 2 == $advert->type)
                $model = UploadedFile::uploadFile($model, 'picture', 'ad');
            if ($model->save()) {
                if (1 == $advert->type || 2 == $advert->type)
                    UploadedFile::saveFile('picture', $model->picture);
                    
                @SystemLog::record(Yii::app()->user->name."添加广告位图片：{$model->name}");
                $this->setFlash('success', Yii::t('appAdvertPicture', '添加广告位图片：') . $model->name);
                $this->redirect(array('admin', 'advert_id' => $model->advert_id));
            }else{
            	@SystemLog::record(Yii::app()->user->name."添加广告位图片：{$model->name} 失败");
            }
        }
        $this->render('_form', array(
            'model' => $model,
            'advert' => $advert
        ));
    }

    /**
     * 更新
     * @param type $id
     */
    public function actionUpdate($id) {
       
        $model = $this->loadModel($id);
        $this->performAjaxValidation($model);
        $advert = AppAdvert::model()->findByPk($model->advert_id);
        if (isset($_POST['AppAdvertPicture'])) {
            $picture = $model->picture;
            $model->attributes = $_POST['AppAdvertPicture'];
            $model->start_time = strtotime($model->start_time);

            $model->end_time = strtotime($model->end_time);

            if (1 == $advert->type || 2 == $advert->type)//图片
                $model = UploadedFile::uploadFile($model, 'picture', 'ad');
            if ($model->picture == false)
                $model->picture = $picture;
            if ($model->save()) {
                if (1 == $advert->type || 2 == $advert->type)//图片
                    UploadedFile::saveFile('picture', $model->picture);
                if (is_object(CUploadedFile::getInstance($model, 'picture'))) {
                    if ($this->getParam('oldFile'))
                        UploadedFile::delete(Yii::getPathOfAlias('att') . DS . $this->getParam('oldFile'));
                }
                
                @SystemLog::record(Yii::app()->user->name."修改广告位图片：{$model->name}");
                $this->setFlash('success', Yii::t('appAdvertPicture', '修改广告位图片：') . $model->name);
                $this->redirect(array('admin', 'advert_id' => $model->advert_id));
            }else{
            	@SystemLog::record(Yii::app()->user->name."修改广告位图片：{$model->name} 失败");
            }
        }
        $model->start_time = date('Y-m-d H:i:s', $model->start_time);
        $model->end_time = !empty($model->end_time) ? date('Y-m-d H:i:s', $model->end_time) : '';
        $this->render('_form', array(
            'model' => $model,
            'advert' => $advert
        ));
    }

    /**
     * 删除
     * @param type $id
     */
    public function actionDelete($id) {
        $this->loadModel($id)->delete();
        @SystemLog::record(Yii::app()->user->name."删除广告位图片：{$id}");
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * 批量删除
     */
    public function actionDelAll() {
        if ($this->isPost()) {
            $criteria = new CDbCriteria;
            $criteria->addInCondition('id', $_POST['selectdel']);
            AppAdvertPicture::model()->deleteAll($criteria);
            @SystemLog::record(Yii::app()->user->name."批量删除广告位图片：".implode(',', $_POST['selectdel']));
            if ($this->isAjax()) {
                echo CJSON::encode(array('success' => true));
            } else {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }
        } else {
            throw new CHttpException(400, Yii::t('appAdvertPicture', '无效的请求'));
        }
    }

}
