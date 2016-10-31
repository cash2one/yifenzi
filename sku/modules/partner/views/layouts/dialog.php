<?php
/* @var $this SController */
// 卖家中心对话框布局文件
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo CHtml::encode($this->pageTitle) ?></title>
        <link rel="shortcut icon" href="<?php echo DOMAIN ?>/favicon.ico" type="mage/x-icon">
        <link rel="icon" href="<?php echo DOMAIN ?>/favicon.ico" type="mage/x-icon">
        <link href="<?php echo CSS_DOMAIN; ?>global.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo CSS_DOMAIN; ?>seller.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo CSS_DOMAIN; ?>custom.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo CSS_DOMAIN; ?>custom-seller.css" rel="stylesheet" type="text/css"/>
        <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
        <script type="text/javascript" src="<?php echo DOMAIN ?>/js/artDialog/jquery.artDialog.js?skin=aero"></script>
        <script src="<?php echo DOMAIN ?>/js/artDialog/plugins/iframeTools.source.js" type="text/javascript"></script>
        <script type="text/javascript">
            if (typeof success != 'undefined') {
                art.dialog.opener.location.reload();
                art.dialog.close();
            }
        </script>
    </head>
    <body>
        <?php echo $content; ?>
    </body>
</html>