<script type="text/javascript" src="/yifenzi/js/iscroll-probe.js"></script>
<style type="text/css">
    #wrapper {top: 95px;}
    .pullUp,.pullLoad{text-align: center;font-size: 14px;color: #999999;}
</style>
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
    var content = '', async = '';
    var flag = false; //判断有没数据 返回
    $(function(){
        //导航点击切换
        var i = 0;
        var ohtml = '';
        var content = '';
        var next_page = 1;
        getContent(next_page);
        var scrollTop = 0;
        var client = $(window).height();
        $(window).scroll(function(){
            scrollTop = $(window).scrollTop()+client;
            if(scrollTop>=$('body').height()){
                next_page++;
                getContent(next_page);
            }
        })
        //图片懒加载
        $('img').picLazyLoad();
    })
    ///获取限购产品
    function getContent(page)
    {
        var content = "";
        $.ajax({
            type:"post",
            url:'<?php echo $this->createUrl('user/buyRecord')?>',
            cache: false,
            async: false,
            dataType:"json",
            data:{'YII_CSRF_TOKEN':"<?php echo Yii::app()->request->csrfToken?>",'page':page},
            success:function(dat){
                if(dat.result) {
                    var data = dat.data;
                    //console.info(data);
                    for (var i = 0; i < data.length; i++) {
                        var status = parseInt(data[i].status);
                        //console.info(status);
                        switch (status) {
                            case 1:
                                content += '<li><a href="<?php echo $this->createUrl('user/bugdetailend')?>?id=' + data[i].order_id
                                + '_' + data[i].goods_id + '"><img src="' + data[i].goods_thumb + '"><span class="title on">揭晓中</span></a>';
                                content += '<div class="right"><a href="<?php echo $this->createUrl('goods/view')?>?id=' + data[i].goods_id
                                + '&nper=' + data[i].current_nper + '">';
                                content += '<p class="name"><span>[第' + data[i].current_nper + '期]</span>' + data[i].goods_name + '</p>';
                                content += '<p class="count">价值：￥' + data[i].goods_price + '</p>';
                                content += '<p class="overTime">你已加入 <i>' + data[i].goods_number + '</i> 份子 </p>';
                                content += '<p class="overTime">揭晓时间：' + data[i].sumlotterytime + '</p></a></div></li>';
//                             <div class="right">
//                                 <p class="name"><span>[第27期]</span><a href="detail.html">华为Mate8华为Mate8Mate8华为Mate8华为华为Mate8华为Mate8Mate8华为Mate8华为Mate8</a></p>
//                                 <p class="count">价值：￥3488.00</p>
//                                 <p class="overTime">你已加入 <i>10</i> 分子 </p>
//                                 <p class="overTime">揭晓时间：02月29日09:55</p>
//                             </div>
                                break;
                            case 2:
                                content += '<li><a href="<?php echo $this->createUrl('user/bugdetailend')?>?id=' + data[i].order_id
                                + '_' + data[i].goods_id + '"><img src="' + data[i].goods_thumb + '"><span class="title">已揭晓</span></a>';
                                content += '<div class="right"><a href="<?php echo $this->createUrl('goods/view')?>?id=' + data[i].goods_id
                                + '&nper=' + data[i].current_nper + '">';
                                content += '<p class="name"><span>[第' + data[i].current_nper + '期]</span>' + data[i].goods_name + '</p>';
                                content += '<p class="count">价值：￥' + data[i].goods_price + '</p>';
                                if (data[i].mobile) {
                                    content += '<p class="overTime">获奖者：<i>' + data[i].mobile + '</i></p>';
                                } else {
                                    content += '<p class="overTime">获奖者：<i>' + data[i].gai_number + '</i></p>';
                                }

                                content += '<p class="overTime">揭晓时间：' + data[i].sumlotterytime + '</p></a></div></li>';
//       						<li>
//                             <a href="bug-detail-end.html"><img src="images/prc_88x88.png"><span class="title">已揭晓</span></a>
//                             <div class="right">
//                                 <p class="name"><span>[第27期]</span><a href="detail.html">华为Mate8华为Mate8Mate8华为Mate8华为华为Mate8华为Mate8Mate8华为Mate8华为Mate8</a></p>
//                                 <p class="count">价值：￥3488.00</p>
//                                 <p class="overTime">获奖者：<i>靠谱的一分子</i></p>
//                                 <p class="overTime">揭晓时间：02月29日09:55</p>
//                             </div>
//                         </li>
                                break;
                            case 0:
                                content += '<li><a href="<?php echo $this->createUrl('user/bugdetailend')?>?id=' + data[i].order_id
                                + '_' + data[i].goods_id + '"><img src="' + data[i].goods_thumb + '"><span class="title being">进行中</span></a>';
                                content += '<div class="right"><a href="<?php echo $this->createUrl('goods/view')?>?id=' + data[i].goods_id
                                + '&nper=' + data[i].current_nper + '">';
                                content += '<p class="name"><span>[第' + data[i].current_nper + '期]</span>' + data[i].goods_name + '</p>';
                                content += '<p class="count">价值：￥' + data[i].goods_price + '</p>';
                                content += '<p class="overTime">你已加入 <i>' + data[i].goods_number + '</i> 份子 </p>';
                                content += '<p class="speedbg"><span class="speed"><span style="width:' + data[i].percentage + '%" class="speedIng"><i></i></span></span></p>';
                                content += '<p class="spengBottom">' + (parseInt(data[i].count_nper) - parseInt(data[i].inventory)) + '<span>' + parseInt(data[i].count_nper) + '</span></p></a></div></li>';
//       						<li>
//                             <a href="bug-detail-end.html"><img src="images/prc_88x88.png"><span class="title being">进行中</span></a>
//                             <div class="right">
//                                 <p class="name"><span>[第27期]</span><a href="detail.html">华为Mate8华为Mate8Mate8华为Mate8华为华为Mate8华为Mate8Mate8华为Mate8华为Mate8</a></p>
//                                 <p class="count">价值：￥3488.00</p>
//                                 <p class="overTime">你已加入 <i>10</i> 分子 </p>
//                                 <p class="speedbg"><span class="speed"><span style="width:40%" class="speedIng"><i></i></span></span></p>
//                                 <p class="spengBottom">640<span>2848</span></p>
//                             </div>
//                         </li>
                                break;
                        }
                    }
                    $('.newList > ul > .clearfix').before(content);
                    content = '';
                }else{
                    $('.pullLoad').html('目前暂无更多数据.....');
                }
            }
        });

    }
</script>
