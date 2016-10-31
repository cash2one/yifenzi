<div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>
    <table cellpadding="0" cellspacing="0" class="searchTable">
        <tr>
            <th><?php echo $form->label($model, 'item_name'); ?></th>
            <td><?php echo $form->textField($model, 'item_name', array('class' => 'text-input-bj  least')); ?></td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" class="searchTable">
        <tr>
            <th><?php echo $form->label($model, 'item_status'); ?></th>
            <td><?php echo $form->radioButtonList($model, 'item_status', GameStoreItems::status(), array('separator' => '')); ?></td>
        </tr>
    </table>
    <?php echo CHtml::submitButton(Yii::t('advert', '搜索'), array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>
</div>