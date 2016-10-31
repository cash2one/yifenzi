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
         <hr>
         <label>广告添加</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AppAdvertPicture.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AppAdvertPicture.Admin" id="AppAdvertPictureAdmin">
        <label for="AppAdvertPictureAdmin">列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AppAdvertPicture.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AppAdvertPicture.Create" id="AppAdvertPictureCreate">
        <label for="AppAdvertPictureCreate">添加</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AppAdvertPicture.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AppAdvertPicture.Update" id="AppAdvertPictureUpdate">
        <label for="AppAdvertPictureUpdate">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.AppAdvertPicture.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.AppAdvertPicture.Delete" id="AppAdvertPictureDelete">
        <label for="AppAdvertPictureDelete">删除</label>
        )
       
    </td>
</tr>