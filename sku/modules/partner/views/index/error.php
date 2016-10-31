<?php
/* @var $this HomeController */
/* @var $error array */

$this->pageTitle=Yii::app()->name . ' - Error';
$this->breadcrumbs=array(
	'Error',
);
?>

<div class="toolbar">
    <h3><?php echo CHtml::encode($message); ?></h3>
</div>

<div class="error">
    <?php echo Yii::t('partnerModule.home','错误'); ?> <?php echo $code; ?>
</div>
<br/>
<div>
    <a href="javascript:history.back()"><?php echo Yii::t('partnerModule.home','[返回上一页]'); ?></a>
</div>