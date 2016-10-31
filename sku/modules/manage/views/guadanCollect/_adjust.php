<?php
#@月充值限额
?>
<?php
    $form = $this->beginWidget('CActiveForm',array(
        'id'=>$this->id . '_form',
//        'enableAjaxValidation' => true,
//        'enableClientValidation' => true,
//        'clientOptions' => array(
//            'validateOnSubmit' => true,
//        ),
    ));
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come">
    <tbody>
        <tr class="odd">
            <td>
                <input type="radio" name='adjust' value="0" <?php if(!$model->value){ echo 'checked="checked"';}?>> 不限制
            </td>
        </tr>
        <tr class="even">
            <td>
                <input type="radio" name='adjust' value="1" <?php if($model->value){ echo 'checked="checked"';}?> id="has-checked"> 限制
                <?php echo $form->textField($model,'value',array('class'=>'text-input-bj  middle','onfocus'=>"$('#webConfig_value_em_').hide()"))?>积分
                <div class="errorMessage" id="webConfig_value_em_" style="display: none"></div>
            </td>
        </tr>
    </tbody>
</table>

<div>
    <b>说明：</b>
    <p>当月会员充值积分不可高于本限额，若触发购买的积分金额超过设定值，在用户端提示限额超出</p>
</div>
<div style="text-align: center;margin-top: 10px;" class="do-action">
    <input type="submit" class="regm-sub" value="确定">
    <a href="javascript:;" onclick="$('.aui_state_focus').remove();" class="regm-sub">取消</a>
</div>
<?php $this->endWidget();?>