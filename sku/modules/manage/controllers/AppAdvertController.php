<?php

/**
 * 广告位控制器
 * @author qinghao.ye <qinghaoye@sina.com>
 */
class AppAdvertController extends MController {

    public function filters() {
        return array(
            'rights',
        );
    }

    /**
     * 列表
     */
    public function actionAdmin() {
        $model = new AppAdvert('search');
        $model->unsetAttributes();
        if (isset($_GET['AppAdvert']))
            $model->attributes = $_GET['AppAdvert'];
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * 添加
     */
    public function actionCreate() {
        $model = new AppAdvert('create');
        $this->performAjaxValidation($model);
        if (isset($_POST['AppAdvert'])) {
            $model->attributes = $_POST['AppAdvert'];
            if ($model->save()) {
            	@SystemLog::record(Yii::app()->user->name."添加广告位：{$model->name}");
                $this->setFlash('success', Yii::t('advert', '添加广告位成功：') . $model->name);
                $this->redirect(array('admin'));
            }else{
            	@SystemLog::record(Yii::app()->user->name."添加广告位：{$model->name} 失败");
            }
        }
        $this->render('_form', array(
            'model' => $model,
        ));
    }

    /**
     * 更新
     * @param type $id
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $this->performAjaxValidation($model);
        if (isset($_POST['AppAdvert'])) {
            $model->attributes = $_POST['AppAdvert'];
            if ($model->save()){
            	@SystemLog::record(Yii::app()->user->name."更新广告位：{$model->name}");
            	$this->redirect(array('admin', 'id' => $model->id));
            }else{
            	@SystemLog::record(Yii::app()->user->name."更新广告位：{$model->name} 失败");
            }
                
        }

        $this->render('_form', array(
            'model' => $model,
        ));
    }

    /**
     * 删除
     * @param type $id
     */
    public function actionDelete($id) {
        $this->loadModel($id)->delete();
        @SystemLog::record(Yii::app()->user->name."删除广告位：{$id}");
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
            AppAdvert::model()->deleteAll($criteria);
            @SystemLog::record(Yii::app()->user->name."批量删除广告位：".implode(',', $_POST['selectdel']));
            if (!empty($_POST['selectdel']))
                foreach ($_POST['selectdel'] as $val) {
                    $picData = AppAdvertPicture::model()->findAll('advert_id=:advert_id', array(':advert_id' => $val));
                    if ($picData)
                        foreach ($picData as $v) {
                            $v->delete();
                        }
                }
            if ($this->isAjax()) {
                echo CJSON::encode(array('success' => true));
            } else {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }
        } else {
            throw new CHttpException(400, Yii::t('advertPicture', '无效的请求'));
        }
    }

    /**
     * 生成所有广告缓存
     */
    public function actionGenerateAllAppAdvertCache() {
        Tool::cache(AppAdvert::CACHEDIR)->flush();  // 清除所有广告缓存
        AppAdvert::generateAllAppAdvertCache();
        @SystemLog::record(Yii::app()->user->name."生成所有广告缓存：");
        $this->setFlash('success', Yii::t('advert', '成功生成所有广告缓存文件'));
        $this->redirect(array('admin'));
    }

}
