<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/yifenzi/css/swiper.css');
?>
<script src="/yifenzi/js/swiper.min.js"></script>
<script src="/yifenzi/js/common.js"></script>
<script src="/yifenzi/js/jweixin-1.0.0.js"></script>

<header class="normal">
    <div class="head-wrap">
        <h2>一份子</h2>
        <!--<a href="javascript:history.go(-1);" class="goback_btn"></a>-->

			<?php if (Yii::app()->session['type'] == 'android'): ?>
			<a href="http://android/yifenzi/returnGFT" class="goback_btn"></a>
			<?php endif?>
			<?php if (Yii::app()->session['type'] == 'ios'): ?>
			<a href="http://ios/yifenzi/returnGFT" class="goback_btn"></a>
			<?php endif?>

        <a href="#" class="search_btn" id="search_show"></a>
        <?php $this->renderPartial('_search', array('model' => $model)); ?>
    </div>
</header>
<div class="container">
<div class="warpbg">
    <?php if($advert){ ?>
    <div class="banner">
        <div id="module_1" class="swiper-container">
            <div class="swiper-wrapper">
                <!--广告这里要改呀-->
                <?php //foreach ($advert as $a): ?>
                    <!--<div class="two swiper-slide"><img src="<?php //echo $a['picture'] ?>" style="width: 100%; height: 100%;" /></div>-->
                <?php //endforeach; ?>
                <?php foreach($advert as $k=>$v): ?>
                    <div class="two swiper-slide"><a href ="<?php echo $v['tourl']?>" ><img onerror="this.src='/yifenzi/images/img-load.png'" data-original="<?php echo ATTR_DOMAIN . '/' . $v['img'] ?>" style="width: 100%;"/></a></div>
                <?php endforeach;?>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
        <!-- Initialize Swiper -->
        <script>
            var swiper = new Swiper('#module_1.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                loop: true,
                loopAdditionalSlides: 0,
                autoplay: 5000, //可选选项，自动滑动
                autoplayDisableOnInteraction: false
            });
        </script>
    </div>
    <?php } ?>
    <div class="popbox">
        <h4>人气推荐<a href="<?php echo Yii::app()->createUrl('/yifenzi/goods/list');?>"></a></h4>
        <div id="module_2" class="swiper-container">
            <div class="swiper-wrapper">
                <?php foreach ($recommendcd as $r): ?>
                    <div class="one swiper-slide">
                        <div class="popItem">
                            <a href="<?php echo Yii::app()->createUrl('/yifenzi/goods/view',array('id'=>$r->goods_id,'nper'=>$r->current_nper))?>">
                                <?php
                                $number = YfzOrderGoods::getGoodsNumber($r->goods_id, $r->current_nper);
                                $sales = $number->goods_number ? $number->goods_number : 0;
                                $total = ceil($r->shop_price / $r->single_price);
                                ?>
                                <p class="memuImg"><img onerror="this.src='/yifenzi/images/img-load.png'" width="100%" height="100%" data-original="<?php echo ATTR_DOMAIN . '/' . $r->goods_thumb ?>" /></p>
                                <p class="price">价值：￥<?php echo $r->shop_price; ?></p>
                                <p class="speedbg"><span class="speed"><span class="speedIng" style="width:<?php echo ($sales/$total) * 100 ?>%"><i></i></span></span></p>
                                <p class="count"><?php echo $sales; ?><span><?php echo $total ?></span></p>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
            var swiper = new Swiper('#module_2.swiper-container', {
                slidesPerView: 3.5,
                paginationClickable: true,
                spaceBetween: 5,
                freeMode: true
            });
        </script>
    </div>
    <div class="newbox">
        <h4>最新揭晓<a href="<?php echo Yii::app()->createUrl('/yifenzi/goods/announced')?>"></a></h4>
        <div class="newList">
            <ul>
                <?php foreach($announced as $an):?>
                <li>
                    <a class="imgbox" href="<?php echo Yii::app()->createUrl('/yifenzi/goods/view',array('id'=>$an['goods_id'],'nper'=>$an['current_nper']))?>"><img onerror="this.src='/yifenzi/images/img-load.png'" data-original="<?php echo $an['thumb']?>"></a>
                    <div class="right">
                        <p class="name"><span>[第<?php echo $an['current_nper']?>期]</span><a href="<?php echo Yii::app()->createUrl('/yifenzi/goods/view',array('id'=>$an['goods_id'],'nper'=>$an['current_nper']))?>"><?php echo $an['name']?></a></p>
                        <?php if(strtotime($an['sumlotterytime']) > time()):?>
                        <p class="time" date="<?php echo $an['sumlotterytime']?>"><span id="time_h">00</span>:<span id="time_m">00</span>:<span id="time_s">00</span></p>
                        <?php else: ?>
                        <p class="time end" date="<?php echo $an['sumlotterytime']?>">已揭晓</p>
                        <?php endif;?>
                        <p class="count max">价值：<br>￥<?php echo number_format($an['price'], 2);?></p>
                    </div>
                </li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
    <!-- 专区 -->
    <?php if($column){ ?>
    <div class="zonebox">
        <h4>专区浏览<!--<a href="#"></a>--></h4>
        <div class="zoneList">
            <ul>
                <!--///广告///-->
                <?php foreach($column as $k=>$v){ ?>
                <li><a href="<?php echo Yii::app()->createUrl('/yifenzi/goods/list');?>?column_id=<?php echo $v['id'] ?>"><img onerror="this.src='/yifenzi/images/img-load.png'"  data-original="<?php echo ATTR_DOMAIN. '/' .$v['zone_thumb'] ?>"></a></li>
                <?php } ?>
                <!--<li><a href="theme.html"><img src="http://usr.im/280x160"></a></li>-->
            </ul>
        </div>
    </div>
    <?php }?>
    <!-- end -->
    <div class="h60"></div>
</div>
</div>
<script src="/yifenzi/js/picLazyLoad.js"></script>
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
    $('img').picLazyLoad();
</script>
<script id="DS_PRE_JS" type="text/javascript"
        src="http://cdn.datastory.com.cn/js/pre-ds-min.js?dsTid=8cefb2ec-24d0-4ab2-b9ae-cb6013c8a247">
</script>
<?php
$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (strpos($user_agent, 'MicroMessenger')):
    $jssdk = new JSSDK(WxPayConfig::APPID, WxPayConfig::APPSECRET);
    $signPackage = $jssdk->GetSignPackage();
    if(!empty($signPackage)):
?>
<script>
    var share_link = "<?php echo $signPackage["url"];?>";
    wx.config({
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            // 所有要调用的 API 都要加到这个列表中
            'onMenuShareTimeline',
            'onMenuShareAppMessage'
        ]
    });
    wx.ready(function () {
        DS.ready(function () {
            wx.onMenuShareAppMessage({
                title: '',
                desc: '',
                link: DS.linkChange(share_link),
                imgUrl: 'http://skuatt.e-gatenet.cn//2016/10/20/d13f56e676c6d7663f5caa5a3a9214ba.jpg',
                success: function () {
                    DS.sendRepost("appMessage");
                    //other code
                }
            });
            wx.onMenuShareTimeline({
                title: '',
                desc: '',
                link: DS.linkChange(share_link),
                imgUrl: 'http://skuatt.e-gatenet.cn//2016/10/20/d13f56e676c6d7663f5caa5a3a9214ba.jpg',
                success: function () {
                    DS.sendRepost("timeline");
                    //other code
                }
            });
        });
    });
</script>
        <?php endif;?>
    <?php endif;?>
<!-- 加载 js-->

<?php echo $this->renderPartial('/layouts/_announce')?>