
<?php
/* @var $this OrderController */
/* @var $model Order */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#order-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
//	return false;
});
");

$this->breadcrumbs = array(
	Yii::t('order', '一份子后台管理'),
	Yii::t('order', '栏目添加'),
);
?>
<?php


$form = $this->beginWidget('CActiveForm', array(
	"id" => 'appVersion-form',
	"enableAjaxValidation" => false,
	"enableClientValidation" => true,
	"clientOptions" => array(
		'validateOnSubmit' => true,
	),
	"htmlOptions" => array("enctype" => 'multipart/form-data'),
));
?>

	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="sec">
		<tbody>
		<tr>
			<th colspan="2" class="title-th odd">基本信息</th>
		</tr>
		<tr>
			<td width="5%">
				<?php echo $form->labelEx($model, 'column_name'); ?>
			</td>
			<td>
				<?php echo $form->textField($model, 'column_name',array("class" => 'text-input-bj longest')); ?>
				<?php echo $form->error($model, 'column_name',array('style'=>'bottom:6px;left:620px')) ?>
			</td>
		</tr>
		<tr>

			<td>
				<?php echo $form->labelEx($model,'column_desc');?>
			</td>
			<td>
				<?php echo $form->textArea($model,'column_desc',array('cols'=>"100", 'rows'=>"5"));?>
				<?php echo $form->error($model,'column_desc');?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $form->labelEx($model, 'column_type'); ?>
			</td>
			<td>
				<?php echo $form->dropDownList($model, 'column_type', Column::getColumType(), array('class' => 'text-input-bj')); ?>
				<?php echo $form->error($model, 'column_type'); ?>
			</td>
		</tr>
		<!--		<tr>-->
		<!--			<td>-->
		<!--				--><?php //echo $form->labelEx($model, 'parent_id'); ?>
		<!--			</td>-->
		<!--			<td>-->
		<!--				--><?php //echo $form->dropDownList($model,'parent_id',Column::getParentColumn(),$htmlOptions=array('prompt' => '请选择','class' => 'text-input-bj'));?>
		<!--				--><?php //echo $form->error($model, 'parent_id'); ?>
		<!--			</td>-->
		<!--		</tr>-->
		<tr>
			<td class="even">
				<?php echo $form->labelEx($model, 'column_att'); ?>
			</td>
			<td>
				<?php echo $form->dropDownList($model, 'column_att', Column::getColumAtt(), array('class' => 'text-input-bj')); ?>
				<?php echo $form->error($model, 'column_att'); ?>
			</td>
		</tr>
		<tr>
			<td class="even">
				<?php echo $form->labelEx($model,'tourl');?>
			</td>
			<td>
				<?php echo $form->textField($model,'tourl',array("class" => 'text-input-bj longest'));?>
				<?php echo $form->error($model,'tourl',array("class" => 'text-input-bj longest'));?>
			</td>
		</tr>
		<tr>
			<td class="even">
				<?php echo $form->labelEx($model,'sort_order'); ?>
			</td>
			<td>
				<?php echo $form->textField($model,'sort_order',array('value'=>'0')); ?>
				<?php echo $form->error($model,'sort_order'); ?>
			</td>
		</tr>


		<tr>
			<td>
				<?php echo $form->labelEx($model, 'is_zone'); ?>
			</td>
			<td>
				<?php echo $form->radioButtonList($model,'is_zone',$htmlOptions=array('不显示','显示'));?>
				<?php echo $form->error($model, 'is_zone'); ?>
			</td>
		</tr>
		<!--缩略图-->
		<tr>
			<td class="odd">
				<?php echo $form->labelEx($model,'zone_thumb'); ?>
			</td>
			<td>
				<?php
				$this->widget('application.widgets.CUploadPic', array(
					'attribute' => 'zone_thumb',
					'model' => $model,
					'form' => $form,
					'num' => 1,
					'btn_value' => Yii::t('sellerGoods', '上传图片'),
					'render'=>'upload',
					'folder_name' => 'files',
					'include_artDialog' => true,
					'upload_width' => 280,
					'upload_height' => 160,
				));
				?>
				<?php echo $form->error($model, 'zone_thumb',array('style'=>'bottom:26px;left:93px')) ?>
				&nbsp;<div class="gray">(<?php echo Yii::t('YifenGoods', '最多上传1张（宽280*高160）,请先删除后上传'); ?>)</div>
			</td>
		</tr>
		<!--缩略图-->
		<tr>
			<td class="odd">
				<?php echo $form->labelEx($model,'column_logo'); ?>
			</td>
			<td>
				<?php
				$this->widget('application.widgets.CUploadPic', array(
					'attribute' => 'column_logo',
					'model' => $model,
					'form' => $form,
					'num' => 1,
					'btn_value' => Yii::t('sellerGoods', '上传图片'),
					'render'=>'_upload',
					'folder_name' => 'files',
					'include_artDialog' => true,
					'upload_width' => 40,
					'upload_height' => 40,
				));
				?>
				<?php echo $form->error($model, 'column_logo',array('style'=>'bottom:26px;left:93px')) ?>
				&nbsp;<div class="gray">(<?php echo Yii::t('YifenGoods', '最多上传1张（宽40*高40）,请先删除后上传'); ?>)</div>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $form->labelEx($model, 'is_show'); ?>
			</td>
			<td>
				<?php echo $form->radioButtonList($model,'is_show',$htmlOptions=array('不显示','显示'));?>
				<?php echo $form->error($model, 'is_show'); ?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<?php echo CHtml::submitButton(Yii::t('goods', '添加'), array('class' => 'reg-sub')); ?>
			</td>
		</tr>

		</tbody>







	</table>

<?php $this->endWidget();?>

