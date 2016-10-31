<?php
/* @var $this FranchiseeArtileController */
/* @var $model FranchiseeArtile */
/* @var $form CActiveForm */
?>
<h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.storeStaffs', '超市商品库存管理'); ?></h3>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'superStoreGoddsForm',
   'enableAjaxValidation' => false,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true, //客户端验证
    ),
        ));
?>
<style>
    .regm-sub{
        border:1px solid #ccc;
        background: #fff;
        padding: 5px;
        border-radius: 3px;
        cursor: pointer;
    }
</style>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
		<tbody>
		
			<tr>
				<th width="10%"><?php echo Yii::t('partnerModule.storeStaffs','商品名'); ?></th>
				<td width="90%">
					<?php echo $model->goods->name; ?>
            		<?php echo $form->error($model, 'stock'); ?>
				</td>
			</tr>
		
			<tr>
				<th width="10%"><?php echo Yii::t('partnerModule.storeStaffs','库存增\减数量'); ?></th>
				<td width="90%">
					<?php echo $form->textField($model, 'stock', array('class' => 'inputtxt1','style'=>'width:300px;')); ?>
            		<?php echo $form->error($model, 'stock'); ?>
				</td>
			</tr>
		
		</tbody>
	</table>
	<?php $this->endWidget(); ?>
	
	<div class="profileDo mt15">
		<a href="javascript:void(0);" class="sellerBtn03" onclick="$('#superStoreGoddsForm').submit();"><span><?php echo Yii::t('partnerModule.storeStaffs', '确定');   ?></span></a>&nbsp;&nbsp;<a href="javascript:history.go(-1);" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.storeStaffs','返回'); ?></span></a>
	</div>

