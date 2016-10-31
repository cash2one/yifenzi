<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>
<div class="border-info clearfix">
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'用户姓名'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'name',array('size'=>11,'maxlength'=>11,'class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody>
    </table>


    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'GW号'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'gai_number',array('size'=>18,'maxlength'=>18,'class'=>'text-input-bj  middle')); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'mobile'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'mobile',array('size'=>19,'maxlength'=>19,'class'=>'text-input-bj  middle')); ?>
            </td>
        </tr>
        </tbody>
    </table>

  

    <div class="c10">
        <?php echo CHtml::submitButton('查询',array('class'=>'reg-sub')) ?>
    </div>
    <div class="c10">
    </div>
    <?php $this->endWidget(); ?>