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
        DOMAIN_YIFENZI3 . '/<_c:\w+>/<_a:\w+>/<id:\d+>' => 'yifenzi3/<_c>/<_a>',
        DOMAIN_YIFENZI3 . '/<_c:\w+>/<_a:\w+>' => 'yifenzi3/<_c>/<_a>',
        DOMAIN_YIFENZI3 . '<_q:.*>/*' => array(
            'yifenzi3<_q>',
            'urlSuffix' => ''
        ),
        '<_c:\w+>/<id:\d+>' => '<_c>/view',
        '<_c:\w+>/<_a:\w+>/<id:\d+>' => '<_c>/<_a>',
        '<_c:\w+>/<_a:\w+>' => '<_c>/<_a>',
    ),
    'baseUrl' => DOMAIN,
);
