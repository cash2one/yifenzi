$(function(){
	/*是否显示登录密码*/
	$(".LFBut").click(function(){
		var num=$(this).attr("num");
        var value = $(this).prev().val();
		if(num==1){
			$(this).attr("num","2");
			$(this).css("background","url(../../../images/m/bg/m_but1.png) no-repeat");
			$(this).css("background-size","100% 100%");
            $(this).prev().remove();
            $(this).before('<input id="LoginForm_password" name="LoginForm[password]" type="text" class="inputTxt bbot" value="'+ value+'"/>');
		}
		if(num==2){
			$(this).attr("num","1");
			$(this).css("background","url(../../../images/m/bg/m_but2.png) no-repeat");
			$(this).css("background-size","100% 100%");
			$(this).prev().remove();
            $(this).before('<input id="LoginForm_password" name="LoginForm[password]" type="password" class="inputTxt bbot" value="'+ value+'"/>');
		}
	});

    $(".LFBut2").click(function(){
        var num=$(this).attr("num");
        var value = $(this).prev().val();
        if(num==1){
        $(this).attr("num","2");
        $(this).css("background","url(../../../images/m/bg/m_but1.png) no-repeat");
        $(this).css("background-size","100% 100%");
        $(this).prev().remove();
        $(this).before('<input id="Member_password" name="Member[password]"type="text" class="inputTxt bbot" value="'+ value+'"/>');
        }
    if(num==2){
        $(this).attr("num","1");
        $(this).css("background","url(../../../images/m/bg/m_but2.png) no-repeat");
        $(this).css("background-size","100% 100%");
        $(this).prev().remove();
        $(this).before('<input id="Member_password" name="Member[password]" type="password" class="inputTxt bbot" value="'+ value+'"/>');
        }
    });
	/*结束*/
	
	/*我的订单菜单样式切换*/
	$(".orderNav ul li a").click(function(){
		$(".orderNav ul li a img").removeClass("orderNavSelected");
		$(this).find("img").addClass("orderNavSelected");
	})
	/*结束*/
	
	/*选择颜色样式切换*/
    /*
	$(".ColorList span").click(function(){
		$(".ColorList span").removeClass("SelectColorItem");
		$(this).addClass("SelectColorItem");
	});*/
	/*结束*/
	
	/*选择收货人地址样式切换*/
	$(".addressList li").click(function(){
        if($(this).find('.OSProducts').hasClass("addressSel")){
            $(this).find('.OSProducts').removeClass("addressSel");
        }else{
            $(this).find('.OSProducts').addClass('addressSel');
            $(this).siblings().find('.OSProducts').removeClass('addressSel');
        }
        addDefault();//添加默认收货地址
	});
	/*结束*/
	
	/*是否默认收货地址样式切换*/
	$(".AFDefault").click(function(){
		var num=$(this).attr("num");
		if(num==1){
			$(this).attr("num","2");
			$(this).addClass("AFDefaultSel");
			$("#Address_default").val(1);
		}
		if(num==2){
			$(this).attr("num","1");
			$(this).removeClass("AFDefaultSel");
			$("#Address_default").val(0);
		}
		
	});
	/*结束*/
	
	/*购物车全选样式切换*/
	$(".cartQSel").click(function(){
		var num=$(".cartQSelTotal").attr("num");
		if(num==1){
			$(".cartQSelTotal").attr("num","2");
			$(".cartQSelTotal").css("background","url(../../../images/m//bg/m_ioc13.png) no-repeat");
			$(".cartQSelTotal").css("background-size","100% 100%");
			$("input[id^='cartQSel_']").attr('name','cart_goods[]');
		}
		if(num==2){
			$(".cartQSelTotal").attr("num","1");
			$(".cartQSelTotal").css("background","url(../../../images/m/bg/m_ioc14.png) no-repeat");
			$(".cartQSelTotal").css("background-size","100% 100%");
			$("input[id^='cartQSel_']").attr('name','');
		}
		/*计算购物车选中要结算物品的数量 */
		var cartQSelTotal=$(".cartList").find(".cartQSelTotal");
		var CNum=0;
        var total_amount = 0;//商品初始总金额为0
		for(var i=0;i<cartQSelTotal.length;i++){
			if(cartQSelTotal.eq(i).attr("num")==2){
                  var price = cartQSelTotal.eq(i).next().find('span.price').text().substring(1);
                  var quantity = cartQSelTotal.eq(i).next().find('span.quantity').text().substring(0);
                  total_amount += parseFloat(price) * parseInt(quantity);
                  //alert(total_amount);
				CNum++;
			}
		}
		CountNum(CNum);
        CountPrice(total_amount);
		/*计算购物车选中要结算物品的数量结束*/
	});
	/*结束*/
	
	/*购物车模块全选样式切换*/
	$(".cartModuleSel").click(function(){
		var num=$(this).parent().parent().parent().parent().parent().find(".cartQSelTotal").attr("num");
		if(num==1){
			$(this).parent().parent().parent().parent().parent().find(".cartQSelTotal").attr("num","2")
			$(this).parent().parent().parent().parent().parent().find(".cartQSelTotal").css("background","url(../../../images/m/bg/m_ioc13.png) no-repeat");
			$(this).parent().parent().parent().parent().parent().find(".cartQSelTotal").css("background-size","100% 100%");
			var data=$(this).attr('data_cart');
			$("input[id^='cartQSel_" + data + "']").attr('name','cart_goods[]');
		}
		if(num==2){
			$(this).parent().parent().parent().parent().parent().find(".cartQSelTotal").attr("num","1");
			$(this).parent().parent().parent().parent().parent().find(".cartQSelTotal").css("background","url(../../../images/m/bg/m_ioc14.png) no-repeat");
			$(this).parent().parent().parent().parent().parent().find(".cartQSelTotal").css("background-size","100% 100%");
			var data=$(this).attr('data_cart');
			$("input[id^='cartQSel_"+data+"']").attr('name','');
		}
		/*计算购物车选中要结算物品的数量 */
		var cartQSelTotal=$(".cartList").find(".cartQSelTotal");
		var CNum=0;
        var total_amount = 0;//商品初始总金额为0
		for(var i=0;i<cartQSelTotal.length;i++){
			if(cartQSelTotal.eq(i).attr("num")==2){	
                var price = cartQSelTotal.eq(i).next().find('span.price').text().substring(1);
                var quantity = cartQSelTotal.eq(i).next().find('span.quantity').text().substring(0);
                total_amount += parseFloat(price) * parseInt(quantity);
				CNum++;
			}
		}
		CountNum(CNum);
        CountPrice(total_amount);
		/*计算购物车选中要结算物品的数量结束*/
	});
	/*结束*/
	
	/*购物车单个选中样式切换*/
	$(".cartThisSel").click(function(){
		var num=$(this).attr("num");
		var goodsid=$(this).attr('data_goodid');
		if(num==1){
			$(this).attr("num","2");
			$(this).css("background","url(../../../images/m/bg/m_ioc13.png) no-repeat");
			$(this).css("background-size","100% 100%");
			$("input[id$='_"+goodsid+"']").attr('name','cart_goods[]');
		}
		if(num==2){
			$(this).attr("num","1");
			$(this).css("background","url(../../../images/m/bg/m_ioc14.png) no-repeat");
			$(this).css("background-size","100% 100%");
			$("input[id$='_"+goodsid+"']").attr('name','');
		}
		/*计算购物车选中要结算物品的数量 */
		var cartQSelTotal=$(".cartList").find(".cartQSelTotal");
		var CNum=0;
        var total_amount = 0;//商品初始总金额为0
		for(var i=0;i<cartQSelTotal.length;i++){
			if(cartQSelTotal.eq(i).attr("num")==2){
                var price = cartQSelTotal.eq(i).next().find('span.price').text().substring(1);
                var quantity = cartQSelTotal.eq(i).next().find('span.quantity').text().substring(0);
                total_amount += parseFloat(price) * parseInt(quantity);
				CNum++;
			}
		}
		CountNum(CNum);
        CountPrice(total_amount);
		/*计算购物车选中要结算物品的数量结束*/
	});
	/*结束*/
	
	/*结算数量*/
	function CountNum(CNum){
		$(".cartBtn").val();
		$(".cartBtn").val("结算("+CNum+")");	
	}

   function CountPrice(str){
       var new_price = '￥' + str.toFixed(2);
       $('#totalPrice').text(new_price);
   }
   
   /*订单评价星星选中样式*/
	$(".OAItemMainLeft img").click(function(){
		var num=$(this).attr("num");
		var imgList=$(this).parent().find("img");
		$(this).parent().find("img").attr("src","../../../images/m/bg/m_ioc17.png");
		for(var i=0;i<num;i++){
			imgList.eq(i).attr("src","../../../images/m/bg/m_ioc16.png")
		}
		if(parseInt(num)==5){
			$(this).parent().parent().find(".OAItemMainRight").text("超好，五星好评");
			$(this).parent().parent().find(".OAItemMainRight").addClass("OAItemColor1");
		}
		if(parseInt(num)==4){
			$(this).parent().parent().find(".OAItemMainRight").text("不错，还行");
			$(this).parent().parent().find(".OAItemMainRight").removeClass("OAItemColor1");
		}
		if(parseInt(num)<=3){
			$(this).parent().parent().find(".OAItemMainRight").text("一般");
			$(this).parent().parent().find(".OAItemMainRight").removeClass("OAItemColor1");
		}
	});

   /*支付页面支付方式选择*/
	$(".paymentTotal").click(function(){
		$(".cartThisSel").css("background","url(../../../images/m/bg/m_ioc14.png) no-repeat");
		$(".cartThisSel").css("background-size","100% 100%");
		$(this).find(".cartThisSel").css("background","url(../../../images/m/bg/m_ioc13.png) no-repeat");
		$(this).find(".cartThisSel").css("background-size","100% 100%");
		var paytype=$(this).find(".cartThisSel").attr("paytype");
		$("#OrderForm_payType").val(paytype);
	});
	/*用户中心帮助展示效果*/
	$(".mebHelp").click(function(){
		$("#mebHelp").toggle();
	});
	$(".mebHelpClose").click(function(){
		$("#mebHelp").toggle();
	});
	/*用户中心帮助展示效果结束*/
})
