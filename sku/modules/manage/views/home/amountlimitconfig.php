
<?php $form = $this->beginWidget('CActiveForm', $formConfig); ?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tbody>
        <tr>
            <th colspan="2" style="text-align: center" class="title-th">
                <?php echo Yii::t('home', '收益分配配置'); ?>
            </th>
        </tr>
        
        <tr>
            <th style="width: 220px"><?php echo $form->labelEx($model, 'isEnable'); ?></th>
            <td>
                <?php echo $form->dropDownList($model, 'isEnable',AmountLimitConfigForm::getStatus()); ?>
                <?php echo $form->error($model, 'isEnable'); ?>
            </td>
        </tr>
        
        <tr>
            <th style="width: 220px"><?php echo $form->labelEx($model, 'memberPointPayPreStoreLimit'); ?></th>
            <td>
                <?php echo $form->textField($model, 'memberPointPayPreStoreLimit', array('class' => 'text-input-bj  long valid')); ?>
                <?php echo $form->error($model, 'memberPointPayPreStoreLimit'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'memberTotalPayPreStoreLimit'); ?></th>
            <td>
                <?php echo $form->textField($model, 'memberTotalPayPreStoreLimit', array('class' => 'text-input-bj  long')); ?>
                <?php echo $form->error($model, 'memberTotalPayPreStoreLimit'); ?>
            </td>
        </tr>
        
        
        <tr>
            <th></th>
            <td><?php echo CHtml::submitButton(Yii::t('home', '保存'), array('class' => 'reg-sub')); ?></td>
        </tr>
    </tbody>
</table>
<?php $this->endWidget(); ?>