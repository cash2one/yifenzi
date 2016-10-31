<div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>
    <table cellpadding="0" cellspacing="0" class="searchTable">
        <tbody>
            <tr>
                <th><?php echo $form->label($model, 'goods_id'); ?></th>
                <td><?php echo $form->textField($model, 'goods_id', array('class' => 'text-input-bj least')); ?></td>
                <th><?php echo $form->label($model, 'goods_name'); ?></th>
                <td><?php echo $form->textField($model, 'goods_name', array('class' => 'text-input-bj  middle')); ?></td>
                <th><?php echo '添加时间' ?></th>
                <td>
                    <?php $this->widget('manage.extensions.timepicker.timepicker',array(
                        'model'=>$model,
                        'select'=>'date',
                        'name'=> 'startTime',
                        'cssClass'=>'datefield text-input-bj least',
                        'options'=> array(
                            'value'=> '',
                        )
                    ))?> 
                    --
                    <?php $this->widget('manage.extensions.timepicker.timepicker',array(
                        'model'=>$model,
                        'select'=>'date',
                        'cssClass'=>'datefield text-input-bj least',
                        'name'=> 'endTime',
                        'options'=> array(
                            'value'=> '',
                        )
                    ))?> 
                </td>
                <!--<th><?php //echo $form->label($model, 'recommended'); ?></th>-->
                <!--<td><?php //echo $form->radioButtonList($model, '', array('class' => 'text-input-bj  middle')); ?></td>-->
            </tr>
        </tbody>
    </table>
    <?php echo CHtml::submitButton(Yii::t('user', '搜索'), array('class' => 'reg-sub')); ?>
    <?php $this->endWidget(); ?>
</div>