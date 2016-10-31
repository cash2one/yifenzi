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
            <th align="right"><?php echo $form->label($model, 'payment_code'); ?>：</th>
            <td><?php echo $form->textField($model, 'payment_code', array('class' => 'text-input-bj')); ?></td>
          
            <th align="right">启用状态：</th>
            <td><?php echo $form->dropDownList($model, 'enabled', YfzPayment::getEnabledStatus(), array('empty' => '全部',)); ?></td>
            <th align="right">在线状态：</th>
            <td><?php echo $form->dropDownList($model, 'is_online', YfzPayment::getOnlineStatus(), array('empty' => '全部',)); ?></td>

        </tr>
    </table>
    <div class="c10">
    </div>
    <?php echo CHtml::submitButton(Yii::t('SmsLog','搜索'), array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>

</div>