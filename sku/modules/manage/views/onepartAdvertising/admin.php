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
    Yii::t('order', '广告列表'),

);

?>
<div style="display: inline-block;text-align: left;width: 44%">
        <?php if(Yii::app()->user->checkAccess('Manage.OnepartAdvertising.Adds')) echo CHtml::link('添加广告',Yii::app()->createUrl("/onepartAdvertising/adds"),array('class'=>'regm-sub'));?>&nbsp;
</div>
<?php
$this->widget('GridView', array(
    'id' => 'onepartAdvertising-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        array(
            'name' => 'id',
            'value' => '$data->id',
            'type' => 'raw',
        ),
        array(
            'name'=>'advertising_name',
            'value'=>'$data->advertising_name',
            'type'=>'raw'
        ),
        array(
           'name' => 'types',
            'value' => 'Advertising::getAppAdvertTypeSlide($data->types)',
        ),
        array(
            'name' => 'tourl',
            'value' => '$data->tourl ',
        ),
        array(
            'name' => 'img_h',
            'value' => '$data->img_h ',
        ),
        array(
            'name' => 'img_w',
            'value' => '$data->img_w ',
        ),
        array(
            'name' => 'addtime',
            'value' => 'date("Y-m-d H:i:s", $data->addtime)'
        ),
        array(
            "name"  =>  'is_show',
            'value' =>  '$data->is_show ? "显示" : "不显示"',
        ),
        array(
            'class' => 'CButtonColumn',
            "header"    =>  "操作",
            'template' => '{update}{delete}',
            'htmlOptions' => array('style' => 'width:220px', 'class' => 'button-column'),
            'updateButtonImageUrl' => false,
            'deleteButtonImageUrl' => false,
            'deleteConfirmation' => Yii::t('advertising', '删除广告位将连同删除所有所属广告，请谨慎操作！'),
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('user', '编辑'),
                    'url' => 'Yii::app()->createUrl("onepartAdvertising/updates",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.OnepartAdvertising.Updates')"
                ),
                'delete' => array(
                    'label' => Yii::t('user', '删除'),
                    'url' => 'Yii::app()->createUrl("onepartAdvertising/delete",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.onepartAdvertising.Delete')",
                ),
            )
        )
    ),
));
?>
