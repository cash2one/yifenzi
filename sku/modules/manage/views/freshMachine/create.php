<?php
/* @var $this FreshMachineController */
/* @var $model FreshMachine */

$this->breadcrumbs=array(
    Yii::t('FreshMachine','生鲜机管理','admin')=> array('admin'),
    Yii::t('FreshMachine','添加生鲜机'),
);
?>


<?php $this->renderPartial('_form', array('model'=>$model)); ?>