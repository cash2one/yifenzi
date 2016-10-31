<?php
// 切换超市门店视图
$this->breadcrumbs = array(
    Yii::t('partnerModule.store', '切换超市门店') => array('/partner/store/change')
);
?>
<div class="toolbar">
    <b><?php echo Yii::t('partnerModule.store', '切换超市门店'); ?></b>
    <span><span class="red"><?php echo Yii::t('partnerModule.store', '当前超市门店：'); ?><?php echo $curr_super['name'] ?></span></span>
</div>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody><tr>
            <th width="40%" class="bgBlack"><?php echo Yii::t('partnerModule.store', '名称'); ?></th>
            <th width="20%" class="bgBlack"><?php echo Yii::t('partnerModule.store', '类型'); ?></th>
            <th width="10%" class="bgBlack"><?php echo Yii::t('partnerModule.store', '电话'); ?></th>
            <th width="10%" class="bgBlack"><?php echo Yii::t('partnerModule.store', '状态'); ?></th>
            <th width="20%" class="bgBlack"><?php echo Yii::t('partnerModule.store', '操作'); ?></th>
        </tr>

        <?php $i = 1; ?>
        <?php foreach ($supers as $super): ?>

            <tr <?php if ($i % 2 == 0): ?>class="even"<?php endif; ?>>
                <td class="ta_c"><?php echo $super['name']; ?></td>
                <td class="ta_c"><?php echo StoreCategory::getCategoryName($super['category_id']); ?></td>
                <td class="ta_c"><?php echo $super['mobile']; ?></td>
                <td class="ta_c"><?php echo Supermarkets::getStatus($super['status']); ?></td>
                <td class="ta_c">
                    <?php if ($super['id'] == $curr_super['id']): ?>
                        <b class="red"><?php echo Yii::t('partnerModule.store', '当前选定超市门店'); ?></b>								
                    <?php else: ?>
                        <a href="<?php echo Yii::app()->createUrl('/partner/store/change', array('super_id' => $super['id'])); ?>" class="sellerBtn03" <?php if ($super['status'] == Supermarkets::STATUS_APPLY || $super['status'] == Supermarkets::STATUS_DISABLE): ?>onclick="return confirm('<?php echo Yii::t('partnerModule.store','当前门店未审核通过或者禁用状态，确定切换？');?>')"<?php else: ?>onclick="return confirm('确定切换门店吗？')"<?php endif; ?>><span><?php echo Yii::t('partnerModule.store', '切换'); ?></span></a>
                            <?php endif; ?>
                </td>
            </tr>
            <?php $i++; ?>
        <?php endforeach; ?>

    </tbody>
</table>
<div class="page_bottom clearfix">
	<div class="pagination">
		<?php
		  $this->widget('CLinkPager',array(   //此处Yii内置的是CLinkPager，我继承了CLinkPager并重写了相关方法
		    'header'=>'',
		    'prevPageLabel' => Yii::t('partnerModule.page', '上一页'),
		    'nextPageLabel' => Yii::t('partnerModule.page', '下一页'),
		    'pages' => $pager,       
		    'htmlOptions'=>array(
		       'class'=>'paging',   //包含分页链接的div的class
		     )
		  )
		  );
		?>
	</div>
</div>

