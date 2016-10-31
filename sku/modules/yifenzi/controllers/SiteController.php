<?php

class SiteController extends YfzController
{
    public function actionIndex()
    {
        if(!Yii::app()->user->checkLogin())
        {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (strpos($user_agent, 'MicroMessenger')) {
                $login = new LoginForm();
                $login->weiXinLogin();
            }
        }
        $this->pageTitle = '首页';
        //读下配置，确定条数
        //广告
//        $advert = AppAdvert::getConventionalAdCache('yifenzi');
        $advertModel = new  Advertising();
		$model = new YfzGoods;
        $datas = $advertModel->getTypesData();
        //广告多余或者少于的处理
        //推荐
        $recommended = YfzGoods::getRecommendedGoods(15);
        //最新揭晓产品
        $announced = YfzOrderGoodsNpers::getAnnounced(4);

        //专区限制
        $sql = "select * from {{column}} where is_zone=1 and zone_thumb <> ''";
        $columnData = Yii::app()->gwpart->createCommand( $sql )->queryAll();
//            var_dump($announced);
        $this->render('index', array(
            'advert' => $datas,
            'recommendcd' => $recommended,
            'announced' => $announced,
            'column'    =>  $columnData,
			'model' => $model,
        ));
    }



    public function actionError()
    {
    	$this->layout = false;
    	if ($error = Yii::app()->errorHandler->error) {
    		if (Yii::app()->request->isAjaxRequest)
    			echo $error['message'];
    		else{
    		    $this->redirect(DOMAIN_YIFENZI);
     			$this->render('error', $error);
    		}
    	}
    }
    public function actionLocation2()
    {
        if(!empty($_GET['code']) && !empty($_GET['state'])) {
            $url = DOMAIN_YIFENZI2 . '/?&code=' . $_GET['code'] . '&state=' . $_GET['state'];
            Header("Location: $url");
            exit();
        }
    }
    public function actionLocation3()
    {
        if(!empty($_GET['code']) && !empty($_GET['state'])) {
            $url = DOMAIN_YIFENZI3 . '/?&code=' . $_GET['code'] . '&state=' . $_GET['state'];
            Header("Location: $url");
            exit();
        }
    }

}
