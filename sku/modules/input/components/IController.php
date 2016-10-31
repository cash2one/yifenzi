<?php

/**
 * 合作商模块控制器父类
 * @author leo8705
 */
class IController extends Controller {

    public $layout = 'main';
    public $menu = array();
    public $breadcrumbs = array();
    public $curr_menu_name;   //当前菜单名

    public function beforeAction($action) {
        parent::beforeAction($action);
//        var_dump($this);
        //判断登录
        if (!Yii::app()->user->checkLogin()) {
            $this->redirect('/home/login');
        }
        return true;
    }

}
