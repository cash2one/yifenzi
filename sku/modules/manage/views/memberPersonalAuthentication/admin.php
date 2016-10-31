<?php
$this->breadcrumbs = array(
    Yii::t('site', '个人认证'),
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
            'name' => 'member_id',
            'value' => '$data->member_id',
            'type' => 'raw'
        ),
        array(
            'name'=>'real_name',
            'value'=>'$data->real_name',
            'type'=>'raw'
        ),
        array(
            'name'=>'identification',
            'value'=>'$data->identification',
            'type'=>'raw'
        ),
        array(
            'name' => 'bank_card_number',
            'value' => '$data->bank_card_number',
            'type'=>'raw'
        ),
        array(
            'name' => 'status',
            'value' => '"<span class=\"status\" data-status=\"$data->status\">".MemberPersonalAuthentication::status($data->status)."</span>"',
            'type' => 'raw'
        ),
    		
    		array(
    				'name' => 'auto_status',
    				'value' => '"<span class=\"status\" data-status=\"$data->status\">".MemberPersonalAuthentication::autoStatus($data->auto_status)."</span>"',
    				'type' => 'raw'
    		),
    		
        array(
            'class' => 'CButtonColumn',
            'template' => '{apply}',
            'htmlOptions' => array('style' => 'width:120px', 'class' => 'button-column'),
            'updateButtonImageUrl' => false,
            'buttons' => array(
                'apply' => array(
                    'url' => 'Yii::app()->createUrl("memberPersonalAuthentication/apply",array("id"=>$data->id))',
                    'label' => Yii::t('partners', '审核'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.MemberPersonalAuthentication.Apply')"
                ),
            )
        ),
    ),
));
?>
