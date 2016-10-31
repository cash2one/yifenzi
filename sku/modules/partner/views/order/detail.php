<?php
/* @var $this Controller */
/* @var $model Order */
$this->breadcrumbs = array(
    Yii::t('partnerModule.order', '订单') => array('index'),
    Yii::t('partnerModule.order', '详情'),
);
?>
<script src="<?php echo DOMAIN ?>/js/artDialog/plugins/iframeTools.source.js" type="text/javascript"></script>
<div class="mainContent">
    <div class="toolbar">
        <h3><?php echo Yii::t('partnerModule.order', '订单详情'); ?>—<?php echo $model->code; ?></h3>
    </div>
    <?php if (!empty($address)): ?>
        <h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.order', '收货人信息'); ?></h3>
        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
            <tbody>
                <tr>
                    <th width="10%"><?php echo Yii::t('partnerModule.order', '收货人'); ?></th>
                    <td width="40%"><?php echo $address->real_name ?></td>
                    <th width="10%"><?php echo Yii::t('partnerModule.order', '收货地址'); ?></th>
                    <td width="40%"><?php echo Region::getName($address->province_id, $address->city_id, $address->district_id) . '  ' . $address->street ?></td>
                </tr>
                <tr>
                    <th><?php echo Yii::t('partnerModule.order', '联系方式'); ?></th>
                    <td><?php echo $address->mobile ?></td>
                    <th><?php echo Yii::t('partnerModule.order', '邮编'); ?></th>
                    <td><?php echo $address->zip_code ?></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
    <h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.order', '订单信息'); ?></h3>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
        <tbody>
            <tr>
                <th width="10%"><?php echo Yii::t('partnerModule.order', '订单编号'); ?></th>
                <td width="23%"><?php echo $model->code ?></td>
                <th width="10%"><?php echo Yii::t('partnerModule.order', '订单时间'); ?></th>
                <td width="23%">
                
                 <?php echo  Yii::t('partnerModule.order', '下单时间')?><?php echo date('Y-m-d G:i:s',$model->create_time)?><br/>
		          <?php echo $model->pay_time? Yii::t('partnerModule.order', '支付时间').date('Y-m-d G:i:s',$model->pay_time).'<br/>':''?>
		          <?php echo $model->send_time? Yii::t('partnerModule.order', '发货时间').date('Y-m-d G:i:s',$model->send_time).'<br/>':''?>
		          <?php echo $model->sign_time?Yii::t('partnerModule.order', '签收时间').date('Y-m-d G:i:s',$model->sign_time).'<br/>':''?>
		          <?php echo $model->cancel_time? Yii::t('partnerModule.order', '取消时间').date('Y-m-d G:i:s',$model->cancel_time).'<br/>':''?>
		             
                </td>
                <th width="10%"><?php echo Yii::t('partnerModule.order', '订单状态'); ?></th>
                <td width="24%"><?php echo Order::status($model->status) ?></td>
            </tr>
            <tr>
                <th><?php echo Yii::t('partnerModule.order', '支付状态'); ?></th>
                <td><?php echo Order::payStatus($model->pay_status) ?></td>
                <th><?php echo Yii::t('partnerModule.order', '送货方式'); ?></th>
                <td><?php echo Order::shippingType($model->shipping_type) ?></td>
                <th><?php echo Yii::t('partnerModule.order', '总价'); ?></th>
                <td><?php echo $model->total_price ?></td>
            </tr>
             <tr>
                <th><?php echo Yii::t('partnerModule.order', '支付方式'); ?></th>
                <td><?php echo $model->pay_status==Order::PAY_STATUS_YES?Order::getPayType($model->pay_type):'' ?></td>
                <th><?php echo Yii::t('partnerModule.order', '商家、系统备注'); ?></th>
                <td><?php echo$model->seller_remark ; ?></td>
                <th><?php echo Yii::t('partnerModule.order', '联系电话'); ?></th>
                <td><?php echo$model->mobile ; ?></td>
            </tr>
            <tr>
                <th ><?php echo Yii::t('partnerModule.order', '用户备注'); ?></th>
                <td colspan="5"><?php echo $model->remark ;?></td>

            </tr>
        </tbody>
    </table>
    <h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.order', '商品信息'); ?></h3>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
        <tbody>
            <tr>
                <th  width="10%"></th>
                <th width="10%"><?php echo Yii::t('partnerModule.order', '商品名称'); ?></th>
                <th width="10%"><?php echo Yii::t('partnerModule.order', '单价'); ?></th>
                <th width="10%"><?php echo Yii::t('partnerModule.order', '数量'); ?></th>
                <th width="10%"><?php echo Yii::t('partnerModule.order', '总价'); ?></th>
                  <th width="10%"><?php echo Yii::t('partnerModule.order', '货道编号'); ?></th>
               
            </tr>
            <?php foreach ($orders as $k => $v): ?>
            <tbody>
                <tr>
                    <th width="10%" align="center"><?php echo $k + 1 ?></th>
                    <td width="15%"align="center"><?= isset($v->goods->name) ?  $v->goods->name : '' ?></td>
                    <td width="15%" align="center"><?php echo $v->price ?></td>
                    <td width="15%" align="center"><?php echo $v->num ?></td>
                    <td width="15%" align="center"><?php echo $v->total_price ?></td>
                     <td width="15%" align="center"><?php echo  (!empty($v->sg_outlets))?$v->sg_outlets:'-'?></td>
                </tr>
            </tbody>
        <?php endforeach; ?>
    </table>
    <div class="profileDo mt15">
        <a href="javascript:history.go(-1);" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.order', '返回'); ?></span></a>
    </div>




