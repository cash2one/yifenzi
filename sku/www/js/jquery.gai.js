/* 
========================================
	@name 	:	盖网首页脚本
	@author	:	盖网
	@data	:	2013.5.11
========================================
*/

// 插件集合
jQuery.easing["jswing"]=jQuery.easing["swing"];jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(x,t,b,c,d){return jQuery.easing[jQuery.easing.def](x,t,b,c,d)},easeOutCirc:function(x,t,b,c,d){return c*Math.sqrt(1-(t=t/d-1)*t)+b},easeOutQuad:function(x,t,b,c,d){return-c*(t/=d)*(t-2)+b},easeOutBounce:function(x,t,b,c,d){if((t/=d)<1/2.75){return c*7.5625*t*t+b}else if(t<2/2.75){return c*(7.5625*(t-=1.5/ 2.75) * t + .75) + b} else if (t < 2.5 /2.75){return c*(7.5625*(t-=2.25/2.75)*t+.9375)+b}else{return c*(7.5625*(t-=2.625/2.75)*t+.984375)+b}}});(function($){$.fn.extend({imgChange:function(o){o=$.extend({thumbObj:null,botPrev:null,botNext:null,effect:"fade",curClass:"act",thumbOverEvent:true,speed:400,autoChange:true,clickFalse:false,changeTime:5e3,delayTime:0,showTxt:false,visible:1,start:0,steps:1,circular:false,vertical:true,fqwidth:30,easing:"swing"},o||{});var _self=$(this);var _p=_self.parent();var _pp=_p.parent();var thumbObj;var size=_self.size();var nowIndex=0;var index;var startRun;var delayRun;var _img=_self.find("img");var b=false,animCss=o.vertical?"top":"left",sizeCss=o.vertical?"height":"width";var i;var g=o.vertical?_self.outerHeight(true):_self.outerWidth(true);if(o.showTxt){_p.after("<i class='bg'></i><a class='txt' href='"+_img.eq(0).parent().attr("href")+"'>"+_img.eq(0).attr("alt")+"</a>")}if(o.effect=="scroll"||o.effect=="wfScroll"){var v=o.visible;if(size<=v)return false;if(o.circular){if(o.effect=="scroll"){_p.prepend(_self.slice(size-v-1+1).clone()).append(_self.slice(0,v).clone());o.start+=v}else{_p.prepend(_self.clone())}}var f=_p.children(),itemLength=f.size(),curr=o.start,h=g*itemLength,j=g*v,scrollSize=g*size;f.css({overflow:"hidden","float":o.vertical?"none":"left",width:_self.width(),height:_self.height()});_p.css({margin:0,padding:0,position:"relative",listStyle:"none",overflow:"hidden",zoom:1,zIndex:1}).css(sizeCss,h+"px").css(animCss,-(curr*g));_pp.css({visibility:"visible",overflow:"hidden",position:"relative",zIndex:2,left:0}).css(sizeCss,j+"px")}else if(o.effect=="accordion"){_p.css(!o.vertical?{width:g+(size-1)*o.fqwidth+"px",overflow:"hidden"}:{height:g+(size-1)*o.fqwidth+"px",overflow:"hidden"});_self.css(!o.vertical?{width:o.fqwidth+"px","float":"left",overflow:"hidden"}:{height:o.fqwidth+"px","float":"none",overflow:"hidden"}).eq(0).addClass("act").animate(!o.vertical?{width:g+"px"}:{height:g+"px"},800,o.easing).end().click(function(){index=_self.index($(this));fadeAB();if(o.clickFalse){return false}});if(o.thumbOverEvent){_self.hover(function(){index=_self.index($(this));delayRun=setTimeout(fadeAB,o.delayTime)},function(){clearTimeout(delayRun)})}}else if(o.effect=="wb"){_p.css({position:"relative"});_pp.css({height:g*o.visible,overflow:"hidden",position:"relative"})}else{_self.hide().eq(0).show()}if(o.thumbObj&&!o.circular){thumbObj=$(o.thumbObj);thumbObj.removeClass(o.curClass).eq(0).addClass(o.curClass);thumbObj.click(function(){index=thumbObj.index($(this));fadeAB();if(o.clickFalse){return false}});if(o.thumbOverEvent){thumbObj.hover(function(){index=thumbObj.index($(this));delayRun=setTimeout(fadeAB,o.delayTime)},function(){clearTimeout(delayRun)})}}if(o.botNext){$(o.botNext).click(function(){if(_self.queue().length<1){runNext()}return false})}if(o.thumbObj&&o.circular)$.each($(o.thumbObj),function(i,a){$(a).mouseover(function(){$(o.thumbObj).removeClass(o.curClass).eq(i).addClass(o.curClass);return go(o.visible+i)})});if(o.botPrev){$(o.botPrev).click(function(){if(o.effect=="scroll"&&o.circular){return go(curr-o.steps)}else{if(_self.queue().length<1){index=(nowIndex+size-1)%size;fadeAB()}return false}})}if(o.effect=="wfScroll"){startRun=setInterval(marquee,o.changeTime);_pp.hover(function(){clearInterval(startRun)},function(){startRun=setInterval(marquee,o.changeTime)})}else if(o.autoChange){startRun=setInterval(runNext,o.changeTime);_p.add(thumbObj).add(o.botPrev).add(o.botNext).hover(function(){clearInterval(startRun)},function(){startRun=setInterval(runNext,o.changeTime)})}function fadeAB(){if(nowIndex!=index){if(o.thumbObj){$(o.thumbObj).removeClass(o.curClass).eq(index).addClass(o.curClass)}if(size<=index){nowIndex=index;return false}if(o.speed<=0){_self.eq(nowIndex).hide().end().eq(index).show()}else if(o.effect=="fade"){_self.stop(true,true).eq(nowIndex).fadeOut(o.speed).end().eq(index).fadeIn(o.speed)}else if(o.effect=="scroll"){_p.stop(true,true).animate(!o.vertical?{left:-(index*g)}:{top:-(index*g)},o.speed,o.easing)}else if(o.effect=="cutIn"){_self.css({zIndex:1,display:"block"}).stop(true,true).eq(nowIndex).css({zIndex:5,opacity:2}).end().eq(index).css({zIndex:6,top:"-"+g+"px"}).animate({opacity:1,top:0},o.speed,o.easing)}else if(o.effect=="alternately"){_self.css({display:"none"}).stop(true,true).eq(nowIndex).css({zIndex:10,display:"block"}).animate(!o.vertical?{left:"-"+g/2+"px"}:{top:"-"+g/2+"px"},o.speed,function(){$(this).css({zIndex:5}).animate(!o.vertical?{left:0}:{top:0},o.speed)}).end().eq(index).css({display:"block"}).animate(!o.vertical?{left:g/2+"px"}:{top:g/2+"px"},o.speed,function(){$(this).css({zIndex:10}).animate(!o.vertical?{left:0}:{top:0},o.speed)})}else if(o.effect=="accordion"){_self.stop(true,true).eq(nowIndex).removeClass("act").animate(!o.vertical?{width:o.fqwidth+"px"}:{height:o.fqwidth+"px"},o.speed,o.easing).end().eq(index).addClass("act").animate(!o.vertical?{width:g+"px"}:{height:g+"px"},o.speed,o.easing)}else if(o.effect=="wb"){_p.stop(true,true).animate({top:g+g/4+"px"},o.speed,function(){_p.children().last().prependTo(_p);_p.children().first().hide();_p.css({top:0}).children().first().fadeIn(800)})}else{_self.stop(true,true).eq(nowIndex).css({zIndex:10}).slideUp(o.speed).end().eq(index).css({zIndex:5}).slideDown(o.speed)}if(o.showTxt){var _txt=_img.eq(index).attr("alt");var _url=_img.eq(index).parent().attr("href");_p.siblings(".txt").html(_txt).attr("href",_url)}nowIndex=index}}function marquee(){if(o.vertical){if(_pp.scrollTop()>=scrollSize){_pp.scrollTop(_pp.scrollTop()-scrollSize+o.steps)}else{i=_pp.scrollTop();i+=o.steps;_pp.scrollTop(i)}}else{if(_pp.scrollLeft()>=scrollSize){_pp.scrollLeft(_pp.scrollLeft()-scrollSize+o.steps)}else{i=_pp.scrollLeft();i+=o.steps;_pp.scrollLeft(i)}}}function go(a){if(size<=o.steps)return false;if(!b){if(o.beforeStart)o.beforeStart.call(this,vis());if(o.circular){if(a<=o.start-v-1){_p.css(animCss,-((itemLength-v*2)*g)+"px");curr=a==o.start-v-1?itemLength-v*2-1:itemLength-v*2-o.steps}else if(a>=itemLength-v+1){_p.css(animCss,-(v*g)+"px");curr=a==itemLength-v+1?v+1:v+o.steps}else curr=a}else{if(a<0||a>itemLength-v)return;else curr=a}b=true;_p.animate(animCss=="left"?{left:-(curr*g)}:{top:-(curr*g)},o.speed,o.easing,function(){if(o.afterEnd)o.afterEnd.call(this,vis());b=false})}return false}function vis(){return f.slice(curr).slice(0,v)}function runNext(){index=(nowIndex+1)%size;if(o.effect=="scroll"&&o.circular){return go(curr+o.steps)}else{fadeAB()}}function css(a,b){return parseInt($.css(a[0],b))||0}}})})(jQuery);


/*== 模拟滚动条 ==*/
(function(a){a.tiny=a.tiny||{};a.tiny.scrollbar={options:{axis:"y",wheel:40,scroll:true,lockscroll:true,size:"auto",sizethumb:"auto",invertscroll:false}};a.fn.tinyscrollbar=function(d){var c=a.extend({},a.tiny.scrollbar.options,d);this.each(function(){a(this).data("tsb",new b(a(this),c))});return this};a.fn.tinyscrollbar_update=function(c){return a(this).data("tsb").update(c)};function b(q,g){var k=this,t=q,j={obj:a(".viewport",q)},h={obj:a(".overview",q)},d={obj:a(".scrollbar",q)},m={obj:a(".track",d.obj)},p={obj:a(".thumb",d.obj)},l=g.axis==="x",n=l?"left":"top",v=l?"Width":"Height",r=0,y={start:0,now:0},o={},e="ontouchstart" in document.documentElement;function c(){k.update();s();return k}this.update=function(z){j[g.axis]=j.obj[0]["offset"+v];h[g.axis]=h.obj[0]["scroll"+v];h.ratio=j[g.axis]/h[g.axis];d.obj.toggleClass("disable",h.ratio>=1);m[g.axis]=g.size==="auto"?j[g.axis]:g.size;p[g.axis]=Math.min(m[g.axis],Math.max(0,(g.sizethumb==="auto"?(m[g.axis]*h.ratio):g.sizethumb)));d.ratio=g.sizethumb==="auto"?(h[g.axis]/m[g.axis]):(h[g.axis]-j[g.axis])/(m[g.axis]-p[g.axis]);r=(z==="relative"&&h.ratio<=1)?Math.min((h[g.axis]-j[g.axis]),Math.max(0,r)):0;r=(z==="bottom"&&h.ratio<=1)?(h[g.axis]-j[g.axis]):isNaN(parseInt(z,10))?r:parseInt(z,10);w()};function w(){var z=v.toLowerCase();p.obj.css(n,r/d.ratio);h.obj.css(n,-r);o.start=p.obj.offset()[n];d.obj.css(z,m[g.axis]);m.obj.css(z,m[g.axis]);p.obj.css(z,p[g.axis])}function s(){if(!e){p.obj.bind("mousedown",i);m.obj.bind("mouseup",u)}else{j.obj[0].ontouchstart=function(z){if(1===z.touches.length){i(z.touches[0]);z.stopPropagation()}}}if(g.scroll&&window.addEventListener){t[0].addEventListener("DOMMouseScroll",x,false);t[0].addEventListener("mousewheel",x,false);t[0].addEventListener("MozMousePixelScroll",function(z){z.preventDefault()},false)}else{if(g.scroll){t[0].onmousewheel=x}}}function i(A){a("body").addClass("noSelect");var z=parseInt(p.obj.css(n),10);o.start=l?A.pageX:A.pageY;y.start=z=="auto"?0:z;if(!e){a(document).bind("mousemove",u);a(document).bind("mouseup",f);p.obj.bind("mouseup",f)}else{document.ontouchmove=function(B){B.preventDefault();u(B.touches[0])};document.ontouchend=f}}function x(B){if(h.ratio<1){var A=B||window.event,z=A.wheelDelta?A.wheelDelta/120:-A.detail/3;r-=z*g.wheel;r=Math.min((h[g.axis]-j[g.axis]),Math.max(0,r));p.obj.css(n,r/d.ratio);h.obj.css(n,-r);if(g.lockscroll||(r!==(h[g.axis]-j[g.axis])&&r!==0)){A=a.event.fix(A);A.preventDefault()}}}function u(z){if(h.ratio<1){if(g.invertscroll&&e){y.now=Math.min((m[g.axis]-p[g.axis]),Math.max(0,(y.start+(o.start-(l?z.pageX:z.pageY)))))}else{y.now=Math.min((m[g.axis]-p[g.axis]),Math.max(0,(y.start+((l?z.pageX:z.pageY)-o.start))))}r=y.now*d.ratio;h.obj.css(n,-r);p.obj.css(n,y.now)}}function f(){a("body").removeClass("noSelect");a(document).unbind("mousemove",u);a(document).unbind("mouseup",f);p.obj.unbind("mouseup",f);document.ontouchmove=document.ontouchend=null}return c()}}(jQuery));

/*线下活动九宫图展示*/
$(document).ready(function(){
						   
	//$(".nineBox").find(".pre").hide();初始化为第一版
	var page=1;//初始化当前的版面为1
	var $show=$(".nineBox").find(".sliderBox");//找到图片展示区域
//	var page_count=$show.find("ul").length;
	var $width_box=$show.parents(".nineBox").width();//找到图片展示区域外围的div
	//显示title文字
	$show.find("li").hover(function(){
		$(this).find(".title").show();								
	},function(){
		$(this).find(".title").hide();
	})
	// 隐藏所有工具提示
	$(".sliderBox li").each(function(){
		$(".sliderBox li .title", this).css("opacity", "0");
	});
	
	$(".sliderBox li").hover(function(){ // 悬浮 
		$(this).stop().fadeTo(500,1).siblings().stop().fadeTo(500,0.2);
		$(".sliderBox li .title", this).stop().animate({opacity:1,bottom:"0px"},300);
	},function(){ // 寻出
		$(this).stop().fadeTo(500, 1).siblings().stop().fadeTo(500,1);	
		$(".sliderBox li .title", this).stop().animate({opacity:0,bottom:"-30px"},300);
	});
						   
});

/*================= 首页大屏焦点图  ===========================*/
(function ($) { 
	$.fn.gai = function (options) {
		  var defaultVal = { 
		  Time:1000,     //默认时间
		  ShowsTime:700 //切换过度时间
	}; 

	//工厂 
	var obj = $.extend(defaultVal, options); 
	return this.each(function () {
			var selObject = $(this);
			var len = selObject.find('li').length; 
			var index = 0;
			function tw(){
				var  win =$( window).width()
				if(win<=1200 ){
				  $('.gwSlide').width(1200)
				}else{
					 $('.gwSlide').width(win)
					}
				}
				
			$(window).resize(function(e) {
               tw(); 
            });

			       var btn = "<div class='btnGa'>";
					for(var i=0; i < len; i++) {
						btn += "<span></span>";
					}
					btn += "</div>";
					selObject.append(btn);
                    selObject.find('.btnGa').find('span').eq(0).addClass('cur')
					   
				$(".btnGa span").mouseenter(function() {
						index = $(".btnGa span").index(this);
					    Fadeinbox(index);
					});
				selObject.find('ul').find('li').css({'position':'absolute',"opacity":0 }).hide();;
				selObject.find('ul').find('li').eq(0).css({"opacity":1}).show();
				
				 function setTimes(){
						  picTimer = setInterval(function() {
							  //自动淡入淡出
									  index ++;
									  if(index == len) {index = 0;}
									  Fadeinbox(index);	
							   },obj.Time);		   
				 }
				 setTimes();
				 $(selObject).hover(function(){
							clearInterval(picTimer);
							},function(){
								setTimes();
				});	 		
			   function Fadeinbox(index) {
				  selObject.find('ul').find('li').eq(index).stop().animate({"opacity":1,'z-index':1},obj.ShowsTime,function(){
				  selObject.find('ul').find('li').eq(index).siblings().hide();
				  }).show();
				  selObject.find('ul').find('li').eq(index).siblings().stop().animate({"opacity":0,'z-index':0},obj.ShowsTime);
				  selObject.find('.btnGa').find('span').eq(index).addClass('cur').siblings().removeClass('cur')
				  
				 }
			  $('.mainBox .advMore a').bind("mouseenter", function(){
					  $(this).siblings().stop().animate({opacity:0.5},1000);
			  }); 
			  $('.mainBox .advMore a').bind("mouseleave", function(){
					 $(this).siblings().stop().animate({opacity:1},1000);
			  
			  }); 			   		
	
}); 
} 
})(jQuery); //闭包




/*================= 首页  ===========================*/
$( document).ready(function(e) {
    toCart();       //首页商品列表加入购物车按钮
	toCity();       //城市选择
	toActivity();   //线下活动
	toProduct();    //商品列表分类
    backTop();      //返回顶部
});

/*================= 返回顶部  ===========================*/
function  backTop() {
	  /*$(window).scroll(function(){
       var y = $(window).scrollTop();
	  if ( y > 300){
	     $(".back—top").show(500);
	  }
	  if ( y <300){
		  $(".back—top").hide(500);
	  }
	  });*/
 $(".back—top .backTop").click(function(){
     $('body,html').stop().animate({scrollTop:0},500);
     return false;
 });
 
 if ($.browser.msie && ($.browser.version == "6.0") && !$.support.style) {
	  var tobblck = $(".back—top");
	  function ie6(){
		  var w = $(window).width();
		  var h = $(window).height();
		  var ht = tobblck.height();
		  var scrollTop = $(window).scrollTop();
		  var i=h-(ht+10);
		  tobblck.stop().animate({ top: scrollTop + i });
		  }
	     ie6();
	  $(window).resize(function(){
          ie6();
	  });
	  $(window).scroll(function () {
	       ie6(); 
	  });		   
}
};


//城市选择
function toCity(){
$('.topBar .city').hover(function(e) {
$('.topBar .city .cityName').addClass('cityhover');
$('.topBar .city .cityList').show();
},function(){
$('.topBar .city .cityName').removeClass('cityhover');
$('.topBar .city .cityList').hide();
});
}

/*线下活动类别切换效果*/
function setTab(name,cursel,n){
for(i=1;i<=n;i++){
var menu=document.getElementById(name+i);
var con=document.getElementById("con_"+name+"_"+i);
menu.className=i==cursel?"curr":"";
con.style.display=i==cursel?"block":"none";
}
}



//商品列表分类
function toProduct(){
	$(".productBox .con .category").each(function() {
	$( this).find('li').last().css({"border-bottom":"1px solid #fff"});  
	});
    
	//商品切换
	$(".productBox").each(function() {
		 var obj = $(this);
		 var stbox = obj.find('.category').find('li');
		 var showBox = obj.find('.itemize');
		 
         //切换鼠标当前商品页  
		 
		$(stbox).hover(function() {
			  $(this).siblings().stop().animate({opacity:0.8},1000);
			  $(this).parents('ul').css({"background":"#333"})
			 /* var n=$(stbox).index(this);
			  $(showBox).show().prev('.proList').hide();
			  $(obj).find('.itemize .boxShow').eq(n).show().siblings('.boxShow').hide();*/
			  },function() {
				  $(this).parents('ul').css({"background":"#fff"})
			  $(this).siblings().stop().animate({opacity:1},1000);   
		});
		
		//关闭商品列表
		$(obj).mouseleave(function(e) {
				$(showBox).hide().prev('.proList').show();  
        });
	});
}

//加入购物车效果
function toCart(){
	var Box=$('.productBox').find('li');
	$(Box).live('hover', function() {
	$(this).find('.toCart').stop().toggle();
	});
	
   $(document).find('.Carfly').live('click', function() {
		  var thisTop = $(this).siblings('.img_m').find('img').offset().top; 
		  var thisLeft = $(this).siblings('.img_m').find('img').offset().left; 
		  var imgWidth = $(this).siblings('.img_m').find('img').width();
		  var imgheight = $(this).siblings('.img_m').find('img').height();
		  var carTop = $('.back—top a:last').offset().top;
		  var carleft = $('.back—top a:last').offset().left;
		
		  $('body').append("<div class='carfly'></div");
		  $(this).siblings('.img_m').find('img').clone().prependTo('.carfly');
		  $('.carfly').css({"position":"absolute", "left":thisLeft, "top":thisTop,"width":imgWidth,"height":imgheight,"z-index":9998,"opacity":1,"border":"2px solid #C30"})
		  $('.carfly img').css({"width":"100%","height":"100%"})
		  $('.carfly').stop().animate({
			  top:carTop+10,
			  left:carleft-60,
			  width:35,
			  height:35,
			  opacity:0.8
			  },600,
		  function(){
			  $('.carfly').stop().animate({
			  top:carTop+10,
			  left:carleft
			  },600,
		  function(){
			  $( this).hide().remove();
			  //var num=$('.back—top .num');
			  //var i=num.text();   //获取当前商品数量
			  //i++;
			  //num.text(i);
			  // alert("成功加入购物车，您的当前购物车里有"+i+"件商品");
		  } 
	)
  })
});	
}



/*================= 首页大屏焦点图 @zyx ===========================*/
(function ($) { 
	$.fn.gaiAdv = function (options) {
		  var defaultVal = { 
		  Time:10,     // 显示时间
		  boxW:1200,   // 广告宽度
		  boxH:500     // 广告高度
	}; 

	//工厂 
	var obj = $.extend(defaultVal, options); 
	return this.each(function () {
			var BOX = $(this);
			var W = $(window).width();
			var H = $(window).height(); 
			var L_box = (obj.boxW)/2;
			var T_box = (obj.boxH)/2;
            var i = obj.Time;
			var n;
			var scrollTop = $(window).scrollTop();
			
	        //初始化样式
			$(BOX).css({"width":obj.boxW,"height":obj.boxH,"top":-obj.boxH,"margin-left":-L_box});
			$(BOX).find('.con').css({"width":obj.boxW,"height":obj.boxH})
			$(BOX).prepend("<a href='javascript:;' title='关闭' class='close_adv'></a><span class='time'>"+obj.Time+"</span>")
			 
			 //判断ie6
			if ($.browser.msie && ($.browser.version == "6.0") && !$.support.style) {
					var scrollTop = $(window).scrollTop();
					var H = $(window).height();
					var T_box = (obj.boxH)/2;
					var n = ((H/2)-T_box)+scrollTop;
					var T_box = 0;
					toAdv_show(n);
				$(window).scroll(function () {
					var scrollTop = $(window).scrollTop();
					var H = $(window).height();
					var T_box = (obj.boxH)/2;
					var n = ((H/2)-T_box)+scrollTop;
					var T_box = 0;
					$(BOX).css({"top":n,"margin-left":-L_box,"margin-top":0});
			     })
			}else{
			   var n = '50%';
			   toAdv_show(n);
			};
			
			//关闭广告
			function toAdv_closed(){
				$(BOX).animate({"top":-obj.boxH},500,"easeInQuint",function() {
				$(BOX).hide();
			});
			}
			
			//显示广告
			function toAdv_show(n){
				$(BOX).show();
				$(BOX).animate({"top":n,"margin-left":-L_box,"margin-top":-T_box},800,"easeOutBack",function() {
				var times = setInterval(function(){  
				i--;    
				if(i > 0) {    
				   $(BOX).find('.time').text(i);     
				} else {    
				   toAdv_closed();
				}}, 1000);    

				})
			}
			
			//点击关闭
			$(BOX).find('.close_adv').click(function(e) {
                toAdv_closed();
            });
			
			
			
//==			
}); 
} 
})(jQuery); //闭包










