<?php
/* @var $this machineGoodsController */
/* @var $model machineGoods */

$this->breadcrumbs=array(
                 Yii::t('partnerModule.machine','返回盖网售货机列表')=>array('list'),
	Yii::t('partnerModule.machine','售货机商品列表'),
);
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#machineGoods-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");

?>
<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.machine','商品列表'); ?></h3>   
</div>

<?php $this->renderPartial('_goods_search',array(
	'model'=>$model,
	'mid'=>$m_model->id,
)); ?>
<?php echo CHtml::link(Yii::t('partnerModule.machine','添加商品'),
    $this->createAbsoluteUrl('/partner/machine/goodsAdd',array('mid'=>$m_model->id)),array('class'=>'mt15 btnSellerAdd')); ?>
 <a href="list" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.machine','返回'); ?></span></a>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
	<tbody><tr>
			<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.machine','商品名称');?></th>
			<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','封面图片');?></th>
			<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','销售价');?></th>
			<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','库存');?></th>
			<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','冻结库存');?></th>
			<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','状态');?></th>
                                                     <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','商品审核状态');?></th>
			<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.machine','操作');?></th>
		</tr>
		
		<?php foreach ($goods_data as $data):?>
		
		<tr class="even">
			<td class="ta_c"><?php echo $data->goods->name;  ?></td>
			<td class="ta_c"><?php echo  CHtml::image(ATTR_DOMAIN.'/'.$data->goods->thumb,$data->goods->name,array('width'=>'85px')); ?></td>
			<td class="ta_c">￥<?php echo $data->goods->price;?></td>
			<td class="ta_c"><?php echo isset($stocks[$data->goods_id])?$stocks[$data->goods_id]['stock']: Yii::t('partnerModule.machine','获取失败'); ?></td>
			<td class="ta_c"><?php echo isset($stocks[$data->goods_id])?$stocks[$data->goods_id]['frozenStock']: Yii::t('partnerModule.machine','获取失败'); ?></td>
                                                    <td class="ta_c"><?php echo VendingMachineGoods::getStatus($data->status); ?></td>
                                                    <td class="ta_c"><?php echo Goods::getStatus($data->goods->status);?></td>
			<td class="ta_c">
				<a href="<?php echo Yii::app()->createUrl('/partner/machine/goodsEdit/',array('id'=>$data->goods->id,'mid'=>$data->machine_id));?>"><?php echo Yii::t('partnerModule.machine', '修改货道')?></a>

				<?php if($data->status == SuperGoods::STATUS_ENABLE){?>
				| <a href="<?php echo Yii::app()->createUrl('/partner/machine/goodsDisable/',array('id'=>$data->id,'mid'=>$mid));?>"><?php echo Yii::t('partnerModule.machine', '下架')?></a>
				<?php } else {?>
				| <a href="<?php echo Yii::app()->createUrl('/partner/machine/goodsEnable/',array('id'=>$data->id,'mid'=>$mid));?>"><?php echo Yii::t('partnerModule.machine', '上架')?></a>
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
