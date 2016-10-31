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
        Yii::t('order', '广告添加'),
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
				<?php echo $form->labelEx($model,'advertising_name');?>
			</td>
			<td>
				<?php echo $form->textField($model,'advertising_name');?>
				<?php echo $form->error($model,'advertising_name');?>
			</td>
        </tr>
        <tr>
			<td>
				<?php echo $form->labelEx($model,'types');?>
			</td>
			<td>
				<?php echo $form->dropDownList($model, 'types', Advertising::getAppAdvertTypeSlide(), array('class' => 'text-input-bj')); ?>
				<?php echo $form->error($model,'types');?>
			</td>
        </tr>
        <tr>

			<td>
				<?php echo $form->labelEx($model,'img');?>
			</td>
			<td>
				<?php
				$this->widget('application.widgets.CUploadPic', array(
					'attribute' => 'img',
					'model'=>$model,
					'form'=>$form,
					'num' => 1,
					'btn_value'=> Yii::t('sellerGoods', '上传图片'),
					'folder_name' => stristr($model->img,'/',true),
					'render'=>'_upload1',
					'include_artDialog' => true,
				));
				?>
				<?php echo $form->error($model,'img');?>
			</td>
        </tr>
		<tr>
			<td>
			<?php echo $form->labelEx($model,'tourl');?>
			</td>
			<td>
				<?php echo $form->textField($model,'tourl');?>
				<?php echo $form->error($model,'tourl');?>
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
				<?php echo $form->labelEx($model,'img_h'); ?>
			</td>
			<td>
				<?php echo $form->textField($model,'img_h'); ?>
				<?php echo $form->error($model,'img_h'); ?>
			</td>
        </tr>
		<tr>
			<td>
				<?php echo $form->labelEx($model,'img_w'); ?>
			</td>
			<td>
				<?php echo $form->textField($model,'img_w'); ?>
				<?php echo $form->error($model,'img_w'); ?>
			</td>
        </tr>
       <tr>
			<td>
			</td>
			<td>
				<?php echo CHtml::submitButton(Yii::t('goods', '添加'), array('class' => 'reg-sub')); ?>
			</td>
        </tr>

    </tbody>
</table>
<?php $this->endWidget();?>
</form>