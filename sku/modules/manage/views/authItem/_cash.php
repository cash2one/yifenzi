<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.RechargeCashManagement', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.RechargeCashManagement" id="RechargeCashManagement"><label for="RechargeCashManagement">充值提现管理</label>
    </td>
    <td>
        <label>提现管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.CashHistory.ApplyCash', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.CashHistory.ApplyCash" id="CashHistoryApplyCash">
        <label for="CashHistoryApplyCash">提现列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.CashHistory.SetReview', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.CashHistory.SetReview" id="CashHistorySetReview">
        <label for="CashHistorySetReview">审阅</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.CashHistory.ApplyCashDetail', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.CashHistory.ApplyCashDetail" id="CashHistoryApplyCashDetail">
        <label for="CashHistoryApplyCashDetail">查看</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.CashHistory.CheckedBatch', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.CashHistory.CheckedBatch" id="CashHistoryCheckedBatch">
        <label for="CashHistoryCheckedBatch">批量审核</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.CashHistory.CashBatchUpdate', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.CashHistory.CashBatchUpdate" id="CashHistoryCashBatchUpdate">
        <label for="CashHistoryCashBatchUpdate">批量转账</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.CashHistory.ApplyCashExport', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.CashHistory.ApplyCashExport" id="CashHistoryApplyCashExport">
        <label for="CashHistoryApplyCashExport">导出excel</label>
        )

    </td>
</tr>