<?php
/* @var $this OrderController */
/* @var $model Order */
/* @var $form CActiveForm */
$this->breadcrumbs = array(
    Yii::t('partnerModule.order', '订单'),
    Yii::t('partnerModule.order', '列表'),
);
?>
<script src="<?php echo DOMAIN ?>/js/artDialog/plugins/iframeTools.source.js" type="text/javascript"></script>

<div class="toolbar">
    <b><?php echo Yii::t('partnerModule.order', '订单列表'); ?></b>
</div>

<?php $this->renderPartial('_search', array('model' => $model)); ?>

<?php
$order = $this->getParam('Order');
$Order_status = empty($order)? 'new':$order['status'] ;?>

<div class="gateAssistant mt15 clearfix">
    <b class="black"><?php echo Yii::t('partnerModule.order', '订单状态'); ?>：</b>
    <a href="<?php
    echo $this->createAbsoluteUrl('order/index', array(
    ))
    ?>" class="<?php echo $this->getParam('on') == 'not' ? 'sellerBtn05' : 'sellerBtn02' ?>">
        <span <?php
	$refund = $this->getParam('Refund');
        if($Order_status == 'new'&&empty($refund)):?> style="color:red;"<?php endif ;?>><?php echo Yii::t('partnerModule.order', '全部'); ?>(<?php echo $allNum; ?>)</span>
    </a>
    
      <a href="<?php
    echo $this->createAbsoluteUrl('order/index', array(
    'Order[status]' => $model::STATUS_NEW
    ))
    ?>" class="<?php echo $this->getParam('on') == 'not' ? 'sellerBtn05' : 'sellerBtn02' ?>">
          <span <?php if($Order_status== (string)($model::STATUS_NEW)):?> style="color:red;"<?php endif ;?>><?php echo Yii::t('partnerModule.order', '新订单'); ?>(<?php echo $newNum; ?>)</span>
    </a>
   

    <a href="<?php
    echo $this->createAbsoluteUrl('order/index', array(
        'Order[status]' => $model::STATUS_PAY,
    ))
    ?>" class="<?php echo $this->getParam('on') == 'send' ? 'sellerBtn05' : 'sellerBtn02' ?>">
        <span <?php if((int)$Order_status==$model::STATUS_PAY):?> style="color:red;"<?php endif ;?>><?php echo Yii::t('partnerModule.order', '已支付'); ?>(<?php echo $payNum; ?>)</span>
    </a>

    <a href="<?php
    echo $this->createAbsoluteUrl('order/index', array(
        'Order[status]' => $model::STATUS_SEND,
    ))
    ?>" class="<?php echo $this->getParam('on') == 'refund' ? 'sellerBtn05' : 'sellerBtn02' ?>">
        <span <?php if((int)$Order_status==$model::STATUS_SEND):?> style="color:red;"<?php endif ;?>><?php echo Yii::t('partnerModule.order', '已发货'); ?>(<?php echo $sendNum; ?>)</span>
    </a>

      <a href="<?php
    echo $this->createAbsoluteUrl('order/index', array(
        'Refund' => $model::REFUND_STATUS_SUCCESS,
    ))
    ?>" class="<?php echo $this->getParam('on') == 'return' ? 'sellerBtn05' : 'sellerBtn02' ?>">
          <span <?php if((int)$this->getParam('Refund')==$model::REFUND_STATUS_SUCCESS):?> style="color:red;"<?php endif ;?>><?php echo Yii::t('partnerModule.order', '已退款'); ?>(<?php echo $refundedNum; ?>)</span>
    </a>
    
      <a href="<?php
    echo $this->createAbsoluteUrl('order/index', array(
        'Order[status]' => $model::STATUS_COMPLETE,
    ))
    ?>" class="<?php echo $this->getParam('on') == 'return' ? 'sellerBtn05' : 'sellerBtn02' ?>">
        <span <?php if((int)$Order_status==$model::STATUS_COMPLETE):?> style="color:red;"<?php endif ;?>><?php echo Yii::t('partnerModule.order', '完成'); ?>(<?php echo $completeNum; ?>)</span>
    </a>
    
      <a href="<?php
    echo $this->createAbsoluteUrl('order/index', array(
        'Order[status]' => $model::STATUS_CANCEL,
    ))
    ?>" class="<?php echo $this->getParam('on') == 'return' ? 'sellerBtn05' : 'sellerBtn02' ?>">
        <span <?php if((int)$Order_status==$model::STATUS_CANCEL):?> style="color:red;"<?php endif ;?>><?php echo Yii::t('partnerModule.order', '取消'); ?>(<?php echo $cancelNum; ?>)</span>
    </a>
    
   
    
     <a href="<?php
    echo $this->createAbsoluteUrl('order/index', array(
        'Order[status]' => $model::STATUS_FROZEN,
    ))
    ?>" class="<?php echo $this->getParam('on') == 'return' ? 'sellerBtn05' : 'sellerBtn02' ?>">
        <span <?php if((int)$Order_status==$model::STATUS_FROZEN):?> style="color:red;"<?php endif ;?>><?php echo Yii::t('partnerModule.order', '冻结'); ?>(<?php echo $frozenNum; ?>)</span>
    </a>
</div>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt15 sellerT3">
    <tr>
        <th  width="10%" class="bgBlack"><?php echo Yii::t('partnerModule.order', '订单编号'); ?></th>
        <th width="10%" class="bgBlack"><?php echo Yii::t('partnerModule.order', '订单类型'); ?></th>
        <th width="10%" class="bgBlack"><?php echo Yii::t('partnerModule.order', '网点'); ?></th>
        <th width="10%" class="bgBlack"><?php echo Yii::t('partnerModule.order', '订单金额'); ?></th>
        <th width="15%" class="bgBlack"><?php echo Yii::t('partnerModule.order', '状态'); ?></th>
        <th width="10%" class="bgBlack"><?php echo Yii::t('partnerModule.order', '操作'); ?></th>
    </tr>
    <?php /** @var $v Order */ ?>
    <?php foreach ($orders as $k => $v): ?>
        <tr>
            <td colspan="5" align="left" valign="middle" class="bgE5">    
                <?php echo Yii::t('partnerModule.order', '下单时间'); ?>：
                <?php echo $this->format()->formatDatetime($v->create_time) ?>&nbsp; &nbsp; &nbsp;
            </td>
        </tr>
        <tr>
             <td align="center"><?php echo $v->code?></td>
             <td align="center"><?php echo Order::type($v->type)?>
             <?php if ($v->father_id>0) {
             	echo '[子订单]';
             }?>
             </td>
              <td align="center"><?php echo $v->type==Order::TYPE_SUPERMARK?(isset($v->machine->name)?$v->machine->name:''):($v->type==Order::TYPE_MACHINE?(isset($v->freshMachine->name)?$v->freshMachine->name:''):($v->type==Order::TYPE_FRESH_MACHINE?(isset($v->store->name)?$v->store->name:''):"未知")) ?></td>
            <td align="center"><?php echo $v->total_price?></td>
            <td align="center" style="color:red"><?php echo Order::status($v->status);?>
                <?php if(!empty($v->refund_status)):?>
                <br>
                <span style="color:green"><?php echo Order::refundStatus($v->refund_status);?></span>
                <?php endif;?>
            </td>
            <td  align="center" valign="middle" class="controlList">
                <p><?php echo CHtml::link(Yii::t('partnerModule.order','订单详情'),
                        $this->createAbsoluteUrl('/partner/order/detail/id/' . $v->id)) ?>
                </p>
              
              
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        
        <td colspan="1">
        </td>
        <td height="35" colspan="6" align="center" valign="middle" class="bgF4">

            <?php
            $this->widget('LinkPager', array(
                'pages' => $pages,
                'jump' => false,
                'htmlOptions' => array('class' => 'pagination'),
            ))
            ?>

        </td>
    </tr>
</table>

<div style="display: none;" id="confirmArea">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="tab-come">
        <tbody>
            <tr>
                <th style="text-align: center;padding-bottom: 5px;font-size: 14px;" id="confimTitle" class="title-th even" colspan="3"></th>
            </tr>
            <tr>
                <td id="confirmDetail" colspan="2" class="odd">

                </td>
            </tr>

        </tbody>
    </table>
</div>

<script>
    /**
     * ajax 操作订单 备货、取消订单等的公共方法
     * @param url
     * @param code
     * @param msg
     */

//关闭订单
$("#OrderClose").click(function() {
    var code = $(this).attr("data-code");
    var url = '<?php echo Yii::app()->createAbsoluteUrl('/partner/order/close') ?>';
    var msg = '<?php echo Yii::t('partnerModule.order', '您确认要取消此订单吗？'); ?>';
    $("#confimTitle").html(msg);
    art.dialog({     
        icon: 'question',
        title: '<?php echo Yii::t('partnerModule.order', '消息') ?>',
        okVal: '<?php echo Yii::t('partnerModule.order', '确定') ?>',
        cancelVal: '<?php echo Yii::t('partnerModule.order', '取消') ?>',
        content: $("#confirmArea").html(),
        lock: true,
        cancel: true,
        ok: function() {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: url,
                data: {code: code, YII_CSRF_TOKEN: "<?php echo Yii::app()->request->csrfToken ?>"},
                success: function(data) {
                    if (data.success) {
                        art.dialog({icon: 'succeed', content: data.success});
                        location.reload();
                    } else {
                        art.dialog({icon: 'error', content: data.error});
                    }
                }
            });
        }
    });
    return false;
});
//发货
$("#OrderSend").click(function() {
    var code = $(this).attr("data-code");
    var url = '<?php echo Yii::app()->createAbsoluteUrl('/partner/order/send') ?>';
    var msg = '<?php echo Yii::t('partnerModule.order', '您确认要发货吗？'); ?>';
    $("#confimTitle").html(msg);
    art.dialog({     
        icon: 'question',
        title: '<?php echo Yii::t('partnerModule.order', '消息') ?>',
        okVal: '<?php echo Yii::t('partnerModule.order', '确定') ?>',
        cancelVal: '<?php echo Yii::t('partnerModule.order', '取消') ?>',
        content: $("#confirmArea").html(),
        lock: true,
        cancel: true,
        ok: function() {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: url,
                data: {code: code, YII_CSRF_TOKEN: "<?php echo Yii::app()->request->csrfToken ?>"},
                success: function(data) {
                    if (data.success) {
                        art.dialog({icon: 'succeed', content: data.success});
                        location.reload();
                    } else {
                        art.dialog({icon: 'error', content: data.error});
                    }
                }
            });
        }
    });
    return false;
});

function getExcel() {
    var url = window.location.href.replace("order/index", "order/export");
    window.open(url);
}

</script>
<?php
$this->renderPartial('/layouts/_export', array(
    'model' => $model, 'exportPage' => $exportPage, 'totalCount' => $totalCount,
));
?>

<style>
.pagination{ padding:0 }
</style>

