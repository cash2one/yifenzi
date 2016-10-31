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
           <?php echo $model->isNewRecord ? Yii::t('InputGoods', '产品库商品项目添加'):Yii::t('InputGoods', '产品库商品项目编辑'); ?>
        </td>
    </tr> 
    <tr>
        <th><?php echo $form->labelEx($model,'name')?>：</th>
        <td>  <?php
            echo $form->dropDownList($model, 'name', EnGoodsRule::getName(), array('class' => 'text-input-bj', 'prompt' => Yii::t('InputGoods', '请选择'))
            );
            ?>
            <?php echo $form->error($model, 'name'); ?></td>
    </tr>
    <tr>
        <th align="right">
          <?php echo $form->labelEx($model, 'type'); ?>：
        </th>
        <td>
            <?php
            echo $form->dropDownList($model, 'type', EnGoodsRule::getType(), array('class' => 'text-input-bj', 'prompt' => Yii::t('InputGoods', '请选择'))
            );
            ?>
            <?php echo $form->error($model, 'type'); ?>
        </td>
    </tr>
     <tr>
        <th><?php echo $form->labelEx($model,'upload_bonus')?>：</th>
        <td><?php echo $form->textField($model, 'upload_bonus', array('class' => 'text-input-bj')); ?>
                <?php echo $form->error($model, 'upload_bonus'); ?></td>
    </tr>
     <tr>
        <th><?php echo $form->labelEx($model,'adopt_bonus')?>：</th>
        <td><?php echo $form->textField($model, 'adopt_bonus', array('class' => 'text-input-bj')); ?>
                <?php echo $form->error($model, 'adopt_bonus'); ?></td>
    </tr>
    <tr>
        <th align="right">
          <?php echo $form->labelEx($model, 'is_input'); ?>：
        </th>
        <td>
            <?php
            echo $form->dropDownList($model, 'is_input', EnGoodsRule::getInput(), array('class' => 'text-input-bj', 'prompt' => Yii::t('InputGoods', '请选择'))
            );
            ?>
            <?php echo $form->error($model, 'is_input'); ?>
        </td>
    </tr>
    
    <tr>    
          <th></th>
          <td>
            <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('inputGoods', '新增') : Yii::t('inputGoods', '编辑'), array('class' => 'reg-sub')); ?>
              <a href="release" ><?php echo CHtml::Button( Yii::t('inputGoods', '返回'), array('class' => 'reg-sub')); ?></a>
          </td>
      </tr>
      <?php $this->endWidget();?>
</table>
