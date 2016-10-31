<?php

/* @var $this AccountFlowController */
/* @var $model AccountFlow */

$this->breadcrumbs = array(
    '流水' => array('admin'),
    '详情',
);
?>


<?php

$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'id',
        'account_id',
        'sku_number',
        'date',
        array(
            'name' => 'create_time',
            'value' => date("Y-m-d H:i:s", $model->create_time)
        ),
        array(
            'name' => 'type',
            'value' => AccountFlow::showType($model->type)
        ),
        'debit_amount',
        'credit_amount',
        'operate_type',
        array(
            'name' => 'operate_type',
            'value' => AccountFlow::showOperateType($model->operate_type)
        ),
        'trade_spec',
        'trade_terminal_id',
        'ratio',
        'target_id',
        'code',
        'serial_number',
        'area_id',
        'remark',
        'province_id',
        'city_id',
        'district_id',
        'week',
        'week_day',
//         array(
//             'name' => 'ip',
//             'value' => Tool::int2ip($model->ip)
//         )
    ),
));
?>
