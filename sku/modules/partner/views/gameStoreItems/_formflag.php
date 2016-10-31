<?php
//
/* @var $this GameStoreItemsController */
/* @var $model GameStoreItems */
/* @var $form CActiveForm */
?>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id .'-form',
//     'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
));
?>
<div class="mainContent">
    <div class="toolbar">
        <h3><?php echo $model->isNewRecord ? Yii::t('gameStoreItems', '添加特殊商品') : Yii::t('gameStoreItems', '编辑特殊商品') ?> </h3>
    </div>
    <h3 class="mt15 tableTitle"><?php echo Yii::t('gameStoreItems', '商品信息') ?></h3>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
        <tbody><tr>
            <th width="10%"><b class="red">*</b><?php echo Yii::t('gameStoreItems', '商品名称') ?></th>
            <td width="90%">
                <?php echo $model->isNewRecord ? $form->textField($model, 'item_name', array('class' => 'inputtxt1', 'style' => 'width:300px')) : $form->textField($model, 'item_name', array('class' => 'inputtxt1', 'style' => 'width:300px', 'readonly' => true)); ?>
                <?php echo $form->error($model, 'item_name') ?>
            </td>
        </tr>
        <tr>
            <th><b class="red">*</b><?php echo Yii::t('gameStoreItems', '每日提供数量') ?></th>
            <td width="90%">
                <?php echo $form->textField($model, 'item_number', array('class' => 'inputtxt1', 'style' => 'width:50px')); ?>
                <?php echo $form->error($model, 'item_number') ?>
            </td>
        </tr>
        <tr>
            <th><b class="red">*</b><?php echo Yii::t('gameStoreItems', '商品状态') ?></th>
            <td>
                <?php echo $form->radioButtonList($model, 'item_status', $model::status(), array('separator' => ' ')) ?>
                <?php echo $form->error($model, 'item_status') ?>
            </td>
        </tr>
        <tr>
            <th><?php echo Yii::t('gameStoreItems', '商品描述') ?></th>
            <td>
                <?php echo $form->textField($model, 'item_description', array('class' => 'inputtxt1','style' => 'width:600px')); ?><b>(限制输入20个中文)</b>
                <?php echo $form->error($model, 'item_description') ?>
            </td>
        </tr>
        <tr>
            <th><?php echo Yii::t('gameStoreItems', '商家描述') ?></th>
            <td>
                <?php echo $form->textField($model, 'store_description', array('class' => 'inputtxt1','style' => 'width:600px')); ?><b>(限制输入10个中文)</b>
                <?php echo $form->error($model, 'store_description') ?>
            </td>
        </tr>
        <tr>
            <th><b class="red">*</b><?php echo Yii::t('gameStoreItems', '活动开始日期') ?></th>
            <td>
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'id'=>'start_date',
                    'model'=>$model,
                    'name' => 'start_date',
                    'select'=>'date',
                    'htmlOptions' => array(
                        'readonly' => 'readonly',
                        'class' => 'inputtxt1 readonly',
                    )
                ));
                ?>
                <?php echo $form->error($model, 'start_date') ?>
            </td>
        </tr>
        <tr>
            <th><b class="red">*</b><?php echo Yii::t('gameStoreItems', '活动结束日期') ?></th>
            <td>
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'id'=>'end_date',
                    'model'=>$model,
                    'name' => 'end_date',
                    'select'=>'date',
                    'htmlOptions' => array(
                        'readonly' => 'readonly',
                        'class' => 'inputtxt1 readonly',
                    )
                ));
                ?>
                <?php echo $form->error($model, 'end_date') ?>
            </td>
        </tr>
        <tr>
            <th><b class="red">*</b><?php echo Yii::t('gameStoreItems', '每日开抢时间') ?></th>
            <td>
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'id'=>'start_time',
                    'model'=>$model,
                    'name' => 'start_time',
                    'select'=>'time',
                    'htmlOptions' => array(
                        'readonly' => 'readonly',
                        'class' => 'inputtxt1 readonly',
                    )
                ));
                ?>
                <?php echo $form->error($model, 'start_time') ?>
            </td>
        </tr>
        <tr>
            <th><b class="red">*</b><?php echo Yii::t('gameStoreItems', '每日结束时间') ?></th>
            <td>
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'id'=>'end_time',
                    'model'=>$model,
                    'name' => 'end_time',
                    'select'=>'time',
                    'htmlOptions' => array(
                        'readonly' => 'readonly',
                        'class' => 'inputtxt1 readonly',
                    )
                ));
                ?>
                <?php echo $form->error($model, 'end_time') ?>
            </td>
        </tr>
        <tr>
            <th><b class="red">*</b><?php echo Yii::t('gameStoreItems', '单次获得数量') ?></th>
            <td>

                <?php echo $form->textField($model, 'limit_per_time', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
                <?php echo $form->error($model, 'limit_per_time') ?>
            </td>
        </tr>
        <tr>
            <th><b class="red">*</b><?php echo Yii::t('gameStoreItems', '概率') ?></th>
            <td>
                <?php echo $form->textField($model, 'probability', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>(可设置0-9，0--抢到水果概率为100%，9为10%)
                <?php echo $form->error($model, 'probability') ?>
            </td>
        </tr>
        </tbody></table>
    <div class="profileDo mt15">
        <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('gameStoreItems', '新增') : Yii::t('gameStoreItems', '保存'), array('class' => 'sellerBtn06')); ?>
    </div>
</div>
<?php $this->endWidget(); ?>
<script>
    $("#GameStoreItems_limit_per_time").change(function() {
        var number = parseInt($(this).val());
        var total = parseInt($("#GameStoreItems_item_number").val());
        if(number > total){
            $(this).val(total);
        }
    });
</script>
