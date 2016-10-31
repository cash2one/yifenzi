<?php
/* @var $this GoodsController */
/* @var $model Goods */
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="tab1">
    <tr>
        <td colspan="2" class="title-th">
            <?php echo Yii::t('goods', '商品信息'); ?>
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
            <?php echo Yii::t('goods', '原始分类'); ?>：
        </th>
        <td>
            <?=  $cate?$cate->name:'未知分类' ?>
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo Yii::t('goods', '所属分类'); ?>：
        </th>
        <td>
            <?php echo isset($model->goodsCategory->name)?$model->goodsCategory->name:'未知分类' ?>
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
            <?php echo Yii::t('goods', '详情') ?>：
        </th>
        <td>
            <?php echo $model->content; ?>                  
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo Yii::t('goods', '售卖价格') ?>：
        </th>
        <td>
            <?php echo $model->price ?>                  
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo Yii::t('goods', '供货价格') ?>：
        </th>
        <td>
            <?php echo $model->supply_price ?>                  
        </td>
    </tr>
   
    
    <tr>
        <th align="right">
            <?php echo Yii::t('goods', '状态'); ?>：
        </th>
        <td>
            <?php echo VendingMachine::getStatus($model->status); ?>
        </td>
    </tr>

    <tr>
        <th align="right">
            <?php echo Yii::t('goods', '操作'); ?>：
        </th>
        <td>
            <input id="Btn_Add" type="button" value="审核通过" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl('/manage/goods/apply', array('id' => $model->id, 'apply' => 'pass')); ?>'">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input id="Btn_Add" type="button" value="审核不通过" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl('/manage/goods/apply', array('id' => $model->id, 'apply' => 'unpass')); ?>'">

        </td>
    </tr>

</table>