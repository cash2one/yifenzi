<label>代理列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('CommonAccountAgentDist.AgentList', $rights)): ?>checked="checked"<?php endif; ?> value="CommonAccountAgentDist.AgentList" id="CommonAccountAgentDistAgentList">
<label for="CommonAccountAgentDistAgentList">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('CommonAccountAgentDist.AjaxUpdateAgent', $rights)): ?>checked="checked"<?php endif; ?> value="CommonAccountAgentDist.AjaxUpdateAgent" id="CommonAccountAgentDistAjaxUpdateAgent">
<label for="CommonAccountAgentDistAjaxUpdateAgent">更新代理</label>
<input type="checkbox" name="rights[]" <?php if (in_array('CommonAccountAgentDist.RemoveAgent', $rights)): ?>checked="checked"<?php endif; ?> value="CommonAccountAgentDist.RemoveAgent" id="CommonAccountAgentDistRemoveAgent">
<label for="CommonAccountAgentDistRemoveAgent">移除代理</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>代理帐户列表</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('CommonAccountAgentDist.AgentAccountList', $rights)): ?>checked="checked"<?php endif; ?> value="CommonAccountAgentDist.AgentAccountList" id="CommonAccountAgentDistAgentAccountList">
<label for="CommonAccountAgentDistAgentAccountList">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('CommonAccountAgentDist.Create', $rights)): ?>checked="checked"<?php endif; ?> value="CommonAccountAgentDist.Create" id="CommonAccountAgentDistCreate">
<label for="CommonAccountAgentDistCreate">分配金额</label>
<input type="checkbox" name="rights[]" <?php if (in_array('CommonAccountAgentDist.Dist', $rights)): ?>checked="checked"<?php endif; ?> value="CommonAccountAgentDist.Dist" id="CommonAccountAgentDistDist">
<label for="CommonAccountAgentDistDist">确认分配金额</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>代理帐户分配金额记录</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('CommonAccountAgentDist.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="CommonAccountAgentDist.Admin" id="CommonAccountAgentDistAdmin">
<label for="CommonAccountAgentDistAdmin">列表</label>
)