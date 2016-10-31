<?php
/**
 * 公用的控制器类
 * @author lc
 *
 */
class YfzController extends BaseController{
	public $layout = 'main';

	// meta
	public $keywords = '一份子';
	public $description = '一份子';
	public $pageTitle = '一份子';
	public $CACHE_PATH = "yfzcache";

	//footer 12345五种配置
	public $footerPage = 1;

	/**
	 * 是否显示页面底部的导航
	 * true 显示
	 * false 不显示
	 * @author chen.luo
	 * @since 2016年4月28日下午5:54:38
	 */
	public $footerDisplay = true;

	/**
	 * 上一级的页面
	 */
	public $parentPage;


	public function beforeAction($action) {
        $this->getWeixingOpenId();
		return parent::beforeAction($action);
	}


    /*
     * 获取微信openid
     * */
    public function getWeixingOpenId(){
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $openid = Yii::app()->user->getState(WeixinMember::MEMBER_OPENID);
            if (strpos($user_agent, 'MicroMessenger') && empty($openid)) {
                Yii::import('comext.WxpayAPI_php_v3.lib.*', 1);
                Yii::import('comext.WxpayAPI_php_v3.cert.*', 1);
                require_once "WxPay.Api.php";
                require_once "WxPay.JsApiPay.php";

                //①、获取用户openid
                $tools = new JsApiPay();
                $openId = $tools->GetOpen2id();
                Yii::app()->user->setState(WeixinMember::MEMBER_OPENID,$openId);
            }
        //Yii::app()->user->setState(WeixinMember::MEMBER_OPENID,"oesGhwkATp10LsSkzBMWlQ_i2ywo");
    }

	public function getOpenID(){
		//获取用户oppenid
		Yii::import('comext.WxpayAPI_php_v3.lib.*',1);
		Yii::import('comext.WxpayAPI_php_v3.cert.*',1);
		require_once "WxPay.Api.php";
		require_once "WxPay.JsApiPay.php";

		$member_id = Yii::app()->user->id;
		//①、获取用户openid
		$tools = new JsApiPay();
		$openId = $tools->GetOpen2id();

		if (isset($openId)){
			if (isset($member_id)){
				$model = new WeixinMember();
				$data = $model->find(array("condition"=>"member_id={$member_id}","select"=>array("id")));
				if (!$data->id){
					$insetData = array(
						"member_id" =>  $member_id,
						"openid"    =>  $openId,
					);
					Yii::app()->db->createCommand()->insert("gw_yifenzi_weixin_member", $insetData);
				}
				$this->setSession('openID', $openId, 36000); //过期时间
			}

		}
	}
	/**
	 * 检测当前是否是登录状态
	 * 未登陆状态跳转到登陆页面
	 */
	public function checkLogin()
	{
		if(!Yii::app()->user->checkLogin())
		    $this->redirect('/member/login');
	}

	/**
	 * @return CFileCache
	 */
	public function Caches(){
		return Tool::cache("yifenzi3/cache");
	}
}