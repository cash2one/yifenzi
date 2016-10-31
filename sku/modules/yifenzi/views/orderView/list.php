<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>一份子展示页</title>
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl?>/yifenzi/css/common_m.css">
    <script src="<?php echo Yii::app()->baseUrl?>/yifenzi/js/jquery-2.1.4.min.js" type="text/javascript" charset="utf-8"></script>
</head>
<script>
    (function () {
        $('.pcphoneJudgment .on').html('555555');
    })(jQuery);
</script>
<body>
<div class="main">
    <!-- <div class="wrap_left">
        <div class="phone_projection">
            <div class="phone_bg">

            </div>
        </div>
    </div> -->
    <div class="wrap_right">
        <div class="step-1">
            <h2 id="goodsName">请选择商品</h2>
            <div class="progress progress_info">
                <span class="fl">已参与</span>
                <span class="fr">剩余</span>
                <div class="clear"></div>
            </div>
            <div class="progress_bar">
                <div class="bar back_bar"></div>
                <div class="bar front_bar"></div>
                <div class="bar_icon"></div>
            </div>
            <div class="progress progress_info">
                <span class="fl cur_share">0</span>
                <span class="fr end_share">0</span>
                <div class="clear"></div>
            </div>
            <div class="count_down"></div>
        </div>
        <div class="step-2">
            <p class="info">幸运份子</p>
            <p class="luckynumber">123456</p>
        </div>
        <div class="item step-3">
            <?php if(!empty($lists)):?>
                <?php foreach($lists as $k=>$v):
                    $total = ceil($v['shop_price'] / $v['single_price']);?>
            <div id="<?php echo $v['goods_id'].'_'.$v['current_nper'].'_'.$total?>" onclick="toOrder(this)" class="item_<?php echo $k+1;?>"><a href="javascript:void(0)"><?php echo $v['goods_name'];?></a></div>
                <?php endforeach;?>
            <div class="clear"></div>
            <?php endif;?>
        </div>
    </div>
</div>
<script type="text/javascript">
    var idList = '';
    var sumlotterytime = 0;
    function toOrder(obj){
        idList = obj.id;
        var goodsInfo = idList.split("_");
        $('#goodsName').html('[第'+goodsInfo[1]+'期]'+obj.firstChild.innerHTML);
    }

    /*布局尺寸以设计稿为基准等比例设置:基于--[rem]*/
    (function () {
        document.addEventListener('DOMContentLoaded', function () {
            var html = document.documentElement;
            var windowWidth = html.clientWidth;
            if(windowWidth>2700){
                windowWidth=2700;
            }
            html.style.fontSize = windowWidth / 27 + 'px';
        }, false);
    })();
    $(function(){
        //调整手机背景图高度
        var docHeight = 0;
        function adjustHeight(){
            docHeight = $(document).height();
            $('.phone_projection').css({height:docHeight});
        }
        adjustHeight();
        $(window).resize(function(){
            adjustHeight();
        })


        //模拟进度条
        var cur_share = 0;	//当前数量
        var end_share = parseInt($('.end_share').html());	//总数量
         var timer1 = setInterval(function(){
         var cur_share = parseInt($('.cur_share').html());
             if(end_share != 0) {
                 if (cur_share >= end_share) {
                     $('.end_share').html(0);
                     $('.front_bar').css({'width': 1 * 100 + '%'});
                     $('.bar_icon').css({'left': "calc(" + 1 * 100 + '%' + " - 20px)"})
                     clearInterval(timer1);
                     clearInterval(timer3);
                     $('.step-3').hide();
                     $('.count_down').show();
                     //模拟倒计时
                     var sec = sumlotterytime - 2;	//默认倒计时---sec秒
                     $('.count_down').html(sec);
                     var timer2 = setInterval(function () {
                         if (sec <= 0) {
                             clearInterval(timer2);
                             $('.step-1').hide();
                             $('.step-2').show();
                         }
                         else {
                             sec--;
                             $('.count_down').html(sec);
                         }

                     }, 1000)
                 }
                 else {
                     // cur_share ++;
                     if((end_share - cur_share) != $('.end_share').html())
                        $('.end_share').html(end_share - cur_share);
                     var percent = cur_share / end_share;	//购买百分比
                     $('.front_bar').css({'width': percent * 100 + '%'});
                     $('.bar_icon').css({'left': "calc(" + percent * 100 + '%' + " - 20px)"})
                 }
             }
         },100);



        //clearInterval(timer3);
        var timer3 = setInterval(function () {
            if (idList !== '') {
                $.ajax({
                    type: "post",
                    url: "/orderView/GetOrder",
                    data: {'YII_CSRF_TOKEN': "<?php echo Yii::app()->request->csrfToken?>", "idList": idList},
                    dataType: "json",
                    success: function (data) {
                        if (end_share != data.end_share) {
                            $('.end_share').html(data.end_share);
                            end_share = data.end_share;
                        }
                        if ($('.cur_share').html() != data.cur_share)
                            $('.cur_share').html(data.cur_share);
                        if(data.luckynumber != 0){
                            $('.luckynumber').html(data.luckynumber);
                        }
                        if(data.sumlotterytime != 0)
                            sumlotterytime = data.sumlotterytime;
                    }
                });
            }
        }, 1000)
    });
    $(document).ready(function(){
        $('.pcphoneJudgment').css('display','none');
    });

</script>
</body>
</html>