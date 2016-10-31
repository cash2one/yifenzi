<?php

$this->breadcrumbs = array(
    Yii::t('FreshMachine', '生鲜机机机管理'),
    Yii::t('FreshMachine', '生鲜机列表'),
);
?>
<?php

/* @var $this SupermarketsController */
/* @var $model  Supermarkets */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#FreshMachine-grid').yiiGridView('update', {
		data: $(this).serialize()
	})
});
");
?>
<div class="search-form" >
<?php $this->renderPartial('_search', array('model' => $model)); ?>
</div>
<?php if (Yii::app()->user->checkAccess('Manage.FreshMachine.Create')): ?>
    <a class="regm-sub" href="<?php echo Yii::app()->createAbsoluteUrl('/FreshMachine/create') ?>"><?php echo Yii::t('FreshMachine', '添加生鲜机') ?></a>    
<?php endif; ?>
<?php if (Yii::app()->user->checkAccess('Manage.FreshMachine.Record')): ?>
     <a class="regm-sub" href="<?php echo Yii::app()->createAbsoluteUrl('/FreshMachine/record') ?>"><?php echo Yii::t('FreshMachine', '签到记录') ?></a>
<?php endif; ?>
<?php

$this->widget('GridView', array(
    'id' => 'FreshMachine-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        array(
            'name' => '编码',
            'value' => '$data->code',
            'type' => 'raw',
        ),
        array(
            'name' => 'name',
            'value' => '$data->name',
            'type' => 'raw',
        ),
         array(
            'name' => '所属商家盖网号',
            'value' => 'isset($data->partner->id)?$data->partner->gai_number:""',
            'type' => 'raw',
        ),
         array(
            'name' => '防闪退启用状态',
            'value' => '($data->flash_back_status == FreshMachine::IS_BACK_NO)?"<span style=color:red>".FreshMachine::getIsBack($data->flash_back_status)."</span>":FreshMachine::getIsBack($data->flash_back_status)',
            'type' => 'raw',
//            'htmlOptions' => array('style' => 'color:red'),
//            'visible' => '$data->flash_back_status == FreshMachine::IS_BACK_NO'
        ),
        array(
            'name' => '系统版本',
            'value' => 'isset($data->version)?empty($data->version)?"-":$data->version:"-"',
            'type' => 'raw',
        ),
        array(
            'name' => '状态',
            'value' => 'FreshMachine::getStatus($data->status)',
            'type' => 'raw',
        ),
        array(
            'name' => '激活状态',
            'value' => 'FreshMachine::getIsActivate($data->is_activate)',
            'type' => 'raw',
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}{record}',
            'htmlOptions' => array('style' => 'width:200px', 'class' => 'button-column'),
            'viewButtonImageUrl' => false,
            'buttons' => array(
                'update' => array(
                    'imageUrl' => false,
                    'label' => Yii::t('FreshMachine', '编辑'),
                    'url' => 'Yii::app()->createUrl("FreshMachine/update",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.FreshMachine.update')"
                ),
                'record' => array(
                    'imageUrl' => false,
                    'label' => Yii::t('FreshMachine', '签到列表'),
                    'url' => 'Yii::app()->createUrl("FreshMachine/recordOne",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.FreshMachine.recordOne')",
                    'options' => array(
                        'class' => 'regm-sub-a',
                    )
                ),
            )
        ),
    ),
));
?>


