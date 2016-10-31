<?php
/* @var $this ProductController */
/* @var $model Product */
/* @var $form CActiveForm */
?>

<div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
                <th>
                    <?php echo $form->label($model, 'name'); ?>：
                </th>
                <td>
                    <?php echo $form->textField($model, 'name', array('size' => 11, 'maxlength' => 11, 'class' => 'text-input-bj  least')); ?> 
                </td>    
            </tr>
        </tbody>
    </table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
                <th>
                    <?php echo $form->label($model, 'mobile'); ?>：
                </th>
                <td>
                    <?php echo $form->textField($model, 'mobile', array('size' => 11, 'maxlength' => 11, 'class' => 'text-input-bj  least')); ?>
                </td>    
            </tr>
        </tbody>
    </table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <?php echo Yii::t('supermarkets','申请类型'); ?>：
            </th>
            <td id="tdPay">
                <?php echo $form->radioButtonList($model,'type', FreshQuestResult::getType(),
                    array('empty'=>Yii::t('order','全部'),'separator'=>'')) ?>
            </td>
        </tr>
        </tbody></table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right"><?php echo $form->label($model, 'create_time'); ?>：</th>
            <td >
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'model'=>$model,
                    'name'=>'start_time',
                ));
                ?> -
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'model'=>$model,
                    'name'=>'end_time',
                ));
                ?>
            </td>
        </tr>
        </tbody></table>



    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <td colspan="2">
                <?php echo CHtml::submitButton('搜索', array('class' => 'reg-sub')) ?>
            </td>
        </tr></tbody>
    </table>
    <a href="javascript:void()" class="regm-sub" onclick="exportExcel()">导出EXCEL</a>

    <?php $this->endWidget(); ?>

</div>
<script type="text/javascript">
    function exportExcel(){
        var form = document.forms[0];
        form.action = "<?php echo Yii::app()->createUrl('freshQuestResult/exportExcel')?>";
        form.submit();
        form.action = '';
    }
</script>
