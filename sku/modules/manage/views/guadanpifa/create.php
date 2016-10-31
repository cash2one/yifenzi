<?php
/**
 * @author zhenjun_xu <412530435@qq.com>
 * Date: 2016/1/12 0012
 * Time: 21:03
 * @var $this MController
 * @var $model GuadanCollect
 * @var $form CActiveForm
 */
$this->breadcrumbs = array('积分批发' => array('admin'), '添加积分批发政策');
?>

    <style>
        .searchTable {
            line-height: 30px;
            float: none;
        }

        .searchTable td {
            padding: 10px;
        }
    </style>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'clientOptions' => array(
        'validateOnSubmit' => false,
    ),
));
?>
<div style="width:100%;border: 1px solid #000000;">
    <p style="padding-left:2px;font-weight: 900;color:#000000;font-size: 18px;line-height: 50px;height: 50px;text-align: center;border: 1px solid #000000;">商户批发政策</p>
</div>
<div style="width:100%;height: auto;border: 1px solid #000000;border-top: none">
    <table class="searchTable" >
        <tr>
            <td>
                <label class="uuuu">应用地区:</label>
                全国
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model,'limit_score') ?>:
                <?php echo $form->textField($model, 'limit_score', array('class' => 'text-input-bj  middle limitScore',"onblur"=>"checkScore(this)")) ?>
                <?php echo $form->error($model, 'limit_score') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'distribution_ratio') ?>:
                <?php echo $form->textField($model, 'distribution_ratio', array('class' => 'text-input-bj  middle distribution',"style"=>"width:40px","onblur"=>"checkRatio(this)")) ?><span >%</span>
                <?php echo $form->error($model, 'distribution_ratio') ?>
            </td>
        </tr>
    </table>
</div>


    <table class="searchTable  addtable" style="width:100%">
        <tr style="background: #F2F2F2;">
            <th style="width:25%;font-weight: 700;color:#333333">批发金额</th>
            <th style="width:25%;font-weight: 700;color:#333333">折扣</th>
            <th style="width:25%;font-weight: 700;color:#333333">总优惠比例</th>
            <th style="width:25%;font-weight: 700;color:#333333">操作  <a href="#"  class="addCollect" style="padding-left: 6px;padding-right: 6px;color:#0000FF">添加</a></th>
        </tr>

    </table>


<div style="display: none;" id="confirmArea">
    <div class="search-form" >
        <div class="border-info clearfix search-form">
            <table class="searchTable">
                <tr>
                    <td><input type="checkbox" class ="checkbox">批发积分: <span>大于或等于</span> <input type="text" class = "min text-input-bj " id="min" value=""  min =""></td>
                </tr>
                <tr>
                    <td><input type="checkbox" class ="checkbox"> 批发积分: <span>小于</span> <input type="text" class = "max text-input-bj " id="max" value="" max =""></td>
                </tr>
                <tr>
                    <td>适用折扣:  <input type="text" class = "ratio text-input-bj " ratio="" onblur="checkRatio(this)"></td>
                </tr>

            </table>
        </div>
    </div>

</div>
<div style="margin-top: 60px">
   <strong style="float:left">说明：</strong> <textarea cols=100 rows=10 name="explain" ></textarea>
</div>

<div  style="margin-top: 60px">
    <span style="cursor :pointer" class="regm-sub submit">新增</span>
<!--    --><?php //echo CHtml::submitButton($model->isNewRecord ? "新增" : "编辑", array('class' => 'regm-sub submit')) ?>
</div>



<?php $this->endWidget() ?>

<script src="<?php echo DOMAIN_M ?>/js/swf/js/artDialog.iframeTools.js"></script>
<script type="text/javascript">
    function checkRatio(obj){
//        var distribution = $(".distribution").val();
        var distribution = $(".distribution").val();
//        var ex = /^(100|[1-9]?\d(\.\d)?)$/;
        var ex = /(?!^0\.0?0$)^[0-9][0-9]?(\.[0-9]{1,2})?$|^100$/;
        var x = ex.test(distribution);
        if(x == false){
            alert("商家推荐者分配比例为0到100之间的整数或一位小数点小数");
            return;
        }

    }
    function setIframeValue(){
        var min = $('.min').attr('min');
        var max = $('.max').attr('max');
        var ratio = $('.ratio').attr('ratio');
        $('.min').val(min);
        $('.max').val(max);
        $('.ratio').val(ratio);
        $('.min').attr('min','');
        $('.max').attr('max','');
        $('.ratio').attr('ratio','');

    }

    $(function() {
        $('.submit').click(function() {
            var code = $(this).attr("data-code");
            var url = '<?php echo Yii::app()->createAbsoluteUrl('/guadanpifa/ajaxCheck') ?>';
            var d = {};
            var t = $('form').serializeArray();

            $.each(t, function(i) {
                if(this.name == "GuadanPartnerConfigDetail[]"){
                    d[i] = this.value;
                }

            });
            data =JSON.stringify(d);
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: url,
                data: {code: code, 'YII_CSRF_TOKEN': '<?php echo Yii::app()->request->csrfToken ?>', data: data},
                success: function(data) {
                    if (data.success) {
                        $('form').submit();
                    } else {
                        alert(data.error);
                    }
//                    $('#secKillSearch').removeAttr('disabled');
                }
            });
        });
    });
    function del(obj){
        if(confirm("确定要删除？")) {
            $(obj).parent().parent().remove();
        }
    }
    //检查是否有重复数据
    function check(min){
        var sortArray = new Array();
        var oldTr     = new Array();
        $('.addtable').find('tr:gt(0)').each(function(index, element) {
            var index = $(this).index();
            if(index > 0){
                sortArray[index-1] = parseInt($(this).find('input').attr('min'));

            }
        });
        sortArray.sort(function(a,b){
            var a1= parseInt(a);
            var b1= parseInt(b);
            if(a1>b1){
                return 1;
            }else if(a1<b1){
                return -1;
            }
            return 0;
        });

        var status = true;
        $.each(sortArray, function(i, v){
                if(sortArray[i] == min){
                    status = false;
                }
        });
        return status;

    }
//排序函数
    function dealSort(){
        var sortArray = new Array();
        var oldTr     = new Array();
        $('.addtable').find('tr:gt(0)').each(function(index, element) {
            var index = $(this).index();
            if(index > 0){
                sortArray[index-1] = parseInt($(this).find('input').attr('min'));
//                oldTr[index-1] = $(this).html();
                oldTr[sortArray[index-1]] = $(this).html();
            }
        });
        sortArray.sort(function(a,b){
            var a1= parseInt(a);
            var b1= parseInt(b);
            if(a1>b1){
                return 1;
            }else if(a1<b1){
                return -1;
            }
            return 0;
        });

        var html = '';
        var status = true;
        $.each(sortArray, function(i, v){
            if(i<sortArray.length){
                if(sortArray[i] == sortArray[i+1]){

                    status = false;
                }
            }
            html += "<tr style='border-bottom: 1px solid #C9C9C9;border-right: 1px solid #C9C9C9;border-left: 1px solid #C9C9C9;'>"+oldTr[sortArray[i]]+"</tr>";
        });
       if(status === true){
           $('.addtable').find('tr:gt(0)').each(function(index, element) {
               $(this).remove();
           });

           $('.addtable').append(html);
       }else{
           alert("你输入的数据有重复");
       }


    }

    function edit(obj){
        var distribution = $(".distribution").val();
        var limitScore = $(".limitScore").val();
        var min  = $(obj).parent().siblings('.hidden').attr('min');
        var max  = $(obj).parent().siblings('.hidden').attr('max');
        var ratio  = $(obj).parent().siblings('.hidden').attr('ratio');

        $(".min").siblings('.checkbox').attr("checked",false);
        $(".max").siblings('.checkbox').attr("checked",false);
        if(min > 0){
            $(".min").siblings('.checkbox').attr("checked",true);

            $(".min").attr("min",min);
        }
        if(max > 0){
            $(".max").siblings('.checkbox').attr("checked",true);
            $(".max").attr("max",max) ;
        }
        $('.ratio').val(ratio);
        $(".ratio").attr("ratio",ratio) ;

        if(!distribution){
            alert("商家推荐折扣百分比不能为空");
            return;
        }
        if(!limitScore){
            alert("全国商家限额的积分不能为空");
            return;
        }

        var code = $(this).attr("data-code");
        var url = '<?php echo Yii::app()->createAbsoluteUrl('/secKillGrab/addProduct') ?>';
        art.dialog({
            title: '<?php echo Yii::t('sellerOrder', '添加积分批发政策规则') ?>',
            okVal: '<?php echo Yii::t('sellerOrder', '新增') ?>',
            cancelVal: '<?php echo Yii::t('sellerOrder', '取消') ?>',
            content: $("#confirmArea").html(),
            lock: true,
            init: function(){ setIframeValue(); },
            cancel: true,
            ok: function() {
                var min = 0;
                var max = "";
                $(".checkbox").each(function(i){
                    if($(this).attr("checked") && i == 0){
                        min = $(".min").val();
                    }
                    if($(this).attr("checked") && i == 1){
                        max = $(".max").val();
                    }
                });
                if(!min && !max){
                    alert("请至少填写一个批发积分");
                    return;
                }

                var ratio = $(".ratio").val();

                if(!ratio){
                    alert("商家折扣不能为空")
                    return
                }
                if(ratio<=0 || ratio >100){
                    alert("商家折扣请填写0到100之间正整数");
                    return;
                }
                var distribution = $(".distribution").val();
                var b = "+";
                var c = eval(100-ratio);
                var distribution = eval(c+b+distribution);
                if(distribution>100){
                    alert("您的优惠额不能大于100");
                    return;
                }
                var html="<tr style='border-bottom: 1px solid #C9C9C9;border-right: 1px solid #C9C9C9;border-left: 1px solid #C9C9C9;' >";
                html += "<td style='width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;'>"
                if(!min){
                    html += "n < "+max;
                }
                if(!max){
                    html += min+" <= n";
                }
                if(min && max){
                    html += min+" <= n < "+max;
                }
                html += "</td><td style='width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;'>"
                html += ratio+"%</td><td style='width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;'>"
                html += distribution+"%</td><td style='width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;'><a href='#' onclick='edit(this)'class='addC' style='padding-left: 6px;padding-right: 6px;color:#0000FF'>编辑</a></th><a href='#' onclick='del(this)' class='addCollect' style='padding-left: 6px;padding-right: 6px;color:#0000FF'>删除</a></th></td>"
                if(!max){
                    html += "<input class='hidden' name ='GuadanPartnerConfigDetail[]' type='hidden' min = "+min+" max = '' ratio = "+ratio+" value='"+min+","+max+","+ratio+"'/> </tr>"
                }else{
                    html += "<input class='hidden' name ='GuadanPartnerConfigDetail[]' type='hidden' min = "+min+" max = "+max+" ratio = "+ratio+" value='"+min+","+max+","+ratio+"'/> </tr>"
                }


                var length = $(obj).parent().parent().parent().append(html);
                $(obj).parent().parent().remove();
                dealSort()



            }
        });
        return true;
    }
    //添加积分批发政策
    $(".addCollect").click(function() {
        var distribution = $(".distribution").val();
        var limitScore = $(".limitScore").val();
        if(!distribution){
            alert("商家推荐折扣百分比不能为空");
            return;
        }
        if(!limitScore){
            alert("全国商家限额的积分不能为空");
            return;
        }
        var code = $(this).attr("data-code");

        art.dialog({
            title: '<?php echo Yii::t('sellerOrder', '添加积分批发政策规则') ?>',
            okVal: '<?php echo Yii::t('sellerOrder', '新增') ?>',
            cancelVal: '<?php echo Yii::t('sellerOrder', '取消') ?>',
            content: $("#confirmArea").html(),
            lock: true,
            cancel: true,
            ok: function() {
                  var min = 0;
                  var max = "";
                $(".checkbox").each(function(i){
                    if($(this).attr("checked") && i == 0){
                         min = $(".min").val();
                    }
                    if($(this).attr("checked") && i == 1){
                         max = $(".max").val();
                    }
                })
                if(!min && !max){
                   alert("请至少填写一个批发积分")
                    return;
                }

                var ratio = $(".ratio").val();
                if(!ratio){
                    alert("商家折扣不能为空")
                    return
                }
                if(ratio<=0 || ratio >100){
                    alert("商家折扣请填写0到100之间正整数");
                    return;
                }
                var distribution = $(".distribution").val();
                var b = "+";
                var c = eval(100-ratio);
                var distribution = eval(c+b+distribution);
                if(distribution>100){
                    alert("您的优惠额不能大于100");
                    return;
                }
                var html="<tr style='border-bottom: 1px solid #C9C9C9;border-right: 1px solid #C9C9C9;border-left: 1px solid #C9C9C9;' >";
                html += "<td style='width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;'>"
                if(!min){
                    html += "n < "+max;
                }
                if(!max){
                    html += min+" <= n";
                }
                if(min && max){
                    html += min+" <= n < "+max;
                }
                html += "</td><td style='width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;'>"
                html += ratio+"%</td><td style='width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;'>"
                html += distribution+"%</td><td style='width:25%;text-align: center;border-right: 1px solid #C9C9C9;font-weight: 400;font-size:14px;'><a href='#' onclick='edit(this)'class='addC' style='padding-left: 6px;padding-right: 6px;color:#0000FF'>编辑</a></th><a href='#' onclick='del(this)' class='addCollect' style='padding-left: 6px;padding-right: 6px;color:#0000FF'>删除</a></th></td>"

                if(!max){
                    html += "<input class='hidden' name ='GuadanPartnerConfigDetail[]' type='hidden' min = "+min+" max = '' ratio = "+ratio+" value='"+min+","+max+","+ratio+"'/> </tr>"
                }else{
                    html += "<input class='hidden' name ='GuadanPartnerConfigDetail[]' type='hidden' min = "+min+" max = "+max+" ratio = "+ratio+" value='"+min+","+max+","+ratio+"'/> </tr>"
                }

                if(check(min) == false){
                    alert("请检查你输入的数据是否有重复");
                    return;
                }
                $(".addtable").children().last().append(html);

               dealSort()

            }
        });
        return true;
    });

</script>