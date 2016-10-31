<?php
/* @var $this InputGoodsController */
/* @var $model InputGoods */

$this->breadcrumbs=array(
    Yii::t('FreshMachine','发布管理','admin')=> array('release'),
    Yii::t('FreshMachine','产品库商品项目添加'),
);
?>


<?php $this->renderPartial('_rule_form', array('model'=>$model)); ?>