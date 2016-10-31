<?php
/* @var $this OrderController */
/* @var $model Orders */
/* @var $form CActiveForm */
?>
<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>
<div class="border-info clearfix">
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'code'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'code',array('size'=>60,'maxlength'=>64,'class'=>'text-input-bj  middle')); ?>
            </td>
        </tr>
        </tbody></table>
        
        <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'store_id'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'store_id',array('class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody></table>
        
        <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'machine_id'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'machine_id',array('class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody></table>
        
      <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo Yii::t('order','会员盖网号'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'gai_number',array('size'=>11,'maxlength'=>11,'class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody></table>
     <table cellspacing="0" cellpadding="0" class="searchTable">
    <tbody><tr>
            <th align="right">
                <b><?php echo Yii::t('order','网点'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'network',array('size'=>11,'maxlength'=>11,'class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody></table>
    <div class="c10"></div>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'status'); ?>：
            </th>
            <td id="tdPay">
            <?php echo $form->radioButtonList($model,'status', array(null=>'全部'),
                    array('separator'=>''))?>
                <?php echo $form->radioButtonList($model,'status', Order::status(),
                    array('separator'=>'')) ?>
            </td>
        </tr>
        </tbody></table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'type'); ?>：
            </th>
            <td id="tdPay">
                <?php echo $form->radioButtonList($model,'type',$model::type(),
                    array('empty'=>array('0'=>Yii::t('order','全部')),'separator'=>'')) ?>
            </td>
        </tr>
        </tbody></table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'shipping_type'); ?>：
            </th>
            <td id="tdPay">
                <?php echo $form->radioButtonList($model,'shipping_type',$model::shippingType(),
                    array('empty'=>array('0'=>Yii::t('order','全部')),'separator'=>'')) ?>
            </td>
        </tr>
        </tbody></table>
    <div class="c10">
    <?php echo CHtml::submitButton('搜索',array('class'=>'reg-sub')) ?>
        <!--<a href="javascript:;" class="regm-sub" onclick="showExport()"><?php echo Yii::t('main', '导出报表'); ?></a>-->   
</div>
<div class="c10">
</div>
<?php $this->endWidget(); ?>
