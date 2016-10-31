<tr>
    <td rowspan="5">
        <input type="checkbox" name="rights[]" <?php if (in_array('Main.MemberManagement', $rights)): ?>checked="checked"<?php endif; ?> value="Main.MemberManagement" id="MainMemberManagement"><label for="MainMemberManagement">会员管理</label>
    </td>   
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Member', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Member" id="SubMember"><label for="SubMember">会员管理</label>
    </td>
    <td>
        <?php $this->renderPartial('_memberconfig', array('rights' => $rights)); ?>
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Jms', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Jms" id="SubJms"><label for="SubJms">加盟商管理</label>
    </td>
    <td>
        <?php $this->renderPartial('_jmsconfig', array('rights' => $rights)); ?>
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Store', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Store" id="SubStore"><label for="SubStore">商铺管理</label>
    </td>
    <td>
        <?php $this->renderPartial('_storeconfig', array('rights' => $rights)); ?>
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Common', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Common" id="SubCommon"><label for="SubCommon">公用账户管理</label>
    </td>
    <td>
        <label>共有帐户列表</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('CommonAccount.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="CommonAccount.Admin" id="CommonAccountAdmin">
        <label for="CommonAccountAdmin">列表</label>
        )
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Agent', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Agent" id="SubAgent"><label for="SubAgent">代理管理</label>
    </td>
    <td>
        <?php $this->renderPartial('_agentconfig', array('rights' => $rights)); ?>
    </td>
</tr>