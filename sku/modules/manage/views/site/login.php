<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo Yii::t('site','后台管理登录'); ?></title>
        <link href="/manage/css/login.css?v=2" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div class="dWrapper">
            <div id="dLogin" class="pnlLogin">
                <div class="errormsg"></div>
                <?php
                $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'login-form',
                    'enableClientValidation' => false,
                       'enableAjaxValidation' => false,
                    'clientOptions'=>array(
                            'validateOnSubmit'=>true,
                    ),
                ));
                ?>
                <table cellpadding="0" cellspacing="0" class="tbl">
                    <tr>
                        <td class="c1"><?php echo Yii::t('site','用户名'); ?>：</td>
                        <td><?php echo $form->textField($model, 'username', array('class' => 'inputbox username ')); ?></td>
                        <td><?php echo $form->error($model, 'username', array('class' => 'field-validation-error')); ?></td>
                    </tr>
                    <tr>
                        <td class="c1"><?php echo Yii::t('site','密码'); ?>：</td>
                        <td><?php echo $form->passwordField($model, 'password', array('class' => 'inputbox password')); ?></td>
                        <td><?php echo $form->error($model, 'password', array('class' => 'field-validation-error')); ?></td>
                    </tr>
                    <tr>
                            <th class="c1"><?php echo Yii::t('site','验证码'); ?>：</th>
                        <td>
                            <?php echo $form->textField($model, 'verifyCode', array('class' => 'inputbox vcode')); ?>
                            <?php $this->widget('CCaptcha', array('showRefreshButton' => false, 'clickableImage' => true, 'imageOptions' => array('alt' => Yii::t('site','点击换图'), 'title' => Yii::t('site','点击换图'), 'style' => 'margin-bottom:-12px;cursor:pointer'))); ?>
                        </td>
                        <td><?php echo $form->error($model, 'verifyCode', array('class' => 'field-validation-error')); ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="text-align: right;"><?php echo CHtml::submitButton('', array('class' => 'btnlogin')); ?></td>
                        <td style="width:180px;"></td>
                    </tr>
                </table>   
                <?php $this->endWidget(); ?>
            </div>
        </div>
        
        
<script type="text/javascript">
	    $("#LoginForm_verifyCode").blur(function(){
        setTimeout(function(){
        	//alert($("#LoginForm_verifyCode_em_").attr("style"));
        	if($("#LoginForm_verifyCode_em_").attr('style')!='display: none;' && $("#LoginForm_verifyCode_em_").attr('style')!=null){
    			$("#yw0").click();
    		}
            },1000);
		
        });

</script>
        
    </body>
</html>
