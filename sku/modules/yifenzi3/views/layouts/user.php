<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo $this->pageTitle;?></title>
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
</head>
<style type="text/css">
    .unLoginConfirm.active{
        animation:fBottom 0.5s linear 1;
        -webkit-animation:fBottom 0.5s linear 1;
        -webkit-animation-fill-mode:forwards;
        animation-fill-mode:forwards;
    }
    @keyframes fBottom{
        from{
            transform:translateY(0px);
        }
        to{
            transform:translateY(-100px);
        }
    }

    @-webkit-keyframes fBottom{
        from{
            -webkit-transform:translateY(0px);
        }
        to{
            -webkit-transform:translateY(-100px);
        }
    }
    .userInfo p{display: inline-block; float: left; height: 50px; padding-top: 10px; line-height: normal;}
</style>
<body>
<header class="normal">
    <h2><?php echo $this->title;?></h2>
    <?php if(!empty($this->parentPage)):?><a href="<?php echo $this->createUrl($this->parentPage)?>" class="goback_btn"></a><?php endif;?>
    <?php if(!empty($this->topActionName)):?><a href="javascript:$('#address-form').submit()" class="save_btn"><?php echo $this->topActionName?></a><?php endif;?>
</header>
<?php echo $content; ?>
<?php echo $this->renderPartial('/layouts/_footer')?>
<script>
    //导航点击切换
    $("#guide").find("a").click(function(){
        $("#guide a").removeClass("active");
        $(this).addClass("active");
    })
</script>
</body>
<script type="text/javascript">
    $(function(){
        var oHeight = $('body').height();
        $('.floatLayout').css({'height':oHeight});
        $('.unLoginBtn a').click(function(){
            $('.floatLayout').show();
            $('footer').hide();
            $('.unLoginConfirm').show().addClass('active');
        })

        $('.cancel').click(function(){
            $('.floatLayout').hide();
            $('footer').show();
            $('.unLoginConfirm').hide().removeClass('active');
        })
    })
</script>
</html>