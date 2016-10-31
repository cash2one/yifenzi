<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>个人中心</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<!-- 微信测试用清理缓存  -->
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />

	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="email=no">
	<link rel="stylesheet" type="text/css" href="css/common.css">
	<script src="js/zepto.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<style type="text/css">
.unLoginConfirm.active{
	animation:fBottom 0.5s linear 1;
	-webkit-animation:fBottom 0.5s linear 1;
	-webkit-animation-fill-mode:forwards; 
    animation-fill-mode:forwards; 
}
@keyframes fBottom{
	from{
		transform:translateY(0px);
	}
	to{
		transform:translateY(-100px);
	}
}

@-webkit-keyframes fBottom{
	from{
		-webkit-transform:translateY(0px);
	}
	to{
		-webkit-transform:translateY(-100px);
	}
}
.userInfo p{display: inline-block; float: left; height: 50px; padding-top: 10px; line-height: normal;}
</style>
<body>
	<header class="normal">
		<h2>个人中心</h2>
		<a href="javascript:history.go(-1);" class="goback_btn"></a>
	</header>
	<div class="container">
		<p class="personal-title"><?php echo $v['nickname'];?></p>
		<div class="personal-imgBox">
			<a href="userInfoReset.html" class="userIcon"></a>
		</div>
		<p class="personalId"><?php echo $v['gai_number'];?></p>
		<p class="personalAddress"><?php echo $model->getName($v['province_id']);?><?php echo  $model->getName($v['city_id']);?></p>
		<ul class="userInfo">
			<li><a href="<?php echo $this->createUrl("buyrecord"); ?>">购买记录<span class="arrowRight"><span></span></span></a></li>
			<li><a href="<?php echo $this->createUrl("getproduct"); ?>">获得的奖品<span class="arrowRight"><span></span></span></a></li>
			<li>
				<a href="<?php echo $this->createUrl("addressset"); ?>">
					<p><span>收货地址管理</span><br/><span class="AdrInfo">请添加收货地址</span></p>
					<!-- <span>收货地址管理</span><span class="AdrInfo">请添加收货地址</span> -->
					<span class="arrowRight"><span></span></span>
					
				</a>
			</li>
			<div class="clearfix"></div>
		</ul>
		<div class="unLoginBtn">
			<a href="javascript:;">退出登录</a>
		</div>
	</div>
	<div class="h60"></div>
	<div class="floatLayout"></div>
	<div class="unLoginConfirm">
		<a href="javascript:;">确定退出</a>
		<a href="javascript:void(0)" class="cancel">取消</a>
	</div>
	<footer>
		<nav id="guide">
			<a href="index.html" class="home">首页</a>
			<a href="product.html" class="product">所有商品</a>
			<a href="announce.html" class="announce">最新揭晓</a>
			<a href="cart.html" class="cart"><i class="print">2</i>购物车</a>
			<a href="javascript:;" class="user active">个人中心</a>
		</nav>
	</footer>
	<script>
	//导航点击切换
		$("#guide").find("a").click(function(){
			$("#guide a").removeClass("active");
			$(this).addClass("active");
		})
	</script>
</body>
<script type="text/javascript">
$(function(){
	var oHeight = $('body').height();
	$('.floatLayout').css({'height':oHeight});
	$('.unLoginBtn a').tap(function(){
		$('.floatLayout').show();
		$('footer').hide();
		$('.unLoginConfirm').show().addClass('active');
	})

	$('.cancel').click(function(){
		$('.floatLayout').hide();
		$('footer').show();
		$('.unLoginConfirm').hide().removeClass('active');
	})
})
</script>
</html>