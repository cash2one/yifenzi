<?php
/* @var $this OrderController */
/* @var $model Order */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#order-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
//	return false;
});
");

$this->breadcrumbs = array(
		Yii::t('order', '商户管理'),
		Yii::t('order', '商户列表'),
);
?>
<div class="search-form" >
    <?php $this->renderPartial('_search', array('model' => $model)); ?>
</div>

<?php
$this->widget('GridView', array(
    'id' => 'order-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        array(
            'name' => 'name',
            'value' => '$data->name',
            'type' => 'raw',
        ),
        array(
            'name'=>'head',
            'value'=>'CHtml::image(ATTR_DOMAIN . "/" . $data->head, $data->name, array("width" => 100,"height" => 80, "style" => "display: inline-block"))',
            'type'=>'raw'
        ),
       array(
           'name'=>'gai_number',
           'value'=>'$data->gai_number',
       ),
         array(
           'name'=>'运营方盖网号',
           'value'=>'Partners::OperatorRelation($data->id)',
       ),
        array(
            'name' => 'mobile',
            'value' => '$data->mobile',
        ),
        array(
            'name' => 'status',
            'value' => '"<span class=\"status\" data-status=\"$data->status\">".Partners::getStatus($data->status)."</span>"',
            'type' => 'raw',
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}{apply}',
            'htmlOptions' => array('style' => 'width:120px', 'class' => 'button-column'),
            'updateButtonImageUrl' => false,
            'updateButtonLabel' => Yii::t('partners', '编辑'),
            'buttons' => array(
                'update' => array(
                    'url' => 'Yii::app()->createUrl("partners/update",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.Partners.update')"
                ),
                'apply' => array(
                    'url' => 'Yii::app()->createUrl("partners/apply",array("id"=>$data->id))',
                    'label' => Yii::t('partners', '审核'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.Partners.apply')"
                ),
            )
        ),
    ),
));
?>
