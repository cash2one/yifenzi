<?php
$this->breadcrumbs=array(
    Yii::t('partnerModule.partner','合作商家管理'),
    Yii::t('partnerModule.partner','查看资料'),
);

?>

<h3 class="mt15 tableTitle"> <?php echo $model->name; ?> <?php echo Yii::t('partnerModule.partner', '的'); ?> <?php echo Yii::t('partnerModule.partner', '详细资料信息'); ?></h3>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '商家名称'); ?> </th>
        <td width="90%">
            <?php echo $model->name; ?>
        </td>
    </tr>

    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '头像'); ?> </th>
        <td width="90%">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->head ?>" width="120"/>
        </td>
    </tr>
    
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '所在地区'); ?> </th>
        <td width="90%">
            <?php echo Region::getName($model->province_id, $model->city_id, $model->district_id) ?>          
        </td>
    </tr>
    
     <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '详细地址'); ?> </th>
        <td width="90%">

            <?php echo CHtml::encode($model->street) ?>
        </td>
    </tr>
    
     <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '邮政编码'); ?> </th>
        <td width="90%">

   <?php echo CHtml::encode($model->zip_code) ?> 
        </td>
    </tr>
    
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '联系电话'); ?> </th>
        <td width="90%">
            <?php echo $model->mobile; ?>
        </td>
    </tr>
    
        <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '状态'); ?> </th>
        <td width="90%">
            <?php echo Partners::getStatus($model->status); ?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '银行卡'); ?> </th>
        <td width="90%">
            <?php echo $model->bank_account; ?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '账户名'); ?> </th>
        <td width="90%">
            <?php echo $model->bank_account_name; ?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '银行名称'); ?> </th>
        <td width="90%">
            <?php echo $model->bank_name; ?>
        </td>
    </tr>

    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '银行所属地'); ?> </th>
        <td width="90%">
            <?php echo $model->bank_area; ?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '开户支行'); ?> </th>
        <td width="90%">
            <?php echo $model->bank_account_branch; ?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '银行卡图片'); ?> </th>
        <td width="90%">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->bank_card_img ?>" width="120"/>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '身份证'); ?> </th>
        <td width="90%">
            <?php echo $model->idcard; ?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '身份证正面照片'); ?> </th>
        <td width="90%">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_font ?>" width="120"/>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '身份证反面照片'); ?> </th>
        <td width="90%">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_back ?>" width="120"/>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '营业执照'); ?> </th>
        <td width="90%">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->license_img ?>" width="120"/>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '营业执照过期时间'); ?> </th>
        <td width="90%">
            <?php echo date('Y-m-d',(int)$model->license_expired_time);?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '肉菜检验证明'); ?> </th>
        <td width="90%">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->meat_inspection_certificate_img ?>" width="120"/>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '肉菜检验证明过期时间'); ?> </th>
        <td width="90%">
            <?php echo date('Y-m-d',(int)$model->meat_inspection_expired_time);?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '卫生许可证明'); ?> </th>
        <td width="90%">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->health_permit_certificate_img ?>" width="120"/>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '卫生许可证明过期时间'); ?> </th>
        <td width="90%">
            <?php echo date('Y-m-d',(int)$model->health_permit_expired_time);?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '食品流通许可证明'); ?> </th>
        <td width="90%">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->food_circulation_permit_certificate_img ?>" width="120"/>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '食品流通许可证明过期时间'); ?> </th>
        <td width="90%">
            <?php echo date('Y-m-d',(int)$model->food_circulation_expired_time);?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '进货来源证明'); ?> </th>
        <td width="90%">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->stock_source_certificate_img ?>" width="120"/>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo Yii::t('partnerModule.partner', '进货来源证明过期时间'); ?> </th>
        <td width="90%">
            <?php echo date('Y-m-d',(int)$model->stock_source_expired_time);?>
        </td>
    </tr>
    </tbody>
</table>


