<?php
/* @var $this AssistantController */
/* @var $model Assistant */
/* @var $form CActiveForm */
?>
<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>

<div class="seachToolbar">
    <table width="95%" cellspacing="0" cellpadding="0" border="0" class="sellerT5">
        <tbody>
        <tr>

            <td>
                <th width="8%"><?php echo Yii::t('partnerModule.store','商品名'); ?>：</th>
            <td width="15%">
                <?php echo $form->textField($model,'name',array('class'=>'inputtxt1','style'=>'width:90%')); ?>
            </td>

            <td>
                <?php echo CHtml::submitButton(Yii::t('partnerModule.store','搜索'),array('class'=>'sellerBtn06')) ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<?php $this->endWidget(); ?>