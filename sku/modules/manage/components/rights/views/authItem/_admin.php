<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Main.Administrators', $rights)): ?>checked="checked"<?php endif; ?> value="Main.Administrators" id="MainAdministrators"><label for="MainAdministrators">管理员管理</label>
    </td>   
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Admin" id="SubAdmin"><label for="SubAdmin">管理员管理</label>
    </td>
    <td>
        <label>管理员列表</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('User.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="User.Admin" id="UserAdmin">
        <label for="UserAdmin">列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('User.Update', $rights)): ?>checked="checked"<?php endif; ?> value="User.Update" id="UserUpdate">
        <label for="UserUpdate">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('User.Reset', $rights)): ?>checked="checked"<?php endif; ?> value="User.Reset" id="UserReset">
        <label for="UserReset">重置密码</label>
        )
        <label>管理员角色</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('AuthItem.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="AuthItem.Admin" id="AuthItemAdmin">
        <label for="AuthItemAdmin">列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AuthItem.CreateRole', $rights)): ?>checked="checked"<?php endif; ?> value="AuthItem.CreateRole" id="AuthItemCreateRole">
        <label for="AuthItemCreateRole">添加</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AuthItem.UpdateRole', $rights)): ?>checked="checked"<?php endif; ?> value="AuthItem.UpdateRole" id="AuthItemUpdateRole">
        <label for="AuthItemUpdateRole">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AuthItem.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="AuthItem.Delete" id="AuthItemDelete">
        <label for="AuthItemDelete">删除</label>
        )
        <label>管理员操作日志</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('User.Log', $rights)): ?>checked="checked"<?php endif; ?> value="User.Log" id="UserLog">
        <label for="UserLog">列表</label>
        )
    </td>
</tr>