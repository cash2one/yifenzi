<?php
$this->breadcrumbs = array(
    Yii::t('vendingMachine', '售货机管理'),
    Yii::t('vendingMachine', '售货机列表'),
);
?>
<?php
/* @var $this SupermarketsController */
/* @var $model  Supermarkets */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#vendingMachine-grid').yiiGridView('update', {
		data: $(this).serialize()
	})
});
");
?>
<div class="search-form" >
<?php $this->renderPartial('_search', array('model' => $model)); ?>
</div>

<?php
$this->widget('GridView', array(
    'id' => 'vendingMachine-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        array(
            'name' => '编码',
            'value' => '$data->code',
            'type' => 'raw',
        ),

        array(
            'name' => '盖网号',
            'value' => 'isset($data->partner->id)?$data->partner->gai_number:""',
            'type' => 'raw',
        ),
       array(
            'name' => '售货机名称',
            'value' => '$data->name',
            'type' => 'raw',
        ),
    		array(
    				'name' => 'category_id',
    				'value' => 'StoreCategory::getCategoryName($data->category_id)',
    				'type' => 'raw',
    		),
        array(
            'name' => '状态',
            'value' => 'VendingMachine::getStatus($data->status)',
            'type' => 'raw',
        ),
        array(
            'name'=>'激活状态',
            'value'=>'VendingMachine::getIsActivate($data->is_activate)',
            'type'=>'raw',
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}{apply}',
            'htmlOptions' => array('style' => 'width:120px', 'class' => 'button-column'),
            'viewButtonImageUrl' => false,
            'buttons' => array(
//                'view' => array(
//                    'label' => Yii::t('vendingMachine', '查看'),
//                    'url' => 'Yii::app()->createUrl("vendingMachine/view",array("id"=>$data->id))',
//                    'visible' => "Yii::app()->user->checkAccess('Manage.VendingMachine.View')"
//                ),
                'apply' => array(
                    'url' => 'Yii::app()->createUrl("vendingMachine/apply",array("id"=>$data->id))',
                    'label' => Yii::t('vendingMachine', '审核'),
                    'visible' => 'Yii::app()->user->checkAccess("Manage.VendingMachine.apply")'
                ),
                'update'=>array(
                    'imageUrl' => false,
                      'label' => Yii::t('vendingMachine', '编辑'),
                     'url' => 'Yii::app()->createUrl("vendingMachine/update",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.VendingMachine.update')"
                ),
            )
        ),
    ),
));
?>


