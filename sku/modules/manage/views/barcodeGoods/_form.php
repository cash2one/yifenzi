<?php
/* @var $this  BarcodeGoodsController */
/* @var $model BarcodeGoods */
/* @var $form CActiveForm */
?>
<?php
Yii::app()->clientScript->registerScriptFile(DOMAIN . '/js/artDialog/plugins/iframeTools.js', CClientScript::POS_END);
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'barcodeGoods-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ), 'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tbody>
    <tr>
        <th colspan="2" style="text-align: center" class="title-th">
            <?php if ($model->isNewRecord): ?>
                <?php echo Yii::t('category', '添加条形码'); ?>
            <?php else: ?>
                <?php echo Yii::t('category', '修改条形码'); ?>
            <?php endif; ?>
        </th>
    </tr>
    <tr>
        <th style="width: 220px"><?php echo $form->labelEx($model, 'barcode'); ?></th>
        <td>
            <?php echo $form->textField($model, 'barcode', array('class' => 'text-input-bj')); ?>
            <?php echo $form->error($model, 'barcode'); ?>
        </td>
    </tr>
    <tr>
        <th><?php echo $form->labelEx($model, 'name'); ?></th>
        <td>
            <?php echo $form->textField($model, 'name', array('class' => 'text-input-bj')); ?>
            <?php echo $form->error($model, 'name'); ?>
        </td>
    </tr>
    <tr>
        <th><?php echo $form->labelEx($model, 'default_price'); ?></th>
        <td>
            <?php echo $form->textField($model, 'default_price', array('class' => 'text-input-bj')); ?>
            <?php echo $form->error($model, 'default_price'); ?>
        </td>
    </tr>
    <tr>
        <th><?php echo $form->labelEx($model, 'model'); ?></th>
        <td>
            <?php echo $form->textField($model, 'model', array('class' => 'text-input-bj'));?>
            <?php echo $form->error($model, 'model'); ?>
        </td>
    </tr>
    <tr>
        <th><?php echo $form->labelEx($model, 'unit'); ?></th>
        <td>
            <?php echo $form->textField($model, 'unit', array('class' => 'text-input-bj')); ?>
            <?php echo $form->error($model, 'unit'); ?>
        </td>
    </tr>
    <tr>
        <th><?php echo Yii::t('barcode','商品描述'); ?></th>
        <td>
            <?php echo $form->textArea($model, 'describe', array('class' => 'text-input-bj long')); ?>
            <?php echo $form->error($model, 'describe'); ?>
        </td>
    </tr>
<!--    <tr>-->
<!--        <th>--><?php //echo $form->labelEx($model, 'outlets'); ?><!--</th>-->
<!--        <td>-->
<!--            --><?php //echo $form->textField($model, 'outlets', array('class' => 'text-input-bj')); ?>
<!--            --><?php //echo $form->error($model, 'outlets'); ?>
<!--        </td>-->
<!--    </tr>-->
    <tr>
        <th><?php echo $form->labelEx($model, 'thumb'); ?></th>
        <td>
            <?php echo $form->fileField($model, 'thumb'); ?>
            <?php echo $form->error($model, 'thumb',array('style' => 'position: relative; display: inline-block'), false, false); ?>
            <?php if (!$model->isNewRecord && $model->thumb): ?>
                <input type="hidden" name="oldThumbnail" value="<?php echo $model->thumb; ?>" />
                <?php echo CHtml::image(ATTR_DOMAIN . DS . $model->thumb, '', array('style' => 'max-width:250px')); ?>
            <?php endif; ?>
        </td>
    </tr>
    
        <tr>
            <th><?php echo $form->labelEx($imgModel, 'path'); ?></th>
            <td>
                <?php
                $this->widget('application.widgets.CUploadPic', array(
                    'attribute' => 'path',
                    'model' => $imgModel,
                    'form' => $form,
                    'num' => 6,
                    'btn_value' => Yii::t('sellerGoods', '上传图片'),
                    'render' => '_upload',
                    'folder_name' => 'files',
                    'include_artDialog' => false,
                ));
                ?>
                <?php echo $form->error($imgModel, 'path') ?>

            </td>
        </tr>

    <tr>
        <th></th>
        <td>
            <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('category', '创建') : Yii::t('category', '编辑'), array('class' => 'reg-sub')); ?>
        </td>
    </tr>
    </tbody>
</table>
<?php $this->endWidget(); ?>

