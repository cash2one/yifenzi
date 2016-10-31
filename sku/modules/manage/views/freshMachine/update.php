<?php
/* @var $this FreshMachineController */
/* @var $model FreshMachine */

$this->breadcrumbs=array(
    Yii::t('FreshMachine','生鲜机管理')=> array('admin'),
    Yii::t('FreshMachine','修改资料'),
);
?>


<?php $this->renderPartial('_form', array('model'=>$model)); ?>