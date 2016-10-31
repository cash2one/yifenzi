<tr>
    <td rowspan="5">
        <input type="checkbox" name="rights[]" <?php if (in_array('Main.RechargeCashManagement', $rights)): ?>checked="checked"<?php endif; ?> value="Main.RechargeCashManagement" id="MainRechargeCashManagement"><label for="MainRechargeCashManagement">充值兑现管理</label>
    </td>   
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Card', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Card" id="SubCard"><label for="SubCard">充值卡管理</label>
    </td>
    <td>
        <?php $this->renderPartial('_prepaidcardconfig', array('rights' => $rights)); ?>
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Return', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Return" id="SubReturn"><label for="SubReturn">积分返还管理</label>
    </td>
    <td>
        <?php $this->renderPartial('_returnconfig', array('rights' => $rights)); ?>
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Cash', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Cash" id="SubCash"><label for="SubCash">兑现管理</label>
    </td>
    <td>
        <?php $this->renderPartial('_cashconfig', array('rights' => $rights)); ?>
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Recharge', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Recharge" id="SubRecharge"><label for="SubRecharge">积分充值管理</label>
    </td>
    <td>
        <label>积分充值列表</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Recharge.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Recharge.Admin" id="RechargeAdmin">
        <label for="RechargeAdmin">列表</label>
        )
    </td>
</tr>