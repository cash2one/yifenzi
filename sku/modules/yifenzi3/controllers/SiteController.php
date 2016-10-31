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

    public function actiontest()
    {
        $yfzcart = new YfzCart();

        $goodsData = Yfzgoods::model()->find(array("condition" => "goods_id=1"));
        $goodsData = $goodsData->attributes;
        $yfzcart->cart->add($goodsData, 1);
        $cookie = new CHttpCookie('mycookie', 'this is my cookie');
        $cookie->expire = time() + 60;  //有限期30天
        Yii::app()->request->cookies['mycookie'] = $cookie;
        print_r($yfzcart->cart->goodsList());
    }


    public function actionError()
    {
    	$this->layout = false;
        if ($error = Yii::app()->errorHandler->error) {
    		if (Yii::app()->request->isAjaxRequest)
    			echo $error['message'];
    		else{
    		    $this->redirect(DOMAIN_YIFENZI3);
     			$this->render('error', $error);
    		}
    	}
    }

}
