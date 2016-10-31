<?php
$this->breadcrumbs = array(
    Yii::t('supermarkets', '门店管理'),
    Yii::t('supermarkets', '门店列表'),
);
?>
<?php
/* @var $this SupermarketsController */
/* @var $model  Supermarkets */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#supermarkets-grid').yiiGridView('update', {
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
    'id' => 'supermarkets-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        array(
            'name' => 'name',
            'value' => '$data->name',
            'type' => 'raw',
        ),
        array(
            'name' => '盖网号',
            'value' => 'isset($data->partner->id)?$data->partner->gai_number:""',
            'type' => 'raw',
        ),
        array(
            'name' => 'mobile',
            'value' => '$data->mobile',
        ),
    		array(
    				'name' => 'category_id',
    				'value' => 'StoreCategory::getCategoryName($data->category_id)',
    				'type' => 'raw',
    		),
          array(
            'name' => '推荐人',
            'value' => 'isset($data->referrals->id)?$data->referrals->gai_number:""',
            'type' => 'raw',
        ),
        array(
            'name' => 'status',
            'value' => '"<span class=\"status\" data-status=\"$data->status\">".Supermarkets::getStatus($data->status)."</span>"',
            'type' => 'raw',
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}{apply}',
            'htmlOptions' => array('style' => 'width:120px', 'class' => 'button-column'),
            'updateButtonImageUrl' => false,
            'updateButtonLabel' => Yii::t('supermarkets', '编辑'),
            'buttons' => array(
                'update' => array(
                    'url' => 'Yii::app()->createUrl("supermarkets/update",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.Supermarkets.update')"
                ),   
                'apply' => array(
                    'url' => 'Yii::app()->createUrl("supermarkets/apply",array("id"=>$data->id))',
                    'label' => Yii::t('supermarkets', '审核'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.Supermarkets.apply')"
                ),
             
            )
        ),
    ),
));
?>
