<?php
/* @var $this GameStoreDeliveryController */
/* @var $model GameStoreDelivery */
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
        <h3><?php echo $model->isNewRecord ? Yii::t('gameStoreDelivery', '添加发货记录') : Yii::t('gameStoreDelivery', '编辑发货记录') ?> </h3>
    </div>
    <h3 class="mt15 tableTitle"><?php echo Yii::t('gameStoreDelivery', '发货信息') ?></h3>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
        <tbody><tr>
            <th width="10%"><b class="red">*</b><?php echo Yii::t('gameStoreDelivery', '发货商品信息') ?></th>
            <td width="90%">
                <?php echo $form->textField($model, 'delivery_items', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
                <?php echo $form->error($model, 'delivery_items') ?>
            </td>
        </tr>
        <tr>
            <th><b class="red">*</b><?php echo Yii::t('gameStoreDelivery', '发货时间') ?></th>
            <td>
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'id'=>'delivery_time',
                    'model'=>$model,
                    'name' => 'delivery_time',
                    'select'=>'datetime',
                    'htmlOptions' => array(
                        'readonly' => 'readonly',
                        'class' => 'inputtxt1 readonly',
                    )
                ));
                ?>
                <?php echo $form->error($model, 'delivery_time') ?>
            </td>
        </tr>
        </tbody></table>
    <div class="profileDo mt15">
        <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('gameStoreItems', '新增') : Yii::t('gameStoreItems', '保存'), array('class' => 'sellerBtn06')); ?>
    </div>
</div>
<?php $this->endWidget(); ?>
