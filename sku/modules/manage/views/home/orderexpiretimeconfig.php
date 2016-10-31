
<?php $form = $this->beginWidget('CActiveForm', $formConfig); ?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tbody>
        <tr>
            <th colspan="2" style="text-align: center" class="title-th">
                <?php echo Yii::t('home', '订单时间配置'); ?>
            </th>
        </tr>
        
        <tr>
            <th style="width: 300px"><?php echo $form->labelEx($model, 'orderExpireTime'); ?></th>
            <td>
                <?php echo $form->textField($model, 'orderExpireTime', array('class' => 'text-input-bj  long valid')); ?>
                <?php echo $form->error($model, 'orderExpireTime'); ?>
            </td>
        </tr>
        
        <tr>
            <th><?php echo $form->labelEx($model, 'orderUnsendRefundTime'); ?></th>
            <td>
                <?php echo $form->textField($model, 'orderUnsendRefundTime', array('class' => 'text-input-bj  long valid')); ?>
                <?php echo $form->error($model, 'orderUnsendRefundTime'); ?>
            </td>
        </tr>
          <tr>
            <th><?php echo $form->labelEx($model, 'orderUnsendAutoRefundTime'); ?></th>
            <td>
                <?php echo $form->textField($model, 'orderUnsendAutoRefundTime', array('class' => 'text-input-bj  long valid')); ?>
                <?php echo $form->error($model, 'orderUnsendAutoRefundTime'); ?>
            </td>
        </tr>
         <tr>
            <th><?php echo $form->labelEx($model, 'machineAutoCancelTime'); ?></th>
            <td>
                <?php echo $form->textField($model, 'machineAutoCancelTime', array('class' => 'text-input-bj  long valid')); ?>
                <?php echo $form->error($model, 'machineAutoCancelTime'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'machineUnTakeAutoCancelTime'); ?></th>
            <td>
                <?php echo $form->textField($model, 'machineUnTakeAutoCancelTime', array('class' => 'text-input-bj  long valid')); ?>
                <?php echo $form->error($model, 'machineUnTakeAutoCancelTime'); ?>
            </td>
        </tr>
        
        <tr>
            <th><?php echo $form->labelEx($model, 'machineScanOrderUnTakeAutoCancelTime'); ?></th>
            <td>
                <?php echo $form->textField($model, 'machineScanOrderUnTakeAutoCancelTime', array('class' => 'text-input-bj  long valid')); ?>
                <?php echo $form->error($model, 'machineScanOrderUnTakeAutoCancelTime'); ?>
            </td>
        </tr>
        
        <tr>
            <th></th>
            <td><?php echo CHtml::submitButton(Yii::t('home', '保存'), array('class' => 'reg-sub')); ?></td>
        </tr>
    </tbody>
</table>
<?php $this->endWidget(); ?>