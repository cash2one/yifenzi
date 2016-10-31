<?php
/**
 * 游戏店铺设置控制器
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/11/18 15:36
 */
class GameStoreController extends MController {
    public function filters() {
        return array(
            'rights',
        );
    }

    /**
     * 游戏店铺列表
     */
    public function actionAdmin() {
        $model = new GameStore('search');
        $model->unsetAttributes();
        if (isset($_GET['GameStore']))
            $model->attributes = $this->getParam('GameStore');
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * 添加店铺
     */
    public function actionCreate() {
        $model = new GameStore();
        $this->performAjaxValidation($model);
        if (isset($_POST['GameStore'])) {
            $_POST['GameStore']['create_time'] = time();
            $_POST['GameStore']['update_time'] = time();
            $model->attributes = $_POST['GameStore'];

            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name."添加店铺：{$model->store_name}");
                $this->setFlash('success', Yii::t('GameStore', '添加店铺成功：') . $model->store_name);
                $this->redirect(array('admin'));
            }else{
                $errors = $model->getErrors();
                $errStr = '';
                if(!empty($errors)){
                    foreach ($errors as $key => $value){
                        $errStr .= '\r\n'.$key.':';
                        if(is_array($value))foreach ($value as $val){
                            $errStr .= ' '.$val;
                        }
                    }
                }
                @SystemLog::record(Yii::app()->user->name."添加店铺：{$model->store_name} 失败");
                $this->setFlash('success', Yii::t('GameStore', '添加店铺失败：') . $model->store_name.$errStr);
                $this->redirect(array('admin'));
            }
        }
        $this->render('_form', array(
            'model' => $model,
        ));
    }

    /**
     * 修改店铺
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $this->performAjaxValidation($model);
        if (isset($_POST['GameStore'])) {
            $_POST['GameStore']['update_time'] = time();
            $model->attributes = $_POST['GameStore'];

            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name."修改店铺：{$model->store_name}");
                $this->setFlash('success', Yii::t('GameStore', '修改店铺成功：') . $model->store_name);
                $this->redirect(array('admin'));
            }else{
                $errors = $model->getErrors();
                $errStr = '';
                if(!empty($errors)){
                    foreach ($errors as $key => $value){
                        $errStr .= '\r\n'.$key.':';
                        if(is_array($value))foreach ($value as $val){
                            $errStr .= ' '.$val;
                        }
                    }
                }
                @SystemLog::record(Yii::app()->user->name."修改店铺：{$model->store_name} 失败");
                $this->setFlash('success', Yii::t('GameStore', '修改店铺失败：') . $model->store_name.$errStr);
                $this->redirect(array('admin'));
            }
        }
        $this->render('_form', array(
            'model' => $model,
        ));
    }
}