<?php

/* @var $this AccountFlowController */
/* @var $model order */


$this->widget('ext.PHPExcel.EExcelView', array(
    'id' => 'accountFlow-grid',
    'dataProvider' => $model->backendSearch(),
//    'filter' => $model,
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
//        'gai_number',
        array(
            'filter' => false,
            'name' => '订单编号',
            'value' => '" ".$data->code'
        ),

        array(
//            'filter' => false,
            'name' => '商品名字',
            'value' =>'$data->name'
        ),
        array(

            'name' => '商品数量',
            'value' => '$data->num',
        ),
        array(
            'name'=>'商品单价',
            'value'=>'$data->price'
        ),
        array(
            'filter' => false,
            'name' => 'total_price',
            'value' => '$data->total_price'
        ),
        array(
            'filter' => false,
            'name' => 'status',
            'value' => 'Order::status($data->status)'
        ),
//        'node',
        array(
            'filter' => false,
            'name' => 'pay_status',
            'value' => 'Order::payStatus($data->pay_status)',
        ),

        array(
            'name' => 'type',
            'value' => 'Order::type($data->type)'
        ),
        array(
            'filter' => false,
            'name' => '下单时间',
            'value' => 'date("Y-m-d H:i:s", $data->create_time)'
        ),


    ),
));

?>
