<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $this->pageTitle?></title>
    <link href="/js/swf/css/uploadimg.css" rel="stylesheet" type="text/css" />
    <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
</head>
<body>
	<?php echo $content;?>
</body>
</html>
