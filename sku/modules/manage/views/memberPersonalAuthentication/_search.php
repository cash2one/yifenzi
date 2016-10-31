<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>
<div class="border-info clearfix">
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'member_id'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'member_id',array('size'=>11,'maxlength'=>11,'class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php //echo $form->label($model,'real_name'); ?>：
            </th>
            <td>
                <?php //echo $form->textField($model,'real_name',array('size'=>10,'maxlength'=>10,'class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'identification'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'identification',array('size'=>18,'maxlength'=>18,'class'=>'text-input-bj  middle')); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'bank_card_number'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'bank_card_number',array('size'=>19,'maxlength'=>19,'class'=>'text-input-bj  middle')); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'status'); ?>：
            </th>
            <td id="tdPay">
                <?php echo $form->radioButtonList($model,'status', array(null=>'全部'),
                    array('separator'=>''))?>
                <?php echo $form->radioButtonList($model,'status', MemberPersonalAuthentication::status(),
                    array('separator'=>'')) ?>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="c10">
        <?php echo CHtml::submitButton('搜索',array('class'=>'reg-sub')) ?>
    </div>
    <div class="c10">
    </div>
    <?php $this->endWidget(); ?>