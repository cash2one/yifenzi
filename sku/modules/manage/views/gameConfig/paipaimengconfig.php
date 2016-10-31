<?php
//啪啪萌僵尸游戏配置 视图
/* @var $form  CActiveForm */
/* @var $model GameConfigForm */
?>
    <style>
        th.title-th  {text-align: center;}
    </style>
<?php $form = $this->beginWidget('CActiveForm', $formConfig); ?>

    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
        <tbody>
        <tr>
            <th colspan="2"  class="title-th even">
                <?php echo Yii::t('home', '啪啪萌僵尸游戏配置管理'); ?>
            </th>
        </tr>
        <tr>
            <th style="width: 250px">
                <?php echo $form->labelEx($model, 'config_name'); ?>
            </th>
            <td>
                <?php echo $form->hiddenField($model, 'app_type',array('value' => AppVersion::APP_TYPE_GAME_PAIPAIMENG)); ?>
                <?php echo $form->error($model, 'app_type'); ?>
                <?php if($model->config_name): ?>
                    <?php echo $form->textField($model, 'config_name', array('class' => 'text-input-bj  long valid','readonly' => true, 'style' => 'width:100px; background:#eee;')); ?>
                <?php else: ?>
                    <?php echo $form->textField($model, 'config_name', array('class' => 'text-input-bj  long valid')); ?>
                <?php endif;?>
                <?php echo $form->error($model, 'config_name'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'value'); ?></th>
            <td>
                <?php
                $this->widget('manage.extensions.editor.WDueditor', array(
                    'model' => $model,
                    'attribute' => 'value',
                ));
                ?>
                <?php echo $form->error($model, 'value'); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php echo CHtml::submitButton(Yii::t('home', '保存'), array('class' => 'reg-sub')) ?>
            </td>
        </tr>
        </tbody>
    </table>
<?php $this->endWidget(); ?>