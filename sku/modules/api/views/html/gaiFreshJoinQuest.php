<!doctype html>
<html>
<head>
    <title>盖鲜生商户入驻申请</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords" content="盖鲜生"/>
    <meta name="description" content="盖鲜生"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-cale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta content="telephone=no" name="format-detection"/>
    <link type="text/css" rel="stylesheet" href="<?php echo DOMAIN ?>/quest/styles/mmodule.css"/>
    <script src="<?php echo DOMAIN ?>/quest/js/jquery-2.1.1.min.js"></script>
    <script src="<?php echo DOMAIN ?>/quest/js/area.js"></script>
    <script src="<?php echo DOMAIN ?>/quest/js/areadata.js"></script>
    <script src="<?php echo DOMAIN ?>/quest/js/jquery-migrate-1.2.1.min.js"></script>
    <script>
        $(function (){
            initComplexArea('selectprov', 'selectcity', 'selectdistrict', area_array, sub_array, '0', '0', '0');
            //商品选中其他
            $("#product-list .checkbox").click(function(){
                if($(this).prop("checked")==true){
                    if($(this).hasClass("others")){
                        $(this).parent().siblings(".input-box").removeClass("hide");
                    }
                }else{
                    if($(this).hasClass("others")){
                        $(this).parent().siblings(".input-box").val("").addClass("hide");
                    }
                }
            })
        });

        //得到地区码
        function getAreaID(){
            var area = 0;
            alert(222);
            if($("#selectdistrict").val() != "0"){
                area = $("#selectdistrict").val();
            }else if ($("#selectcity").val() != "0"){
                area = $("#selectcity").val();
            }else{
                area = $("#selectprov").val();
            }
            return area;
        }
        //根据地区码查询地区名
        function getAreaNamebyID(areaID){
            var areaName = "";
            if(areaID.length == 2){
                areaName = area_array[areaID];
            }else if(areaID.length == 4){
                var index1 = areaID.substring(0, 2);
                areaName = area_array[index1] + " " + sub_array[index1][areaID];
            }else if(areaID.length == 6){
                var index1 = areaID.substring(0, 2);
                var index2 = areaID.substring(0, 4);
                areaName = area_array[index1] + " " + sub_array[index1][index2] + " " + sub_arr[index2][areaID];
            }
            return areaName;
        }
    </script>
</head>
<body>
<div class="wrap" id="wrap">
    <div class="container">
        <div class="header clearfix">
            <a href="#" class="logo" title="盖鲜生">
                <img src="<?php echo DOMAIN ?>/quest/images/bg/logo.png" alt="盖鲜生Logo">
            </a>
            <div class="slogan">
                <img src="<?php echo DOMAIN ?>/quest/images/bg/slogan.png" alt="新鲜触手可及">
            </div>
        </div>
        <div class="introduction">
            <img src="<?php echo DOMAIN ?>/quest/images/bg/introduction-02.png" alt="盖鲜生商户入驻申请">
        </div>

        <div class="form-header">
            <img src="<?php echo DOMAIN ?>/quest/images/bg/title-02.png" alt="想加入盖鲜生开拓自己的事业吗？请告诉我们！">
        </div>
        <div class="form-row">
            <h3>您所在城市：</h3>
            <div class="address-box">
                <div class="city-box">
                    <select name="province" id="selectprov" onChange="changeComplexProvince(this.value, sub_array, 'selectcity', 'selectdistrict');" placeholder="选择省份"></select>
                    <select name="city" id="selectcity" onChange="changeCity(this.value,'selectdistrict','selectdistrict');" disabled="disabled"></select>
                    <span id="selectdistrict_div"><select name="district" id="selectdistrict"  disabled="disabled"></select></span>
                </div>
                <div class="address-detail">
                    <input type="text" id="address-detail" class="input-box" placeholder="请输入您的详细地址">
                </div>
            </div>
        </div>
        <div id="product-list"  class="form-row">
            <h3>您所提供的商品（多选）：</h3>
            <div class="options product-list">
                <label>
                    <input type="checkbox" class="checkbox goodsName"  name="productList" value="蔬菜">
                    <span>蔬菜</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="productList" value="水果">
                    <span>水果</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="productList" value="肉类">
                    <span>肉类</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="productList" value="家禽">
                    <span>家禽</span>
                </label>
            </div>
            <div class="options product-list">
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="productList" value="水产">
                    <span>水产</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="productList" value="海鲜">
                    <span>海鲜</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="productList" value="搭配菜品">
                    <span>搭配菜品</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="productList" value="鲜奶乳品">
                    <span>鲜奶乳品</span>
                </label>
            </div>
            <div class="options product-list">
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="productList" value="粮油副食">
                    <span>粮油副食</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="productList" value="各地特产">
                    <span>各地特产</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="productList" value="快捷速食">
                    <span>快捷速食</span>
                </label>
            </div>
            <div class="options">
                <label><input type="checkbox" class="checkbox goodsName others" name="productList" select="true" value="其他"><span>其他</span></label>
                <input type="text" id = "input_goods" class="input-box hide" placeholder="请输入您所提供的商品">
            </div>
        </div>
        <div class="form-row contact">
            <h3>提供联系方式，盖鲜生专员会尽快与您联系！！</h3>
            <div class="contact-box">
                <div class="user-box">
                    <input type="text" class="input-box userName" onblur="checkName(this)" placeholder="请输入您的姓名" maxlength="20">
                </div>
                <div class="phone-box">
                    <input type="text" class="input-box mobile" onblur ="checkMobile(this)" placeholder="请输入您的手机号码" maxlength="14">
                </div>
            </div>
        </div>
        <div class="ta-c">
            <input type="submit"  class="submit-btn" value="">
        </div>

        <div class="footer">
            <a href="tel://4006206899">
                <img src="<?php echo DOMAIN ?>/quest/images/bg/tel-line.png" alt="盖鲜生合作热线">
            </a>
        </div>
    </div>
</div>
</body>
</html>
<script >
    function checkName(obj){
        var name = $(obj).val();
        if(name ==""){
            alert('名字不能为空')
        }
    }

    function checkMobile(obj){
        var mobile = $(obj).val();
        if(mobile ==""){
            alert('手机号码不能为空')
        }
        var pattern = /^13[0-9]{1}[0-9]{8}$|^15[0-9]{1}[0-9]{8}$|^18[0-9]{1}[0-9]{8}$|^14[0-9]{1}[0-9]{8}$|^(852){0,1}[0-9]{8}$/;
        if(pattern.test(mobile) != true){
            alert('手机号码格式不正确')
        }
    }

    $("#selectprov").change(function(){
        $("#selectcity").removeAttr('disabled');
    });

    $("#selectcity").click(function(){
        $("#selectdistrict").removeAttr('disabled');
    });

</script>
<script>
    $(".submit-btn").click(function(){
        var id = $("#selectprov").val();
        if(id == 11 || id == 12 || id == 31 || id == 71 || id == 50 || id == 81 || id == 82){
            var cityName = $("#selectprov  option:selected").text()+$("#selectcity  option:selected").text()+$('#address-detail').val();
        }else{
            var cityName = $("#selectprov  option:selected").text()+$("#selectcity  option:selected").text()+$("#selectdistrict  option:selected").text()+$('#address-detail').val();
        }
        var goodsName ={};
        $(".goodsName").each(function(i){
            if($(this).attr("checked")){
                if($(this).attr("select")){
                    goodsName[i] = $(this).val()+":"+$("#input_goods").val()
                }else{
                    goodsName[i] = $(this).val();
                }
            }
        })
        var mobile = $(".mobile").val();
        var name = $(".userName").val();
        var quest ={"您所在城市":cityName,"您所提供的商品（多选）":goodsName};
        var data ={"mobile":mobile,"name":name,"quest":quest}
        var str = JSON.stringify(data)
        $.ajax({
            type: 'POST',
            url:'<?php echo $this->createAbsoluteUrl('/api/html/gaiFreshJoinQuest'); ?>',
            data: {'data': str},
            dataType: 'jsonp',
            jsonp:"callback",
            success: function (data){
                alert(data.msg)
            },
            error: function(){
                alert('未提交成功，请重新提交')
            }
        });
    })
</script>
