<?php
/**
 * 前台配置文件
 * @author leo8705
 */
// 定义当前系统的路径分隔符常量
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// 当前配置文件物理路径
$ConfigDir = dirname(__FILE__);
defined('ConfigDir') or define('ConfigDir', $ConfigDir);

defined('testMode') or define('testMode', true);

// 密钥路径别名设置
$configDir = dirname(__FILE__);
Yii::setPathOfAlias('keyPath', $configDir . DS . 'key');

// 当前应用根目录物理路径的路径别名设置
$root = $ConfigDir . DS . '..' . DS . '..';
Yii::setPathOfAlias('root', $root);

// 公共扩展
Yii::setPathOfAlias('comext', $ConfigDir . DS . '..' . DS . 'extensions');

// 公共扩展
Yii::setPathOfAlias('widgets', $ConfigDir . DS . '..' . DS . 'widgets');

// 上传商品图片目录
Yii::setPathOfAlias('uploads', UPLOAD_REMOTE ? UPLOAD_REMOTE . 'uploads' : $root . DS . 'source' . DS . 'uploads');

// 上传附件目录
Yii::setPathOfAlias('att', UPLOAD_REMOTE ? UPLOAD_REMOTE . 'attachments' : $root . DS . 'source' . DS . 'attachments');

// 缓存根目录别名
Yii::setPathOfAlias('cache', $root . DS . 'source' . DS . 'cache');

// 前台配置
$config = array(
    'preload' => array('log'), // 预载入 log 组件
    'language' => 'zh_cn', // 应用语言
    'charset' => 'utf-8', // 页面字符集
    'timezone' => 'Asia/Shanghai', // 时区
    'basePath' => dirname(__FILE__) . DS . '..',
    'name' => '小微企业联盟',
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.vendor.ZhConverter.ZhTranslate',
    ),
    'modules' => array(
        'api', 'super', 'manage', 'partner', 'openapi','orderapi','game','input','yifenzi','yifenzi2','yifenzi3',
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => false ,
            'generatorPaths' => array(
                'application.gii', //自定义的模板路径
            ),
        ),
    ),
    'components' =>
        $_SERVER['SERVER_NAME'] == substr(DOMAIN_M, 7) ? require(dirname(__FILE__) . DS . 'components_m.php') : (
        ($_SERVER['SERVER_NAME'] == substr(DOMAIN_YIFENZI, 7)) ? require(dirname(__FILE__) . DS . 'components_yfz.php') :(($_SERVER['SERVER_NAME'] == substr(DOMAIN_YIFENZI2, 7)) ? require(dirname(__FILE__) . DS . 'components_yfz2.php') : (($_SERVER['SERVER_NAME'] == substr(DOMAIN_YIFENZI3, 7)) ? require(dirname(__FILE__) . DS . 'components_yfz3.php') : require(dirname(__FILE__) . DS . 'components.php')))),
    'params' => $_SERVER['SERVER_NAME'] == substr(DOMAIN_M, 7) ? require(dirname(__FILE__) . DS . 'params_m.php') : require(dirname(__FILE__) . DS . 'params.php'),
);
$db = require(dirname(__FILE__) . DS . 'db.php');
$config['components'] = CMap::mergeArray($config['components'], $db);

$ftp = require(dirname(__FILE__) . DS . 'ftp.php');
$config['components'] = CMap::mergeArray($config['components'], $ftp);
//echo "<pre>";var_dump($config['components']);exit;
return $config;

