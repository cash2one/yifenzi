<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Sub.Category', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Sub.Category" id="MainCategory"><label for="MainCategory">分类管理</label>
    </td>   
    <td>
        <label>商品分类</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Category.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Category.Admin" id="CategoryAdmin">
        <label for="CategoryAdmin">列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Category.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Category.Create" id="CategoryCreate">
        <label for="CategoryCreate">添加</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Category.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Category.Update" id="CategoryUpdate">
        <label for="CategoryUpdate">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Category.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Category.Delete" id="CategoryDelete">
        <label for="CategoryDelete">删除</label>
        )
         <hr>
        <label>门店分类</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.StoreCategory.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.StoreCategory.Admin" id="StoreCategoryAdmin">
        <label for="StoreCategoryAdmin">列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.StoreCategory.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.StoreCategory.Create" id="StoreCategoryCreate">
        <label for="StoreCategoryCreate">添加</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.StoreCategory.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.StoreCategory.Update" id="StoreCategoryUpdate">
        <label for="StoreCategoryUpdate">编辑</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.StoreCategory.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.StoreCategory.Delete" id="StoreCategoryDelete">
        <label for="StoreCategoryDelete">删除</label>
        )
    </td>
</tr>