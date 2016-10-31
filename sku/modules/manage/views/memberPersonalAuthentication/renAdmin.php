<?php
$this->breadcrumbs = array(
    Yii::t('site', '配送员管理'),
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
<div class="border-info clearfix">
    <?php
    $this->renderPartial('ren_search', array(
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
            'name'=>'name',
            'value'=>'$data->name',
            'type'=>'raw'
        ),
        array(
            'name'=>'身份证',
            'value'=>'$data->member_personal->identification',
            'type'=>'raw'
        ),
        array(
            'name' => '盖网号',
            'value' => 'Member::getMemberById($data->member_id,array("gai_number"))->gai_number',
            'type'=>'raw'
        ),
        array(
            'name' => '注册地',
            'value' => 'Region::getName($data->member_personal->member->province_id, $data->member_personal->member->city_id)',
            'type'=>'raw'
        ),
         array(
            'name' => '配送员注册时间',
            'value' => 'date("Y-m-d H:i:s","$data->create_time")',
            'type'=>'raw'
        ),
          array(
            'name' => '手机号',
            'value' => '$data->mobile',
            'type'=>'raw'
        ),
        array(
            'name' => '押金缴纳',
            'value' => 'Distribution::getDeposit($data->deposit_status)',
            'type'=>'raw'
        ),
        array(
            'name' => '缴纳金额',
            'value' => '0',
            'type'=>'raw'
        ),
        array(
            'name' => '服务次数',
            'value' => '$data->service_count',
            'type'=>'raw'
        ),
        array(
            'name' => '配送费收入',
            'value' => '',
            'type'=>'raw'
        ),
        array(
            'class' => 'CButtonColumn',
             'header' => Yii::t('partners', '操作'),
             'htmlOptions' => array('style' => 'width:200px', 'class' => 'button-column'),
            'template' => '{check}{status1}{status2}',
            'buttons' => array(
                'check' => array(
                    'url' => 'Yii::app()->createUrl("memberPersonalAuthentication/check",array("id"=>$data->id))',
                    'label' => Yii::t('partners', '收入查询'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.MemberPersonalAuthentication.Check')",
                    'options' => array(
                        'class' => 'regm-sub-a',
                    )
                ),
                'status1' => array(
                    'url' => 'Yii::app()->createUrl("memberPersonalAuthentication/resetStatus",array("id"=>$data->id))',
                    'label' => '启用',
                    'visible' => '$data->status == Distribution::STATUS_OPEN'
                ),
                'status2' => array(
                    'url' => 'Yii::app()->createUrl("memberPersonalAuthentication/resetStatus",array("id"=>$data->id))',
                    'label' => '禁用',
                    'visible' => '$data->status == Distribution::STATUS_CLOSE'
                ),
            )
        ),
    ),
));
?>
