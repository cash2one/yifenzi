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
                    <b><?php echo $form->label($model, 'name'); ?>：
                </th>
                <td>
                    <?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 64, 'class' => 'text-input-bj  middle')); ?>
                </td>
            </tr>
        </tbody>
    </table>
        <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody>
            <tr>
                <th align="right">
                    <b><?php echo $form->label($model, 'barcode'); ?>：
                </th>
                <td>
                    <?php echo $form->textField($model, 'barcode', array('size' => 60, 'maxlength' => 64, 'class' => 'text-input-bj  middle')); ?>
                </td>
            </tr>
        </tbody>
    </table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
                <th>
                    <b><?php echo $form->label($model, 'price'); ?>：
                </th>
                <td>
                    <?php echo $form->textField($model, 'price', array('size' => 11, 'maxlength' => 11, 'class' => 'text-input-bj  least')); ?> -
                    <?php echo $form->textField($model, 'endPrice', array('size' => 11, 'maxlength' => 11, 'class' => 'text-input-bj  least')); ?>
                    <?php echo $form->error($model,'price');?>
                </td>    
            </tr>
        </tbody>
    </table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
                <th><b><?php echo $form->label($model, 'category'); ?>：</b></th>
                <td>
                    <?php echo $form->textField($model, 'category', array('size' => 11, 'maxlength' => 11, 'class' => 'text-input-bj  least')); ?>
                </td>
        </tbody>
    </table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th><b><?php echo $form->label($model, 'partner_name'); ?>：</b></th>
            <td>
                <?php echo $form->textField($model, 'partner_name', array('size' => 20, 'maxlength' => 20, 'class' => 'text-input-bj  least')); ?>
            </td>
        </tbody>
    </table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th><b><?php echo $form->label($model, 'gai_number'); ?>：</b></th>
            <td>
                <?php echo $form->textField($model, 'gai_number', array('size' => 20, 'maxlength' => 20, 'class' => 'text-input-bj  least')); ?>
            </td>
        </tbody>
    </table>
 <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b>
                <?php echo $form->label($model,'status'); ?>：
                </b>
            </th>
            <td id="tdPay">
                <?php echo $form->radioButtonList($model,'status', Goods::getStatus(),
                    array('empty'=>Yii::t('order','全部'),'separator'=>'')) ?>
            </td>
        </tr>
        </tbody></table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right"><b>
                <?php echo $form->label($model,'is_one'); ?>：
                </b>
            </th>
            <td id="tdPay">
                <?php echo $form->radioButtonList($model,'is_one', Goods::gender(),
                    array('empty'=>Yii::t('order','全部'),'separator'=>'')) ?>
            </td>
        </tr>
        </tbody></table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b>
                <?php echo $form->label($model,'is_promo'); ?>：
                </b>
            </th>
            <td id="tdPay">
                <?php echo $form->radioButtonList($model,'is_promo', Goods::gender(),
                    array('empty'=>Yii::t('order','全部'),'separator'=>'')) ?>
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
    

    <?php $this->endWidget(); ?>

</div>
