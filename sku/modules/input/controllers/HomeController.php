<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HomeController
 *
 * @author Administrator
 */
class HomeController extends Controller {

    public function actions() {
        return array(
            'captcha' => array(
                'class' => 'CaptchaAction',
                'height' => '35',
                'width' => '70',
                'minLength' => 4,
                'maxLength' => 4,
                'offset' => 2,
            ),
        );
    }

    public function actionIndex() {
        if ($this->getSession('id')) {
     
            $this->redirect('/member/inputGoods');
        } else {
            $this->redirect('/member/inputGoods');
        }
    }

    /**
     * 登录
     */
    public function actionLogin() {

        if (Yii::app()->user->id) {
            $this->redirect(Yii::app()->homeUrl);
        }
        $this->layout = false;
        $model = new LoginForm;
        $this->performAjaxValidation($model);
        $users = array();
        $msg = '';
        if (isset($_POST['LoginForm'])) {
            $aMember = new ApiMember();
            $logRs = $aMember->login($_POST['LoginForm']['username'], $_POST['LoginForm']['password']);

            if ($logRs['success'] == true) {

                $memberRs = $logRs['memberInfo'];
                $memberInfo = Member::model()->find('gai_number=:gai_number', array(':gai_number' => $memberRs['gai_number']));
                if ($memberInfo) {


                    Yii::app()->user->Login($memberInfo['id'], $memberInfo);
                    foreach ($memberInfo as $key => $v) {
                        Yii::app()->user->setState($key, $v);
                    }

                    $this->setFlash('success', Yii::t('home', '登录成功'));

                    $this->redirect(Yii::app()->homeUrl);
                } else {
                    $msg = Yii::t('home', '登录失败,获取用户信息失败');
                }
            } else {
                $msg = Yii::t('home', '登录失败,' . isset($logRs['msg']) ? $logRs['msg'] : Yii::t('home', '账号或用户名错误'));
            }
        }
        $this->render('login', array(
            'model' => $model,
            'users' => $users,
            'msg' => $msg,
        ));
    }

    public function actionError() {
        $this->layout = 'seller';
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * 退出登录
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(array('/input/home/login'));
    }

    /**
     * 屏蔽ie6-7
     */
    public function actionNotSupported() {
        $this->layout = false;
        $this->renderPartial('notsupported');
    }

}
