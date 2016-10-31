<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>一份子-<?php echo $this->pageTitle ?></title>
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
<?php
/**
 * 找回密码模型
 */
header("Cache-control: private");
?>
<?php
    $form = $this->beginWidget('CActiveForm', array(
        //'id' => 'findpw-form',
		'id' => $this->id . '-form',
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
<?php if ($step == 1): ?>
    <header>
        <h2>验证手机号码</h2>
        <a href="javascript:history.go(-1);" class="goback_btn"></a>
    </header>
    
  
    <div class="forgetpw_form">
        <div class="input_box">
            <?php
            echo $form->textField($model, 'mobile', array('class' => 'register_tel','disabled'=>'disabled','placeholder' => '请输入手机号码'));
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
        <input type ="submit" value ="下一步">
    </div>

<?php else: ?>

    <header>
        <h2>设置支付密码</h2>
        <a href="javascript:history.go(-1);" class="goback_btn"></a>
    </header>
    <div class="register_form">
    <div class="input_box">
        <?php echo $form->passwordField($model, 'password3' ,array('class'=>'register_pw','placeholder'=>'请输入6位数字支付密码',"maxlength"=>6)); ?>
        <!--<input type="password" class="register_pw" placeholder="请输入6位数字支付密码" maxlength="6" required/>-->
        <p class="tips"><?php echo $form->error($model, 'password3'); ?></p>
    </div>
    <div class="input_box">
        <?php echo $form->passwordField($model, 'confirmpassword3' ,array('class'=>'register_pw','placeholder'=>'请输入6位数字支付密码',"maxlength"=>6)); ?>
        <!--<input type="password" class="register_pw" placeholder="请再次输入6位数字支付密码" maxlength="6" required/>-->
        <p class="tips"><?php echo $form->error($model, 'confirmpassword3'); ?></p>
    </div>
        <?php echo $form->hiddenField($model, 'mobile'); ?>
        <?php echo $form->hiddenField($model, 'verifyCode'); ?>
        <?php echo CHtml::hiddenField('step', $step); ?>
    </div>
    <div class="btn_bar">
        <input type ="submit" value ="确定">
    </div>

<?php endif; ?>
<?php $this->endWidget(); ?>
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
</html>