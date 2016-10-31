<?php
/* @var $this GameStoreMemberController */
/* @var $model GameStoreMember */
/* @var $form CActiveForm */
?>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id .'-form',
//     'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
));
?>
<div class="mainContent">
    <div class="toolbar">
        <h3><?php Yii::t('gameStoreMember', '编辑用户信息') ?> </h3>
    </div>
    <h3 class="mt15 tableTitle"><?php echo Yii::t('gameStoreMember', '用户信息') ?></h3>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
        <tbody><tr>
            <th width="10%"><b class="red">*</b><?php echo Yii::t('gameStoreMember', '用户姓名') ?></th>
            <td width="90%">
                <?php echo $form->textField($model, 'real_name', array('class' => 'inputtxt1', 'style' => 'width:100px')); ?>
                <?php echo $form->error($model, 'real_name') ?>
            </td>

        </tr>
        <tr>
            <th><b class="red">*</b><?php echo Yii::t('gameStoreMember', '手机号') ?></th>
            <td width="90%">
                <?php echo $form->textField($model, 'mobile', array('class' => 'inputtxt1', 'style' => 'width:150px')); ?>
                <?php echo $form->error($model, 'mobile') ?>
            </td>
        </tr>
        <tr>
            <th><?php echo Yii::t('gameStoreMember', '用户地址') ?></th>
            <td>
                <?php echo $form->textField($model, 'member_address', array('class' => 'inputtxt1','style' => 'width:600px')); ?>
                <?php echo $form->error($model, 'member_address') ?>
            </td>
        </tr>
        <tr>
            <th><?php echo Yii::t('items_info', '商品信息') ?></th>
            <td>
                <?php echo $form->textField($model, 'items_info', array('class' => 'inputtxt1','style' => 'width:600px','readonly' => true)); ?>
                <?php echo $form->error($model, 'items_info') ?>
            </td>
        </tr>
        </tbody></table>
    <div class="profileDo mt15">
        <?php echo CHtml::submitButton( Yii::t('gameStoreMember', '保存'), array('class' => 'sellerBtn06')); ?>
    </div>
</div>
<?php $this->endWidget(); ?>
