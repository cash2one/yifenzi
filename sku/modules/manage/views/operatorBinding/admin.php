<?php
$this->breadcrumbs = array(
    Yii::t('site', '运营方绑定'),
);
?>

<?php
/* @var $this OrderController */
/* @var $model Orders */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#order-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
});
");
?>

<div class="search-form" >
    <?php
    $this->renderPartial('_search', array(
        'model' => $model,
    ));
    ?>
</div>
<div class="c10"></div>

<?php

$this->widget('GridView', array(
    'id' => 'personal-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        array(
            'name' => 'p_gai_number',
            'value' => '$data->p_gai_number',
            'type' => 'raw'
        ),

        array(
            'name'=>'m_gai_number',
            'value'=>'$data->m_gai_number',
            'type'=>'raw'
        ),
        array(
            'name'=>'status_name',
            'value'=>'OperatorRelation::getStaus($data->status)',
            'type'=>'raw'
        ),
        array(
             'name' => 'create_times',
             'value' => 'date("Y-m-d H:i:s",$data->create_times)',
             'type'=>'raw'
         ),
    		
        array(
            'class' => 'CButtonColumn',
            'template' => '{apply}',
            'htmlOptions' => array('style' => 'width:60px', 'class' => 'button-column'),
            'updateButtonImageUrl' => false,
            'buttons' => array(
                'apply' => array(
                    'url' => 'Yii::app()->createUrl("operatorBinding/detail",array("id"=>$data->id))',
                    'label' => Yii::t('partners', '详情'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.OperatorBinding.Apply')"
                ),
            ),
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{apply}',
            'htmlOptions' => array('style' => 'width:60px', 'class' => 'button-column'),
            'updateButtonImageUrl' => false,
            'buttons' => array(
                'apply' => array(
                    'url' => 'Yii::app()->createUrl("operatorBinding/update",array("id"=>$data->id))',
                    'label' => Yii::t('partners', '修改'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.OperatorBinding.Apply')"
                ),
            ),
        ),

    ),
));
?>
