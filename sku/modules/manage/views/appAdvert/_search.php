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
                <th><?php echo $form->label($model, 'name'); ?></th>
                <td><?php echo $form->textField($model, 'name', array('class' => 'text-input-bj  least')); ?></td>
                <th><?php echo $form->label($model, 'code'); ?></th>
                <td><?php echo $form->textField($model, 'code', array('class' => 'text-input-bj  least')); ?></td>
            </tr>
        </tbody>
    </table>
    <?php echo CHtml::submitButton(Yii::t('appAdvert', '搜索'), array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>
</div>