/*tab 切换 所有涉及到tab切换的请套用此方法*/
function setTab(name,cursel,n){
	for(i=1;i<=n;i++){
		var menu=$("#"+name+i);
		var con=$("#tabCon_"+name+"_"+i);
		if(i==cursel){
			$("#"+name+i).addClass("curr");
			$("#tabCon_"+name+"_"+i).css({"display":'block'});
		}else{
			$("#"+name+i).removeClass("curr");
			$("#tabCon_"+name+"_"+i).css({"display":'none'});
		}
	}
}

/*鍔犲叆璐墿杞�*/
$(document).find('.Carfly').live('click', function() {
	var thisImg = $(this).siblings('.img').find('img');
	if (!thisImg.length) {
		thisImg = $(this).parent().find('img');
	}
	if (!thisImg.length)
		return false;
	var thisTop = thisImg.offset().top;
	var thisLeft = thisImg.offset().left;
	var imgWidth = thisImg.width();
	var imgheight = thisImg.height();
	var carTop = $('.backTop .bL a:last').offset().top;
	var carleft = $('.backTop .bL a:last').offset().left;

	$('body').append("<div class='carfly'></div>");
	thisImg.clone().prependTo('.carfly');
	$('.carfly').css({"position": "absolute", "left": thisLeft, "top": thisTop, "width": imgWidth, "height": imgheight, "z-index": 9998, "opacity": 1, "border": "2px solid #C30"})
	$('.carfly img').css({"width": "100%", "height": "100%"})
	$('.carfly').stop().animate({
		top: carTop + 10,
		left: carleft - 60,
		width: 35,
		height: 35,
		opacity: 0.8
	}, 600,
		function() {
			$('.carfly').stop().animate({
				top: carTop + 10,
				left: carleft
			}, 600,
					function() {
						$(this).hide().remove();
						var num = $('.backTop .num,.mycart #cartNum');
						var i = parseInt($(num.get(0)).text());   //鑾峰彇褰撳墠鍟嗗搧鏁伴噺
						i++;
						num.text(i);
					}
			)
		})
});


