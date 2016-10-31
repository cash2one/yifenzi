<?php
/** @var $this GameStoreController */
/** @var $model GameStore */
/** @var $form CActiveForm */
$title = Yii::t('gameStore', '店铺基本设置');
$this->pageTitle = $title . '-' . $this->pageTitle;
$this->breadcrumbs = array(
    Yii::t('gameStore', '游戏店铺管理') => array('view'),
    $title,
);
?>
<div class="toolbar">
    <b><?php echo Yii::t('gameStore', '编辑游戏店铺'); ?></b>
</div>
<h3 class="mt15 tableTitle"><?php echo Yii::t('gameStore', '基本信息'); ?></h3>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
));
?>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
    <tr>
        <th width="10%"><?php echo $form->labelEx($model, 'store_name'); ?></th>
        <td>
             <?php echo $form->textField($model, 'store_name', array('class' => 'inputtxt1','readonly' => true)); ?>
             <?php echo $form->error($model, 'store_name'); ?>
        </td>
    </tr>
    <tr>
        <th width="120px">
            <?php echo $form->labelEx($model, 'store_phone'); ?>：
        </th>
        <td>
            <?php echo $form->textField($model, 'store_phone', array('class' => 'inputtxt1')); ?>
            <?php echo $form->error($model, 'store_phone'); ?>
        </td>
    </tr>
    <tr>
        <th width="120px">
            <?php echo $form->labelEx($model, 'store_address'); ?>：
        </th>
        <td>
            <?php echo $form->textField($model, 'store_address', array('class' => 'inputtxt1','style' => 'width:300px;')); ?>
            <?php echo $form->error($model, 'store_address'); ?>
        </td>
    </tr>
    <tr>
        <th width="120px">
            <?php echo $form->labelEx($model, 'store_status'); ?>：
        </th>
        <td>
            <?php echo $form->radioButtonList($model, 'store_status', GameStore::status(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'store_status'); ?>
        </td>
    </tr>
    <tr>
        <th width="120px" class="even">
            <?php echo "已抢限制";?>：
        </th>
        <td class="even">
            <?php echo $form->textField($model, 'limit_time_hour', array('class' => 'inputtxt1','style' => 'width:40px;'));?>小时
            <?php echo $form->error($model, 'limit_time_hour'); ?>
            <?php echo $form->textField($model, 'limit_time_minute', array('class' => 'inputtxt1','style' => 'width:40px;')); ?>分钟(限定填写0-24小时，0-60分钟)
            <?php echo $form->error($model, 'limit_time_minute'); ?>
        </td>
    </tr>
    </tbody>
</table>
<div class="profileDo mt15">
    <a href="#" class="sellerBtn03" id="submitBtn">
        <span><?php echo Yii::t('sellerStore', '保存'); ?></span></a>&nbsp;&nbsp;
    <a href="<?php echo $this->createAbsoluteUrl('view') ?>" class="sellerBtn01">
        <span><?php echo Yii::t('sellerStore', '返回'); ?></span></a>
</div>

<?php $this->endWidget() ?>
<script>
    $("#submitBtn").click(function() {
        $('form').submit();
    });
</script>