<?php
/* @var $this SupermarketsController */
/* @var $model Supermarkets */
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="tab1">
    <tr>
        <td colspan="6" class="title-th">
            <?php echo Yii::t('supermarkets', '门店信息'); ?>
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
            <?php echo $model->getAttributeLabel('logo') ?>：
        </th>
        <td>
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->logo ?>" width="250"/>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo Yii::t('supermarkets', '地区') ?>：
        </th>
        <td>
            <?php echo Region::getName($model->province_id, $model->city_id, $model->district_id) ?>         
        </td>
    </tr>
    
     <tr>
        <th align="right">
            <?php echo Yii::t('supermarkets', '地址') ?>：
        </th>
        <td>         
            <?php echo CHtml::encode($model->street) ?> ( <?php echo CHtml::encode($model->zip_code) ?> )
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo Yii::t('supermarkets', '联系电话'); ?>：
        </th>
        <td>
            <?php echo $model->mobile; ?>
        </td>
    </tr>

    <tr>
        <th align="right">
            <?php echo Yii::t('supermarkets', '状态'); ?>：
        </th>
        <td>
            <?php echo Partners::getStatus($model->status); ?>
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
            <?php echo Yii::t('supermarkets', '操作'); ?>：
        </th>
        <td>
            <input id="Btn_Add" type="button" value="审核通过" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl('/manage/supermarkets/apply', array('id' => $model->id, 'apply' => 'pass')); ?>'">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input id="Btn_Add" type="button" value="审核不通过" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl('/manage/supermarkets/apply', array('id' => $model->id, 'apply' => 'unpass')); ?>'">

        </td>
    </tr>

</table>