<div class="border-info clearfix search-form">
<table class="searchTable">
    <tr>
		<td>
		<span>待绑定用户数量：<font size="3px" color="red"><?php echo MemberBind::GetNotBindMem();?>   </font></span>
		</td>
		<td></td>
		<td>
		<?php if (Yii::app()->user->checkAccess('Manage.MemberBind.CreateBind') ):?>
		<input id="Btn_Add" type="button" value="<?php echo Yii::t('MemberBind', '手动绑定'); ?>" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl("memberBind/createBind"); ?>'" />
		<?php endif;?>
		</td>
   </tr>
   
</table>
</div>
