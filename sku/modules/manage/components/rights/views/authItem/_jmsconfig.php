<label>线下加盟商列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Franchisee.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Franchisee.Admin" id="FranchiseeAdmin">
<label for="FranchiseeAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Franchisee.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Franchisee.Create" id="FranchiseeCreate">
<label for="FranchiseeCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Franchisee.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Franchisee.Update" id="FranchiseeUpdate">
<label for="FranchiseeUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Franchisee.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Franchisee.Delete" id="FranchiseeDelete">
<label for="FranchiseeDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>加盟商对账</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeConsumptionRecord.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeConsumptionRecord.Admin" id="FranchiseeConsumptionRecordAdmin">
<label for="FranchiseeConsumptionRecordAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeConsumptionRecord.Confirm', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeConsumptionRecord.Confirm" id="FranchiseeConsumptionRecordConfirm">
<label for="FranchiseeConsumptionRecordConfirm">批量对账</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>加盟商线下活动城市</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeActivityCity.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeActivityCity.Admin" id="FranchiseeActivityCityAdmin">
<label for="FranchiseeActivityCityAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeActivityCity.Create', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeActivityCity.Create" id="FranchiseeActivityCityCreate">
<label for="FranchiseeActivityCityCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeActivityCity.Update', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeActivityCity.Update" id="FranchiseeActivityCityUpdate">
<label for="FranchiseeActivityCityUpdate">修改</label>
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeActivityCity.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeActivityCity.Delete" id="FranchiseeActivityCityDelete">
<label for="FranchiseeActivityCityDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>加盟商文章列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeArtile.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeArtile.Admin" id="FranchiseeArtileAdmin">
<label for="FranchiseeActivityCityAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeArtile.Update', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeArtile.Update" id="FranchiseeArtileUpdate">
<label for="FranchiseeArtileUpdate">修改</label>
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeArtile.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeArtile.Delete" id="FranchiseeArtileDelete">
<label for="FranchiseeArtileDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>盖机列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Machine.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Machine.Admin" id="MachineAdmin">
<label for="MachineAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Machine.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Machine.Create" id="MachineCreate">
<label for="MachineCreate">添加推荐者</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Machine.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Machine.Delete" id="MachineDelete">
<label for="MachineDelete">移除推荐者</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>加盟商分类</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeCategory.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeCategory.Admin" id="FranchiseeCategoryAdmin">
<label for="FranchiseeCategoryAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeCategory.Create', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeCategory.Create" id="FranchiseeCategoryCreate">
<label for="FranchiseeCategoryCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeCategory.Update', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeCategory.Update" id="FranchiseeCategoryUpdate">
<label for="FranchiseeCategoryUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeCategory.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeCategory.Delete" id="FranchiseeCategoryDelete">
<label for="FranchiseeCategoryDelete">删除</label>
<input type="checkbox" name="rights[]" <?php if (in_array('FranchiseeCategory.GenerateAllCategoryCache', $rights)): ?>checked="checked"<?php endif; ?> value="FranchiseeCategory.GenerateAllCategoryCache" id="FranchiseeCategoryGenerateAllCategoryCache">
<label for="FranchiseeCategoryGenerateAllCategoryCache">缓存更新</label>
)