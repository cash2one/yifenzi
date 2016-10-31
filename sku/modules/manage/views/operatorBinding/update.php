<?php
$this->breadcrumbs=array(
    Yii::t('MemberBind','运营方绑定'),
    Yii::t('MemberBind','修改绑定'),
);
?>
<div class="border-info clearfix search-form">
    <table class="searchTable tab-reg">
        <tr>
            <td>
                <span><?php echo Yii::t('OperatorBinding', '运营方GW号：'); ?></span>
            </td>
            <td>
                <input type="text" id="OPGW" value="<?php echo $result['ps_gai_number'] ?>" class="text-input-bj  least"/>
            </td>
        </tr>
        <tr>
            <td>
                <span><?php echo Yii::t('OperatorBinding', '商家GW号：'); ?></span>
            </td>
            <td>
                <input type="text" id="PGW" value="<?php echo $result['pr_gai_number'] ?>" class="text-input-bj  least"/>
            </td>
        </tr>

        <tr>
            <td>
                <span><?php echo Yii::t('OperatorBinding', '绑定状态：'); ?></span>
            </td>
            <td>
                <select id="status" class="text-input-bj  least">
                    <option <?php if($result['status'] == 1) echo "selected='true'" ?> value="1"><?php echo Yii::t('OperatorBinding', '有效'); ?></option>
                    <option <?php if($result['status'] == 0) echo "selected='true'" ?> value="0"><?php echo Yii::t('OperatorBinding', '无效'); ?></option>
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
            }else {
                $.ajax({
                    type: "post",
                    async: false,
                    dataType: "json",
                    timeout: 5000,
                    url: "<?php echo $this->createUrl("/operatorBinding/upBindRecord",array('id'=>$result['id']));?>",
                    data: {
                        'PGW': PGW,
                        'OPGW': OPGW,
                        'status': status
                    },
                    success: function (data) {
//                        alert(data['result']);
                        if(data['result'] =='pgw'){
                            art.dialog({
                                icon: 'error',
                                content: '商家GW号已失效！',
                                ok: true
                            }); 
                            exit;
                        }if(data['result'] =='opgw'){
                            art.dialog({
                                icon: 'error',
                                content: '请输入有效的运营方GW号！',
                                ok: true
                            });
                            $('#OPGW').val('');                
                            exit;
                        }
                        if (data["result"]=='success') {
                            art.dialog({
                                icon: 'success',
                                content: '修改绑定成功！',
                                ok: true
                            });
                            window.location.href = "<?php echo $this->createUrl("/operatorBinding/admin");?>";
                        } else {
                            art.dialog({
                                icon: 'error',
                                content: '修改绑定失败！message:' + data["message"],
                                ok: true
                            });
                        }

                    }
                });
            }
        }
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
                'BindGW' : BindGW,
                'id' : <?php echo $_GET['id']?>
            },
            success:function(data){
                if(data["result"] == 0){
                    art.dialog({
                        icon: 'error',
                        content: "该商家已绑定过，不能再次被绑定",
                        ok: true
                    });
//                    obj.val("");
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
                        content: "请输入有效的商家GW号！",
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
//        if($('#OPGW').val() != ''){
//            if($('#PGW').val() == $('#OPGW').val()){
//                art.dialog({
//                    icon: 'error',
//                    content: "该商家GW号不能跟运营商GW号相同",
//                    ok: true
//                });
//                $('#OPGW').val('');
//            }else{
//                CheckGWnumber($('#OPGW'))
//            }
//        }
//    });
</script>