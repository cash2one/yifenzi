<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.machine','售货机列表')=>array('list'),
    Yii::t('partnerModule.machine','售货机编辑'),
);

?>
    <div class="toolbar">
        <h3><?php echo Yii::t('partnerModule.machine','售货机编辑'); ?></h3>
    </div>
<?php $this->renderPartial('_form', array('model'=>$model));