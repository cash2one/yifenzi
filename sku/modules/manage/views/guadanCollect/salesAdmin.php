<?php
/* @var $this Controller */
/* @var $model Guadan */
$this->breadcrumbs = array('售卖管理' => array('guadanAdmin'), '列表');
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#goods-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
//	return false;
});
");
?>
<!--<div class="search-form" >
<?php // $this->renderPartial('_searchSell', array('model' => $model)); ?>
</div>-->
<?php
$this->widget('GridView', array(
    'id' => 'guadan-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
//        array(
//            'class'=>'CCheckBoxColumn',
//            'name'=>'code',
//            'selectableRows'=>2,
////            'disabled'=>'$data->status==$data::STATUS_ENABLE?false:true',
//            'checkBoxHtmlOptions'=>array('class'=>'ids'),
//        ),
        array(
            'name'=>'code',
            'value'=>'isset($data->code)?$data->code:""', 
            'type'=>'raw'
        ),

         array(
            'name'=>'time_start',
            'value'=>'isset($data->time_start)?date("Y-m-d H:i",$data->time_start):""', 
            'type'=>'raw'
        ),
        array(
            'name'=> Yii::t('GuadanCollect','投入积分'),
            'value'=>'(isset($data->amount_bind) && isset($data->amount_unbind)) ? number_format(bcadd($data->amount_bind,$data->amount_unbind,2),2): 0',
            'type'=>'raw'
        ),
        array(
            'name'=>'time_end',
            'value'=>'isset($data->time_end)?date("Y-m-d H:i",$data->time_end):""', 
            'type'=>'raw'
        ),
          array(
            'name'=>  Yii::t('GuadanCollect','售卖进度'),
            'value'=>'(isset($data->amount_bind) && isset($data->amount_unbind) && isset($data->sale_amount_bind) && isset($data->sale_amount_unbind)) ? bcdiv(bcadd($data->sale_amount_bind,$data->sale_amount_unbind),bcadd($data->amount_bind,$data->amount_unbind,2),2)*100 . "%" : "0%";', 
            'type'=>'raw'
        ),
    	array(
            'name'=>'bind_size',
            'value'=> 'isset($data->bind_size)?number_format($data->bind_size,2):""',
            'type'=>'raw'
    	),
    	array(
            'name'=>'new_member_count',
            'value'=> 'isset($data->new_member_count)?$data->new_member_count:""',
            'type'=>'raw'            
        ),	
        array(
            'header' => '操作',
            'class' => 'CButtonColumn',
            'htmlOptions' => array('style' => 'width:120px', 'class' => 'button-column'),
            'template' => '{enablezc}{stop}{views}{finished}{stoped}',
            'updateButtonImageUrl' => false,
            'buttons' => array(
                'stop' => array(
                    'url' => 'Yii::app()->createUrl("guadanCollect/stop",array("id"=>$data->id))',
                    'label' => Yii::t('user', '中止'),
                    'options'=>array('onclick'=>'stop(this);return false;'),
                    'visible' => 'Yii::app()->user->checkAccess(\'Manage.GuadanCollect.Stop\') && $data->status != $data::STAUS_FINISHED &&  $data->status != $data::STATUS_NEW && $data->status != $data::STATUS_DISABLE '
                ),
                'views' => array(
                    'url' => 'Yii::app()->createUrl("guadanCollect/view",array("id"=>$data->id))',
                    'label' => Yii::t('user', '详情'),
                    'visible' => 'Yii::app()->user->checkAccess(\'Manage.GuadanCollect.View\') && $data->status != $data::STAUS_FINISHED && $data->status != $data::STATUS_DISABLE'
                ),
                'finished' => array(
                    'label' => Yii::t('vendingMachine', '已完结'),
                    'visible' => '$data->status == $data::STAUS_FINISHED',
                ),
                'stoped' => array(
                    'label' => Yii::t('vendingMachine', '已中止'),
                    'visible' => '$data->status == $data::STATUS_DISABLE'
                ),
            		'enablezc' => array(
            				'url' => 'Yii::app()->createUrl("guadanCollect/enablezc",array("id"=>$data->id))',
            				'label' => Yii::t('user', '启用'),
            				 'options'=> array(
		                        'onclick'=>'return confirm("确定启用售卖政策？")',
		                    ),
            				'visible' => 'Yii::app()->user->checkAccess(\'Manage.GuadanCollect.Enablezc\') && $data->status == $data::STATUS_NEW '
            		),
            ),
        )
    ),
));
?>
<script src="<?php echo DOMAIN_M?>/js/swf/js/artDialog.iframeTools.js"></script>
<script>
    var button;
    function stop(obj){
        button = obj;
        $url = $(obj).attr('href');
        art.dialog.load($url,{title:'中止挂单',tmpl:'tmpl'},false);
    }

    function enable(obj){
        button = obj;
        $url = $(obj).attr('href');
        art.dialog.load($url,{title:'启用政策',tmpl:'tmpl'},false);
    }
    
    $('body').delegate('.aui_state_focus .stopped','click',function(){
        var id = $(this).attr('data-id');
        if(confirm("确定终止吗？该操作不可恢复")){
            $.get('<?php echo $this->createUrl("guadanCollect/stopSales")?>',{id:id},function(data){
                if(data.result){
                    $('<div class="aui_loading" style="position: absolute;margin: auto auto;left: 0;right: 0;bottom: 0;top: 0;width: 100%;height: 100%;"></div>').prependTo('.aui_outer');
                    $(button).parent('td').html('<a href="javascript:;">已中止</a>');
                    $('.aui_state_focus,.aui_loading').remove();
                } else {
                    $('.aui_state_focus,.aui_loading').remove();
                    art.dialog.alert('中止售卖失败');
                }
            },'json');
        }
    });
    
    /**
     * 调整的弹窗
     * @returns {undefined}
     */
    function adjust(){
        art.dialog.load('<?php echo $this->createUrl('guadanCollect/adjust')?>',{title:'会员月充值限额',tmpl:'tmpl'},false);
    }
    $('body').delegate('#guadanCollect_form','submit',function(){
        var t=$(this);
        if($('#has-checked').prop('checked')){
            var val = $('#WebConfig_value').val();
            if($.trim(val) == ''){
                $('#webConfig_value_em_').text('积分不能为空白').show();
                return false;
            }
            if(!val.match(/^[0-9]+(.[0-9]{1,2})?$/)){
                $('#webConfig_value_em_').text('积分必须是数字').show();
                return false;
            }
        }
        t.submit();
    })
</script>

