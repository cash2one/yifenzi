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
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl?>/yifenzi3/css/common.css">
	<script src="<?php echo Yii::app()->baseUrl?>/yifenzi3/js/zepto.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<body class="<?php echo $this->bodyClass?>">
    <?php echo $content; ?>
    <script type="text/javascript">
    $("#getVerityCode").click(function () {
        if (!$(this).hasClass('disabled')) {
            var csrf = '<?php echo Yii::app()->request->getCsrfToken() ?>';
            var mobile = $('#Member_mobile').val(); //检测是不是手机号码
            var passwd = $('#Member_password').val();
            var conpasswd = $('#Member_confirmpassword').val()
            if (jQuery.trim(mobile) == '') {
                $('#Member_mobile_em_').text('请填入手机号码').show();
                return false;
            } else {
                if(!mobile.match(/(^1[34578]{1}\d{9}$)|(^852\d{8}$)/)){
                    $('#Member_mobile_em_').text('手机号码不合法').show();
                    return false;
                }
            }
            <?php if($this->action->id == 'register'):?>
                if(jQuery.trim(passwd) == ''){
                    $('#Member_password_em_').text('请先输入密码').show();
                    return false;
                }
                if(jQuery.trim(conpasswd) == ''){
                    $('#Member_confirmpassword_em_').text('请先输入确认密码').show();
                    return false;
                }
                if(passwd != conpasswd){
                    $('#Member_confirmpassword_em_').text('密码不一致').show();
                    return false;
                }
            <?php endif;?>
            $.ajax({
                type: 'POST',
                url: '<?php echo $this->createUrl('member/getVerifyCode') ?>',
                data: {mobile: mobile, YII_CSRF_TOKEN: csrf},
                dateType: 'json',
                success: function (data) {
                    //console.log(data);
                }
            })
            $(this).addClass("disabled").text("60秒");
            var second = 60;
            var timer = setInterval(function () {
                $("#getVerityCode").text(--second + "秒");
                if (second <= -1) {
                    $("#getVerityCode").removeClass("disabled").text("重新获取验证码");
                    clearInterval(timer);
                }
            }, 1000)
        }
    });
</script>
</body>
</html>