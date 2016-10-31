<?php
/* @var $this superGoodsController */
/* @var $model superGoods */

$this->breadcrumbs=array(
	Yii::t('partnerModule.storeGoods','门店商品管理'),
	Yii::t('partnerModule.storeGoods','门店商品列表'),
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
<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.storeGoods','商品列表'); ?></h3>
</div>

<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
<?php echo CHtml::link(Yii::t('partnerModule.storeGoods','添加商品'),
    $this->createAbsoluteUrl('/partner/storeGoods/add'),array('class'=>'mt15 btnSellerAdd')); ?>

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
	<tbody><tr>
		<th class="bgBlack" width="5%">
                <input type="checkbox" id="checkAll" /><label for="checkAll"><?php echo Yii::t('cashHistory', '全选'); ?></label>
        </th>
			<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.storeGoods','商品名称');?></th>
			<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.storeGoods','封面图片');?></th>
			<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.storeGoods','销售价');?></th>
			<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.storeGoods','库存');?></th>
			<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.storeGoods','冻结库存');?></th>
			<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.storeGoods','状态');?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.storeGoods','商品审核状态');?></th>
			<th class="bgBlack" width="25%"><?php echo Yii::t('partnerModule.storeGoods','操作');?></th>
		</tr>
		
		<?php foreach ($goods_data as $data):?>
		
		<tr class="even">
		
			<td class="ta_c"><?php echo CHtml::checkBox('CacheKey',false,array('value'=>$data->id,'data-status'=>$data->status,'data-goods-status'=>$data->goods->status)) ?></td>
			<td class="ta_c"><?php echo $data->goods->name;  ?></td>
			<td class="ta_c"><?php echo  CHtml::image(ATTR_DOMAIN.'/'.$data->goods->thumb,$data->goods->name,array('width'=>'100px','height'=>'80px')); ?></td>
			<td class="ta_c">￥<?php echo $data->goods->price;?></td>
			<td class="ta_c"><?php echo isset($stocks[$data->goods_id])?$stocks[$data->goods_id]['stock']: Yii::t('partnerModule.storeGoods','获取失败'); ?></td>
			<td class="ta_c"><?php echo isset($stocks[$data->goods_id])?$stocks[$data->goods_id]['frozenStock']: Yii::t('partnerModule.storeGoods','获取失败'); ?></td>
			<td class="ta_c"><?php echo SuperGoods::getStatus($data->status); ?></td>
                        <td class="ta_c"><?php echo Goods::getStatus($data->goods->status);?></td>
			<td class="ta_c">
				<a href="<?php echo Yii::app()->createUrl('/partner/goods/update/',array('id'=>$data->goods->id,'sid'=>$this->super_id,'returnUrl'=>$this->id));?>" ><?php echo Yii::t('partnerModule.storeGoods', '修改')?></a>
				
				| <a href="<?php echo Yii::app()->createUrl('/partner/storeGoods/stockIn/',array('id'=>$data->id));?>"  ><?php echo Yii::t('partnerModule.storeGoods', '进货')?></a>
				| <a href="<?php echo Yii::app()->createUrl('/partner/storeGoods/stockOut/',array('id'=>$data->id));?>" ><?php echo Yii::t('partnerModule.storeGoods', '出货')?></a>
				
				<?php if($data->status == SuperGoods::STATUS_ENABLE){?>
				| <a href="<?php echo Yii::app()->createUrl('/partner/storeGoods/disable/',array('id'=>$data->id));?>"><?php echo Yii::t('partnerModule.storeGoods', '下架')?></a>
				<?php } else {?>
				| <a href="<?php echo Yii::app()->createUrl('/partner/storeGoods/enable/',array('id'=>$data->id));?>"><?php echo Yii::t('partnerModule.storeGoods', '上架')?></a>
				<?php }?>
			</td>
		</tr>
		
		<?php endforeach;?>
		
		<tr>
			<td colspan=9>
				<?php echo CHtml::button(Yii::t('partnerGoods', '批量上架'), array('data-status' => 'toEnable', 'class' => 'mt15 btnSellerAdd toEnable')) ?>
			</td>
		
		</tr>
		
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


 <div style="display: none;" id="confirmArea">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="tab-come">
            <tbody>
            <tr>
                <th style="text-align: center" id="confimTitle" class="title-th even" colspan="2"></th>
            </tr>
            <tr>
                <td id="confirmDetail" colspan="2" class="odd">

                </td>
            </tr>

            </tbody>
        </table>
    </div>

<script type="text/javascript">
       $("#checkAll").click(function() {
            if (this.checked) {
                $(":input[name='CacheKey']").attr('checked', 'checked');
            } else {
                $(":input[name='CacheKey']").removeAttr('checked');
            }
        });
        //批量审核操作
        $("input.toEnable").click(function(){
            var ids = [];
            //var details = [];
            $(":input[name='CacheKey']:checked").each(function() {
                //筛选符合条件的
                if($(this).attr('data-goods-status')=='<?php echo Goods::STATUS_PASS ?>' && $(this).attr('data-status')!='<?php echo SuperGoods::STATUS_ENABLE ?>'){
                    ids.push($(this).val());
                    //details.push($(this).next().text());
                }
            });
            if (ids.length == 0) {
                art.dialog({
                    icon: 'error',
                    content: '<?php echo Yii::t('partnerGoods', '请选择未上架并已通过审核的商品 [非审核通过的商品不能上架]'); ?>',
                    lock: true
                });
            }else{
                var updateTitle = $(this).val();
                $("#confimTitle").html("<?php echo Yii::t('cashHistory', '确认操作'); ?> \"" + updateTitle + "\"？");
                //$("#confirmDetail").html(details.join("<br/>"));
                $("#confirmTR").hide();
                var content = $("#confirmArea").html();
                $("#confirmTR").show();
                art.dialog({
                    icon: 'question',
                    content: content,
                    lock: true,
                    cancel: true,
                    ok: function() {
                        $.ajax({
                            type: "POST",
                            url: "<?php echo $this->createUrl('storeGoods/multEnable') ?>",
                            data: {
                                idArr: ids.join(','),
                                YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>',
                            },
                            success: function(data) {

								var icons = 'succeed';

								if(data!='批量上架成功'){
									var icons = 'error';
								}
                                
                                art.dialog({
                                    icon: icons,
                                    content: data,
                                    ok:function(){
                                        location.reload();
                                    }
                                });

                            }
                        });
                    }
                })
            }
        });
</script>