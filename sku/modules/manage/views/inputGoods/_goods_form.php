<?php
/* @var $this InputGoodsController */
/* @var $model ActiveGoods */


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
           <?php echo $model->isNewRecord ? Yii::t('InputGoods', '添加录入项目'):Yii::t('InputGoods', '编辑录入项目'); ?>
        </td>
    </tr> 
    <tr>
        <th><?php echo $form->labelEx($model,'name')?>：</th>
        <td><?php echo $form->textField($model, 'name', array('class' => 'text-input-bj')); ?>
                <?php echo $form->error($model, 'name'); ?></td>
    </tr>
    <tr>
    <table width="70%" style="text-align:center;" align="center" border="1">
        <tr>
            <td colspan="2">录入项目</td>
            <td>项目类型</td>
            <td>上传奖励</td>
            <td>采纳奖励</td>
        </tr>
        <tr class="info">
	<td>
		
		
<!--		<input type="hidden" value="<?php //echo $data->fcrid?>"/>-->
	</td>
        </tr>
        <?php foreach($data as $k=>$v):?>
        <?php if($v->is_input == EnGoodsRule::EN_INPUT):?>
        <tr>
            <td style=" text-align: left"> 
                <input class="cbbox" id="cbx_<?php echo $data->id?>" type="checkbox" value="<?php echo $v->id?>" <?php if(isset($rule_arr)&& in_array($v->id, $rule_arr)){?>checked="checked"<?php  };?>/>
            </td>
            <td> <?php echo $v->name;?></td>
                <td> <?php echo EnGoodsRule::getType($v->type);?></td>
                <td> <?php echo $v->upload_bonus;?></td>
                  <td> <?php echo $v->adopt_bonus;?></td>
        </tr>
        <?php endif;?>
        <?php  endforeach;?>
    </table>
</tr>
<?php echo CHtml::hiddenField('ActiveGoods[r_ids]') ?>
<?php echo CHtml::hiddenField('ActiveGoods[type_name]')?>
    <tr>    
          <th></th>
          <td>
            <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('inputGoods', '新增') : Yii::t('inputGoods', '编辑'), array('class' => 'reg-sub','id'=>'ids')); ?>
              <a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/storeActive') ?>"><?php echo CHtml::Button( Yii::t('inputGoods', '返回'), array('class' => 'reg-sub')); ?></a>
          </td>
      </tr>
      <?php $this->endWidget();?>
</table>
<script>

$("#ids").click(function() {
            var ids = [];
            var name = [];
            $(".cbbox:checked").each(function() {
                ids.push($(this).val());         
                name.push($(this).parent().next('td').html()); 
            });
            
            if (ids.length)
            {             
                $("#ActiveGoods_type_name").val(name);
                $("#ActiveGoods_r_ids").val(ids);
            }
        });
</script>