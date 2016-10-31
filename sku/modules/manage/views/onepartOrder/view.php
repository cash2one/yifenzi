<?php
$this->breadcrumbs = array(
    Yii::t('order', '订单管理') => array('admin'),
    Yii::t('order', '订单详情'),
);
if(!$member) $member = new Member;
$code = json_decode($orderGoods->winning_code);
?>
<?php
/* @var $this OrderController */
/* @var $model Orders */
?>
<style>
    /*        height: 25px;
        font-family: "微软雅黑";
        line-height: 25px;
        padding: 5px;
        color: #666;
        position: relative;*/
    table tr td{width: 25%;}
    table tr th{width: 5%;text-align: center;}
    .winning-code li{display: inline-block;padding: 0 5px;}
    .com-box{position: relative;}
</style>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="tab1">
    <tr>
        <td colspan="5" class="title-th">
            <?php echo Yii::t('order', '订单信息'); ?>
        </td>
    </tr>
    <tr>
        <th>
            <?php echo Yii::t('onpartOrder', '产品名称'); ?>：
        </th>
        <td  colspan="">
            <?php echo $model->goods_name ?>
        </td>
        <th></th>
        <td></td>
        <td rowspan="5" id="winning">
            获得中奖码
            <ul class="winning-code">
			<?php if(!empty($code)):?>
                <?php foreach($code as $k=>$c):?>
                <li <?php if($c==$model->winning_code){ echo 'style="background:red"';}?>>
                    <?php echo $c?>
                </li>
                <?php 
                    if($k>40){ 
                        echo "<li>......</li>";
                        break;
                    }
                ?>
                <?php endforeach;?>
	            <?php endif;?>
            </ul>
        </td>
    </tr>
    <tr>
        <th>
            <?php echo "剩余人数" ?>：
        </th>
        <td>
            <?php echo ceil($model->shop_price/$model->single_price) - YfzOrderGoods::countGoods($model->goods_id,$model->current_nper) . '人次'; ?>
        </td>
        <th>
            <?php echo "总需次数" ?>：
        </th>
        <td>
            <?php echo ceil($model->shop_price/$model->single_price); ?>
        </td>
    </tr>
    <tr>
        <th>
            <?php echo '商品期数'; ?>：
        </th>
        <td>
            <?php echo "第" . $model->current_nper . "期"; ?>
        </td>
        <th>
            <?php echo '商品价格'; ?>：
        </th>
        <td>
            <?php echo "￥".$model->shop_price ?>
        </td>
    </tr>
    <tr>
        <th>
            <?php echo "中奖人" ?>：
        </th>
        <td>
            <?php  echo $member->username;?>
        </td>
        <th>
            <?php echo "中奖码" ?>：
        </th>
        <td>
            <?php echo $model->winning_code ?>
        </td>
    </tr>

    <tr>
        <th>
            <?php echo "购买次数" ?>
        </th>
        <td>
            <?php echo count($code)."人次"; ?>
        </td>
        <th>
            <?php echo "购买总价" ?>
        </th>
        <td>
            <?php echo "￥".bcmul(count($code),$orderGoods['single_price'],2)."元"; ?>
        </td>
    </tr>
  
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="tab1">
    <tr>
        <td colspan="6" class="title-th">
            <?php echo Yii::t('order', '中奖人信息'); ?>
        </td>
    </tr>
    <tr>
        <th>购买人ID:</th>
        <td>
            <?php echo $member->id; ?>
        </td>
        <th>购买人GW:</th>
        <td>
            <?php echo $member->gai_number; ?>
        </td>
        <th>购买人名称:</th>
        <td>
            <?php   $address = Address::model()->find('member_id=:id AND `default`=:default',array(':id'=>$model->member_id,':default'=>Address::DEFAULT_IS));

            if(!$address){
                $address_new = Member::getMemberAddressNew($member->id);
                echo !empty($address_new)?$address_new['real_name']:'';
            }else{
                echo !empty($address)?$address['real_name']:'';
            }
            ?>
        </td>
    </tr>
    <tr>
        <th>购买人手机:</th>
        <td>
            <?php echo $member->mobile; ?>
        </td>
        <th>购买时间:</th>
        <td>
            <?php echo date("Y-m-d H:i:s",$orderGoods->addtime); ?>
        </td>
        <th>收货地址:</th>
        <td>
            <?php
                // if(!$address) echo '用户没有默认地址';
				if(!$address){
                    echo Region::getName($address_new["province_id"],$address_new["city_id"],$address_new["district_id"]) . ' ' .  $address_new["street"];
				}
                else{
                    //兼容旧数据
                    echo Region::getName($address->province_id,$address->city_id,$address->district_id) . ' ' .  $address->street;
                }
            ?>
        </td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="tab1">
    <tr>
        <td colspan="6" class="title-th">
            <?php echo Yii::t('order', '中奖人地址信息'); ?>
        </td>
    </tr>
</table>
<div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
            "id" => 'onepartOrder-form',
            'action'=>  Yii::app()->createUrl('onepartOrder/updateShipping'),
            "enableAjaxValidation" => false,
            "enableClientValidation" => true,
            "clientOptions" => array(
                'validateOnSubmit' => true,
            ),
        ));
    ?>
    <table cellpadding="0" cellspacing="0" class="searchTable" style="float: none;">
        <tbody>
            <tr>
                <th><?php echo '购买方式：'; ?></th>
                <td><?php echo YfzOrder::getPayStatus($order->payment_type); ?></td>
            </tr>
            <tr>
                <th><?php echo '当前订单状态：'; ?></th>
                <td><?php echo YfzOrder::getOrderStatus($order->order_status) . ' '. YfzOrder::getShipping($order->invoice_no). ' '. YfzOrder::getDeliveryStatus($order->is_delivery) ?></td>
            </tr>
<!--            <tr>
                <th><?php echo '订单状态：'; ?></th>
                <td><?php //echo $form->textField($model, 'member_id', array('class' => 'text-input-bj middle')); ?></td>
            </tr>-->
            <tr>
                <th><?php echo '物流公司：'; ?></th>
                <td>
                    <?php 
                        echo $form->textField($order, 'invoice_company', array('class' => 'text-input-bj middle'));
                        echo $form->error($order,'invoice_company');
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo '快递单号：'; ?></th>
                <td>
                    <?php echo $form->textField($order, 'invoice_no', array('class' => 'text-input-bj middle')); ?>
                    <?php echo $form->error($order,'invoice_no');?>
                </td>
            </tr>
            
        </tbody>
    </table>
    <div>
        <?php echo $form->hiddenField($order,'order_id')?>
        <?php echo CHtml::submitButton(Yii::t('user', '更新'), array('class' => 'reg-sub')); ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
<?php if(count($code) > 40):?>
<div style="display:none;background: #ccc;padding: 6px;" id="code">
    <ul class="winning-code">
        <?php if(!empty($code)):?>
        <?php foreach($code as $c):?>
        <li <?php if($c==$model->winning_code){ echo 'style="background:red"';}?>>
            <?php echo $c?>
        </li>
        <?php endforeach;?>
        <?php endif;?>
    </ul>
</div>
<script type="text/javascript">
    var w=$('.com-box').width();
    var h=$('.com-box').height();
    var t = setTimeout(function(){
        $('#winning').mousemove(function(e){
            var x=e.pageX;
            var y=e.pageY-10;
            $('#code').css({
                position:'absolute',
                width:x-30,
                right:w-x+30,
                top:y-30
            }).show();
        }).mouseleave(function(e){
            $('#code').hide();
        });
    },500)
</script>
<?php endif; ?>
