<?php
Yii::app()->ClientScript->registerCssFile(Yii::app()->baseUrl . '/yifenzi/css/swiper.css')
?>
<script type="text/javascript" src="/yifenzi/js/iscroll-probe.js"></script>
<script type="text/javascript" src="/yifenzi/js/jweixin-1.0.0.js"></script>
<header class="announce">
    <h2>最新揭晓</h2>
    <a href="javascript:history.go(-1);" class="goback_btn"></a>
    <!--<a href="javascript:void(0)" class="classify">全部分类<span></span></a>-->
</header>
<style type="text/css">
    .list-group.active li:first-child{width: 96%;position: fixed; top: 44px; background-color: #fff;}
    .list-group.active li:nth-child(2){margin-top: 104px;}
</style>
<div class="container noRelative">
    <div class="warpbg">
        <?php $this->renderPartial('/layouts/_column')?>
    </div>
    <div class="setting"></div>
    <div id="wrapper">
        <div id="scroller">
            <div class="newList">
                <ul class="data">
                    <!--                    <li>
                                            <a class="imgbox" href="detail-ing.html"><img src="images/prc_88x88.png"></a>
                                            <div class="right">
                                                <p class="name"><span>[第27期]</span><a href="detail.html">华为Mate8华为Mate8Mate8华为Mate8华为Mate8</a></p>
                                                <p class="time" date="2017/4/31 00:20:10"><span id="time_h">01</span>:<span id="time_m">18</span>:<span id="time_s">16</span></p>
                                                <p class="count max">价值：<br>￥3488.00</p>
                                            </div>
                                        </li>-->
                    <!------------------------------------数据插入位置---------------------------------->
                    <div class="clearfix"></div>
                </ul>
                <?php
                    if(!$announce){?>


                        <!--<p class="pullLoad">暂无数据</p>-->
                    <?php }else{?>
                        <p class="pullLoad">往下滚动读取更多......</p>
                    <?php } ?>

                <div class="pullUp"></div>

                <div class="h60"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.classify').tap(function(){
            $('.list-group').addClass('active')
            var h=$('.list-group').height();
            $(".container").css("height",h);
            $(".container").css("overflow","hidden");
            $(".setting").css("display","block");
        });
       $('.list-group li').click(function(){
            $(this).parent().removeClass('active');
            $(".container").css("height","");
            $(".container").css("overflow","");
            $(".setting").css("display","none");
        })
</script>
<script type="text/javascript">
    var items_per_page = 10;
    var scroll_in_progress = false;//滚动过程
    var myScroll;
    var refreshAmount = 10;//加载条数
    //var fmNum = 10000789;
    var content = '', async = '';
    var flag = false;
    var next_page = 1;//下一页

    load_content = function (refresh, next_page) {

        content = '';
        setTimeout(function () { // 插入数据
            if (!refresh) {//第一次插入-----根据高度判断插入数

                if (<?php echo count($announce)?>){
                    var deviceHeight = $(window).height();
                    var remainHeight = deviceHeight;
                    <?php
                    $content = '';
                    $time = 0; //得到最大的id
                    // 第一次加载 显示揭晓 列表
                    foreach ($announce as $an) {
                        $time = ($time < strtotime($an['sumlotterytime']) && $time) ? $time : strtotime($an['sumlotterytime']);
                        $content .= '<li nperid=' . $an['id'] . '><a class="imgbox" href="' . Yii::app()->createUrl('/yifenzi/goods/view', array('id' => $an['goods_id'],'nper'=>$an['current_nper'])) . '"><img src="' . $an['thumb'] . '"></a><div class="right">' .
                            '<p class="name"><span>[第' . $an['current_nper'] . '期]</span><a href="' . Yii::app()->createUrl('/yifenzi/goods/view', array('id' => $an['goods_id'],'nper'=>$an['current_nper'])) . '">' . $an['name'] . '</a></p>';
                        if (strtotime($an['sumlotterytime']) < time())
                            $content .= '<p class="time end" date="' . $an['sumlotterytime'] . '">已揭晓</p>';
                        else
                            $content .='<p class="time" date="' . $an['sumlotterytime'] . '"><span id="time_h">01</span>:<span id="time_m">18</span>:<span id="time_s">16</span></p>';

                        $content .= '<p class="count max">价值：<br>￥' . number_format($an['price'], 2) . '</p></div></li>';
                    }
                    ?>
                    content = '<?php echo $content; ?>';
                    $('.newList .clearfix').before(content);
                }
                else{
                    noDataTips();
                }


            } else if (refresh && next_page) {
                refreshAmount = refresh;
                getProduct(next_page);
                if (!flag)
                    $('.newList .clearfix').before(async);
            }

            if (refresh) {
                myScroll.refresh();
                pullActionCallback();

            } else {
                if (myScroll) {
                    myScroll.destroy();
                    $(myScroll.scroller).attr('style', '');
                    myScroll = null;
                }
                trigger_myScroll();
            }
        }, 1000);

    };

    function pullUpAction(callback) {
        refresh = 10;//默认插入10条
        //如果无数据
        if (flag) {//最多100条
            refresh = 0;
            $('.pullLoad').html('目前暂无更多数据.....');
            setTimeout(function () {
                $('.pullUp').html('').hide();
            }, 500);
            return false;
        } else {
            load_content(refresh, next_page += 1);
        }
        if (callback) {
            callback();
        }
    }
    function pullActionCallback() {
        //延迟提示语消失时间
        if (pullUpEl && pullUpEl.className.match('pullUp')) {
            setTimeout(function () {
                $('.pullUp').html('');
            }, 200);

        }
    }

    var pullActionDetect = {
        count: 0,
        limit: 10,
        check: function (count) {
            //延迟出现提示语句
            setTimeout(function () {
                if (myScroll.y <= (myScroll.maxScrollY + 200) && pullUpEl && !pullUpEl.className.match('loading')) {
                    $('.pullUp').html('<span class="pullUpLabel">正在加载数据...</span>');
                    pullUpAction();
                }
            }, 500);
        }
    }

    function trigger_myScroll(offset) {
        pullDownEl = document.querySelector('#wrapper .pullDown');
        if (pullDownEl) {
            pullDownOffset = pullDownEl.offsetHeight;
        } else {
            pullDownOffset = 0;
        }
        pullUpEl = document.querySelector('#wrapper .pullUp');
        if (pullUpEl) {
            pullUpOffset = pullUpEl.offsetHeight;
        } else {
            pullUpOffset = 0;
        }

        myScroll = new IScroll('#wrapper', {
            probeType: 1,
            tap: true,
            click: false,
            preventDefaultException: {tagName: /.*/},
            mouseWheel: true,
            scrollbars: true,
            fadeScrollbars: true,
            interactiveScrollbars: false,
            keyBindings: false,
            deceleration: 0.0002,
            //startY: (parseInt(offset) * (-1))
        });

        myScroll.on('scrollStart', function () {
            scroll_in_progress = true;
        });
        myScroll.on('scroll', function () {
            scroll_in_progress = true;
            if ($('#wrapper ul > li').length >= items_per_page) {
                pullActionDetect.check(0);
            }
        });
        myScroll.on('scrollEnd', function () {
            setTimeout(function () {
                scroll_in_progress = false;
            }, 100);
        });
        // In order to prevent seeing the "pull down to refresh" before the iScoll is trigger - the wrapper is located at left:-9999px and returned to left:0 after the iScoll is initiated
        setTimeout(function () {
            $('#wrapper').css({left: 0});
        }, 100);
    }

    function getProduct(next_page)
    {
        $.ajax({
            type: 'get',
            datetype: 'json',
            cache: false,
            async: false,
            url: '<?php echo preg_replace('/\&page=\d{0,}|\?page=\d{0,}/', '', Yii::app()->request->url) ?>',
            data: {page: next_page},
            success: function (data) {
                data = eval("(" + data + ")");
                if (data.result) {
                    announce(data);
                } else {
                    flag = true;
                    $('.pullLoad').html('目前暂无更多数据.....');
                }
            }
        })
    }

    $(function () {
        load_content();
    })


    var time = <?php echo $time;?>;
    $(function () {
        (function longPolling() {
            $.ajax({
                url: "<?php echo Yii::app()->createUrl('/yifenzi/goods/send')?>",
                data: {time: time,YII_CSRF_TOKEN:'<?php echo Yii::app()->request->getCsrfToken();?>'},
                dataType: "json",
                type:'post',
                timeout: 5000,
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    if (textStatus == "timeout") { // 请求超时
                        longPolling(); // 递归调用
                        // 其他错误，如网络错误等
                    } else {
                        longPolling();
                    }
                },
                success: function (data, textStatus) {
                    if (textStatus == "success") { // 请求成功
                        async = '';
                        if(data.success)
                            announce(data);
                        $('.newList ul').show(2000).prepend(async);
                        longPolling();
                    }
                }
            });
        })();

    });
    
    function announce(data)
    {
        var product = data.data;
        var time_start = new Date().getTime();
        var time_end;
        async = '';
        for (var i = 0, len = product.length; i < len; i++) {
            async += '<li nperid=' + product[i]['id'] + '><a class="imgbox" href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id
          					+'&nper='+product[i].current_nper+'"><img src="' + product[i]['thumb'] + '"></a><div class="right">' +
                    '<p class="name"><span>[第' + product[i]['current_nper'] + '期]</span><a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id
          					+'&nper='+product[i].current_nper+'"></p>';
            time_end = new Date(product[i]['sumlotterytime']).getTime();
            time = time > (time_end/1000) ? (time_end/1000) : time; 
            if (time_start > time_end)
                async += '<p class="time end" date="' + product[i]['sumlotterytime'] + '">已揭晓</p>';
            else
                async += '<p class="time" date="' + product[i]['sumlotterytime'] + '"><span id="time_h">00</span>:<span id="time_m">00</span>:<span id="time_s">100</span></p>';
            async += '<p class="count max">价值：<br>￥' + product[i]['price'] + '</p></div></li>';
        }
    }
</script>
<!-- 加载 js-->
<?php echo $this->renderPartial('/layouts/_announce') ?> 