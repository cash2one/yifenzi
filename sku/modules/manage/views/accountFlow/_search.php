<?php
/* @var $this AccountFlowController */
/* @var $model AccountFlow */
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
                <td><?php echo $form->radioButtonList($model, 'type', AccountFlow::getType(), array('separator' => ' ')); ?></td>
                <th><?php echo $form->label($model, 'operate_type'); ?></th>
                <td><?php echo $form->dropDownList($model, 'operate_type', AccountFlow::getOperateType(), array('empty' => '', 'class' => 'text-input-bj  least')); ?></td>
                <th><?php echo $form->label($model, 'order_code'); ?></th>
                <td><?php echo $form->textField($model, 'order_code', array('class' => 'text-input-bj  least')); ?></td>
                <th><?php echo $form->label($model, 'month'); ?></th>
                <td><?php
                    echo $form->dropDownList($model, 'month', AccountFlow::getMonth(), array(
                        'class' => 'text-input-bj least',
                        'ajax' => array(
                            'type' => 'POST',
                            'url' => $this->createUrl('/accountFlow/changeMonth'),
                            'data' => array(
                                'month' => 'js:this.value',
                                'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                            ),
                    )));
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
    <?php echo CHtml::submitButton('搜索', array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>
</div>