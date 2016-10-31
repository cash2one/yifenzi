<?php
// 切换加盟商视图
$this->breadcrumbs = array(
    Yii::t('partnerModule.machine', '售货机管理'),
    Yii::t('partnerModule.machine', '盖网售货机列表')
);
?>
<div class="toolbar">
	<b> <?php echo Yii::t('partnerModule.machine','盖网售货机列表');?></b>
	<span><?php echo Yii::t('partnerModule.machine','盖网售货机使用数量及位置。');?></span>
</div>

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
						<tbody><tr>
								<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.machine','名称');?></th>
								<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','机器编号');?></th>
								<th class="bgBlack" width="5%"><?php echo Yii::t('partnerModule.machine','类型');?></th>
								<th class="bgBlack" width="5%"><?php echo Yii::t('partnerModule.machine','状态');?></th>
								<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.machine','所在地区');?></th>
								<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.machine','操作');?></th>
							</tr>
							
							<?php foreach ($machine_data as $data):?>
							
							<tr class="even">
								<td class="ta_c"><?php echo $data->name;  ?></td>
								<td class="ta_c"><?php echo $data->code;  ?></td>
								<td class="ta_c"><?php echo StoreCategory::getCategoryName($data['category_id']);?></td>
								<td class="ta_c"><b class="red"><?php echo VendingMachine::getStatus($data->status);?></b></td>
								<td class="ta_c"><?php echo Region::getName($data->province_id,$data->city_id,$data->district_id)?></td>
								<td class="ta_c">
								
									<a href="<?php echo Yii::app()->createUrl('/partner/machine/machineGoodsList/',array('mid'=>$data->id));?>"><?php echo Yii::t('partnerModule.machine', '商品管理')?></a>
									| <a href="<?php echo Yii::app()->createUrl('/partner/machine/machineUpdate/',array('id'=>$data->id));?>"><?php echo Yii::t('partnerModule.machine', '售货机管理')?></a>
                                    | <a href="<?php echo Yii::app()->createUrl('/partner/machine/machineCellStore/',array('id'=>$data->id));?>"><?php echo Yii::t('partnerModule.machine', '格子铺管理')?></a>
									<!-- 
									<?php if ($data->status==1):?>
									<a href="<?php echo Yii::app()->createUrl('/partner/machine/stop/',array('id'=>$data->id));?>" class="sellerBtn03"   ><span>停用</span></a>
									<?php else:?>
									<a href="<?php echo Yii::app()->createUrl('/partner/machine/run/',array('id'=>$data->id));?>" class="sellerBtn03"   ><span>启用</span></a>
									<?php endif;?>
								 	-->
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
		    'maxButtonCount'=>15,    //分页数目
		    'htmlOptions'=>array(
		       'class'=>'paging',   //包含分页链接的div的class
		     )
		  )
		  );
		?>
	</div>
</div>