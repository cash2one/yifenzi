<label>积分兑现申请单</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('CashHistory.ApplyCash', $rights)): ?>checked="checked"<?php endif; ?> value="CashHistory.ApplyCash" id="CashHistoryApplyCash">
<label for="CashHistoryApplyCash">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('CashHistory.ApplyCashDetail', $rights)): ?>checked="checked"<?php endif; ?> value="CashHistory.ApplyCashDetail" id="CashHistoryApplyCashDetail">
<label for="CashHistoryApplyCashDetail">查看</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>企业会员提现申请单</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('CashHistory.EnterpriseApplyCash', $rights)): ?>checked="checked"<?php endif; ?> value="CashHistory.EnterpriseApplyCash" id="CashHistoryEnterpriseApplyCash">
<label for="CashHistoryEnterpriseApplyCash">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('CashHistory.EnterpriseApplyCashDetail', $rights)): ?>checked="checked"<?php endif; ?> value="CashHistory.EnterpriseApplyCashDetail" id="CashHistoryEnterpriseApplyCashDetail">
<label for="CashHistoryEnterpriseApplyCashDetail">查看</label>
)