<?php
/* @var $this SkuBarcodeGoodsController */
/* @var $model SkuBarcodeGoods */

$this->breadcrumbs = array(Yii::t('barcodeGoods', '条码库管理') => array('admin'), Yii::t('category', '创建'));
$this->renderPartial('_form', array('model' => $model,'imgModel' => $imgModel,));
?>