<?php

/**
 * 控制台配置文件
 * @author leo8705
 */
// 定义当前系统的路径分隔符常量
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// 当前配置文件物理路径
$consoleConfigDir = dirname(__FILE__);

// 当前应用根目录物理路径的路径别名设置
$root = $consoleConfigDir . DS . '..' ;
Yii::setPathOfAlias('root', $root);

// 公共目录物理路径别名设置
$appl = $root . DS ;
Yii::setPathOfAlias('appl', $appl);

// 控制台日志缓存目录
$consoleLog = $root . DS . '..' . DS . 'source' . DS . 'cache' . DS . 'consolelog';
Yii::setPathOfAlias('consoleLog', $consoleLog);


// 控制台配置
$consoleConfig = array(
    'basePath' => dirname(__FILE__) . DS . '..',
    'import' => array(
        'appl.components.*',
    	'appl.models.*',
    ),
	'components' => require(dirname(__FILE__) . DS . 'components.php'),
);

$db = require(dirname(__FILE__) . DS . 'db.php');
$consoleConfig['components'] = CMap::mergeArray($consoleConfig['components'], $db);
return $consoleConfig;
