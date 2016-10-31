<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>参与记录</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <!-- 微信测试用清理缓存  -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="email=no">
    <link rel="stylesheet" type="text/css" href="/yifenzi/css/common.css">
    <script src="/yifenzi/js/zepto.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="/yifenzi/js/iscroll-probe.js"></script>
    <script type="text/javascript" src="/yifenzi/js/iscroll-probe.js"></script>
    <script type="text/javascript" src="/yifenzi/js/picLazyLoad.js"></script>
</head>
<body onload="loaded()">
<header class="normal">
    <h2>参与记录</h2>
    <a href="javascript:history.go(-1);" class="goback_btn"></a>
</header>
<style type="text/css">
    #wrapper {top: 95px;}
    .pullUp,.pullLoad{text-align: center;font-size: 14px;color: #999999;}
</style>
<div class="pullUp"></div>
<div id="wrapper" style="position:static">
    <div id="scroller">
        <div class="warpbg">
            <ul class="joinList data">

                <div class="clearfix"></div>
            </ul>
            <div class="pullLoad"></div>
            <div class="pullLoad"></div>
        </div>
    </div>
</div>

<div class="h60"></div>

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
        var i = 0;
        var ohtml = '';
        var content = '';
        var next_page = 1;
        var goods_id = <?php echo $goods_id;?>;
        function insertLi(){
            ohtml = '';
            if (<?php echo  !empty($record)?1:0;?>){
                <?php
                //获取用户信息//
                $content = '';
                   if(is_array($record) && !empty($record)){
                foreach ($record as $r) {
                 $member = Member::getMemberInfo($r['member_id']);
                 $GWnumber = ($member && $member['gai_number']) ? substr_replace($member['gai_number'],'****',4,4) : '一份子';
                 $price = ceil($model->shop_price/$model->single_price);
                  $address_data = Address::model()->find('member_id=:id AND `default`=:default',array(':id'=>$member['id'],':default'=>Address::DEFAULT_IS));
        $address_new = Member::getMemberAddressNew($member['id']);
        if(!$address_data){
            if(!empty($address_new)){
                $address = Region::getName($address_new["province_id"],$address_new["city_id"]);
            }else {
                $adress = Tool::GetIpLookup($member['id']);
                if (!empty($adress)) {
                    $address = $adress['province'] . ' ' . $adress['city'];
                } else {
                    $address = '广东 广州';
                }
            }
        }
        else{
            $address = Region::getName($address_data->province_id,$address_data->city_id);
        }
                 $addtime = date('Y-m-d H:i:s',$r['addtime']). substr($r['addtime'], strpos( $r['addtime'],'.'));
                    $content .= '<li><div class="guestInfo"><div class="guestIcon"><span><span style ="width: 50px;height: 50px;border-radius: 50%; vertical-align: middle; background-size: auto 100%;"><img src="/yifenzi/images/userIcon.png" witdh="100%" height="100%"></span></span></div><div class="guestDetail"><p><strong>'.$GWnumber.'</strong><label><span>'.$price.'</span><span class="pIcon"></span><span class="mgl30"> '.$r['goods_number'].'</span><span class="fIcon"></span></label></p><p><small>'.$address.'</small><label><small class="black">'.$addtime.'</small></label></p></div></div></li>';
                }
                }
                ?>
                ohtml += '<?php echo $content; ?>';
                $('.joinList .clearfix').before(ohtml);
            }else{
                noDataTips();
            }
        }
        insertLi();
        var scrollTop = 0;
        var client = $(window).height();
        $(window).scroll(function(){
            scrollTop = $(window).scrollTop()+client;
            if(scrollTop>=$('body').height()){
                next_page++;
                getProduct(goods_id,next_page);
            }
        })
        //图片懒加载
        $('img').picLazyLoad();
    })
    ///获取限购产品
    function getProduct(goods_id,next_page)
    {
        $.ajax({
            type: 'get',
            datetype: 'json',
            cache: false,
            async: false,
            url: '<?php echo preg_replace('/\&page=\d{0,}|\?page=\d{0,}/', '', Yii::app()->request->url) ?>',
            data: {page: next_page,id:goods_id},
            success: function (data) {
                data = eval("(" + data + ")");
                async = '';
                if (data.result) {
                    var product = data.data;
                    for (var i = 0, len = product.length; i < len; i++) {
                        async += '<li><div class="guestInfo"><div class="guestIcon"><span><span style ="width: 50px;height: 50px;border-radius: 50%; vertical-align: middle; background-size: auto 100%;"><img src="/yifenzi/images/userIcon.png" witdh="100%" height="100%"></span></span></div><div class="guestDetail"><p><strong>'+product[i]['GWnumber']+'</strong><label><span>'+product[i]['price']+'</span><span class="pIcon"></span><span class="mgl30">'+product[i]['goods_number']+'</span><span class="fIcon"></span></label></p><p><small>'+product[i]['address']+'</small><label><small class="black">'+product[i]['addtime']+'</small></label></p></div></div></li>';
                    }
                    $('.joinList .clearfix').before(async);
                } else {
                    async = '';
                    flag = true;
                    $('.pullLoad').html('目前暂无更多数据.....');
                }
            }
        })
    }
</script>
<script id="DS_PRE_JS" type="text/javascript"
        src="http://cdn.datastory.com.cn/js/pre-ds-min.js?dsTid=8cefb2ec-24d0-4ab2-b9ae-cb6013c8a247">
</script>

</body>
</html>