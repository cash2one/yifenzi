<?php

/* @var $this AccountFlowController */
/* @var $model order */


$this->widget('comext.PHPExcel.EExcelView', array(
    'id' => 'accountFlow-grid',
    'dataProvider' => $model->search(),
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
            'filter' => false,
            'name' => '商品名字',
            'value' =>'isset($data->ordersGoods[0]->name)?$data->ordersGoods[0]->name:""',
        ),
        array(

            'name' => '商品数量',
            'value' => 'isset($data->ordersGoods[0]->num)?$data->ordersGoods[0]->num:""',
        ),
        array(
            'name'=>'商品单价',
            'value'=>'isset($data->ordersGoods[0]->price)?$data->ordersGoods[0]->price:""'
        ),
        array(
            'filter' => false,
            'name' => 'total_price',
            'value' => '$data->total_price'
        ),
        array(
            'name'=>'gai_number',
            'value'=>'isset($data->partner->id)?$data->partner->gai_number:""',
        ),
        array(
            'name'=>'门店、售货机名称',
            'value'=>'(isset($data->store)?$data->store->name:"").(isset($data->freshMachine)?$data->freshMachine->name:"").(isset($data->machine)?$data->machine->name:"")',
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
            'filter' => false,
            'name' => '下单时间',
            'value' => 'date("Y-m-d H:i:s", $data->create_time)'
        ),


    ),
));

?>
