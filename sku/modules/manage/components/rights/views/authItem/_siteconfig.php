<label>网站配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.SiteConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.SiteConfig" id="HomeSiteConfig">
<label for="HomeSiteConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>SEO配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.SeoConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.SeoConfig" id="HomeSeoConfig">
<label for="HomeSeoConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>短信接口配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.SmsApiConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.SmsApiConfig" id="HomeSmsApiConfig">
<label for="HomeSmsApiConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>短信模板配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.SmsModelConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.SmsModelConfig" id="HomeSmsModelConfig">
<label for="HomeSmsModelConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>文件上传配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.UploadConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.UploadConfig" id="HomeUploadConfig">
<label for="HomeUploadConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>系统信息</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.Main', $rights)): ?>checked="checked"<?php endif; ?> value="Home.Main" id="HomeMain">
<label for="HomeMain">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>敏感词设置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.FilterWorldConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.FilterWorldConfig" id="HomeFilterWorldConfig">
<label for="HomeFilterWorldConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>地址配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Region.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="Region.Admin" id="RegionAdmin">
<label for="RegionAdmin">列表</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Region.Delete', $rights)): ?>checked="checked"<?php endif; ?> value="Region.Delete" id="RegionDelete">
<label for="RegionDelete">删除</label>
<input type="checkbox" name="rights[]" <?php if (in_array('Region.Update', $rights)): ?>checked="checked"<?php endif; ?> value="Region.Update" id="RegionUpdate">
<label for="RegionUpdate">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>会员升级配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.ScheduleConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.ScheduleConfig" id="HomeScheduleConfig">
<label for="HomeScheduleConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>系统任务管理</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.TaskConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.TaskConfig" id="HomeTaskConfig">
<label for="HomeTaskConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>运费修改客服配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.FreightLinkConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.FreightLinkConfig" id="HomeFreightLinkConfig">
<label for="HomeFreightLinkConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>全局搜索热门词配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.GlobalKeyWordConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.GlobalKeyWordConfig" id="HomeGlobalKeyWordConfig">
<label for="HomeGlobalKeyWordConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>短信发送记录</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('SmsLog.Admin', $rights)): ?>checked="checked"<?php endif; ?> value="SmsLog.Admin" id="SmsLogAdmin">
<label for="SmsLogAdmin">列表</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>发送邮件设置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.EmailConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.EmailConfig" id="HomeEmailConfig">
<label for="HomeEmailConfig">编辑</label>
)
&nbsp;&nbsp;
&nbsp;&nbsp;
<label>汇率配置</label>
(
<input type="checkbox" name="rights[]" <?php if (in_array('Home.RateConfig', $rights)): ?>checked="checked"<?php endif; ?> value="Home.RateConfig" id="HomeRateConfig">
<label for="HomeRateConfig">编辑</label>
)