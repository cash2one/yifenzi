<script type="text/javascript" src="/yifenzi/js/iscroll-probe.js"></script>
<div style="height: 40px"></div>
<div id="container">
    <div class="newList">
        <ul>
            <div class="clearfix"></div>
        </ul>
        <p class="pullLoad"></p>
    </div>
    <div class="h60"></div>
</div>
<script type="text/javascript">
    var items_per_page = 10;
    var scroll_in_progress = false;//滚动过程
    var myScroll;
    //    var fmNum = 10000789;
    var flag = false; //判断有没数据 返回
    $(function(){
        //导航点击切换
        var i = 0;
        var ohtml = '';
        var content = '';
        var next_page = 1;
        getProduct(next_page);
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
        var async = '';
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
                        async += '<li><a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id
                        +'&nper='+product[i].current_nper+'"><img src="' + product[i]['goods_thumb'] + '"></a>' +
                        '<div class="right">' +
                        '<p class="name"><span>[第' + product[i]['current_nper'] + '期]</span><a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id
                        +'&nper='+product[i].current_nper+'">' + product[i]['goods_name'] + '</a></p>' +
                        '<p class="count">价值：￥' + product[i]['shop_price'] + '</p>' +
                        '<p class="wining"><span>中奖码' + product[i]['winning_code'] + '</span>揭晓时间：'+ product[i]['sumlotterytime'] + '</p>' +
                        '</div></li>';
                    }
                    $('.newList > ul > .clearfix').before(async);
                    async = '';
                } else {
                    $('.pullLoad').html('目前暂无更多数据.....');
                }
            }
        })
    }
</script>
