<?php

/**
 * 前台控制器父类
 * @author leo8705
 */
class Controller extends BaseController {

    public $layout = 'main';
    public $menu = array();
    public $breadcrumbs = array();
    // meta
    public $keywords;
    public $description;
    public $title;
    public $globalkeywords;  //全局搜索变量

    public function beforeAction($action) {
//         $globalkeyword = $this->getConfig('globalkeyword');
//         $this->globalkeywords = explode('|', $globalkeyword['hotSearchKeyword']);

        return parent::beforeAction($action);
    }
    

    /**
     * 跳转操作
     * request 参数
     * turnback = 1 || turnbackUrl = 1时执行跳转
     * croute 为urlencode后的路由
     * Enter description here ...
     */
    public function turnback() {
    	if (!empty($_REQUEST['turnback'])) {
    		$this->redirect($this->createUrl(urldecode($_REQUEST['croute'])));
    	}
    
    	//根据url跳转
    	if (!empty($_REQUEST['turnbackByUrl'])) {
    		$this->redirect(urldecode($_REQUEST['turnbackUrl']));
    	}
    }
    
    /**
     * 设置回跳 跳转
     * Enter description here ...
     * @param unknown_type $to_route  跳转目标 路由
     * @param unknown_type $back_route  回跳目标路由
     *
     * 设置后需在目标操作中添加turnback() 方法。
     *
     */
    public function turnbackRedirect($to_route, $back_route = '') {
    	if (empty($back_route))
    		$back_route = '/' . $this->getRoute();
    	$this->redirect($this->createUrl($to_route, array('turnback' => 1, 'croute' => urlencode($back_route))));
    }
    
    

	/**
	 * 模拟post
	 * @param unknown $url
	 * @param unknown $post_data
	 */
    public static function post($url,$post_data){
    	return Tool::post($url, $post_data);
    }

}