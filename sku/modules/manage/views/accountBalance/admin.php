<?php

/* @var $this AccountBalanceController */
/* @var $model AccountBalance */

$this->breadcrumbs = array(
    '余额' => array('admin'),
    '列表',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#accountBalance-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<?php $this->renderPartial('_search', array('model' => $model,)); ?>
<?php
$this->widget('GridView', array(
    'id' => 'accountBalance-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'filter' => $model,
    'columns' => array(
        'sku_number',
        array(
            'filter' => false,
            'name' => 'yesterday_amount',
            'value' => '$data->yesterday_amount'
        ),
        array(
            'filter' => false,
            'name' => 'today_amount',
            'value' => '$data->today_amount'
        ),
        array(
            'filter' => AccountBalance::getType(),
            'name' => 'type',
            'value' => 'AccountBalance::showType($data->type)'
        ),
        array(
            'filter' => false,
            'name' => 'remark',
            'value' => '$data->remark'
        ),
        array(
            'filter' => false,
            'name' => 'create_time',
            'value' => 'date("Y-m-d", $data->create_time)'
        ),
        array(
            'filter' => false,
            'name' => 'last_update_time',
            'value' => 'date("Y-m-d", $data->last_update_time)'
        ),
        array(
            'class' => 'CButtonColumn',
            'header' => Yii::t('home', '操作'),
            'template' => '{checkHash}{resetHash}',
            'htmlOptions' => array(
                'style' => 'width:100px;'
            ),
            'buttons' => array(
                'checkHash' => array(
                    'label' => '校验hash',
                    'url' => 'Yii::app()->createUrl("accountBalance/checkHash",array("id"=>$data->id))',
                   'visible' => 'Yii::app()->user->checkAccess("Manage.AccountBalance.CheckHash")',
                    'options' => array(
                        'class' => 'regm-sub checkHash',
                    ),
                ),
                'resetHash' => array(
                    'label' => '重置hash',
                    'url' => '$data->id',
                    'options' => array('class' => 'regm-sub resetHash', 'onclick' => 'return confirm("' . Yii::t('member', '确定重设吗?') . '")',),
                   'visible' => 'Yii::app()->user->checkAccess("Manage.AccountBalance.ResetHash")',
                ),
            )
        )
    ),
));

?>
<script>
    $(function(){
        //检查hash
        jQuery(document).on('click','#accountBalance-grid a.checkHash',function() {
            var th = this,
                afterDelete = function(){};
            jQuery('#accountBalance-grid').yiiGridView('update', {
                type: 'POST',
                url: jQuery(this).attr('href'),
                dataType:'json',
                success: function(data) {
                    art.dialog(data);
                },
                error: function(XHR) {
                    return afterDelete(th, false, XHR);
                }
            });
            return false;
        });
        //重置hash
        jQuery(document).on('click','#accountBalance-grid a.resetHash',function() {
            var th = this,
                afterDelete = function(){};
            jQuery('#accountBalance-grid').yiiGridView('update', {
                type: 'POST',
                url: "<?php echo Yii::app()->createUrl("accountBalance/resetHash") ?>",
                data:{id:jQuery(this).attr('href'),CSRFTOKEN:"<?php echo Yii::app()->request->csrfToken ?>"},
                dataType:'json',
                success: function(data) {
                    art.dialog(data);
                },
                error: function(XHR) {
                    return afterDelete(th, false, XHR);
                }
            });
            return false;
        });
    });
</script>
