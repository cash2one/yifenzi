<?php
/* @var $this OrderController */
/* @var $model Order */
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#order-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
//	return false;
});
");

$this->breadcrumbs = array(
    Yii::t('order', '一份子后台管理'),
    Yii::t('order', '栏目列表'),

);

?>
<div style="display: inline-block;text-align: left;width: 44%">
        <?php if(Yii::app()->user->checkAccess('Manage.OnepartManagement.Adds')) echo CHtml::link('添加栏目',Yii::app()->createUrl("/onepartManagement/adds"),array('class'=>'regm-sub'));?>&nbsp;
</div>
<?php if(Yii::app()->user->hasFlash('del')){
    echo Yii::app()->user->getFlash('del');
}
?>

<?php
if(!Yii::app()->user->checkAccess('Manage.OnepartManagement.Sort')){
    $display = "style='display:none'";
}else{
    $display = '';
}
$this->widget('GridView', array(
    'id' => 'order-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'tab-reg',
    'columns' => array(
	    array(
            'selectableRows' => 2,
            'class' => 'CCheckBoxColumn',
            'checkBoxHtmlOptions' => array('name' => 'ids[]'),
        ),
        array(
            'footer' => '<button type="button" '."$display".' onclick="sortColumn();" class="regm-sub">排序</button>',
            'class'=>'InputColumn',
            'name'=>'sort_order',
            'value'=>'isset($data->sort_order)?$data->sort_order:""',
            'type'=> 'text',
            'htmlOptions'=>array('style'=>'width:30px'),
            'visible' => "Yii::app()->user->checkAccess('Manage.OnepartManagement.Sort')",
        ),
        array(
            'name' => 'id',
            'value' => '$data->id',
            'type' => 'raw',
        ),
        array(
            'name'=>'column_name',
            'value'=>'$data->column_name',
            'type'=>'raw'
        ),
       // array(
       //     'name'=>'column_desc',
       //     'value'=>'$data->column_desc',
       // ),
        array(
            'name'=>'column_type',
            //'value'=>'$data->column_type ? "云购模型" : "其它模型"',
			'value' => 'Column::getColumType($data->column_type)',
        ),
        array(
            'name' => 'column_att',
            //'value' => '$data->column_att ? "内部栏目" : "其它栏目"',
			'value' => 'Column::getColumAtt($data->column_att)',
        ),
        array(
            'name' => 'tourl',
            'value' => '$data->tourl',
        ),
        array(
            'name' => 'addtime',
            'value' => 'date("Y-m-d H:i:s", $data->addtime)'
        ),
        array(
            'name' => 'altertime',
            'value' => 'date("Y-m-d H:i:s", $data->altertime)'
        ),
        array(
            "name"  =>  'sort_order',
            'value' =>  '$data->sort_order ',
        ),
        array(
            "name"  =>  'is_show',
            'value' =>  '$data->is_show ? "显示" : "不显示"',
        ),
        array(
            'class' => 'CButtonColumn',
            "header"    =>  "操作",
            'afterDelete'=>'function(link,success,data){alert(data);}',
            'template' => '{update}{delete}',
            'htmlOptions' => array('style' => 'width:220px', 'class' => 'button-column'),
            'updateButtonImageUrl' => false,
            'deleteButtonImageUrl' => false,
            'deleteConfirmation' => Yii::t('advertising', '此栏目下已添加商品不可随意删除,请先删除商品！'),
            'buttons' => array(
                'update' => array(
                    'label' => Yii::t('user', '编辑'),
                    'url' => 'Yii::app()->createUrl("onepartManagement/updates",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.OnepartManagement.Updates')"
                ),
                'delete' => array(
                    'label' => Yii::t('user', '删除'),
                    'url' => 'Yii::app()->createUrl("onepartManagement/delete",array("id"=>$data->id))',
                    'visible' => "Yii::app()->user->checkAccess('Manage.onepartManagement.Delete')",
                ),
            )
        )
    ),
));
?>

<script type="text/javascript">
function sortColumn()
    {
        var num=0,data=[];
        $("#order-grid input[name='ids[]']").each(function(index){
            var t = $(this);
            if(t.prop('checked')){
                num++;
                var v = t.val();
                var name = 'sort_order['+v+']';
                input = $("#order-grid input[name='"+name+"']");
                sortValue = input.val();
                data.push({id:v,sort:sortValue});
                return ;
            } 
        });
        $.ajax({
            url:'<?php echo Yii::app()->createUrl('/onepartManagement/sort')?>',
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
        if(!num) alert('请选择栏目');
    }
</script>