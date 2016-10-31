<?php
/* @var $this SupermarketsController */
/* @var $model Supermarkets */
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="tab1">
    <tr>
        <td colspan="2" class="title-th">
            <?php echo Yii::t('vendingmachine', '售货机信息'); ?>
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
            <?php echo Yii::t('vendingmachine', '装机编码'); ?>：
        </th>
        <td>
            <?php echo $model->code ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('thumb') ?>：
        </th>
        <td>
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->thumb ?>" width="250"/>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo Yii::t('vendingmachine', '地区') ?>：
        </th>
        <td>
            <?php echo Region::getName($model->province_id, $model->city_id, $model->district_id) ?>  <?php echo CHtml::encode($model->address) ?>                  
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo Yii::t('vendingmachine', '地址') ?>：
        </th>
        <td>
            <?php echo CHtml::encode($model->address) ?>                  
        </td>
    </tr>
    <tr>
        <th align="right">
             <?php echo $model->getAttributeLabel('user_id') ?>：
        </th>
        <td>
            <?php echo $model->user_id; ?>
        </td>
    </tr>
    
    <tr>
        <th align="right">
             <?php echo $model->getAttributeLabel('user_ip') ?>：
        </th>
        <td>
            <?php echo $model->user_ip; ?>
        </td>
    </tr>

     <tr>
        <th align="right">
             <?php echo $model->getAttributeLabel('lat') ?>：
        </th>
        <td>
            <?php echo $model->lat; ?>
        </td>
    </tr>
    
     <tr>
        <th align="right">
             <?php echo $model->getAttributeLabel('lng') ?>：
        </th>
        <td>
            <?php echo $model->lng; ?>
        </td>
    </tr>
    
    <tr>
        <th align="right">
             <?php echo $model->getAttributeLabel('setup_time') ?>：
        </th>
        <td>
            <?php echo empty($model->setup_time) ? '':date('Y-m-d H:i:s',$model->setup_time);?>
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo Yii::t('vendingmachine', '状态'); ?>：
        </th>
        <td>
            <?php echo VendingMachine::getStatus($model->status); ?>
        </td>
    </tr>
    
    <tr>
        <th align="right">
             <?php echo $model->getAttributeLabel('fee') ?>：
        </th>
        <td>
            <?php echo $model->fee; ?>%
        </td>
    </tr>

    <tr>
        <th align="right">
            <?php echo Yii::t('vendingmachine', '操作'); ?>：
        </th>
        <td>
            <input id="Btn_Add" type="button" value="审核通过" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl('/manage/vendingMachine/apply', array('id' => $model->id, 'apply' => 'pass')); ?>'">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input id="Btn_Add" type="button" value="审核不通过" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl('/manage/vendingMachine/apply', array('id' => $model->id, 'apply' => 'unpass')); ?>'">

        </td>
    </tr>

</table>