<?php

/**
 * 网站首页控制器
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class SiteController extends Controller {

    public $layout = 'home';
    public $author;

    public function actions() {
        return array(
            'selectLanguage' => array('class' => 'CommonAction', 'method' => 'selectLanguage'),
        );
    }

    public function beforeAction($action) {
//        $seo = $this->getConfig('seo');
//        $this->author = $seo['author'];
//        $this->title = $seo['title'];
//        $this->keywords = $seo['keyword'];
//        $this->description = $seo['description'];
        return parent::beforeAction($action);
    }

    public function actionIndex() {
         $this->render('index');
    }

    public function actionError() {
        $this->layout = 'main';
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else{
//                $this->render('error', $error);
                echo json_encode($error);exit;
            }
        }
    }


    public function actionTest(){
    	$this->setSession('testKey','testValue1');
    	echo $this->getSession('testKey');
    }


}
