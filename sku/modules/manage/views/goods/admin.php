<?php
/* @var $this ProductController */
/* @var $model Product */
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
            'name'=>'gai_number',
            'value'=>'isset($data->partners->gai_number)?$data->partners->gai_number:""',
            'type'=>'raw'
        ),
         array(
            'name'=>'barcode',
            'value'=>'isset($data->barcode)?$data->barcode:""', 
            'type'=>'raw'
        ),
         array(
            'name'=>'category',
            'value'=>'isset($data->goodsCategory->name)?$data->goodsCategory->name:""', 
            'type'=>'raw'
        ),
        array(
            'name'=>'thumb',
            'value'=> 'CHtml::image(ATTR_DOMAIN . "/" . $data->thumb, $data->name, array("width" => 100,"height" => 80, "style" => "display: inline-block"))',
            'type'=>'raw'
        ),
         array(
            'name'=>'partner_id',
            'value'=>'isset($data->partners->name)?$data->partners->name:""', 
            'type'=>'raw'
        ),
          array(
            'name'=>'supply_price',
            'value'=>'isset($data->supply_price)?$data->supply_price:""', 
            'type'=>'raw'
        ),
          array(
            'name'=>'price',
            'value'=>'isset($data->price)?$data->price:""', 
            'type'=>'raw'
        ),

    	array(
    			'name'=>'status',
    			'value'=> 'isset($data->status)?Goods::getStatus($data->status):""',
    			'type'=>'raw'
    	),
    		
        array(
            'header' => '操作',
            'class' => 'CButtonColumn',
            'htmlOptions' => array('style' => 'width:120px', 'class' => 'button-column'),
            'template' => '{update}{apply}',
            'updateButtonLabel' => Yii::t('home', '编辑'),
            'updateButtonImageUrl' => false,
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('user', '编辑'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.Goods.Update')"
                ),
                'apply' => array(
                    'url' => 'Yii::app()->createUrl("goods/apply",array("id"=>$data->id))',
                    'label' => Yii::t('vendingMachine', '审核'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.Goods.apply')"
                ),
            ),
        )
    ),
));
?>

