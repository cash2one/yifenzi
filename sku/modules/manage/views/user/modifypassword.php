<?php $this->breadcrumbs = array(Yii::t('user', '管理员') => array('admin'), Yii::t('user', '修改密码')); ?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'user-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true
    ),
        ));
?>
<?php // echo $form->errorSummary($model); ?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tbody><tr><td colspan="2" class="title-th even" align="center"><?php echo Yii::t('user', '密码信息'); ?></td></tr></tbody>
    <tbody>
        <tr><th colspan="2" class="odd"></th></tr>
        <tr>
            <th class="even"><?php echo $form->labelEx($model, 'originalPassword'); ?></th>
            <td class="even">
                <?php echo $form->passwordField($model, 'originalPassword', array('class' => 'text-input-bj middle')); ?>
                <?php echo $form->error($model, 'originalPassword'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd"><?php echo $form->labelEx($model, 'password'); ?></th>
            <td class="odd">
                <?php echo $form->passwordField($model, 'password', array('class' => 'text-input-bj middle')); ?>
                <?php echo $form->error($model, 'password'); ?>
            </td>
        </tr>
        <tr>
            <th class="even"><?php echo $form->labelEx($model, 'confirmPassword'); ?></th>
            <td class="even">
                <?php echo $form->passwordField($model, 'confirmPassword', array('class' => 'text-input-bj middle')); ?>
                <?php echo $form->error($model, 'confirmPassword'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd"></th>
            <td class="odd"><?php echo CHtml::submitButton(Yii::t('user', '修改'), array('class' => 'reg-sub')); ?></td>
        </tr>
    </tbody>
</table>
<?php $this->endWidget(); ?>