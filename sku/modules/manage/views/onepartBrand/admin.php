<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#order-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
//	return false;
});
");

$this->breadcrumbs = array(
		Yii::t('order', '一份子后台管理'),
		Yii::t('order', '品牌列表'),

);
?>
<div style="display: inline-block;text-align: left;width: 44%">
    <?php if(Yii::app()->user->checkAccess('Manage.OnepartBrand.Adds')) echo CHtml::link('添加品牌',Yii::app()->createUrl("/onepartBrand/adds"),array('class'=>'regm-sub'));?>&nbsp;
</div>
<?php if(Yii::app()->user->hasFlash('del')){

    echo Yii::app()->user->getFlash('del');
}
?>
<?php
$this->widget('GridView', array(
    'id' => 'order-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        array(
            'name' => 'brand_id',
            'value' => '$data->brand_id',
            'type' => 'raw',
        ),
        array(
            'name'=>'brand_name',
            'value'=>'$data->brand_name',
            'type'=>'raw'
        ),
        array(
            'name' => 'brand_logo',
            'value'=> 'CHtml::image(ATTR_DOMAIN . "/" . $data->brand_logo, $data->brand_logo, array("width" => 100,"height" => 80, "style" => "display: inline-block"))',
            'type'=>'raw'
        ),
        array(
            'name' => 'site_url',
            'value' => '$data->site_url',
        ),
        array(
            "name"  =>  'is_show',
            'value' =>  '$data->is_show ? "显示" : "不显示"',
        ),
        array(
            'class' => 'CButtonColumn',
            "header"    =>  "操作",
			'afterDelete'=>'function(link,success,data){alert(data);}',
            'template' => '{update}{delete}',
            'htmlOptions' => array('style' => 'width:220px', 'class' => 'button-column'),
            'updateButtonImageUrl' => false,
            'deleteButtonImageUrl' => false,
            'deleteConfirmation' => Yii::t('advertising', '请确认是否要删除品牌，请谨慎操作！'),
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('user', '编辑'),
                    'url' => 'Yii::app()->createUrl("onepartBrand/updates",array("id"=>$data->brand_id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.OnepartBrand.Updates')"
                ),
                'delete' => array(
                    'label' => Yii::t('user', '删除'),
                    'url' => 'Yii::app()->createUrl("onepartBrand/delete",array("id"=>$data->brand_id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.onepartBrand.Delete')",
                ),
            )
        )
    ),
));
?>
