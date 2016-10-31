<?php
/* @var $this superStaffsController */
/* @var $model superStaffs */

$this->breadcrumbs=array(
	Yii::t('partnerModule.storeStaffs','员工管理'),
	Yii::t('partnerModule.storeStaffs','员工列表'),
);
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#superStaffs-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");

?>
<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.storeStaffs','员工列表'); ?></h3>
</div>

<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
<?php echo CHtml::link(Yii::t('partnerModule.storeStaffs','添加员工'),
    $this->createAbsoluteUrl('/partner/storeStaffs/add'),array('class'=>'mt15 btnSellerAdd')); ?>

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
	<tbody><tr>
			<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.storeStaffs','员工名称');?></th>
			<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.storeStaffs','头像');?></th>
                                                     <th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.storeStaffs','电话');?></th>
			<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.storeStaffs','状态');?></th>
			<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.storeStaffs','操作');?></th>
		</tr>
		
		<?php foreach ($datas as $data):?>
		
		<tr class="even">
			<td class="ta_c"><?php echo $data->name;  ?></td>
			<td class="ta_c"><?php echo  CHtml::image(ATTR_DOMAIN.'/'.$data->head,$data->name,array('width'=>'80px','height'=>'100px')); ?></td>
			<td class="ta_c"><?php echo $data->mobile; ?></td>
			<td class="ta_c"><?php echo SuperStaffs::getStatus($data->status); ?></td>
			<td class="ta_c" >
				<a href="<?php echo Yii::app()->createUrl('/partner/storeStaffs/update/',array('id'=>$data->id));?>" ><?php echo Yii::t('partnerModule.storeStaffs', '修改')?></a>
				
				<?php if($data->status == SuperStaffs::STATUS_ENABLE){?>
				| <a href="<?php echo Yii::app()->createUrl('/partner/storeStaffs/disable/',array('id'=>$data->id));?>"><?php echo Yii::t('partnerModule.storeStaffs', '禁用')?></a>
				<?php } else {?>
				| <a href="<?php echo Yii::app()->createUrl('/partner/storeStaffs/enable/',array('id'=>$data->id));?>"><?php echo Yii::t('partnerModule.storeStaffs', '启用')?></a>
				<?php }?>
			</td>
		</tr>
		
		<?php endforeach;?>
		
</tbody></table>
					
					
<div class="page_bottom clearfix">
	<div class="pagination">
		<?php
		  $this->widget('CLinkPager',array(   //此处Yii内置的是CLinkPager，我继承了CLinkPager并重写了相关方法
		    'header'=>'',
		    'prevPageLabel' => Yii::t('partnerModule.page', '上一页'),
		    'nextPageLabel' => Yii::t('partnerModule.page', '下一页'),
		    'pages' => $pager,       
		    'maxButtonCount'=>10,    //分页数目
		    'htmlOptions'=>array(
		       'class'=>'paging',   //包含分页链接的div的class
		     )
		  )
		  );
		?>
	</div>
</div>
