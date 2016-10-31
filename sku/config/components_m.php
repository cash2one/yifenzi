<?php
/**
 * 管理后台配置
 */

return array(
    'fileCache' => array(
                   'class' => 'CFileCache',
                   'cachePath' => $root . DS . '..' . DS . 'source' . DS . 'cache',
                   'keyPrefix' => 'sku_',
//         'class' => 'system.caching.CMemCache',
//         'servers' => array(
//             array(
//                 'host' => '172.16.8.206',
//                 'port' => 58728,
            	
//             ),
//         ),
//     		'keyPrefix' => 'sku_'
    ),

    'session' => array(
        'class' => 'CCacheHttpSession',
        'autoStart' => true,
        'cacheID' => 'sessionCache', //  sessionCache  or  cache
// 				'cookieMode' => 'allow',
// 				'cookieParams' => array(
// 						'domain' => SC_DOMAIN_M,
// 						'lifetime' => 0
// 				),
        'timeout' => 3600
    ),

    'sessionCache' => array(
        'class' => 'system.caching.CMemCache',
        'servers' => array(
            array(
                'host' => '172.16.8.206',
                'port' => 58728,
            ),
        ),

    ),

    //数据格式化
    'format' => array(
        'class' => 'Formatter',
        'dateFormat' => 'Y-m-d',
        'timeFormat' => 'H:i:s',
        'datetimeFormat' => 'Y-m-d H:i:s',
        'booleanFormat' => array('否', '是'),
    ),

    'log' => array(
        'class' => 'CLogRouter',
        'routes' => array(
            array(// Yii调试扩展工具
                'class' => 'ext.yii-debug-toolbar.YiiDebugToolbarRoute', // 调试工具路径
                'ipFilters' => array('127.0.0.1', '*'),
                'skipController' => array('thumb', 'cart', 'ueditor', 'design', 'upload'), //跳过，不显示调试工具的控制器
                'enabled' => YII_DEBUG,
            ),
            array(// 开启将错误信息记录数据库
                'class' => 'CDbLogRoute',
                'levels' => 'error, warning, info',
                'logTableName' => 'applog',
                'connectionID' => 'db'
            ),
        ),
    ),

    //路由类
    'urlManager' => require(dirname(__FILE__) . DS . 'urlManager_m.php'),
    'errorHandler' => array(
        'errorAction' => 'site/error',
    ),

    //用户类
    'user' => array(
        'class' => 'RWebUser',
        'allowAutoLogin' => true,
        'authTimeout' => 3600, //用户登陆后处于非活动状态的超时时间（秒）
        'stateKeyPrefix' => 'gaiwangsku_manage_',
//         		'identityCookie' => array(
//         				'domain' => SC_DOMAIN_M,
//         				'path' => '/m/'
//         		),
    ),

    'authManager' => array(
        'class' => 'RDbAuthManager',
        'connectionID' => 'db',
        'itemTable' => 'gw_sku_auth_item',
        'itemChildTable' => 'gw_sku_auth_item_child',
        'assignmentTable' => 'gw_sku_auth_assignment',
        'rightsTable' => 'gw_sku_rights',
    ),
    'redis' => array(
        "class" => "application.extensions.YiiRedis.ARedisConnection",
        "hostname" => "127.0.0.1",
        "port" => 6379
    ),

);