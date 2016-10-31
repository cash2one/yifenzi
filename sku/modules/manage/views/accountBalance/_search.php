<?php
/* @var $this AccountBalanceController */
/* @var $model AccountBalance */
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
        <tbody>
            <tr>
                <th><?php echo $form->label($model, 'sku_number'); ?></th>
                <td><?php echo $form->textField($model, 'sku_number', array('class' => 'text-input-bj  least')); ?></td>
                <th><?php echo $form->label($model, 'type'); ?></th>
                <td><?php echo $form->radioButtonList($model, 'type', AccountBalance::getType(), array('separator' => ' ')); ?></td>
                <th><?php echo $form->label($model, 'today_amount'); ?></th>
                <td><?php echo $form->dropDownList($model, 'today_amount', AccountBalance::getAmountCompare(), array('class' => 'text-input-bj  least')); ?></td>
                <th><?php echo $form->label($model, 'yesterday_amount'); ?></th>
                <td><?php echo $form->dropDownList($model, 'yesterday_amount', AccountBalance::getAmountCompare(), array('class' => 'text-input-bj  least')); ?></td>
            </tr>
        </tbody>
    </table>
    <?php echo CHtml::submitButton('搜索', array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>
</div>