// 分类选择
function selectClass(obj) {
    $("#commodityspan").hide();
    $("#commoditydt").show();
    $("#commoditydd").show();
    $(obj).siblings('li').children('a').attr('class', '');
    $(obj).children('a').attr('class', 'selected');
    //隐藏上次的选择
    var currentTab = $(obj).parent().parent().attr('id');
    if (currentTab == 'class_div_1') {
        $("#class_div_2").children('ul').empty();
        $("#class_div_3").children('ul').empty();
    } else if (currentTab == 'class_div_2') {
        $("#class_div_3").children('ul').empty();
    }
    toNextClass(obj.id, currentTab);
}
/**
 * 根据数据，格式化Li html
 * @param data
 * @returns {*}
 */
function liHtml(data,id) {
    var a = '';
    for (var i = 0; i < data.length; i++) {
        var selected = '';
        if(id==data[i].id) selected = 'class="selected"';
        a += '<li onclick="selectClass(this);" id="' + data[i].id + '|' + data[i].type_id + '">' +
            '<a href="javascript:void(0)" data-id="' + data[i].id + '" '+selected+'>' + data[i].name + '</a></li>';
    }
    return a;
}
/**
 * 分类选择显示
 * @param text
 * @param currentTab
 */
function toNextClass(text, currentTab) {
    var valarray = text.split('|');
    $('#class_id').val(valarray[0]);
    $('#t_id').val(valarray[1]);
    $('#dataLoading').show();//显示加载中
    $.getJSON(commonData.getJsonUrl, {'cid': valarray[0]},
        function (data) {
            var str = "";
            if (data != null) {
                $('#button_next_step').attr('disabled', true);
                var class_div_id = parseInt(currentTab.match(/\d/)) + 1;
                var a = liHtml(data,0);
                $('#class_div_' + class_div_id).parents('.wp_category_list').removeClass('blank');
                for (j = class_div_id; j <= 4; j++) {
                    $('#class_div_' + (j + 1)).parents('.wp_category_list').addClass('blank');
                }
                $('#class_div_' + class_div_id).children('ul').empty().append(a).nextAll('div').children('ul').empty();
                $.each($('a[class=selected]'), function (i) {
                    str += $(this).html() + "&nbsp;&gt;&nbsp;";
                });
                str = str.substring(0, str.length - 16);
                $('#commoditydd').html(str);
                $('#commoditya').hide();	//添加到常用分类
                $('#dataLoading').hide();
            } else {
                $.each($('a[class=selected]'), function (i) {
                    str += $(this).html() + "&nbsp;&gt;&nbsp;";
                });
                str = str.substring(0, str.length - 16);
                $('#commoditydd').html(str);
                $('#commoditya').show();	//添加到常用分类
                disabledButton();
                $('#dataLoading').hide();
            }
        }
    );
}
function disabledButton() {
    if ($('#class_id').val() != '') {
        $('#button_next_step').attr('disabled', false).css('cursor', 'pointer');
    } else {
        $('#button_next_step').attr('disabled', true).css('cursor', 'auto');
    }
}

// ajax添加常用分类
$('#commoditya > a').unbind().click(function () {
    $.getJSON(commonData.addCategoryStapleUrl, {cid: $('#class_id').val(), name: $("#commoditydd").text()}, function (data) {
        if (data.done) {
            var li = '<li data-id="' + data.id + '">' +
                '<a href="#" style="color:#3366cc" data-cid="' + data.category_id + '" class="selectStaple">' + $("#commoditydd").text() + '</a> --- ' +
                '<a href="#" class="delStaple" >' + commonData.delText + '</a></li>';
            $('#categoryStaple > ul').append(li);
        } else {
            alert(data.msg);
        }
    });
});
// 常用分类选择
$('.selectStaple').die().live('click', function () {
    $('#dataLoading').show();//显示加载中
    $('.wp_category_list').addClass('blank');
    var cid = $(this).attr('data-cid');
    $.getJSON(commonData.selectStapleUrl, {cid: cid}, function (data) {
        if (data.done) {
            $('#class_div_2,#class_div_3').children('ul').empty();
            $('a[class=selected]').attr('class','');
            $("a[data-id='"+data.class_one+"']").attr("class","selected");
            if (data.one.length > 0) {
                $('#class_div_2').children('ul').append(liHtml(data.one, data.class_two)).parents('.wp_category_list').removeClass('blank');
            }
            if (data.two.length > 0) {
                $('#class_div_3').children('ul').append(liHtml(data.two, data.class_three)).parents('.wp_category_list').removeClass('blank');
            }
            $('#t_id').val(data.type_id);
        } else {
            $('.wp_category_list').css('background', '#E7E7E7 none');
            $(this).parent().css({'background': '#3399FD', 'color': '#FFF'});
        }
    });
    $('#dataLoading').hide();
    $('#class_id').val(cid);
    $("#commodityspan").hide();
    $("#commoditydt").show();
    $('#commoditydd').show().html($(this).text());
    $('#commSelect').val($(this).text());
    disabledButton();
    $('#commListArea').hide();
    $('#commoditya').hide();
    return false;
});

// ajax删除常用分类
$('.delStaple').die().live('click', function () {
    li = $(this).parent();
    $.getJSON(commonData.delStapleUrl, {id: li.attr('data-id')}, function (data) {
        if (data.done) {
            li.remove();
        } else {
            alert(data.msg);
        }
    });
    return false;
});