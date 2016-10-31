<!doctype html>
<html>
<head>
    <title>盖鲜生装机申请</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords" content="盖鲜生"/>
    <meta name="description" content="盖鲜生"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-cale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta content="telephone=no" name="format-detection"/>
    <link type="text/css" rel="stylesheet" href="<?php echo DOMAIN ?>/quest/styles/mmodule.css"/>
    <script src="<?php echo DOMAIN ?>/quest/js/jquery-2.1.1.min.js"></script>
    <script src="<?php echo DOMAIN ?>/quest/js/jquery-migrate-1.2.1.min.js"></script>
    <script>
        $(function(){
            //所在城市选中其他
            $("#city-list .radio").click(function(){
                if($(this).hasClass("others")){
                    $(this).parent().siblings(".input-box").removeClass("hide");
                }else{
                    $("#city-list .input-box").val("").addClass("hide");
                }
            })

            //选中安装地址
            $(".address .checkbox").click(function(){
                if($(this).prop("checked")==true){
                    $(this).parent().siblings(".input-box").removeClass("hide");
                }else{
                    $(this).parent().siblings(".input-box").val("").addClass("hide");
                }
            })

            //食品、增值服务选中其他
            $("#food-list .checkbox,#service-list .checkbox").click(function(){
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
        })
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
            <img src="<?php echo DOMAIN ?>/quest/images/bg/introduction-01.png" alt="盖鲜生智能装机申请">
        </div>

        <div class="form-header">
            <img src="<?php echo DOMAIN ?>/quest/images/bg/title-01.png" alt="想把盖鲜生带进您的小区吗？请告诉我们在哪里！">
        </div>
        <div id="city-list" class="form-row">
            <h3>您所在城市：</h3>
            <div class="options hot-city">
                <label><input type="radio" class="radio" name="city" value="广州"><span>广州</span></label>
                <label><input type="radio" class="radio" name="city" value="上海"><span>上海</span></label>
                <label><input type="radio" class="radio" name="city" value="北京"><span>北京</span></label>
            </div>
            <div class="options">
                <label><input type="radio" class="radio others" name="city" select ="true"><span>其他</span></label>
                <input type="text"  id ="input_city" class="input-box hide" placeholder="请输入您的城市">
            </div>
        </div>
        <div class="form-row">
            <h3>您希望盖鲜生安装的具体地址：</h3>
            <div class="options address">
                <p>
                    <label>
                        <input type="checkbox" class="checkbox address"  name="address-details" value="社区">
                        <span>社区</span>
                    </label>
                    <input type="text" class="input-box hide" placeholder="*区/*路/*楼">
                </p>
                <p>
                    <label>
                        <input type="checkbox" class="checkbox address"  name="address-details" value="办公楼">
                        <span>办公楼</span>
                    </label>
                    <input type="text" class="input-box hide" placeholder="*区/*路/*楼">
                </p>
                <p>
                    <label>
                        <input type="checkbox" class="checkbox address others" name="address-details" select = "true" value="其他">
                        <span>其他</span>
                    </label>
                    <input type="text" class="input-box hide" placeholder="请填写详细的地址">
                </p>
            </div>
        </div>
        <div id="food-list" class="form-row">
            <h3>您希望在盖鲜生智能机上方便、快捷的买到哪些食品？（多选）</h3>
            <div class="options food-list">
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="foodList" value="蔬菜水果">
                    <span>蔬菜水果</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="foodList" value="肉类海鲜">
                    <span>肉类&海鲜</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="foodList" value="搭配好的食材">
                    <span>搭配好的食材</span>
                </label>
            </div>
            <div class="options food-list">
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="foodList" value="鲜奶乳品">
                    <span>鲜奶乳品</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="foodList" value="高端有机食材">
                    <span>高端有机食材</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="foodList" value="各地特色食材">
                    <span>各地特色食材</span>
                </label>
            </div>
            <div class="options food-list">
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="foodList" value="粮油副食">
                    <span>粮油副食</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="foodList" value="快捷速食">
                    <span>快捷速食</span>
                </label>
                <label>
                    <input type="checkbox" class="checkbox goodsName" name="foodList" value="日常调味">
                    <span>日常调味</span>
                </label>
            </div>
            <div class="options">
                <label>
                    <input type="checkbox" class="checkbox goodsName others" name="foodList" select="true" value="其他">
                    <span>其他</span>
                </label>
                <input type="text" id = "input_goodsName" class="input-box hide" placeholder="请输入您希望的食品">
            </div>
        </div>
        <div id="service-list" class="form-row">
            <h3>您还希望盖鲜生能提供哪些增值服务？（多选）</h3>
            <div class="options">
                <label><input type="checkbox" class="checkbox service" name="service" value="充值服务（手机充值、公交卡充值等）"><span>充值服务（手机充值、公交卡充值等）</span></label>
            </div>
            <div class="options clearfix">
                <label><input type="checkbox" class="checkbox service" name="service" value="市政、家政服务"><span>市政、家政服务</span></label>
                <label class="fr"><input type="checkbox" class="checkbox service" name="service" value="水电煤气交费"><span>水电煤气交费</span></label>
            </div>
            <div class="options">
                <label><input type="checkbox" class="checkbox service" name="service" value="wifi接入"><span>wifi接入</span></label>
            </div>
            <div class="options">
                <label>
                    <input type="checkbox" class="checkbox service others" name="service" select = "true" value="其他"><span>其他</span>
                </label>
                <input type="text" id="input_service" class="input-box hide" placeholder="请输入您期待的增值服务">
            </div>
        </div>
        <div class="form-row contact">
            <h3>提供联系方式，我们会尽快将盖鲜生智能机铺设到您希望的地方。</h3>
            <div class="contact-box">
                <div class="user-box">
                    <input type="text" class="input-box userName" onblur="checkName(this)"  placeholder="请输入您的姓名" maxlength="20">
                </div>
                <div class="phone-box">
                    <input type="text" class="input-box mobile" onblur ="checkMobile(this)" placeholder="请输入您的手机号码" maxlength="14">
                </div>
            </div>
        </div>
        <div class="ta-c">
            <input id = "submit" type="submit" class="submit-btn" value="">
        </div>

        <div class="footer">
            <a href="tel://4006206899"><img src="<?php echo DOMAIN ?>/quest/images/bg/tel-line.png" alt="盖鲜生合作热线"></a>

        </div>
    </div>
</div>
</body>
</html>
<script>
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

</script>
<script>
    $("#submit").click(function(){
        var cityName = {};
        if($('input:radio[name="city"]:checked').attr("select")){
            var cityName =  $("#input_city").val();
        }else{
            var cityName =$('input:radio[name="city"]:checked').val();
        }
        var address ={};
        $(".address").each(function(i){
            if($(this).attr("checked")){
                address[i] = $(this).val()+":"+$(this).parent('label').siblings('input').val();
            }
        })
        var goodsName ={};
        $(".goodsName").each(function(i){
            if($(this).attr("checked")){
                if($(this).attr("select")){
                    goodsName[i] = $(this).val()+":"+$("#input_goodsName").val()
                }else{
                    goodsName[i] = $(this).val();
                }


            }
        })
        var service = {};
        $(".service").each(function(i){
            if($(this).attr("checked")){
                if($(this).attr("select")){
                    service[i] = $(this).val()+":"+$("#input_service").val()
                }else{
                    service[i] = $(this).val();
                }
            }
        })
        var mobile = $(".mobile").val();
        var  name = $(".userName").val();
        var quest ={"您所在城市":cityName,"您希望盖鲜生安装的具体地址":address,"您希望在盖鲜生智能机上方便、快捷的买到哪些食品？（多选）":goodsName,"您还希望盖鲜生能提供哪些增值服务？（多选）":service};

        var data ={"mobile":mobile,"name":name,"quest":quest};
        var str = JSON.stringify(data);
        $.ajax({
            type: 'POST',
            url:'<?php echo $this->createAbsoluteUrl('/api/html/gaiFreshMachineApplyQuest'); ?>',
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
