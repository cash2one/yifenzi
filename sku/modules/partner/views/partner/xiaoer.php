<?php
/* @var $this PartnerController */
/* @var $model Xiaoer */

$this->breadcrumbs = array(
    Yii::t('partnerModule.superGoods', '店小二管理'),
    Yii::t('partnerModule.superGoods', '店小二列表'),
);
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#superGoods-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>


<br>
<?php echo CHtml::link('<span>'. Yii::t('partnerModule.superGoods','添加店小二').'</span>', $this->createAbsoluteUrl('/partner/partner/createXiao'), array('class' => 'sellerBtn03 submitBt'));?>



<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
    	<tr>
            <th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.superGoods', '盖网号'); ?></th>           
            <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.superGoods', '姓名'); ?></th>
             <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.superGoods', '手机号'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.superGoods', '状态'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.superGoods', '操作'); ?></th>
        </tr>
<?php foreach ($data as $v): ?>
            <tr class="even">
                <td class="ta_c"><?php echo isset($v->member)?$v->member->gai_number:'-'  ?></td>
                <td class="ta_c"><?php echo  isset($v->member)?(!empty($v->member->real_name)?$v->member->real_name:$v->member->username):'-' ?></td>
                <td class="ta_c"><?php echo isset($v->member)?$v->member->mobile:'-'  ?></td>
                <td class="ta_c"><?php echo Xiaoer::getStatus($v->status); ?></td>
                <td class="ta_c">
                    <a href="<?php echo Yii::app()->createUrl('/partner/partner/updateXiao/', array('id' => $v->id)); ?>"><?php echo Yii::t('partnerModule.superGoods', '修改') ?></a>
                    <a href="<?php echo Yii::app()->createUrl('/partner/partner/delete/', array('id' => $v->id)); ?>"><?php echo Yii::t('partnerModule.superGoods', '删除') ?></a>
                </td>
            </tr>

<?php endforeach; ?>
        


    </tbody></table>


<div class="page_bottom clearfix">
<div class="pagination">
        <?php
        $this->widget('CLinkPager', array(//此处Yii内置的是CLinkPager，我继承了CLinkPager并重写了相关方法
            'header' => '',
            'prevPageLabel' => Yii::t('partnerModule.page', '上一页'),
            'nextPageLabel' => Yii::t('partnerModule.page', '下一页'),
            'pages' => $pager,
            'maxButtonCount' => 10, //分页数目
            'htmlOptions' => array(
                'class' => 'paging', //包含分页链接的div的class
            )
                )
        );
        ?>
    </div>
</div>
