<?php
/* @var $this FreshQuestResult */
/* @var $model FreshQuestResult */
$this->breadcrumbs = array('商品' => array('admin'), '列表');
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#goods-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
//	return false;
});
");
?>
<div class="search-form" >
<?php $this->renderPartial('_search', array('model' => $model)); ?>

</div>

<?php
$this->widget('GridView', array(
    'id' => 'goods-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg', 
    'columns' => array(
        array(
            'name'=>'name',
            'value'=>'isset($data->name)?$data->name:""',
            'type'=>'raw'
        ),
         array(
            'name'=>'mobile',
            'value'=>'isset($data->mobile)?$data->mobile:""',
            'type'=>'raw'
        ),
        array(
            'name' => 'type',
            'value' => 'FreshQuestResult::getType($data->type)'
        ),
        array(
            'name'=>'create_time',
            'value'=>'date("Y-m-d H:i:s", $data->create_time)',
            'type'=>'raw'
        ),
        array(
            'name'=>'所在城市',
            'value'=>'FreshQuestResult::getCity($data->data)',
            'type'=>'raw'
        ),
        array(
            'header' => '操作',
            'class' => 'CButtonColumn',
            'htmlOptions' => array('style' => 'width:220px', 'class' => 'button-column'),
            'template' => '{viewQuest}',
            'updateButtonLabel' => Yii::t('home', '编辑'),
            'updateButtonImageUrl' => false,

            'buttons' => array(
                'viewQuest' => array(
                    'url' => 'Yii::app()->createUrl("freshQuestResult/viewQuest",array("id"=>$data->id))',
                    'label' => Yii::t('user', '详情'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.FreshQuestResult.ViewQuest')"
                ),

            ),
        )
    ),
));
?>


