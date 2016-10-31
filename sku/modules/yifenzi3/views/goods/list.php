<style type="text/css">
    #wrapper {top: 95px;}
    .pullUp,.pullLoad{text-align: center;font-size: 14px;color: #999999;}
</style>
<script type="text/javascript" src="/yifenzi3/js/picLazyLoad.js"></script>
<script type="text/javascript">

    var items_per_page = 10;
    var scroll_in_progress = false;//滚动过程
    var myScroll;
    var refreshAmount = <?php echo $limit ?>;//加载条数
    //    var fmNum = 10000789;
    var content = '', async = '';
    var flag = false; //判断有没数据 返回
    $(function(){
        //导航点击切换
        var liAmount = <?php echo $limit ?>;  //初始化插入条数
        var i = 0;
        var ohtml = '';
        var content = '';
        var next_page = 1;
        function insertLi(){
            ohtml = '';
            <?php
                  $content = '';
                  foreach ($lists as $k => $an) {
                      $content .= '<li><a href="' . Yii::app()->createUrl('/yifenzi3/goods/view', array('id' => $an['goods_id'],'nper'=>$an['current_nper'])) . '"><img src="' . $an['goods_thumb'] . '"></a>' .
                          '<div class="right">' .
                          '<p class="name"><span>[第' . $an['current_nper'] . '期]</span>' . $an['goods_name'] . '</p>' .
                          '<a href="' . Yii::app()->createUrl('/yifenzi3/goods/view', array('id' => $an['goods_id'],'nper'=>$an['current_nper'])) . '"><p class="count">价值：￥' . $an['shop_price'] . '</p></a>' .
                          '<p class="card addTap" onclick="addGoods('.$an['goods_id'].')"></p>' .
                          '<a href="' . Yii::app()->createUrl('/yifenzi3/goods/view', array('id' => $an['goods_id'],'nper'=>$an['current_nper'])) . '"><div class="speedBox"><p class="speedbg"><span class="speed"><span class="speedIng" style="width:' . number_format($an['salesTotal'] / ($an['shop_price'] / $an['single_price']),2)*100 . '%"><i></i></span></span></p>' .
                          '<p class="spengBottom">' . $an['salesTotal'] . '<span>' . ceil($an['shop_price'] / $an['single_price']) . '</span></p></div></a></div></li>';
                  }
                  ?>
            ohtml += '<?php echo $content; ?>';
            $('.newList .clearfix').before(ohtml);
            $('img').picLazyLoad();
        }
        insertLi();
        var scrollTop = 0;
        var client = $(window).height();
        $(window).scroll(function(){
            scrollTop = $(window).scrollTop()+client;
            if(scrollTop>=$('body').height()){
                next_page++;
                getProduct(next_page);
            }
        })
        //图片懒加载
        $('img').picLazyLoad();
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
                    async = '';
                    for (var i = 0, len = product.length; i < len; i++) {
                        async += '<li><a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id+'&nper='+product[i].current_nper+'"><img src="' + product[i]['goods_thumb'] + '"></a>' +
                        '<div class="right">' +
                        '<a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id+'&nper='+product[i].current_nper+'"><p class="name"><span>[第' + product[i]['current_nper'] + '期]</span><a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id+'&nper='+product[i].current_nper+'">' + product[i]['goods_name'] + '</a></p>' +
                        '<a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id+'&nper='+product[i].current_nper+'"><p class="count">价值：￥' + product[i]['shop_price'] + '</p></a>' +
                        '<p class="card addTap"></p>' +
                        '<div class="speedBox"><p class="speedbg"><span class="speed"><span class="speedIng" style="width:' + Math.round((product[i]['salesTotal'] / (product[i]['shop_price'] / product[i]['single_price']))*100) + '%"><i></i></span></span></p>' +
                        '<a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id+'&nper='+product[i].current_nper+'"><p class="spengBottom">' + product[i]['salesTotal'] + '<span>' + Math.ceil(product[i]['shop_price'] / product[i]['single_price']) + '</span></p></a></div></div></li>';
                    }
                    $('.newList .clearfix').before(async);
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
    .search_btn_group .btsearch{display: inline-block;background-color: #fff;width:41px;height: 30px;line-height: 30px;text-align: left;border-radius: 5px;margin-left:-3px;}
</style>
<body>
<header class="normal">
    <div class="head-wrap">
        <h2>所有商品</h2>
        <a href="javascript:history.go(-1);" class="goback_btn"></a>
        <a href="#" class="search_btn" id="search_show"></a>
        <?php $this->renderPartial('_search', array('model' => $model)); ?>
    </div>
</header>
<div class="tab-box">
    <div class="tab">
        <a href="javascript:void(0)" class="tabItem active">全部分类</a>
        <a href="javascript:void(0)" class="tabItem">最新揭晓</a>
        <div class="clearfix"></div>
    </div>
</div>
<div class="container">
    <?php echo $this->renderPartial('/layouts/_column')?>
    <ul class="list-group">
        <li onclick="javascript:window.location.href='<?php echo Yii::app()->createUrl("/yifenzi3/goods/announced")?>'">
            <a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/announced')?>">
                <span class="active">最新揭晓</span>
                <i class="listIcon choose"></i>
            </a>
        </li>
        <li onclick="javascript:window.location.href='<?php echo Yii::app()->createUrl("/yifenzi3/goods/announced")?>?retUrl=<?php echo Yii::app()->request->url ?>&type=hot'">
            <a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/announced')?>?retUrl=<?php echo Yii::app()->request->url ?>&type=hot">
                <span>人气</span>
            </a>
        </li>
        <li onclick="javascript:window.location.href='<?php echo Yii::app()->createUrl("/yifenzi3/goods/announced")?>?retUrl=<?php echo Yii::app()->request->url ?>&type=pricex'">
            <a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/announced')?>?retUrl=<?php echo Yii::app()->request->url ?>&type=pricex">
                <span>价值（由高到低）</span>
            </a>
        </li>
        <li onclick="javascript:window.location.href='<?php echo Yii::app()->createUrl("/yifenzi3/goods/announced")?>?retUrl=<?php echo Yii::app()->request->url ?>&type=pricem'">
            <a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/announced')?>?retUrl=<?php echo Yii::app()->request->url ?>&type=pricem">
                <span>价值（由低到高）</span>
            </a>
        </li>
        <li onclick="javascript:window.location.href='<?php echo Yii::app()->createUrl("/yifenzi3/goods/announced")?>?retUrl=<?php echo Yii::app()->request->url ?>&type=news'">
            <a href="<?php echo Yii::app()->createUrl('/yifenzi3/goods/announced')?>?retUrl=<?php echo Yii::app()->request->url ?>&type=news">
                <span>最新</span>
            </a>
        </li>
    </ul>
    <div class="setting"></div>
    <div class="newList">
        <ul class="allShow">
            <!--产品处-->
            <div class="clearfix"></div>
        </ul>
        <p class="pullLoad"></p>
        <div class="pullUp"></div>
    </div>
</div>

<div class="height54"></div>
<footer>
    <nav id="guide">
        <a href="/" class="home<?php if($this->footerPage == 1):?> active<?php endif;?>">首页</a>
        <a href="<?php echo $this->createUrl('goods/list');?>" class="product<?php if($this->footerPage == 2):?> active<?php endif;?>">所有商品</a>
        <a href="<?php echo $this->createUrl('goods/announced');?>" class="announce<?php if($this->footerPage == 3):?> active<?php endif;?>">最新揭晓</a>
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
    $('.tabItem').click(function(){
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