// JavaScript Document
$(document).ready(function(){
	 $('.options').hide(); //初始ul隐藏
	 $('.selectBox span').hover(function(){ //鼠标移动函数
		$(this).parent().find('ul.options').slideDown();  //找到ul.son_ul显示
		$(this).parent().find('li').hover(function(){$(this).addClass('hover')},function(){$(this).removeClass('hover')}); //li的hover效果
		$(this).parent().hover(function(){},
							   function(){
								   $(this).parent().find("ul.options").slideUp(); 
								   }
							   );
		},function(){}
		);
	 $('ul.options li').click(function(){
		$(this).parents('.selectBox').find('span').html($(this).html());
		$(this).parents('.options').find('ul').slideUp();
		});
	 }
	 );