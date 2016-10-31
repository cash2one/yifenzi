<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.WebConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.WebConfig" id="MainHome"><label for="MainWebConfig">配置管理</label>
    </td>   
    <td>
        <label>网站配置管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Home.SiteConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Home.SiteConfig" id="HomeSiteConfig">
        <label for="HomeSiteConfig">网站配置</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Home.AssignConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Home.AssignConfig" id="HomeAssignConfig">
        <label for="HomeAssignConfig">分配配置</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Home.AmountLimitConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Home.AmountLimitConfig" id="HomeAmountLimitConfig">
        <label for="HomeAmountLimitConfig">消费限额配置</label>
		<input type="checkbox" name="rights[]" <?php if (in_array('Manage.Home.OrderExpireTimeConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Home.OrderExpireTimeConfig" id="OrderExpireTimeConfig">
        <label for="OrderExpireTimeConfig">订单时间配置</label>
        )

    </td>
</tr>