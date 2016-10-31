<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>商品录入登录页</title>
    <link rel="stylesheet" type="text/css" href="<?php echo CSS_DOMAIN; ?>style_input1.css" />

</head>
<style>
    .red{
        color: red;
    }
</style>
<body>	
	<div class="wrapper">
    	<div class="web-header">
        	<div class="title fl">商品录入系统</div>
        </div>
        <div class="web-login-bg">
            <div class="web-login">
                <p class="title">商品录入系统</p>
                <?php
            $form = $this->beginWidget('CActiveForm', array(
                'id' => 'home-form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => true,
                'clientOptions' => array(
                    'validateOnSubmit' => true,
                ),
            ));
            ?>
                <p><span><?php echo Yii::t('home','用户名')?>：</span>
                <?php echo $form->textField($model, 'username', array('class' => 'input-login')); ?>
                </p>
                <?php echo $form->error($model, 'username',array('class'=>'red')); ?>
                <p><span><?php echo Yii::t('home','密&nbsp;&nbsp;&nbsp;&nbsp;码')?>：</span>
                <?php echo $form->passwordField($model, 'password', array('class' => 'input-login')); ?>            
                </p>
               <?php echo $form->error($model, 'password',array('class'=>'red')); ?>
                <p><span>验证码：</span> 
                 <?php echo $form->textField($model, 'verifyCode', array('class' => 'input-login')); ?>
                 <?php $this->widget('CCaptcha', array('showRefreshButton' => false, 'clickableImage' => true, 'imageOptions' => array('alt' => Yii::t('home','点击换图'), 'title' => Yii::t('home','点击换图'), 'style' => 'margin-bottom:-502px;margin-left:305px;margin-top:-30px;cursor:pointer'))); ?>           
                </p>

               <?php echo $form->error($model, 'verifyCode',array('class'=>'red')); ?>

                <p>
                <?php echo CHtml::submitButton(Yii::t('input', '登录'), array('class' => 'submit-login')); ?></p>
              
                     <?php $this->endWidget(); ?>
            </div>
        </div>
    </div>
</body>
</html>
<script>
<?php if (!empty($msg)) { ?>
	alert("<?php echo $msg ?>");
<?php } ?>
    
    $("#LoginForm_verifyCode").blur(function(){
        setTimeout(function(){
        	//alert($("#LoginForm_verifyCode_em_").attr("style"));
        	if($("#LoginForm_verifyCode_em_").attr('style')!='display: none;' && $("#LoginForm_verifyCode_em_").attr('style')!=null){
    			$("#yw0").click();
    		}
            },1000);
		
        });
</script>