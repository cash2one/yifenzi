<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.storeGoods','超市门店商品库存出货'),
    Yii::t('partnerModule.storeGoods','门店商品库存出货'),
);

?>
    <div class="toolbar">
        <h3><?php echo Yii::t('partnerModule.storeGoods','门店商品库存出货'); ?></h3>
    </div>
<?php $this->renderPartial('_stock', array('model'=>$model)); ?>