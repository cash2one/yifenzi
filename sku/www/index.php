<?php
//  @session_start();
// 网站前台入口文件
include(dirname(__FILE__) . '/../config/constant.php'); 
include(dirname(__FILE__) . '/../../framework/yii.php');
$app = Yii::createWebApplication(include(dirname(__FILE__) . '/../config/main.php'));
defined('CSS_DOMAIN') or define('CSS_DOMAIN', DOMAIN.'/css/encss/');
@session_start();
$app->run();
