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
            <th><?php echo $form->label($model, 'version_name'); ?></th>
            <td><?php echo $form->textField($model, 'version_name', array('class' => 'text-input-bj  least')); ?></td>
            <th><?php echo $form->label($model, 'system_type'); ?></th>
            <td><?php
                $system_type = AppVersion::getSystemType();
                $system_type = array_reverse($system_type,true);
                $system_type[''] = Yii::t('appVersion','全部');
                $system_type = array_reverse($system_type,true);
                echo $form->radioButtonList($model, 'system_type', $system_type, array('separator'=>'')); ?></td>
        </tr>
        </tbody>
    </table>
    <?php echo CHtml::submitButton(Yii::t('appVersion', '搜索'), array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>
</div>