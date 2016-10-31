<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.Administrators', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.Administrators" id="ManageMainAdministrators"><label for="ManageMainAdministrators">管理员管理</label>
    </td>   
    <td>
    
    <label>用户信息</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.User.UserInfo', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.User.UserInfo" id="UserUserInfo">
        <label for="UserUserInfo">操作记录</label>
        <!-- 
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.User.ModifyPassword', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.User.ModifyPassword" id="UserModifyPassword">
        <label for="ManageUserModifyPassword">修改密码</label>
         -->
        )
     <hr>
        <label>管理员列表</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.User.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.User.Admin" id="UserAdmin">
        <label for="UserAdmin">列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.User.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.User.Create" id="UserCreate">
        <label for="UserCreate">添加</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.User.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.User.Update" id="UserUpdate">
        <label for="UserUpdate">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.User.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.User.Delete" id="UserDelete">
        <label for="UserDelete">删除</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.User.Reset', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.User.Reset" id="UserReset">
        <label for="UserReset">重置密码</label>
        )
         <hr>
        <label>管理员角色</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AuthItem.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AuthItem.Admin" id="AuthItemAdmin">
        <label for="AuthItemAdmin">列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AuthItem.CreateRole', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AuthItem.CreateRole" id="AuthItemCreateRole">
        <label for="AuthItemCreateRole">添加</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AuthItem.UpdateRole', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AuthItem.UpdateRole" id="AuthItemUpdateRole">
        <label for="AuthItemUpdateRole">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AuthItem.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AuthItem.Delete" id="AuthItemDelete">
        <label for="AuthItemDelete">删除</label>
        )
         <hr>
        <label>管理员操作日志</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.User.Log', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.User.Log" id="UserLog">
        <label for="UserLog">列表</label>
        )
    </td>
</tr>