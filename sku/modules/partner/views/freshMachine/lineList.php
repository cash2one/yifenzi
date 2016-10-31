<?php
/* @var $this FreshMachineController */
/* @var $model FreshMachineGoods */

$this->breadcrumbs = array(
    Yii::t('partnerModule.freshMachine', '返回盖网生鲜机列表') => array('list'),
    Yii::t('partnerModule.freshMachine', '生鲜机货道列表'),
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
    <h3><?php echo Yii::t('partnerModule.freshMachine', '货道列表('.$m_model->name.')'); ?></h3>
</div>
<?php echo CHtml::link(Yii::t('partnerModule.freshMachine', '添加货道'), $this->createAbsoluteUrl('/partner/freshMachine/lineAdd', array('mid' => $m_model->id)), array('class' => 'mt15 btnSellerAdd','style'=>'margin-right:10px;'));
?>
<?php echo CHtml::link(Yii::t('partnerModule.freshMachine', '左柜'), $this->createAbsoluteUrl('/partner/freshMachine/freshLine', array('mid' => $m_model->id,'name'=>'L')), array('class' => 'mt15 btnSellerAdd'));
?>
<?php echo CHtml::link(Yii::t('partnerModule.freshMachine', '右柜'), $this->createAbsoluteUrl('/partner/freshMachine/freshLine', array('mid' => $m_model->id,'name' => 'R')), array('class' => 'mt15 btnSellerAdd','style'=>'margin-left:20px;margin-right:10px;'));
?>
 <a href="list" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.freshMachine','返回'); ?></span></a>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody><tr>
            <th class="bgBlack" ><?php echo Yii::t('partnerModule.freshMachine', '货道名称'); ?></th>
            <th class="bgBlack" ><?php echo Yii::t('partnerModule.freshMachine', '货道编码'); ?></th>
            <th class="bgBlack" ><?php echo Yii::t('partnerModule.freshMachine', '状态'); ?></th>
            <th class="bgBlack" ><?php echo Yii::t('partnerModule.freshMachine', '商家名'); ?></th>	
            <th class="bgBlack" ><?php echo Yii::t('partnerModule.freshMachine', '商家盖网号'); ?></th>	
            <th class="bgBlack" ><?php echo Yii::t('partnerModule.freshMachine', '操作'); ?></th>
            
        </tr>

        <?php foreach ($goods_data as $data): ?>

            <tr class="even">
                <td class="ta_c"><?php echo $data->name; ?></td>
                <td class="ta_c"><?php echo $data->code ?></td>
                <td class="ta_c"><?php echo (!empty($data->expir_time) && $data->expir_time < time()) ? '<b class="red">'.Yii::t('partnerModule.freshMachine','已失效'): FreshMachineLine::getStatus($data->status); ?></td>
                <td class="ta_c"><?php echo isset($data->partner)?$data->partner->name:'' ?></td>
                <td class="ta_c"><?php echo isset($data->partner)?$data->partner->gai_number:'' ?></td>
                <td class="ta_c">

                    <a href="<?php echo Yii::app()->createUrl('/partner/freshMachine/lineEdit', array('id' => $data->id, 'mid' => $mid)); ?>"><?php echo Yii::t('partnerModule.freshMachine', '修改') ?></a>

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
