<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.TradeManagement', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.TradeManagement" id="ManageMainTradeManagement"><label for="ManageMainTradeManagement">交易管理</label>
    </td>
    <td>
        <label>账户余额</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AccountBalance.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AccountBalance.Admin" id="AccountBalanceAdmin">
        <label for="AccountBalanceAdmin">余额列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AccountBalance.CheckHash', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AccountBalance.CheckHash" id="AccountBalanceCheckHash">
        <label for="AccountBalanceCheckHash">检查hash</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AccountBalance.ResetHash', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AccountBalance.ResetHash" id="AccountBalanceResetHash">
        <label for="AccountBalanceResetHash">重置hash</label>
        )
        <hr>
        <label for="FreshQuestResultAdmin">交易流水</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AccountFlow.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AccountFlow.Admin" id="AccountFlowAdmin"> 
        <label for="AccountFlowAdmin">流水日志</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AccountFlow.View', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AccountFlow.View" id="AccountFlowView"> 
        <label for="AccountFlowView">查看</label>
        )
   

    </td>
</tr>
<!--<tr>
    <td>
        
    </td>
</tr>-->