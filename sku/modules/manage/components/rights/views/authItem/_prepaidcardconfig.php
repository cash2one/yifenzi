<label>充值卡管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('PrepaidCard.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="PrepaidCard.Admin" id="PrepaidCardAdmin">
<label for="PrepaidCardAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('PrepaidCard.Create', $rights)): ?>checked="checked"<?php endif; ?> value="PrepaidCard.Create" id="PrepaidCardCreate">
<label for="PrepaidCardCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('PrepaidCard.List', $rights)): ?>checked="checked"<?php endif; ?> value="PrepaidCard.List" id="PrepaidCardList">
<label for="PrepaidCardList">使用记录</label>
<input type="checkbox" name="rights[]" <?php if (in_array('PrepaidCard.View', $rights)): ?>checked="checked"<?php endif; ?> value="PrepaidCard.View" id="PrepaidCardView">
<label for="PrepaidCardView">查看</label>
<input type="checkbox" name="rights[]" <?php if (in_array('PrepaidCard.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="PrepaidCard.Delete" id="PrepaidCardDelete">
<label for="PrepaidCardDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
