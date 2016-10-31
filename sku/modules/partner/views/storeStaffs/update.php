<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.storeStaffs','门店管理'),
    Yii::t('partnerModule.storeStaffs','修改门店员工资料'),
);
?>
<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.storeStaffs','修改门店员工资料'); ?></h3>
</div>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>