<?php
/* @var $this OrderController */
/* @var $model Order */
/* @var $form CActiveForm */
?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'action' => Yii::app()->createUrl($this->route),
    'method' => 'get',
        ));
?>
<div class="seachToolbar">

    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="sellerT5">
        <tbody>
            <tr>
                <th width="10%"><?php echo Yii::t('partnerModule.order', '订单编号'); ?>：</th>
                <td width="12%"><?php echo $form->textField($model, 'code', array('style' => 'width:90%', 'class' => "inputtxt1")); ?></td>
                <th width="10%"><?php echo Yii::t('partnerModule.order', '商品名称'); ?>：</th>
                <td width="12%"><?php echo $form->textField($model, 'goods_name', array('style' => 'width:90%', 'class' => "inputtxt1")); ?></td>
                 <th width="10%"><?php echo Yii::t('partnerModule.order', '网点'); ?>：</th>
                <td width="12%"><?php echo $form->textField($model, 'network', array('style' => 'width:90%', 'class' => "inputtxt1")); ?></td>
                <th width="10%"><?php echo Yii::t('partnerModule.order', '下单时间'); ?>：</th>
                <td width="20%">
                    <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $model,
                        'attribute' => 'create_time',
                        'options' => array(
                            'dateFormat' => Yii::t('partnerModule.order','yy-mm-dd'),
                            'changeMonth' => true,
                            'changeYear' => true,
                        ),
                        'htmlOptions' => array(
                            'readonly' => 'readonly',
                            'class' => 'inputtxt1',
							'style' => 'width:35%',
                        )
                    ));
                    ?>  -  <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $model,
                        'attribute' => 'end_time',
                        'language' => 'zh_cn',
                        'options' => array(
                            'dateFormat' => Yii::t('partnerModule.order','yy-mm-dd'),
                            'changeMonth' => true,
                            'changeYear' => true,
                        ),
                        'htmlOptions' => array(
                            'readonly' => 'readonly',
                            'class' => 'inputtxt1',
							'style' => 'width:35%',
                        )
                    ));
                    ?>
                </td>
                <td width="26%"> <?php echo CHtml::submitButton(Yii::t('partnerModule.order', '搜索'), array('class' => 'sellerBtn06')); ?> &nbsp;&nbsp;
                <?php //echo CHtml::button(Yii::t('partnerModule.order', "导出EXCEL"),array('class'=>'sellerBtn07','onclick'=>'getExcel()'))?>
            </tr>
<!--            <tr>
                <th><?php echo Yii::t('partnerModule.order', '状态'); ?>：</th>
                <td colspan="6">
                    <?php echo $form->radioButtonList($model,'status', array(null=>'全部'),
                    array('separator'=>''))?>
                    <?php echo $form->radioButtonList($model, 'status', Order::status(),  array('separator'=>''))
                    ?>
                </td>
            </tr>-->
            <?php echo $form->hiddenField($model,'status');?>
             <tr>
                <th><?php echo Yii::t('partnerModule.order', '类型'); ?>：</th>
                <td colspan="6">
                      <?php echo $form->radioButtonList($model,'type', array(null=>'全部'),
                    array('separator'=>''))?>
                      <?php echo $form->radioButtonList($model,'type',$model::type(),
                    array('separator'=>'')) ?>
                </td>
            </tr>
        </tbody>
    </table>

</div>
<?php $this->endWidget(); ?>