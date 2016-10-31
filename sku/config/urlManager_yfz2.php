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
        DOMAIN_YIFENZI2 . '/<_c:\w+>/<_a:\w+>/<id:\d+>' => 'yifenzi2/<_c>/<_a>',
        DOMAIN_YIFENZI2 . '/<_c:\w+>/<_a:\w+>' => 'yifenzi2/<_c>/<_a>',
        DOMAIN_YIFENZI2 . '<_q:.*>/*' => array(
            'yifenzi2<_q>',
            'urlSuffix' => ''
        ),
        '<_c:\w+>/<id:\d+>' => '<_c>/view',
        '<_c:\w+>/<_a:\w+>/<id:\d+>' => '<_c>/<_a>',
        '<_c:\w+>/<_a:\w+>' => '<_c>/<_a>',
    ),
    'baseUrl' => DOMAIN,
);
