<?php
/* @var $this Controller */
/* @var $model Guadan */
$this->breadcrumbs = array('挂单管理' => array('guadanAdmin'), '列表');
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#goods-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
//	return false;
});
");
?>
<div class="search-form" >
<?php $this->renderPartial('_search', array('model' => $model,'upload_model'=>$upload_model)); ?>
</div>
<div class="search-form" >
    <div style="width:1000px;float:left;">
        待绑定积分总额：<strong><?php echo HtmlHelper::formatPrice(Guadan::getAmount(Guadan::TYPE_TO_BIND)) ?></strong>&nbsp;&nbsp;
        非绑定积分总额：<strong><?php echo HtmlHelper::formatPrice(Guadan::getAmount(Guadan::TYPE_NO_BIND)) ?></strong>&nbsp;&nbsp;
        
        <b style="color:red;">（提取时必选同时选择待绑定积分及非绑定积分，且各自金额必须大于0）</b>
        
    </div>
    
<?php if(Yii::app()->user->checkAccess('Manage.Guadan.Collect')):?>
    
    <div style="width:500px;float:right;text-align:right;">
        积分出售：
        <button type="button"  class="regm-sub collectAll">全部提取</button> &nbsp;
        <button type="button"  class="regm-sub collectPart">部分提取</button>
    </div>
    <?php endif;?>

</div>
<?php
$this->widget('GridView', array(
    'id' => 'guadan-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
        array(
            'class'=>'CCheckBoxColumn',
            'name'=>'id',
            'selectableRows'=>2,
            'disabled'=>'$data->status==$data::STATUS_ENABLE ? (float)$data->amount_remain ? false : true  :true',
            'checkBoxHtmlOptions'=>array('class'=>'ids'),
        ),
        'code',
        array(
            'name'=>'gai_number',
            'value'=>'isset($data->gai_number)?$data->gai_number:""', 
            'type'=>'raw'
        ),

         array(
            'name'=>'amount',
            'value'=>'isset($data->amount)?number_format($data->amount,2):""', 
            'type'=>'raw'
        ),
          array(
            'name'=>'amount_remain',
            'value'=>'isset($data->amount_remain)?number_format($data->amount_remain,2):""', 
            'type'=>'raw'
        ),
          array(
            'name'=>'discount',
            'value'=>'isset($data->discount)?$data->discount."%":""', 
            'type'=>'raw'
        ),
    	array(
    			'name'=>'type',
    			'value'=> 'isset($data->type)?Guadan::getType($data->type):""',
    			'type'=>'raw'
    	),
        array(
            'name'=>'status',
            'value'=> 'isset($data->type)?Guadan::getStatus($data->status):""',
            'type'=>'raw'
        ),
        array(
            'name'=>'create_time',
            'type'=>'dateTime'
        ),
        array(
            'header' => '操作',
            'class' => 'CButtonColumn',
            'htmlOptions' => array('style' => 'width:120px', 'class' => 'button-column'),
            'template' => '{frozen}{unfreeze}{disable}',
            'updateButtonImageUrl' => false,
            'buttons' => array(
                'frozen' => array(
                    'url' => 'Yii::app()->createUrl("guadan/frozen",array("id"=>$data->id))',
                    'label' => Yii::t('user', '冻结'),
                    'visible' => 'Yii::app()->user->checkAccess(\'Manage.Guadan.Frozen\') && $data->status==$data::STATUS_ENABLE'
                ),
                'unfreeze' => array(
                    'url' => 'Yii::app()->createUrl("guadan/unfreeze",array("id"=>$data->id))',
                    'label' => Yii::t('user', '解冻'),
                    'visible' => 'Yii::app()->user->checkAccess(\'Manage.Guadan.Unfreeze\') && $data->status==$data::STATUS_FROZEN'                    
                ),
                'disable' => array(
                    'url' => 'Yii::app()->createUrl("guadan/disable",array("id"=>$data->id))',
                    'label' => Yii::t('vendingMachine', '撤销'),
                    'options'=> array(
                        'onclick'=>'return confirm("确定撤销挂单？")',
                    ),
                    'visible' => 'Yii::app()->user->checkAccess(\'Manage.Guadan.disable\') && (float)$data->amount_remain && !(float)bcsub((float)$data->amount,(float)$data->amount_remain,2)'
                ),
            ),
        )
    ),
));
?>
<script>
    $(function(){
        //全部提取
        $('.collectAll').click(function(){
            document.location.href = "<?php echo $this->createAbsoluteUrl('/guadan/collect') ?>";
        });
        //批量提取
        $('.collectPart').click(function(){
            var ids =  $(".ids:checked");
            if(ids.length > 0){
                var data = [];
                ids.each(function(){
                    data.push(this.value)
                });
                document.location.href = "<?php echo $this->createAbsoluteUrl('/guadan/collect') ?>?ids="+data.join(',');
            }else{
                alert('请选择要操作的数据!');
            }
        });
    });
</script>

