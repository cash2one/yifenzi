<?php
/* @var $this FreshMachineController */
/* @var $model FreshMachine */

$this->breadcrumbs=array(
    Yii::t('FreshMachine','发布管理','admin')=> array('release'),
    Yii::t('FreshMachine','产品库商品项目编辑'),
);
?>


<?php $this->renderPartial('_rule_form', array('model'=>$model)); ?>