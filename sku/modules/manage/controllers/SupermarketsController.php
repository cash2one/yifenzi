<?php

/*
 * 门店管理
 * @author zehui.hong
 */

class SupermarketsController extends MController {

    /**
     * 审核门店
     */
    public function actionApply($id) {
        $model = $this->loadModel($id);       
        if ($this->getParam('apply') == 'pass') {
            $model->status = Supermarkets::STATUS_ENABLE;
            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name . "审核门店通过：" . $model->name);
                $this->setFlash('success', Yii::t('supermarkets', '审核门店通过：') . $model->name);
                $this->redirect(array('/supermarkets/admin'));
            }
        } 
        if ($this->getParam('apply') == 'unpass') {
            $model->status = Supermarkets::STATUS_DISABLE;
//            var_dump($model->attributes);die;
            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name . "审核门店不通过：" . $model->name);
                $this->setFlash('success', Yii::t('supermarkets', '审核门店不通过：') . $model->name);
                $this->redirect(array('/supermarkets/admin'));
            }
        }

        $this->render('apply', array(
            'model' => $model,
        ));
    }

    /*
     * 门店列表页
     */

    public function actionAdmin() {
        $model = new Supermarkets('search');
        $model->unsetAttributes();
        if (isset($_GET['Supermarkets']))
            $model->attributes = $_GET['Supermarkets'];
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * 门店详细页面
     * @param type $id
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $referrals = Member::model()->findByPk($model->referrals_id);
        $model->referrals_gai_number =$referrals['gai_number'];
        $model->scenario = 'update';
        $this->performAjaxValidation($model);
        if (isset($_POST['Supermarkets'])) {     
            $post = $_POST['Supermarkets'];
            $model->attributes = $post;
            $referrals_id = Member::getByGwNumber($post['referrals_gai_number']);
            $model->referrals_id = count($referrals_id) == 0 ? '' : $referrals_id['id'];
            $model->logo = $_POST['Supermarkets']['logo'];      
            if ($model->save()) {
                $this->setFlash('success', Yii::t('supremarkets', '编辑成功'));
                $this->redirect(array('admin'));
            }
        }
        $this->render('update', array(
            'model' => $model,
        ));
    }

}
