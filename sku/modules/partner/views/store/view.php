<?php
$this->breadcrumbs=array(
    Yii::t('partnerModule.store','超市门店管理'),
    Yii::t('partnerModule.store','门店信息'),
);

?>

<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.store','门店信息'); ?></h3>
</div>

<?php echo CHtml::link(Yii::t('partnerModule.store','编辑资料'),
    $this->createAbsoluteUrl('/partner/store/update'),array('class'=>'mt15 btnSellerAdd')); ?>

 
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.store', '门店名称'); ?> </th>
        <td width="90%">
            <?php echo $model->name; ?>
        </td>
    </tr>
    
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.store', 'logo'); ?> </th>
        <td width="90%">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->logo ?>" width="250"/>
        </td>
    </tr>
    
       <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.store', '店铺分类'); ?> </th>
        <td width="90%">
            <?php echo isset($cate->name)?$cate->name:'' ?>
        </td>
    </tr>
    
       <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.store', '是否送货上门'); ?> </th>
        <td width="90%">
            <?php echo Supermarkets::getDelivery($model->is_delivery); ?>
        </td>
    </tr>
    <?php if($model->is_delivery == Supermarkets::DELIVERY_YES):?>
      <tr>
            <th><?php echo Yii::t('partnerModule.store', '起送金额'); ?></th>
            <td>
                <?php echo $model->delivery_start_amount; ?>
            </td>
        </tr>

        <tr>
            <th><?php echo Yii::t('partnerModule.store', '免费配送最低金额'); ?></th>
            <td>
                <?php echo$model->delivery_mini_amount; ?>
            </td>
        </tr>

        <tr>
            <th><?php echo Yii::t('partnerModule.store', '送货上门附加服务费'); ?></th>
            <td>
                <?php echo$model->delivery_fee; ?>
            </td>
        </tr>
    
    <?php    endif;?>
    
      <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.store', '营业时间'); ?> </th>
        <td width="90%">
            <?php echo $model->open_time; ?>
        </td>
    </tr>
    
          <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.store', '送货时间'); ?> </th>
        <td width="90%">
            <?php echo $model->delivery_time; ?>
        </td>
    </tr>
    
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.store', '所在地区'); ?> </th>
        <td width="90%">
            <?php echo Region::getName($model->province_id, $model->city_id, $model->district_id) ?>       
        </td>
    </tr>
     <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.store', '详细地址') ?> </th>
        <td width="90%">

            <?php echo CHtml::encode($model->street) ?>
        </td>
    </tr>
    
      <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.store', '邮编'); ?> </th>
        <td width="90%">
             <?php echo CHtml::encode($model->zip_code) ?>
        </td>
    </tr>
    
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.store', '联系电话'); ?> </th>
        <td width="90%">
            <?php echo $model->mobile; ?>
        </td>
    </tr>  
            
           <tr>
            <th><?php echo Yii::t('partnerModule.store', '纬度'); ?></th>
            <td>
                <?php echo$model->lng; ?>
            </td>
        </tr>
        
             <tr>
            <th><?php echo Yii::t('partnerModule.store', '经度'); ?></th>
            <td>
                <?php echo$model->lat; ?>
            </td>
        </tr>
  
        <tr>
            <th><?php echo Yii::t('store','状态') ?></th>
            <td>
               <?php echo Supermarkets::getStatus($model->status);?>
            </td>
        </tr>
    
    </tbody>
</table>