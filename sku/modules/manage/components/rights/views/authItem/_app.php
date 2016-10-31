<tr>
    <td rowspan="2">
        <input type="checkbox" name="rights[]" <?php if (in_array('Main.AppManagement', $rights)): ?>checked="checked"<?php endif; ?> value="Main.AppManagement" id="MainAppManagement"><label for="MainAppManagement">APP管理</label>
    </td>   
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.AppAdvert', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.AppAdvert" id="SubAppAdvert"><label for="SubAppAdvert">广告管理</label>
    </td>
    <td>
        <label>APP广告位列表</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('AppAdvert.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="AppAdvert.Admin" id="AppAdvertAdmin">
        <label for="AppAdvertAdmin">列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AppAdvert.Create', $rights)): ?>checked="checked"<?php endif; ?> value="AppAdvert.Create" id="AppAdvertCreate">
        <label for="AppAdvertCreate">添加</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AppAdvert.Update', $rights)): ?>checked="checked"<?php endif; ?> value="AppAdvert.Update" id="AppAdvertUpdate">
        <label for="AppAdvertUpdate">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AppAdvert.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="AppAdvert.Delete" id="AppAdvertDelete">
        <label for="AppAdvertDelete">删除</label>
        )
        &nbsp;&nbsp;
        &nbsp;&nbsp;
        <label>APP广告位图片管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('AppAdvertPicture.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="AppAdvertPicture.Admin" id="AppAdvertPictureAdmin">
        <label for="AppAdvertPictureAdmin">列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AppAdvertPicture.Create', $rights)): ?>checked="checked"<?php endif; ?> value="AppAdvertPicture.Create" id="AppAdvertPictureCreate">
        <label for="AppAdvertPictureCreate">添加</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AppAdvertPicture.Update', $rights)): ?>checked="checked"<?php endif; ?> value="AppAdvertPicture.Update" id="AppAdvertPictureUpdate">
        <label for="AppAdvertPictureUpdate">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AppAdvertPicture.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="AppAdvertPicture.Delete" id="AppAdvertPictureDelete">
        <label for="AppAdvertPictureDelete">删除</label>
        )
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.AppManage', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.AppManage" id="SubAppManage"><label for="SubAppManage">APP管理</label>
    </td>
    <td>
        <label>版本管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('AppVersion.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="AppVersion.Admin" id="AppVersionAdmin">
        <label for="AppVersionAdmin">列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AppVersion.Create', $rights)): ?>checked="checked"<?php endif; ?> value="AppVersion.Create" id="AppVersionCreate">
        <label for="AppVersionCreate">添加</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AppVersion.Update', $rights)): ?>checked="checked"<?php endif; ?> value="AppVersion.Update" id="AppVersionUpdate">
        <label for="AppVersionUpdate">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('AppVersion.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="AppVersion.Delete" id="AppVersionDelete">
        <label for="AppVersionDelete">删除</label>
        )
    </td>
</tr>