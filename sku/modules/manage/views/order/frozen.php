<?php
$this->breadcrumbs = array(
    Yii::t('order', '订单管理')=>array('admin'),
    Yii::t('order', '订单冻结'),
);

$form = $this->beginWidget('ActiveForm', array(
    'id' => 'order-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
));
?>
<table width="50%" border="0" cellspacing="0" cellpadding="0" class="tab-come">
    <tbody>
        <tr>
            <th colspan="2"></th>
        </tr>

        <tr>
            <th colspan="2" class="title-th odd">
                备注
            </th>
        </tr>
        <tr style="width: 50%">
            <th><?php echo Yii::t('order','备注'); ?>&nbsp;<span class="required">*</span></th>
            <td>
                <?php echo $form->textArea($model,'remark',array('style'=>'width:50%','rows'=>10,'value'=>''))?>
                <p style="display: block;float:left"><?php echo $form->error($model,'remark') ?></p>
            </td>
        </tr>
        <tr>
            <th>
            </th>
            <td>
                <?php echo CHtml::submitButton(Yii::t('order', '确定'), array('class' => 'reg-sub')); ?>
            </td>
        </tr>
    </tbody>

</table>
<?php $this->endWidget() ?>