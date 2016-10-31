<label>商品管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Product.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Product.Admin" id="ProductAdmin">
<label for="ProductAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Product.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Product.Update" id="ProductUpdate">
<label for="ProductUpdate">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>积分日志</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Wealth.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Wealth.Admin" id="WealthAdmin">
<label for="WealthAdmin">列表</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>物流公司管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Express.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Express.Admin" id="ExpressAdmin">
<label for="ExpressAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Express.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Express.Create" id="ExpressCreate">
<label for="ExpressCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Express.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Express.Update" id="ExpressUpdate">
<label for="ExpressUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Express.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Express.Delete" id="ExpressDelete">
<label for="ExpressDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>商品规格管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Spec.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Spec.Admin" id="SpecAdmin">
<label for="SpecAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Spec.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Spec.Create" id="SpecCreate">
<label for="SpecCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Spec.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Spec.Update" id="SpecUpdate">
<label for="SpecUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Spec.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Spec.Delete" id="SpecDelete">
<label for="SpecDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>商品规格值管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('SpecValue.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="SpecValue.Admin" id="SpecValueAdmin">
<label for="SpecValueAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('SpecValue.Create', $rights)): ?>checked="checked"<?php endif; ?> value="SpecValue.Create" id="SpecValueCreate">
<label for="SpecValueCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('SpecValue.Update', $rights)): ?>checked="checked"<?php endif; ?> value="SpecValue.Update" id="SpecValueUpdate">
<label for="SpecValueUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('SpecValue.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="SpecValue.Delete" id="SpecValueDelete">
<label for="SpecValueDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>商品类型管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Type.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Type.Admin" id="TypeAdmin">
<label for="TypeAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Type.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Type.Create" id="TypeCreate">
<label for="TypeCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Type.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Type.Update" id="TypeUpdate">
<label for="TypeUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Type.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Type.Delete" id="TypeDelete">
<label for="TypeDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>商品属性管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Attribute.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Attribute.Admin" id="AttributeAdmin">
<label for="AttributeAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Attribute.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Attribute.Create" id="AttributeCreate">
<label for="AttributeCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Attribute.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Attribute.Update" id="AttributeUpdate">
<label for="AttributeUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Attribute.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Attribute.Delete" id="AttributeDelete">
<label for="AttributeDelete">删除</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>商品属性值管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('AttributeValue.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="AttributeValue.Admin" id="AttributeValueAdmin">
<label for="AttributeValueAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('AttributeValue.Create', $rights)): ?>checked="checked"<?php endif; ?> value="AttributeValue.Create" id="AttributeValueCreate">
<label for="AttributeValueCreate">添加</label>
<input type="checkbox" name="rights[]" <?php if (in_array('AttributeValue.Update', $rights)): ?>checked="checked"<?php endif; ?> value="AttributeValue.Update" id="AttributeValueUpdate">
<label for="AttributeValueUpdate">编辑</label>
<input type="checkbox" name="rights[]" <?php if (in_array('AttributeValue.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="AttributeValue.Delete" id="AttributeValueDelete">
<label for="AttributeValueDelete">删除</label>
)