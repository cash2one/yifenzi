<label>积分返还卡</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('PrepaidCard.Index', $rights)): ?>checked="checked"<?php endif; ?> value="PrepaidCard.Index" id="PrepaidCardIndex">
<label for="PrepaidCardIndex">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('PrepaidCard.CreateGeneral', $rights)): ?>checked="checked"<?php endif; ?> value="PrepaidCard.CreateGeneral" id="PrepaidCardCreateGeneral">
<label for="PrepaidCardCreateGeneral">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('PrepaidCard.Detail', $rights)): ?>checked="checked"<?php endif; ?> value="PrepaidCard.Detail" id="PrepaidCardDetail">
<label for="PrepaidCardDetail">使用记录</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;