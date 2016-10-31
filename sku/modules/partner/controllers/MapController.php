<?php

/**
 * 地图控制器
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class MapController extends Controller {

    public $defaultAction = 'show';
    // 默认经纬度为广州
    public $lng = '113.3065'; // 经度
    public $lat = '23.121113'; // 纬度

    /**
     * 不作权限控制的action
     * @return string
     */

    public function allowedActions() {
        return 'show';
    }
    
    public function actionShow() {
        $this->layout = false;
        if ((int)$lng = $this->getParam('lng'))
            $this->lng = $lng;
        if ((int)$lat = $this->getParam('lat'))
            $this->lat = $lat;
        $this->render('show');
    }

}
