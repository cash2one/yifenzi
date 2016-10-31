<?php

/* @var $this ApplogController */
/* @var $model Applog */

$this->breadcrumbs = array(
    '程序日志' => array('admin'),
    '列表',
);
?>
<?php

$this->widget('GridView', array(
    'id' => 'applog-grid',
    'dataProvider' => $model->search(),
    'cssFile' => false,
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        'id',
        'order_code',
        'send_data',
    	'get_data',
        array(
            'name' => 'create_time',
            'value' => 'date("Y-m-d H:i:s", $data->create_time)'
        ),
//        'message',
        array(
            'class' => 'CButtonColumn',
            'template' => '{view}',
            'viewButtonImageUrl' => false,
        ),
    ),
));
?>
