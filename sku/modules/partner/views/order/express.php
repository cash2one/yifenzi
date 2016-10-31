<?php
/* @var $this SoldController */
/* @var $model Order */
/* @var $form CActiveForm */
?>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'order-form',
        'action' => Yii::app()->createUrl($this->route, array('code' => $model->code)),
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true, //客户端验证
        ),
    ));
    ?>
        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
            <tbody>
                <tr>
                    <th width="10%"><?php echo Yii::t('partnerModule.order', '物流公司'); ?></th>
                    <td width="40%">
                    <?php echo $form->dropDownList($model,'express',  Express::getExpress())?>
                    </td>

                </tr>
                <tr>
                    <th width="10%"><?php echo Yii::t('partnerModule.order', '快递单号'); ?></th>
                    <td width="40%">
                        <?php echo $form->textField($model,'shipping_code',array('style'=>'width:225px','class'=>'inputtxt1'))?>
                        <?php echo $form->error($model, 'shipping_code'); ?>
                    </td>
                </tr>
                <tr>
                    <th width="10%"></th>
                    <?php echo $form->hiddenField($model,'orderId',array('value'=>$model->id))?>
                    <td> <?php echo CHtml::submitButton(Yii::t('partnerModule.order', ($this->action->id == 'changeExpress')?Yii::t('partnerModule.order','修改'):Yii::t('partnerModule.order','发货')), array('class' => 'sellerBtn06')); ?></td>
                </tr>
            </tbody>
        </table>
        <?php $this->endWidget(); ?>

<script type="text/javascript">
    if (typeof success != 'undefined') {
        art.dialog.opener.location.reload();
        art.dialog.close();
    }
</script>

