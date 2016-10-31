<?php
/* @var $this SmsLogController */
/* @var $model SmsLog */

$this->breadcrumbs = array(
    Yii::t('smsLog', '短信通道') => array('yfzSendSmsRecord'), 
    '短信发送记录',   
);

?>

<?php
$this->renderPartial('_search', array(
    'model' => $model,
));

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#SmsLog-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="c10"></div>
<?php
$this->widget('GridView', array(
    'id' => 'SmsLog-grid',
    'dataProvider' => $model->search(),
    'cssFile' => false,
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
		'mobile',
    	array(
			'name'=> 'content',
    		'value'=>'SmsLog::showContent($data->type,$data->content)',
		),
		array(
			'name'=>'create_time',
			'value'=>'date("Y-m-d H:i:s",$data->create_time)',
		),
		array(
			'name'=>'status',
			'header'=>'状态',
			'value'=>'$data->status?(SmsLog::showStatus($data->status)):""',		
		),	
		'count',		
		array(
			'name'=>'target_id',			
		),
		array(
			'name'=>'type',
			'header'=>'类型',
			'value'=>'$data->type?(SmsLog::showType($data->type)):""',
		),
		array(
			'name'=>'send_time',
			'value'=>'date("Y-m-d H:i:s",$data->send_time)',
		),
		array(
			'name'=>'interface',
			'header'=>'接口',
			'value'=>'$data->interface?(SmsLog::showInterface($data->interface)):""',
		),	
    ),
));
?>
