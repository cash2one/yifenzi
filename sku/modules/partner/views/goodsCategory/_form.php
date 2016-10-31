<?php
/* @var $this GoodsCategoryController */
/* @var $model GoodsCategory */
/* @var $form CActiveForm */
?>
<h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.superGoods', '基本信息'); ?></h3>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
));
?>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
    <tr>
        <th width="10%"><?php echo $form->labelEx($model, 'name'); ?></th>
        <td width="90%">
            <?php echo $form->textField($model, 'name', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
            <?php echo $form->error($model, 'name'); ?>
        </td>
    </tr>

    <tr>
        <th><?php echo $form->labelEx($model, 'sort'); ?></th>
        <td>
            <?php echo $form->textField($model, 'sort', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?><span style="color:red">("<?php echo Yii::t('partnerModule.goodsCategory','默认最大排序为255'); ?>")</span>
            <?php echo $form->error($model, 'sort'); ?>
        </td>
    </tr>


    </tbody>
</table>


<div class="profileDo mt15">
    <a href="#" class="sellerBtn03 submitBt"><span><?php echo Yii::t('partnerModule.superGoods', '保存'); ?></span></a>&nbsp;&nbsp;
<!--    <a href="<?php echo $this->createAbsoluteUrl('goodsCategory/index') ?>" class="sellerBtn01">
        <span><?php echo Yii::t('partnerModule.superGoods', '返回'); ?></span>
    </a>-->
     <a href="javascript:history.go(-1);" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.superGoods', '返回'); ?></span></a>
</div>
<?php $this->endWidget(); ?>

<script>
    $(".submitBt").click(function () {
        $("form").submit();
    });
</script>

