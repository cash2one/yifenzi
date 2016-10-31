<script type="text/javascript" src="/yifenzi/js/iscroll-probe.js"></script>
<?php
//    var_dump(Yii::app()->request);exit;
//    var_dump(get_class_methods(Yii::app()->request));exit;
?>
<header class="normal">
    <div class="head-wrap">
        <h2>限购专区</h2>
        <a href="javascript:history.go(-1);" class="goback_btn"></a>
        <a href="#" class="search_btn" id="search_show"></a>
        <div class="search_bar">
            <div class="search_input">
                <input type="text" placeholder="请输入商品名称"></div>
            <div class="search_btn_group">
                <a href="javascript:;">搜索</a>
                <a href="javascript:;" id="search_cancel">取消</a>
            </div>
        </div>
    </div>
</header>
<div class="container">
<div id="wrapper">
    <div id="scroller">
        <div class="warpbg">
            <div class="newList">
                <ul>
                    <!--            <li>
                                        <a href="#"><img src="images/prc_88x88.png"></a>
                                        <div class="right">
                                            <p class="name"><a href="#">华为Mate8</a></p>
                                            <p class="count"><span>3GB+32GB</span><br>版价值：￥3488.00</p>
                                            <p class="card"></p>
                                            <p class="spengTop">已参与<span>剩余</span></p>
                                            <p class="speedbg"><span class="speed"><span class="speedIng" style="width:40%"><i></i></span></span></p>
                                            <p class="spengBottom">640<span>2848</span></p>
                                        </div>
                                    </li> -->
                    <!------------------------------------数据插入位置---------------------------------->
                    <div class="clearfix"></div>
                </ul>
                <p class="pullLoad">往下滚动读取更多......</p>
                <div class="pullUp"></div>
            </div>
            <div class="h60"></div>
        </div>
    </div>
</div>
</div>
<script>
//导航点击切换
    $("#guide").find("a").click(function () {
        $("#guide a").removeClass("active");
        $(this).addClass("active");
    })
//显示隐藏导航栏
    $("#search_show").click(function () {
        $(".head-wrap").addClass("search_show");
    })
    $("#search_cancel").click(function () {
        $(".head-wrap").removeClass("search_show");
    })
</script>
<script type="text/javascript">

    var items_per_page = 10;
    var scroll_in_progress = false;//滚动过程
    var myScroll;
    var refreshAmount = <?php echo $limit ?>;//加载条数
//    var fmNum = 10000789;
    var content = '', async = '';
    var flag = false; //判断有没数据 返回
    var next_page = 1;//下一页
    load_content = function (refresh, next_page) {
        setTimeout(function () { // 插入数据
            if (!refresh) {//第一次插入-----根据高度判断插入数
                var deviceHeight = $(window).height();
                var remainHeight = deviceHeight - $('header').height() - $('.h60').height();
<?php
$content = '';
foreach ($limits as $k => $an) {
    $content .= '<li><a href="' . Yii::app()->createUrl('/yifenzi/goods/view', array('id' => $an['goods_id'],'nper'=>$an['current_nper'])) . '"><img src="' . ATTR_DOMAIN . '/' . $an['goods_thumb'] . '"></a>' .
            '<div class="right">' .
            '<p class="name"><span>[第' . $an['current_nper'] . '期]</span><a href="' . Yii::app()->createUrl('/yifenzi/goods/view', array('id' => $an['goods_id'],'nper'=>$an['current_nper'])) . '">' . $an['goods_name'] . '</a></p>' .
            '<p class="count">价值：￥' . $an['shop_price'] . '</p>' .
            '<p class="card"></p>' .
            '<div class="speedBox"><p class="speedbg"><span class="speed"><span class="speedIng" style="width:' . $an['salesTotal'] / number_format(ceil($an['shop_price'] / $an['single_price']), 2) . '%"><i></i></span></span></p>' .
            '<p class="spengBottom">' . $an['salesTotal'] . '<span>' . number_format(ceil($an['shop_price'] / $an['single_price']), 2) . '</span></p></div></div></li>';
}
?>
                content += '<?php echo $content; ?>';
                $('.newList .clearfix').before(content);

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
        refresh = 10;//默认插入20条
        //如果无数据
//        if ($('.newList li').length >= 100) {//最多100条
//            refresh = 0;
//            $('.pullLoad').html('目前暂无更多数据.....');
//            setTimeout(function () {
//                $('.pullUp').html('').hide();
//            }, 500);
//            return false;
//        }
        if (flag) {
            $('.pullLoad').html('目前暂无更多数据.....');
            setTimeout(function () {
                $('.pullUp').html('').hide();
            }, 500);
            return false;
        }
        load_content(refresh, next_page += 1);
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
    $(function () {
        load_content();
    })

    document.addEventListener('touchmove', function (e) {
        e.preventDefault();
    }, false);
    ///获取限购产品
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
                    var product = data.data;
                    for (var i = 0, len = product.length; i < len; i++) {
                        async += '<li><a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id+'&nper='+product[i].current_nper+'"><img src="<?php echo ATTR_DOMAIN ?>/' + product[i]['goods_thumb'] + '"></a>' +
                                '<div class="right">' +
                                '<p class="name"><span>[第' + product[i]['current_nper'] + '期]</span><a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id+'&nper='+product[i].current_nper+'">' + product[i]['goods_name'] + '</a></p>' +
                                '<p class="count">价值：￥' + product[i]['shop_price'] + '</p>' +
                                '<p class="card"></p>' +
                                '<div class="speedBox"><p class="speedbg"><span class="speed"><span class="speedIng" style="width:' + product[i]['salesTotal'] / (product[i]['shop_price'] / product[i]['single_price']) + '%"><i></i></span></span></p>' +
                                '<p class="spengBottom">' + product[i]['salesTotal'] + '<span>' + Math.ceil(product[i]['shop_price'] / product[i]['single_price']) + '</span></p></div></div></li>';
                    }
                } else {
                    flag = true;
                    $('.pullLoad').html('目前暂无更多数据.....');
                }
            }
        })
    }
</script>