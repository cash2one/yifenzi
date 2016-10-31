<?php
$this->breadcrumbs=array(
    Yii::t('MemberBind','运营方绑定'),
    Yii::t('MemberBind','手动绑定'),
);
?>
<div class="border-info clearfix search-form">
<table class="searchTable tab-reg">
    <tr>
        <td>
            <span><?php echo Yii::t('OperatorBinding', '运营方GW号：'); ?></span>
        </td>
        <td>
            <input type="text" id="OPGW" class="text-input-bj  least"/>
        </td>
    </tr>
    <tr>
        <td>
            <span><?php echo Yii::t('OperatorBinding', '商家GW号：'); ?></span>
        </td>
        <td>
            <input type="text" id="PGW" class="text-input-bj  least"/>
        </td>
    </tr>

    <tr>
        <td>
            <span><?php echo Yii::t('OperatorBinding', '绑定状态：'); ?></span>
        </td>
        <td>
            <!--<input type="text" id="status" VALUE="1" class="text-input-bj  least" />-->
            <select id="status" class="text-input-bj  least">
                <option value="1"><?php echo Yii::t('OperatorBinding', '有效'); ?></option>
                <option value="0"><?php echo Yii::t('OperatorBinding', '无效'); ?></option>
            </select>
        </td>
    </tr>
<tr>
   <td>
   <a class="regm-sub" id="Btn_Add" href="javascript:SubmitBind()">提交绑定</a>
   <td>
	   <input id="Btn_Return" type="button" value="<?php echo Yii::t('OperatorBinding', '取消'); ?>" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl("operatorBinding/admin"); ?>'" />
	</td>
</tr>
   
</table>
</div>
<script type="text/javascript">
    function SubmitBind(){
        PGW = $('#PGW').val();
        OPGW = $('#OPGW').val();
        status = $('#status').val();

        if(PGW == '' || OPGW == ''){
            art.dialog({
                icon: 'error',
                content: '运营方GW号或商家GW号不能为空！',
                ok: true
            });
        }else{

            if(PGW == OPGW){
                art.dialog({
                    icon: 'error',
                    content: "该商家GW号不能跟运营商GW号相同",
                    ok: true
                });
                $('#OPGW').val('');
            }else{
                $.ajax({
                    type : "post",
                    async : false,
                    dataType : "json",
                    timeout : 5000,
                    url : "<?php echo $this->createUrl("/operatorBinding/bindRecord");?>",
                    data : {
                        'PGW' : PGW,
                        'OPGW' : OPGW,
                        'status' : status
                    },
                    success:function(data){
                        if(data['result'] =='pgw'){
                            art.dialog({
                                icon: 'error',
                                content: '商家GW号已失效或不存在！',
                                ok: true
                            }); 
                            $('#PGW').val('');
                            exit;
                        }
                        if(data['result'] =='opgw'){
                            art.dialog({
                                icon: 'error',
                                content: '请输入有效的运营方GW号！',
                                ok: true
                            });
                            $('#OPGW').val('');                
                            exit;
                        }
                        if(data['result']== 'haspgw'){
                              art.dialog({
                                icon: 'error',
                                content: '商家已绑定过，不能再次被绑定,请在绑定列表中进行修改！',
                                ok: true
                            });              
                            exit;
                        }
                        if(data["result"]){
                            art.dialog({
                                icon: 'success',
                                content: '绑定成功！',
                                ok: true
                            });
                            window.location.href="<?php echo $this->createUrl("/operatorBinding/admin");?>";
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
                    }
                });

            }
        }
    }


    function CheckGWnumber(obj){
        var BindGW = obj.val();
        $.ajax({
            type : "post",
            async : false,
            dataType : "json",
            timeout : 5000,
            url : "<?php echo $this->createUrl("/operatorBinding/checkGW");?>",
            data : {
                'BindGW' : BindGW
            },
            success:function(data){
                if(data["result"] == 0){
                    art.dialog({
                        icon: 'error',
                        content: "该商家不存在",
                        ok: true
                    });
                    obj.val("");
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

    function CheckGWnumberBind(obj){
        var BindGW = obj.val();
        $.ajax({
            type : "post",
            async : false,
            dataType : "json",
            timeout : 5000,
            url : "<?php echo $this->createUrl("/operatorBinding/checkPartner");?>",
            data : {
                'BindGW' : BindGW
            },
            success:function(data){
                if(data["result"] == 0){
                    art.dialog({
                        icon: 'error',
                        content: "该商家已绑定过，不能再次被绑定,请在绑定列表中进行修改",
                        ok: true
                    });
                    obj.val("");
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


//    $('#PGW').blur(function(){
//        if($('#PGW').val() != ''){
//            CheckGWnumber($('#PGW'));
//            CheckGWnumberBind($('#PGW'));
//        }
//    });

//    $('#OPGW').blur(function(){
//        if($('#PGW').val() != ''){
//            if($('#PGW').val() == $('#OPGW').val()){
//                art.dialog({
//                    icon: 'error',
//                    content: "该商家GW号不能跟运营商GW号相同",
//                    ok: true
//                });
//                $('#OPGW').val('');
//            }else{
//                CheckGWnumber($('#OPGW'));
//            }
//        }
//    });
</script>