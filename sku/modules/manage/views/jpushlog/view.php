<?php

/* @var $this ApplogController */
/* @var $model Applog */

$this->breadcrumbs = array(
    '程序日志' => array('admin'),
    '详情'
);
?>

<?php

$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'id',
        'order_code',
        'send_data',
        'get_data',
    		
//     		array(
//     				'name' => 'create_time',
//     				'value' => date("Y-m-d H:i:s", $model->create_time)
//     		),
    		
//     		array(
//     				'name' => 'get_data',
//     				'value' => unserialize($model->get_data)
//     		),
		array(
				'name' => 'create_time',
				'value' => date("Y-m-d H:i:s", $model->create_time)
		),
    ),
));
?>
