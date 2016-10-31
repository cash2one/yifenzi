<?php
$this->breadcrumbs = array(
    Yii::t('appAdvert', '广告位') => array('admin'),
    $model->isNewRecord ? Yii::t('appAdvert', '新增') : Yii::t('appAdvert', '修改')
);
?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'appAdvert-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));
?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tbody><tr><td colspan="2" class="title-th even" align="center"><?php echo $model->isNewRecord ? Yii::t('appAdvert', '添加广告位') : Yii::t('appAdvert', '修改广告位'); ?></td></tr></tbody>
    <tbody>
        <tr>
            <th style="width: 220px" class="odd">
                <?php echo $form->labelEx($model, 'name'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textField($model, 'name', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'name'); ?>
            </td>
        </tr>
        <tr>
            <th class="even">
                <?php echo $form->labelEx($model, 'code'); ?>
            </th>
            <td class="even">
                <?php echo $form->textField($model, 'code', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'code'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'content'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textArea($model, 'content', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'content'); ?>
            </td>
        </tr>
        <tr>
            <th class="even">
                <?php echo $form->labelEx($model, 'type'); ?>
            </th>
            <td class="even">
                <?php echo $form->dropDownList($model, 'type', AppAdvert::getAppAdvertType(), array('prompt' => '请选择', 'class' => 'text-input-bj')); ?>
                <?php echo $form->error($model, 'type'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'status'); ?>
            </th>
            <td class="odd">
                <?php echo $form->radioButtonList($model, 'status', AppAdvert::getAppAdvertStatus(), array('separator' => '')); ?>
                <?php echo $form->error($model, 'status'); ?>
            </td>
        </tr>
        <tr>
            <th class="even">
                <?php echo $form->labelEx($model, 'width'); ?>
            </th>
            <td class="even">
                <?php echo $form->textField($model, 'width', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'width'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'height'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textField($model, 'height', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'height'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd"></th>
            <td colspan="2" class="odd">
                <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('appAdvert', '新增') : Yii::t('appAdvert', '保存'), array('class' => 'reg-sub')); ?>
            </td>
        </tr>
    </tbody>
</table>
<?php $this->endWidget(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        toggleTr($('input[name="AppAdvert[direction]"]:checked').val());
    });
    function toggleTr(value) {
        $("#cityTr").hide();
        $("#categoryTr").hide();
        if (value === '1') {
            $("#cityTr").show();
        } else if (value === '2') {
            $("#categoryTr").show();
        }
    }
</script>
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
    $('#AppAdvert_category_id').val(Id);
    $('#category_name').val(Name);
};
var doClose = function() {
    if (null != dialog) {
        dialog.close();
    }
};
", CClientScript::POS_HEAD);
?>