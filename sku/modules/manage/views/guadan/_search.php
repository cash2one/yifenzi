<?php
/* @var $this ProductController */
/* @var $model Product */
/* @var $form CActiveForm */
?>
<style>
    <!--
    .search-form{ line-height:45px; }
    -->
</style>
<div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
         'enableClientValidation' => true,
        'clientOptions' => array(
        'validateOnSubmit' => true,
    ),

    ));
    ?>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody>
            <tr>
                <th align="right">
                    <b><?php echo $form->label($model, 'gai_number'); ?>：
                </th>
                <td>
                    <?php echo $form->textField($model, 'gai_number', array('size' => 60, 'maxlength' => 64, 'class' => 'text-input-bj  middle')); ?>
                </td>
            </tr>
        </tbody>
    </table>


    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
                <td colspan="2">
                    <?php echo CHtml::submitButton('搜索', array('class' => 'reg-sub')) ?>
                </td>
            </tr></tbody>
    </table>
    

    <?php $this->endWidget(); ?>

    <br/>

    <b><label for="">excel导入挂单</label>：
                </b>
    
    
    <?php if(Yii::app()->user->checkAccess('Manage.Guadan.ExcelImport')):?>
    
 <?php
    $form = $this->beginWidget('CActiveForm', array(
    'id' => 'import-form',
	'action' => Yii::app()->createUrl('guadan/excelImport'),
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
?>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody>
        <tr>
        
        <td>
                <?php echo CHtml::label('类型:', 'type')?>
            </td>
            <td>
                <?php echo CHtml::dropDownList('type',Guadan::TYPE_TO_BIND,Guadan::getType())?>
            </td>

            <td>
                <?php echo $form->labelEx($upload_model, 'file') ?>
            </td>
            <td>
                <?php echo $form->fileField($upload_model, 'file') ?>
                <?php echo $form->error($upload_model, 'file', array(), false); ?>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <?php echo CHtml::submitButton('上传', array('class' => 'reg-sub')) ?>&nbsp;&nbsp;&nbsp;
                <?php if(Yii::app()->user->checkAccess('Manage.Guadan.GuadanImportTemplate')):?>
                <?php echo CHtml::link('模板',Yii::app()->createUrl('guadan/guadanImportTemplate'), array('class'=>'reg-sub ImportTemplate')) ?>
                <?php endif;?>
            </td>
        </tr>

        </tbody>
    </table>
<?php $this->endWidget(); ?>
    
    <?php endif;?>
</div>

<script>
    //导出模板传参，不同的模板下载不同的内容
$(".ImportTemplate").click(function(){
    document.location.href = this.href + '?type=' +$(".searchTable #type").val();
    return false;
});
</script>