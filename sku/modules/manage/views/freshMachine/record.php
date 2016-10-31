<?php

$this->breadcrumbs = array(
    Yii::t('FreshMachine', '生鲜机机机管理'),
    Yii::t('FreshMachine', '生鲜机列表'),
      Yii::t('FreshMachine', '签到列表'),
);
?>
<?php

/* @var $this SupermarketsController */
/* @var $model  Supermarkets */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#record-grid').yiiGridView('update', {
		data: $(this).serialize()
	})
});
");
?>

<?php

$this->widget('GridView', array(
    'id' => 'record-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
            array(
            'name'=>'签到时间',
            'value'=>'date("Y-m-d H:i:s",$data->create_time)',
            'type'=>'raw'
        ),
        array(
            'name'=>'手机号',
            'value'=>'$data->mobile',
            'type'=>'raw'
        ),
        array(
            'name' => '姓名',
            'value' => '$data->name',
            'type'=>'raw'
        ),
        array(
            'name' => '名称',
            'value' => '$data->fresh_machine->name',
            'type'=>'raw'
        ),
         array(
            'name' => '机器编号',
            'value' => '$data->fresh_machine->code',
            'type'=>'raw'
        ),
          array(
            'name' => '类型',
            'value' => 'StoreCategory::getCategoryName($data->fresh_machine->category_id)',
            'type'=>'raw'
        ),
        array(
            'name' => '所属地区',
            'value' => 'Region::getName($data->fresh_machine->province_id, $data->fresh_machine->city_id,$data->fresh_machine->district_id)',
            'type'=>'raw'
        ),
    ),
));
?>




<?php
//$this->breadcrumbs = array('商品' => array('admin'), '列表');
//Yii::app()->clientScript->registerScript('search', "
//$('.search-form form').submit(function(){
//	$('#record-grid').yiiGridView('update', {
//		data: $(this).serialize()
//	});
//});
//");
?>


<div class="c10"></div>

<?php
//$this->widget('GridView', array(
//    'id' => 'record-grid',
//    'dataProvider' => $model->search(),
//    'itemsCssClass' => 'tab-reg',
//    'columns' => array(
  
//    ),
//));
?>
