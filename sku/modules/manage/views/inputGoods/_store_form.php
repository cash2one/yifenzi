<?php
/* @var $this InputGoodsController */
/* @var $model EnGoodsRule */


?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions' => array(
        'validateOnSubmit' => true, // 客户端验证
    ),
));
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come">
    <tr>
        <td colspan="2" class="title-th">
            <?php echo $model->isNewRecord ? Yii::t('InputGoods', '新增活动店铺'):Yii::t('InputGoods', '编辑活动店铺'); ?>
        </td>
    </tr> 
    <tr>
        <th><?php echo $form->labelEx($model,'name')?>：</th>
        <td><?php echo $form->textField($model, 'name', array('class' => 'text-input-bj')); ?>
                <?php echo $form->error($model, 'name'); ?></td>
    </tr>
    <tr>
        <th><?php echo $form->labelEx($model,'address')?>：</th>
        <td><?php echo $form->textField($model, 'address', array('class' => 'text-input-bj')); ?>
                <?php echo $form->error($model, 'address'); ?></td>
    </tr>
    
    <tr>    
          <th></th>
          <td>
            <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('inputGoods', '新增') : Yii::t('inputGoods', '编辑'), array('class' => 'reg-sub')); ?>
              <a href="release" ><?php echo CHtml::Button( Yii::t('inputGoods', '取消'), array('class' => 'reg-sub')); ?></a>
          </td>
      </tr>
      <?php $this->endWidget();?>
</table>
