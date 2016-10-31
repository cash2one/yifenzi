<?php $this->breadcrumbs = array(Yii::t('user', '管理员') => array('admin'), Yii::t('user', '列表')); ?>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
    $('#user-grid').yiiGridView('update', {data: $(this).serialize()});
    return false;
});
");
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
                <th><?php echo $form->label($model, 'username'); ?></th>
                <td><?php echo $form->textField($model, 'username', array('class' => 'text-input-bj  least')); ?></td>
            </tr>
        </tbody>
    </table>
    
    <table cellpadding="0" cellspacing="0" class="searchTable">
        <tbody>
            <tr>
                <th><?php echo $form->label($model, 'info'); ?></th>
                <td><?php echo $form->textField($model, 'info', array('class' => 'text-input-bj')); ?></td>
            </tr>
        </tbody>
    </table>
    <?php echo CHtml::submitButton(Yii::t('user', '搜索'), array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>
</div>


<div class="c10"></div>
<?php
$this->widget('GridView', array(
    'id' => 'user-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'cssFile' => false,
    'columns' => array(
        'username',
        'info',
		array(
            'name' => 'ip',
            'value' => 'Tool::number2ip($data->ip)'
        ),
        array(
            'name' => 'create_time',
            'value' => 'date("Y-m-d G:i:s",$data->create_time)'
        ),
    ),
));
?>