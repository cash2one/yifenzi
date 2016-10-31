<?php

/**
 * 售货机管理控制器
 *
 * @author zehui.hong
 */
class VendingMachineController extends MController {

    /**
     * 售货机列表
     */
    public function actionAdmin() {
        $model = new VendingMachine('search');
        $model->unsetAttributes();
        if (isset($_GET['VendingMachine'])){
    $model->attributes = $_GET['VendingMachine'];}
        $this->render('admin', array('model' => $model));
    }
//
//    /**
//     * 详情页
//     */
//    public function actionView($id) {
//        $model = $this->loadModel($id);
//        $this->render('view', array('model' => $model, 'order_goods' => $order_goods));
//    }
//
    /**
     * 售货机审核
     */
    public function actionApply($id) {
        $model = $this->loadModel($id);
        if ($this->getParam('apply') == 'pass') {
            $model->status = VendingMachine::STATUS_ENABLE;
            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name . "售货机审核通过：" . $model->name);
                $this->setFlash('success', Yii::t('vendingmachine', '售货机审核通过：') . $model->name);
                $this->redirect(array('/vendingMachine/admin'));
            }
        } 
        if ($this->getParam('apply') == 'unpass') {
            $model->status = VendingMachine::STATUS_DISABLE;
//            var_dump($model->attributes);die;
            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name . "售货机审核不通过：" . $model->name);
                $this->setFlash('success', Yii::t('vendingmachine', '售货机审核不通过：') . $model->name);
                $this->redirect(array('/vendingMachine/admin'));
            }
        }
        $this->render('apply', array('model' => $model));
    }

    /**
     * 编辑售货机信息
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
                $model->scenario = 'update';
        $this->performAjaxValidation($model);
        if (isset($_POST['VendingMachine'])) {
            $model->attributes = $_POST['VendingMachine'];
            $model->update_time = time();
            if ($model->save()) {
                $this->setFlash('success', Yii::t('vendingMachine', '编辑成功'));
                $this->redirect(array('admin'));
            }
        }
        $this->render('update', array('model' => $model));
    }

}
