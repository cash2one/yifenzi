<?php
/* @var $this SmsLogController */
/* @var $model SmsLog */

$this->breadcrumbs = array(
    Yii::t('onepartPay', '支付管理') => array('admin'),
    '支付列表',
);

?>

<style>
    .tab-come th{text-align: center;}
</style>

<?php $form = $this->beginWidget('CActiveForm',$formConfig);?>

<table width="100%" border="0" class="tab-come" cellspacing="0" cellpadding="0">
    <tbody>
    <?php foreach($PayData as $k=>$v):?>
        <tr>
            <th style="width: 180px">
                <?php echo $v->payment_name; ?>：
            </th>

            <th style="width: 120px">
                <?php echo $form->labelEx($v,strtolower($v->payment_code).'_enabled');?>：
            </th>
            <td>
                <?php echo $form->radioButtonList($v,strtolower($v->payment_code).'_enabled',YfzPayment::getEnabledStatus(),array('separator'=>''))?>
                <?php echo $form->error($v, strtolower($v->payment_code).'_enabled'); ?>
            </td>

            <th style="width: 120px">

            </th>
            <td>

            </td>
        </tr>
    <?php endforeach;?>

    <tr>
        <td colspan="2">
            <?php echo CHtml::submitButton(Yii::t('home', '保存'), array('class' => 'reg-sub')) ?>
        </td>
    </tr>
    </tbody>
</table>
<?php $this->endWidget();?>

