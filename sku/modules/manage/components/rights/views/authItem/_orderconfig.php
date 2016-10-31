<label>订单管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Order.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Order.Admin" id="OrderAdmin">
<label for="OrderAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Order.View', $rights)): ?>checked="checked"<?php endif; ?> value="Order.View" id="OrderView">
<label for="OrderView">查看</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>订单评论</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Comment.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Comment.Admin" id="CommentAdmin">
<label for="CommentAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Comment.ChangeStatus', $rights)): ?>checked="checked"<?php endif; ?> value="Comment.ChangeStatus" id="CommentChangeStatus">
<label for="CommentChangeStatus">切换状态</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>异常订单</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Order.Exception', $rights)): ?>checked="checked"<?php endif; ?> value="Order.Exception" id="OrderException">
<label for="OrderException">列表</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>运费编辑管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('FreightEdit.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="FreightEdit.Admin" id="FreightEditAdmin">
<label for="FreightEditAdmin">列表</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;