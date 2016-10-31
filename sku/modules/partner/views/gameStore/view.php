<?php
/** @var $this GameStoreController */
/** @var $model GameStore */
$title = Yii::t('gameStore', '查看游戏店铺');
$this->pageTitle = $title . '-' . $this->pageTitle;
$this->breadcrumbs = array(
    Yii::t('gameStore', '游戏店铺管理') => array('view'),
    $title,
);
?>
<div class="toolbar">
    <b><?php echo $title ?></b>
    <span><?php echo Yii::t('gameStore', '查看游戏店铺基本信息'); ?></span>
</div>
<?php echo CHtml::link(Yii::t('gameStore', '编辑游戏店铺'), $this->createAbsoluteUrl('/partner/gameStore/update',array('id' => $model->id)), array('class' => 'sellerBtn08')); ?>
<?php echo CHtml::link(Yii::t('gameStore', '游戏店铺商品'), $this->createAbsoluteUrl('/partner/gameStoreItems/index',array('id' => $model->id)), array('class' => 'sellerBtn08')); ?>
<?php echo CHtml::link(Yii::t('gameStore', '店铺用户列表'), $this->createAbsoluteUrl('/partner/gameStoreMember/index',array('id' => $model->id)), array('class' => 'sellerBtn08')); ?>
<?php echo CHtml::link(Yii::t('gameStore', '店铺发货列表'), $this->createAbsoluteUrl('/partner/gameStoreDelivery/index',array('id' => $model->id)), array('class' => 'sellerBtn08')); ?>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
    <tr>
        <th width="10%"><?php echo $model->getAttributeLabel('store_name') ?>：</th>
        <td>
                <?php echo $model->store_name; ?>
        </td>
    </tr>
    <tr>
        <th width="120px">
            <?php echo $model->getAttributeLabel('store_phone') ?>：
        </th>
        <td>
            <?php echo $model->store_phone;?>
        </td>
    </tr>
    <tr>
        <th width="120px">
            <?php echo $model->getAttributeLabel('store_address') ?>：
        </th>
        <td>
            <?php echo CHtml::encode($model->store_address);?>
        </td>
    </tr>
    <tr>
        <th width="120px">
            <?php echo $model->getAttributeLabel('store_status') ?>：
        </th>
        <td>
            <?php echo GameStore::status($model->store_status);?>
        </td>
    </tr>
    <tr>
        <th><?php echo $model->getAttributeLabel('limit_time_hour') ?></th>
        <td><b class="red"><?php echo $model->limit_time_hour;?><?php echo Yii::t('gameStore', '小时'); ?></b></td>
    </tr>
    <tr>
        <th><?php echo $model->getAttributeLabel('limit_time_minute') ?></th>
        <td><b class="red"><?php echo $model->limit_time_minute;?><?php echo Yii::t('gameStore', '分钟'); ?></b></td>
    </tr>
    </tbody>
</table>