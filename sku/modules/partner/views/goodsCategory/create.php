<?php
/* @var $this GoodsCategoryController */
/* @var $model GoodsCategory */

$this->breadcrumbs=array(
    Yii::t('partnerModule.goodsCategory','商品管理')=>array('index'),
    Yii::t('partnerModule.goodsCategory','分类管理'),
);

?>
<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.goodsCategory','添加分类'); ?></h3>
</div>
<?php $this->renderPartial('_form', array('model'=>$model)); ?>

