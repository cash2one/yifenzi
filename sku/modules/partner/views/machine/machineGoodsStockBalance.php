<?php
// 切换加盟商视图
$this->breadcrumbs = array(
    Yii::t('partnerModule.machine', '加盟商') => array('/seller/franchisee/'),
    Yii::t('partnerModule.machine', '盖网售货机商品库存流水')
);
?>
<div class="toolbar">
	<b> <?php echo $model->name;?> <?php echo Yii::t('partnerModule.machine','的盖网售货机商品库存流水');?></b>
	<span><?php echo Yii::t('partnerModule.machine','盖网售货机商品库存流水。');?></span>
	
</div>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'action' => Yii::app()->createUrl($this->route,array('mid'=>$_GET['mid'])),
    'method' => 'get',
));
?>
<div class="seachToolbar">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="sellerT5">
        <tr>
<?php if ($no_search!=true ):?>

            <th width="10%"><?php echo $form->label($balance_model, 'g_name'); ?>：</th>
            <td width="30%">
                <?php echo $form->textField($balance_model, 'g_name', array('class' => 'inputtxt1','style'=>'width:90%;')); ?>
            </td>
            <td width="60%"><input type="submit" class="sellerBtn06" value="<?php echo Yii::t('partnerModule.machine', '搜索');?>"/>&nbsp;&nbsp;
                <?php echo CHtml::button(Yii::t('partnerModule.machine', "导出EXCEL"),array('class'=>'sellerBtn07','onclick'=>'getExcel()'))?>

                <?php echo CHtml::button(Yii::t('partnerModule.machine', "返回"),array('class'=>'mt15 btnSellerEditor','onclick'=>'history.go(-1)', 'style'=>"float:right"))?>
                </td>
<?php else: ?>

        <td>
                <?php echo CHtml::button(Yii::t('partnerModule.machine', "导出EXCEL"),array('class'=>'sellerBtn07','onclick'=>'getExcel()'))?>

                <?php echo CHtml::button(Yii::t('partnerModule.machine', "返回"),array('class'=>'mt15 btnSellerEditor','onclick'=>'history.go(-1)', 'style'=>"float:right"))?>
                </td>
        

<?php endif;?>
</tr>
    </table>
</div>

<?php $this->endWidget(); ?>

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
						<tbody><tr>
								<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.machine','日期');?></th>
								<th class="bgBlack" width="20%"><?php echo Yii::t('partnerModule.machine','商品名称');?></th>
								<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','节点');?></th>
								<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','节点类型');?></th>
								<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','数量');?></th>
								<th class="bgBlack" width="10%"><?php echo Yii::t('partnerModule.machine','结余');?></th>
							</tr>
							
							<?php foreach ($balance_data as $data):?>
							
							<tr class="even">
							    <td class="ta_c"><?php echo date('Y-m-d G:i:s', $data->create_time);  ?></td>
								<td class="ta_c"><?php echo $data->name;  ?></td>
								<td class="ta_c"><b class="red"><?php echo VendingMachineStockBalance::getNode($data->node);?></b></td>
								<td class="ta_c"><b class="red"><?php echo VendingMachineStockBalance::getNodeType($data->node_type);?></b></td>
								<td class="ta_c"><?php echo $data->num;?></td>
								<td class="ta_c"><?php echo $data->balance;?></td>
							</tr>
							
							<?php endforeach;?>
							
					</tbody></table>
					
					
					
					
<div class="page_bottom clearfix">
	<div class="pagination">
		<?php
		  $this->widget('CLinkPager',array(  
		    'header'=>'',
		    'prevPageLabel' => Yii::t('partnerModule.page', '上一页'),
		    'nextPageLabel' => Yii::t('partnerModule.page', '下一页'),
		    'pages' => $pager,       
		    'maxButtonCount'=>10, 
		    'htmlOptions'=>array(
		       'class'=>'paging', 
		     )
		  )
		  );
		?>
	</div>
</div>
<script>
    /**
     * 获取Excel
     */
    function getExcel() {
        var url = window.location.href.replace("franchisee/vendingMachineGoodsStockBalance", "franchisee/excel");
        window.open(url);
    }
</script>