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
<?php
header("Cache-control: private");
?>
<div class="login_box">
<div class="logo_bar">
    <img class="logo" src="<?php echo Yii::app()->baseUrl ?>/yifenzi3/images/logo.png" alt="一份子">
</div>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'login-form',
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true
    ),
        ));
?>
<?php
    CHtml::$errorContainerTag = 'p';
    $form->errorMessageCssClass = 'tips';
?>
<div class="login_form">
    <div class="input_box">
        <?php
        echo $form->textField($model, 'username', array('class' => 'login_user', 'placeholder' => '请输入GW号或手机号','maxlength'=>11));
        echo $form->error($model, 'username');
        ?>
    </div>
    <div class="input_box">
        <?php
        echo $form->passwordField($model, 'password', array('class' => 'login_pw', 'placeholder' => '请输入密码','maxlength'=>20));
        echo $form->error($model, 'password');
        ?>
    </div>

    <!-- 验证码开始 -->
    <?php if (LoginForm::captchaRequirement()): ?>
    <div class="input_box">
        <?php
        echo $form->textField($model, 'verifyCode', array(
            'class' => 'login_pw',
            'placeholder' => Yii::t('memberHome', '请输入验证码'),
        ));
        ?>
        <span style="position: absolute;right: 100px; top: 10px;;">
                            <?php
                            $this->widget('CCaptcha', array(
                                'showRefreshButton' => false,
                                'clickableImage' => false,
                                'id' => 'verifyCodeImg',
                                'imageOptions' => array('alt' => Yii::t('memberHome', '点击换图'), 'title' => Yii::t('memberHome', '点击换图'))
                            ));
                            ?>
                        </span>
        <?php echo $form->error($model, 'verifyCode'); ?>
    </div>
        <script>
            //点击旁边的刷选验证码
            function changeVeryfyCode() {
                jQuery.ajax({
                    url: "<?php echo Yii::app()->createUrl('/yifenzi3/member/captcha/refresh/1') ?>",
                    dataType: 'json',
                    cache: false,
                    success: function(data) {
                        jQuery('#verifyCodeImg').attr('src', data['url']);
                        jQuery('body').data('captcha.hash', [data['hash1'], data['hash2']]);
                    }
                });
                return false;
            }
        </script>
    <?php endif; ?>
    <!-- 验证码结束 -->

    <div class="input_box forgetpw">
        <?php if (LoginForm::captchaRequirement()): ?>
        <span style="color: #ffffff;font-size: 14px;padding: 8px;display: inline-block;">看不清？<em onclick="changeVeryfyCode()" >换一张</em></span>
        <?php endif;?>
        <a href="<?php echo $this->createUrl('member/resetPassword'); ?>" class="forget_pw">忘记密码</a>
    </div>
</div>
<div class="btn_group">
    <a href="javascript:$('#login-form').get(0).submit()" class="login_btn">登录</a>
    <a href="<?php echo $this->createUrl('member/register') ?>" class="register_btn">注册</a>
</div>
    <div style="height:60px;"></div>
</div>
<?php
$this->endWidget()?>
<body class="<?php echo $this->bodyClass?>">
<script>
    <?php
        if($message = $this->getFlash('success')){
            echo "alert('{$message}')";
        }
    ?>
</script>
<?php echo $this->renderPartial('/layouts/_footer')?>
</body>