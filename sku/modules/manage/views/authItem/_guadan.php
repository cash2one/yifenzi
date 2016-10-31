<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.Guadan', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.Guadan" id="MainGuadan"><label for="MainGuadan">积分挂单管理</label>
    </td>
    <td>
        <label>积分批发</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadanpifa.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadanpifa.Admin" id="GuadanpifaAdmin">
        <label for="GuadanpifaAdmin">积分挂单列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadanpifa.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadanpifa.Create" id="GuadanpifaCreate">
        <label for="GuadanpifaCreate">新增政策</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadanpifa.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadanpifa.Update" id="GuadanpifaUpdate">
        <label for="GuadanpifaUpdate">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadanpifa.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadanpifa.Delete" id="GuadanpifaUpdate">
        <label for="GuadanpifaDelete">删除</label>
        )
        <hr>
        <label for="">挂单管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadan.GuadanAdmin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadan.GuadanAdmin" id="GuadanAdmin"> 
        <label for="GuadanAdmin">挂单管理</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadan.ExcelImport', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadan.ExcelImport" id="ExcelImport"> 
        <label for="ExcelImport">挂单导入</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadan.Collect', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadan.Collect" id="Collect"> 
        <label for="Collect">挂单提取及新增政策</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadan.DelCollect', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadan.DelCollect" id="DelCollect"> 
        <label for="DelCollect">删除挂单</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadan.Frozen', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadan.Frozen" id="Frozen"> 
        <label for="Frozen">挂单冻结</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadan.Unfreeze', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadan.Unfreeze" id="Unfreeze"> 
        <label for="Unfreeze">挂单解冻</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadan.Disable', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadan.Disable" id="Disable"> 
        <label for="Disable">挂单撤销</label>
        
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadan.GuadanImportTemplate', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadan.GuadanImportTemplate" id="GuadanImportTemplate"> 
        <label for="GuadanImportTemplate">挂单导入模板</label>
        
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.GuadanRule.Add', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.GuadanRule.Add" id="GuadanRuleAdd">
        <label for="GuadanRuleAdd">新增政策规则</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.GuadanRule.Del', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.GuadanRule.Del" id="GuadanRuleDel">
        <label for="GuadanRuleDel">删除政策规则</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.GuadanRule.Edit', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.GuadanRule.Edit" id="GuadanRuleEdit">
        <label for="GuadanRuleEdit">编辑政策规则</label>
        
        )
        <hr>
        <label>售卖管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.GuadanCollect.SellAdmin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.GuadanCollect.SellAdmin" id="SellAdmin"> 
        <label for="SellAdmin">售卖管理</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.GuadanCollect.View', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.GuadanCollect.View" id="View"> 
        <label for="View">查看详情</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.GuadanCollect.Stop', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.GuadanCollect.Stop" id="Stop"> 
        <label for="Stop">中止挂单</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.GuadanCollect.StopSales', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.GuadanCollect.StopSales" id="SellAdmin"> 
        <label for="StopSales">中止售卖</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.GuadanCollect.Adjust', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.GuadanCollect.Adjust" id="Adjust"> 
        <label for="Adjust">调整额度</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.GuadanCollect.Enablezc', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.GuadanCollect.Enablezc" id="Adjust"> 
        <label for="Enablezc">开启挂单</label>
        )
        <hr>
        <label>绑定管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.MemberBind.Index', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.MemberBind.Index" id="MemberBindIndex">
        <label for="MemberBindIndex">绑定管理列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.MemberBind.CreateBind', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.MemberBind.CreateBind" id="MemberBindCreateBind">
        <label for="MemberBindCreate">手动绑定</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.MemberBind.Detail', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.MemberBind.Detail" id="MemberBindDetail">
        <label for="MemberBindUpdate">绑定详情</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.MemberBind.CheckBindGW', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.MemberBind.CheckBindGW" id="MemberBindCheckBindGW">
        <label for="MemberBindCheckBindGW">查看名单</label>

        )
        <hr>
        <label>日志管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Guadan.Log', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Guadan.Log" id="GuadanguadanLog">
        <label for="FreshQuestResultAdmin">日志列表</label>
        )

    </td>
</tr>
<!--<tr>
    <td>
        
    </td>
</tr>-->