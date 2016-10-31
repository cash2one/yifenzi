<tr>
    <td rowspan="4">
        <input type="checkbox" name="rights[]" <?php if (in_array('Main.SiteConfigurationManagement', $rights)): ?>checked="checked"<?php endif; ?> value="Main.SiteConfigurationManagement" id="MainSiteConfigurationManagement"><label for="MainSiteConfigurationManagement">网站配置管理</label>
    </td>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Config', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Config" id="SubConfig"><label for="SubConfig">网站配置管理</label>
    </td>   
    <td>
        <?php $this->renderPartial('_siteconfig', array('rights' => $rights)); ?>
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Score', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Score" id="SubScore"><label for="SubScore">积分配置管理</label>
    </td>
    <td>
        <?php $this->renderPartial('_scoreconfig', array('rights' => $rights)); ?>
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Charity', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Charity" id="SubCharity"><label for="SubCharity">盖网通公益管理</label>
    </td>
    <td>
        <label>捐款列表</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Charity.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Charity.Admin" id="CharityAdmin">
        <label for="CharityAdmin">查看</label>
        )
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.Data', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.Data" id="SubCharity"><label for="SubData">网站数据管理</label>
    </td>
    <td>
        <label>多语言-后台</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Link.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Link.Admin" id="LinkAdmin">
        <label for="LinkAdmin">查看修改</label>
        )
        <label>多语言-前台</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Link.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Link.Admin" id="LinkAdmin">
        <label for="LinkAdmin">查看修改</label>
        )
    </td>
</tr>