<?php
/* @var $this FreshMachineController */
/* @var $model FreshMachine */

$this->breadcrumbs=array(
    Yii::t('FreshMachine','发布管理','admin')=> array('storeActive'),
    Yii::t('FreshMachine','店铺商品编辑'),
);
?>

<?php $this->renderPartial('_goods_form', array('model'=>$model,'data'=>$data,'rule_arr'=>$rule_arr)); ?>