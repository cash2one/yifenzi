<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.Order', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.Order" id="Manage.MainOrder"><label for="MainOrder">订单</label>
    </td>   
    <td>
        <label>订单管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Order.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Order.Admin" id="OrderAdmin">
        <label for="OrderAdmin">订单列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Order.View', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Order.View" id="OrderView">
        <label for="OrderView">查看订单</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Order.CloseOrder', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Order.CloseOrder" id="OrderCloseOrder">
        <label for="OrderCloseOrder">关闭订单</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Order.Complete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Order.Complete" id="OrderCompleteOrder">
        <label for="OrderCompleteOrder">完成订单</label>
        )
        
    </td>
</tr>