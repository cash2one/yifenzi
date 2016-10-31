<?php
$this->breadcrumbs = array(
    Yii::t('order', '订单管理'),
    Yii::t('order', '订单列表'),
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
//	return false;
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
    'id' => 'order-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
    		'id',
        array(
            'name' => 'code',
            'value' =>'$data->code',
            'type' => 'raw',
        ),
    		array(
    				'name'=>'父订单id',
    				'value'=>'isset($data->father_id)&&$data->father_id>0?$data->father_id:"无"',
    		),
    		
    		array(
    				'name'=>'会员盖网号',
    				'value'=>'isset($data->member->id)?$data->member->gai_number:""',
    		),
    		
         array(
           'name'=>'商家盖网号',
           'value'=>'isset($data->partner->id)?$data->partner->gai_number:""',
       ),
       array(
    				'name'=>'网点',
    				'value'=>'$data->type==Order::TYPE_SUPERMARK?(isset($data->store->name) ? $data->store->name : ""):($data->type==Order::TYPE_MACHINE?(isset($data->machine->name) ? $data->machine->name : ""):($data->type==Order::TYPE_FRESH_MACHINE?(isset($data->freshMachine->name) ? $data->freshMachine->name : ""):"未知"))',
    		),
    		
        array(
            'name' => 'type',
            'value' => 'Order::type($data->type)',
            'type' => 'raw',
        ),
        array(
            'name' => 'status',
            'value' => 'Order::status($data->status)',
        ), 
    		array(
    				'name' => 'pay_status',
    				'value' => 'Order::payStatus($data->pay_status)',
    		),
            array(
                'name' => '支付方式',
                'value' => '$data->pay_status == Order::PAY_STATUS_YES ? Order::getPayType($data->pay_type) : ""',
            ),
    		array(
    				'name' => 'create_time',
    				'value' => 'date("Y-m-d G:i:s",$data->create_time)',
    		),
        array(
            'class' => 'CButtonColumn',
            'template' => '{view}{close}{frozen}{complete}',
            'htmlOptions' => array('style'=>'width:350px','class'=>'button-column'),
            'viewButtonImageUrl' => false,
            'buttons' => array(
                'view' => array(
                    'label' => Yii::t('user', '查看'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.Order.View')"
                ),
                'close' => array(
                    'label' => Yii::t('order', '关闭'),
                    'url' =>'Yii::app()->createAbsoluteUrl("order/closeOrder",array("id"=>$data->id))',
                    'visible' => '($data->status != Order::STATUS_CANCEL&&$data->status !=Order::STATUS_COMPLETE)'."&&Yii::app()->user->checkAccess('Manage.Order.CloseOrder')".'&&$data->father_id==0',
                ),
				'frozen' => array(
						'label' => Yii::t('order', '冻结'),
						'url' =>'Yii::app()->createAbsoluteUrl("order/frozen",array("id"=>$data->id))',
						'visible' => '($data->pay_status == Order::PAY_STATUS_YES && $data->status != Order::STATUS_FROZEN && $data->status != Order::STATUS_CANCEL && $data->status != Order::STATUS_COMPLETE)'."&&Yii::app()->user->checkAccess('Manage.Order.Frozen')".'&&$data->father_id==0&&$data->type==Order::TYPE_SUPERMARK',
				),
				'complete' => array(
						'label' => Yii::t('order', '完成'),
						'url' =>'Yii::app()->createAbsoluteUrl("order/complete",array("id"=>$data->id))',
						'visible' => '($data->pay_status == Order::PAY_STATUS_YES && $data->status != Order::STATUS_CANCEL && $data->status != Order::STATUS_COMPLETE)'."&&Yii::app()->user->checkAccess('Manage.Order.Complete')".'&&$data->father_id==0',
				),

            )
        ),
    ),
));
?>

<?php

if($this->showExport==true){
	$this->renderPartial('/layouts/_export', array(
		'model' => $model, 'exportPage' => $exportPage, 'totalCount' => $totalCount,
));
}

?>


