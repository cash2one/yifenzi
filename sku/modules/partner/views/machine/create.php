<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.machine','售货机管理'),
    Yii::t('partnerModule.machine','售货机申请'),
);

?>
    <div class="toolbar">
        <h3><?php echo Yii::t('partnerModule.machine','售货机申请'); ?></h3>
    </div>
<?php $this->renderPartial('_form', array('model'=>$model));