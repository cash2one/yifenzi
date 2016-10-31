<label>友情链接列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Link.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Link.Admin" id="LinkAdmin">
<label for="LinkAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Link.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Link.Create" id="LinkCreate">
<label for="LinkCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Link.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Link.Update" id="LinkUpdate">
<label for="LinkUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Link.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Link.Delete" id="LinkDelete">
<label for="LinkDelete">删除</label>
)