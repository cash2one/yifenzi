<?php

/**
 * url重写规则
 * @author leo8705
 */
return array(
    'urlFormat' => 'path',
    'showScriptName' => false,
    'urlSuffix' => '',
    'rules' => array(
//         'http://skumanage.' . SHORT_DOMAIN . '/<_c:\w+>/<_a:\w+>/<id:\d+>' => 'manage/<_c>/<_a>',
//         'http://skumanage.' . SHORT_DOMAIN . '/<_c:\w+>/<_a:\w+>' => 'manage/<_c>/<_a>',
//         'http://skumanage.' . SHORT_DOMAIN . '<_q:.*>/*' => array(
//             'manage/main/',
//             'urlSuffix' => ''
//         ),
    		'http://manage.' . SHORT_DOMAIN . '/<_c:\w+>/<_a:\w+>/<id:\d+>' => 'manage/<_c>/<_a>',
    		'http://manage.' . SHORT_DOMAIN . '/<_c:\w+>/<_a:\w+>' => 'manage/<_c>/<_a>',
    		'http://manage.' . SHORT_DOMAIN . '<_q:.*>/*' => array(
    				'manage/main/',
    				'urlSuffix' => ''
    		),
        '<_c:\w+>/<_a:\w+>/<id:\d+>' => '<_c>/<_a>',
        '<_c:\w+>/<_a:\w+>' => '<_c>/<_a>',
    		
    	'gii'=>'gii',
    	'gii/<controller:\w+>'=>'gii/<controller>',
    	'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
    ),
    'baseUrl' => DOMAIN_M,
);
