<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.Goods', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.Goods" id="MainGoods"><label for="MainGoods">商品管理</label>
    </td>   

    <td>
        <label>商品管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Goods.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Goods.Admin" id="GoodsAdmin">
        <label for="GoodsAdmin">商品列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Goods.Apply', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Goods.Apply" id="GoodsApply">
        <label for="GoodsApply">商品审核</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Goods.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Goods.Update" id="GoodsUpdate">
        <label for="GoodsUpdate">更新商品信息</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Goods.ExcelImport', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Goods.ExcelImport" id="GoodsExcelImport">
        <label for="GoodsExcelImport">商家商品导入</label>
        )
         <hr>
        <label>条码库管理</label>
        (
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.BarcodeGoods.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.BarcodeGoods.Admin" id="BarcodeGoodsAdmin">
        <label for="BarcodeGoodsAdmin">条码库商品列表</label>
        <!-- 
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.BarcodeGoods.Export', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.BarcodeGoods.Export" id="BarcodeGoodsExport">
        <label for="BarcodeGoodsExport">条码库导出</label>
         -->
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.BarcodeGoods.View', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.BarcodeGoods.View" id="BarcodeGoodsView">
        <label for="BarcodeGoodsView">查看条码库商品</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.BarcodeGoods.Create', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.BarcodeGoods.Create" id="BarcodeGoodsCreate">
        <label for="BarcodeGoodsCreate">创建条码库商品</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.BarcodeGoods.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.BarcodeGoods.Update" id="BarcodeGoodsUpdate">
        <label for="BarcodeGoodsUpdate">更新条码库商品信息</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.BarcodeGoods.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.BarcodeGoods.Delete" id="BarcodeGoodsDelete">
        <label for="BarcodeGoodsDelete">删除条码库商品</label>
         <input type="checkbox" name="rights[]" <?php if (in_array('Manage.BarcodeGoods.ExcelImport', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.BarcodeGoods.ExcelImport" id="BarcodeGoodsExcelImport">
        <label for="BarcodeGoodsExcelImport">条码库导入</label>
        )
        
         <hr>
          <label>录入审核</label>
          (
           <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.Admin" id="InputGoodsAdmin">
        <label for="InputGoodsAdmin">待审核列表</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.Release', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.Release" id="InputGoodsApply">
        <label for="InputGoodsApply">发布管理</label>
         <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.OpenGoods', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.OpenGoods" id="InputGoodsOpenGoods">
        <label for="InputGoodsOpenGoods">重新开放</label>
         <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.Apply', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.Apply" id="InputGoodsOpenGoods">
        <label for="InputGoodsApply">审核</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.EnCreate', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.EnCreate" id="InputGoodsEnCreate">
        <label for="InputGoodsEnCreate">添加项目</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.Update" id="InputGoodsUpdate">
        <label for="InputGoodsUpdate">编辑</label>
         <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.DeleteRule', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.DeleteRule" id="InputGoodsDeleteRule">
        <label for="InputGoodsDeleteRule">删除</label>
         <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.StoreActive', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.StoreActive" id="InputGoodsStoreActive">
        <label for="InputGoodsStoreActive">店铺录入活动商品</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.AddStore', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.AddStore" id="InputGoodsAddStore">
        <label for="InputGoodsAddStore">新增活动店铺</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.AddGoods', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.AddGoods" id="InputGoodsAddGoods">
        <label for="InputGoodsAddGoods">新增商品</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.AddGoods', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.AddGoods" id="InputGoodsAddGoods">
        <label for="InputGoodsAddGoods">新增商品</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.UpdateGoods', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.UpdateGoods" id="InputGoodsUpdateGoods">
        <label for="InputGoodsUpdateGoods">编辑商品</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.DeleteGoods', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.DeleteGoods" id="InputGoodsDeleteGoods">
        <label for="InputGoodsDeleteGoods">删除商品</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.UpdateStore', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.UpdateStore" id="InputGoodsUpdateStore">
        <label for="InputGoodsUpdateStore">编辑店铺</label>
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.InputGoods.StoreDelete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.InputGoods.StoreDelete" id="InputGoodsStoreDelete">
        <label for="InputGoodsStoreDelete">删除店铺</label>
         )
    </td>
</tr>