<?php
/* @var $this ProductController */
/* @var $model Product */
/* @var $form CActiveForm */
?>
<style>
    <!--
    .search-form{ line-height:45px; }
    -->
</style>
<div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    ));
    ?>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody>
            <tr>
                <th align="right">
                    <b><?php echo $form->label($model, 'status'); ?>：
                </th>
                <td>
                    <?php
                    echo $form->dropDownList($model, 'status', BarcodeGoods::getStatus(), array('class' => 'text-input-bj', 'prompt' => Yii::t('InputGoods', '全部'))
                    );
                    ?>
                </td>
                <th align="right">
                    <b><?php echo $form->label($model, 'name'); ?>：
                </th>
                <td>
                    <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 64, 'class' => 'text-input-bj  middle')); ?>
                </td>
                 <th align="right">
                    <b><?php echo $form->label($model, 'barcode'); ?>：
                </th>
                <td>
                    <?php echo $form->textField($model, 'barcode', array('size' => 60, 'maxlength' => 64, 'class' => 'text-input-bj  middle')); ?>
                </td>
            </tr>
           
            <tr>
                <td colspan="6">
                    <?php echo CHtml::submitButton('搜索', array('class' => 'reg-sub')) ?>
                </td>
            </tr>
        </tbody>
    </table>

 
    <?php $this->endWidget(); ?>

</div>
