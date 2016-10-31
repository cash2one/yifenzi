<?php
/**
 * @author zhenjun_xu <412530435@qq.com>
 * Date: 2016/1/12 0012
 * Time: 21:03
 * @var $this MController
 * @var $model GuadanRule
 * @var $form CActiveForm
 */
?>
    <script>
        if (typeof success != 'undefined') {
            parent.location.reload();
            art.dialog.close();
        }
    </script>
    <style>
        .searchTable {
            line-height: 30px;
            float: none;
        }

        .searchTable td {
            padding: 10px;
        }
        .required{
            width:100px;
            display:inline-block;
        }
    </style>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
));
?>
    <table class="searchTable">
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'title') ?>:
                <?php echo $form->textField($model, 'title', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'title') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'amount') ?>:
                <?php echo $form->textField($model, 'amount', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'amount') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'amount_pay') ?>:
                <?php echo $form->textField($model, 'amount_pay', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'amount_pay') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'amount_give') ?>:
                <?php echo $form->textField($model, 'amount_give', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'amount_give') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'give_installment') ?>:
                <?php echo $form->textField($model, 'give_installment', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'give_installment') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'amount_installment') ?>:
                <?php echo $form->textField($model, 'amount_installment', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'amount_installment') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'installment_time') ?>:
                <?php echo $form->textField($model, 'installment_time', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'installment_time') ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $form->labelEx($model, 'amount_limit') ?>:
                <?php echo $form->textField($model, 'amount_limit', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'amount_limit') ?>
            </td>
        </tr>
        <tr>
            <td>
                <label class="remark">优惠说明&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp : </label>
<!--                --><?php //echo $form->labelEx($model, 'remark',array('class' => 'remark')) ?><!--:-->
                <?php echo $form->textArea($model, 'remark', array('class' => 'text-input-bj  middle')) ?>
                <?php echo $form->error($model, 'remark') ?>
            </td>
        </tr>
    </table>
<style>
    .remark{
        float:left;
        width: 110px;
    }
</style>


    <table class="searchTable">

        <tr>
            <td>
                <?php echo CHtml::submitButton($model->isNewRecord ? "新增" : "编辑", array('class' => 'regm-sub')) ?>
            </td>
        </tr>
    </table>

<?php $this->endWidget() ?>