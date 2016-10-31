<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.AppAdvert', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.AppAdvert" id="MainAppAdvert"><label for="MainAppAdvert">广告管理</label>
    </td>   
    <td>
        <label>广告位列表</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AppAdvert.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AppAdvert.Admin" id="AppAdvertAdmin">
        <label for="AppAdvertAdmin">列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AppAdvert.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AppAdvert.Create" id="AppAdvertCreate">
        <label for="AppAdvertCreate">添加</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AppAdvert.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AppAdvert.Update" id="AppAdvertUpdate">
        <label for="AppAdvertUpdate">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AppAdvert.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AppAdvert.Delete" id="AppAdvertDelete">
        <label for="AppAdvertDelete">删除</label>
        )
       
    </td>
</tr>