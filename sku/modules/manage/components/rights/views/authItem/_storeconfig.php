<label>商家列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Store.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Store.Admin" id="StoreAdmin">
<label for="StoreAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Store.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Store.Update" id="StoreUpdate">
<label for="StoreUpdate">编辑</label>
)