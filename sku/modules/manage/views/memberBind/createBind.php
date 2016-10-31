<script type="text/javascript">
function SubmitBind(){
	CountNumber = $('#CountNumber').html(); //待绑定总数
	BindGW = $('#BindGW').val();    //被绑定的盖网号(推荐人)
	BindNumber = $('#BindNumber').val();   //手动绑定的新盖网号数目

	if(!BindGW || parseInt(BindNumber) == 0){
		art.dialog({
			icon: 'error',
			content: '输入的绑定会员数不可小于零或绑定盖网号不能为空',
			ok: true});

		}else if((parseInt(BindNumber)==BindNumber) && (parseInt(CountNumber)>=parseInt(BindNumber))){
	        $.ajax({
		    		type : "post",
		    		async : false,
		    		dataType : "json",
		    		timeout : 5000,
		    		url : "<?php echo $this->createUrl("/memberBind/bindRecord");?>",
		    		data : {
		    			'BindGW' : BindGW,
		    			'BindNumber' : BindNumber,
		    			},
	    			success:function(data){
		    			if(data["result"]){
		    				art.dialog({
	    						icon: 'success',
	    						content: '绑定成功！',
	    						ok: true
	    						});
		    				 window.location.href="<?php echo $this->createUrl("/memberBind/index");?>"; 
		    			}else{
		    				art.dialog({
	    						icon: 'error',
	    						content: '绑定失败！message:'+data["message"],
	    						ok: true
	    						});
			    		}

	    				},
    				error:function(data){
    					art.dialog({
    						icon: 'error',
    						content: '数据错误！请重新加载！',
    						ok: true
    						});
	    				},
	            });
        }else{
        	art.dialog({
    			icon: 'error',
    			content: '输入的绑定会员数必须为整数且必须小于等于待绑定用户数',
    			ok: true});
			return false;
            }

}

function CheckGWnumber(){
	BindGW = $('#BindGW').val();
	$.ajax({
		type : "post",
		async : false,
		dataType : "json",
		timeout : 5000,
		url : "<?php echo $this->createUrl("/memberBind/checkGW");?>",
		data : {
			'BindGW' : BindGW,
			},
		success:function(data){
			if(data["result"] == 0){
				art.dialog({
					icon: 'error',
					content: "请输入正确的GW号或此GW号从未登录",
					ok: true
					});
				$('#BindGW').val("");
				}
			},
		error:function(){
			art.dialog({
				icon: 'error',
				content: '数据错误！请重新加载！',
				ok: true});
			}
		});
}
</script>
<div class="border-info clearfix search-form">
<table class="searchTable tab-reg">
<tr>
  <td colspan="2">
      <span>待绑定用户数量：<span size="3px" color="red" id="CountNumber"><?php echo MemberBind::GetNotBindMem();?>  </span></span>
  </td>
</tr>

<tr>
   <td>
      <span>绑定GW号：</span>
   </td>
  <td>
     <input type="text" id="BindGW" class="text-input-bj  least" onblur="CheckGWnumber()"/>
  </td>
</tr>

<tr>
   <td>
      <span>绑定会员数：</span>
	</td>
	<td>
	  <input type="text" id="BindNumber" class="text-input-bj  least" />
	</td>
</tr>

<tr>
   <td>
   <a class="regm-sub" id="Btn_Add" href="javascript:SubmitBind()">提交绑定</a>
   <td>
	   <input id="Btn_Return" type="button" value="<?php echo Yii::t('MemberBind', '取消'); ?>" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl("memberBind/index"); ?>'" />
	</td>
</tr>
   
</table>
</div>