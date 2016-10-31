<?php
/* @var $this GameStoreDeliveryController */
/* @var $model GameStoreDelivery */
$title = Yii::t('GameStoreDelivery', '添加发货记录');
$this->pageTitle = $title . '-' . $this->pageTitle;
$this->breadcrumbs = array(
    Yii::t('GameStoreDelivery', '店铺发货管理') => array('index'),
    $title,
);
?>
<?php $this->renderPartial('_form', array('model'=>$model)); ?>