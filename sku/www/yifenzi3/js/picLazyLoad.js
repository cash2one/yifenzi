/**
 * Zepto picLazyLoad Plugin
 * ximan http://ons.me/484.html
 * 20140517 v1.0
 */

$('img').each(function(i){
	//$(this).attr('data-original',$(this).attr('src'));
	$(this).attr('src','/yifenzi/images/load.gif');
});

;(function($){
	$.fn.picLazyLoad=function(settings){
		var $this=$(this),
			_winScrollTop=0,
			_winHeight=$(window).height();
		settings=$.extend({
			threshold:-100,
			placeholder:'/yifenzi/images/load.gif'
		},settings||{});
		lazyLoadPic();
		$(window).on('scroll',function(){
			_winScrollTop=$(window).scrollTop();
			lazyLoadPic();
		});
		function lazyLoadPic(){
			$this.each(function(){
				var $self=$(this);
				if($self.is('img')){
					if($self.attr('data-original')){
						var _offsetTop=$self.offset().top;
						if((_offsetTop-settings.threshold)<=(_winHeight+_winScrollTop)){
							$self.attr('src',$self.attr('data-original'));
							$self.removeAttr('data-original');
						}
					}
				}
				else{
					if($self.attr('data-original')){
						if($self.css('background-image')=='none'){
							$self.css('background-image','url('+settings.placeholder+')');
						}
						var _offsetTop=$self.offset().top;
						if((_offsetTop-settings.threshold)<=(_winHeight+_winScrollTop)){
							$self.css('background-image','url('+$self.attr('data-original')+')');
							$self.removeAttr('data-original');
						}
					}
				}
			});
		}
	}
})(Zepto);
