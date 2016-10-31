/**
 * jsonp 获取购物车信息，网站右上角并设置相关显示
 */
function getCartInfo() {
    $.ajax({
        url:commonVar.loadCartUrl,
        dataType:'jsonp',
        jsonp:"callBack",
        jsonpCallback:"jsonpCallback",
        success:function(data){
            $("#cartNum").html(data.num);
            $("#mian_botom_cartcount").html(data.num);
            $(".cartList").html(data.cart);
        }
    });
}
//删除购物车
function deleteCart(store_id, spec_id, goods_id) {
    $.ajax({
        url:commonVar.deleteCartUrl,
        dataType:'jsonp',
        jsonp:"callBack",
        jsonpCallback:"jsonpCallback",
        data:{store_id:store_id, spec_id:spec_id, goods_id:goods_id},
        success:function(data){
            if(!data.done) console.log(data);
            getCartInfo();
        }
    });
}
//立即删除本商品在购物车的显示
$("#myCart .doDel").live('click',function(){
    if($("#cartList .reload").length ==0){  //添加提示
        $("#cartList").append(commonVar.reloadTips);
    }
    $(this).parent().parent().remove();
});

//添加到购物车
function addCart(gid, sid){
    $.ajax({
        url:commonVar.addCartUrl,
        dataType:'jsonp',
        jsonp:"callBack",
        jsonpCallback:"jsonpCallback",
        data:{quantity:1, spec_id:sid, goods_id:gid},
        success:function(data){
            if(data.error) alert(data.error);
        }
    });
}

$(document).ready(function () {
    getCartInfo();
    //鼠标悬停获取购物车信息，悬停半秒才显示
    $("#myCart").hover(function () {
        myCartShowST = setTimeout(function () {
            if (!$("#myCart").hasClass('has_load')) getCartInfo();
            $('#cartList').stop().show();
            $("#myCart").addClass('has_load')
        }, 500);
    }, function () {
        $('#cartList').stop().hide();
        $("#myCart").removeClass('has_load');
        clearTimeout(myCartShowST);
    });
});