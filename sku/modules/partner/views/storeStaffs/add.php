<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.storeStaffs','超市门店添加员工'),
    Yii::t('partnerModule.storeStaffs','添加员工'),
);

?>
    <div class="toolbar">
        <h3><?php echo Yii::t('partnerModule.storeStaffs','添加员工'); ?></h3>
    </div>
<?php $this->renderPartial('_form', array('model'=>$model)); ?>