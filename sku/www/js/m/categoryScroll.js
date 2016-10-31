$(function(){
	var formHeight=$(window).height();
	var topHeight=$(".mainNav").height();
	var sidebarHeight=parseInt(formHeight)-parseInt(topHeight);
	$("#sidebar").css("height",sidebarHeight);
	$("#sidebar").css("top",topHeight);
})
