<!--[if lt IE 8]><script>window.location.href="http://seller.<?php echo SHORT_DOMAIN ?>/home/notSupported"</script><![endif]-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="Keywords" content="" />
        <meta name="Description" content="" />
        <title><?php echo Yii::t('sellerHome', '小微企业联盟商家平台登录'); ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo CSS_DOMAIN; ?>global.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo CSS_DOMAIN; ?>seller.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo CSS_DOMAIN; ?>custom.css" />
    </head>
    <body>
    
    <!--弹窗提示-->
     <?php $this->renderPartial('/layouts/_msg'); ?>
    
        <div class="wrap">
            <div class="header bgb10">
                <div class="logowrap clearfix">
                    <?php echo CHtml::link(CHtml::image(DOMAIN . '/images/bg/seller_logo.jpg'), GAIWANG_DOMAIN, array('class' => 'slogo','target'=>'_blank')); ?>
                    <span class="logoTit">
                        <h1><?php echo Yii::t('partnerModule.home', '小微企业联盟商家平台'); ?></h1>
                        <p> &nbsp;SKU seller platform</p>
                    </span>
                </div>
            </div>
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
            <div class="main bgfff clearfix">
                <div class="w1060">
                    <div class="sellerLogin">
                        <h1><?php echo Yii::t('partnerModule.home', '卖家登录'); ?></h1>
                        <dl class="clearfix">
                            <dt><?php echo $form->label($model, 'username'); ?>：</dt>
                            <dd>
                                <?php echo $form->textField($model, 'username', array('class' => 'inputtxt2', 'placeholder' => Yii::t('partnerModule.home', '用户名/会员编号')));
                                ?>
                            </dd>
                            <dd style="height: 100%">
                                <?php
                                if (!empty($users)) {
                                    echo CHtml::label(
                                            Yii::t('memberHome', '{mobile}绑定了多个盖网编号，请选择', array('{mobile}' => $model->username)), 'gai_number');

                                    echo CHtml::dropDownList('gai_number', '', $users, array('style' => 'position: absolute;'));
                                }
                                ?>
                                <?php echo $form->error($model, 'username'); ?>
                            </dd>
                        </dl>
                        <dl class="clearfix">
                            <dt><?php echo $form->label($model, 'password'); ?>：</dt>
                            <dd>
                                <?php echo $form->passwordField($model, 'password', array('class' => 'inputtxt2')); ?>

                            </dd>
                            <dd style="height: 100%"><?php echo $form->error($model, 'password'); ?></dd>
                        </dl>
                         <dl class="clearfix">
                            <dt><?php echo Yii::t('partnerModule.home','验证码'); ?>：</dt>
                        <dd>
                            <?php echo $form->textField($model, 'verifyCode', array('class' => 'w100 inputtxt2')); ?>
                            <?php $this->widget('CCaptcha', array('showRefreshButton' => false, 'clickableImage' => true, 'imageOptions' => array('alt' => Yii::t('partnerModule.home','点击换图'), 'title' => Yii::t('partnerModule.home','点击换图'), 'style' => 'margin-bottom:-12px;cursor:pointer'))); ?>
                        </dd>
                        <dd style="height: 100%"><?php echo $form->error($model, 'verifyCode'); ?></dd>
                             <dd  class="checkbox01">
                                 | 语言: <?php echo CHtml::dropDownList('select_language',HtmlHelper::LANG_ZH_CN,HtmlHelper::languageInfo()) ?>
                             </dd>
                    </dl>

                        <dl class="do clearfix">
                            <?php echo CHtml::submitButton(Yii::t('partnerModule.home', '登录'), array('class' => 'sellerSubmitBtn')); ?>
                            <?php  $url = $this->createAbsoluteUrl('login'); $sku =  urlencode($url);?>
                            <?php echo CHtml::link(Yii::t('partnerModule.home', '忘记密码？'),'http://member.'.GAIWANG_SHORT_DOMAIN.'/home/resetPassword?returnUrl='.$sku,array("target"=>'_blank')); ?>
                        </dl>
                    </div>
                </div>
            </div>
            <?php $this->endWidget(); ?>
            <div class="footer clearfix">
                <p>
                    <?php echo CHtml::link(Yii::t('partnerModule.home', '关于盖网'), GAIWANG_DOMAIN.'/about',array("target"=>'_blank')); ?> | 
                    <?php echo CHtml::link(Yii::t('partnerModule.home', '帮助中心'), 'http://help.'.GAIWANG_SHORT_DOMAIN,array("target"=>'_blank')); ?>  |
                    <?php echo CHtml::link(Yii::t('partnerModule.home', '网站地图'), GAIWANG_DOMAIN.'/sitemap',array("target"=>'_blank')); ?>  | 
                    <?php echo CHtml::link(Yii::t('partnerModule.home', '诚聘英才'), GAIWANG_DOMAIN.'/job',array("target"=>'_blank')); ?>  | 
                    <?php echo CHtml::link(Yii::t('partnerModule.home', '联系客服'), GAIWANG_DOMAIN.'/contact',array("target"=>'_blank')); ?>  | 
                    <?php echo CHtml::link(Yii::t('partnerModule.home', '免责声明'), GAIWANG_DOMAIN.'/statement',array("target"=>'_blank')); ?>
                </p>
                <p><?php echo Tool::getConfig('site', 'copyright'); ?></p>
            </div>
            <script>
                (function(i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function() {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                            m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

                ga('create', 'UA-51285352-1', 'gatewang.com');
                ga('send', 'pageview');

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
        </div>
    </body>
</html>
