<label>酒店品牌列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('HotelBrand.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="HotelBrand.Admin" id="HotelBrandAdmin">
<label for="HotelBrandAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelBrand.Create', $rights)): ?>checked="checked"<?php endif; ?> value="HotelBrand.Create" id="HotelBrandCreate">
<label for="HotelBrandCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelBrand.Update', $rights)): ?>checked="checked"<?php endif; ?> value="HotelBrand.Update" id="HotelBrandUpdate">
<label for="HotelBrandUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelBrand.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="HotelBrand.Delete" id="HotelBrandDelete">
<label for="HotelBrandDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>酒店级别列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('HotelLevel.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="HotelLevel.Admin" id="HotelLevelAdmin">
<label for="HotelLevelAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelLevel.Create', $rights)): ?>checked="checked"<?php endif; ?> value="HotelLevel.Create" id="HotelLevelCreate">
<label for="HotelLevelCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelLevel.Update', $rights)): ?>checked="checked"<?php endif; ?> value="HotelLevel.Update" id="HotelLevelUpdate">
<label for="HotelLevelUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelLevel.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="HotelLevel.Delete" id="HotelLevelDelete">
<label for="HotelLevelDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>酒店热门地址</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('HotelAddress.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="HotelAddress.Admin" id="HotelAddressAdmin">
<label for="HotelAddressAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelAddress.Create', $rights)): ?>checked="checked"<?php endif; ?> value="HotelAddress.Create" id="HotelAddressCreate">
<label for="HotelAddressCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelAddress.Update', $rights)): ?>checked="checked"<?php endif; ?> value="HotelAddress.Update" id="HotelAddressUpdate">
<label for="HotelAddressUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelAddress.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="HotelAddress.Delete" id="HotelAddressDelete">
<label for="HotelAddressDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>酒店价格区间</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('HotelParams.PriceRangeConfig', $rights)): ?>checked="checked"<?php endif; ?> value="HotelParams.PriceRangeConfig" id="HotelParamsPriceRangeConfig">
<label for="HotelParamsPriceRangeConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>酒店参数配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('HotelParams.HotelParamsConfig', $rights)): ?>checked="checked"<?php endif; ?> value="HotelParams.HotelParamsConfig" id="HotelParamsHotelParamsConfig">
<label for="HotelParamsHotelParamsConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>酒店管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Hotel.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Hotel.Admin" id="HotelAdmin">
<label for="HotelAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Hotel.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Hotel.Create" id="HotelCreate">
<label for="HotelCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Hotel.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Hotel.Update" id="HotelUpdate">
<label for="HotelUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Hotel.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Hotel.Delete" id="HotelDelete">
<label for="HotelDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>酒店客房列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('HotelRoom.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="HotelRoom.Admin" id="HotelRoomAdmin">
<label for="HotelRoomAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelRoom.Create', $rights)): ?>checked="checked"<?php endif; ?> value="HotelRoom.Create" id="HotelRoomCreate">
<label for="HotelRoomCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelRoom.Update', $rights)): ?>checked="checked"<?php endif; ?> value="HotelRoom.Update" id="HotelRoomUpdate">
<label for="HotelRoomUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('HotelRoom.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="HotelRoom.Delete" id="HotelRoomDelete">
<label for="HotelRoomDelete">删除</label>
)
