<?php
/** @var $this Controller */
/** @var $order Order */
/** @var $v OrderGoods */
?>
<?php $flag = true; //跨列标记 ?>
<?php foreach($order->orderGoods as $v): ?>
    <tr  class="bgF4">
        <td  align="center" valign="middle" class="tit productArr01">
            <a href="<?php echo $this->createAbsoluteUrl('/goods/'.$v->goods_id) ?>" class="img" target="_blank">
                <img src="<?php echo Tool::showImg(IMG_DOMAIN.'/'.$v->goods_picture,'c_fill,h_32,w_32') ?>" >
            </a>
            <?php echo CHtml::link($v->goods_name,$this->createAbsoluteUrl('/goods/'.$v->goods_id),
                array('class'=>'name','target'=>'_blank')); ?>
            <?php
                if(ShopCart::checkHyjGoods($v->goods_id)){
                    echo "<font style='color:red'>(合约机-所购号码".$order->extend.")</font>";
                }
            
            ?>
                <span style="color:#999" class="name">
                <?php
                        if ($v->spec_value) {
                            $spec=  unserialize($v->spec_value);
                            $specstr='';
                            foreach ($spec as $ks => $vs) {
                                $specstr .= $ks . ':' . $vs . ' ';
                            }
                            echo $specstr;
                        }
                 ?>
                </span>
        </td>
        <td  align="center" valign="middle"><b><?php echo HtmlHelper::formatPrice($v['gai_price']) ?></b></td>
        <td  align="center" valign="middle"><b class="color1b"><?php echo $v['quantity'] ?></b></td>
        <td  align="center" valign="middle">
            <b class="color1b">
                <?php if($v['freight_payment_type']!=Goods::FREIGHT_TYPE_MODE): ?>
                    <?php echo Goods::freightPayType($v['freight_payment_type']) ?>
                <?php else: ?>
                    <?php echo HtmlHelper::formatPrice($v['freight'])?>
                <?php endif; ?>
            </b>
        </td>

        <?php $rowSpan = count($order->orderGoods) > 1 ? 'rowspan="'.count($order->orderGoods).'"':false ?>
        <?php if(($rowSpan && $flag) || $flag):  //和并列 || 显示 ?>
            <td  align="center" valign="middle" <?php echo $rowSpan ?>>
                <p class="red">
                    <?php 
                    $allGai=0;
                    foreach ($order->orderGoods as $v){
                        $allGai+=$v['gai_price']*$v['quantity'];
                    }
                    echo HtmlHelper::formatPrice($allGai + $order->freight); 
                    ?>
                </p>
                <p class="gray">（<?php echo Yii::t('partnerModule.order','含运费'); ?>：
                    <?php echo HtmlHelper::formatPrice($order->freight); ?>）</p>
            </td>
            <td  align="center" valign="middle" class="controlList" <?php echo $rowSpan ?>>
                <?php echo $order::status($order->status) ?><br/>
                <?php if($order->status==$order::STATUS_NEW): //新订单才显示先关支付、物流状态 ?>
                    <?php echo $order::payStatus($order->pay_status) ?><br/>
                    <?php echo $order::deliveryStatus($order->delivery_status); ?><br/>
                    <?php if($order->refund_status!=$order::REFUND_STATUS_NONE): ?>
                        <?php echo $order::refundStatus($order->refund_status).Yii::t('partnerModule.order','退款') ?>
                    <?php endif ?>
                    <?php if($order->return_status!=$order::RETURN_STATUS_NONE): ?>
                        <?php echo $order::returnStatus($order->return_status) ?>
                    <?php endif ?>
                <?php endif; ?>
                <?php if($order->is_right == $order::RIGHT_YES): ?>
                    <?php echo $order::rightStatus($order->is_right) ?>
                <?php endif ?>
            </td>

            <td  align="center" valign="middle" class="controlList" <?php echo $rowSpan ?>>
                <p><?php echo CHtml::link(Yii::t('partnerModule.order','订单详情'),
                        $this->createAbsoluteUrl('/seller/order/detail/code/' . $order->code)) ?>
                </p>
                <?php //新订单，没有签收，都可以关闭交易 ?>
                <?php if($order->status==$order::STATUS_NEW && $order->refund_status==Order::REFUND_STATUS_NONE && $order->return_status==Order::RETURN_STATUS_NONE): ?>
                <p><?php echo CHtml::link(Yii::t('partnerModule.order','关闭交易'),'#',array('class'=>'closeOrder','data-code'=>$order->code)); ?></p>
                <?php endif; ?>

                <?php if(($order->status==$order::STATUS_NEW && $order->pay_status==$order::PAY_STATUS_YES
                && $order->delivery_status==$order::DELIVERY_STATUS_NOT && $order->refund_status==$order::REFUND_STATUS_NONE) || ($order->refund_status==$order::REFUND_STATUS_FAILURE && $order->delivery_status==Order::DELIVERY_STATUS_NOT)): ?>
                <?php echo CHtml::link(Yii::t('partnerModule.order','备货'),'#',array('class'=>'stockup','data-code'=>$order->code)); ?>
                <?php endif; ?>

                <?php if(($order->refund_status==Order::REFUND_STATUS_FAILURE && $order->delivery_status==$order::DELIVERY_STATUS_WAIT)||$order->status==$order::STATUS_NEW && $order->delivery_status==$order::DELIVERY_STATUS_WAIT): ?>
                <p><?php echo CHtml::link(Yii::t('partnerModule.order','发货'),
                        $this->createAbsoluteUrl('/seller/order/detail/code/' . $order->code.'#delivery')); ?></p>
                <?php endif; ?>
                
                <?php if ($order->status == $order::STATUS_NEW && $order->pay_status == $order::PAY_STATUS_YES && $order->delivery_status == $order::DELIVERY_STATUS_SEND&& $order->return_status==Order::RETURN_STATUS_PENDING): //新订单，已支付，已出货
                    ?>
                <p> <a href="javascript:ConfirmAGRePurcharse('<?php echo $order->id?>','<?php echo $order->deduct_freight?>')"><?php echo Order::returnStatus($order->return_status)?></a></p>
                 <?php elseif ($order->return_status==Order::RETURN_STATUS_AGREE):?>
                <p> <?php echo CHtml::link(Yii::t('partnerModule.order','签收退货'),$this->createAbsoluteUrl('/seller/order/signReturn/',array('code' => $order->code)),array('class'=>'agreeReturn','data-code'=>$order->id,'onclick'=>'return ConfirmDelete()')); ?></p>
                <?php endif; ?>
                 <?php if($order->refund_status==Order::REFUND_STATUS_PENDING):?>
                    <p><?php echo CHtml::link(Yii::t('partnerModule.order','同意退款'),'#',array('class'=>'agreerefund','data-code'=>$order->code,'data-agree'=>Order::REFUND_STATUS_SUCCESS)); ?></p>
                    <p><?php echo CHtml::link(Yii::t('partnerModule.order','不同意退款'),'#',array('class'=>'disagreerefund','data-code'=>$order->code,'data-disagree'=>Order::REFUND_STATUS_FAILURE)); ?></p>
                 <?php endif;?>
            </td>
        <?php endif; ?>

        <?php $flag = $rowSpan ? false : true; ?>

    </tr>
<?php endforeach; ?>
