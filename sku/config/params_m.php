<?php

return array(
    'noLogin' => array(
        'site/login',
        'site/logout',
        'site/captcha',
        'site/error',
    ),

    'customCUButton' => array(
        'class' => 'CButtonColumn',
        'header' => Yii::t('home','操作'),
        'template' => '{update}{delete}',
        'updateButtonLabel' => Yii::t('home','编辑'),
        'updateButtonImageUrl' => false,
        'deleteButtonLabel' => Yii::t('home','删除'),
        'deleteButtonImageUrl' => false,
    ),
);
