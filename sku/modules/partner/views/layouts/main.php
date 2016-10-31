<?php
// 店家布局文件
/* @var $this SController */
?>
<!--[if lt IE 8]><script>window.location.href="http://partner.<?php echo SHORT_DOMAIN ?>/home/notSupported"</script><![endif]-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="Keywords" content="" />
        <meta name="Description" content="" />
        <title><?php echo CHtml::encode($this->pageTitle) ?></title>
        <link rel="shortcut icon" href="<?php echo DOMAIN ?>/favicon.ico" type="mage/x-icon">
            <link rel="icon" href="<?php echo DOMAIN ?>/favicon.ico" type="mage/x-icon">
                <link href="<?php echo CSS_DOMAIN; ?>global.css" rel="stylesheet" type="text/css" />
                <link href="<?php echo CSS_DOMAIN; ?>seller.css" rel="stylesheet" type="text/css" />
                <link href="<?php echo CSS_DOMAIN; ?>custom.css" rel="stylesheet" type="text/css"/>
                <link href="<?php echo CSS_DOMAIN; ?>custom-seller.css" rel="stylesheet" type="text/css"/>
                <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
                <!--处理IE6中透明图片兼容问题-->
                <!--[if IE 6]>
                <script type="text/javascript" src="../js/DD_belatedPNG.js" ></script>
                <script type="text/javascript">
                    DD_belatedPNG.fix('.logo img,.menu dl dt a,.menu dl dd ul li a');
                </script>
                <![endif]-->
                <script type="text/javascript">
                    function showHide01(m, objname, n) {
                        for (var i = 0; i <= n; i++) {
                            $("#" + objname + i).css('display', 'none');
                        }
                        $("#" + objname + m).css('display', 'block');
                        $("#menu dl").removeClass('curr');
                        $("#" + objname + m).parent().addClass('curr');
                    }
                    $(function() {
                        var height = parseInt($(document).height()) - 81;
                        $("#menu").css('height', height);
                    })
                </script>
                </head>
                <body>
                    <div class="wrap">
                        <div class="header">
                            <div class="bg">
                                <table width="100%" height="80" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="logo">
                                        
                                        <div class="logowrap clearfix">
                    <span class="logoTit">
                        <h1><?php echo Yii::t('partnerModule.home', '小微企业联盟商家平台'); ?></h1>
                        <p> &nbsp;SKU seller platform</p>
                    </span>
                </div>
                                        
                                        
                                        </td>
                                        <td class="td_quickmenu">
                                            <div class="admin clearfix" style="width:auto;">
                                                <b class="b1">
                                                    <img src="<?php echo ATTR_DOMAIN. '/' . (isset($this->partnerInfo['head'])?$this->partnerInfo['head']:'') ?>" style="width:38px;height:38px;" />
                                                </b>
                                                <div class="info_wrap">
                                                    <div class="welcome" style="width:auto;height: 20px;overflow: hidden">
                                                        <?php echo Yii::t('partnerModule.page', '欢迎您，'); ?>
                                                        <span><?php echo $this->getUser()->getState('gai_number'); ?></span>
                                                    </div>
                                                    <span class="info clearfix">
                                                        <?php echo CHtml::link(Yii::t('partnerModule.page', '退出登录'), array('/partner/home/logout')); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="main clearfix">
                            <div class="menu" id="menu">
                                <div class="menu_wrap">
                                    <?php
                                    $menus = include(Yii::getPathOfAlias('application') . DS . 'config' . DS . 'partnerMenu.php');
                                    ?>
                                    <?php $i = 0; ?>
                                    <?php foreach ($menus as $k => $menu): ?>

                                        <?php
//                                         if ($k=='fmManage' && empty($this->fresh_machine_line) && empty($this->fresh_machine_list)) {				//判断生鲜机权限
//                                         	$this->fresh_machine_line = empty($this->fresh_machine_line)?FreshMachine::getLineByPartnerId($this->partner_id):$this->fresh_machine_line;
// 											$this->fresh_machine_list = empty($this->fresh_machine_list)?FreshMachine::getListByPartnerId($this->partner_id):$this->fresh_machine_list;
//                                         }
                                        
                                        if ($this->getParam('onlyTest')=='testLine2') {
                                        	var_dump($this->fresh_machine_line,$this->fresh_machine_list);
                                        	exit();
                                        }
                                         
                                         if ($k=='fmManage' && empty($this->fresh_machine_line) && empty($this->fresh_machine_list)) {				//判断生鲜机权限
                                        	continue;
                                        }

                                        ?>
                                        
                                        <?php $showMenu = $this->showMenu($menu['children']); ?>
                                        <dl class="<?php echo $menu['class'] ?> <?php echo $showMenu ? 'curr' : '' ?>">
                                            <dt>
                                                <a onclick="showHide01(<?php echo $i ?>, 'items', 5);" class="on"><?php echo $menu['name'] ?></a>
                                            </dt>
                                            <dd id="items<?php echo $i; ?>" style="display: <?php echo $showMenu ? 'block' : 'none' ?>" >
                                                <ul>
                                                    <?php foreach ($menu['children'] as $val => $url): ?>
                                                        <?php
                                                        $link = is_array($url) ? $url['value'] : $url;
                                                        ?>
                                                        <li>
                                                            <?php 
                                                            $option = array();
                                                            $curr_path ='/'.$this->getModule()->id.'/'. $this->getId().'/'.$this->action->id; 
															if ($curr_path==$link || $this->curr_menu_name==$link) {
																$option = array('style'=>'font-size:14px;font-weight:900;');
															}
                                                            
                                                            echo CHtml::link($val, $this->createAbsoluteUrl($link),$option);
                                                            
                                                            ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </dd>
                                        </dl>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                            <div class="workground">
                                <div class="workground_wrap">
                                    <div class="position">
                                        <?php echo Yii::t('partnerModule.page', '当前位置'); ?>：
                                    
                                        <?php
                                        $this->widget('zii.widgets.CBreadcrumbs', array(
                                            'homeLink' => false,
                                            'links' => $this->breadcrumbs,
                                            'tagName' => 'span',
                                            'inactiveLinkTemplate' => '<b>{label}</b>',
                                            'separator' => ' &gt; ',
                                        ));
                                        ?>
                                        <div style="float:right;color:#000;padding-right: 30px" >
                                            <?php if (isset($this->exportAction) && (isset($this->showExport) && $this->showExport==true )): ?>
                                            <?php $rote = ucwords($this->id) . '.' . ucwords($this->exportAction); ?>
<!--                                            --><?php //if (Yii::app()->user->checkAccess($rote) && isset($this->exportAction)): ?>
                                                <a href="javascript:;" class="regm-sub" style="color:#fff" onclick="showExport()"><?php echo Yii::t('main', '导出excel'); ?></a>
<!--                                                --><?php //endif; ?>
                                            <?php endif; ?></div>



                                    </div>
                                    <div class="mainContent">
                                        <?php echo $content; ?>
                                        <script>
                    //给普通超链接添加颜色
                    $(".mainContent a").each(function() {
                        var that = $(this);
                        if (that.find('span').length == 0 && that.attr('class') == undefined && !that.parent().hasClass('selected')) {
                            that.css('color', '#3366CC');
                        }
                    });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--弹窗提示-->
                    <?php $this->renderPartial('/layouts/_msg'); ?>
                    <?php
                    $tips = isset($this->storeId) ? Design::getTipsStatus($this->storeId) : '';
                    ?>
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

                    </script>
                </body>
                </html>