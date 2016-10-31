<?php

/* @var $this AccountFlowController */

$this->breadcrumbs = array(
    '导出余额',
);
?>
<div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'post',
    ));
    ?>
    <table cellpadding="0" cellspacing="0" class="searchTable">
        <tbody>
        <tr>
            <th>日期</th>
            <td>
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'name' => 'date',
                    'select'=>'date',
                ));
                ?>
            </td>
        </tr>
        </tbody>
    </table>
    <?php echo CHtml::submitButton('导出', array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>
</div>
