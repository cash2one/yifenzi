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
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl?>/yifenzi/css/common.css">
	<script src="<?php echo Yii::app()->baseUrl?>/yifenzi/js/zepto.min.js" type="text/javascript" charset="utf-8"></script>
<?php
/**
 * 找回密码模型
 */
header("Cache-control: private");
?>

<?php if ($step == 1): ?>
    <header>
        <h2>忘记密码</h2>
        <a href="javascript:history.go(-1);" class="goback_btn"></a>
    </header>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'findpw-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        )
    ));
    ?>
    <?php
    CHtml::$errorContainerTag = 'p';
    $form->errorMessageCssClass = 'tips';
    ?>
    <div class="forgetpw_form">
        <div class="input_box">
            <?php
            echo $form->textField($model, 'mobile', array('class' => 'register_tel', 'placeholder' => '请输入手机号码'));
            echo $form->error($model, 'mobile');
            ?>
        </div>
        <div class="register_verify">
            <?php
            echo $form->textField($model, 'verifyCode', array('class' => 'verify_input', 'placeholder' => '验证码'));
            echo $form->error($model, 'verifyCode');
            ?>
            <?php echo CHtml::hiddenField('step', $step); ?>
            <a href="javascript:void(0);" class="verify_btn enabled" id="getVerityCode">获取验证码</a><!--  添加类disabled为禁止点击状态-->
        </div>
    </div>
    <div class="btn_bar">
        <a href="javascript:$('#findpw-form').submit()">确定</a>
    </div>
    <?php $this->endWidget(); ?>
<?php else: ?>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'changepw-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        )
    ));
    ?>
    <?php
    CHtml::$errorContainerTag = 'p';
    $form->errorMessageCssClass = 'tips';
    ?>
    <header>
        <h2>设置密码</h2>
        <a href="javascript:history.go(-1);" class="goback_btn"></a>
    </header>
    <div class="register_form">
        <div class="input_box">
            <?php
            echo $form->passwordField($model, 'password', array('class' => 'register_pw', 'placeholder' => '请输入密码'));
            echo $form->error($model, 'password');
            ?>
        </div>
        <div class="input_box">    
            <?php
            echo $form->passwordField($model, 'confirmpassword', array('class' => 'register_pw', 'placeholder' => '请再次输入密码'));
            echo $form->error($model, 'confirmpassword');
            ?>
        </div>
        <?php echo $form->hiddenField($model, 'mobile'); ?>
        <?php echo $form->hiddenField($model, 'verifyCode'); ?>
        <?php echo CHtml::hiddenField('step', $step); ?>
    </div>
    <div class="btn_bar">
        <a href="javascript:$('#changepw-form').submit()">确定</a>
    </div>
    <?php $this->endWidget(); ?>
<?php endif; ?>
<body class="<?php echo $this->bodyClass?>">
<script>
    $('input[type=text]').blur(function(){
		$('.verify_btn').removeClass('gray');
	})
    // 防止重复提交
    $('form').on('beforeValidate', function (e) {
        $(':submit').attr('disabled', true).addClass('disabled');
    });
    $('form').on('afterValidate', function (e) {
        if (cheched = $(this).data('yiiActiveForm').validated == false) {
            $(':submit').removeAttr('disabled').removeClass('disabled');
        }
    });
    $('form').on('beforeSubmit', function (e) {
        $(':submit').attr('disabled', true).addClass('disabled');
    });
	
	$("#getVerityCode").click(function () {
		var obool1 = $('#Member_mobile_em_').css('display')=='block'?false:true;
		//var obool2 = $('#Member_password_em_').css('display')=='block'?false:true;
		//var obool3 = $('#Member_confirmpassword_em_').css('display')=='block'?false:true;
		
		if (!$(this).hasClass('disabled')) {
				var csrf = '<?php echo Yii::app()->request->getCsrfToken() ?>';
				var mobile = $('#Member_mobile').val(); //检测是不是手机号码
				var passwd = $('#Member_password').val();
				var conpasswd = $('#Member_confirmpassword').val();

				
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
					
					if ( $("#Member_mobile_em_").html() != "" ){
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
				
			}
		if(obool1){
            if (!$(this).hasClass('disabled')) {
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
		}
		else{
			$('.verify_btn').addClass('gray');
			if (!$(this).hasClass('disabled')) {
				var csrf = '<?php echo Yii::app()->request->getCsrfToken() ?>';
				var mobile = $('#Member_mobile').val(); //检测是不是手机号码
				var passwd = $('#Member_password').val();
				var conpasswd = $('#Member_confirmpassword').val();

				
				if (jQuery.trim(mobile) == '') {
					$('#Member_mobile_em_').text('请填入手机号码').show();
					return false;
				} else {
					if(!mobile.match(/(^1[34578]{1}\d{9}$)|(^852\d{8}$)/)){
						$('#Member_mobile_em_').text('手机号码不合法').show();
						return false;
					}
				//	if(mobileRs){
				//		$('#Member_mobile_em_').text('该手机号码已注册咯,没在啦').show();
				 //       return false;
					//}
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
					
					if ( $("#Member_mobile_em_").html() != "" ){
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
				
			}

		}
		
		
    });
</script>
</body>