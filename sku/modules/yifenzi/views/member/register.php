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
<header>
    <h2>注 册</h2>
    <a href="javascript:history.go(-1);" class="goback_btn"></a>
</header>
 <?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id.'-form',
    'enableAjaxValidation'=>true,
    'enableClientValidation' => true,
    "clientOptions" => array(
        'validateOnSubmit' => true,
    ),
        ))
?>
<?php
CHtml::$errorContainerTag = 'p';
$form->errorMessageCssClass = 'tips';
?>
<div class="register_form">
    <div class="input_box">
        <?php
        echo $form->textField($model, 'mobile', array('class' => 'register_tel', 'placeholder' => '请输入手机号码',"maxlength"=>11,'onBlur'=>"mobileTest(this);", 'autocomplete' => false));
        echo $form->error($model, 'mobile');
        ?>
    </div>
    <div class="input_box">
        <?php
        echo $form->passwordField($model, 'password', array('class' => 'register_pw', 'placeholder' => '请输入6-20位密码',"maxlength"=>20));
        echo $form->error($model, 'password');
        ?>
    </div>
    <div class="input_box">
        <?php
        echo $form->passwordField($model, 'confirmpassword', array('class' => 'register_pw', 'placeholder' => '请再次输入密码',"maxlength"=>20));
        echo $form->error($model, 'confirmpassword');
        ?>
    </div>
    <div class="register_verify">
        <?php
        echo $form->textField($model, 'verifyCode', array('class' => 'verify_input', 'placeholder' => '验证码',"maxlength"=>6));
        echo $form->error($model, 'verifyCode');
        ?>
        <a href="javascript:void(0);" class="verify_btn" id="getVerityCode">获取验证码</a><!--  添加类disabled为禁止点击状态-->
    </div>
    <br/>
    <div class="" style="">
        <input name="Fruit" type="checkbox" checked="checked" id="protocol"  value="" style="width: 15px;text-align:left;"/>
        我同意
        <a href="<?php echo $this->createUrl('member/lookProtocol'); ?>" class="" style="text-align:left;">一分子协议</a>
    </div>
</div>
<div class="btn_bar">
    <?php  echo CHtml::submitButton('注册',array('style'=>'border:0')) ?>
    <!--<a href="javascript:$('#register-form').submit()">注 册</a>-->
</div>
<?php $this->endWidget(); ?>
<body class="<?php echo $this->bodyClass?>">
    <script type="text/javascript">
        /**
         * 手机验证通过之后进行数据同步
         * @param obj
         * @returns {boolean}
         */
        function mobileTest(obj){
            var csrf = '<?php echo Yii::app()->request->getCsrfToken() ?>';
            var v = $(obj).val();
            if (!v) return false;
            var filter=/^0?(13[0-9]|15[012356789]|17[0678]|18[0-9]|14[57])[0-9]{8}$/;
            var ret = filter.test(v);
            if (ret == true){
                $.ajax({
                    type: 'POST',
                    url: '<?php echo $this->createUrl('member/mobilesync') ?>',
                    data: {mobile: v, YII_CSRF_TOKEN: csrf},
                    dateType: 'json',
                    success: function (data) {
                        console.log(data);
                    }
                })
            }
        }
        $("#protocol").click(function () {
            if($("input[type='checkbox']").is(':checked')) {
                $('input[name=yt0]').removeAttr("disabled");
                $('input[name=yt0]').css('background','#283c4b');
            }else{
                $('input[name=yt0]').attr("disabled", true);
                $('input[name=yt0]').css('background','#ededed');
            }
        });
    $("#getVerityCode").click(function () {
		$('input').blur(function(){
		$('.verify_btn').removeClass('gray');
	    })
		var obool1 = $('#Member_mobile_em_').css('display')=='block'?false:true;
		var obool2 = $('#Member_password_em_').css('display')=='block'?false:true;
		var obool3 = $('#Member_confirmpassword_em_').css('display')=='block'?false:true;
		
		if (!$(this).hasClass('disabled')) {
			$('.verify_btn').addClass('gray');
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
				<?php endif;?>
			}
		if(obool1&&obool2&&obool3){
            if (!$(this).hasClass('disabled')) {
                $(this).addClass("disabled").text("60秒");
                var second = 60;
                var timer = setInterval(function () {
                    $("#getVerityCode").text(--second + "秒");
                    if (second <= -1) {
                        $("#getVerityCode").removeClass("disabled").text("重新获取验证码");
                        $('.verify_btn').removeClass('gray');
                        clearInterval(timer);
                    }
                }, 1000)
            }
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
    });
</script>
</body>