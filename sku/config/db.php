<?php

/**
 * 项目数据库配置文件
 * @author leo8705
 */
return array(
    'db' => array(
        'class' => 'CDbConnection',
        'connectionString' => 'mysql:host=172.18.7.206;dbname=gaiwangsku',
        'emulatePrepare' => true,
        'username' => 'gateall',
        'password' => '123456',
        'charset' => 'utf8',
        'tablePrefix' => 'gw_sku_',
        'enableProfiling' => true,
    ),
    'gw' => array(
        'class' => 'CDbConnection',
        'connectionString' => 'mysql:host=172.18.7.206;dbname=gaiwang',
        'emulatePrepare' => true,
        'username' => 'gateall',
        'password' => '123456',
        'charset' => 'utf8',
        'tablePrefix' => 'gw_',
    ),
    'ac' => array(
        'class' => 'CDbConnection',
        'connectionString' => 'mysql:host=172.18.7.206;dbname=accountsku',
        'emulatePrepare' => true,
        'username' => 'gateall',
        'password' => '123456',
        'charset' => 'utf8',
        'tablePrefix' => 'gw_sku_',
    ),
    'game' => array(
        'class' => 'CDbConnection',
        'connectionString' => 'mysql:host=172.18.7.206;dbname=game',
        'emulatePrepare' => true,
        'username' => 'game_user',
        'password' => '123456',
        'charset' => 'utf8',
        'tablePrefix' => 'gw_game_',
    ),
    'gt' => array(
        'class' => 'CDbConnection',
        'connectionString' => 'mysql:host=172.18.7.206;dbname=gaitong',
        'emulatePrepare' => true,
        'username' => 'gateall',
        'password' => '123456',
        'charset' => 'utf8',
        'tablePrefix' => 'gt_',
    ),
	'gwpart' => array(
        'class' => 'CDbConnection',
        'connectionString' => 'mysql:host=172.18.7.206;dbname=gaiwangpart',
        'emulatePrepare' => true,
        'username' => 'gateall',
        'password' => '123456',
        'charset' => 'utf8',
        'tablePrefix' => 'gw_yifenzi_',
    ),
);

