<?php
$this->breadcrumbs=array(
		Yii::t('MemberBind','绑定管理'),
		Yii::t('MemberBind','绑定详情'),
);
?>

<script type="text/javascript">
function CheckGWnumber(bind_id,gai_fun_member_id,gai_number){
	$.ajax({
		type : "post",
		async : false,
		dataType : "json",
		timeout : 5000,
		url : "<?php echo $this->createUrl("memberBind/checkBindGW");?>",
		data : {
			'bind_id' : bind_id,
			'gai_fun_member_id' : gai_fun_member_id,
			'sku_number' : gai_number,
			},
		success:function(data){
			var str = '<table class="listTable" algin="right"  style="border-collapse:separate;border-spacing:15px;">';
			for(var key in data){
				if(key == "sku_number"){
					str += "<tr>"
						  +   "<td colspan='2'>GW号："+ data[key] +"</td>"
						 // +   "<td>"+ data[key] +"</td>"
						  +"</tr>";
					}else{
						str += "<tr><td>名单列表：</td><td></td></tr>";
						for(var keyval in data[key]){
							if(keyval%2 == 0){
								 str +=  "<tr><td>"+ data[key][keyval] +"</td>"
								}else{
									str +=  "<td>"+ data[key][keyval] +"</td></tr>"
								}
							}
						}
				}
			str += '</table>';
			$('#confirmArea').html(str);
			art.dialog({
				icon: 'success',
				title:"绑定名单",
				content: $("#confirmArea").html(),
				ok: true
				});
			},
		error:function(){
			art.dialog({
				icon: 'error',
				title:"绑定名单",
				content: '数据错误！请重新加载！',
				ok: true});
			}
		});
}
</script>

<input id="Btn_Return" type="button" value="<?php echo Yii::t('MemberBind', '返回'); ?>" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl("/MemberBind/Index"); ?>'" />
<h></h>
<div class="border-info clearfix search-form">
<table class="searchTable">
    <tr>
		<td>
		<span>绑定时间：<font color="red"><?php echo date("Y-m-d H:i:s",$create_time); ?>   </font></span>
		</td>
		<td>
		<span>绑定类型：<font color="red"><?php echo $type; ?>   </font></span>
		</td>
	</tr>
	<tr>
		<td>
		<span>绑定用户数：<font color="red"><?php echo $BindNumber ?>   </font></span>
		</td>
		<td>
		<span>服务GW号：<font color="red"><?php echo $BindGW; ?>   </font></span>
		</td>
   </tr>
   
</table>
</div>

	<div class="coypyright"  style="display: none;" id="confirmArea">

	</div>
<?php 
$this->widget('GridView',array(
		'id' => 'MemberBind-GridView',
		'dataProvider' => $model->search($id),
		'cssFile' => false,
		'itemsCssClass' => 'tab-reg',
		'columns' => array(
				array(
						'headerHtmlOptions' => array('width' => '25%'),
						'header' => Yii::t('MemberBind','盖粉GW号'),
						'value' => '$data->gai_number',
				),
				array(
						'headerHtmlOptions' => array('width' => '25%'),
						'header' => Yii::t('MemberBind','绑定用户数量'),
						'value' => 'MemberBindDetail::GetCount($data->bind_id,$data->gai_fun_member_id)',
				),
				array(
						'header' => Yii::t('MemberBind', '操作'),
						'type' => 'raw',
						'value' => 'MemberBindDetail::CreateButton($data->bind_id,$data->gai_fun_member_id,$data->gai_number)'
				),
		),
));
?>