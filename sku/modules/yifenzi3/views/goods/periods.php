<header class="periods_title">
    <h2>期数</h2>
    <a href="javascript:history.go(-1);" class="goback_btn"></a>
</header>
<div id="wrapper" style="overflow:visible">
    <div id="scroller">
        <div class="periods_box">
            <div class="periods_list">
                <!------------------------------------数据插入位置---------------------------------->
                <!-- <div class="active"><a href="join-result.html">10131</a></div>
                <div class=""><a href="join-result.html">10130</a></div>
                <div class=""><a href="join-result.html">10129</a></div>
                <div class=""><a href="join-result.html">10128</a></div> -->
                <div class="clearfix"></div>
            </div>
            <p class="pullLoad">往下滚动读取更多......</p>
            <div class="pullUp"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/yifenzi3/js/iscroll-probe.js"></script>

<script type="text/javascript">

    var items_per_page = 10;
    var scroll_in_progress = false;//滚动过程
    var myScroll;
    var fmNum = <?php echo $model->current_nper ?>;
    var content = '';
    var fmNumCache = 0;
    /*控制加载条数*/
    var deviceHeight = $(window).height();
    var deviceWidth = $(window).width();
    var remainHeight = deviceHeight - $('header').height();
    var refreshAmount = (parseInt(remainHeight / (deviceWidth * 0.92 / 4 + 36)) * 8);//加载条数
    var periods;
    /*插入位置*/
    var $insertLoca = $('.periods_list > .clearfix');

    function pad(num, n) {
        return Array(n > num ? (n - ('' + num).length + 1) : 0).join(0) + num;
    }
    load_content = function (refresh) {
        content = '';
        setTimeout(function () { // 插入数据（定时器模拟延迟）
            var fmLen = fmNum;
            fmNumCache = fmNum - refreshAmount;
            if (!refresh) {//第一次插入-----根据高度判断插入数
                /*var fmLen = fmNum;
                 fmNumCache = fmNum-refreshAmount;*/
                if (fmNumCache < 0) {
                    for (var i = 0; i < fmLen; i++) {
                        periods = fmNum--;
                        content += '<div><a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/view',array('id'=>$model->goods_id));?>?nper='+periods+'">' + periods + '</a></div>';
                        $('.pullLoad').html('');
                    }
                }
                else {
                    for (var i = 0; i < refreshAmount; i++) {
                        periods = fmNum--;
                        content += '<div><a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/view',array('id'=>$model->goods_id));?>?nper='+periods+'">' + periods + '</a></div>'
                    }
                }
                $insertLoca.before(content);

            } else if (refresh) {

                if (fmNum <= 0) {
                    loadNoMore()
                }
                else if (fmNumCache <= 0) {
                    for (var i = 0; i < fmLen; i++) {
                        periods = fmNum--;
                        content += '<div><a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/view',array('id'=>$model->goods_id));?>?nper='+periods+'">' + periods + '</a></div>';
                    }
                    loadNoMore();
                    $insertLoca.before(content);
                }
                else {
                    for (var i = 0; i < refreshAmount; i++) {
                        periods = fmNum--;
                        content += '<div><a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/view',array('id'=>$model->goods_id));?>?nper='+periods+'">' + periods + '</a></div>'
                    }
                    $insertLoca.before(content);
                }
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
            //点击切换
            $(".periods_box .periods_list a").click(function () {
                $(".periods_box .periods_list>div").removeClass("active");
                $(this).parent().addClass("active");
            })
        }, 1000);

    };

    function pullUpAction(callback) {
        //如果无数据
        if ($('.periods_list div').length >= 100) {//最多100条
            loadNoMore();
        }
        load_content(refreshAmount);
        if (callback) {
            callback();
        }
    }

    function loadNoMore() {
        $('.pullLoad').html('目前暂无更多数据.....');
        setTimeout(function () {
            $('.pullUp').html('').hide();
        }, 500);
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
            if (count) {
                pullActionDetect.count = 0;
            }
            //延迟出现提示语句
            setTimeout(function () {
                if (myScroll.y <= (myScroll.maxScrollY + 200) && pullUpEl && !pullUpEl.className.match('loading')) {
                    $('.pullUp').html('<span class="pullUpLabel">正在加载数据...</span>');
                    pullUpAction();
                } else if (pullActionDetect.count < pullActionDetect.limit) {
                    pullActionDetect.check();
                    pullActionDetect.count++;
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

            if ($('#wrapper .periods_list > div').length >= items_per_page) {
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

    $(function () {
        load_content();
    })

    document.addEventListener('touchmove', function (e) {
        e.preventDefault();
    }, false);

</script>