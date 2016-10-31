<?php
$this->breadcrumbs = array(
    Yii::t('storeCategory', '店铺分类管理') => array('admin'),
    Yii::t('storeCategory', '店铺分类列表'),
);
?>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
    $('#storeCategory-grid').yiiGridView('update', {data: $(this).serialize()});
    return false;
});
");
?>
<?php $this->renderPartial('_search', array('model' => $model,)); ?>
<?php if (Yii::app()->user->checkAccess('Manage.StoreCategory.Create')): ?>
    <a class="regm-sub" href="<?php echo Yii::app()->createAbsoluteUrl('/storeCategory/create') ?>">添加新分类</a>
<?php endif; ?>
<div class="c10"></div>
<?php
$this->widget('GridView', array(
    'id' => 'storeCategory-grid',
    'dataProvider' => $model->search(),
    'cssFile' => false,
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        'name',
        array(
            'name' => 'style',
            'value' => '$data->style ? CHtml::image(ATTR_DOMAIN."/".$data->style, $data->name, array("width" => "22px", "height" => "22px")) : ""',
            'type' => 'raw'
        ),
    		'sort',
        array(
            'class' => 'CButtonColumn',
            'header' => Yii::t('home', '操作'),
            'template' => '{update}{deleteC}',
            'updateButtonImageUrl' => false,
            'deleteButtonImageUrl' => false,
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('user', '编辑'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.StoreCategory.Update')"
                ),
                'deleteC' => array(
                    'label' => Yii::t('user', '删除'),
					'url' => 'Yii::app()->createUrl("storeCategory/delete",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.StoreCategory.Delete')",
                ),
            )
        )
    ),
));
?>

<script type="text/javascript">
$("a[title='删除']").live('click',function() {
	if(!confirm('确定要删除这条数据吗?')) return false;
	return true;
});
</script>
