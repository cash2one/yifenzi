<?php
/* @var $this machineGoodsController */
/* @var $model machineGoods */

$this->breadcrumbs=array(
    Yii::t('partnerModule.machine','返回盖网售货机列表')=>array('list'),
    Yii::t('partnerModule.machine','格子铺商品列表'),
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
    <h3><?php echo Yii::t('partnerModule.machine','格子铺商品列表'); ?></h3>
</div>
<!---->
<?php $this->renderPartial('_cellStore_goods_search',array(
    'model'=>$model,
    'mid'=>$mid,
)); ?>
<?php echo CHtml::link(Yii::t('partnerModule.machine','添加格子铺'),
    $this->createAbsoluteUrl('/partner/machine/machineCellStoreAdd',array('mid'=>$mid)),array('class'=>'mt15 btnSellerAdd')); ?>
<a href="/machine/list" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.machine','返回'); ?></span></a>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody><tr>
        <th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.machine','商品名称');?></th>
        <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','封面图片');?></th>
        <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','销售价');?></th>
        <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','格子铺编码');?></th>
        <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','状态');?></th>
        <th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.machine','操作');?></th>
    </tr>
<?php if(is_array($list)){ ?>
    <?php foreach ($list as $val):?>

        <tr class="even">
            <td class="ta_c"><?php echo $val['goods']['name'];  ?></td>
            <td class="ta_c"><?php echo  CHtml::image(ATTR_DOMAIN.'/'.$val['goods']['thumb'],$val['goods']['name'],array('width'=>'85px')); ?></td>
            <td class="ta_c">￥<?php echo $val['goods']['price'];?></td>
            <td class="ta_c"><?php echo $val['code'];?></td>

            <td class="ta_c"><?php echo VendingMachineGoods::getStatus($val['status']); ?></td>

            <td class="ta_c">
                <a href="<?php echo Yii::app()->createUrl('/partner/machine/machineCellStoreEdit/',array('id'=>$val['id'],'mid'=>$val['machine_id']));?>"><?php echo Yii::t('partnerModule.machine', '修改格子铺')?></a>

                <?php if($val['status'] == VendingMachineCellStore::STATUS_ENABLE){?>
                    | <a href="<?php echo Yii::app()->createUrl('/partner/machine/goodsShelves/',array('id'=>$val['id'],'mid'=>$mid));?>"><?php echo Yii::t('partnerModule.machine', '下架')?></a>
                <?php } else {?>
                    | <a href="<?php echo Yii::app()->createUrl('/partner/machine/goodsAdded/',array('id'=>$val['id'],'mid'=>$mid));?>"><?php echo Yii::t('partnerModule.machine', '上架')?></a>
                <?php }?>
                <!-- 
                | <a href="<?php echo Yii::app()->createUrl('/partner/machine/machineCellStoreDel',array('id'=>$val['id'],'mid'=>$mid));?>"><?php echo Yii::t('partnerModule.machine', '删除')?></a>
                 -->
            </td>
        </tr>

    <?php endforeach;?>
    <?php }?>
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

<script type='text/javascript'>
//    function delGoods(){
//        if(confirm("确定要删除？")){
//            location.href = <?php //echo Yii::app()->createUrl('/partner/machine/machineCellStoreDel',array('id'=>$val['id'],'mid'=>$mid));?>
//
//        }
//    }
    //关闭订单
    $(".btnSellerAdd").click(function() {
        var code = $(this).attr("data-code");
        var url = '<?php echo Yii::app()->createUrl('/partner/machine/checkGoodsNum') ?>';
        var id = <?php echo $mid; ?>;
        $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: url,
                    data: {code: code, YII_CSRF_TOKEN: '<?php echo Yii::app()->request->csrfToken ?>', mid: id},
                    success: function(data) {
                        if (data.success) {
                            location.href = <?php echo $this->createAbsoluteUrl('/partner/machine/machineCellStoreAdd',array('mid'=>$mid));?>
                        } else {
                            alert(data.error);
                        }
                    }
                });


        return false;
    });


</script>

