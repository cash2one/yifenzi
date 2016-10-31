$(function(){
    /*12/29 产品详情页面产品规格选择*/
    $("#selectColor").click(function(){
        $(".setMask").animate({
            bottom:"51px"
        });
        $(".setColorItem").animate({
            bottom:"51px"
        });
    });

    /*订单（购物车）编辑 12/29*/

    $(".cartTitleOK,.setMask").click(function(){
        $(".setMask").animate({
            bottom:"-100%"
        });
        $(".setColorItem,.editItem").animate({
            bottom:"-100%"
        });
    });
    
    /*编辑状态切换 2015/3/31*/
    $(".cartTitleRight").click(
        function () {     	
        	var storeId=$(this).attr('storeId');
            if($(this).hasClass('cartTitleOK'))
             {
            	$(".storeOld_"+storeId).show();
            	$(".storeEdit_"+storeId).css("display","none");
                $(this).parent().parent().parent().parent().find('.OSProducts').css("height","80px");
                $(this).removeClass('cartTitleOK');
                window.location.reload();
            }
            else{
            	$(".storeOld_"+storeId).hide();
            	$(".storeEdit_"+storeId).css("display","inline-block");

            	$(this).parent().parent().parent().parent().find('.OSProducts').css("height","110px");
                $(this).addClass('cartTitleOK');
            }
        }

    );

});