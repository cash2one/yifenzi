<?php
/* @var $this FranchiseeArtileController */
/* @var $model FranchiseeArtile */

$this->breadcrumbs = array(
    Yii::t('partnerModule.freshMachine', '生鲜机管理') => array('FreshLine?mid=' . $mid),
    Yii::t('partnerModule.freshMachine', '修改生鲜机货道'),
);
?>

<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.freshMachine', '修改生鲜机货道'); ?></h3>
</div>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true, //客户端验证
    ),
        ));
?>
<style>
    .regm-sub{
        border:1px solid #ccc;
        background: #fff;
        padding: 5px;
        border-radius: 3px;
        cursor: pointer;
    }
</style>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>

        <tr>
            <th width="10%" align="right"><?php echo $form->labelEx($model, 'name'); ?>：</th>
            <td>           
                <?php echo $form->textField($model, 'name', array('class' => 'inputtxt1', 'style' => 'width:300px;')); ?>
                <?php echo $form->error($model, 'name'); ?>
            </td>
        </tr>
        <tr >
            <th align="right"><?php echo $form->labelEx($model, 'code'); ?>：</th>
            <td >  
                <?php echo $form->textField($model, 'code', array('class' => 'inputtxt1', 'style' => 'width:300px;')); ?>  (格式一般为：L12)
                <?php echo $form->error($model, 'code'); ?>
            </td>

        </tr>
        <tr>    
            <th align="right"><?php echo $form->labelEx($model, 'status'); ?>：</th>
            <td>
                <?php echo $form->dropDownList($model, 'status', FreshMachineLine::getStatus(), array('class' => 'inputtxt1')); ?>
                <?php echo $form->error($model, 'status'); ?>
            </td>
        </tr>
        <tr>
            <th align="right">
                <?php echo $form->labelEx($model, 'expir_time'); ?>：
            </th>
            <td >
                <?php $this->widget('comext.timepicker.timepicker', array('model' => $model, 'name' => 'expir_time', 'options' => array(), 'cssClass' => 'inputtxt1')); ?><b class="red">（<?php echo Yii::t('partnerModule.freshMachine','不填则表示永不过期');?>）
                <?php echo $form->error($model, 'expir_time'); ?>
            </td>
        </tr>
        <tr>
            <th align="right"><?php echo Yii::t('partnerModule.freshMachine', '租用货道商家盖网号') ?>：</th>
            <td >  
                <?php echo $form->textField($model, 'gai_number', array('class' => 'inputtxt1', 'style' => 'width:300px;')); ?><b class="red">(<?php echo Yii::t('partnerModule.freshMachine','* 修改商家GW号将会取消占用货道的商品订单，下架商品');?>)
                <?php echo $form->error($model, 'gai_number'); ?>
            </td>
        </tr>


    </tbody>
</table>
<?php $this->endWidget(); ?>

<div class="profileDo mt15">
    <a href="javascript:void(0);" class="sellerBtn03" onclick="$('#freshMachine-form').submit();"><span><?php echo $model->isNewRecord ? Yii::t('partnerModule.freshMachine', '添加') : Yii::t('partnerModule.freshMachine', '确定'); ?></span></a>&nbsp;&nbsp;<a href="javascript:history.go(-1);" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.freshMachine', '返回'); ?></span></a>
</div>

<script type="text/javascript" language="javascript" src="<?php echo DOMAIN ?>/js/iframeTools.source.js"></script>
