<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo $this->pageTitle?></title>
	<meta name="description" content="<?php echo $this->description?>">
	<meta name="keywords" content="<?php echo $this->keywords?>">
	<!-- 微信测试用清理缓存  -->
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />

	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="email=no">
	<link rel="stylesheet" type="text/css" href="/yifenzi3/css/common.css">
	<script src="/yifenzi3/js/zepto.min.js" type="text/javascript" charset="utf-8"></script>
	<!-- <script src="http://zeptojs.com/zepto.min.js" type="text/javascript" charset="utf-8"></script> -->
</head>
<body>
<?php echo $content; ?>
<?php echo $this->renderPartial('/layouts/_footer')?>
</body>
</html>