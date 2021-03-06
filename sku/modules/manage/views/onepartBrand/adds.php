<?php
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
		Yii::t('order', '品牌列表'),
        Yii::t('order', '品牌添加'),
);
?>
<?php if(Yii::app()->user->hasFlash('false')){
	echo Yii::app()->user->getFlash('false');
}?>
<?php
$form = $this->beginWidget('CActiveForm', array(
//	'id' => 'appVersion-form',
//	'enableAjaxValidation' => true,
	'enableClientValidation' => true,
	'clientOptions' => array(
		'validateOnSubmit' => true,
	),
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="sec">
    <tbody>
        <tr>
            <th colspan="2" class="title-th odd">基本信息</th>
        </tr>
        <tr>
			<td width="5%">
				<?php echo $form->labelEx($model,'brand_name');?>
			</td>
			<td>
				<?php echo $form->textField($model,'brand_name',array("class" => 'text-input-bj longest'));?>
				<?php echo $form->error($model,'brand_name');?>
			</td>
        </tr>
        <tr>
			<td>
				<?php echo $form->labelEx($model,'brand_logo');?>
			</td>
			<td>
				<?php
				$this->widget('application.widgets.CUploadPic', array(
					'attribute' => 'brand_logo',
					'model'=>$model,
					'form'=>$form,
					'num' => 1,
					'btn_value'=> Yii::t('sellerGoods', '上传图片'),
					'folder_name' => stristr($model->brand_logo,'/',true),
					'render'=>'_upload1',
					'include_artDialog' => true,
				));
				?>
				<?php echo $form->error($model,'brand_logo');?>
			</td>
        </tr>
        <tr>

			<td>
				<?php echo $form->labelEx($model,'brand_desc');?>
			</td>
			<td>
				<?php echo $form->textArea($model,'brand_desc',array('cols'=>"100", 'rows'=>"5"));?>
				<?php echo $form->error($model,'brand_desc');?>
			</td>
        </tr>
		<tr>
			<td>
			<?php echo $form->labelEx($model,'site_url');?>
			</td>
			<td>
				<?php echo $form->textField($model,'site_url',array("class" => 'text-input-bj longest'));?>
				<?php echo $form->error($model,'site_url');?>
			</td>
		</tr>
        <tr>
			<td>
				<?php echo $form->labelEx($model,'is_show');?>
			</td>
			<td>
				<?php echo $form->radioButtonList($model,'is_show',$htmlOptions=array('不显示','显示'));?>
				<?php echo $form->error($model,'is_show');?>
			</td>
        </tr>
        <tr>
			<td>
				<?php echo $form->labelEx($model,'sort_order'); ?>
			</td>
			<td>
				<?php echo $form->textField($model,'sort_order'); ?>
				<?php echo $form->error($model,'sort_order'); ?>
			</td>
        </tr>
       <tr>
			<td>
			</td>
			<td>
				<?php echo CHtml::submitButton('添加品牌', array('class' => 'regm-sub')); ?>
			</td>
        </tr>

    </tbody>
</table>
<?php $this->endWidget();?>
</form>