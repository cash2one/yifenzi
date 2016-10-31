<style>
.tab-come th {text-align: center;}
.long {width:500px;}</style>
<div style="display: inline-block;text-align: left;width: 44%">
     <input style="float: left;margin-left: 5px;" id="manual_send_sms" type="button" value="手动发送短信" class="regm-sub" >
</div>
<div class="form">
<?php $form = $this->beginWidget('CActiveForm',$formConfig);?>
     <script type="text/javascript" src="/js/EMSwitchBox.js"></script>
   
    <table width="100%" border="0" class="tab-come" cellspacing="1" cellpadding="0">
        <tbody>
            <tr>
            <th colspan="2" style="text-align: center" class="title-th">
                <?php echo Yii::t('home', '短信模板配置'); ?>
            </th>
        </tr>
        
        <tr>
            <th style="width: 300px"><?php echo $form->labelEx($model, 'winner'); ?></th>
            <td>
                <?php echo $form->textField($model, 'winner', array('class' => 'text-input-bj  long valid')); ?>
                <?php echo $form->error($model, 'winner'); ?>
            </td>
        </tr>

		
         <tr>
            <th></th>
            <td><?php echo CHtml::submitButton(Yii::t('home', '保存'), array('class' => 'reg-sub')); ?></td>
        </tr>
        </tbody>
    </table>
<?php $this->endWidget();?>
</div>

<div style="display: none" id="confirmArea">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="tab-come">
	    <tbody>

            <tr id="confirmTR" >
                
                <td class="even" >
                    <input id="phone" style="float: left;margin-left: 5px;width:300px;height: 30px;" type="text" name="phone" value="" placeholder="输入手机号码,多个输入可用;隔开">
                </td>
            </tr>

            <tr id="confirmTR" style="background:#FFF;">
                
                <td class="even" >                   
                   <textarea id="sms_content" style="float: left;margin-left: 5px;width:300px;height: 200px;" type="text" name="sms_content" value="" placeholder="输入短信内容"></textarea>
                </td>
            </tr>
		</tbody>
	</table>
</div>
<script type="text/javascript">
//手动发送短信
    $("#manual_send_sms").bind('click',function() {     
        var code = $(this).attr("data-code");
        var url = '<?php echo Yii::app()->createAbsoluteUrl('/onepartSms/yfzSendSms') ?>';
        art.dialog({
            title: '<?php echo Yii::t('sellerOrder', '手动发送短信') ?>',
           // okVal: '<?php echo Yii::t('sellerOrder', '发送') ?>',
		    button: [{name: '发送', callback: function () {
				var phone = $("#phone").val();
				var sms_content = $("#sms_content").val();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: url,
                    data: {code: code, YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>',phone:phone,sms_content:sms_content},
                    success: function(data) {
                        if (data.success) {
                            art.dialog({icon: 'succeed', content: data.success});
                            location.reload();
                        }else {
                            alert(data.error);
                        }
                    }
                });
				this
				.button({
                    id: 'button-disabled',
					name: '发送',
                    disabled: true,//防止网络卡而造成重复发送短信
                });
				return false;
			    }
			
			}] ,
            cancelVal: '<?php echo Yii::t('sellerOrder', '取消') ?>',
            content: $("#confirmArea").html(),
            lock: true,
            cancel: true,
           
        });
        return false;
    });
</script>