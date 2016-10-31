<?php
/* @var $this ProductController */
/* @var $model Product */
$this->breadcrumbs = array('订单' => array('admin'), '列表');
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$('#goods-grid').yiiGridView('update', { //orderid
		data: $(this).serialize()
	});
//	return false;
});
");
?>
<div class="search-form" >
<?php $this->renderPartial('_search', array('model'=>$model)); ?>
</div>
<?php if(!empty($data)):?>
<div class="c10"></div>
<div id="yifenGoods-grid" class="grid-view">
    <table class="tab-reg">
        <thead>
        <tr>
            <th>订单号</th>
            <th>产品ID</th>
            <th>产品名称</th>
            <th>购买用户</th>
            <th>购买次数</th>
            <th>购买总价</th>
			<th>购买日期</th>
            <th>开奖日期</th>
            <th>是否有收货地址</th>
            <th>订单状态</th>
			<th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($data as $v):?>
	    <tr>
            <td><?php echo $v['order_sn'];?></td>
            <td><?php echo $v["goods_id"];?></td>
            <td><?php echo $v['goods_name'];?></td>
            <td><?php echo Member::getMemberInfos($v["member_id"]);?></td>
			<td><?php echo count(json_decode($v['winning_code']))."人次";?></td>
			<td><?php echo "￥".bcmul(count(json_decode($v['winning_code'])),$v['single_price'],2)."元";?></td>
			<td><?php echo date("Y-m-d H:i:s",YfzOrderGoods::getNumberByOrderIds($v["order_id"],$v["goods_id"]));?></td>
            <td><?php echo date("Y-m-d H:i:s",$v['sumlotterytime']);?></td>
			<td>
			<?php 
                 $address = Address::model()->find('member_id=:id',array(':id'=>$v["member_id"]));
                 if(!$address) echo '否';
                 else {
                     echo "是";
                 }
            ?>
			</td>
			<td><?php echo YfzOrder::getOrderStatus($v["order_status"]).",".YfzOrder::getShipping($v["invoice_no"]).",".YfzOrder::getDeliveryStatus($v["is_delivery"]);?></td>
            <td>
            <?php if ($this->getUser()->checkAccess('Manage.OnepartOrder.View')) : ?><!--检查权限 begin-->
            <a href ="<?php echo Yii::app()->createUrl("onepartOrder/view",array("id"=>$v["goods_id"],"nper"=>$v["current_nper"],'order_id'=>$v["order_id"])) ?>" class ="reg-sub">详情</a>
            <?php endif?>
            </td>
            
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    
	<div class="pager">
    <?php
	    $this->widget('SLinkPager', array(
		    'header' => '',
		    'cssFile' => Yii::app()->baseUrl."/css/reg.css",
		    'firstPageLabel' => Yii::t('page', '首页'),
		    'lastPageLabel' => Yii::t('page', '末页'),
		    'prevPageLabel' => Yii::t('page', '上一页'),
		    'nextPageLabel' => Yii::t('page', '下一页'),
		    'maxButtonCount' => 10,
		    'pages' => $pages,
		    'htmlOptions' => array(
			'class' => 'yiiPageer'
		    )
	    ));
    ?>  
    </div>
</div>
   
<?php else:?>
<div class="c10"></div>
<div id="second-kill-grid" class="grid-view">
    <table class="tab-reg">
        <thead>
        <tr>
            <th>订单编号</th>
            <th>购买时间</th>
            <th>购买次数</th>
            <th>购买人</th>
            <th>来自</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div style ="text-align:center;margin-top:10px;"><?php echo Yii::t('goods','没有找到数据');?></div>
</div>
<?php endif ?>
<!--增加开始-->
<div style="display: none" id="confirmArea">
    <style>
        .aui_buttons{
            text-align: center;
        }
    </style>
    <?php 
     $form = $this->beginWidget('ActiveForm', array(
          'id' => $this->id . '-form',
          'enableAjaxValidation' => true,
          'enableClientValidation' => true,
      ));
    ?>
  
<?php $this->endWidget(); ?>

</div>
<!--增加结束-->
<script src="<?php echo DOMAIN_M?>/js/swf/js/artDialog.iframeTools.js"></script>
<script type="text/javascript">
    
</script>