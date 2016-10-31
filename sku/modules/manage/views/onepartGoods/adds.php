<?php
/* @var $this OrderController */
/* @var $model Order */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#order-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
//	return false;
});
");

$this->breadcrumbs = array(
		Yii::t('order', '一份子后台管理'),
		Yii::t('order', '商品添加'),
);
?>
<?php $this->renderPartial('_form', array('model' => $model,'imgModel'=>$imgModel)); ?>