<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo Yii::t('main', '小微企业联盟后台管理'); ?></title>
        <script type="text/javascript">
            document.domain = '<?php echo SHORT_DOMAIN ?>';
        </script>
        <!--[if IE 6]>
        <script src="http://restest.<?php echo SHORT_DOMAIN ?>/Res/ucenter/images/DD_belatedPNG_0.0.8a.js" mce_src="http://restest.<?php echo SHORT_DOMAIN ?>/Res/ucenter/images/DD_belatedPNG_0.0.8a.js"></script>
        <script type="text/javascript">DD_belatedPNG.fix('*');</script>
        <![endif]-->
        <link rel="stylesheet" type="text/css" href="/manage/css/common.css" />
        <link href="/manage/css/help.css" rel="stylesheet" type="text/css">  
            <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
            <script src="/manage/js/jquery-ui-1.8.18.custom.min.js" type="text/javascript"></script>
            <script src="/manage/js/admin.js" type="text/javascript"></script>
            <script type="text/javascript">
            default_mod = 'system';
            default_url = '<?php echo Yii::app()->createAbsoluteUrl('/site/index/'); ?>';
            </script>
            <script src="/manage/js/barder.js" type="text/javascript"></script>
    </head>
    <body>
        <div id="dTop">
            <div class="topctx">
                <div class="logo" title="<?php echo Yii::t('main', '小微企业联盟管理后台'); ?>"><img alt="<?php echo Yii::t('main', '小微企业联盟管理后台'); ?>" src="/manage/images/logo.png" /></div>
            </div>
            <div class="pnlInfo">
                <ul class="toolbar uinfo">
                    <li class="ico_quit"><?php echo CHtml::link(Yii::t('main', '退出登录'), $this->createAbsoluteUrl('/site/logout')); ?></li>
                    <li class="ico_home"><a href="<?php echo GAIWANG_DOMAIN ?>" target="_blank"><?php echo Yii::t('main', '盖象商城首页'); ?></a></li>
                    <li class="ico_user"><?php echo Yii::t('main', '欢迎'); ?><a href="javascript:void(0)"><?php echo $this->getUser()->name; ?></a></li>
                </ul>
            </div>
        </div>
        <div class="nav">
            <ul id="yw0">
                <li <?php if ($this->action->id == 'userInfo'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '用户信息'), array('/main/userInfo')); ?></li>
                <li class="hr"><span></span></li>
                               
                <?php if (Yii::app()->user->checkAccess('Manage.Main.WebConfig')): ?>
                    <li <?php if ($this->action->id == 'webConfig'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '网站配置管理'), array('/main/webConfig')); ?></li>
                    <li class="hr"><span></span></li>
                <?php endif; ?>
                <?php if (Yii::app()->user->checkAccess('Manage.Main.WebData')): ?>
                    <li <?php if ($this->action->id == 'webData'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '网站数据管理'), array('/main/webData')); ?></li>
                    <li class="hr"><span></span></li>
                <?php endif; ?>
                
                <?php if (Yii::app()->user->checkAccess('Manage.Main.Administrators')): ?>
                    <li <?php if ($this->action->id == 'administrators'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '管理员管理'), array('/main/administrators')); ?></li>
                    <li class="hr"><span></span></li>
                <?php endif; ?>

                <?php if (Yii::app()->user->checkAccess('Manage.Main.Partners')): ?>
                    <li <?php if ($this->action->id == 'partners'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '商户管理'), array('/main/partners')); ?></li>
                    <li class="hr"><span></span></li>
                <?php endif; ?>
   
   				<?php if (Yii::app()->user->checkAccess('Manage.Main.Goods')): ?>
                    <li <?php if ($this->action->id == 'goods'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '商品管理'), array('/main/goods')); ?></li>
                    <li class="hr"><span></span></li>
                <?php endif; ?>
   
      				<?php if (Yii::app()->user->checkAccess('Manage.Main.AppAdvert')): ?>
                    <li <?php if ($this->action->id == 'appAdvert'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '广告管理'), array('/main/appAdvert')); ?></li>
                    <li class="hr"><span></span></li>
                <?php endif; ?>
                <?php if (Yii::app()->user->checkAccess('Manage.Main.QuestResult')): ?>
                    <li <?php if ($this->action->id == 'questResult'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '问卷调查管理'), array('/main/questResult')); ?></li>
                    <li class="hr"><span></span></li>
                <?php endif; ?>
                <?php if (Yii::app()->user->checkAccess('Manage.Main.RechargeCashManagement')): ?>
                    <li <?php if ($this->action->id == 'rechargeCashManagement'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '充值提现管理'), array('/main/rechargeCashManagement')); ?></li>
                    <li class="hr"><span></span></li>
                <?php endif; ?>
                <?php if (Yii::app()->user->checkAccess('Manage.Main.GameConfig')): ?>
                    <li <?php if ($this->action->id == 'gameConfig'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '游戏管理'), array('/main/gameConfig')); ?></li>
                    <li class="hr"><span></span></li>
                <?php endif; ?>
                
                <?php if (Yii::app()->user->checkAccess('Manage.Main.Guadan')): ?>
                    <li <?php if ($this->action->id == 'guadan'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '积分挂单管理'), array('/main/guadan')); ?></li>
                    <li class="hr"><span></span></li>
                <?php endif; ?>
                
                <?php if (Yii::app()->user->checkAccess('Manage.Main.TradeManagement')): ?>
                    <li <?php if ($this->action->id == 'tradeManagement'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '交易管理'), array('/main/tradeManagement')); ?></li>
					<li class="hr"><span></span></li>
                <?php endif; ?>
                
                <?php if (Yii::app()->user->checkAccess('Manage.Main.onepartManagement')): ?>
                    <li <?php if ($this->action->id == 'onepartManagement'): ?>class="active"<?php endif; ?>><?php echo CHtml::link(Yii::t('main', '一份子后台管理'), array('/main/onepartManagement')); ?></li>
					<li class="hr"><span></span></li>
                <?php endif; ?>
   
            </ul>        
        </div>
        <div class="c-head"></div>
        <div class="bar-hs2"></div> 
        <div id="dLeft" style="float: left">
            <div class="bar-top"></div>
            <div class="navTitle"><em class="ico_mus"></em><?php echo Yii::t('main', '导航目录'); ?></div>
            <div class="bar-hs"></div>
            <div class="actionGroup">
                <?php if (is_array($menus)):  ?>
                    <?php $i = 1; ?>
                    <?php foreach ($menus as $key => $value): ?>

                        <?php //if (Yii::app()->user->checkAccess(ucfirst($this->module->id).'.'.str_replace(' ', '.', ucwords(str_replace('/', ' ', trim($value['url'], '/')))))): ?>
                            <h3 <?php if ($i == 1): ?>class="hover"<?php endif; ?>><em class="ico-1"></em><a href="javascript:void(0)"><?php echo $key; ?></a></h3>
                            <div class="ctx" <?php if ($i == 1): ?>style="display: block;"<?php endif; ?>>
                                <ul>
                                    <?php foreach ($value['sub'] as $k => $v): ?>

                                        <?php if (Yii::app()->user->checkAccess(ucfirst($this->module->id).'.'.str_replace(' ', '.', ucwords(str_replace('/', ' ', trim($v, '/'))))) || $v=='/user/modifyPassword'): ?>
                                            <li class="item"><a href="javascript:app.openUrl('<?php echo Yii::app()->createUrl($v); ?>')">▪ <?php echo $k; ?></a></li>  
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php //endif; ?>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="links"><div class="t-hr"></div> <?php echo Yii::t('main', '技术支持:广州盖网通科技有限公司'); ?></div>
            <div class="bar-footer"></div>
        </div>
        <?php if ($this->action->id == 'hotelManagement'): ?>
            <script type="text/javascript">
                $(function() {
                    $.post("<?php echo $this->createAbsoluteUrl('/hotelOrder/newHotelOrder') ?>", {YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>'}, function(data) {
                        if (data != 0) {
                            $('#total').text(data);
                            $('#assistant').show();
                        }
                    });
                })

                var tim_aip = window.setInterval(realTime, 60000);
                function realTime()
                {
                    $.post("<?php echo $this->createAbsoluteUrl('/hotelOrder/newHotelOrder') ?>", {YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>'}, function(data) {
                        if (data != 0) {
                            $('#total').text(data);
                            $('#assistant').show();
                        }
                    });
                }
            </script>

            <div id="assistant">
                <div class="assBox">
                    <a class="assClose" onclick="document.getElementById('assistant').style.display = 'none'"></a>
                    <p>亲，有<b id="total"></b>条新订单！
                        <!--<a href="#">点击查看</a>-->
                        <?php echo Chtml::link('点击查看', $this->createAbsoluteUrl('/hotelOrder/newList'), array('target' => 'dCtxFrame')) ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        <!--div id="dSplitbar">
            <a class="btn" href="javascript:app.togSidebar()" title="收起侧边栏"><img src="backoffice/style/images/bar-hs.gif" width="15" height="110" /></a>
        </div-->
        <div id="dBody"><iframe id="dCtxFrame" name="dCtxFrame" frameborder="0" scrolling="yes" class="adminFrame" style="overflow: visible;"></iframe></div>
        <script type="text/javascript">
            app.resize();
            app.loadDefault();
        </script>
    </body>
</html>
