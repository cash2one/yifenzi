<?php
/* @var $this FranchiseeArtileController */
/* @var $model FranchiseeArtile */

$this->breadcrumbs=array(
    Yii::t('partnerModule.machine','售货机管理')=> array('list'),
    Yii::t('partnerModule.machine','售货机商品出库'), 
);
?>

<div class="toolbar">
	<h3><?php echo Yii::t('partnerModule.machine','售货机商品出库'); ?></h3>
</div>
<h3 class="mt15 tableTitle"><?php echo $model->name; ?></h3>

<?php $this->renderPartial('_goods_stock_form', array('model'=>$model)); ?>