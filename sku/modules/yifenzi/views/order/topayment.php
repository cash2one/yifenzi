<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>一份子-结算</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<!-- 微信测试用清理缓存  -->
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />

	<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="email=no">
	<link rel="stylesheet" type="text/css" href="/yifenzi/css/common.css?v=1">
	<script src="/yifenzi/js/zepto.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<body class="pb-60">
	<header>
		<h2>结算</h2>
		<a href="javascript:history.go(-1);" class="goback_btn"></a>
	</header>
	<div class="container">
	<div class="payment_list">
		<ul>
			<!-- <li>
				<img src="http://usr.im/88x88" alt="">
				<span class="period">[第28期]</span>
				<span class="name">华为Mate8 3GB+32GB版</span> 
				<span>1人次/<i class="price">&yen;1.00</i></span>
			</li> -->
			<?php foreach($ordergoods as $k=>$v):?>
    			<li data="<?php echo $v['order_id'];?>" lat="<?php echo $v['order_sn'];?>">
    				<img src="<?php echo ATTR_DOMAIN.'/'.$v['goods_image'] ?>" alt="">
    				<span class="period">[第<?php echo $v['current_nper'] ;?>期]</span>
    				<span class="name"><?php echo $v['goods_name'] ;?></span> 
    				<span>1人次/<i class="price">&yen;<?php echo  $v['single_price'];?></i></span>
    			</li>
			<?php endforeach;?>
		</ul>
	</div>
	<div class="payment_total">
		总需支付金额：<i class="price">&yen;<?php echo $ordergoods[0]['order_amount'] ? $ordergoods[0]['order_amount'] : 0.00;?></i>
	</div>
	<div class="payment_tips">
		<span class="title">支付方式</span>
	</div>
	<div class="payments" >
		<ul id="payments_type">
            <?php if(!empty($PayDataConfig) && ($PayDataConfig['JFPALY'] == 1)):?>
			<li>
				<span class="payIcon pay_1">积分支付</span>
                <span class="accountRemain">账户余额：<em><?php echo $today_amount;?></em></span>
				<span class="checkBox checked" data="pay_1"></span>
			</li>
            <?php endif;?>
                <?php if(!empty($PayDataConfig) && ($PayDataConfig['GHTPALY'] == 1)):?>
			<li>
				<span class="payIcon pay_2">在线支付</span>
				<span class="checkBox <?php if(($PayDataConfig['JFPALY'] == 0)) echo "checked";?>" data="pay_2"></span>
			</li>
            <?php endif;?>
                <?php if(!empty($PayDataConfig) && ($PayDataConfig['WXPAY'] == 1)):?>
			<li>
				<span class="payIcon pay_3">微信支付</span>
				<span class="checkBox <?php if(($PayDataConfig['GHTPALY'] == 0) && ($PayDataConfig['JFPALY'] == 0)) echo "checked";?>" data="pay_3"></span>
			</li>
            <?php endif;?>
		</ul>
	</div>
	<div class="btn_bar">
		<a href="javascript:;" class="pay_btn">去支付</a>
	</div>
	<!-- 支付样式-->
	<div class="layer"></div>
	<div class="pay_popup" style="display:none">
		<a href="javascript:;" class="close_btn"></a>
		<div class="pay_tips">请输入6位支付密码</div>
		<div class="pay_tol">￥00.00</div>
		<div class="pay_input_pw" style="position:relative">
			<input id="payPassWord" maxlength="6" type="password"/>
			<ul class="print">
				<li><i></i></li>
				<li><i></i></li>
				<li><i></i></li>
				<li><i></i></li>
				<li><i></i></li>
				<li style="margin-right: -1px;"><i></i></li>
			</ul>
		</div>
		<span class="back_tips"></span>
		<a href="javascript:{var retUrl = escape(window.location.href);window.location.href='/member/restpaypass?retUrl='+retUrl+'&types=set';}" class="reset-paypw">设置支付密码</a>
	</div>
	<!-- End 支付样式 -->
	</div>
	<script>
	$(".payments li").click(function(){
		$(".payments .checkBox").removeClass("checked");
		$(this).find(".checkBox").addClass("checked");
	})

	$('.close_btn').click(function(){
		$('.pay_popup').hide();
		$('.layer').hide();
		$('.pay_input_pw input').val('');
	})

	$(".print").click(function(){
		//支付等待的时候阻止编辑密码
		if(!$(".back_tips").hasClass('loadding')){
			$('.pay_input_pw input').focus();
		}
	})
    var yyy = 1;
	//支付密码Js
	$('.pay_btn').click(function(){
		var pay_type = $(".payments .checked").attr("data");

		switch ( pay_type ){
			case 'pay_1':
				if($('#payments_type').find('.checked').parent().index()==0){

					var retUrl = escape(window.location.href);
					//验证支付密码是否有设置
					$.ajax({
						type:"post",
						url:'/order/checkPayPass',
						dataType:"json",
						data:{'YII_CSRF_TOKEN':"<?php echo Yii::app()->request->csrfToken?>"},
						success:function(data){
							if ( data && (data.err == 1 || data.err == '1') ){
								alert(data.msg);
								window.location.href="/member/restpaypass?retUrl="+retUrl+"&types=set";
								return false;
							}

							//验证完成，用户有支付密码时执行的代码
							$(".pay_tol").text($(".payment_total .price").text());
							$('.layer').show();
							$('.pay_popup').show();
                            $('.pay_input_pw input').focus();

						}
					});

				}
				break;
			case 'pay_2':
				topay();
				break;
			case 'pay_3':
				var orderID = 0;
				var orderSN = '';
				$(".payment_list ul li").each(function(){
					orderID = $(this).attr("data");
					return false;
				});

				$(".payment_list ul li").each(function(){
					orderSN = $(this).attr("lat");
					return false;
				});

				if ( !pay_type && !orderID )  return false;

				<?php if (!isset($is_wx)){?>
					var url = "/order/wxpay/order_id/"+orderID+"_"+orderSN;
					window.location.href = url;
				<?php }else{?>
					callpay();
				<?php } ?>

				break;
		}

		return false;

	});

	$(document).ready(function(){
		var url = window.location.href;
//		var s = 'http://yifenzi.gaiwangsku.com/order/topayment/order_id/157';
		var bool = url.indexOf("/order/wxpay/order_id/");
		if (bool > 0){
			$(".payments .checkBox").removeClass("checked");
			$(".checkBox[data=pay_3]").addClass("checked");
			callpay();
		}
	});

	//调用微信JS api 支付
	function jsApiCall()
	{
		<?php if (isset($jsApiParameters)){?>
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				<?php echo $jsApiParameters; ?>,
				function(res){
//					WeixinJSBridge.log(res.err_msg);
//					alert(res.err_code+res.err_desc+res.err_msg);
//					alert(res.err_msg);
					if(res.err_msg == "get_brand_wcpay_request:ok") {
						var orderID = 0;
						$(".payment_list ul li").each(function(){
							orderID = $(this).attr("data");
							return false;
						});

						var url = '/order/ordersuccess?code='+orderID+"_2"+"&msg=支付成功";
						var Ochannel = 1; //默认
						<?php if( isset($order_channel) ){?>
							Ochannel = <?php echo $order_channel?>;
							var order_channel = parseInt(Ochannel);
							if (order_channel == 2){
								url =  "<?php echo DOMAIN_YIFENZI2; ?>/order/ordersuccess?code="+orderID+"_2"+"&msg=支付成功";
							}

							if (order_channel == 3){
								url =  "<?php echo DOMAIN_YIFENZI3; ?>/order/ordersuccess?code="+orderID+"_2"+"&msg=支付成功";
							}
						<?php }?>
						window.location.href = url;
					}
				}
			);
		<?php } ?>


	}

	function callpay()
	{
		if (typeof(WeixinJSBridge) == "undefined"){
			if( document.addEventListener ){
				document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			}else if (document.attachEvent){
				document.attachEvent('WeixinJSBridgeReady', jsApiCall);
				document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			}
		}else{
			jsApiCall();
		}
	}


	$('#payPassWord').on('input',function(){
		var $this = $(this);
		var num = $this.val().length;
		//根据输入增减小圆点
		$(".print li i").removeClass("active");
		for(var i =0;i<num;i++){
			$(".print li i").eq(i).addClass("active");
		}
		if($this.val().length==6){
			//输入6位密码执行....
			$('.pay_input_pw input').blur().attr("disabled","disabled");
			$(".back_tips").attr("class","back_tips loadding").text("正在支付请稍后...");
			$.ajax({
				type:"post",
				url:'/order/paypasscorrect',
				dataType:"json",
				data:{'YII_CSRF_TOKEN':"<?php echo Yii::app()->request->csrfToken?>","code":$this.val()},
				success:function(data){
					console.info(data);
					if ( data && (data.err == 1 || data.err == '1') ){
						$('.pay_input_pw input').focus().val('').removeAttr("disabled");
						$(".print li i").removeClass("active");
						$(".back_tips").attr("class","back_tips wrong").text("密码错误，请重新输入");
						$('.close_btn').show();
						return false;
					}else{
//						$('.pay_input_pw input').blur();
						$('.close_btn').hide();
						topay();
					}
				}
			});
		}
	})
	
	function topay(){
		var pay_type = $(".payments .checked").attr("data");
		var orderID = 0;
		var orderSN = '';
		$(".payment_list ul li").each(function(){
			orderID = $(this).attr("data");
			return false;
		});

		$(".payment_list ul li").each(function(){
			orderSN = $(this).attr("lat");
			return false;
		});

		if ( !pay_type && !orderID )  return false;

		switch( pay_type ){
		case "pay_1":

			//准备去支付代码
			var price = $(".payment_total .price").text();
			var remark = '订单支付成功，金额为：' + price;
			$.ajax({
				type:"post",
				url:'<?php echo Yii::app()->createUrl('yifenzi/order/requestOrderPay')?>',
				dataType:"json",
				data:{'YII_CSRF_TOKEN':"<?php echo Yii::app()->request->csrfToken?>","orderID":orderID,"pay_type":pay_type,"orderSN":orderSN,"remark":remark},
				success:function(data){
					var url = '';

					url = "/order/ordersuccess?code="+orderID+"_"+data.err+"&msg="+data.msg;
					if (data.order_channel != undefined){
						var order_channel = parseInt(data.order_channel);
						if (order_channel == 2){
							url =  "<?php echo DOMAIN_YIFENZI2; ?>/order/ordersuccess?code="+orderID+"_"+data.err+"&msg="+data.msg;
						}

						if (order_channel == 3){
							url =  "<?php echo DOMAIN_YIFENZI3; ?>/order/ordersuccess?code="+orderID+"_"+data.err+"&msg="+data.msg;
						}
					}
					window.location.href=url;
				}
			});
			break;
			case "pay_2":
				//高汇通支付
				var price = $(".payment_total .price").text();
				var remark = '订单支付成功，金额为：' + price;
				$.ajax({
					type:"post",
					url:'<?php echo Yii::app()->createUrl('yifenzi/order/requestOrderPay')?>',
					dataType:"json",
					data:{'YII_CSRF_TOKEN':"<?php echo Yii::app()->request->csrfToken?>","orderID":orderID,"pay_type":pay_type,"orderSN":orderSN,"remark":remark},
					success:function(data){
						console.info(data);
						if (data){
							var status = parseInt(data.err);
							if ( status == 4 ){
								post(data.baseurl,data.params);
//								var url = "/order/ordersuccess?code="+orderID+"_"+data.err+"&msg="+data.msg;
//								window.location.href=data.url;

							}
						}
//						var url = "/order/ordersuccess?code="+orderID+"_"+data.err+"&msg="+data.msg;
//						window.location.href=url;
					}
				});
			break;
		}
		
	}

	function post(url, params) {
		var temp = document.createElement("form");
		temp.action = url;
		temp.method = "post";
//		temp.style.display = "none";
		for (var x in params) {
			var opt = document.createElement("input");
//			opt.type="text";
			opt.name = x;
			opt.setAttribute("value",params[x]);
			opt.setAttribute("type","hidden");
			temp.appendChild(opt);
		}

		var sb = document.createElement("input");
		sb.setAttribute("type","submit");
		sb.setAttribute("class","f_submit");
        sb.setAttribute("type","hidden");
		temp.appendChild(sb);

		document.body.appendChild(temp);
		temp.submit();
		return temp;
	}
	</script>
</body>
</html>