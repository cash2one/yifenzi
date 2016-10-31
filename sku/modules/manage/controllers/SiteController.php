<?php

/**
 * SKU项目后台管理登陆,退出控制器
 * User: Administrator
 * Date: 2015/5/25
 * Time: 14:29
 * @author binbin.liao
 */
class SiteController extends MController
{

    public function actions()
    {
        return array(
            'captcha' => array(
                'class' => 'CaptchaAction',
                'backColor' => 0xFFFFFF,
                'foreColor' => 0x2040A0,
                'height' => '35',
                'minLength' => 4,
                'maxLength' => 4,
                'offset' => 3,
            ),
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * 后台管理日志
     * @author binbin.liao
     */
    public function actionIndex(){
        $dataProvider = new CActiveDataProvider('SystemLog', array(
            'criteria' => array(
                'order' => 'create_time DESC',
            ),
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
        $this->render('index',array('dataProvider' => $dataProvider,));
    }


    public function actionLogin()
    {
        $this->layout = false;
//        var_dump($this->actions());
      
        $model = new LoginForm;
 
//         if (!Yii::app()->user->isGuest)
//             $this->redirect(Yii::app()->homeUrl);

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            if ($model->validate() && $model->login()) {
                //$this->redirect('/index/userInfo');
            }
//            else{
////                print_r($model->getErrors());
//            }
        }
        $this->render('login', array('model' => $model));
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $url =  Yii::app()->createAbsoluteUrl('/manage/site/login');
        $this->redirect($url);
    }

}