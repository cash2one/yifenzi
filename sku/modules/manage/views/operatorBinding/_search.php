<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>
<div class="border-info clearfix">
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'p_gai_number'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'p_gai_number',array('size'=>11,'maxlength'=>11,'class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'m_gai_number'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'m_gai_number',array('size'=>10,'maxlength'=>10,'class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <b><?php echo $form->label($model,'status_name'); ?>：
            </th>           
               <td>              
                    <?php
                echo $form->dropDownList($model, 'status', OperatorRelation::getStatusName(),array('class' => 'text-input-bj', 'empty' => Yii::t('goods', '全部')));
                 ?>
            </td>
        </tr>
        </tbody>
    </table>
    <?php echo CHtml::submitButton('搜索',array('class'=>'reg-sub')) ?>
    <input id="Btn_Add" type="button" value="<?php echo Yii::t('OperatorBinding', '手动绑定'); ?>" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl("operatorBinding/createBind"); ?>'" />

    <div class="c10">
    </div>
    <?php $this->endWidget(); ?>