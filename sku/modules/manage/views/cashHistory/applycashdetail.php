<?php
/** @var $model CashHistory */
/** @var $memberModel Member */
/** @var $form  CActiveForm */

?>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'member-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
));
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come">
    <tr>
        <td colspan="2" style="text-align: center" class=" title-th">
            <?php echo Yii::t('cashHistory','会员基本信息'); ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right">
            <?php echo Yii::t('cashHistory','盖网编号'); ?>：
        </th>
        <td>
           <?php echo $memberModel->sku_number ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right">
            <?php echo Yii::t('cashHistory','会员用户名'); ?>：
        </th>
        <td>
            <?php echo $memberModel->username; ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right">
            <?php echo Yii::t('cashHistory','会员真实姓名'); ?>：
        </th>
        <td>
            <?php echo $memberModel->real_name ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right">
            <?php echo Yii::t('cashHistory','会员手机号'); ?>：
        </th>
        <td>
            <?php echo $memberModel->mobile ?>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center" class=" title-th">
            <?php echo Yii::t('cashHistory','会员银行帐号信息'); ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right">
            <?php echo Yii::t('cashHistory','开户行'); ?>：
        </th>
        <td>
            <?php echo $model->bank_name ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right">
            <?php echo Yii::t('cashHistory','银行地址'); ?>：
        </th>
        <td>
            <?php echo $model->bank_address ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right">
            <?php echo Yii::t('cashHistory','账户名'); ?>：
        </th>
        <td>
            <?php echo $model->account_name ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right">
            <?php echo Yii::t('cashHistory','银行帐号'); ?>：
        </th>
        <td>
            <?php echo $model->account ?>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center" class=" title-th">
            <?php echo Yii::t('cashHistory','申请兑现信息'); ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right" class="even">
            <?php echo Yii::t('cashHistory','申请金额'); ?>：
        </th>
        <td class="even">
            ￥ <?php echo $model->money ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right" class="odd">
            <?php echo Yii::t('cashHistory','手续费'); ?>：
        </th>
        <td class="odd">
            ￥ <?php echo $fee = sprintf('%0.2f',$model->money*$model->factorage/100) ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right" class="even">
            <?php echo Yii::t('cashHistory','手续费率'); ?>：
        </th>
        <td class="even">
            <?php echo $model->factorage ?>%
        </td>
    </tr>
    <tr>
        <th width="100" align="right" class="odd">
            <?php echo Yii::t('cashHistory','实际扣除'); ?>：
        </th>
        <td class="odd">
            ￥ <?php echo $model->money+$fee; ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right" class="even">
            <?php echo Yii::t('cashHistory','应转账金额'); ?>：
        </th>
        <td style="color: Red; font-weight: bold;" class="even">
            ￥ <?php echo $model->money ?>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center" class=" title-th">
            <?php echo Yii::t('cashHistory','操作'); ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right">
            <?php echo Yii::t('cashHistory','原因'); ?>：
        </th>
        <td>
           <?php if(in_array($model->status,array($model::STATUS_APPLYING,$model::STATUS_TRANSFERING,$model::STATUS_CHECKED))): ?>
                <?php echo $form->textArea($model,'reason',array('rows'=>'3','cols'=>'20','class'=>'text-input-bj  text-area valid')) ?>
               <br />
               <a class="copyToReason" href="#" ><?php echo Yii::t('cashHistory','余额不足'); ?></a>
               &nbsp; &nbsp; &nbsp; &nbsp;
               <a class="copyToReason" href="#" ><?php echo Yii::t('cashHistory','帐号信息不对'); ?></a>
               <?php else: ?>
               <?php echo $model->reason ?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right">
            <?php echo Yii::t('cashHistory','状态'); ?>：
        </th>
        <td id="tdStatus">
            <?php if(in_array($model->status,array($model::STATUS_APPLYING,$model::STATUS_TRANSFERING,$model::STATUS_CHECKED))): ?>
                <?php $status = $model::status();
                    //unset($status[$model::STATUS_APPLYING]);
                ?>
                <?php echo $form->radioButtonList($model,'status',$status,array('separator'=>' ')); ?>
            <?php else: ?>
                <?php echo $model::status($model->status) ?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th width="100" align="right">
            <?php if(in_array($model->status,array($model::STATUS_APPLYING,$model::STATUS_TRANSFERING,$model::STATUS_CHECKED))): ?>
                <?php echo CHtml::submitButton('保存',array('class'=>'reg-sub')) ?>
            <?php endif; ?>
        </th>
        <td id="tdStatus">

        </td>
    </tr>


</table>
<?php $this->endWidget(); ?>

<script>
    $(".copyToReason").click(function(){
        $("#CashHistory_reason").val($(this).html());
        return false;
    });
    $(":input[type='submit']").click(function(){
        var status = $("#CashHistory_status input:checked").val();
        var reason = $("#CashHistory_reason").val();
        if(status==<?php echo $model::STATUS_TRANSFERED ?> || status==<?php echo $model::STATUS_FAIL ?>){
            if (confirm("<?php echo Yii::t('cashHistory','您确认已审核，并保存信息？'); ?>")) {
                if (reason == "") {
                    alert("<?php echo Yii::t('cashHistory','请填写原因信息，若成功填写转账人等信息，若失败填写失败原因'); ?>");
                    return false;
                }
            }
            else {
                return false;
            }
        }
        return true;
    });
</script>