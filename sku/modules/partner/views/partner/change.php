<?php
// 切换当前商家视图
$this->breadcrumbs = array(
    Yii::t('partnerModule.partner', '切换当前商家') => array('/partner/store/change')
);
?>
<div class="toolbar">
    <b><?php echo Yii::t('partnerModule.partner', '切换当前管理商家'); ?></b>
    <span><span class="red"><?php echo Yii::t('partnerModule.partner', '当前管理商家：'); ?><?php echo $curr_partner['gai_number'] ?></span></span>
</div>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody><tr>
            <th width="40%" class="bgBlack"><?php echo Yii::t('partnerModule.partner', '商家GW号'); ?></th>
            <th width="20%" class="bgBlack"><?php echo Yii::t('partnerModule.partner', '操作'); ?></th>
        </tr>

        <?php $i = 1; ?>
        <?php foreach ($partners as $partner): ?>

            <tr <?php if ($i % 2 == 0): ?>class="even"<?php endif; ?>>
                <td class="ta_c"><?php echo $partner['gai_number']; ?><?php if($this->partnerInfo['member_id']==$partner['member_id']):?>(自己)<?php endif;?></td>
                <td class="ta_c">
                    <?php if ($partner['member_id'] == $this->curr_act_member_id): ?>
                        <b class="red"><?php echo Yii::t('partnerModule.partner', '当前选定商家'); ?></b>								
                    <?php else: ?>
                        <a href="<?php echo Yii::app()->createUrl('/partner/partner/operChange', array('mid' => $partner['member_id'])); ?>" class="sellerBtn03" ><span><?php echo Yii::t('partnerModule.partner', '切换'); ?></span></a>
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

