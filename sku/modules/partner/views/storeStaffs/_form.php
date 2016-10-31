<?php
/* @var $this FranchiseeArtileController */
/* @var $model FranchiseeArtile */
/* @var $form CActiveForm */
?>
<h3 class="mt15 tableTitle"><?php echo $model->isNewRecord ? Yii::t('partnerModule.storeStaffs', '添加超市员工') : Yii::t('partnerModule.storeStaffs', '更新超市员工信息'); ?></h3>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'superStoreStaffsForm',
    'enableAjaxValidation' => false,
    'enableClientValidation' => true,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions' => array(
        'validateOnSubmit' => true, //客户端验证
    ),
        ));
?>
<style>
    .regm-sub{
        border:1px solid #ccc;
        background: #fff;
        padding: 5px;
        border-radius: 3px;
        cursor: pointer;
    }
</style>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>


        <tr>
            <th width="10%"><?php echo $form->label($model, 'name') ?><span class="required">*</span></th>
            <td width="90%">

                <?php if ($model->isNewRecord): ?>

                    <?php echo $form->textField($model, 'name', array('class' => 'inputtxt1', 'style' => 'width:300px;')); ?>
                    <?php echo $form->error($model, 'name'); ?>

                <?php else : ?>

                    <?php echo $model->name; ?>

                <?php endif; ?>

            </td>
        </tr>




        <tr>
            <th width="10%"><?php echo $form->label($model, 'nick_name') ?><span class="required">*</span></th>
            <td width="90%">
                <?php echo $form->textField($model, 'nick_name', array('class' => 'inputtxt1', 'style' => 'width:300px;')); ?>
                <?php echo $form->error($model, 'nick_name'); ?>
            </td>
        </tr>
        
        <tr>
            <th width="10%"><?php echo $form->label($model, 'password') ?><?php if ($model->isNewRecord): ?><span class="required">*</span><?php else : ?> <?php endif; ?></th>
            <td width="90%">
                <?php echo $form->passwordField($model, 'password', array('class' => 'inputtxt1', 'style' => 'width:300px;')); ?><?php if(!$model->isNewRecord):?><span style="color:red">("<?php echo Yii::t('partnerModule.storeStaffs','不填则不修改密码');?>")<?php endif;?></span>
                <?php echo $form->error($model, 'password'); ?>
            </td>
        </tr>

        <tr>
            <th><?php echo $form->labelEx($model, 'head'); ?></th>
            <td>

                <?php echo $form->fileField($model, 'head') ?>
                <span class="gray"><?php echo Yii::t('partnerModule.storeStaffs', '请上传不大于1M的图片'); ?></span>

                <?php echo $form->error($model, 'head'); ?>

                <p class="mt10">
                    <?php
                    if (!$model->isNewRecord) {
                        echo CHtml::image(ATTR_DOMAIN . '/' . $model->head, $model->name, array('width' => '200px'));
                    }
                    ?>
                </p>


            </td>
        </tr>

        <tr>
            <th width="10%"><?php echo $form->label($model, 'mobile') ?><span class="required">*</span></th>
            <td width="90%">
                <?php echo $form->textField($model, 'mobile', array('class' => 'inputtxt1', 'style' => 'width:300px;')); ?>
                <?php echo $form->error($model, 'mobile'); ?>
            </td>
        </tr>

        <tr>
            <th width="10%"><?php echo $form->label($model, 'status') ?><span class="required">*</span></th>
            <td width="90%">
                <?php echo $form->dropDownList($model, 'status', SuperStaffs::getStatus(), array('class' => '')) ?>
                <?php echo $form->error($model, 'status'); ?>
            </td>
        </tr>


    </tbody>
</table>
<?php $this->endWidget(); ?>

<div class="profileDo mt15">
    <a href="javascript:void(0);" class="sellerBtn03" onclick="$('#superStoreStaffsForm').submit();"><span><?php echo $model->isNewRecord ? Yii::t('partnerModule.storeStaffsStaffs', '添加') : Yii::t('partnerModule.storeStaffsStaffs', '确定'); ?></span></a>&nbsp;&nbsp;<a href="javascript:history.go(-1);" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.storeStaffsStaffs', '返回'); ?></span></a>
</div>

<script type="text/javascript" language="javascript" src="<?php echo DOMAIN ?>/js/iframeTools.source.js"></script>
<?php
Yii::app()->clientScript->registerScript('superStoreStaffs', "
var dialog = null;
jQuery(function($) {
    $('#seachRefMem').click(function() {
        dialog = art.dialog.open('" . $this->createAbsoluteUrl('goods/searchList') . "', { 'id': 'selectGoods', title: '".Yii::t('partnerModule.storeStaffs','搜索员工')."', width: '1000px', height: '520px', lock: true });
    })
})
 var doClose = function() {
                if (null != dialog) {
                    dialog.close();
                }
            };
var onSelectGoods = function (id,name,thumb,spec_id) {
    if (id) {
        $('#SuperGoods_goods_id').val(id);
        $('#RefGoodsName').val(name);
    }
};
	                		        		
", CClientScript::POS_HEAD);
?>
