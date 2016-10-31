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
        'level',
        'category',
        'logtime',
        'message',
    ),
));
?>
