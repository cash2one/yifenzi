<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.storeGoods','门店商品管理'),
    Yii::t('partnerModule.storeGoods','修改门店商品'),
);
?>
<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.storeGoods','修改门店商品'); ?></h3>
</div>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>