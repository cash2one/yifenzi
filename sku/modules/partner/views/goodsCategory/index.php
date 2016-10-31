<?php
/* @var $this GoodsCategoryController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
    Yii::t('partnerModule.goodsCategory','商品管理'),
    Yii::t('partnerModule.goodsCategory','分类列表'),
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
    <h3><?php echo Yii::t('partnerModule.goodsCategory','分类列表'); ?></h3>
</div>
<br>
<?php echo CHtml::link( '<span>'. Yii::t('partnerModule.goodsCategory','添加分类').'</span>',
    $this->createAbsoluteUrl('/partner/goodsCategory/create'),array('class'=>'sellerBtn03 submitBt')); ?>

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody><tr>
         <th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.goodsCategory','排序');?></th>
        <th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.goodsCategory','分类名称');?></th>
        <th class="bgBlack" width="25%"><?php echo Yii::t('partnerModule.goodsCategory','操作');?></th>
    </tr>

    <?php foreach ($goods_data as $data):?>

        <tr class="even">
            <td class="ta_c"><?php echo $data->sort;  ?></td>
            <td class="ta_c"><?php echo $data->name;  ?></td>
            <td class="ta_c">
                <a href="<?php echo Yii::app()->createUrl('/partner/goodsCategory/update/',array('id'=>$data->id));?>"><?php echo Yii::t('partnerModule.superGoods', '修改')?></a>
                <a href="<?php echo Yii::app()->createUrl('/partner/goodsCategory/delete/',array('id'=>$data->id));?>"><?php echo Yii::t('partnerModule.superGoods', '删除')?></a>
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
