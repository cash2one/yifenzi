<?php
$this->breadcrumbs = array(
    Yii::t('gameStore', '游戏配置管理') => array('admin'),
    $model->isNewRecord ? Yii::t('gameStoreItems', '添加商品') : Yii::t('gameStoreItems', '修改商品')
);
?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'gameStoreItems-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
?>

    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
        <tbody>
        <tr>
            <td colspan="2" class="title-th even"
                align="center"><?php echo $model->isNewRecord ? Yii::t('gameStoreItems', '添加商品') : Yii::t('gameStoreItems', '修改商品'); ?></td>
        </tr>
        </tbody>
        <tbody>
        <tr>
            <th class="even">
                <?php echo $form->labelEx($model, 'flag'); ?>
            </th>
            <td class="even">
                <?php echo $model->isNewRecord ? $form->radioButtonList($model, 'flag', GameStoreItems::getFlagStatus(), array('separator' => '')) : $form->radioButtonList($model, 'flag', GameStoreItems::getFlagStatus(), array('separator' => '','disabled' => true)); ?>
                <?php echo $form->error($model, 'flag'); ?>
            </td>
        </tr>
        <tr>
        <tr>
            <th style="width: 220px" class="odd">
                <?php echo $form->labelEx($model, 'item_name'); ?>
            </th>
            <td class="odd">
                <?php echo $model->isNewRecord ? $form->textField($model, 'item_name', array('class' => 'text-input-bj  middle')) : $form->textField($model, 'item_name', array('class' => 'text-input-bj  middle','readonly' => true)) ; ?>
                <?php echo $form->error($model, 'item_name'); ?>
            </td>
        </tr>
        <tr>
            <th class="even">
                <?php echo $form->labelEx($model, 'item_number'); ?>
            </th>
            <td class="even">
                <?php echo $form->textField($model, 'item_number', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'item_number'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'item_status'); ?>
            </th>
            <td class="odd">
                <?php echo $form->radioButtonList($model, 'item_status', GameStoreItems::status(), array('separator' => '')); ?>
                <?php echo $form->error($model, 'item_status'); ?>
            </td>
        </tr>
        <tr>
            <th class="even">
                <?php echo $form->labelEx($model, 'item_description'); ?>
            </th>
            <td class="even">
                <?php echo $form->textField($model, 'item_description', array('class' => 'text-input-bj longest')); ?><b>（限制输入20个中文）</b>
                <?php echo $form->error($model, 'item_description'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'store_description'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textField($model, 'store_description', array('class' => 'text-input-bj longest')); ?><b>（限制输入10个中文）</b>
                <?php echo $form->error($model, 'store_description'); ?>
            </td>
        </tr>
        <tr>
            <th class="even"><?php echo $form->labelEx($model, 'start_date'); ?></th>
            <td class="even">
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'id'=>'start_date',
                    'model'=>$model,
                    'name' => 'start_date',
                    'select'=>'date',
                    'htmlOptions' => array(
                        'readonly' => 'readonly',
                        'class' => 'text-input-bj  least readonly',
                    )
                ));
                ?>
                <?php echo $form->error($model, 'start_date'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd"><?php echo $form->labelEx($model, 'end_date'); ?></th>
            <td class="odd">
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'id'=>'end_date',
                    'model'=>$model,
                    'name' => 'end_date',
                    'select'=>'date',
                    'htmlOptions' => array(
                        'readonly' => 'readonly',
                        'class' => 'text-input-bj  least readonly',
                    )
                ));
                ?>
                <?php echo $form->error($model, 'end_date'); ?>
            </td>
        </tr>
        <tr>
            <th class="even">
                <?php echo $form->labelEx($model, 'start_time'); ?>
            </th>
            <td class="even">
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'id'=>'start_time',
                    'model'=>$model,
                    'name' => 'start_time',
                    'select'=>'time',
                    'htmlOptions' => array(
                        'readonly' => 'readonly',
                        'class' => 'text-input-bj  least readonly',
                    )
                ));
                ?>
                <?php echo $form->error($model, 'start_time'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd">
                <?php echo $form->labelEx($model, 'end_time'); ?>
            </th>
            <td class="odd">
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'id'=>'end_time',
                    'model'=>$model,
                    'name' => 'end_time',
                    'select'=>'time',
                    'htmlOptions' => array(
                        'readonly' => 'readonly',
                        'class' => 'text-input-bj  least readonly',
                    )
                ));
                ?>
                <?php echo $form->error($model, 'end_time'); ?>
            </td>
        </tr>
        <tr>
            <th class="even">
                <?php echo $form->labelEx($model, 'limit_per_time'); ?>
            </th>
            <td class="even">
                <?php echo $form->textField($model, 'limit_per_time', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'limit_per_time'); ?>
            </td>
        </tr>
        <tr id = "ordinary">
            <th class="odd">
                <?php echo $form->labelEx($model, 'bees_number'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textField($model, 'bees_number', array('class' => 'text-input-bj  middle')); ?>(可设置0-9，0--抢到水果概率为100%，9为10%)
                <?php echo $form->error($model, 'bees_number'); ?>
            </td>
        </tr>
        <tr id = "special">
            <th class="even">
                <?php echo $form->labelEx($model, 'probability'); ?>
            </th>
            <td class="even">
                <?php echo $form->textField($model, 'probability', array('class' => 'text-input-bj  middle')); ?>(获取惊喜大奖的概率，以百万分比率计算)
                <?php echo $form->error($model, 'probability'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd"></th>
            <td colspan="2" class="odd">
                <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('gameStore', '新增') : Yii::t('gameStore', '保存'), array('class' => 'reg-sub')); ?>
            </td>
        </tr>
        </tbody>
    </table>
<?php $this->endWidget(); ?>
<script>
    $(document).ready(function(){
        var flag = $("input[name='GameStoreItems[flag]']:checked").val()
        if (flag == <?php echo GameStoreItems::SPECIAL_ITEM_FLAG?>) {
            $('#ordinary').hide();
            $('#special').show();
        }else{
            $('#ordinary').show();
            $('#special').hide();
        }

        $('#GameStoreItems_flag').change(function(){
            var flag = $("input[name='GameStoreItems[flag]']:checked").val()
            if (flag == <?php echo GameStoreItems::SPECIAL_ITEM_FLAG?>) {
                $('#ordinary').hide();
                $('#special').show();
            }else{
                $('#ordinary').show();
                $('#special').hide();
            }
        });

        $("#GameStoreItems_limit_per_time").change(function () {
            var number = parseInt($(this).val());
            var total = parseInt($("#GameStoreItems_item_number").val());
            if (number > total) {
                $(this).val(total);
            }
        });
    });
</script>