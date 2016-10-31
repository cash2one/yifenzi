<?php
/* @var $this InputGoodsController */
/* @var $model StoreActive */

$this->breadcrumbs=array(
    Yii::t('InputGoods','发布管理','admin')=> array('storeActive'),
    Yii::t('InputGoods','产品库商品项目编辑'),
);
?>


<?php $this->renderPartial('_store_form', array('model'=>$model)); ?>