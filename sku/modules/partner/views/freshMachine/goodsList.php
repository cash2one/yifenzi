<?php
/* @var $this FreshMachineController */
/* @var $model FreshMachineGoods */

$this->breadcrumbs = array(
    Yii::t('partnerModule.freshMachine', '返回盖网生鲜机列表') => array('list'),
    Yii::t('partnerModule.freshMachine', '生鲜机商品列表'),
);
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#FreshMachine-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.freshMachine', '商品列表('.$m_model->name.')'); ?></h3>
</div>

<?php $this->renderPartial('_search',array(
    'model'=>$model,
    'mid' => $m_model->id
)); ?>
<?php echo CHtml::link(Yii::t('partnerModule.freshMachine', '添加商品'), $this->createAbsoluteUrl('/partner/freshMachine/goodsAdd', array('mid' => $m_model->id)), array('class' => 'mt15 btnSellerAdd','style' => 'margin-right:10px;'));
?>
<?php echo CHtml::link(Yii::t('partnerModule.freshMachine', '全部'), $this->createAbsoluteUrl('/partner/freshMachine/freshGoods', array('mid' => $m_model->id)), array('class' => 'mt15 btnSellerAdd','style' => 'margin-right:10px;'));
?>
<?php echo CHtml::link(Yii::t('partnerModule.freshMachine', '上架'), $this->createAbsoluteUrl('/partner/freshMachine/freshGoods', array('mid' => $m_model->id,'status' => 1)), array('class' => 'mt15 btnSellerAdd','style' => 'margin-right:10px;'));
?>
<?php echo CHtml::link(Yii::t('partnerModule.freshMachine', '下架'), $this->createAbsoluteUrl('/partner/freshMachine/freshGoods', array('mid' => $m_model->id,'status' => 2)), array('class' => 'mt15 btnSellerAdd'));
?>
 <a href="list" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.freshMachine','返回'); ?></span></a>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody><tr>
    
    	<th class="bgBlack" width="5%">
                <input type="checkbox" id="checkAll" /><label for="checkAll"><?php echo Yii::t('cashHistory', '全选'); ?></label>
        </th>
    
            <th class="bgBlack" width="7%"><?php echo Yii::t('partnerModule.freshMachine', '商品名称'); ?></th>
            <th class="bgBlack" width="7%"><?php echo Yii::t('partnerModule.freshMachine', '封面图片'); ?></th>
            <th class="bgBlack" width="7%"><?php echo Yii::t('partnerModule.freshMachine', '条形码'); ?></th>
            <th class="bgBlack" width="7%"><?php echo Yii::t('partnerModule.freshMachine', '销售价'); ?></th>
            <th class="bgBlack" width="7%"><?php echo Yii::t('partnerModule.freshMachine', '重量'); ?></th>
            <th class="bgBlack" width="7%"><?php echo Yii::t('partnerModule.freshMachine', '规格'); ?></th>
            <th class="bgBlack" width="7%"><?php echo Yii::t('partnerModule.freshMachine', '产地'); ?></th>
            <th class="bgBlack" width="7%"><?php echo Yii::t('partnerModule.freshMachine', '有效期'); ?></th>
            <th class="bgBlack" width="5%"><?php echo Yii::t('partnerModule.freshMachine', '货道'); ?></th>
            <th class="bgBlack" width="5%"><?php echo Yii::t('partnerModule.freshMachine', '占用库存'); ?></th>
            <th class="bgBlack" width="5%"><?php echo Yii::t('partnerModule.freshMachine', '可用库存'); ?></th>
            <th class="bgBlack" width="5%"><?php echo Yii::t('partnerModule.freshMachine', '状态'); ?> </th>
            <th class="bgBlack" width="7%"><?php echo Yii::t('partnerModule.freshMachine', '商品审核状态'); ?></th>
            <th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.freshMachine', '操作'); ?></th>
        </tr>

        <?php foreach ($goods_data as $data): ?>

            <tr class="even">
           		<td class="ta_c"><?php echo CHtml::checkBox('CacheKey',false,array('value'=>$data->id,'data-status'=>$data->status,'data-goods-status'=>$data->goods->status)) ?></td>
                <td class="ta_c"><?php echo $data->goods->name; ?></td>
                <td class="ta_c"><?php echo CHtml::image(ATTR_DOMAIN . '/' . $data->goods->thumb, $data->goods->name, array('width' => '120px','height'=>'100px')); ?></td>
                <td class="ta_c"><?php echo $data->goods->barcode; ?></td>
                <td class="ta_c">￥<?php echo $data->goods->price; ?></td>
                <td class="ta_c"><?php echo $data->weight; ?>g</td>
                <td class="ta_c"><?php echo $data->specifications; ?></td>
                <td class="ta_c"><?php echo $data->goods_address; ?></td>
                <td class="ta_c"><?php echo $data->expr_time > 0 ? date('Y-m-d H:i:s',$data->expr_time) : '0000-00-00 00:00:00'; ?></td>
                <td class="ta_c">  <?php  if ($data->status==FreshMachineGoods::STATUS_ENABLE) {
                    if($data->status == FreshMachineGoods::STATUS_ENABLE && isset($stocks[$data->line_id]) && $stocks[$data->line_id]['stock']*1==0){
                	echo '<b class="red">'.$data->lines->name;
                    }else{
                        echo $data->lines->name;
                    }
                }elseif(!empty($data->lines->expir_time) && $data->lines->expir_time< time()) {echo $data->lines->name.'<b class="red">'.Yii::t('partnerModule.freshMachine','已失效') ;}elseif($data->lines->status == FreshMachineLine::STATUS_ENABLE){ echo $data->lines->name;}elseif($data->lines->status == FreshMachineLine::STATUS_DISABLE){ echo $data->lines->name.' <b class="red">'.Yii::t('partnerModule.freshMachine','已禁用');}elseif($data->lines->status == FreshMachineLine::STATUS_ENABLE){ echo $data->lines->name;}elseif($data->lines->status == FreshMachineLine::STATUS_EMPLOY){ echo $data->lines->name.' <b class="red">'.Yii::t('partnerModule.freshMachine','已占用');}else{echo $data->lines->name.' <b class="red">'.Yii::t('partnerModule.freshMachine','未上架不占用货道');}?></td>
                <td class="ta_c"><?php echo $data->status == FreshMachineGoods::STATUS_ENABLE?(isset($stocks[$data->line_id]) ? $stocks[$data->line_id]['frozenStock']*1 : Yii::t('partnerModule.freshMachine', '获取失败')):'-'; ?></td>
                <td class="ta_c"><?php echo $data->status == FreshMachineGoods::STATUS_ENABLE?(isset($stocks[$data->line_id]) ? $stocks[$data->line_id]['stock']*1==0?'<b class="red">'.$stocks[$data->line_id]['stock']*1:$stocks[$data->line_id]['stock']*1: Yii::t('partnerModule.freshMachine', '获取失败')):'-'; ?></td>

                <td class="ta_c"><?php echo FreshMachineGoods::getStatus($data->status);?></td>
                <td class="ta_c"><?php echo Goods::getStatus($data->goods->status); ?></td>
                <td class="ta_c">
                    <a href="<?php echo Yii::app()->createUrl('/partner/freshMachine/goodsEdit/', array('id' => $data->goods->id, 'mid' => $data->machine_id,'line_id'=>$data->lines->id,'gid'=>$data->id)); ?>"><?php echo Yii::t('partnerModule.freshMachine', '修改') ?></a>

                    <?php if ($data->status == FreshMachineGoods::STATUS_ENABLE) { ?>
                        | <a href="<?php echo Yii::app()->createUrl('/partner/freshMachine/goodsDisable/', array('id' => $data->id, 'mid' => $mid)); ?>"><?php echo Yii::t('partnerModule.freshMachine', '下架') ?></a>

                        | <a href="<?php echo Yii::app()->createUrl('/partner/freshMachine/goodsStockIn/', array('id' => $data->id, 'mid' => $mid)); ?>"><?php echo Yii::t('partnerModule.freshMachine', '进货') ?></a>

                        | <a href="<?php echo Yii::app()->createUrl('/partner/freshMachine/goodsStockOut/', array('id' => $data->id, 'mid' => $mid)); ?>"><?php echo Yii::t('partnerModule.freshMachine', '出货') ?></a>

                    <?php } else { ?>
                        | <a href="<?php echo Yii::app()->createUrl('/partner/freshMachine/goodsEnable/', array('id' => $data->id, 'mid' => $mid)); ?>"><?php echo Yii::t('partnerModule.freshMachine', '上架') ?></a>
                    <?php } ?>
                </td>
            </tr>

        <?php endforeach; ?>
        
        
        <tr>
			<td colspan=15>
				<?php echo CHtml::button(Yii::t('partnerGoods', '批量上架'), array('data-status' => 'toEnable', 'class' => 'mt15 btnSellerAdd toEnable')) ?>
			</td>
		
		</tr>
		

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
       //上下架筛选
       function pushList(){
           var html = '<a href="#">上架</a><a href="#">下架</a>';
           $('.iconfont').append(html);

       }
        //批量审核操作
        $("input.toEnable").click(function(){
            var ids = [];
            //var details = [];
            $(":input[name='CacheKey']:checked").each(function() {
                //筛选符合条件的
                if($(this).attr('data-goods-status')=='<?php echo Goods::STATUS_PASS ?>' && $(this).attr('data-status')!='<?php echo FreshMachineGoods::STATUS_ENABLE ?>'){
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
                $("#confimTitle").html("<?php echo Yii::t('partnerGoods', '确认操作'); ?> \"" + updateTitle + "\"？");
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
                            url: "<?php echo $this->createUrl('freshMachine/multEnableGoods') ?>",
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

