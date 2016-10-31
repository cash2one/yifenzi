<?php
$this->breadcrumbs = array(
    Yii::t('barcodeGoods', '商品管理'),
    Yii::t('barcodeGoods', '条码库管理'),
);
?>

<div class="search-form" >
<?php $this->renderPartial('_search', array('model' => $model)); ?>
</div>
<?php
/* @var $this SupermarketsController */
/* @var $model  Supermarkets */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#barcodeGoodsGrid-grid').yiiGridView('update', {
		data: $(this).serialize()
	})
});
");
?>

<?php if ($this->getUser()->checkAccess('Manage.BarcodeGoods.Create')): ?>
    <a class="regm-sub" href="<?php echo $this->createAbsoluteUrl('/barcodeGoods/create') ?>"><?php echo Yii::t('category', '添加条形码') ?></a>
<?php endif; ?>

<?php
$this->widget('GridView', array(
    'id' => 'barcodeGoodsGrid-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        array(
            'name' => 'barcode',
            'value' => '$data->barcode',
            'type' => 'raw',
        ),
        array(
            'name' => 'name',
            'value' => '$data->name',
            'type' => 'raw',
        ),
    		'cate_name',
        array(
            'name' => 'default_price',
            'value' => '$data->default_price',
            'type' => 'raw',
        ),
        array(
            'name'=>'thumb',
            'value'=>'CHtml::image(ATTR_DOMAIN . "/" . $data->thumb, $data->name, array("width" => 100,"height" => 80, "style" => "display: inline-block"))',
            'type'=>'raw',
        ),
        array(
            'name'=>'model',
            'value'=>'$data->model',
            'type'=>'raw',
        ),
        array(
            'name'=>'unit',
            'value'=>'$data->unit',
            'type'=>'raw',
        ),
    	'brand',
//        array(
//            'name'=>'store',
//            'value'=>'$data->store',
//            'type'=>'raw',
//        ),
//        array(
//            'name'=>'outlets',
//            'value'=>'$data->outlets',
//            'type'=>'raw',
//        ),
        array(
            'name'=>'create_time',
            'value'=>'date("Y-m-d G:i:s",$data->create_time)',
            'type'=>'raw',
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}{delete}',
            'htmlOptions' => array('style' => 'width:120px', 'class' => 'button-column'),
            'viewButtonImageUrl' => false,
            'buttons' => array(
                'update'=>array(
                    'imageUrl' => false,
                    'label' => Yii::t('barcodeGoods', '编辑'),
                    'url' => 'Yii::app()->createUrl("barcodeGoods/update",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.BarcodeGoods.update')"
                ),
                'delete' => array(
                    'imageUrl' => false,
                    'label' => Yii::t('barcodeGoods', '删除'),
                    'url' => 'Yii::app()->createUrl("barcodeGoods/delete",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.BarcodeGoods.delete')"
                ),
            )
        ),

    ),
));
?>
