<?php
/* @var $this PartnersController */
/* @var $model Partners */
/** @var  $form CActiveForm */
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come">
    <tbody>
        <tr class="odd">
            <th style="width: 120px" align="right"><?php echo $model->getAttributeLabel('code')?>：</th>
            <td>
                <?php echo $model->code;?>
            </td>
        </tr>
        <tr class="even">
            <th style="width: 120px" align="right"><?php echo '投入积分'?>：</th>
            <td>
                <?php echo bcadd($model->amount_unbind,$model->amount_bind,2);?>
            </td>
        </tr>
        <tr class="odd">
            <th style="width: 120px" align="right"><?php echo $model->getAttributeLabel('time_start')?>：</th>
            <td>
                <?php echo date('Y-m-d H:i:s',$model->time_start);?>
            </td>
        </tr>
        <tr class="even">
            <th style="width: 120px" align="right"><?php echo '售卖进度'?>：</th>
            <td>
                <?php echo bcdiv(bcadd($model->sale_amount_bind,$model->sale_amount_unbind),bcadd($model->amount_bind,$model->amount_unbind,2),2)*100 . "%";?>
            </td>
        </tr>
        <tr class="odd">
            <th style="width: 120px" align="right"><?php echo $model->getAttributeLabel('bind_size')?>：</th>
            <td>
                <?php echo $model->bind_size;?>
            </td>
        </tr>
        <tr class="even">
            <th style="width: 120px" align="right"><?php echo $model->getAttributeLabel('new_member_count')?>：</th>
            <td>
                <?php echo $model->new_member_count;?>
            </td>
        </tr>
    </tbody>
</table>

<div>
    <b>说明：</b>
    <p>中止后的售卖计划不可再次启动，未出售的积分将按出资比例返回至会员挂单中。</p>
</div>
<div style="text-align: center;margin-top: 10px;" class="do-action">
    <a href="javascript:;" class="regm-sub stopped" data-id="<?php echo $model->id?>">确定中止</a>&nbsp;&nbsp;<a href="javascript:;" onclick="$('.aui_state_focus').remove();" class="regm-sub">取消</a>
</div>
