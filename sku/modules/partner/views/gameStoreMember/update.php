<?php
/* @var $this GameStoreMemberController */
/* @var $model GameStoreMember */
$title = Yii::t('GameStoreMember', '编辑用户信息');
$this->pageTitle = $title . '-' . $this->pageTitle;
$this->breadcrumbs = array(
    Yii::t('GameStoreMember', '用户信息管理') => array('index'),
    $title,
);
?>
<?php $this->renderPartial('_form', array('model'=>$model)); ?>