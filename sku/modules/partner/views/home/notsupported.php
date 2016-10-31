<!--[if !IE]><!--><script>window.location.href="<?php echo DOMAIN;?>"</script><!--<![endif]-->
<!--[if gte IE 8]><script>window.location.href="<?php echo DOMAIN;?>"</script><![endif]-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo Yii::t('partnerModule.home', '升级您的浏览器 -- 盖网卖家平台'); ?></title>
    <style>
        html,body{
            height:100%;
            padding:0;
            margin:0;
        }
        body {
            background:#fff;
            font-family:宋体;
        }
        p {
            font-family:黑体;
            letter-spacing:1px;
            color:#999;
            font-size:20px;
        }
        dl,dt,dd {
            margin:0;
            padding:0;
        }
        dl {
            float:left;
            padding:10px;
        }
        dd {
            padding:5px;
            letter-spacing:1px;
            font-size:13px;
            font-family:Tahoma;
        }
        img {
            border:none;
        }
        div {
            width:645px;
            height:140px;
            margin:0 auto;
            text-align:center;
        }
        a {
            color:#06f;
            text-decoration:none;
        }
        a:hover {
            text-decoration:underline;
        }

    </style>
</head>
<body>

<table border="0" width="100%" height="95%">
    <tr>
        <td style="text-align:center;font-size:12px;color:#666;font-size:16px;">
            <h1><?php echo Yii::t('partnerModule.home', '盖网卖家平台'); ?></h1>
            <p><?php echo Yii::t('partnerModule.home', '您使用的浏览器版本过低，卖家平台已不再支持IE6/7，推荐升级到以下浏览器'); ?></p>
            <div>
                <dl>
                    <dt><a href="http://www.google.cn/intl/zh-CN/chrome/browser/" target="_blank">
                            <img src="<?php echo DOMAIN ?>/images/bg/chrome.jpg" alt="<?php echo Yii::t('partnerModule.home', '谷歌浏览器'); ?>" />
                        </a>
                    </dt>
                    <dd>Chrome</dd>
                </dl>
                <dl>
                    <dt><a href="http://firefox.com.cn/download/" target="_blank">
                            <img src="<?php echo DOMAIN ?>/images/bg/firefox.png" alt="<?php echo Yii::t('partnerModule.home', '火狐浏览器'); ?>" />
                        </a>
                    </dt>
                    <dd>Firefox</dd>
                </dl>
                <dl>
                    <dt><a href="http://windows.microsoft.com/zh-cn/internet-explorer/download-ie" target="_blank">
                            <img src="<?php echo DOMAIN ?>/images/bg/IE.png" alt="<?php echo Yii::t('partnerModule.home', 'IE浏览器'); ?>" /></a>
                    </dt>
                    <dd>Internet Explorer 8+</dd>
                </dl>
                <dl>
                    <dt><a href="http://support.apple.com/downloads/#internet" target="_blank">
                            <img src="<?php echo DOMAIN ?>/images/bg/safari.jpg" alt="<?php echo Yii::t('partnerModule.home', '苹果浏览器'); ?>" /></a>
                    </dt>
                    <dd>Safari</dd>
                </dl>
                <dl>
                    <dt><a href="http://www.opera.com/zh-cn/computer/windows" target="_blank">
                            <img src="<?php echo DOMAIN ?>/images/bg/opera.jpg" alt="<?php echo Yii::t('partnerModule.home', '欧鹏浏览器'); ?>" /></a>
                    </dt>
                    <dd>Opera</dd>
                </dl>
            </div>
            <p style="clear:both;font-size:14px;font-weight:normal;font-family:宋体;color:#666;"><?php echo Yii::t('partnerModule.home', '注意：如果使用的是360之类的双核浏览器，请切换到极速模式即可'); ?></p>
        </td>
    </tr>
</table>


</body>
</html>