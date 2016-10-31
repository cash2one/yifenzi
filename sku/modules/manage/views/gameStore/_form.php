<?php
$this->breadcrumbs = array(
    Yii::t('gameStore', '游戏配置管理') => array('admin'),
    $model->isNewRecord ? Yii::t('gameStore', '添加店铺') : Yii::t('gameStore', '修改店铺')
);
?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'gameStore-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
?>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tbody>
    <tr>
        <td colspan="2" class="title-th even"
            align="center"><?php echo $model->isNewRecord ? Yii::t('gameStore', '添加店铺') : Yii::t('gameStore', '修改店铺'); ?></td>
    </tr>
    </tbody>
    <tbody>
    <tr>
        <th style="width: 220px" class="odd">
            <?php echo $form->labelEx($model, 'gai_number'); ?>
        </th>
        <td class="odd">
            <?php echo $form->textField($model, 'gai_number', array('class' => 'text-input-bj  middle')); ?>
            <?php echo $form->error($model, 'gai_number'); ?>
        </td>
    </tr>
    <tr>
        <th class="even">
            <?php echo $form->labelEx($model, 'store_name'); ?>
        </th>
        <td class="even">
            <?php echo $form->textField($model, 'store_name', array('class' => 'text-input-bj  middle')); ?>
            <?php echo $form->error($model, 'store_name'); ?>
        </td>
    </tr>
    <tr>
        <th class="odd">
            <?php echo $form->labelEx($model, 'store_phone'); ?>
        </th>
        <td class="odd">
            <?php echo $form->textField($model, 'store_phone', array('class' => 'text-input-bj  middle')); ?>
            <?php echo $form->error($model, 'store_phone'); ?>
        </td>
    </tr>
    <tr>
        <th class="even">
            <?php echo $form->labelEx($model, 'store_address'); ?>
        </th>
        <td class="even">
            <?php echo $form->textField($model, 'store_address', array('class' => 'text-input-bj  longest')); ?>
            <?php echo $form->error($model, 'store_address'); ?>
        </td>
    </tr>
    <tr>
        <th class="odd">
            <?php echo $form->labelEx($model, 'store_status'); ?>
        </th>
        <td class="odd">
            <?php echo $form->radioButtonList($model, 'store_status', GameStore::status(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'store_status'); ?>
        </td>
    </tr>
    <tr>
        <th class="even">
            <?php echo $form->labelEx($model, 'franchise_stores'); ?>
        </th>
        <td class="even">
            <?php echo $form->radioButtonList($model, 'franchise_stores', GameStore::franchiseStores(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'franchise_stores'); ?>
        </td>
    </tr>
    <tr>
        <th class="odd">
            <?php echo "已抢限制"; ?>
        </th>
        <td class="odd">
            <?php echo $form->textField($model, 'limit_time_hour', array('class' => 'text-input-bj least')); ?>小时
            <?php echo $form->error($model, 'limit_time_hour'); ?>
            <?php echo $form->textField($model, 'limit_time_minute', array('class' => 'text-input-bj least')); ?>分钟(限定填写0-24小时，0-60分钟)
            <?php echo $form->error($model, 'limit_time_minute'); ?>
        </td>
    </tr>
    <tr>
        <th class="even"></th>
        <td colspan="2" class="even">
            <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('gameStore', '新增') : Yii::t('gameStore', '保存'), array('class' => 'reg-sub')); ?>
        </td>
    </tr>
    </tbody>
</table>
<?php $this->endWidget(); ?>