<?php
/**
 * 管理后台配置
 */
ini_set("session.cookie_domain",SC_DOMAIN_YFZ3);
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
        //'class' => 'CCacheHttpSession',
        'autoStart' => true,
        //'cacheID' => 'sessionCache', //  sessionCache  or  cache
        'cookieMode' => 'allow',
        'cookieParams' => array(
            'domain' => SC_DOMAIN_YFZ3,
            'lifetime' => 0
        ),
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
                'skipController' => array('thumb', 'cart', 'ueditor', 'design', 'upload', 'gIndex','sanguorun',
                    'paipaimeng','goldenminer','shentoulili','panzhihua','dafeiji','tantiaogongzhu'), //跳过，不显示调试工具的控制器
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

    'request' => array(
        'enableCsrfValidation' => (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] == substr(DOMAIN_OPENAPI, 7) || $_SERVER['SERVER_NAME'] == substr(DOMAIN_API, 7) || $_SERVER['SERVER_NAME'] == substr(ORDER_API_URL, 7))) ? false : true, // 防止post跨站攻击
        'enableCookieValidation' => true, // 防止Cookie攻击
        'csrfCookie' => array(
            'domain' => SC_DOMAIN_YFZ3,
        ),
        'disableModules' => array('api', 'orderapi', 'game'),
        'class' => 'CustomHttpRequest',

    ),
    
    'statePersister' => array(
        'class' => 'system.base.CStatePersister',
        'stateFile' => $ConfigDir . '/../runtime/state.bin',
    ),

    //用户类
    'user' => array(
        'class' => 'PUser',
        'allowAutoLogin' => true,
        'authTimeout' => 3600, //用户登陆后处于非活动状态的超时时间（秒）
        'stateKeyPrefix' => 'gaiwangsku_',
        'identityCookie' => array(
            'domain' => SC_DOMAIN_YFZ3,
            'path' => '/'
        ),
    ),

    //用户类
    'inputUser' => array(
        'class' => 'IUser',
        'allowAutoLogin' => true,
        'authTimeout' => 3600, //用户登陆后处于非活动状态的超时时间（秒）
        'stateKeyPrefix' => 'gaiwangsku_',
        'identityCookie' => array(
            'domain' => SC_DOMAIN_YFZ3,
            'path' => '/'
        ),
    ),
    // messages组件类默认的是CPhpMessageSource
    'messages' => array(
        //没有找不到繁体翻译的时候，将使用自动转换
        'onMissingTranslation' => array('ZhTranslateEventHandler', 'ZhMissingTranslation'),
    ),
    //路由类
    'urlManager' => require(dirname(__FILE__) . DS . 'urlManager_yfz3.php'),
    'errorHandler' => array(
        'errorAction' => 'site/error',
    ),

    'oauth2Auth' => array(
        'class' => 'application.extensions.oauth2server.OAuth2ServerAuth',
        'identityClass' => 'UserIdentity',
        'loginEndpoint' => 'sociallogin/oauth2/login',
        'authorizeEndpoint' => 'sociallogin/oauth2/authorize',
    ),
    'oauth2Resource' => array(
        'class' => 'application.extensions.oauth2server.OAuth2ServerResource',
    ),
    'redis' => array(
        "class" => "application.extensions.YiiRedis.ARedisConnection",
        "hostname" => "127.0.0.1",
        "port" => 6379
    ),
);