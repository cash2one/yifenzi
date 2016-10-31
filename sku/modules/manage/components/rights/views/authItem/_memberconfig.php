<label>会员列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Member.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Member.Admin" id="MemberAdmin">
<label for="MemberAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Member.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Member.Update" id="MemberUpdate">
<label for="MemberUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Member.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Member.Delete" id="MemberDelete">
<label for="MemberDelete">删除</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Member.ResetPass', $rights)): ?>checked="checked"<?php endif; ?> value="Member.ResetPass" id="MemberResetPass">
<label for="MemberResetPass">重设密码</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Member.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Member.Create" id="MemberCreate">
<label for="MemberCreate">添加普通会员</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Member.EnterpriseCreate', $rights)): ?>checked="checked"<?php endif; ?> value="Member.EnterpriseCreate" id="MemberEnterpriseCreate">
<label for="MemberEnterpriseCreate">添加企业会员</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>会员类型配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('MemberType.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="MemberType.Admin" id="MemberTypeAdmin">
<label for="MemberTypeAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('MemberType.Update', $rights)): ?>checked="checked"<?php endif; ?> value="MemberType.Update" id="MemberTypeUpdate">
<label for="MemberTypeUpdate">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>会员角色列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('MemberRole.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="MemberRole.Admin" id="MemberRoleAdmin">
<label for="MemberRoleAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('MemberRole.Create', $rights)): ?>checked="checked"<?php endif; ?> value="MemberRole.Create" id="MemberRoleCreate">
<label for="MemberRoleCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('MemberRole.Update', $rights)): ?>checked="checked"<?php endif; ?> value="MemberRole.Update" id="MemberRoleUpdate">
<label for="MemberRoleUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('MemberRole.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="MemberRole.Delete" id="MemberRoleDelete">
<label for="MemberRoleDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>兴趣爱好列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Interest.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Interest.Admin" id="InterestAdmin">
<label for="InterestAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Interest.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Interest.Create" id="InterestCreate">
<label for="InterestCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Interest.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Interest.Update" id="InterestUpdate">
<label for="InterestUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Interest.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Interest.Delete" id="InterestDelete">
<label for="InterestDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>兴趣爱好分类列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('InterestCategory.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="InterestCategory.Admin" id="InterestCategoryAdmin">
<label for="InterestCategoryAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('InterestCategory.Create', $rights)): ?>checked="checked"<?php endif; ?> value="InterestCategory.Create" id="InterestCategoryCreate">
<label for="InterestCategoryCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('InterestCategory.Update', $rights)): ?>checked="checked"<?php endif; ?> value="InterestCategory.Update" id="InterestCategoryUpdate">
<label for="InterestCategoryUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('InterestCategory.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="InterestCategory.Delete" id="InterestCategoryDelete">
<label for="InterestCategoryDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>群发站内信</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Message.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Message.Create" id="MessageCreate">
<label for="MessageCreate">发送</label>
)