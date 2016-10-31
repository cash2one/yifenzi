<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.partner','商家资料管理'),
    Yii::t('partnerModule.partner','修改资料'),
);
?>
<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.partner','修改资料'); ?></h3>
</div>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>