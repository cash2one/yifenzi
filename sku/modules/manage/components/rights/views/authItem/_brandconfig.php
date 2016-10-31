<label>商品分类管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Category.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Category.Admin" id="CategoryAdmin">
<label for="CategoryAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Category.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Category.Create" id="CategoryCreate">
<label for="CategoryCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Category.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Category.Update" id="CategoryUpdate">
<label for="CategoryUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Category.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Category.Delete" id="CategoryDelete">
<label for="CategoryDelete">删除</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Category.GenerateAllCategoryCache', $rights)): ?>checked="checked"<?php endif; ?> value="Category.GenerateAllCategoryCache" id="AdvertGenerateAllCategoryCache">
<label for="CategoryGenerateAllCategoryCache">缓存更新</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>商品品牌管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Brand.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Brand.Admin" id="BrandAdmin">
<label for="BrandAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Brand.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Brand.Create" id="BrandCreate">
<label for="BrandCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Brand.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Brand.Update" id="BrandUpdate">
<label for="BrandUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Brand.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Brand.Delete" id="BrandDelete">
<label for="BrandDelete">删除</label>
)