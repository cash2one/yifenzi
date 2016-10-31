<?php
/* @var $this InputGoodsController */
/* @var $model InputGoods */
$this->breadcrumbs = array(Yii::t('category', '录入商品') , Yii::t('inputGoods', '列表'));
?>

<table border="1" cellspacing="1" cellpadding="0" style="text-align: center;">
    <tr>
        <td  width="150px" height="40px"><a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/admin') ?>"><?php echo Yii::t('inputGoods', '产品库商品') ?></a></td>
<!--        <td  width="150px" height="40px"><a href="<?php echo Yii::app()->createAbsoluteUrl('/inputGoods/unGoods') ?>"><?php echo Yii::t('inputGoods', '非产品库商品') ?></a></td>
        <td  width="150px" height="40px">店铺录入活动商品</td>-->
        <td  width="150px" height="40px">本日更新商品：<?php echo count($query)?></td>
        <td width="150px" height="40px">总待审核商品：<?PHP echo ApplyBarcodeGoods::model()->COUNT('status=:status',array(':status'=>  ApplyBarcodeGoods::STATUS_APPLY))?></td>
        <td  width="150px" height="40px">总已审核商品：<?PHP echo ApplyBarcodeGoods::model()->COUNT('status=:status',array(':status'=>  ApplyBarcodeGoods::STATUS_PASS))?></td>
        <td  width="150px" height="40px">已导入未审核商品：<?PHP echo BarcodeGoods::model()->COUNT('status=:status',array(':status'=>""))?></td>
    </tr>
</table>

<?php
/* @var $this SupermarketsController */
/* @var $model  Supermarkets */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#BarcodeGoodsGrid-grid').yiiGridView('update', {
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
    'id' => 'BarcodeGoodsGrid-grid',
    'dataProvider' => $model->searchInputGoods(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
          array(
            'name' => 'name',
            'value' => '$data->name',
            'type' => 'raw',
        ),
        array(
            'name' => 'barcode',
            'value' => '$data->barcode',
            'type' => 'raw',
        ),
                array(
            'name'=>'model',
            'value'=>'$data->model',
            'type'=>'raw',
        ),
              array(
            'name'=>'分类',
            'value'=>'$data->cate_name',
            'type'=>'raw',
        ),
        array(
            'name'=>'状态',
            'value'=>'BarcodeGoods::getStatus($data->status)',
            'type'=>'raw',
        ),
        array(
        'name'=>'申请数量',
         'value'=>'$data->apply_num',
        ),
        array(
            'header' => '操作',
            'class' => 'CButtonColumn',
            'template' => '{open}{apply}',
            'htmlOptions' => array('style' => 'width:220px'),
            'viewButtonImageUrl' => false,
            'buttons' => array(
                'open'=>array(
                    'imageUrl' => false,
                    'label' => Yii::t('inputGoods', '重新开放 '),
                    'url' => 'Yii::app()->createUrl("inputGoods/openGoods",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.InputGoods.openGoods')",
                    'options'=>array('onClick'=>'return confirm("是否确定当前商品重新开放录入？最多3个用户可提交录入结果。")'),
                ),
                'apply' => array(
                    'imageUrl' => false,
                    'label' => Yii::t('inputGoods', '审核'),
                    'url' => 'Yii::app()->createUrl("inputGoods/apply",array("id"=>$data->id))',
                    'visible' => 'Yii::app()->user->checkAccess("Manage.InputGoods.apply")&&$data->status!=BarcodeGoods::STATUS_PASS'
                ),
            )
        ),

    ),
));
?>
