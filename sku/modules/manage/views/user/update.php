<?php $this->breadcrumbs = array(Yii::t('user', '管理员') => array('admin'), Yii::t('user', '编辑')); ?>
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
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tbody><tr><td colspan="2" class="title-th even" align="center"><?php echo Yii::t('user', '编辑管理员信息'); ?></td></tr></tbody>
    <tbody>
        <tr><th colspan="2" class="odd"></th></tr>
        <tr>
            <th style="width: 220px" class="even"><?php echo $form->labelEx($model, 'username'); ?></th>
            <td class="even">
                <?php echo $form->textField($model, 'username', array('class' => 'text-input-bj middle', 'readonly' => true)); ?>
                <?php echo $form->error($model, 'username'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd"><?php echo $form->labelEx($model, 'real_name'); ?></th>
            <td class="odd">
                <?php echo $form->textField($model, 'real_name', array('class' => 'text-input-bj middle')); ?>
                <?php echo $form->error($model, 'real_name'); ?>
            </td>
        </tr>
        <tr>
            <th class="even"><?php echo $form->labelEx($model, 'sex'); ?></th>
            <td class="even">
                <?php echo $form->radioButtonList($model, 'sex', User::getSex(), array('separator' => '')); ?>
            </td>
        </tr>
        <tr>
            <th class="odd"><?php echo $form->labelEx($model, 'email'); ?></th>
            <td class="odd">
                <?php echo $form->textField($model, 'email', array('class' => 'text-input-bj middle')); ?>
                <?php echo $form->error($model, 'email'); ?>
            </td>
        </tr>
        <tr>
            <th class="even"><?php echo $form->labelEx($model, 'mobile'); ?></th>
            <td class="even">
                <?php echo $form->textField($model, 'mobile', array('class' => 'text-input-bj middle')); ?>
                <?php echo $form->error($model, 'mobile'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd"><?php echo $form->labelEx($model, 'status'); ?></th>
            <td class="odd">
                <?php echo $form->radioButtonList($model, 'status', User::getStatus(), array('separator' => '')); ?>
            </td>
        </tr>
        <tr>
            <th class="even"><?php echo $form->labelEx($model, 'role'); ?></th>
            <td class="even">
                <?php foreach ($roles as $role): ?>
                    <input <?php if (in_array($role['name'], $array)): ?>checked="checked"<?php endif; ?> type="checkbox" name="roles[]" value="<?php echo $role['name']; ?>" /><?php echo $role['description']; ?>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <th class="odd"></th>
            <td class="odd"><?php echo CHtml::submitButton(Yii::t('user', '编辑'), array('class' => 'reg-sub')); ?></td>
        </tr>
    </tbody>
</table>
<?php $this->endWidget(); ?>

