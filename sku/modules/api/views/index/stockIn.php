<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo Yii::t('main', '盖网通后台管理'); ?></title>
    </head>
    <body>
    

<?php echo CHtml::form(Yii::app()->createAbsoluteUrl('api/stock/in'))?>

项目： <?php echo CHtml::dropDownList('project', GoodsStock::PROJECT_SKU_SUPER, GoodsStock::getProject())?>
<br/>

网点：<?php echo CHtml::textField('outlets',3) ; ?>
<br/> 

产品id或者条形码：<?php echo CHtml::textField('target',456789132456) ; ?>
<br/> 

数量：<?php echo CHtml::textField('num',2) ; ?>
<br/> 

校验码：<?php echo CHtml::textField('encryptCode') ; ?>
<br/>  

<?php echo CHtml::submitButton('提交');?>

<?php echo CHtml::endForm() ?>


</body>
</html>

