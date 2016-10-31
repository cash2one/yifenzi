<?php

/**
 * 个人认证后台页面
 */
class MemberPersonalAuthenticationController extends MController {

    public function filters() {
        return array(
            'rights',
        );
    }

    /**
     * 个人认证列表页
     */
    public function actionAdmin() {
        $model = new MemberPersonalAuthentication('search');
        $model->unsetAttributes();
        if (isset($_GET['MemberPersonalAuthentication']))
            $model->attributes = $_GET['MemberPersonalAuthentication'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * 审核个人认证
     */
    public function actionApply($id) {
        $model = $this->loadModel($id);
        $this->performAjaxValidation($model);

        if ($this->getParam('apply') == 'pass') {
            if($model->status == MemberPersonalAuthentication::STATUS_PASS){
                $this->setFlash('error', Yii::t('site', '已审核过的，请勿重复审核！'));
                $this->redirect(array('/memberPersonalAuthentication/admin'));
            }
            $model->status = MemberPersonalAuthentication::STATUS_PASS;

            if ($model->save()) {
                $dis_model = $this->actionRegisterDistribution($model);
                if (count($dis_model->errors) > 0) {
                    $model->status = MemberPersonalAuthentication::STATUS_NOT_PASS;
                    $model->save();
                    foreach ($dis_model->errors as $v) {
                        $dis_errors .= implode(',', $v);
                    }
                    $this->setFlash('error', Yii::t('site', $dis_errors));
                    $this->redirect(array('/memberPersonalAuthentication/admin'));
                } else {
                    @SystemLog::record(Yii::app()->user->name . "审核个人认证通过：" . $model->real_name);
                    $this->setFlash('success', Yii::t('site', '审核个人认证通过：') . $model->real_name . Yii::t('site', '成功'));
                    $this->redirect(array('/memberPersonalAuthentication/admin'));
                }
            } else {
                foreach ($model->errors as $v) {
                    $errors .= implode(',', $v);
                }
                $this->setFlash('error', Yii::t('site', $errors));
                $this->redirect(array('/memberPersonalAuthentication/admin'));
            }
        }

        if ($this->getParam('apply') == 'unpass') {
            $model->status = MemberPersonalAuthentication::STATUS_NOT_PASS;
            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name . "审核个人认证不通过：" . $model->real_name);
                $this->setFlash('success', Yii::t('site', '审核个人认证不通过：') . $model->real_name . Yii::t('site', ' 成功'));
                $this->redirect(array('/memberPersonalAuthentication/admin'));
            }
        }

        $this->render('apply', array(
            'model' => $model,
        ));
    }

    /**
     * 生成人人配送员
     */
    public function actionRegisterDistribution($model) {
//        var_dump($model);
        $distribution_model = new Distribution();
        $distribution_model->attributes = $model->attributes;
        $distribution_model->name = $model->real_name;
//        $distribution_model->deposit= 0;
        $distribution_model->deposit_status = Distribution::DEPOSIT_NO;
        $distribution_model->create_time = time();
        $distribution_model->service_count = 0;
        $distribution_model->member_personal_id = $model->id;
//        var_dump($distribution_model);die;
        $distribution_model->save();
        return $distribution_model;
    }

    /*
     * 人人配送列表
     */

    public function actionRenAdmin() {
        $model = new Distribution('search');
        $model->unsetAttributes();
        if (isset($_GET['Distribution']))
            $model->attributes = $_GET['Distribution'];
        $this->render('renAdmin', array('model' => $model));
    }

    /*
     * 人人配送收查询入页
     */

    public function actionCheck($id) {
        $distribution_model = Distribution::model()->findByPk($id);
//        var_dump($distribution_model);die;
        $model = new DistributionOrder();

        $this->render('check', array('model' => $model, 'd_model' => $distribution_model));
    }

    /**
     * 修改状态
     */
    public function actionResetStatus($id) {
        if ($this->isAjax() && $this->isPost()) {
            $model = Distribution::model()->findByPk($id);
            $status = $_POST['name'];

            if ($status == 'close') {
                $model->status = Distribution::STATUS_CLOSE;
            } else {
                $model->status = Distribution::STATUS_OPEN;
            }
            if ($model->save()) {
                echo '设置成功！';
                exit;
            }
        } else {
            $model = Distribution::model()->findByPk($id);
            if ($model->status == Distribution::STATUS_CLOSE) {
                $model->status = Distribution::STATUS_OPEN;
            } else {
                $model->status = Distribution::STATUS_CLOSE;
            }
            if ($model->save()) {
                $this->setFlash('success', Yii::t('site', '设置成功！'));
                $this->redirect(array('/memberPersonalAuthentication/renAdmin'));
            }
        }
    }

}
