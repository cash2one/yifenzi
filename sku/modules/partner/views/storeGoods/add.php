<?php
/* @var $this AssistantController */
/* @var $model Assistant */

$this->breadcrumbs=array(
    Yii::t('partnerModule.storeGoods','超市门店添加商品'),
    Yii::t('partnerModule.storeGoods','添加商品'),
);

?>
    <div class="toolbar">
        <h3><?php echo Yii::t('partnerModule.storeGoods','添加商品'); ?></h3>
    </div>
<?php $this->renderPartial('_form', array('model'=>$model)); ?>