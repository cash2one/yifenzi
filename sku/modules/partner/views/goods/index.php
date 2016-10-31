<?php
/* @var $this superGoodsController */
/* @var $model superGoods */

$this->breadcrumbs = array(
    Yii::t('partnerModule.superGoods', '商品管理'),
    Yii::t('partnerModule.superGoods', '商品列表'),
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
    <h3><?php echo Yii::t('partnerModule.superGoods', '商品列表'); ?></h3>
</div>

<?php
$this->renderPartial('_search', array(
    'model' => $model,
));
?>
<br>
<?php echo CHtml::link('<span>'. Yii::t('partnerModule.superGoods','添加商品').'</span>', $this->createAbsoluteUrl('/partner/goods/create'), array('class' => 'sellerBtn03 submitBt'));?>



<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
    	<tr>
            <th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.superGoods', '商品名称'); ?></th>
            <th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.superGoods', '本店分类'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.superGoods', '封面图片'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.superGoods', '销售价'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.superGoods', '状态'); ?></th>
            <th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.superGoods', '操作'); ?></th>
        </tr>
<?php if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==2) {
	var_dump($goods_data);
}?>
        
<?php foreach ($goods_data as $data): ?>
<?php if (($data['name'])):?>
            <tr class="even">
                <td class="ta_c"><?php echo $data->name; ?></td>
                <td class="ta_c"><?php echo $data->goodsCategory?$data->goodsCategory->name:Yii::t('partnerModule.superGoods', '未知分类'); ?></td>
                <td class="ta_c"><?php echo CHtml::image(ATTR_DOMAIN .'/' . $data->thumb, $data->name, array('width' => '120','height'=>'100px')); ?></td>
                <td class="ta_c">￥<?php echo $data->price; ?></td>
                <td class="ta_c"><?php echo Goods::getStatus($data->status); ?></td>

                <td class="ta_c">
                    <a href="<?php echo Yii::app()->createUrl('/partner/goods/update/', array('id' => $data->id)); ?>"><?php echo Yii::t('partnerModule.superGoods', '修改') ?></a>
                </td>
            </tr>
<?php endif;?>
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
