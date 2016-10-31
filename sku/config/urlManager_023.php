<?php

/**
 * url重写规则
 * @author leo8705
 */
return array(
    'urlFormat' => 'path',
    'showScriptName' => false,
    'urlSuffix' => '.html',
    'rules' => array(
		DOMAIN_YIFENZI . '/<_c:\w+>/<_a:\w+>/<id:\d+>' => 'yifenzi/<_c>/<_a>',
		DOMAIN_YIFENZI . '/<_c:\w+>/<_a:\w+>' => 'yifenzi/<_c>/<_a>',
		DOMAIN_YIFENZI . '<_q:.*>/*' => array(
			'yifenzi<_q>',
			'urlSuffix' => ''
		),
        'http://<_m:(api|super|partner|openapi|orderapi|game|gii|input|yifenzi)>.' . SHORT_DOMAIN . '/<_c:\w+>/<_a:\w+>/<id:\d+>' => '<_m>/<_c>/<_a>',
        'http://<_m:(api|super|partner|openapi|orderapi|game|gii|input|yifenzi)>.' . SHORT_DOMAIN . '/<_c:\w+>/<_a:\w+>' => '<_m>/<_c>/<_a>',
        'http://<_m:(api|super|partner|openapi|orderapi|game|gii|input|yifenzi)>.' . SHORT_DOMAIN . '<_q:.*>/*' => array(
            '<_m><_q>',
            'urlSuffix' => ''
        ),

    		'http://skuapi.' . SHORT_DOMAIN . '/<_c:\w+>/<_a:\w+>/<id:\d+>' => 'api/<_c>/<_a>',
    		'http://skuapi.' . SHORT_DOMAIN . '/<_c:\w+>/<_a:\w+>' => 'api/<_c>/<_a>',
    		'http://skuapi.' . SHORT_DOMAIN . '<_q:.*>/*' => array(
    				'api<_q>',
           			'urlSuffix' => ''
    		),

    		'http://skupartner.' . SHORT_DOMAIN . '/<_c:\w+>/<_a:\w+>/<id:\d+>' => 'partner/<_c>/<_a>',
    		'http://skupartner.' . SHORT_DOMAIN . '/<_c:\w+>/<_a:\w+>' => 'partner/<_c>/<_a>',
    		'http://skupartner.' . SHORT_DOMAIN . '<_q:.*>/*' => array(
    				'partner<_q>',
            		'urlSuffix' => ''
    		),


        '<_c:\w+>/<id:\d+>' => '<_c>/view',
        '<_c:\w+>/<_a:\w+>/<id:\d+>' => '<_c>/<_a>',
        '<_c:\w+>/<_a:\w+>' => '<_c>/<_a>',
    ),
    'baseUrl' => DOMAIN,
);
