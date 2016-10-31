<?php
/* @var $this GameStoreItemsController */
/* @var $model GameStoreItems */
$title = Yii::t('GameStoreItems', '编辑店铺特殊商品');
$this->pageTitle = $title . '-' . $this->pageTitle;
$this->breadcrumbs = array(
    Yii::t('GameStoreItems', '店铺特殊商品管理') => array('index'),
    $title,
);
?>
<?php $this->renderPartial('_formflag', array('model'=>$model)); ?>