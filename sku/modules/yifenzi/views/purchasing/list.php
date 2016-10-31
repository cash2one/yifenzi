<style type="text/css">
    /*#wrapper {top: 95px;}*/
    .pullUp,.pullLoad{text-align: center;font-size: 14px;color: #999999;}
</style>
<script type="text/javascript" src="/yifenzi/js/iscroll-probe.js"></script>
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
                foreach ($lists as $k => $an) {
                    $content .= '<li><a href="' . Yii::app()->createUrl('/yifenzi/goods/view', array('id' => $an['goods_id'],'nper'=>$an['current_nper'])) . '"><img src="' . ATTR_DOMAIN . '/' . $an['goods_thumb'] . '"></a>' .
                        '<div class="right">' .
                        '<p class="name"><span>[第' . $an['current_nper'] . '期]</span><a href="' . Yii::app()->createUrl('/yifenzi/goods/view', array('id' => $an['goods_id'],'nper'=>$an['current_nper'])) . '">' . $an['goods_name'] . '</a></p>' .
                        '<p class="count">价值：￥' . $an['shop_price'] . '</p>' .
                        '<p class="card addTap" onclick="addGoods('.$an['goods_id'].')"></p>' .
                        '<div class="speedBox"><p class="speedbg"><span class="speed"><span class="speedIng" style="width:' . number_format($an['salesTotal'] / ($an['shop_price'] / $an['single_price']),2)*100 . '%"><i></i></span></span></p>' .
                        '<p class="spengBottom">' . $an['salesTotal'] . '<span>' . ceil($an['shop_price'] / $an['single_price']) . '</span></p></div></div></li>';
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
                        async += '<li><a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i]['goods_id']
          					+'&nper='+product[i]['current_nper']+'"><img src="<?php echo ATTR_DOMAIN ?>/' + product[i]['goods_thumb'] + '"></a>' +
                            '<div class="right">' +
                            '<p class="name"><span>[第' + product[i]['current_nper'] + '期]</span><a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i]['goods_id']
          					+'&nper='+product[i]['current_nper']+'">' + product[i]['goods_name'] + '</a></p>' +
                            '<p class="count">价值：￥' + product[i]['shop_price'] + '</p>' +
                            '<p class="card addTap"></p>' +
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
</head>

<style type="text/css">
    .container{top: 100px;}
    .list-group.active li:first-child{width: 96%;position: fixed; top: 96px; background-color: #fff;}
    .list-group.active li:nth-child(2){margin-top: 60px;}
</style>
<body>
<header class="normal">
    <div class="head-wrap">
        <h2><?php echo $this->pageTitle ?></h2>
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
<div id="wrapper">
    <div id="scroller">
        <div class="newList">
            <ul class="allShow">
                <!--产品处-->
                <div class="clearfix"></div>
            </ul>
            <p class="pullLoad">往下滚动读取更多......</p>
            <div class="pullUp"></div>
        </div>
    </div>
</div>
<div class="height54"></div>
<footer>
    <nav id="guide">
        <a href="/" class="home<?php if($this->footerPage == 1):?> active<?php endif;?>">首页</a>
        <a href="<?php echo $this->createUrl('goods/list');?>" class="product<?php if($this->footerPage == 2):?> active<?php endif;?>">所有商品</a>
        <a href="<?php echo $this->createUrl('goods/announced');?>" class="announce<?php if($this->footerPage == 3):?> active<?php endif;?>">即将揭晓</a>
        <a href="<?php echo $this->createUrl('carts/index');?>" class="cart<?php if($this->footerPage == 4):?> active<?php endif;?>"><i class="print">0</i>购物车</a>
        <a href="<?php echo $this->createUrl('user/index');?>" class="user<?php if($this->footerPage == 5):?> active<?php endif;?>">个人中心</a>
    </nav>
</footer>
<script>
    var remainHeight = $(window).height() - $('.header').height()-$('.h60').height()-$('.tab').height();
    //导航点击切换
    $("#guide").find("a").click(function () {
        $("#guide a").removeClass("active");
        $(this).addClass("active");
    })
    $('.tabItem').tap(function(){
        $(this).addClass('active').siblings().removeClass('active');
        var index = $(this).index();
        $('.list-group').eq(index).addClass('active').siblings().removeClass('active');
        var h=$('.list-group').eq(index).height()+50;
        //$(".container").css("height",h);
        h<remainHeight ? h=remainHeight:h=h;
        $(".container").css("overflow","hidden");
        $(".container").css({'height':h});
        $(".setting").css({'height':h,'display':'block'});
    })
    $('.list-group li').click(function(){
        $(this).parent().removeClass('active');
        $(".container").css("height","");
        $(".container").css("overflow","");
        $(".setting").css("display","none");
    })
    //显示隐藏导航栏
    $("#search_show").click(function () {
        $(".head-wrap").addClass("search_show");
    })
    $("#search_cancel").click(function () {
        $(".head-wrap").removeClass("search_show");
    })

    function addGoods(goods_id,types){
        var types = types;
        if ( !goods_id ) return false;
        var reg = new RegExp("^[0-9]*$");
        if(!reg.test(goods_id)) return false;

        $.getJSON("/carts/ajaxadd?goods_id="+goods_id, function(json){
            if (types == 'link'){
                window.location.href = '/carts';
            }
            if (json.status == 2 || json.status == '2'){
                $('body').addTips();
            }else{
                $('body').addTips({bool:0});
            }
            lodingCartNum();
        });
    }

    function lodingCartNum(){
        $.ajax({
            type:"post",
            url:'/carts/getcartnums',
// 		dataType:"json",
            data:{'YII_CSRF_TOKEN':"<?php echo Yii::app()->request->csrfToken?>"},
            success:function(data){
                console.info(data);
                if (data){
                    $(".print").html(data);
                }else{
                    $(".print").html(0);
                }

            }
        });
    }
    lodingCartNum();
</script>