/**
 * 卖家平台，商品规格属性的选择 / 验证输入
 */
$(function () {
    //显示库存配置时候，总库存不可以手动修改
    if($("#stock_setting:visible").length){
        $('#Goods_stock').attr('readonly', 'readonly').css('background', '#E7E7E7 none');
    }
    checkShowColorTable();
    //可自定义图片的属性点击，显示图片上传
    $(".spec_type_img .spec_checkbox input").click(function () {
        var tr_node = "#col_img_table tr[data-type='file_tr_" + $(this).attr("data-id") + "']";
        if (this.checked) {
            $(tr_node).show();
        } else {
            $(tr_node).hide();
        }
        checkShowColorTable();
    });
    //显示已经选择上传的图片
    $('#col_img_table').find('input[type="file"]').live('change', function () {
        $(this).parent().parent().find('.spec-img').attr('src', getFullPath($(this)[0])).show();
    });
    //所有规格的点击选择
    $(".spec_checkbox input").click(function () {
        //1.自定义属性名称
        var pv = $(this).parents('li').find('.spec_name');
        if (typeof(pv.find('input').val()) == 'undefined') {
            $(this).val(pv.html());
            pv.html('<input type="text" maxlength="20" value="' + pv.text() + '" />');
        } else {
            pv.html(pv.find('input').val());
            $(this).val(pv.html());
        }
        var tr_node = "#col_img_table tr[data-type='file_tr_" + $(this).attr("data-id") + "']";
        $(tr_node).find("span.pvname").html(pv.html());
        $(tr_node).find("input").attr("name", pv.html());


        //2.显示库存配置
        var checkedBoxes = $(".spec_checkbox input:checked");
        var type = countSelectType(checkedBoxes);
        //2.2每种属性都有被选择的时候，显示库存配置
        if (type.length == $(".spec_group").size()) {
            //将选择的属性，放入二维数组
            var specArray = [];
            for (var j = 0; j < type.length; j++) {
                specArray[j] = []; //将每一个子元素又定义为数组
                for (var n = 0; n < checkedBoxes.size(); n++) {
                    var key = $(checkedBoxes[n]).attr('data-key');
                    if (key == j) {
                        specArray[j].push(checkedBoxes[n]);
                    }
                }
            }
            //组合库存、价格配置
            var combinationSpec = combination(specArray);
            var tr = '';

            for (var i = 0; i < combinationSpec.length; i++) {
                tr += '<tr>';
                var input_id = 'i_';
                //多种规格
                if ($(".spec_group").size() > 1) {
                    for (j = 0; j < combinationSpec[i].length; j++) {
                        input_id += $(combinationSpec[i][j]).attr('data-id');
                    }
                    for (j = 0; j < combinationSpec[i].length; j++) {
                        var data_id = $(combinationSpec[i][j]).attr('data-id');
                        var spec_input = '<input type="hidden" value="' + combinationSpec[i][j].value + '" name="spec[' + input_id + '][sp_value][' + data_id + ']">';
                        tr += '<td>' + combinationSpec[i][j].value + spec_input + '</td>';
                    }
                } else {
                    //一种
                    input_id += $(combinationSpec[i]).attr('data-id');
                    var data_id = $(combinationSpec[i]).attr('data-id');
                    var spec_input = '<input type="hidden" value="' + combinationSpec[i].value + '" name="spec[' + input_id + '][sp_value][' + data_id + ']">';
                    tr += '<td>' + combinationSpec[i].value + spec_input + '</td>';
                }
                tr += '<td style="display: none" >' +
                '<input type="text" style="display:none;" value="0.00"  name="spec[' + input_id + '][price]" class="text">' +
                '</td><td>' +
                '<input type="text" value="" class="text spec_stock" name="spec[' + input_id + '][stock]" class="text">' +
                '</td><td>' +
                '<input type="text" value=""  name="spec[' + input_id + '][sku]" class="text">' +
                '</td>';
                tr += '</tr>';
            }
            $("#spec_table").html(tr);
            $("#stock_setting").show();
        } else {
            $("#spec_table").html('');
            $("#stock_setting").hide();
            $('#Goods_stock').removeAttr('readonly', 'readonly').css('background', '');
        }


    });
    // 计算商品库存
    $('input.spec_stock').live('change', function () {
        stock_sum();
    });

    /**
     * 验证输入
     */
    $("#submitBtn").click(function () {
        //获取编辑器内容赋值给content，用于ajax验证
        $("#Goods_content").val(editor_Goods_content.getContent());
        //验证 库存配置
        var inputs = $(".spec_table input.spec_stock");
        for (var i = 0; i < inputs.length; i++) {
            if (inputs.get(i).value.length == 0) {
                alert(commonData.skuTips);
                inputs.get(i).focus();
                return false;
            }
        }
        var checkedBoxes = $(".spec_checkbox input:checked");
        var type = countSelectType(checkedBoxes);
        if (type.length > 0 && type.length != $(".spec_group").size()) {
            alert(commonData.selectTips);
            checkedBoxes[0].focus();
            return false;
        }
        return true;
    });
    //运输方式
    $("#Goods_freight_payment_type input").click(function () {
        $("#templateSelect").hide();
        $("#Goods_freight_template_id").val('');
    });
    $("#Goods_freight_payment_type_2").click(function () {
        $("#templateSelect").show();
    });
    //价格输入
    $("#Goods_market_price,#Goods_gai_price,#Goods_price,#Goods_discount,#Goods_sign_integral").keyup(function () {
        if (isNaN(this.value))
            this.value = null;
    });
    $(".spec_table input[data_type='price']").live('keyup', function () {
        if (isNaN(this.value))
            this.value = null;
    });
    $(".spec_table input.spec_stock").live('keyup', function () {
        if (!/^[\d]+$/.test(this.value))
            this.value = null;
    });

});


/**
 * 获取本地文件全路径，兼容火狐浏览器
 * @param obj
 * @returns {*}
 */
function getFullPath(obj) {
    if (obj) {
        //ie
        if (window.navigator.userAgent.indexOf("MSIE") >= 1) {
            obj.select();
            if (window.navigator.userAgent.indexOf("MSIE") == 25) {
                obj.blur();
            }
            return document.selection.createRange().text;
        }
        //firefox
        else if (window.navigator.userAgent.indexOf("Firefox") >= 1) {
            if (obj.files) {
                //return obj.files.item(0).getAsDataURL();
                return window.URL.createObjectURL(obj.files.item(0));
            }
            return obj.value;
        }
        return obj.value;
    }
}

/**
 * 递归组合
 *               [a1,b1]
 *   [a1,a2]     [a1,b2]
 *   [b1,b2]  => [a2,b1]
 *               [a2,b2]
 * @param array
 * @returns {*}
 */
function combination(array) {
    var size = array.length;
    if (size == 0) {
        return array;
    } else if (size == 1) {
        return typeof array[0] == 'object' ? array[0] : [];
    } else {
        var result = [];
        var a = array[0];
        var tmp = array.slice(0); //克隆数组，因为js的数组是通过类似C之类的“指针”的方式来操作的
        tmp.shift();
        for (var i = 0; i < a.length; i++) {
            var b = combination(tmp);
            for (var j = 0; j < b.length; j++) {
                if (typeof b[j] == 'object') {
                    result.push([a[i]].concat(b[j]));
                } else {
                    result.push([a[i], b[j]]);
                }
            }
        }
        return result;
    }
}

/**
 * 计算商品库存
 */
function stock_sum() {
    var stock = 0;
    $('input.spec_stock').each(function () {
        if ($(this).val() != '') {
            stock += parseInt($(this).val());
        }
    });
    $('#Goods_stock').attr('readonly', 'readonly').css('background', '#E7E7E7 none').val(stock);
}

/**
 * 检查是否显示自定义颜色规格图片上传
 */
function checkShowColorTable() {
    if ($(".spec_type_img .spec_checkbox input:checked").size() == 0) {
        $("#col_img_table").hide();
    } else {
        $("#col_img_table").show();
    }
}
/**
 * 计算有多少类属性被选择了，返回数组
 * @param checkedBoxes
 * @returns {Array}
 */
function countSelectType(checkedBoxes) {
    var tmp = [];
    var type = [];
    //2.1 计算有多少类属性被选择了
    checkedBoxes.each(function () {
        var n = $(this).attr('data-key');
        tmp[n] = n;
    });
    for (var i = 0; i < tmp.length; i++) {
        if (tmp[i] != undefined) type.push(tmp[i]);
    }
    return type;
}

