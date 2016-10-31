<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.Partners', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.Partners" id="MainPartners"><label for="MainPartners">商户管理</label>
    </td>   
    <td>
        <label>商户管理列表</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Partners.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Partners.Admin" id="PartnersAdmin">
        <label for="PartnersAdmin">商家列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Partners.Apply', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Partners.Apply" id="PartnersApply">
        <label for="PartnersApply">商家审核</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Partners.Disable', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Partners.Disable" id="PartnersDisable">
        <label for="PartnersDisable">禁用商家</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Partners.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Partners.Update" id="PartnersUpdate">
        <label for="PartnersUpdate">更新商家信息</label>
        )
         <hr>
        <label>门店管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Supermarkets.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Supermarkets.Admin" id="SupermarketsAdmin">
        <label for="SupermarketsAdmin">门店列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Supermarkets.Apply', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Supermarkets.Apply" id="SupermarketsApply">
        <label for="SupermarketsApply">门店审核</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Supermarkets.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Supermarkets.Update" id="SupermarketsUpdate">
        <label for="SupermarketsUpdate">更新门店信息</label>
        )
        <hr>
        
         <label>售货机管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.VendingMachine.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.VendingMachine.Admin" id="VendingMachineAdmin">
        <label for="VendingMachineAdmin">售货机列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.VendingMachine.Apply', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.VendingMachine.Apply" id="VendingMachineApply">
        <label for="VendingMachineApply">售货机审核</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.VendingMachine.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.VendingMachine.Update" id="VendingMachineUpdate">
        <label for="VendingMachineUpdate">更新售货机信息</label>
        )
        <hr>
           <label>生鲜机管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.FreshMachine.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.FreshMachine.Admin" id="FreshMachineAdmin">
        <label for="FreshMachineAdmin">生鲜机列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.FreshMachine.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.FreshMachine.Create" id="FreshMachineCreate">
        <label for="FreshMachineCreate">添加生鲜机</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.FreshMachine.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.FreshMachine.Update" id="FreshMachineUpdate">
        <label for="FreshMachineUpdate">更新生鲜机信息</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.FreshMachine.Record', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.FreshMachine.Record" id="FreshMachineRecord">
        <label for="FreshMachineRecord">签到记录</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.FreshMachine.RecordOne', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.FreshMachine.RecordOne" id="FreshMachineRecordOne">
        <label for="FreshMachineRecordOne">签到列表</label>
        )
        <hr>
        <label>订单管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Order.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Order.Admin" id="OrderAdmin">
        <label for="OrderAdmin">订单列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Order.FreshAdmin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Order.FreshAdmin" id="OrderFreshAdmin">
        <label for="OrderFreshAdmin">盖鲜生订单列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Order.View', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Order.View" id="OrderView">
        <label for="OrderView">查看订单</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Order.CloseOrder', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Order.CloseOrder" id="OrderCloseOrder">
        <label for="OrderCloseOrder">关闭订单</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Order.Complete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Order.Complete" id="OrderCompleteOrder">
        <label for="OrderCompleteOrder">完成订单</label>
        
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Order.Export', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Order.Export" id="OrderExport">
        <label for="OrderExport">导出订单</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Order.FreshExport', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Order.FreshExport" id="OrderFreshExport">
        <label for="OrderFreshExport">导出盖鲜生订单</label>
        
        )
<hr>
        <label>个人认证管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.MemberPersonalAuthentication.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.MemberPersonalAuthentication.Admin" id="MemberPersonalAuthenticationAdmin">
        <label for="PartnersAdmin">个人认证列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.MemberPersonalAuthentication.Apply', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.MemberPersonalAuthentication.Apply" id="MemberPersonalAuthenticationApply">
        <label for="PartnersApply">个人认证审核</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OperatorBinding.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OperatorBinding.Admin" id="MemberOperatorBindingAdmin">
        <label for="PartnersOperatorBinding">运营方绑定管理</label>
        )
    </td>
    

    
</tr>