<?php
/*
 * 域名常量定义
* ***************************************************************************************************************
*/
define('UPLOAD_REMOTE_GT', false); // 盖网通远程图片服务器的图片根目录
define('UPLOAD_REMOTE', false); // 远程图片服务器的图片根目录
define('DOMAIN', 'http://www.gaiwangsku.com');
define('SHORT_DOMAIN', substr(DOMAIN, 11));
define('GAIWANG_DOMAIN', 'http://www.gaiwangsku.com');
define('GAIWANG_SHORT_DOMAIN', substr(GAIWANG_DOMAIN, 11));
define('SUFFIX', substr(SHORT_DOMAIN, strrpos(SHORT_DOMAIN, '.') + 1)); // 域名后缀
define('IMG_DOMAIN', 'http://img.' . (UPLOAD_REMOTE ? 'gwimg.com' : GAIWANG_SHORT_DOMAIN)); // 图片域名
define('ATTR_DOMAIN', 'http://att.' . (UPLOAD_REMOTE ? 'gwimg.com' : GAIWANG_SHORT_DOMAIN)); // 附件域名
define('SC_DOMAIN', '.' . SHORT_DOMAIN); // session cookie 作用域

define('DOMAIN_YIFENZI', 'http://yifenzi.'.SHORT_DOMAIN);	//一份子域名
define('DOMAIN_YIFENZI2', 'http://yifenzi2.'.SHORT_DOMAIN);	//一份子2域名
define('DOMAIN_YIFENZI3', 'http://yifenzi3.'.SHORT_DOMAIN);	//一份子3域名
//define('DOMAIN_YIFENZI', 'http://www.g1fz.com');   //一份子正式环境域名
//define('SC_DOMAIN_YFZ', '.' . substr(DOMAIN_YIFENZI, 11)); // session cookie 一分子作用域
define('SC_DOMAIN_YFZ', SC_DOMAIN); // session cookie 一分子作用域
define('SC_DOMAIN_YFZ2', SC_DOMAIN); // session cookie 一分子作用域
define('SC_DOMAIN_YFZ3', SC_DOMAIN); // session cookie 一分子作用域
define('DOMAIN_API', 'http://api.'.SHORT_DOMAIN);
define('DOMAIN_ORDERAPI', 'http://orderapi.'.SHORT_DOMAIN);
define('DOMAIN_M', 'http://manage.'.SHORT_DOMAIN);
define('DOMAIN_GAME','http://game.'.SHORT_DOMAIN);
define('SHORT_DOMAIN_M', substr(DOMAIN_M, 14));
define('SC_DOMAIN_M', DOMAIN_M); // session cookie 作用域

define('DOMAIN_PARTNER', 'http://partner.'.SHORT_DOMAIN);
define('DOMAIN_OPENAPI', 'http://openapi.'.SHORT_DOMAIN);


define('API_MAIN', 'http://api.gaiwangsku.com/');				//库存接口地址
define('STOCK_API_MAIN', 'http://api.gaiwangsku.com/stock/');				//库存接口地址
define('API_PARTNER_SUPER_MODULES_PROJECT_ID', '104');										//超市模块的api 项目ID
define('API_PARTNER_MODULES_KEY', 'iuew@fjnc#sld!asldiou^sdoif*dd');		//超市模块的api 私钥key

define('API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID', '102');				//售货机模块的库存api 项目ID
define('STOCK_API_VENDING_MACHINE_MODULES_KEY', 'vv22@fjnc#sld!asldiou^sdoif*dd');		//超市模块的库存api 私钥key

define('API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID', '107');				//生鲜机机模块的库存api 项目ID
define('STOCK_API_FRESH_MACHINE_MODULES_KEY', 'vv22@fjnc#sld!asldiou^sdoif*dd');		//生鲜机模块的库存api 私钥key

define('API_MACHINE_CELL_STORE_PROJECT_ID', '108');				//格仔铺的库存的库存api 项目ID
define('STOCK_API_MACHINE_CELL_STORE_KEY', 'b65a4fa02786f5c5j8w891ea8166b24415438t4x');		//格仔铺的库存api 私钥key

define('GAIFUTONG_PROJECT_ID', '105');																	//盖付通后台项目ID
define('GAIFUTONG_PROJECT_KEY', 'vvew@fjnc#sld!333iou^sddcxdd');				//盖付通后台私钥key



define('ORDER_API_URL', 'http://orderapi.'.SHORT_DOMAIN);											//订单接口地址
define('ORDER_API_SIGN_KEY', 'db7a4fa02786f50cf53891ea8166b24415434021');					//订单支付SIGN_KEY常量


define('MEMBER_API_URL', 'http://member.orderapi.com');														//用户中心接口地址
define('MEMBER_API_SIGN_KEY', 'db7a4fa02786f50cf53891ea8166b24415434021');					//用户中心SIGN_KEY常量

define('ORDER_ORDER_API_URL','http://order.orderapi.com');                               //orderApi项目订单接口
define('GAME_API_URL','http://game.'.SHORT_DOMAIN);                                      //游戏接口地址
define('CALLBACK_SIGN_KEY', 'c27a4fa02733222f53891ea8166b24415434021');					//CALLBACK接口SIGN_KEY常量


define('BAIDU_MAP_LOCATION_API_URL', 'http://api.map.baidu.com/geocoder/v2/');								//百度地址ak
define('BAIDU_MAP_API_AK', 'R9QQLiwB8Yb8MDuXpesgUGGI');																	//百度地址ak
define('BAIDU_MAP_API_SECRET_KEY', 'mF0bBdoT4y8EDXajN7WT0YWAbdFt2s4N');									//百度地址SECRET key


define('GAIFUTONG_API_URL', 'http://token.gatewangapi.net');                                        //盖付通后台的接口地址

define('ACCOUNT', 'accountsku'); // 帐目库名
define('YIFENZI', 'gaiwangpart'); // 帐目库名
define('GAME','game');//game数据库常量
define('AMOUNT_SIGN_KEY', 'db4dfs4dfs34dfs4df2786f50cf53891dfs15434021');//余额表密钥
define('GAME_SECRET_KEY',   'fwe^*&3ijcdhf45543');//游戏密钥


define('BANK_API_URL', 'http://www.newgatewang.com/skuauth');												//银行接口地址
define('BANK_API_SIGN_KEY', 'gaiwang_2016sku');															//银行接口SIGN_KEY常量


/*
 * debug 调试等常量
 */
define('YII_TRACE_LEVEL', 9);
define('IS_DEVELOPMENT', true); // 当前是否为开发环境（生产时改为false）
//api项目关闭debug
if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME']==substr(DOMAIN_API, 7) || $_SERVER['SERVER_NAME']==substr(DOMAIN_OPENAPI, 7) || $_SERVER['SERVER_NAME']==substr(ORDER_API_URL, 7))) {
	define('YII_DEBUG', FALSE ); // debug常量（生产时改为false）
}else{
	define('YII_DEBUG', 0); // debug常量（生产时改为false）
}

