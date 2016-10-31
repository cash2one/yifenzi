<label>广告位列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Advert.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Advert.Admin" id="AdvertAdmin">
<label for="AdvertAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Advert.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Advert.Create" id="AdvertCreate">
<label for="AdvertCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Advert.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Advert.Update" id="AdvertUpdate">
<label for="AdvertUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Advert.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Advert.Delete" id="AdvertDelete">
<label for="AdvertDelete">删除</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Advert.GenerateAllAdvertCache', $rights)): ?>checked="checked"<?php endif; ?> value="Advert.GenerateAllAdvertCache" id="AdvertGenerateAllAdvertCache">
<label for="AdvertGenerateAllAdvertCache">缓存更新</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>广告位图片管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('AdvertPicture.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="AdvertPicture.Admin" id="AdvertPictureAdmin">
<label for="AdvertPictureAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('AdvertPicture.Create', $rights)): ?>checked="checked"<?php endif; ?> value="AdvertPicture.Create" id="AdvertPictureCreate">
<label for="AdvertPictureCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('AdvertPicture.Update', $rights)): ?>checked="checked"<?php endif; ?> value="AdvertPicture.Update" id="AdvertPictureUpdate">
<label for="AdvertPictureUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('AdvertPicture.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="AdvertPicture.Delete" id="AdvertPictureDelete">
<label for="AdvertPictureDelete">删除</label>
)