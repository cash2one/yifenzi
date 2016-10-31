<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.partner','店小二管理'),
    Yii::t('partnerModule.partner','修改店小二'),
);
?>
  <div class="toolbar">
        <h3><?php echo Yii::t('partnerModule.partner','修改店小二'); ?></h3>
    </div>

<?php $this->renderPartial('_xiao_form', array('model'=>$model)); ?>