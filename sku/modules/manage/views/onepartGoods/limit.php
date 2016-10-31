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
                最大推荐数:&nbsp;<input type="text" name='max_limit' value="0" class="text-input-bj  middle" id="webConfig_max_limit" onfocus="$('#webConfig_max_limit_em_').hide()">
                <div class="errorMessage" id="webConfig_max_limit_em_" style="display: none"></div>
            </td>
        </tr>
        <tr class="even">
            <td>
                &nbsp;&nbsp;&nbsp;&nbsp;推荐数:&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name='limit' value="0" class="text-input-bj  middle" id="webConfig_limit" onfocus="$('#webConfig_limit_em_').hide()">
                <div class="errorMessage" id="webConfig_limit_em_" style="display: none"></div>
            </td>
        </tr>
    </tbody>
</table>

<div style="text-align: center;margin-top: 10px;" class="do-action">
    <input type="submit" class="regm-sub" value="确定">
    <a href="javascript:;" onclick="$('.aui_state_focus').remove();" class="regm-sub">取消</a>
</div>
<?php $this->endWidget();?>