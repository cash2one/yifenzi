<script>
    /**
     * ajxa获取数据
     */
    function reflash() {
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->createUrl('input/member/getData'); ?>",
            data: {YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>', },
            success: function (msg) {
                var data = eval(msg);
                var content = document.getElementById("showData");
                var b = content.getElementsByTagName("li");
                $.each(data, function (i, result) {
                    var li = result['barcode']
                    var id = result['id'];
                    b[i].id = id;
                    b[i].innerHTML = '<a style="cursor:pointer">' + li + '</a>';
                });
            }
        });
    }

    /*
     * ajax查询
     */
    function search() {
        var name = document.getElementById("searchName").value;
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->createUrl('input/member/ajaxSearch'); ?>",
            data: {
                YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>',
                'name': name
            },
//             async:fales,
            success: function (msg) {
              if(msg){
                var data = eval(msg);
                $(".search-list ul li").remove();
                $.each(data, function (i, result) {
                    var li = "<li><a onclick='getOne(this)' style='cursor:pointer' id=" + result['id'] + ">" + result['name'] + "</a></li>";
                    $(".search-list ul").append(li);
                });
            }else{
                alert('无搜索结果！');
            }
            },
        });
    }

    /*
     * ajax获取单独数据
     */
    function getOne(o) {
        var id = o.id;
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->createUrl('input/member/getOne'); ?>",
            data: {
                YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>',
                'id': id,      
            },
            success: function (msg) {
                var data = eval(msg)
                 var showimg = $('#showimg'); 
//                alert(data);
                $.each(data, function (i, result) {
                    $('#ApplyBarcodeGoods_barcode').val(result['barcode']);
                    $('#ApplyBarcodeGoods_name').val(result['name']);
                    $('#ApplyBarcodeGoods_model').val(result['model']);
                    $('#ApplyBarcodeGoods_unit').val(result['unit']);
                     $('#ApplyBarcodeGoods_cate_name').val(result['cate_name']);
                     $('#ApplyBarcodeGoods_default_price').val(result['default_price']);
                     $('#ApplyBarcodeGoods_describe').val(result['describe']);
                    $('#ApplyBarcodeGoods_id').val(result['id']);
//                    alert(result['thumb']);
                    var img = "<?php echo ATTR_DOMAIN ?>" + '/' + result['thumb'];
                       showimg.html("<img src='"+img+"'>"); 
//                    $('#showImg').attr("src", "<?php echo ATTR_DOMAIN ?>" + '/' + result['thumb']);
$('#big').attr('src',img);
$(".search-list ul li").remove();
                });
                var div = document.getElementById('timer');
                clearInterval(stop);
                i = 0;
                div.style.display = "block";
                stop = setInterval(getRTime, 1000);
            },
            error: function () {
                alert('你已提交该商品,请等待审核!')
            }
        });
    }

    /*
     * 清除内容
     */
    function clearData() {
      var showimg = $('#showimg'); 
        $('#ApplyBarcodeGoods_barcode').val('');
        $('#ApplyBarcodeGoods_name').val('');
        $('#ApplyBarcodeGoods_model').val('');
        $('#ApplyBarcodeGoods_unit').val('');
        $('#ApplyBarcodeGoods_cate_name').val('');
        $('#ApplyBarcodeGoods_thumb').val('');
        $('#ApplyBarcodeGoods_default_price').val('');
        $('#ApplyBarcodeGoods_describe').val('');
        $('#ApplyBarcodeGoods_id').val('');
//        $('#showImg').attr("src", "<?php echo DOMAIN ?>/images/upday.jpg");
        var img = "<?php echo DOMAIN ?>/images/upday.jpg"; 
        showimg.html("<img src='"+img+"'>"); 
        $('#big').attr('src',img);
         var div = document.getElementById('timer');
           div.style.display = "none";
            clearInterval(stop);
            i = 0;
    }

    /**
     * 获取条形码商品
     */
    function getCode() {

        var code = document.getElementById("ApplyBarcodeGoods_barcode").value;
         var showimg = $('#showimg'); 
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->createUrl('input/member/getCode'); ?>",
            data: {
                YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>',
                'barcode': code
            },
            success: function (msg) {
                var data = eval(msg)
                $.each(data, function (i, result) {

                    $('#ApplyBarcodeGoods_name').val(result['name']);
                    $('#ApplyBarcodeGoods_model').val(result['model']);
                    $('#ApplyBarcodeGoods_unit').val(result['unit']);
                    $('#ApplyBarcodeGoods_id').val(result['id']);
                    $('#ApplyBarcodeGoods_cate_name').val(result['cate_name']);
                    $('#ApplyBarcodeGoods_describe').val(result['describe']);
                     $('#ApplyBarcodeGoods_default_price').val(result['default_price']);
//                    $('#showImg').attr("src", "<?php echo ATTR_DOMAIN ?>" + '/' + result['thumb']);
     var img = "<?php echo ATTR_DOMAIN ?>" + '/' + result['thumb'];
                       showimg.html("<img src='"+img+"'>"); 
                });
                var div = document.getElementById('timer');
                clearInterval(stop);
                i=0;
                div.style.display = "block";
                stop = setInterval(getRTime, 1000);
            },
            error: function () {
                alert('该商品不存在或未开放录入的商品！')
               
            }
        });
    }
    var i = 0;
    var stop = 0;
    function getRTime() {

        var time = 600000;

        var m = Math.floor((time - 1000 * i) / 1000 / 60 % 60);
        var s = Math.floor((time - 1000 * i) / 1000 % 60);
        if(m < 10){
        document.getElementById("t_m").innerHTML = "0" + m;
         }else{
              document.getElementById("t_m").innerHTML =  m;
         }
        if (s < 10) {
            document.getElementById("t_s").innerHTML = "0" + s;
            i++;
        } else {
            document.getElementById("t_s").innerHTML = s;
            i++;
        }
        if (i > 600) {
            alert('录入超时');
            clearData();
            var div = document.getElementById('timer');
            div.style.display = "none";
            clearInterval(stop);
            i = 0;
        }
    }

   
    $(function () { 

    var showimg = $('#showimg'); 
//    var big = $('#big');

    
    $("#ApplyBarcodeGoods_thumb").wrap("<form id='myupload' action='<?php echo Yii::app()->createUrl('input/member/Img');?> 'method='post' enctype='multipart/form-data'></form>"); 
    $("#ApplyBarcodeGoods_thumb").change(function(){ //选择文件 
//        var val;
        $("#myupload").ajaxSubmit({ 
            dataType:  'json', //数据格式为json 
    
            data: {
                YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>',
            },
//             async : false,
            success: function(data) { //成功 
                //显示上传后的图片 
//                alert(data);
                val = data;
                var img =data; 
                showimg.html("<img src='"+img+"'>"); 
                $('#big').attr('src',img);
             $("#ytApplyBarcodeGoods_thumb").val(val);
            }, 
            error:function(xhr){ //上传失败 
                alert(xhr.responseText);
                clearData();
//                files.html(xhr.responseText); //返回失败信息 
            } 
        });
    }); 

}); 

    

</script>