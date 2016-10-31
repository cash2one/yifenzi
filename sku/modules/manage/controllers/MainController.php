<?php

/**
 * 后台默认控制器
 * 管理主导航栏
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class MainController extends MController {

    public $layout = false;

    public function filters() {
        return array(
            'rights',
        );
    }

    public function allowedActions() {
        return 'index, userInfo,modifyPassword';
    }

    /**
     * 默认
     */
    public function actionIndex() {
        $menus = $this->getMenu('administrators');
        $this->render('index', array('menus' => $menus));
    }

    /**
     * 用户信息
     */
    public function actionUserInfo() {
        $menus = $this->getMenu('userInfo');
        $this->render('index', array('menus' => $menus));
    }

    /**
     * 管理员管理
     */
    public function actionAdministrators() {
        $menus = $this->getMenu('administrators');
        $this->render('index', array('menus' => $menus));
    }

    /**
     * 商户管理
     */
    public function actionPartners() {
    	$menus = $this->getMenu('partners');
    	$this->render('index', array('menus' => $menus));
    }
    
    /**
     * 商品管理
     */
    public function actionGoods() {
    	$menus = $this->getMenu('goods');
    	$this->render('index', array('menus' => $menus));
    }
    
    /**
     * 网站配置管理
     */
    public function actionWebConfig(){
               $menus = $this->getMenu('webConfig');
               $this->render('index',array('menus'=>$menus));
    }
    
    /*
     * 广告管理
     */
    public function actionAppAdvert(){
               $menus = $this->getMenu('appAdvert');
               $this->render('index',array('menus'=>$menus));
    }
    
     public function actionAppAdvertPicture(){
               $menus = $this->getMenu('appAdvertPicture');
               $this->render('index',array('menus'=>$menus));
    }

    /**
     * 网站数据管理
     */
    public function actionWebData(){
        $menus = $this->getMenu('webData');
        $this->render('index',array('menus'=>$menus));
    }
    /**
     * 网站数据管理
     */
    public function actionQuestResult(){
        $menus = $this->getMenu('questResult');
        $this->render('index',array('menus'=>$menus));
    }

    /**
     * 充值兑现管理
     */
    public function actionRechargeCashManagement() {
        $menus = $this->getMenu('rechargeCashManagement');
        $this->render('index', array('menus' => $menus));
    }

    /**
     * 游戏配置管理
     */
    public function actionGameConfig() {
        $menus = $this->getMenu('gameConfig');
        $this->render('index', array('menus' => $menus));
    }
    
    /**
     * 挂单管理
     */
    public function actionGuadan() {
    	$menus = $this->getMenu('guadan');
    	$this->render('index', array('menus' => $menus));
    }
    
    /**
     * 交易管理
     */
    public function actionTradeManagement() {
    	$meuns = $this->getMenu('tradeManagement');
    	$this->render('index', array('menus' => $meuns));
    } 
	
    /**
     * 一份子栏目
     */
    public function actionOnepartManagement() {
        $meuns = $this->getMenu('onepartManagement');
        $this->render('index', array('menus' => $meuns));
    }
    
    
}
