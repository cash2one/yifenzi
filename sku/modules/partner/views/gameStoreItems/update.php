<?php
/* @var $this GameStoreItemsController */
/* @var $model GameStoreItems */
$title = Yii::t('GameStoreItems', '编辑店铺商品');
$this->pageTitle = $title . '-' . $this->pageTitle;
$this->breadcrumbs = array(
    Yii::t('GameStoreItems', '店铺商品管理') => array('index'),
    $title,
);
?>
<?php $this->renderPartial('_form', array('model'=>$model)); ?>