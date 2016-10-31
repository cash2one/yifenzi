<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.QuestResult', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.QuestResult" id="MainQuestResult"><label for="MainQuestResult">问卷调查管理</label>
    </td>
    <td>

        <label>问卷调查管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.FreshQuestResult.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.FreshQuestResult.Admin" id="FreshQuestResultAdmin">
        <label for="FreshQuestResultAdmin">问卷调查列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.FreshQuestResult.ViewQuest', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.FreshQuestResult.ViewQuest" id="FreshQuestResultViewQuest">
        <label for="FreshQuestResultViewQuest">查看详情</label>

        )

  </br>
    
    

        <label>sku商户加盟资料审核</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.PartnerJoinAuditing.Index', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.PartnerJoinAuditing.Index" id="PartnerJoinAuditingIndex">
        <label for="FreshQuestResultAdmin">sku商户加盟资料审核列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.PartnerJoinAuditing.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.PartnerJoinAuditing.Update" id="PartnerJoinAuditingApply">
        <label for="FreshQuestResultViewQuest">审核</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.PartnerJoinAuditing.Apply', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.PartnerJoinAuditing.Apply" id="PartnerJoinAuditingApply">
        <label for="FreshQuestResultViewQuest">审核操作</label>
        )

    </td>
</tr>