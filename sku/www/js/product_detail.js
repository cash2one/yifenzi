// JavaScript Document

/*
 * Title: jQuery Etalage plugin
 * Author: Berend de Jong, Frique
 * Author URI: http://www.frique.me/
 * Version: 1.3.4 (20130622.1)
 */

(function(a){
	a.fn.etalage=function(b){
		var c=a.extend({
			align:"left",
			thumb_image_width:400,
			thumb_image_height:380,
			source_image_width:900,
			source_image_height:1200,
			zoom_area_width:580,
			zoom_area_height:"justify",
			zoom_area_distance:10,
			zoom_easing:true,
			click_to_zoom:false,
			zoom_element:"auto",
			show_descriptions:true,
			description_location:"bottom",
			description_opacity:0.7,
			small_thumbs:5,
			smallthumb_inactive_opacity:0.8,
			smallthumb_hide_single:true,
			smallthumb_select_on_hover:false,
			smallthumbs_position:"bottom",
			show_begin_end_smallthumb:true,
			magnifier_opacity:0.5,
			magnifier_invert:true,
			show_icon:true,
			icon_offset:20,
			hide_cursor:false,
			show_hint:false,
			hint_offset:15,
			speed:600,
			autoplay:false,
			autoplay_interval:6000,
			keyboard:true,
			right_to_left:false,
			click_callback:function(){return true},
			change_callback:function(){return true}
			},b);
			a.each(this,function(){
				var aG=a(this);
				if(aG.is("ul")&&aG.children("li").length&&aG.find("img.etalage_source_image").length)
				{
					var ad,ab,Q,I,aq,t,f,aS,aL,aw,aR=aG.attr("id"),
					aZ=Math.floor(c.speed*0.7),aC=Math.round(c.speed/100),
					ai=false,z=0,e=false,ao=true,A=false,x=0,al=0,ak=0,Y=0,X=0,aF="hori";
					if(typeof aR==="undefined"||!aR){aR="[no id]"}
					if(c.smallthumbs_position==="left"||c.smallthumbs_position==="right"){aF="vert"}
					if(typeof a.browser==="object"&&a.browser.msie)
					{
						if(a.browser.version<9){ao=false;
						if(a.browser.version<7){e=true}
					}
				}
				aG.addClass("etalage").show();
				var w=aG.children("li").addClass("etalage_thumb");
				w.first().show().addClass("etalage_thumb_active");
				var q=w.length,aJ=c.autoplay;
				if(q<2){aJ=false}
				if(c.align==="right"){aG.addClass("etalage_right")}
				a.each(w,function(a1)
				{a1+=1;var a4=a(this),j=a4.find(".etalage_thumb_image").removeAttr("alt").show(),a3=a4.find(".etalage_source_image"),a2=a4.find("a");a4.data("id",a1).addClass("thumb_"+a1);
				if(!j.length&&a3.length){a4.prepend('<img class="etalage_thumb_image" src="'+a3.attr("src")+'" />')}
else{
	if(!j.length&&!a3.length){a4.remove()}}if(a2.length){a4.find(".etalage_thumb_image").data("anchor",a2.attr("href"))}});var av=w.find(".etalage_thumb_image").css({width:c.thumb_image_width,height:c.thumb_image_height}).show();a.each(av,function(){a(this).data("src",this.src)});var aO=a('<li class="etalage_magnifier"><div><img /></div></li>').appendTo(aG),aa=aO.children("div"),h=aa.children("img");var E=a('<li class="etalage_icon">&nbsp;</li>').appendTo(aG);if(c.show_icon){E.show()}var r;if(c.show_hint){r=a('<li class="etalage_hint">&nbsp;</li>').appendTo(aG).show()
}var K,s=c.zoom_element;if(s!=="auto"&&s&&a(s).length){K=a(s).addClass("etalage_zoom_area").html('<div><img class="etalage_zoom_img" /></div>')}else{s="auto";K=a('<li class="etalage_zoom_area"><div><img class="etalage_zoom_img" /></div></li>').appendTo(aG)}var W=K.children("div"),an;if(ao){an=a('<img class="etalage_zoom_preview" />').css({width:c.source_image_width,height:c.source_image_height,opacity:0.3}).prependTo(W).show()}var aB=W.children(".etalage_zoom_img").css({width:c.source_image_width,height:c.source_image_height});
var az;if(c.show_descriptions){az=a('<div class="etalage_description'+((c.right_to_left)?" rtl":"")+'"></div>').prependTo(K)}var aQ,l,aV,u,y,aj=c.small_thumbs;if(q>1||!c.smallthumb_hide_single){aQ=a('<li class="etalage_small_thumbs"><ul></ul></li>').appendTo(aG);l=aQ.children("ul");a.each(av,function(){var i=a(this);Q=i.data("src");I=i.parents(".etalage_thumb").data("id");a('<li><img class="etalage_small_thumb" src="'+Q+'" /></li>').data("thumb_id",I).appendTo(l)});aV=l.children("li").css({opacity:c.smallthumb_inactive_opacity});
if(aj<3){aj=3}if(q>aj){if(c.show_begin_end_smallthumb){Q=av.eq(q-1).data("src");I=w.eq(q-1).data("id");a('<li class="etalage_smallthumb_first etalage_smallthumb_navtoend"><img class="etalage_small_thumb" src="'+Q+'" /></li>').data("src",Q).data("thumb_id",I).css({opacity:c.smallthumb_inactive_opacity}).prependTo(l);Q=av.eq(0).data("src");I=w.eq(0).data("id");a('<li class="etalage_smallthumb_navtostart"><img class="etalage_small_thumb" src="'+Q+'" /></li>').data("src",Q).data("thumb_id",I).css({opacity:c.smallthumb_inactive_opacity}).appendTo(l);
aV=l.children("li");aV.eq(1).addClass("etalage_smallthumb_active").css({opacity:1})}else{aV.eq(0).addClass("etalage_smallthumb_first etalage_smallthumb_active").css({opacity:1})}aV.eq(aj-1).addClass("etalage_smallthumb_last")}else{aV.eq(0).addClass("etalage_smallthumb_active").css({opacity:1})}a.each(aV,function(j){a(this).data("id",(j+1))});u=aV.children("img");y=aV.length;if(aF==="vert"){aV.addClass("vertical")}}if(c.magnifier_invert){aq=1}else{aq=c.magnifier_opacity}var aN=parseInt(w.css("borderLeftWidth"),10)+parseInt(w.css("borderRightWidth"),10)+parseInt(av.css("borderLeftWidth"),10)+parseInt(av.css("borderRightWidth"),10),Z=parseInt(w.css("marginLeft"),10)+parseInt(w.css("marginRight"),10),B=parseInt(w.css("paddingLeft"),10)+parseInt(w.css("paddingRight"),10)+parseInt(av.css("marginLeft"),10)+parseInt(av.css("marginRight"),10)+parseInt(av.css("paddingLeft"),10)+parseInt(av.css("paddingRight"),10),N=c.thumb_image_width+aN+Z+B,O=c.thumb_image_height+aN+Z+B,aE=0,P=0,ax=0,ag=0,aD=0,o=0,aH=0;
if(q>1||!c.smallthumb_hide_single){aE=parseInt(aV.css("borderLeftWidth"),10)+parseInt(aV.css("borderRightWidth"),10)+parseInt(u.css("borderLeftWidth"),10)+parseInt(u.css("borderRightWidth"),10);P=parseInt(aV.css("marginTop"),10);ax=parseInt(aV.css("paddingLeft"),10)+parseInt(aV.css("paddingRight"),10)+parseInt(u.css("marginLeft"),10)+parseInt(u.css("marginRight"),10)+parseInt(u.css("paddingLeft"),10)+parseInt(u.css("paddingRight"),10);if(aF==="vert"){aD=Math.round((O-((aj-1)*P))/aj)-(aE+ax);ag=Math.round((c.thumb_image_width*aD)/c.thumb_image_height);
o=ag+aE+ax;aH=aD+aE+ax}else{ag=Math.round((N-((aj-1)*P))/aj)-(aE+ax);aD=Math.round((c.thumb_image_height*ag)/c.thumb_image_width);o=ag+aE+ax;aH=aD+aE+ax}}var d=parseInt(K.css("borderTopWidth"),10),aA=parseInt(c.zoom_area_distance,10),J=parseInt(K.css("paddingTop"),10),T,a0;if((c.zoom_area_width-(d*2)-(J*2))>c.source_image_width){T=c.source_image_width}else{T=c.zoom_area_width-(d*2)-(J*2)}if(c.zoom_area_height==="justify"){a0=(O+P+aH)-(d*2)-(J*2)}else{a0=c.zoom_area_height-(d*2)-(J*2)}if(a0>c.source_image_height){a0=c.source_image_height
}var aX,at,v,ar;if(c.show_descriptions){aX=parseInt(az.css("borderLeftWidth"),10)+parseInt(az.css("borderRightWidth"),10);at=parseInt(az.css("marginLeft"),10)+parseInt(az.css("marginRight"),10);v=parseInt(az.css("paddingLeft"),10)+parseInt(az.css("paddingRight"),10);ar=T-aX-at-v}var aM;if(e){aM=a('<iframe marginwidth="0" marginheight="0" scrolling="no" frameborder="0" src="javascript:\'<html></html>\'"></iframe>').css({position:"absolute",zIndex:1}).prependTo(K)}var S=parseInt(aO.css("borderTopWidth"),10),aK=parseInt(w.css("borderTopWidth"),10)+parseInt(w.css("marginTop"),10)+parseInt(w.css("paddingTop"),10)+parseInt(av.css("borderTopWidth"),10)+parseInt(av.css("marginTop"),10)-S,am=av.offset().left-aG.offset().left-S;
if(c.smallthumbs_position==="left"){am=am+o+P}else{if(c.smallthumbs_position==="top"){aK=aK+aH+P}}var V=Math.round(T*(c.thumb_image_width/c.source_image_width)),R=Math.round(a0*(c.thumb_image_height/c.source_image_height)),M=aK+c.thumb_image_height-R,p=am+c.thumb_image_width-V,af=Math.round(V/2),ae=Math.round(R/2),H,C;if(c.show_hint){H=parseInt(c.hint_offset,10)+parseInt(r.css("marginTop"),10);C=parseInt(c.hint_offset,10)+parseInt(r.css("marginRight"),10);if(c.smallthumbs_position==="right"){C=C-o-P
}}if(aF==="vert"){aS=N+P+o;aG.css({width:aS,height:O})}else{aS=N;aG.css({width:aS,height:O+P+aH})}if(c.show_icon){aw={bottom:95,right:parseInt(c.icon_offset,5)};if(c.smallthumbs_position==="left"){aw.left=o+P+parseInt(c.icon_offset,10)}else{if(c.smallthumbs_position==="top"){aw.top+=aH+P}}E.css(aw)}if(c.show_hint){r.css({margin:0,top:-H,right:-C})}h.css({margin:0,padding:0,width:c.thumb_image_width,height:c.thumb_image_height});aa.css({margin:0,padding:0,width:V,height:R});
aw={margin:0,padding:0,left:(p-am)/2,top:(M-aK)/2};if(c.smallthumbs_position==="left"){aw.left="+="+o+P}else{if(c.smallthumbs_position==="top"){aw.top="+="+aH+P}}aO.css(aw).hide();W.css({width:T,height:a0});aw={margin:0,opacity:0};if(c.align==="right"&&s==="auto"){aw.left=-(T+(d*2)+(J*2)+aA)}else{if(s==="auto"){aw.left=aS+aA}}K.css(aw).hide();if(c.show_descriptions){aw={width:ar,bottom:J,left:J,opacity:c.description_opacity};if(c.description_location==="top"){aw.top=J;aw.bottom="auto"}az.css(aw).hide()
}if(q>1||!c.smallthumb_hide_single){if(aF==="vert"){aw={top:0,height:O};if(c.smallthumbs_position==="left"){w.css({left:o+P})}else{aw.marginLeft=N+P}aQ.css(aw);l.css({height:(aH*y)+(y*P)+100});u.css({width:60,height:60,marginLeft:10,marginBottom:5}).attr("height",aD);aV.css({margin:0,marginBottom:P})}else{aw={width:N};if(c.smallthumbs_position==="top"){w.css({top:aH+P})}else{aw.top=O+P}aQ.css(aw);l.css({width:(o*y)+(y*P)+100});u.css({width:60,height:60,marginLeft:10,marginBottom:5}).attr("width",ag);aV.css({margin:0,marginRight:P})}if(aF==="vert"){aL=((aH*aj)+((aj-1)*P))-O
}else{aL=((o*aj)+((aj-1)*P))-N}if(aL>0){for(ad=1;ad<=(y-1);ad=ad+(aj-1)){ab=1;for(ab;ab<=aL;ab+=1){if(aF==="vert"){aV.eq(ad+ab-1).css({marginBottom:(P-1)})}else{aV.eq(ad+ab-1).css({marginRight:(P-1)})}}}}else{if(aL<0){for(ad=1;ad<=(y-1);ad=ad+(aj-1)){ab=1;for(ab;ab<=(-aL);ab+=1){if(aF==="vert"){aV.eq(ad+ab-1).css({marginBottom:(P+1)});l.css({height:parseInt(l.css("height"),10)+1})}else{aV.eq(ad+ab-1).css({marginRight:(P+1)});l.css({width:parseInt(l.css("width"),10)+1})}}}}}}if(c.show_icon&&!c.magnifier_invert){aO.css({background:aO.css("background-color")+" "+E.css("background-image")+" center no-repeat"})
}if(c.hide_cursor){aO.add(E).css({cursor:"none"})}if(e){aM.css({width:W.css("width"),height:W.css("height")})}var ay=w.first().find(".etalage_thumb_image"),ap=w.first().find(".etalage_source_image");if(c.magnifier_invert){h.attr("src",ay.data("src")).show()}if(ao){an.attr("src",ay.data("src"))}aB.attr("src",ap.attr("src"));if(c.show_descriptions){f=ap.attr("title");if(f){az.html(f).show()}}var D=function(){if(t){clearInterval(t);t=false}};var k=function(){if(t){D()}t=setInterval(function(){au()},c.autoplay_interval)
};var U=function(){aO.stop().fadeTo(aZ,aq);E.stop().animate({opacity:0},aZ);K.stop().show().animate({opacity:1},aZ);if(c.magnifier_invert){ay.stop().animate({opacity:c.magnifier_opacity},aZ)}if(aJ){D()}};var aW=function(){aO.stop().fadeOut(c.speed);E.stop().animate({opacity:1},c.speed);K.stop().animate({opacity:0},c.speed,function(){a(this).hide()});if(c.magnifier_invert){ay.stop().animate({opacity:1},c.speed,function(){if(c.click_to_zoom){A=false}})}clearTimeout(x);if(aJ){k()}};var g=function(a3,a1){var j,a2,i=aG.find(".etalage_smallthumb_active").removeClass("etalage_smallthumb_active");
a3.addClass("etalage_smallthumb_active");aO.stop().hide();K.stop().hide();if(!a1){ai=true;i.stop(true,true).animate({opacity:c.smallthumb_inactive_opacity},aZ);a3.stop(true,true).animate({opacity:1},aZ,function(){ai=false})}aG.find(".etalage_thumb_active").removeClass("etalage_thumb_active").stop().animate({opacity:0},c.speed,function(){a(this).hide()});j=w.filter(".thumb_"+a3.data("thumb_id")).addClass("etalage_thumb_active").show().stop().css({opacity:0}).animate({opacity:1},c.speed);ay=j.find(".etalage_thumb_image");
ap=j.find(".etalage_source_image");if(c.magnifier_invert){h.attr("src",ay.data("src"))}if(ao){an.attr("src",ay.data("src"))}aB.attr("src",ap.attr("src"));if(c.show_descriptions){f=ap.attr("title");if(f){az.html(f).show()}else{az.hide()}}if(aJ){D();k()}a2=a3.data("id");if(q>=aj){a2--}ah(a2)};var G=function(a2,j,i,a1){a.each(aV,function(){var a4=a(this),a3={opacity:c.smallthumb_inactive_opacity};if(a4.data("id")===a1.data("id")){a3.opacity=1}if(aF==="vert"){a3.top="-="+a2}else{a3.left="-="+a2}a4.animate(a3,aZ,"swing",function(){if(ai){a1.addClass("etalage_smallthumb_active");
ai=false}})});g(a1,true)};var aY=function(){var a2=Y-al,a1=X-ak,j=-a2/aC,i=-a1/aC;al=al-j;ak=ak-i;if(a2<1&&a2>-1){al=Y}if(a1<1&&a1>-1){ak=X}aB.css({left:al,top:ak});if(ao){an.css({left:al,top:ak})}if(a2>1||a1>1||a2<1||a1<1){x=setTimeout(function(){aY()},25)}};var L=function(){var i;if(c.magnifier_invert){aG.find(".etalage_thumb_active").mouseleave()}if(!c.right_to_left){i=aG.find(".etalage_smallthumb_active").prev();if(!i.length){if(q>aj){F()}else{aV.last().trigger("click")}return true}}else{i=aG.find(".etalage_smallthumb_active").next();
if(!i.length){if(q>aj){ac()}else{aV.first().trigger("click")}return true}}i.trigger("click")};var au=function(){var i;if(c.magnifier_invert){aG.find(".etalage_thumb_active").mouseleave()}if(!c.right_to_left){i=aG.find(".etalage_smallthumb_active").next();if(!i.length){if(q>aj){ac()}else{aV.first().trigger("click")}return true}}else{i=aG.find(".etalage_smallthumb_active").prev();if(!i.length){if(q>aj){F()}else{aV.last().trigger("click")}return true}}i.trigger("click")};var n=function(a2){if(q<=aj||!c.show_begin_end_smallthumb){a2=a2-1
}var a6=aV.eq(a2);if(a6.length&&!ai){var a5=aG.find(".etalage_smallthumb_active"),a1=a5.data("id")-1,j;if(a1>a2){z=a1-a2;var a3=aG.find(".etalage_smallthumb_first"),a7=a3.data("id");if(a2<a7){j=a1-a7;z=z-j;a3.trigger("click")}else{g(a6,false)}}else{if(a1<a2){z=a2-a1;var a4=aG.find(".etalage_smallthumb_last"),i=a4.data("id")-1;if(a2>=i){j=i-a1-1;z=z-j;a4.trigger("click")}else{g(a6,false)}}}}};window[aR+"_previous"]=function(){L()};window[aR+"_next"]=function(){au()};window[aR+"_show"]=function(i){n(i)
};var aI=function(i){if(!c.click_callback(i,aR)){return false}if(typeof etalage_click_callback==="function"){etalage_click_callback(i,aR);return false}return true};var ah=function(i){if(c.change_callback(i,aR)){if(typeof etalage_change_callback==="function"){etalage_change_callback(i,aR)}}};w.add(aO).add(E).mouseenter(function(){if(c.show_hint){r.hide()}if(!c.click_to_zoom||A){U()}}).mouseleave(function(){aW()});var aU=-(c.source_image_width-T),aT=-(c.source_image_height-a0);w.add(aO).add(E).mousemove(function(a5){var j=Math.round(a5.pageX-ay.offset().left+am),i=Math.round(a5.pageY-ay.offset().top+aK);
var a4=(j-af),a3=(i-ae);if(a4<am){a4=am}if(a4>p){a4=p}if(a3<aK){a3=aK}if(a3>M){a3=M}aO.css({left:a4,top:a3});if(c.magnifier_invert){var a2=a4-am,a1=a3-aK;h.css({left:-a2,top:-a1})}Y=-((a4-am)*(1/(c.thumb_image_width/c.source_image_width)));X=-((a3-aK)*(1/(c.thumb_image_height/c.source_image_height)));if(Y<aU){Y=aU}if(X<aT){X=aT}if(c.zoom_easing){clearTimeout(x);aY()}else{if(ao){an.css({left:Y,top:X})}aB.css({left:Y,top:X})}});function aP(a2){z=(z)?z-1:0;ai=true;var a1=aG.find(".etalage_smallthumb_first").removeClass("etalage_smallthumb_first");
var i=aG.find(".etalage_smallthumb_last").removeClass("etalage_smallthumb_last");var a3,j,a5,a4;if(a2==="left"){a3=a1.prev().addClass("etalage_smallthumb_first");j=i.prev().addClass("etalage_smallthumb_last");a5=a1}else{a3=a1.next().addClass("etalage_smallthumb_first");j=i.next().addClass("etalage_smallthumb_last");a5=i}if(z){if(a2==="left"){a3.trigger("click")}else{j.trigger("click")}}else{a4=(aF==="vert")?a3.position().top:a3.position().left;G(a4,a3,j,a5)}}function m(a5){ai=true;var a1=aG.find(".etalage_smallthumb_first").removeClass("etalage_smallthumb_first");
var i=aG.find(".etalage_smallthumb_last").removeClass("etalage_smallthumb_last");var a2,j,a4;if(a5==="end"){a2=aV.eq(y-aj).addClass("etalage_smallthumb_first");j=aV.eq(y-1).addClass("etalage_smallthumb_last");a4=j;if(j.hasClass("etalage_smallthumb_navtostart")){a4=j.prev()}}else{a2=aV.eq(0).addClass("etalage_smallthumb_first");j=aV.eq(aj-1).addClass("etalage_smallthumb_last");a4=a2;if(a2.hasClass("etalage_smallthumb_navtoend")){a4=a2.next()}}var a3=(aF==="vert")?a2.position().top:a2.position().left;
G(a3,a2,j,a4)}function ac(){m("start")}function F(){m("end")}if(q>1||!c.smallthumb_hide_single){aV.click(function(){var a7=a(this),a3,j=0,a5=false,a2,a8,a4,a6,a1;if(!a7.hasClass("etalage_smallthumb_active")&&(!ai||z)){if(a7.hasClass("etalage_smallthumb_first")&&a7.prev().length){aP("left")}else{if(a7.hasClass("etalage_smallthumb_navtoend")){F()}else{if(a7.hasClass("etalage_smallthumb_last")&&a7.next().length){aP("right")}else{if(a7.hasClass("etalage_smallthumb_navtostart")){ac()}else{if(z&&!a(this).next().length){F();
return true}else{if(z&&!a(this).prev().length){ac();return true}}g(a7,false)}}}}}});if(c.smallthumb_select_on_hover){aV.mouseenter(function(){a(this).trigger("click")})}}if(c.click_to_zoom){w.click(function(){A=true;U()})}else{aO.click(function(){var i=ay.data("anchor");if(i){if(aI(i)){window.location=i}}})}if(q>1&&c.keyboard){a(document).keydown(function(i){if(i.keyCode===39||i.keyCode==="39"){if(!c.right_to_left){au()}else{L()}}if(i.keyCode===37||i.keyCode==="37"){if(!c.right_to_left){L()}else{au()
}}})}a(window).bind("load",function(){w.css({"background-image":"none"});K.css({"background-image":"none"});if(ao){ao=false;an.remove()}});if(aJ){k()}}});return this}})(jQuery);

/*左侧商品分类*/
function SDMenu(id) {
	if (!document.getElementById || !document.getElementsByTagName)
		return false;
	this.menu = document.getElementById(id);
	this.submenus = this.menu.getElementsByTagName("li");
	this.remember = true;
	this.speed = 5;
	this.markCurrent = true;
	this.oneSmOnly = true;
}
SDMenu.prototype.init = function() {
	var mainInstance = this;
	for (var i = 0; i < this.submenus.length; i++)
		this.submenus[i].getElementsByTagName("span")[0].onclick = function() {
			mainInstance.toggleMenu(this.parentNode);
		};
	if (this.markCurrent) {
		var links = this.menu.getElementsByTagName("a");
		for (var i = 0; i < links.length; i++){
			links[0].className="current";

			links[i].onclick = function() {
				var link_t = document.getElementsByTagName("a");
				for (var i = 0; i < link_t.length; i++){
					if(link_t[i].className== "current"){
						link_t[i].className='';
					}
				}
				this.className = "current";
		}
			//if (links[i].href == sampleframe.location.href) {
			//	links[i].className = "current";
			//	break;
			//}
		}
	}
	if (this.remember) {
		var regex = new RegExp("sdmenu_" + encodeURIComponent(this.menu.id) + "=([01]+)");
		var match = regex.exec(document.cookie);
		if (match) {
			var states = match[1].split("");
			for (var i = 0; i < states.length; i++)
				this.submenus[i].className = (states[i] == 0 ? "collapsed" : "");
		}
	}
};
SDMenu.prototype.toggleMenu = function(submenu) {
	if (submenu.className == "collapsed")
		this.expandMenu(submenu);
	else
		this.collapseMenu(submenu);
};
SDMenu.prototype.expandMenu = function(submenu) {
	var fullHeight = submenu.getElementsByTagName("span")[0].offsetHeight;
	var links = submenu.getElementsByTagName("a");
	for (var i = 0; i < links.length; i++)
		fullHeight += links[i].offsetHeight;
	var moveBy = Math.round(this.speed * links.length);

	var mainInstance = this;
	var intId = setInterval(function() {
		var curHeight = submenu.offsetHeight;
		var newHeight = curHeight + moveBy;
		if (newHeight < fullHeight)
			submenu.style.height = newHeight + "px";
		else {
			clearInterval(intId);
			submenu.style.height = "";
			submenu.className = "";
			mainInstance.memorize();
		}
	}, 30);
	this.collapseOthers(submenu);
};
SDMenu.prototype.collapseMenu = function(submenu) {
	var minHeight = submenu.getElementsByTagName("span")[0].offsetHeight;
	var moveBy = Math.round(this.speed * submenu.getElementsByTagName("a").length);
	var mainInstance = this;
	var intId = setInterval(function() {
		var curHeight = submenu.offsetHeight;
		var newHeight = curHeight - moveBy;
		if (newHeight > minHeight)
			submenu.style.height = newHeight + "px";
		else {
			clearInterval(intId);
			submenu.style.height = "";
			submenu.className = "collapsed";
			mainInstance.memorize();
		}
	}, 30);
};
SDMenu.prototype.collapseOthers = function(submenu) {
	if (this.oneSmOnly) {
		for (var i = 0; i < this.submenus.length; i++)
			if (this.submenus[i] != submenu && this.submenus[i].className != "collapsed")
				this.collapseMenu(this.submenus[i]);
	}
};
SDMenu.prototype.expandAll = function() {
	var oldOneSmOnly = this.oneSmOnly;
	this.oneSmOnly = true;
	for (var i = 0; i < this.submenus.length; i++)
		if (this.submenus[i].className == "collapsed")
			this.expandMenu(this.submenus[i]);
	this.oneSmOnly = oldOneSmOnly;
};
SDMenu.prototype.collapseAll = function() {
	for (var i = 0; i < this.submenus.length; i++)
		if (this.submenus[i].className != "collapsed")
			this.collapseMenu(this.submenus[i]);
};
SDMenu.prototype.memorize = function() {
	if (this.remember) {
		var states = new Array();
		for (var i = 0; i < this.submenus.length; i++)
			states.push(this.submenus[i].className == "collapsed" ? 0 : 1);
		var d = new Date();
		d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
		document.cookie = "sdmenu_" + encodeURIComponent(this.menu.id) + "=" + states.join("") + "; expires=" + d.toGMTString() + "; path=/";
	}
};

/*商品详情Fixed 2014-06-04*/
$(window).scroll(function() {
    var y = $(window).scrollTop();
    if (y > 960) {
        $('.buyQuickly').css('display', 'inline-block');
        $("#menuTab").addClass('posFixed');
    }
    if (y < 960) {
        $('.buyQuickly').css('display', 'none');
        $("#menuTab").removeClass('posFixed');
    }
});


/*选择城市*/
$(function() {
    $("#store-selector").mouseover(function() {
        $(this).attr('class', 'hover');
    }).mouseleave(function() {
        $(this).removeClass('hover');
    });
    //点击省份，显示相应的城市
    $(".area-list .province").click(function() {
        $(".area-list .cities").hide();
        $("#province_" + $(this).attr("data-value")).show();
    });
    //鼠标离开省份，隐藏城市
    $("#stock_province_item").mouseleave(function() {
        $(".area-list .cities").hide();
    });
});