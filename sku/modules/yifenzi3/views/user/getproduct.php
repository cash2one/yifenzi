<script type="text/javascript" src="/yifenzi3/js/iscroll-probe.js"></script>
<script type="text/javascript">

        var items_per_page = 10;
        var scroll_in_progress = false;//滚动过程
        var myScroll;
        var refreshAmount = 10;//加载条数
        var fmNum = 0;
        var content = '';
        var remainHeight = $(window).height() - $('.header').height();
        load_content = function (refresh, next_page) {

            content = '';
            setTimeout(function () { // 插入数据
                if (!refresh) {//第一次插入-----根据高度判断插入数
                    refreshAmount = (parseInt(remainHeight/40));
                    <?php
                    $content = '';
                    foreach ($data as $k => $an) {
						if($an['sumlotterytime'] < time()){
                        $content .= '<li><a href="' . Yii::app()->createUrl('/yifenzi3/goods/view', array('id' => $an['goods_id'],'nper' => $an['current_nper'])) . '"><img src="' . $an['goods_thumb'] . '"></a><div class="right"><p class="name"><span>[第' . $an['current_nper'] . '期]</span><a href="' . Yii::app()->createUrl('/yifenzi3/goods/view', array('id' => $an['goods_id'],'nper' => $an['current_nper'])) . '">' . $an['goods_name'] . '</a></p><p class="count">版价值：￥' . $an['shop_price'] . '</p><p class="wining"><span>中奖码：' . $an['winning_code'] . '</span>揭晓时间：' . date("Y-m-d H:i:s", $an['sumlotterytime']) . '</p></div></li>';
						}
                    }
//                    ?>
                    content += '<?php echo $content; ?>';
                    $('.newList > ul > .clearfix').before(content);
//
                }else if (refresh && next_page) {
                    refreshAmount = refresh;
                    getProduct(next_page);
                    $('.newList > ul > .clearfix').before(content);
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
            var next_page = 2;
            //如果无数据
            if($('.newList ul li').length>=100){//最多100条
                refresh = 0;
                $('.pullLoad').html('目前暂无更多数据.....');
                setTimeout(function(){
                    $('.pullUp').html('').hide();
                },500);
                return false;
            }
            load_content(refresh, next_page+=1);
            if (callback) {
                callback();
            }
        }
        function pullActionCallback() {
            //延迟提示语消失时间
            if (pullUpEl && pullUpEl.className.match('pullUp')) {
                setTimeout(function(){
                    $('.pullUp').html('');
                },200);

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

                if ($('#wrapper ul > li').length >= items_per_page) {
                    pullActionDetect.check(0);
                }
            });
            myScroll.on('scrollEnd', function () {
                console.log('scroll ended');
                setTimeout(function () {
                    scroll_in_progress = false;
                }, 100);
            });
            // In order to prevent seeing the "pull down to refresh" before the iScoll is trigger - the wrapper is located at left:-9999px and returned to left:0 after the iScoll is initiated
            setTimeout(function () {
                $('#wrapper').css({left: 0});
            }, 100);
        }




          load_content();




        document.addEventListener('touchmove', function (e) {
            e.preventDefault();
        }, false);
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
                            async += '<li><a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id
          					+'&nper='+product[i].current_nper+'"><img src="<?php echo ATTR_DOMAIN ?>/' + product[i]['goods_thumb'] + '"></a>' +
                                '<div class="right">' +
                                '<p class="name"><span>[第' + product[i]['current_nper'] + '期]</span><a href="<?php echo $this->createUrl('goods/view')?>?id='+product[i].goods_id
          					+'&nper='+product[i].current_nper+'">' + product[i]['goods_name'] + '</a></p>' +
                                '<p class="count">价值：￥' + product[i]['shop_price'] + '</p>' +
                                '<p class="wining"><span>中奖码' + product[i]['winning_code'] + '</span>揭晓时间：'+ date("Y-m-d H:i:s", product[i]['sumlotterytime']) + '</p>' +
                                '</div></li>';
                        }
                    } else {
                        flag = true;
                        $('.pullLoad').html('目前暂无更多数据.....');
                    }
                }
            })
        }
    </script>


<div class="warpbg">
    <div id="wrapper" class="noNav">
        <div id="scroller">
            <div class="newList">
                <ul>

                    <div class="clearfix"></div>
                </ul>
                <p class="pullLoad">往下滚动读取更多......</p>
                <div class="pullUp"></div>
            </div>
        </div>
    </div>

    <div class="h60"></div>
</div>
