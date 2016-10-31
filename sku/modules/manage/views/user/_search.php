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
                <th><?php echo $form->label($model, 'username'); ?></th>
                <td><?php echo $form->textField($model, 'username', array('class' => 'text-input-bj  least')); ?></td>
                <th><?php echo $form->label($model, 'real_name'); ?></th>
                <td><?php echo $form->textField($model, 'real_name', array('class' => 'text-input-bj  least')); ?></td>
                <th><?php echo $form->label($model, 'mobile'); ?></th>
                <td><?php echo $form->textField($model, 'mobile', array('class' => 'text-input-bj  least')); ?></td>
                <th><?php echo $form->label($model, 'role'); ?></th>
                <td><?php echo $form->dropDownList($model, 'role', $roles, array('empty' => Yii::t('user', '选择角色'), 'class' => 'text-input-bj  least')); ?></td>
            </tr>
        </tbody>
    </table>
    <?php echo CHtml::submitButton(Yii::t('user', '搜索'), array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>
</div>