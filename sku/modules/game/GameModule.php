<?php
/**
 * 游戏模块
 * @author xiaoyan.luo
 */
class GameModule extends CWebModule {

    //返回状态码
    const RESULT_CODE_0 = 0;    //当前请求失败
    const RESULT_CODE_1 = 1;	//当前请求成功
    const RESULT_CODE_2 = 2;	//当前请求加密数据解密失败（前端密钥不正确）
    const RESULT_CODE_3 = 3;	//积分不足
    const RESULT_CODE_4 = 4;	//账号或密码错误
    const RESULT_CODE_5 = 5;	//多次账号密码错误
    const RESULT_CODE_6 = 6;	//登录过期

    public $defaultController = 'index';

    public function init() {
        $this->setImport(array(
            'game.components.*',
            'game.models.*',
            'manage.models.GameStoreMember',
            'manage.models.GameStore',
            'manage.models.GameStoreItems',
            'manage.models.GameStoreDelivery',
        ));
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            return true;
        } else
            return false;
    }
}
