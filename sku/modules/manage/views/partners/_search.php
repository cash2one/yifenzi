<?php
/* @var $this OrderController */
/* @var $model Orders */
/* @var $form CActiveForm */
?>
<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>
<div class="border-info clearfix">
      <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <?php echo Yii::t('partners','名称'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'name',array('size'=>11,'maxlength'=>11,'class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody>
      </table>
     <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
                <th>
                    <?php echo $form->label($model, 'gai_number'); ?>：
                </th>
                <td>
                    <?php echo $form->textField($model, 'gai_number', array('size' => 11, 'maxlength' => 11, 'class' => 'text-input-bj  least')); ?> 
                </td>    
            </tr>
        </tbody>
    </table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <?php echo $form->label($model,'status'); ?>：
            </th>
            <td id="tdPay">
                <?php echo $form->radioButtonList($model,'status',  Partners::getStatus(),
                    array('empty'=>Yii::t('order','全部'),'separator'=>'')) ?>
            </td>
        </tr>
        </tbody></table>
    
    
    <div class="c10">
    <?php echo CHtml::submitButton('搜索',array('class'=>'reg-sub')) ?>
</div>
<div class="c10">
</div>
<?php $this->endWidget(); ?>
