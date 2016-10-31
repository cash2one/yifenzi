<label>积分分配配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.AllocationConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.AllocationConfig" id="HomeAllocationConfig">
<label for="HomeAllocationConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>积分兑现配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.CreditsConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.CreditsConfig" id="HomeCreditsConfig" >
<label for="HomeCreditsConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>企业会员提现配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.ShopCashConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.ShopCashConfig" id="HomeShopCashConfig" >
<label for="HomeShopCashConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>推荐商家会员配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.RefConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.RefConfig" id="HomeRefConfig" >
<label for="HomeRefConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>代理分配比率设置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.AgentDistConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.AgentDistConfig" id="HomeAgentDistConfig">
<label for="HomeAgentDistConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>支付接口配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.PayAPIConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.PayAPIConfig" id="HomePayAPIConfig" >
<label for="HomePayAPIConfig">编辑</label>
)