// JavaScript Document
$(document).ready(function() {
    //宸﹁竟鑿滃崟
    var navmain = $(".actionGroup h3")
    navmain.eq(0).addClass("hover");
    navmain.eq(0).next('.ctx').show();
    $(".actionGroup h3").click(function(i) {
        $(this).addClass("hover").siblings().removeClass("hover")
        $(this).next('.ctx').show(800).siblings('.ctx').hide(500);
    });

    //鍒濆鍖�
    var bodyWidth = $(window).width();
    var bodyHeight = $(window).height();
    var dbbody = $("#dBody");
    var ws = bodyWidth - 250;
    var hs = bodyHeight - 130;
    dbbody.width(ws).height(hs)
    if (bodyWidth <= 800) {
        $('.nav').addClass('nav2')
    }

    //闅愯棌渚ф爮   
    $(".bar-hs").click(function() {
        $("#dLeft").animate({left: "-300px"}, "slow").hide();
        $(".bar-hs2").fadeIn(1000).css({display: "block"});
        var bodyWidth = $(window).width();
        dbbody.width(bodyWidth - 17);

    });

    //鐩戝惉娴忚鍣�
    $(window).resize(function() {
        var bodyHeight = $(window).height();
        var bodyWidth = $(window).width();
        var ws = bodyWidth - 250;
        var temp = $("#dLeft").is(":hidden");
        dbbody.height(bodyHeight - 130)
        if (temp) {
            dbbody.width(bodyWidth - 20);
        } else {
            dbbody.width(ws);

        }

        if (bodyWidth >= 800) {
            $('.nav').removeClass('nav2')
        }

        if (bodyWidth <= 800) {
            $('.nav').addClass('nav2')
        }

    });

    //鏄剧ず渚ф爮
    $(".bar-hs2").click(function() {
        $("#dLeft").animate({left: "+0px"}, "slow").show();
        $(".bar-hs2").hide();
        var bWidth = $(window).width();
        var wss = bWidth - 250;
        dbbody.width(wss);
    });


});