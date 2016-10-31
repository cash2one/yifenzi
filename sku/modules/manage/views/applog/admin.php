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
        'level',
        'category',
        array(
            'name' => 'logtime',
            'value' => 'date("Y-m-d H:i:s", $data->logtime)'
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
