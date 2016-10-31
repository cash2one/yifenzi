<?php
/* @var $this OrderController */
/* @var $model Order */
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="tab1">
    <tr>
        <td colspan="6" class="title-th">
            <?php echo Yii::t('order', '商家信息'); ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('name') ?>：
        </th>
        <td>
            <?php echo $model->name ?>
        </td>
     </tr>
     <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('head') ?>：
        </th>
        <td>
        <a href="<?php echo ATTR_DOMAIN . '/' . $model->head ?>" target="_blank">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->head ?>" width="250"/>
            </a>
        </td>
        </tr>
     <tr>
        <th align="right">
            <?php echo Yii::t('order', '联系地址') ?>：
        </th>
        <td>
            <?php echo Region::getName($model->province_id, $model->city_id, $model->district_id) ?>
            <br/>
            <?php echo CHtml::encode($model->street) ?> ( <?php echo CHtml::encode($model->zip_code) ?> )
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo Yii::t('order', '联系电话'); ?>：
        </th>
        <td>
        	<?php echo $model->mobile; ?>
        </td>
	</tr>

    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('zip_code') ?>：
        </th>
        <td>
            <?php echo $model->zip_code; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('bank_account') ?>：
        </th>
        <td>
            <?php echo $model->bank_account; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('bank_account_name') ?>：
        </th>
        <td>
            <?php echo $model->bank_account_name; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('bank_card_img') ?>：
        </th>
        <td>
        <a href="<?php echo ATTR_DOMAIN . '/' . $model->bank_card_img ?>" target="_blank">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->bank_card_img ?>" width="250"/>
            </a>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('bank_name') ?>：
        </th>
        <td>
            <?php echo $model->bank_name; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('bank_area') ?>：
        </th>
        <td>
            <?php echo $model->bank_name; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('bank_account_branch') ?>：
        </th>
        <td>
            <?php echo $model->bank_account_branch; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('idcard') ?>：
        </th>
        <td>
            <?php echo $model->idcard; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('idcard_img_font') ?>：
        </th>
        <td>
        <a href="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_font ?>" target="_blank">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_font ?>" width="250"/>
            </a>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('idcard_img_back') ?>：
        </th>
        <td>
        <a href="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_back ?>" target="_blank">
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_back ?>" width="250"/>
            </a>
        </td>
    </tr>

    <?php if(!empty($model->license_img)){ ?>
        <tr>
            <th align="right">
                <?php echo $model->getAttributeLabel('license_img') ?>：
            </th>
            <td>
            <a href="<?php echo ATTR_DOMAIN . '/' . $model->license_img ?>" target="_blank">
                <img src="<?php echo ATTR_DOMAIN . '/' . $model->license_img ?>" width="250"/>
                </a>
            </td>
        </tr>
        <tr>
            <th align="right">
                <?php echo $model->getAttributeLabel('license_expired_time') ?>：
            </th>
            <td>
                <?php echo date('Y-m-d',(int)$model->license_expired_time); ?>
            </td>
        </tr>
    <?php } ?>
    <?php if(!empty($model->meat_inspection_certificate_img)){ ?>
        <tr>
            <th align="right">
                <?php echo $model->getAttributeLabel('meat_inspection_certificate_img') ?>：
            </th>
            <td>
            <a href="<?php echo ATTR_DOMAIN . '/' . $model->meat_inspection_certificate_img ?>" target="_blank">
                <img src="<?php echo ATTR_DOMAIN . '/' . $model->meat_inspection_certificate_img ?>" width="250"/>
                </a>
            </td>
        </tr>
        <tr>
            <th align="right">
                <?php echo $model->getAttributeLabel('meat_inspection_expired_time') ?>：
            </th>
            <td>
                <?php echo date('Y-m-d',(int)$model->meat_inspection_expired_time); ?>
            </td>
        </tr>
    <?php } ?>
    <?php if(!empty($model->health_permit_certificate_img)){ ?>
        <tr>
            <th align="right">
                <?php echo $model->getAttributeLabel('health_permit_certificate_img') ?>：
            </th>
            <td>
            <a href="<?php echo ATTR_DOMAIN . '/' . $model->health_permit_certificate_img ?>" target="_blank">
                <img src="<?php echo ATTR_DOMAIN . '/' . $model->health_permit_certificate_img ?>" width="250"/>
                </a>
            </td>
        </tr>
        <tr>
            <th align="right">
                <?php echo $model->getAttributeLabel('health_permit_expired_time') ?>：
            </th>
            <td>
                <?php echo date('Y-m-d',(int)$model->health_permit_expired_time); ?>
            </td>
        </tr>      
    <?php } ?>
    <?php if(!empty($model->food_circulation_permit_certificate_img)){ ?>
        <tr>
            <th align="right">
                <?php echo $model->getAttributeLabel('food_circulation_permit_certificate_img') ?>：
            </th>
            <td>
            <a href="<?php echo ATTR_DOMAIN . '/' . $model->food_circulation_permit_certificate_img ?>" target="_blank">
                <img src="<?php echo ATTR_DOMAIN . '/' . $model->food_circulation_permit_certificate_img ?>" width="250"/>
                </a>
            </td>
        </tr>
        <tr>
            <th align="right">
                <?php echo $model->getAttributeLabel('food_circulation_expired_time') ?>：
            </th>
            <td>
                <?php echo date('Y-m-d',(int)$model->food_circulation_expired_time); ?>
            </td>
        </tr>
    <?php } ?>
    <?php if(!empty($model->stock_source_certificate_img)){ ?>
        <tr>
            <th align="right">
                <?php echo $model->getAttributeLabel('stock_source_certificate_img') ?>：
            </th>
            <td>
            <a href="<?php echo ATTR_DOMAIN . '/' . $model->stock_source_certificate_img ?>" target="_blank">
                <img src="<?php echo ATTR_DOMAIN . '/' . $model->stock_source_certificate_img ?>" width="250"/>
                </a>
            </td>
        </tr>
        <tr>
            <th align="right">
                <?php echo $model->getAttributeLabel('stock_source_expired_time') ?>：
            </th>
            <td>
                <?php echo date('Y-m-d',(int)$model->stock_source_expired_time); ?>
            </td>
        </tr>
    <?php } ?>
	 <tr>
            <th align="right">
                <?php echo Yii::t('partners','运营方GW号'); ?>：
            </th>
            <td>
               <?php echo $bdgw;?>
            </td>
        </tr>
	<tr>
        <th align="right">
            <?php echo Yii::t('order', '状态'); ?>：
        </th>
        <td>
        	<?php echo Partners::getStatus($model->status); ?>
        </td>
	</tr>
	
	<tr>
        <th align="right">
            <?php echo Yii::t('order', '操作'); ?>：
        </th>
        <td>
        	<input id="Btn_Add" type="button" value="审核通过" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl('/manage/partners/apply',array('id'=>$model->id,'apply'=>'pass'));?>'">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       		<input id="Btn_Add" type="button" value="审核不通过" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl('/manage/partners/apply',array('id'=>$model->id,'apply'=>'unpass'));?>'">
        
        </td>
	</tr>
	
</table>