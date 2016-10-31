<?php

/* @var $this CategoryController */
/* @var $model Category */
$this->breadcrumbs = array(Yii::t('category', '商品分类') => array('admin'), Yii::t('category', '编辑'));
$this->renderPartial('_form', array('model' => $model));
?>