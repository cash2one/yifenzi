<?php
$this->breadcrumbs = array(
    Yii::t('order', '订单管理')=>array('admin'),
    Yii::t('order', '订单详情'),
);
?>
<?php
/* @var $this OrderController */
/* @var $model Orders */
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="tab1">
    <tr>
        <td colspan="8" class="title-th">
            <?php echo Yii::t('order', '订单信息'); ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('code') ?>：
        </th>
        <td>
            <?php echo $model->code ?>
        </td>
        
        <th align="right">
            <?php echo Yii::t('order', '会员编码'); ?>：
        </th>
         <td>
            <?php echo $model->member_id ?>
        </td>
        
        <th align="right">
            <?php echo Yii::t('order', '联系电话'); ?>：
        </th>
         <td>
            <?php echo $model->mobile ?>
        </td>
        
        <th align="right">
            <?php echo $model->getAttributeLabel('store_id') ?>：
        </th>
         <td>
            <?php echo $model->store_id ?>
            <?php if ( !empty($model->machine_id)):?>
            
            机器id：<?php echo $model->machine_id?>
            <?php endif;?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('type') ?>：
        </th>
        <td>
            <?php echo Order::type($model->type)?>
        </td>
        
        <th align="right">
            <?php echo $model->getAttributeLabel('total_price') ?>：
        </th>
         <td>
            <?php echo $model->total_price ?>（运费：<?php echo $model->shipping_fee?>）
        </td>
        
        <th align="right">
            <?php echo $model->getAttributeLabel('pay_price') ?>：
        </th>
         <td>
            <?php echo $model->pay_price ?>
        </td>
        <th align="right">
            支付方式：
        </th>
        <td>
            <?=$model->pay_status == Order::PAY_STATUS_YES ? Order::getPayType($model->pay_type) : "" ?>
        </td>
    </tr>
<tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('status') ?>：
        </th>
        <td>
            <?php echo Order::status($model->status)?>
        </td>
        
        
         <th><?php echo Yii::t('order','备注')?>：</th>
        <td>
            原始：<?php echo $model->remark;?> <br/>
            后续：<?php echo $model->seller_remark;?>
            
        </td>
        <th align="right">
            <?php echo $model->getAttributeLabel('shipping_type') ?>：
        </th>
         <td>
            <?php echo Order::shippingType($model->shipping_type) ?>
        </td>
        
        <?php if($model->type==Order::TYPE_MACHINE||$model->type==Order::TYPE_FRESH_MACHINE||$model->type==Order::TYPE_MACHINE_CELL_STORE):?>
        
        <th align="right">
            <?php echo '机器是否已备\出货' ?>：
        </th>
         <td>
            <?php echo $model->machine_status?'是':'否' ?> | 购买方式： <?php echo  Order::machineTakeType($model->machine_take_type)?>
        </td>
        
        <?php else:?>
        
        <th align="right">

        </th>
         <td>

        </td>
        
        <?php endif;?>
    </tr>
    <?php if(!empty($address)):?>
    <tr>
       <th align="right">
            <?php echo Yii::t('order','客户姓名')?>：
        </th>
         <td>
            <?php echo isset($address->real_name)?$address->real_name:'查无信息' ?>
        </td>
        
         <th><?php echo Yii::t('order','送货地址')?>：</th>
        <td>
            <?php echo  isset($address->street)?$address->street:'查无信息';?>
        </td>
        
        <th><?php echo Yii::t('order','联系电话')?>：</th>
        <td>
            <?php echo  isset($address->mobile)?$address->mobile:'查无信息';?>
        </td>
     <?php endif;?>        
    </tr>
    
    
        <tr>
        <th align="right">
            订单时间：
        </th>
        <td>
          下单时间 - <?php echo date('Y-m-d G:i:s',$model->create_time)?><br/>
          支付时间 - <?php echo $model->pay_time?date('Y-m-d G:i:s',$model->pay_time):'未知'?><br/>
          发货时间 - <?php echo $model->send_time?date('Y-m-d G:i:s',$model->send_time):'未知'?><br/>
          签收时间 - <?php echo $model->sign_time?date('Y-m-d G:i:s',$model->sign_time):'未知'?><br/>
          取消时间 - <?php echo $model->cancel_time?date('Y-m-d G:i:s',$model->cancel_time):'未知'?><br/>
        </td>
        
        </tr>
    
    
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="tab1">
     <tr>
        <td colspan="10" class="title-th">
            <?php echo Yii::t('order', '订单商品'); ?>
        </td>
    </tr>
    <?php    foreach ($order_goods as $v):?>
    <tr>
        <th align="right">
            <?php echo Yii::t('order','商品名称') ?>：
        </th>
        <td>
            <?php echo $v->goods->name?>
        </td>
        <th align="right">
            <?php echo Yii::t('order','商品数量') ?>：
        </th>
        <td>
            <?php echo $v->num?>
        </td>
        <th>
            <?php echo $v->getAttributeLabel('price')?>：
        </th>
        <td>
            <?php echo $v->price;?>
        </td>
         <th>
            <?php echo $v->getAttributeLabel('total_price')?>
        </th>
        <td>
            <?php echo $v->total_price;?>
        </td>
        <th>
            <?php echo Yii::t('order','货道编码');?>
        </th>
        <td>
            <?php echo  (!empty($v->sg_outlets))?$v->sg_outlets:'-'?>
        </td>
    </tr>
    <?php    endforeach;?>
</table>