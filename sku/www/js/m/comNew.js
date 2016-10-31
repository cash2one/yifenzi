(function($) {
    $.fn.tab = function() {
        $(this).each(function() {
            var length = $(this).children('a').length - 1;
            $(this).children().width(parseInt(($(this).width() - length * 2 - 1) / $(this).width() / (length + 1) * 1000) / 10 + "%");
        });
        return $(this);
    };  
    
    $.fn.page = function(options) {
        var settings = {
            data: {},
            url: '',
            callback: null,
            template: null,
            pagesize:10,
            count: 0,
            container: null,
            pageurl: ''
        };
        if (options) {
            $.extend(settings, options);
        }
        if (!settings.template) {
            settings.template = $('#js-good-template').html();
        }
        /* settings.template = template.compile(settings.template); */

        var self = this;
        if ($(self).hasClass('gxui-pages')) {
            if (settings.container.css('position') != 'absolute' || settings.container.css('position') != 'relative') {
                settings.container.css('position', 'relative');
            }
            function setbutton() {
                if (settings.page == 1) {
                    $prev.hide().prev().show();
                } else {
                    $prev.show().prev().hide();
                }
                if (settings.page == $select.find('option').length) {
                    $next.hide().next().show();
                } else {
                    $next.show().next().hide();
                }
            }
            ;
            var ajaxCount = 0;
            var loading = null;
            function ajax() {
                setbutton();
                if (settings.pageurl) {
                    try {
                        history.replaceState(null, '', settings.pageurl.replace(/@page/g, settings.page));
                    } catch (e) {
                    }
                }
                try {
                    loading.remove();
                } catch (e) {
                }
                document.body.scrollTop = settings.container.offset().top;
                $select.unbind('change', $select.data('page-change'));
                $select.val(settings.page).change();
                $select.bind('change', $select.data('page-change'));
                var _ajaxCount = ++ajaxCount;
                settings.data.page = settings.page;
                settings.data.pageSize = settings.pagesize;
                settings.data.count = settings.count;
                loading = $('<img style="position: absolute;top: 5px;left: 50%;margin-left: -12px;" src="com/template/images/public/loading.gif" width="24" height="24"/>').appendTo(settings.container);

                $.ajax({url: settings.url, type: "POST", data: settings.data, error: function() {
                        if (_ajaxCount != ajaxCount)
                            return;
                        loading.remove();
                       // alert('链接服务器失败，请稍后再试！');
                    }, success: function(data) {
                        if (_ajaxCount != ajaxCount)
                            return;
                        try {
                            data = $.parseJSON(data);
                            settings.count = data.count;
                            if (data.list) {
                                var html = '';
                                $.each(data.list, function(index, value) {
                                    html += settings.template({data: value}).replace(new RegExp('<',"gm"),'<').replace(new RegExp('>',"gm"),'>');;
                                });
                                settings.container.html(html);
                                document.body.scrollTop = settings.container.offset().top;
                                if (settings.callback) {
                                    settings.callback(html);
                                }
                            }
                        } catch (e) {
                          //  alert('链接服务器失败，请稍后再试！');
                        }
                    }});
            }
            ;
            var $select = $(self).find('select');
            $(self).find('a').unbind('click');
            var $prev = $(self).find('a').eq('0');
            var $next = $(self).find('a').eq('1');
            $prev.click(function() {
                settings.page--;
                ajax();
            });
            $next.click(function() {
                settings.page++;
                ajax();
            });
            if ($select.data('page-change')) {
                $select.unbind('change', $select.data('page-change'));
            }
            settings.page = parseInt($select.val());
            setbutton();
            $select.data('page-change', function() {
                settings.page = parseInt($select.val());
                ajax();
            });
            $select.bind('change', $select.data('page-change'));
        }
        return $(this);
    };
	// 遮照层
    $.documentMask = function(options) {
        // 扩展参数
        var op = $.extend({
            opacity: 0.6,
            z: 150,
            bgcolor: '#000',
            time: 500,
            id: "jquery_addmask"
        }, options);
        // 创建一个 Mask 层，追加到 document.body
        $("#" + op.id).remove();
        $('<div id="' + op.id + '" class="jquery_addmask"> </div>').appendTo(document.body).css({
            position: 'absolute',
            top: '0px',
            left: '0px',
            'z-index': op.z,
            width: $(window).width(),
            height: $(document).height(),
            'background-color': op.bgcolor,
            opacity: 0
        }).fadeTo(op.time, op.opacity).click(function() {
			$('.shareMask').css('display','none');
            //// 单击事件，Mask 被销毁
            $(this).fadeTo('slow', 0, function(){
               $(this).remove();
            });
        });
        return this;
    };
    
})($);
$(document).ready(function(e) {
	/*Tab*/
    $('.gxui-tab > a').live('click', function() {
        $(this).parent().children('.selected').removeClass('selected');
        $(this).addClass('selected');
        $(this).parent().find('input').val($(this).attr('value'));
    });
	setTimeout(scrollTo,0,0,0);
	$(window).resize(function(){
		var width=$('body').width();	
		$('.search .searchTag a').css('height',150*(width/640));
		$('.search .searchTag a').css('line-height',(150*(width/640))/8.2);
		
		$('.touchslider-item a').css('width',width);
		$('.touchslider-viewport').css('height',280*(width/640));
		$('.proSlider .touchslider-viewport').css('height',370*(width/640));
		$('.redBagBanner .item').css('height',366*(width/640));
		$('.redBagBanner02 .item').css('height',415*(width/640));
		$('.adverLink img').css('height',115*(width/640));
		$('.brand li').css('height',40*(width/640));
		$('.brand li img').css('height',40*(width/640));
		$('.brandProduct li').css('height',245*(width/640));
		$('.brandProduct li img').css('height',245*(width/640));
		$('.mallClass li').css('height',165*(width/640));
		$('.mallClass li:first-child').css('height',330*(width/640));
		
		$('.mallClass02 li').css('height',168*(width/640));
		$('.products li').css('height',410*(width/640));
		$('.products02 li').css('height',300*(width/640));
		$('.products02 li img').css('height',240*(width/640));
		$('.products li .itemInfor p').css('height',40*(width/640));
		$('.products li .itemInfor p').css('line-height',(40*(width/640))/14);
		$('.products02 li .itemInfor p').css('height',60*(width/640));
		$('.products02 li .itemInfor p').css('line-height',(60*(width/640))/16);
		
		/* $('.floatNav .item').css('height',80*($(window).width()/640)); */	
		$('.tagList a').css('width',140*(width/640));
		$('.tagList a').css('height',56*(width/640));
		$('.tagList a').css('line-height',(56*(width/640))/15.4);		
		$('.detailImg img').css('width',640*(width/640));
		$('.redBagTit').css('height',100*(width/640));
		$('.activeList li img').css('height',325*(width/640));
		$('.activeList li:last-child').css('width',620*(width/640));
		$('.activeList li:last-child img').css('height',200*(width/640));
		$('.activeList li:last-child .actName').css('height',200*(width/640));
		$('.couponsList li img').css('height',182*(width/640));
		$('.couponsList li .imgIntro').css('height',182*(width/640));
		$('.shareStep .step .imgItem').css('height',308*(width/640));
		$('.gxui-tab').tab();
	}).resize();
	$(".touchslider").touchSlider({mouseTouch:true, autoplay:true});//幻灯片轮播图
	$('.gxui-tab').tab();
	
	//Menu 菜单导航显示隐藏 
	$('#js-header .navCon').css('display','none');
	var $$menuShow = $('#js-header').find('.mainNav');
	var $$menuShowbox = $('#js-header').find('.navCon');
	$$menuShow.find('.iMenu').toggle(
		function () {
			$(this).siblings('.iSearch').removeClass('hover');
			$(this).siblings().parent('.menuBox').parent('.topNav').parent('.mainNav').find('.navSearch').css("display","none");
			$(this).parent('.menuBox').parent('.topNav').parent('.mainNav').find('.navMenu').css("display","block");
			$(this).parent('.menuBox').parent('.topNav').parent('.mainNav').parent('.header').parent('.wrap').find('.maskBox').css("display","block");
			$(this).addClass('hover');
		},
		function () {
			$(this).parent('.menuBox').parent('.topNav').parent('.mainNav').find('.navMenu').css("display","none");
			$(this).parent('.menuBox').parent('.topNav').parent('.mainNav').parent('.header').parent('.wrap').find('.maskBox').css("display","none");
			$(this).removeClass('hover');
		}
	);	
	$$menuShow.find('.iSearch').toggle(
		function () {
			$(this).siblings('.iMenu').removeClass('hover');
			$(this).siblings().parent('.menuBox').parent('.topNav').parent('.mainNav').find('.navMenu').css("display","none");
			$(this).parent('.menuBox').parent('.topNav').parent('.mainNav').find('.navSearch').css("display","block");
			$(this).parent('.menuBox').parent('.topNav').parent('.mainNav').parent('.header').parent('.wrap').find('.maskBox').css("display","block");
			$(this).addClass('hover');
		},
		function () {
			$(this).parent('.menuBox').parent('.topNav').parent('.mainNav').find('.navSearch').css("display","none");
			$(this).parent('.menuBox').parent('.topNav').parent('.mainNav').parent('.header').parent('.wrap').find('.maskBox').css("display","none");
			$(this).removeClass('hover');
		}
	);
	$('#maskBox').click(function(){	
		$(this).css("display","none");
		$$menuShowbox.css("display","none");
		$$menuShowbox.find('i').removeClass('hover');
	})
	/*浮动按钮的显示隐藏判断*/
	$(".floatNav").css('display','none');	
	$(window).scroll(function(){	
       var y = $(window).scrollTop();
	  if ( y >0){
		 $("#js-header").addClass("posFixed");
		 $(".sidebar").addClass("sidebarPosFixed");
		  $(".main").addClass("posFixed02");
	  }
	  if ( y <0){
		   $("#js-header").removeClass("posFixed");
		   $(".sidebar").removeClass("sidebarPosFixed");
		   $(".main").removeClass("posFixed02");
		   }
		if ( y >screen.availWidth){
		 $(".floatNav").css('display','block');
	  }
	  if ( y <screen.availWidth){
		   $(".floatNav").css('display','none');
		   }
		$(".navCon").css("display","none");
		$(".icoMenu").removeClass('hover');	
	  });
	  
	  /*商品类目滚动式隐藏导航菜单*/
	$(".category-left,.category-right").scroll(function(){ 
		$(".navCon").css("display","none");
		$(".icoMenu").removeClass('hover');			
	});
	 
	  /*返回顶部*/
	 $(".floatNav .floatTop").click(function(){
		 $('body,html').stop().animate({scrollTop:0},500);
		 return false;
	});	
	/*产品列表展示形式*/
	$(".tabFilter .icoCon").toggle(
		function () {
			$(this).removeClass('selected');
			$(this).parent().parent().parent().find('#filter').hide();
		},
		function () {
			$(this).addClass('selected');
			$(this).parent().parent().parent().find('#filter').show();
		}
	);
	$(".tabFilter .icoCon").trigger('click');
	
	$(".icoModel").click(	
		function () {		
			if($(this).is(":first-child"))
			{			
				$(this).siblings(".icoModel").removeClass("selected");						
				$(this).parent().parent().parent().parent().find('#goodlist').addClass("proImg");
				$(this).addClass("selected");			
				
			}
			else
			{
			 $(this).siblings(".icoModel").removeClass("selected");		
			 $(this).parent().parent().parent().parent().find('#goodlist').removeClass("proImg");
			 $(this).addClass("selected");	
			}
		}
	);
	
	/*Pages Begin*/
    $('.gxui-pages select').live('change', function() {/*Select*/
        $(this).parent().children('span').find('b').html($(this).children('option[value="' + $(this).val() + '"]').html());
    });	
	$('.gxui-pages').each(function() {
        var self = this;
        var url = $(self).attr('url');
        var container = $(self).attr('container');
        if (url && container) {
            var srcProperty = $(self).attr('srcProperty') || 'goodsrc';
            var pagesize = $(self).attr('pagesize');
            var count = $(self).attr('count');
            container = $(container);

            $(self).page({
                template: $($(self).attr('template') || '#js-good-template').html(),
                pagesize: pagesize,
                count: count,
                url: url,
                container: container,
                pageurl: $(self).attr('pageurl'),
                callback: function(html) {
                   /*  container.lazyload({child: '[' + srcProperty + ']', srcProperty: srcProperty}); */
                }
            });
           /*  container.lazyload({child: '[' + srcProperty + ']', srcProperty: srcProperty}); */
        }
    });

	/*分享有礼*/
    /*
	$(".icoShare,.icoShare2").click(function(){
		var box=$('<div class="shareMask"><div class="shareTit">分享有礼，小伙伴们赶紧行动吧</div><div class="shareItem"><a class="item is01" href="#"></a><a class="item is02" href="#"></a><a class="item is03" href="#"></a><a class="item is04" href="#"></a><a class="item is05" href="#"></a><a class="item is06" href="#"></a></div></div>').appendTo('body').css({"z-index":"5001","position":"fixed",bottom:0});
		 $.documentMask({z:5000,id:"mask-bg"});		 
		 return false;
	});
    */
	
	$(".couponsList li .item").click(function(){
		var tag=$(this).attr("tag");		
		if(tag==1){
			var box=$('<div class="dialogTable" id="dialogTable"><div class="dialogTh bigTit">恭喜您！</div><div class="dialogBody"><p class="red">成功领取￥100.00盖惠券红包</p><p class="brown">满1000使用<br/>发行:丽宝贸易有限公司</p></div><div class="dialogBtn"><a class="btn">马上使用</a></div></div>').appendTo('body').css({"z-index":"5001","position":"fixed",bottom:"50%",left:0});
		}
		if(tag==2)
		{
		var box=$('<div class="dialogTable" id="dialogTable"><div class="dialogTh bigTit">盖惠券领取失败</div><div class="dialogBody"><p class="bigTit">下次再来吧！</p></div><div class="dialogBtn"><a class="btn" href="javascript:void(-1);">继续努力！</a></div></div>').appendTo('body').css({"z-index":"5001","position":"fixed",bottom:"50%",left:0});		
		}
		if(tag==3)
		{		
			var box=$('<div class="dialogTable" id="dialogTable"><div class="dialogTh bigTit">您已经领过盖惠券</div><div class="dialogBody"><p class="bigTit">下次再来吧！</p></div><div class="dialogBtn"><a class="btn" href="javascript:void(-1);">不能贪心哦~</a></div></div>').appendTo('body').css({"z-index":"5001","position":"fixed",bottom:"50%",left:0});	
		}
		return false;
	});	
	$(".dialogBtn .btn").click(function(){
	 $(this).hide();
	});
	
	/*12/25 产品详情页面查看地址*/
	$(".mgl5").click(function(){
		$(".checkAddress").show();
		$(".addressList").show();
	});
	
	$(".addressList1 li").click(function(){
		$(".addressList1").hide();
		$(".addressList2").show();
	});
	
	$(".addressList2 li").click(function(){
		$(".addressList2").hide();
		$(".addressList1").show();
		$(".checkAddress").hide();
		$(".addressList").hide();
	});
	/*12/25 产品详情页面查看地址--结束*/

});


