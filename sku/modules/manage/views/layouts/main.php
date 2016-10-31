<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $this->pageTitle; ?></title>
    <link rel="shortcut icon" href="<?php echo DOMAIN ?>/favicon.ico" type="mage/x-icon">
    <link rel="icon" href="<?php echo DOMAIN ?>/favicon.ico" type="mage/x-icon">
    <script type="text/javascript">
        //            document.domain = 'gatewang.com';
    </script>
    <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
    <?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/manage/css/reg.css'); ?>
    <script type="text/javascript">
        $(document).ready(function() {
            var bodyWidth = $(".main").width();
            var ws = bodyWidth - 9;
            $(".t-com").width(ws)
            $(window).resize(function() {
                var bodyWidth = $(".main").width();
                var ws = bodyWidth - 9;
                $(".ws").width(ws)
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.tab-come ').each(function() {
                $(this).find('tr:even td').addClass('even');
                $(this).find('tr:odd td').addClass('odd');
                $(this).find('tr:even th').addClass('even');
                $(this).find('tr:odd th').addClass('odd');
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.tab-reg ').each(function() {
                $(this).find('tr:even td').css("background", "#eee");
                $(this).find('tr:odd td').css("background", "#fff");
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            var $thi = $('body,html').find("#u_title li");
            $($thi).hover(function() {
                $(this).addClass("cur").siblings().removeClass("cur");
                var $as = $("#con .con_listbox").eq($("#u_title li").index(this));
                $as.show().siblings().hide();
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.tab-reg2').find('.info').hover(function() {
                $(this).find('td').css({"background": "rgba(255, 184, 0, 0.2)"});
                $(this).next('.user').find('td').css({"background": "rgba(255, 184, 0, 0.2)"});
            }, function() {
                $(this).find('td').removeAttr("style");
                $(this).next('.user').find('td').removeAttr("style");
            });

            $('.tab-reg2').find('.user').hover(function() {
                $(this).find('td').css({"background": "rgba(255, 184, 0, 0.2)"});
                $(this).prev().find('td').css({"background": "rgba(255, 184, 0, 0.2)"});
            }, function() {
                $(this).find('td').removeAttr("style");
                $(this).prev().find('td').removeAttr("style");
            });
        });
    </script>

    <script type="text/javascript">
        var redirect = function(mod) {
            var rawUrl = '';
            if (window.parent.location.href.indexOf('?') > 0) {
                rawUrl = window.parent.location.href.substring(0, window.parent.location.href.indexOf('?'));
            }
            else {
                rawUrl = window.parent.location.href;
            }
            window.parent.location = rawUrl + '?mod=' + mod;
        }
    </script>
</head>
<body>
<div class="main">
    <div class="head-title">
        <div class="t-left"></div>
        <div class="t-com ws" style="width: 1629px;">
            <div class="t-sub">
                <?php
                $acts = array('create', 'update', 'view','edit','enterpriseUpdate','enterpriseCreate','updateRecommend','updateImportant','specValueAdmin','updateImgs','addHongBaoAmount','updateEndTime', 'creditView', 'update2','ViewQuest','past','check','record','record_one');
                $action = $this->getAction()->id;
                if (in_array($action, $acts) || (isset($this->showBack) && $this->showBack == true)){
                    if($this->id=="machine"){
                        echo CHtml::link(Yii::t('main', '返回列表'), "/?r=machine/admin", array('class' => 'regm-sub'));
                    }else{
                        echo CHtml::link(Yii::t('main', '返回列表'), 'javascript:history.back()', array('class' => 'regm-sub'));
                    }
                }
                ?>
                <?php if (isset($this->exportAction)): ?>
                    <?php $rote = ucwords($this->id) . '.' . ucwords($this->exportAction); ?>
                    <?php if (Yii::app()->user->checkAccess($rote) && isset($this->exportAction)): ?>
                        <a href="javascript:;" class="regm-sub" onclick="showExport()"><?php echo Yii::t('main', '导出excel'); ?></a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if($action == 'sellAdmin'):?>
                    <?php $rote = ucwords($this->id); ?>
                    <?php if (Yii::app()->user->checkAccess('Manage.GuadanCollect.Adjust')): ?>
                        会员月充值限额&nbsp;<a href="javascript:;" class="regm-sub" onclick="adjust()"><?php echo Yii::t('main', '调整'); ?></a>
                    <?php endif; ?>
                <?php endif;?>
            </div>
            <?php
            $this->widget('zii.widgets.CBreadcrumbs', array(
                'homeLink' => false,
                'separator' => ' > ',
                'links' => $this->breadcrumbs,
            ));
            ?>
        </div>
        <div class="t-right"></div>
    </div>
    <div class="com-box">
        <?php $this->renderPartial('/layouts/_msg'); ?>
        <?php echo $content; ?>
    </div>
</div>
</body>
</html>