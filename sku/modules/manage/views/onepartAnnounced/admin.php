<?php
/* @var $this ProductController */
/* @var $model Product */
$this->breadcrumbs = array('最新揭晓' => array('admin'));
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#goods-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
//	return false;
});
");
?>

<?php
if(!Yii::app()->user->checkAccess('Manage.OnepartGoods.Sort')){
    $display = "style='display:none'";
}else{
    $display = '';
}
$this->widget('GridView', array(
    'id' => 'yifenGoods-grid',
    'dataProvider' => $model->getAnnouncedNew(),
    'itemsCssClass' => 'tab-reg', 
    'cssFile' => false,
    'columns' => array(
        array(
            'selectableRows' => 2,
            'class' => 'CCheckBoxColumn',
            'checkBoxHtmlOptions' => array('name' => 'ids[]'),
        ),
        array(
            'footer' => '<button type="button" '."$display".'  onclick="sortProduct();" class="regm-sub">排序</button>',
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
			'id'=>'goods_id',
        ),
        array(
            'name'=>'goods_name',
            'value'=>'isset($data->goods_name)?$data->goods_name:""', 
            'type'=>'raw',
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
            'header' => '操作',
            'class' => 'CButtonColumn',
            'htmlOptions' => array('style' => 'width:300px', 'class' => 'button-column'),
            'template' => '{past}',
            'updateButtonLabel' => Yii::t('home', '编辑'),
            'updateButtonImageUrl' => false,
            'deleteButtonImageUrl' => false,
            'buttons' => array(
                'past' => array(
                    'url'=> 'Yii::app()->createUrl("onepartOrderGoods/past",array("id"=>$data->goods_id))',
                    'label' => Yii::t('onepartGoods', '往期'),
                    'visible' => "Yii::app()->user->checkAccess('Manage.onepartOrderGoods.past')"
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
            url:'<?php echo Yii::app()->createUrl('/onepartAnnounced/sort')?>',
            type:'POST',
            dataType:'json',
            data:{'sort_order':data,csrf:'<?php echo Yii::app()->request->getCsrfToken()?>'},
            success:function(data){
                if(data.result == 'success'){
                    alert("排序成功，有"+data.fail+'个失败');
                    window.location.reload();
                }   
            }
        });
        if(!num) alert('请选择商品');
    }

</script>



