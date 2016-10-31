<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'storeCategory-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));
?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tr>
        <td colspan="2" class="title-th even" align="center"><?php echo $model->isNewRecord ? Yii::t('storeCategory', '增加店铺分类') : Yii::t('storeCategory', '修改店铺分类'); ?></td>
    </tr>
    <tr>
        <th width="25%" class="odd"><?php echo $form->labelEx($model, 'name'); ?></th>
        <td class="odd">
            <?php echo $form->textField($model, 'name', array('class' => 'text-input-bj middle')); ?>
            <?php echo $form->error($model, 'name'); ?>
        </td>
    </tr>
    <tr>
        <th ><?php echo $form->labelEx($model, 'style'); ?></th>
        <td>
            <p>
                <?php echo $form->fileField($model, 'style') ?>&nbsp;&nbsp;
                <span class="gray"><?php echo Yii::t('partner', '请上传不大于1M的图片'); ?></span>
            </p>
            <?php echo $form->error($model, 'style', array('style' => 'position: relative; display: inline-block'), false, false) ?>
            <?php if (!empty($model->style)): ?>
                <p class="mt10">
                    <img src="<?php echo ATTR_DOMAIN . '/' . $model->style ?>" width="120"/>
                </p>
            <?php endif; ?>
        </td>

    </tr>
    
    <tr>
        <th width="25%" class="odd"><?php echo $form->labelEx($model, 'name'); ?></th>
        <td class="odd">
            <?php echo $form->textField($model, 'sort', array('class' => 'text-input-bj small')); ?>
            <?php echo $form->error($model, 'sort'); ?>
        </td>
    </tr>
    
    <tr>
        <th class="odd"></th>
        <td class="odd">
            <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('storeCategory', '创建') : Yii::t('storeCategory', '保存'), array('class' => 'reg-sub')); ?>
        </td>
    </tr>
</table>

<?php $this->endWidget(); ?>
