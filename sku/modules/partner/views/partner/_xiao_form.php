<?php
/* @var $this PartnerController */
/* @var $model Xiaoer */
/* @var $form CActiveForm */
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
             <th><span style="color:red">*</span><?php echo Yii::t('xiaoEr','盖网号'); ?>：</th>
        <td>
            <?php echo $form->textField($model, 'gai_number', array('class' => 'inputtxt1', 'style' => 'width:300px',
                )); ?>
            <?php echo $form->error($model, 'gai_number'); ?>
        </td>
    </tr>
    
        <tr>
            <th><?php echo $form->labelEx($model, 'status'); ?>：</th>
            <td>
                <?php echo $form->radioButtonList($model, 'status', $model::getStatus(), array('separator' => '&nbsp')) ?>
                <?php echo $form->error($model, 'status') ?>
            </td>
        </tr>
       
  
    </tbody>
</table>


<div class="profileDo mt15">
 
 <a href="#" class="sellerBtn03 submitBt"><span><?php echo Yii::t('partnerModule.superGoods', '保存'); ?></span></a>
  
</div>
<?php $this->endWidget(); ?>

<script>
    $(".submitBt").click(function () {
        $("form").submit();
    });
</script>