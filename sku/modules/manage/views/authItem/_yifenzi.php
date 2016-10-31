<?php
/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2016/8/19
 * Time: 13:07
 */
?>
<tr>
    <td rowspan="1">
        <input type="checkbox" name="rights[]" <?php if (in_array('Manage.Main.OnepartManagement', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.Main.OnepartManagement" id="ManageMainOnepartManagement"><label for="ManageMainOnepartManagement">一份后台管理</label>
</td>
<td>
    <label>栏目管理</label>
    (
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartManagement.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartManagement.Admin" id="OnepartManagementAdmin">
    <label for="OnepartManagementAdmin">栏目列表</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartManagement.Adds', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartManagement.Adds" id="OnepartManagementAdds">
    <label for="OnepartManagementAdds">添加栏目</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartManagement.Updates', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartManagement.Updates" id="OnepartManagementUpdates">
    <label for="OnepartManagementUpdates">编辑栏目</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartManagement.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartManagement.Delete" id="OnepartManagementDelete">
    <label for="OnepartManagementDelete">删除栏目</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartManagement.Sort', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartManagement.Sort" id="OnepartManagementSort">
    <label for="OnepartManagementSort">栏目排序</label>
    )
    <hr>
    <label for="FreshQuestResultAdmin">商品管理</label>
    (
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartGoods.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartGoods.Admin" id="OnepartGoodsAdmin">
    <label for="OnepartGoodsAdmin">产品列表</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartGoods.Insert', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartGoods.Insert" id="OnepartGoodsInsert">
    <label for="OnepartGoodsInsert">添加商品</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartGoods.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartGoods.Update" id="OnepartGoodsUpdate">
    <label for="OnepartGoodsUpdate">更新商品</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartGoods.Disable', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartGoods.Disable" id="OnepartGoodsDisable">
    <label for="OnepartGoodsDisable">停售产品</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartGoods.Enable', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartGoods.Enable" id="OnepartGoodsEnable">
    <label for="OnepartGoodsEnable">商品启用</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartGoods.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartGoods.Delete" id="OnepartGoodsDelete">
    <label for="OnepartGoodsDelete">删除产品</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartGoods.Sort', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartGoods.Sort" id="OnepartGoodsSort">
    <label for="OnepartGoodsSort">产品排序 </label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartGoods.Limit', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartGoods.Limit" id="OnepartGoodsLimit">
    <label for="OnepartGoodsLimit">设置推荐限制数 </label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartGoods.Recommend', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartGoods.Recommend" id="OnepartGoodsRecommend">
    <label for="OnepartGoodsRecommend">人气商品设置与取消 </label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartOrderGoods.Lottery', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartOrderGoods.Lottery" id="OnepartOrderGoodsLottery">
    <label for="OnepartOrderGoodsLottery">开奖 </label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartOrderGoods.Past', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartOrderGoods.Past" id="OnepartOrderGoodsPast">
    <label for="OnepartOrderGoodsPast">往期列表</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartOrderGoods.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartOrderGoods.Update" id="OnepartOrderGoodsUpdate">
    <label for="OnepartOrderGoodsUpdate">往期修改</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartOrderGoods.BeforeGoodsView', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartOrderGoods.BeforeGoodsView" id="OnepartOrderGoodsBeforeGoodsView">
    <label for="OnepartOrderGoodsBeforeGoodsView">往期详情</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartOrderGoods.OrderGoodsView', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartOrderGoods.OrderGoodsView" id="OnepartOrderGoodsOrderGoodsView">
    <label for="OnepartOrderGoodsOrderGoodsView">订单详情</label>
    )
    <hr>
    <label>最新揭晓</label>
    (
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartAnnounced.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartAnnounced.Admin" id="OnepartAnnounced">
    <label for="OnepartGoodsAdmin">最新揭晓列表</label>
    )
    <hr>
    <label>品牌管理</label>
    (
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartBrand.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartBrand.Admin" id="OnepartBrandAdmin">
    <label for="OnepartBrandAdmin">品牌列表</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartBrand.Adds', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartBrand.Adds" id="OnepartBrandAdds">
    <label for="OnepartBrandAdds">新增品牌</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartBrand.Updates', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartBrand.Updates" id="OnepartBrandUpdates">
    <label for="OnepartBrandUpdates">编辑品牌</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartBrand.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartBrand.Delete" id="OnepartBrandDelete">
    <label for="OnepartBrandDelete">删除品牌</label>
    )
    <hr>
    <label>广告管理</label>
    (
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartAdvertising.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartAdvertising.Admin" id="OnepartAdvertisingAdmin">
    <label for="OnepartAdvertisingAdmin">广告列表</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartAdvertising.Adds', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartAdvertising.Adds" id="OnepartAdvertisingAdds">
    <label for="OnepartAdvertisingAdds">新增广告</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartAdvertising.Updates', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartAdvertising.Updates" id="OnepartAdvertisingUpdates">
    <label for="OnepartAdvertisingUpdates">编辑广告</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartAdvertising.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartAdvertising.Delete" id="OnepartAdvertisingDelete">
    <label for="OnepartAdvertisingDelete">删除广告</label>
    )
    <hr>
    <label>短信管理</label>
    (
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartSms.YfzSendSmsRecord', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartSms.YfzSendSmsRecord" id="OnepartSmsYfzSendSmsRecord">
    <label for="OnepartSmsYfzSendSmsRecord">短信发送记录</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartSms.YfzSmsModel', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartSms.YfzSmsModel" id="OnepartSmsYfzSmsModel">
    <label for="OnepartSmsYfzSmsModel">短信模板配置</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartSms.YfzSendSms', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartSms.YfzSendSms" id="OnepartSmsYfzSendSms">
    <label for="OnepartSmsYfzSendSms">发送短信</label>
    )
    <hr>
    <label>订单管理</label>
    (
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartOrder.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartOrder.Admin" id="OnepartOrderAdmin">
    <label for="AccountBalanceAdmin">订单列表</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartOrder.View', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartOrder.View" id="OnepartOrderView">
    <label for="OnepartOrderCheck">查看中奖订单信息</label>
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartOrder.Check', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartOrder.Check" id="OnepartOrderCheck">
    <label for="OnepartOrderCheck">公司验证</label>
    )
    <hr>
    <label>支付管理</label>
    (
    <input type="checkbox" name="rights[]" <?php if (in_array('Manage.OnepartPay.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Manage.OnepartPay.Admin" id="OnepartPayAdmin">
    <label for="OnepartPayAdmin">订单列表</label>

    )
    <hr>

</td>
</tr>
<!--<tr>
    <td>

    </td>
</tr>-->