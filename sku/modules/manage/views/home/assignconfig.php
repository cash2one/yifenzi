
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
                <?php echo $form->dropDownList($model, 'isEnable',AssignConfigForm::getStatus()); ?>
                <?php echo $form->error($model, 'isEnable'); ?>
            </td>
        </tr>
        
        <tr>
            <th style="width: 220px"><?php echo $form->labelEx($model, 'skuGaiIncome'); ?></th>
            <td>
                <?php echo $form->textField($model, 'skuGaiIncome', array('class' => 'text-input-bj  valid')); ?>
                <?php echo $form->error($model, 'skuGaiIncome'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'skuMemberIncome'); ?></th>
            <td>
                <?php echo $form->textField($model, 'skuMemberIncome', array('class' => 'text-input-bj ')); ?>
                <?php echo $form->error($model, 'skuMemberIncome'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'skuMemberReferrals'); ?></th>
            <td>
                <?php echo $form->textField($model, 'skuMemberReferrals', array('class' => 'text-input-bj  valid')); ?>
                <?php echo $form->error($model, 'skuMemberReferrals'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'skuStoreReferrals'); ?></th>
            <td>
                <?php echo $form->textField($model, 'skuStoreReferrals', array('class' => 'text-input-bj ')); ?>
                <?php echo $form->error($model, 'skuStoreReferrals'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'skuAgentIncome'); ?></th>
            <td>
                <?php echo $form->textField($model, 'skuAgentIncome', array('class' => 'text-input-bj ')); ?>
                <?php echo $form->error($model, 'skuAgentIncome'); ?>
            </td>
        </tr>
  
  
          <tr>
            <th colspan="2" style="text-align: center" class="title-th">
                <?php echo Yii::t('home', '生鲜机、售货机分配配置'); ?>
            </th>
        </tr>
        <tr>
            <th style="width: 220px"><?php echo $form->labelEx($model, 'skuMachineOwenerIncome'); ?></th>
            <td>
                <?php echo $form->textField($model, 'skuMachineOwenerIncome', array('class' => 'text-input-bj  valid')); ?>
                <?php echo $form->error($model, 'skuMachineOwenerIncome'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'skuMachineSellerIncome'); ?></th>
            <td>
                <?php echo $form->textField($model, 'skuMachineSellerIncome', array('class' => 'text-input-bj ')); ?>
                <?php echo $form->error($model, 'skuMachineSellerIncome'); ?>
            </td>
        </tr>
        
        
        
        <tr>
            <th colspan="2" style="text-align: center" class="title-th">
                <?php echo Yii::t('home', '默认服务费配置'); ?>
            </th>
        </tr>
        <tr>
            <th style="width: 220px"><?php echo $form->labelEx($model, 'machineDefaultFee'); ?></th>
            <td>
                <?php echo $form->textField($model, 'machineDefaultFee', array('class' => 'text-input-bj  valid')); ?>%
                <?php echo $form->error($model, 'machineDefaultFee'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'freshMachineDefaultFee'); ?></th>
            <td>
                <?php echo $form->textField($model, 'freshMachineDefaultFee', array('class' => 'text-input-bj ')); ?>%
                <?php echo $form->error($model, 'freshMachineDefaultFee'); ?>
            </td>
        </tr>
                <tr>
            <th><?php echo $form->labelEx($model, 'storeDefaultFee'); ?></th>
            <td>
                <?php echo $form->textField($model, 'storeDefaultFee', array('class' => 'text-input-bj ')); ?>%
                <?php echo $form->error($model, 'storeDefaultFee'); ?>
            </td>
        </tr>
        
        <tr>
            <th></th>
            <td><?php echo CHtml::submitButton(Yii::t('home', '保存'), array('class' => 'reg-sub')); ?></td>
        </tr>
    </tbody>
</table>
<?php $this->endWidget(); ?>