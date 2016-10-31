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
		Yii::t('order', '问卷调查管理'),
		Yii::t('order', 'SKU商户加盟审核'),
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
            'name' => 'store_name',
            'value' => '$data->store_name',
            'type' => 'raw',
        ),
       array(
           'name'=>'gai_number',
           'value'=>'$data->gai_number',
       ),
        array(
            'name' => 'mobile',
            'value' => '$data->mobile',
        ),
        array(
            'name' => 'status',
            'value' => '"<span class=\"status\" data-status=\"$data->status\">".PartnerJoinAuditing::getStatus($data->status)."</span>"',
            'type' => 'raw',
        ),
    		array(
    				'name' => 'create_time',
    				'value' => 'date("Y-m-d",$data->create_time)',
    		),
        array(
            'class' => 'CButtonColumn',
            'template' => '{apply}',
            'htmlOptions' => array('style' => 'width:120px', 'class' => 'button-column'),
            'updateButtonImageUrl' => false,
            'updateButtonLabel' => Yii::t('partners', '编辑'),
            'buttons' => array(
                'apply' => array(
                    'url' => 'Yii::app()->createUrl("partnerJoinAuditing/update",array("id"=>$data->id))',
                    'label' => Yii::t('partners', '审核'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.PartnerJoinAuditing.Update')"
                ),
            )
        ),
    ),
));
?>
