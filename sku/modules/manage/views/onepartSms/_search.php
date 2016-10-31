<?php
/* @var $this SmsLogController */
/* @var $model SmsLog */
/* @var $form CActiveForm */
?>

<div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

    <table cellpadding="0" cellspacing="0" class="searchTable">
        <tr>
            <th align="right"><?php echo $form->label($model, 'mobile'); ?>：</th>
            <td><?php echo $form->textField($model, 'mobile', array('class' => 'text-input-bj')); ?></td>
          
            <th align="right">状态：</th>
            <td><?php echo $form->radioButtonList($model, 'status', array('' => Yii::t('smsLog', '全部'))+SmsLog::getStatus(), array('separator' => '')); ?></td>
                        
         </tr>         
    </table>
    <table cellpadding="0" cellspacing="0" class="searchTable">
    	<tr>    
            <th align="right"><?php echo $form->label($model, 'create_time'); ?>：</th>
            <td>
            <?php
                    $this->widget('comext.timepicker.timepicker', array(
	                    'model'=>$model,
	                    'name'=>'create_time',
	                ));
                    ?> -
                    <?php
                    $this->widget('comext.timepicker.timepicker', array(
	                    'model'=>$model,
	                    'name'=>'create_end_time',
	                ));
             ?>
            </td>
            <th align="right"><?php echo $form->label($model, 'send_time'); ?>：</th>
            <td >
           	<?php
                    $this->widget('comext.timepicker.timepicker', array(
	                    'model'=>$model,
	                    'name'=>'send_time',
	                ));
                    ?> -
                    <?php
                    $this->widget('comext.timepicker.timepicker', array(
	                    'model'=>$model,
	                    'name'=>'send_end_time',
	                ));
                    ?>
            </td>
            
         </tr>
    </table>
    <div class="c10">
    </div>
    <?php echo CHtml::submitButton(Yii::t('SmsLog','搜索'), array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>

</div>