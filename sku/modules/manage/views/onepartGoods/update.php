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
    Yii::t('order', '商品'),
    Yii::t('order', '商品编辑'),
);
?>
<?php $this->renderPartial('_form', array('model' => $model,'imgModel'=>$imgModel)); ?>