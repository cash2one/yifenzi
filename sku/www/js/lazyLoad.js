//添加事件
var EventUtil = new Object;
EventUtil.addEventHandler = function (e, t, n) {
    e.addEventListener ? e.addEventListener(t, n, !1) : e.attachEvent ? e.attachEvent("on" + t, n) : e["on" + t] = n
}
/* 
    图片延迟加载
    图片的真正地址是：data-url
    window.onload = function(){
     LAZY.init();
     LAZY.run();
    }
 */
var LAZY = LAZY || {};
LAZY=(function(){
	var pResizeTimer = null;
	var imgs={};
	function resize(){
		if(pResizeTimer) return '';
		resize_run();
	}
	function resize_run(){
		var min={};
		var max={};
		//min.Top=document.documentElement.scrollTop;
        min.Top = document.body.scrollTop + document.documentElement.scrollTop;
		min.Left=document.documentElement.scrollLeft;
		max.Top=min.Top+document.documentElement.clientHeight;
		max.Left=min.Left+document.documentElement.clientWidth;

		for(var i in imgs){
			if(imgs[i]){
				var _img=imgs[i];
				var img=document.getElementById(i);
				var width = img.clientWidth;
				var height = img.clientHeight;
				var wh=position(img);
                //最关键的地方，判断图片是否在可视区域
				if(
					(wh.Top>=min.Top && wh.Top<=max.Top && wh.Left>=min.Left && wh.Left<=max.Left)
					||
					((wh.Top+height)>=min.Top && wh.Top<=max.Top && (wh.Left+width)>=min.Left && wh.Left<=max.Left))
				{
					//img.src=_img.src;
					//alert("document.getElementById(\""+i+"\").src=\""+_img.src+"\";") ;
					//setTimeout("document.getElementById(\""+i+"\").src=\""+_img.src+"\";",100) ;

					(function(imgobj,realsrc){
						setTimeout(
							function() {imgobj.src = realsrc ;}, 100
							) ;
					})(img,_img.src) ;
					delete imgs[i];
				}

			}
		}
	}
    /* 
     获取图片位置
    */
	function position(o){
		var p={Top:0,Left:0};
		while(!!o){
			p.Top+=o.offsetTop;
			p.Left+=o.offsetLeft;
			o=o.offsetParent;
		}
		return p;
	}
	
	return {
		init:function(){
			for(var i=0;i<document.images.length;i++){
				var img = document.images[i];
				var config={};
				config.id = img.id;
				config.src = img.getAttribute('data-url');
				if(config.src && !config.id){
					config.id = encodeURIComponent(config.src) + Math.random();
					img.id = config.id;
				}
				if(!config.id || !config.src) continue;
				LAZY.push(config);
			}
            //iframe框架中的图片
			var ttiframes=document.body.getElementsByTagName("iframe");
			for(var i=0;i<ttiframes.length;i++){
				var config={};
				config.id = ttiframes[i].id;
				config.src = ttiframes[i].getAttribute('data-url');
				if(config.src && !config.id){
					config.id = encodeURIComponent(config.src) + Math.random();
					ttiframes[i].id = config.id;
				}
				if(!config.id || !config.src) continue;
				LAZY.push(config);
			}
		},
		push:function(config){
			imgs[config.id] = config;
		},
		run:function(){
			EventUtil.addEventHandler(window,'scroll',resize);
			resize_run();
			//addEventHandler(window,'resize',resize);
		}
	};
})();

