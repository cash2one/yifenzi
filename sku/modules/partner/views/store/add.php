<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.store','超市门店管理'),
    Yii::t('partnerModule.store','门店申请'),
);

?>
    <div class="toolbar">
        <h3><?php echo Yii::t('partnerModule.store','门店申请'); ?></h3>
    </div>
<?php $this->renderPartial('_form', array('model'=>$model)); ?>