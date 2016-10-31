<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo CHtml::encode($this->pageTitle) ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo CSS_DOMAIN; ?>style_input1.css" />
        <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
        <?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery.form.js");?>
    </head>
    <body>	
        <div class="wrapper">
            <div class="web-header">
                <div class="title fl">商品录入系统</div>              
                <div class="login-data fr"><?php echo CHtml::link(Yii::t('inputModule.page', '退出登录'), array('/input/home/logout')); ?></div>
                  <div class="login-data fr"><?php echo Yii::t('inputModule.page', '欢迎您，'); ?>
                   <?php echo Yii::app()->user->getState('gai_number'); ?>             
                </div>
            </div>
            <div class="web-main">
                <div class="web-menu fl">
                    <ul>
                        <li  id="goods" <?php if($this->action->id =='inputGoods') echo 'class="on"'?>><a href="<?php echo Yii::app()->createUrl('input/member/inputGoods'); ?>">商品录入</a></li>
                        <li  id="records" <?php if($this->action->id =='inputRecords') echo 'class="on"'?>><a href="<?php echo Yii::app()->createUrl('input/member/inputRecords'); ?>">录入记录</a></li>
                        <li></li>
                    </ul>
                </div>
                 <?php $this->renderPartial('/layouts/_msg'); ?>
                <?php echo $content ?>
            </div>
        </div>
    </body>
</html>
<script>
     $('#goods').on('click', function () {
         $(this).attr({'class':'on'});
        $('#records').attr({'class':''});
     });
     $('#records').on('click', function () {
         $(this).attr({'class':'on'});
         $('#goods').attr({'class':''});
     });
    </script>