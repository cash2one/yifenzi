<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!-- <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script> -->
	<title>微信支付样例-支付</title>
</head>
<body>
<br />
<font color="#9ACD32"><b>该笔订单支付金额为<span
			style="color: #f00; font-size: 50px">1分</span>钱
	</b></font>
<br />
<br />
<div align="center">
	<!-- OnClientClick="javascript:callpay();return false;" -->
	<button type="button" Onclick="callpay()">立即支付</button>
	<!-- <button type="button" OnClientClick="javascript:test();return false;">立即支付</button> -->
	<!--<button type="button" Onclick="test();">立即支付</button>-->
</div>
</body>
</html>
<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				alert(res.err_code+res.err_desc+res.err_msg);
			}
		);
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
</script>