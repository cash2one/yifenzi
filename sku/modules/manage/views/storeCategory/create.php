<?php
$this->breadcrumbs = array(
    Yii::t('storeCategory', '店铺分类列表') => array('admin'),
    Yii::t('storeCategory', '增加店铺分类'),
);
?>

<?php $this->renderPartial('_form', array('model' => $model)); ?>