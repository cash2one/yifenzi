<?php
/* @var $this CategoryController */
/* @var $model Category */
/* @var $form CActiveForm */

$form = $this->beginWidget('CActiveForm', array(
    'id' => 'category-form',
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
                    <?php echo Yii::t('category', '添加分类'); ?>
                <?php else: ?>
                    <?php echo Yii::t('category', '修改分类'); ?>
                <?php endif; ?>
            </th>
        </tr>
        <tr>
            <th style="width: 220px"><?php echo $form->labelEx($model, 'name'); ?></th>
            <td>
                <?php echo $form->textField($model, 'name', array('class' => 'text-input-bj')); ?>
                <?php echo $form->error($model, 'name'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'short_name'); ?></th>
            <td>
                <?php echo $form->textField($model, 'short_name', array('class' => 'text-input-bj')); ?>
                <?php echo $form->error($model, 'short_name'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'parent_id'); ?></th>
            <td>
                <?php echo $form->hiddenField($model, 'parent_id', array('value' => $model->parent_id ? $model->parent_id : 0)); ?>
                <?php echo CHtml::textField('parent_name', $model->parentClass ? $model->parentClass->name : '顶级分类', array('class' => 'text-input-bj', 'readonly' => 'true')); ?>
                <?php echo $form->error($model, 'parent_id'); ?>
                <?php if ($model->isNewRecord): ?>
                    <?php echo CHtml::button(Yii::t('category', '选择'), array('class' => 'reg-sub', 'id' => 'getTree')); ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'alias'); ?></th>
            <td>
                <?php echo $form->textField($model, 'alias', array('class' => 'text-input-bj')); ?>
                <?php echo $form->error($model, 'alias'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'type_id'); ?></th>
            <td>
                <?php echo $form->dropDownList($model, 'type_id', Type::model()->getTypeData(), array('prompt' => '请选择', 'class' => 'text-input-bj')); ?>
                <?php echo $form->error($model, 'type_id'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'sort'); ?></th>
            <td>
                <?php echo $form->textField($model, 'sort', array('class' => 'text-input-bj')); ?>
                <?php echo $form->error($model, 'sort'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'status'); ?></th>
            <td>
                <?php echo $form->radioButtonList($model, 'status', Category::getStatus(), array('separator' => '')); ?>
                <?php echo $form->error($model, 'status'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'thumbnail'); ?></th>
            <td>
                <?php echo $form->fileField($model, 'thumbnail'); ?>
                <?php echo $form->error($model, 'thumbnail', array(), false); ?>
                <?php if (!$model->isNewRecord && $model->thumbnail): ?>
                    <input type="hidden" name="oldThumbnail" value="<?php echo $model->thumbnail; ?>" />
                    <?php echo CHtml::image(ATTR_DOMAIN . DS . $model->thumbnail, '', array('width' => '37px', 'height' => '37px')); ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'picture'); ?></th>
            <td>
                <?php echo $form->fileField($model, 'picture'); ?>
                <?php echo $form->error($model, 'picture', array(), false); ?>
                <?php if (!$model->isNewRecord && $model->picture): ?>
                    <input type="hidden" name="oldPicture" value="<?php echo $model->picture; ?>" />
                    <?php echo CHtml::image(ATTR_DOMAIN . DS . $model->picture, '', array('width' => '37px', 'height' => '37px')); ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'recommend'); ?></th>
            <td>
                <?php echo $form->radioButtonList($model, 'recommend', Category::getRecommend(), array('separator' => '')); ?>
                <?php echo $form->error($model, 'recommend'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'fee'); ?></th>
            <td>
                <?php echo $form->textField($model, 'fee', array('class' => 'text-input-bj')); ?>%
                <?php if (!$model->isNewRecord): ?>
                    <?php echo $form->checkBox($model, 'applyToChilden'); ?>
                    <?php echo $form->labelEx($model, 'applyToChilden'); ?>
                <?php endif; ?>
                <?php echo $form->error($model, 'fee'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'keywords'); ?></th>
            <td>
                <?php echo $form->textArea($model, 'keywords', array('class' => 'text-input-bj', 'cols' => 50)); ?>
                <?php echo $form->error($model, 'keywords'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'description'); ?></th>
            <td>
                <?php echo $form->textArea($model, 'description', array('class' => 'text-input-bj', 'cols' => 50)); ?>
                <?php echo $form->error($model, 'description'); ?>
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
<script src="/js/iframeTools.js" type="text/javascript"></script>
<?php
Yii::app()->clientScript->registerScript('categoryTree', "
var dialog = null;
jQuery(function($) {
        var url = '" . $this->createUrl('/category/categoryTree') . "';
        $('#getTree').click(function() {
            dialog = art.dialog.open(url, {'id': 'SearchCat', title: '搜索类别', width: '640px', height: '600px', lock: true});
        })
})
var onSelectedCat = function(Id, Name) {
    $('#Category_parent_id').val(Id);
    $('#parent_name').val(Name);
};
var doClose = function() {
    if (null != dialog) {
        dialog.close();
    }
};
", CClientScript::POS_HEAD);
?>

