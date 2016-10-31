<style type="text/css">
* {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
html {
    -ms-touch-action: none;
}

body {
    overflow: hidden;
}

.pullDownLabel, .pullUpLabel {
    color: #999;
    margin-left: 20px;
}

.pullDown, .pullUp {
    background: #fff;
    height: 40px;
    line-height: 40px;
    font-weight: bold;
    font-size: 0.8em;
    color: #888;
}
.fmList{margin: 0 10px;}
.fmList li{width: 25%; height: 30px; float: left; display: inline-block; padding-bottom: 10px; font-size: 14px; text-align: center;}

</style>
<script type="text/javascript" src="/yifenzi/js/iscroll-probe.js"></script>
    <script type="text/javascript">

    var items_per_page = 10;
    var scroll_in_progress = false;//滚动过程
    var myScroll;
    var refreshAmount = 10;//加载条数
//    var fmNum = 10000789;
    var content = '', async = '';
    var flag = false; //判断有没数据 返回
    var next_page = 1;//下一页
    load_content = function (refresh, next_page) {
        setTimeout(function () { // 插入数据
            if (!refresh) {//第一次插入-----根据高度判断插入数
                if (<?php echo  count($winningEachs)?>){
                    var deviceHeight = $(window).height();
                    var remainHeight = deviceHeight - $('.header').height()-$('.fmTitle').height()-$('.fmList-time').height()-$('.h60').height();
                    refreshAmount = (parseInt(remainHeight/40)*4);
                    <?php
                    $content = '';
                    foreach ($datas as $k => $an) {
                        $content .= '<li>'.$an.'</li>';
                    }
                    ?>
                    content += '<?php echo $content; ?>';
                    $('.container .clearfix').before(content);
                }else{
                    noDataTips();
                }


            } else if (refresh && next_page) {
                refreshAmount = refresh;
                getMore(next_page);
                if (!flag){
					$('.container .clearfix').before(async);
					async = '';
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
        }, 1000);

    };

    function pullUpAction(callback) {
        refresh = 80;//默认插入20条
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
        pullDownEl = document.querySelector('#wrapperss .pullDown');
        if (pullDownEl) {
            pullDownOffset = pullDownEl.offsetHeight;
        } else {
            pullDownOffset = 0;
        }
        pullUpEl = document.querySelector('#wrapperss .pullUp');
        if (pullUpEl) {
            pullUpOffset = pullUpEl.offsetHeight;
        } else {
            pullUpOffset = 0;
        }

        myScroll = new IScroll('#wrapperss', {
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

            if ($('#wrapperss ul > li').length >= items_per_page) {
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
            $('#wrapperss').css({left: 0});
        }, 100);
    }
  
	
	function loaded() {
        load_content();
    }
    
    function getMore(next_page)
    {
        $.ajax({
            type: 'get',
            datetype: 'json',
            cache: false,
            async: false,
            url: '<?php echo preg_replace('/\&page=\d{0,}|\?page=\d{0,}/', '', Yii::app()->request->url) ?>',
            data: {page: next_page},
            success: function (data) {
                async = '';
				data = eval("(" + data + ")");
                if (data.result) {
                    var product = data.data;
                    for (var i = 0, len = product.length; i < len; i++) {
                       async += '<li>'+product[i]+'</li>';
                    }
                }else {
                    async = '';
                    flag = true;
                    $('.pullLoad').html('目前暂无更多数据.....');
                }
            }
        })
    }
</script>
<body onload="loaded()">
<header class="normal">
    <h2>份子码</h2>
    <a href="javascript:history.go(-1);" class="goback_btn"></a>
</header>
<style type="text/css">
.container{top:0px;}  
</style>
<div id="wrapperss">
    <div id="scroller">
        <div class="container">

            <div class="fmTitle">
                <p>共参与人次</p>
                <p><strong class="mgr10"><?php echo $active_num;?></strong><span class="pIcon"></span></p>
            </div>
            <table class="fmList-time">
                <tbody> 
                    <tr><td>揭晓时间：<?php echo date("Y-m-d H:i:s",$sumlotterytime);?></td></tr>
                    <tr><td>云购时间：<?php echo $addtime_micr;?></td></tr>
                </tbody>
            </table>

            <ul class="fmList">
                <!------------------------------------数据插入位置---------------------------------->
                <div class="clearfix"></div>
            </ul>
            <p class="pullLoad"></p>
            <div class="pullUp"></div>
        </div>
    </div>
</div>

</body>
