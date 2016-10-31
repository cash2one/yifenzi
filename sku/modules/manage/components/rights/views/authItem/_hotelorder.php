<label>酒店订单查询列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('HotelOrder.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="HotelOrder.Admin" id="HotelOrderAdmin">
<label for="HotelOrderAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelOrder.View', $rights)): ?>checked="checked"<?php endif; ?> value="HotelOrder.View" id="HotelOrderView">
<label for="HotelOrderView">查看</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>酒店新订单列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('HotelOrder.NewList', $rights)): ?>checked="checked"<?php endif; ?> value="HotelOrder.NewList" id="HotelOrderNewList">
<label for="HotelOrderNewList">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelOrder.VerifyOrder', $rights)): ?>checked="checked"<?php endif; ?> value="HotelOrder.VerifyOrder" id="HotelOrderVerifyOrder">
<label for="HotelOrderVerifyOrder">确认</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelOrder.CancleOrder', $rights)): ?>checked="checked"<?php endif; ?> value="HotelOrder.CancleOrder" id="HotelOrderCancleOrder">
<label for="HotelOrderCancleOrder">取消</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>酒店已确认订单列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('HotelOrder.VerifyList', $rights)): ?>checked="checked"<?php endif; ?> value="HotelOrder.VerifyList" id="HotelOrderVerifyList">
<label for="HotelOrderVerifyList">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelOrder.OrderComplete', $rights)): ?>checked="checked"<?php endif; ?> value="HotelOrder.OrderComplete" id="HotelOrderOrderComplete">
<label for="HotelOrderOrderComplete">完成</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>酒店对账订单列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('HotelOrder.CheckingList', $rights)): ?>checked="checked"<?php endif; ?> value="HotelOrder.CheckingList" id="HotelOrderCheckingList">
<label for="HotelOrderCheckingList">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelOrder.OrderChecking', $rights)): ?>checked="checked"<?php endif; ?> value="HotelOrder.OrderChecking" id="HotelOrderOrderChecking">
<label for="HotelOrderOrderChecking">对账</label>
)