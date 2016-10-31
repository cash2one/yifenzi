<?php
$this->breadcrumbs=array(
    Yii::t('partnerModule.storeStaffs','超市门店管理'),
    Yii::t('partnerModule.storeStaffs','门店信息'),
);

?>

<h3 class="mt15 tableTitle"> <?php echo $model->name; ?> <?php echo Yii::t('partnerModule.storeStaffs', '的'); ?> <?php echo Yii::t('partnerModule.storeStaffs', '门店信息'); ?></h3>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.storeStaffs', '门店名称'); ?> </th>
        <td width="90%">
            <?php echo $model->name; ?>
        </td>
    </tr>

    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.storeStaffs', 'logo'); ?> </th>
        <td width="90%">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->logo ?>" width="250"/>
        </td>
    </tr>
    
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.storeStaffs', '门店地址'); ?> </th>
        <td width="90%">
            <?php echo Region::getName($model->province_id, $model->city_id, $model->district_id) ?>
            <br/>
            <?php echo CHtml::encode($model->street) ?> ( <?php echo CHtml::encode($model->zip_code) ?> )
        </td>
    </tr>
    
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.storeStaffs', '电话'); ?> </th>
        <td width="90%">
            <?php echo $model->name; ?>
        </td>
    </tr>
    
    </tbody>
</table>