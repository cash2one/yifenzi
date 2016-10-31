<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.partner','商家管理'),
    Yii::t('partnerModule.partner','合作商家申请'),
);

?>
    <div class="toolbar">
        <h3><?php echo Yii::t('partnerModule.partner','合作商家&网签申请'); ?></h3>
    </div>
<?php $this->renderPartial('_sign_form', array('model'=>$model)); ?>