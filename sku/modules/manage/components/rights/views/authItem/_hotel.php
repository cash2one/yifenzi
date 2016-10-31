<tr>
    <td rowspan="2">
        <input type="checkbox" name="rights[]" <?php if (in_array('Main.HotelManagement', $rights)): ?>checked="checked"<?php endif; ?> value="Main.HotelManagement" id="MainHotelManagement"><label for="MainHotelManagement">酒店管理</label>
    </td>   
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.HotelInfo', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.HotelInfo" id="SubHotelInfo"><label for="SubHotelInfo">酒店信息</label>
    </td>
    <td>
        <?php $this->renderPartial('_hotelinfo', array('rights' => $rights)); ?>
    </td>
</tr>
<tr>
    <td>
        <input type="checkbox" name="rights[]" <?php if (in_array('Sub.HotelOrder', $rights)): ?>checked="checked"<?php endif; ?> value="Sub.HotelOrder" id="SubHotelOrder"><label for="SubHotelOrder">酒店订单</label>
    </td>
    <td>
        <?php $this->renderPartial('_hotelorder', array('rights' => $rights)); ?>
    </td>
</tr>