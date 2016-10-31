<style>
.btn.greens{width: 180px;line-height: 40px;display: block;margin:20px auto;border: 1px solid #aeaeae;color: #aeaeae;border-radius: 40px;text-align: center;font-size: 13px;}
</style>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo $this->pageTitle;?></title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<!-- 微信测试用清理缓存  -->
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />

	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="email=no">
	<link rel="stylesheet" type="text/css" href="/yifenzi2/css/common.css">
	<script src="/yifenzi2/js/zepto.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
	<header>
		<h2>一份子结果</h2>
		<a href="javascript:history.go(-1);" class="goback_btn"></a>
	</header>
	<!-- 状态显示  -->
	<div class="container">
	<?php if($data['status'] == 2){?>
	<div class="result">
		<i></i>
		<h3>支付成功，请等待揭晓</h3>
		<span>成功支付<?php echo count($data['orderdata']);?>个商品</span>
		<a href="<?php echo Yii::app()->createUrl('/yifenzi2/user/buyrecord') ?>" class="btn">查看购买记录</a>
		<a href="<?php echo $this->createUrl('site/index');?>" class="btn green">继续购买</a>
	</div>
	<?php }else{ ?>
	<div class="payment_tips error">
		<span class="title"></span>
		<?php echo $data['msg']?>
	</div>
	<?php }?>
	<!-- 状态显示  END-->
	
	<div class="payment_tips success">
	<?php 
	   list($date,$sec) = explode(".", $data['orderdata'][0]['addtime']);
	?>
		<div class="order_time"><?php echo date('Y-m-d H:i:s',$date).".".$sec?></div>
		<div class="payment_list">
			<ul>
				<?php foreach($data['orderdata'] as $k=>$v):?>
				<li>
					<span class="period">[第<?php echo $v['cnper']?>期]</span>
					<span class="name"><?php echo $v['goods_name']?></span> 
					<span class="person-time"><?php echo $v['num']?>人次</span>
				</li>
				<?php endforeach;?>
			</ul>
		</div>
		<?php if($data['status'] != 2 ) :?>
		<a href="<?php echo $this->createUrl('carts/index');?>" class="btn greens">返回购物车</a>
		<?php endif?>
	</div>
	
	</div>
</body>

</html>