<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.superGoods','商品管理'),
    Yii::t('partnerModule.superGoods','修改商品'),
);
?>
<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.superGoods','修改商品'); ?></h3>
</div>

<?php $this->renderPartial('_form', array('model'=>$model,'imgModel' => $imgModel,)); ?>