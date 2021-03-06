<?php
/**
 * @author zhenjun_xu <412530435@qq.com>
 * Date: 2016/1/12 0012
 * Time: 21:03
 * @var $this MController
 * @var $model GuadanCollect
 * @var $form CActiveForm
 */
?>
    <script>
        if (typeof success != 'undefined') {
            parent.location.reload();
            art.dialog.close();
        }
    </script>
    <style>
        .searchTable {
            line-height: 30px;
            float: none;
        }

        .searchTable td {
            padding: 10px;
        }
    </style>

    <table class="searchTable tipsNumber">
        <tr>
            <td>可使用非绑定积分总额: <strong id="amountUnbind"><?php echo $model->maxUnbind ?></strong></td>
        </tr>
        <tr>
            <td>提取的待绑定积分总额: <strong id="amountBind"><?php echo $model->maxBind ?></strong></td>
        </tr>
    </table>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
));
?>
    <table class="searchTable">
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'amount_bind') ?>:
                <?php echo $form->textField($model, 'amount_bind', array('class' => 'text-input-bj  middle amountBind')) ?>
                <?php echo $form->error($model, 'amount_bind') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'amount_unbind') ?>:
                <?php echo $form->textField($model, 'amount_unbind', array('class' => 'text-input-bj  middle amountUnbind')) ?>
                <?php echo $form->error($model, 'amount_unbind') ?>
            </td>
        </tr>
    </table>

    <table class="searchTable">
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'bind_size') ?>:
                <?php echo $form->textField($model, 'bind_size', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'bind_size') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'distribution_ratio') ?>:
                <?php echo $form->textField($model, 'distribution_ratio', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'distribution_ratio') ?>
            </td>
        </tr>
    </table>

    <table class="searchTable">
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'time_start') ?>:
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'model' => $model,
                    'name' => 'time_start',
                    'select' => 'datetime',
                    'cssClass' => 'text-input-bj  middle',

                ));
                ?>
                <?php echo $form->error($model, 'time_start') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'time_end') ?>:
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'model' => $model,
                    'name' => 'time_end',
                    'select' => 'datetime',
                    'cssClass' => 'text-input-bj  middle',
                ));
                ?>
                <?php echo $form->error($model, 'time_end') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo CHtml::submitButton($model->isNewRecord ? "新增" : "编辑", array('class' => 'regm-sub')) ?>
            </td>
        </tr>
    </table>

<?php $this->endWidget() ?>

<script>
    /*
     工具包
     */
    var Utils={
        /*
         单位
         */
        units:'个十百千万@#%亿^&~',
        /*
         字符
         */
        chars:'零一二三四五六七八九',
        /*
         数字转中文
         @number {Integer} 形如123的数字
         @return {String} 返回转换成的形如 一百二十三 的字符串
         */
        numberToChinese:function(number){
            var a=(number+'').split(''),s=[],t=this;
            if(a.length>12){
                throw new Error('too big');
            }else{
                for(var i=0,j=a.length-1;i<=j;i++){
                    if(j==1||j==5||j==9){//两位数 处理特殊的 1*
                        if(i==0){
                            if(a[i]!='1')s.push(t.chars.charAt(a[i]));
                        }else{
                            s.push(t.chars.charAt(a[i]));
                        }
                    }else{
                        s.push(t.chars.charAt(a[i]));
                    }
                    if(i!=j){
                        s.push(t.units.charAt(j-i));
                    }
                }
            }
            //return s;
            return s.join('').replace(/零([十百千万亿@#%^&~])/g,function(m,d,b){//优先处理 零百 零千 等
                b=t.units.indexOf(d);
                if(b!=-1){
                    if(d=='亿')return d;
                    if(d=='万')return d;
                    if(a[j-b]=='0')return '零'
                }
                return '';
            }).replace(/零+/g,'零').replace(/零([万亿])/g,function(m,b){// 零百 零千处理后 可能出现 零零相连的 再处理结尾为零的
                return b;
            }).replace(/亿[万千百]/g,'亿').replace(/[零]$/,'').replace(/[@#%^&~]/g,function(m){
                return {'@':'十','#':'百','%':'千','^':'十','&':'百','~':'千'}[m];
            }).replace(/([亿万])([一-九])/g,function(m,d,b,c){
                c=t.units.indexOf(d);
                if(c!=-1){
                    if(a[j-c]=='0')return d+'零'+b
                }
                return m;
            });
        }
    };
    $(function(){
        $("#GuadanCollect_amount_bind,#GuadanCollect_amount_unbind").keyup(function(){
            var id = this.id + 'tips';
            $("#"+id).remove();
            $(this).after('<span id="'+id+'" style="color:#333">'+Utils.numberToChinese(parseInt(this.value))+'</span>');
        });
        $(".tipsNumber strong").each(function(){
            $(this).after("  ("+Utils.numberToChinese(parseInt($(this).html()))+")");
        });

        $(".amountBind").blur(function(){
            var val = $(this).val();
            var  amountBindVal = $("#amountBind").text();
            if(val > parseInt(amountBindVal)){
                $(this).val(amountBindVal);
            }
        })
        $(".amountUnbind").blur(function(){
            var val = $(this).val();
            var  amountUnbindVal = $("#amountUnbind").text();
            if(val > parseInt(amountUnbindVal)){
                $(this).val(amountUnbindVal);
            }
        })
    });



</script>
