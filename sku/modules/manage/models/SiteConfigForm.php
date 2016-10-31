<?php

/**
 * 网站设置模型类
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class SiteConfigForm extends CFormModel {

    public $domain;
    public $name;
    public $weibo;
    public $phone;
    public $service_time;
    public $qq;
    public $description;
    public $copyright;
    public $icp;
    public $iconScript;
    public $statisticsScript;
    public $notice;
    public $automaticallySignTimeOrders;
    public $extendMaximumNum;
    public $ordersActivistTime;
    public $duration;
	public $category_depth;
	
	
	public $api_distance;			
	public $kefu_mobile;
	

    public function rules() {
        return array(
            array('domain, name, api_distance', 'required'),
            array('domain, weibo', 'url'),
//            array('redDeadTime','numerical'),
            array('phone, qq, description, copyright, icp, iconScript, statisticsScript, notice, duration,service_time,category_depth,api_distance,kefu_mobile', 'safe')
        );
    }

    public function attributeLabels() {
        return array(
            'domain' => Yii::t('home','网站地址'),
            'name' => Yii::t('home','网站名称'),
            'weibo' => Yii::t('home','官方微博'),
            'phone' => Yii::t('home','客服电话'),
            'service_time' => Yii::t('home','客服时间'),
            'qq' => Yii::t('home','在线客服QQ'),
            'description' => Yii::t('home','在线客服描述'),
            'copyright' => Yii::t('home','版权信息'),
            'icp' => Yii::t('home','网站ICP备案'),
            'iconScript' => Yii::t('home','360图标脚本'),
            'statisticsScript' => Yii::t('home','统计脚本'),
            'notice' => Yii::t('home','网站公告'),
            'automaticallySignTimeOrders' => Yii::t('home','订单自动签收时间'),
            'extendMaximumNum' => Yii::t('home','买家延长签收时间最大次数'),
            'ordersActivistTime' => Yii::t('home','订单维权时间'),
            'duration' => Yii::t('home','自动登录'),
			'category_depth' => Yii::t('home','商品分类最大层级'),
        		
        		
         	'api_distance' => Yii::t('home','盖付通api店铺搜索范围'),
        	'kefu_mobile' => Yii::t('home','客服电话'),
        );
    }

}
