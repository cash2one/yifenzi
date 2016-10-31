<?php
/* @var $this ProductController */
/* @var $model Product */
$this->breadcrumbs = array('商品列表' => array('admin'));
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
<?php $this->renderPartial('_search', array('model' => $model)); ?>
</div>
<div class="search-form" style="margin-bottom: 8px">
   
    <div style="display: inline-block;text-align: left;width: 44%">
        <?php if(Yii::app()->user->checkAccess('Manage.OnepartGoods.Insert')) echo CHtml::link('添加商品',Yii::app()->createUrl("/onepartGoods/insert"),array('class'=>'regm-sub'));?>&nbsp;
    </div>
<!--    <div style="display: inline-block;text-align: right;width: 49%">
        <?php //echo CHtml::tag('button',array('class'=>'regm-sub limitRecommanded'),'设置推荐数');?>&nbsp;
    </div>-->
</div>
<?php
if(!Yii::app()->user->checkAccess('Manage.OnepartGoods.Sort')){
    $display = "style='display:none'";
}else{
    $display = '';
}
$this->widget('GridView', array(
    'id' => 'yifenGoods-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg', 
    'cssFile' => false,
    'columns' => array(
        array(
            'selectableRows' => 2,
            'class' => 'CCheckBoxColumn',
            'checkBoxHtmlOptions' => array('name' => 'ids[]'),
        ),
        array(
            'footer' => '<button type="button" '."$display".' onclick="sortProduct();" class="regm-sub">排序</button>',
            'class'=>'InputColumn',
            'name'=>'sort_order',
            'value'=>'isset($data->sort_order)?$data->sort_order:""',
            'type'=> 'text',
            'htmlOptions'=>array('style'=>'width:30px')
        ),
        array(
            'name'=> 'ID',
            'value'=>'isset($data->goods_id)?$data->goods_id:""',
            'type'=>'raw',
        ),
        array(
            'name'=>'goods_name',
            'value'=>'(isset($data->goods_name) && $data->is_on_sale)?CHtml::link($data->goods_name,DOMAIN_YIFENZI."/goods/view/".$data->goods_id.".html",array("target"=>"_blank")):$data->goods_name', 
            'type'=>'raw',
        ),
        array(
            'name' => 'column_id',
            'value' => 'isset($data->column_id)?Column::getColumnbyId($data->column_id):"无栏目名称"',
            'type'=>'raw',
        ),
        array(
            'name'=> '添加时间',
			'value' => 'date("Y-m-d H:i:s", $data->add_time)'
        ),
        array(
            'name' => '已参与/总需',
            'value' => 'YfzGoods::getCurrentSales($data->goods_id,$data->current_nper). "/".ceil($data->shop_price/$data->single_price)',
            'type' => 'raw'
        ),
        array(
            'name'=>'期数/最大期数',
            'value'=>'$data->current_nper."/".$data->max_nper',
            'type'=>'raw'
        ),
        array(
            'name'=> '单价/元',
            'value'=>'isset($data->single_price) ? $data->single_price : 0'
        ),
        array(
            'name' => '人气商品',
            'value'=> '!Yii::app()->user->checkAccess("Manage.onepartGoods.recommend") ? ($data->recommended ? "是" : "否") : 
                ( $data->recommended ? 
                CHtml::link("设为非人气",Yii::app()->createUrl("onepartGoods/recommend",array("id"=>$data->goods_id,"recommend"=>$data->recommended)),array("class"=>"regm-sub")) : 
                CHtml::link("设为人气",Yii::app()->createUrl("onepartGoods/recommend",array("id"=>$data->goods_id,"recommend"=>$data->recommended)),array("class"=>"regm-sub")))',
            'type'=>'raw'
        ),
        array(
            'header' => '操作',
            'class' => 'CButtonColumn',
            'htmlOptions' => array('style' => 'width:300px', 'class' => 'button-column'),
            'template' => '{disable}{enable}{update}{past}{delete}{lottery}',
            'updateButtonLabel' => Yii::t('home', '编辑'),
            'updateButtonImageUrl' => false,
            'deleteButtonImageUrl' => false,
            'buttons' => array(
//                'copy' => array(
//                    'url'=> 'Yii::app()->createUrl("onepartGoods/copy",array("id"=>$data->goods_id))',
//                    'label' => Yii::t('onepartGoods', '复制'),
//                    'click' => 'function(){return confirm("确定要复制吗")}',
//                    'visible' => "Yii::app()->user->checkAccess('Manage.onepartGoods.copy')"
//                ),
                'disable' => array(
                    'url'=> 'Yii::app()->createUrl("onepartGoods/disable",array("id"=>$data->goods_id))',
                    'label' => Yii::t('onepartGoods', '停用'),
                    'visible' => 'Yii::app()->user->checkAccess("Manage.onepartGoods.disable") && $data->is_on_sale'
                ),
                'enable'=>array(
                    'url'=> 'Yii::app()->createUrl("onepartGoods/enable",array("id"=>$data->goods_id))',
                    'label' => Yii::t('onepartGoods', '启用'),
                    'visible' => 'Yii::app()->user->checkAccess("Manage.onepartGoods.enable") && !$data->is_on_sale'
                ),
                'update' => array(
                    'label' => Yii::t('onepartGoods', '修改'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.onepartGoods.Update')"
                ),
                'past' => array(
                    'url'=> 'Yii::app()->createUrl("onepartOrderGoods/past",array("id"=>$data->goods_id))',
                    'label' => Yii::t('onepartGoods', '往期'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.onepartOrderGoods.past')"
                ),
                'delete' => array(
                    'label' => Yii::t('onepartGoods', '删除'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.onepartGoods.delete')"
                ),
                'lottery' => array(
                    'url'   =>  'Yii::app()->createUrl("onepartOrderGoods/lottery",array("id"=>$data->goods_id))',
                    'label' => Yii::t('onepartGoods', '开奖'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.OnepartOrderGoods.Lottery')"
                ),
            ),
        )
    ),
));
?>
<script src="<?php echo DOMAIN_M?>/js/swf/js/artDialog.iframeTools.js"></script>
<script type="text/javascript">
    function sortProduct()
    {
        var num=0,data=[];
        $("#yifenGoods-grid input[name='ids[]']").each(function(index){
            var t = $(this);
            if(t.prop('checked')){
                num++;
                var v = t.val();
                var name = 'sort_order['+v+']';
                input = $("#yifenGoods-grid input[name='"+name+"']");
                sortValue = input.val();
                data.push({id:v,sort:sortValue});
                return ;
            } 
        });
        $.ajax({
            url:'<?php echo Yii::app()->createUrl('/onepartGoods/sort')?>',
            type:'POST',
            dataType:'json',
            data:{'sort_order':data,csrf:'<?php echo Yii::app()->request->getCsrfToken()?>'},
            success:function(data){
                if(data.result == 'success'){
                    alert("排序成功，有"+data.fail+'个失败');
                    window.location.reload();
                }   
            }
        })
        if(!num) alert('请选择商品');
    }
    $('.limitRecommanded').on('click',function(){
        art.dialog.load('<?php echo $this->createUrl("/onepartGoods/limit")?>',{
            title:'设置首页推荐数'
        });
    });
    
    $('body').delegate('#onepartGoods_form','submit',function(){
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

