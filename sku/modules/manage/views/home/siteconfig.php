<?php $form = $this->beginWidget('CActiveForm', $formConfig); ?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tbody>
        <tr>
            <th colspan="2" style="text-align: center" class="title-th">
                <?php echo Yii::t('home', '网站基本信息配置'); ?>
            </th>
        </tr>
        <tr>
            <th style="width: 220px"><?php echo $form->labelEx($model, 'name'); ?></th>
            <td>
                <?php echo $form->textField($model, 'name', array('class' => 'text-input-bj  long valid')); ?>
                <?php echo $form->error($model, 'name'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'domain'); ?></th>
            <td>
                <?php echo $form->textField($model, 'domain', array('class' => 'text-input-bj  long')); ?>
                <?php echo $form->error($model, 'domain'); ?>
            </td>
        </tr>
        
        <!-- 
        
        <tr>
            <th><?php echo $form->labelEx($model, 'weibo'); ?></th>
            <td>
                <?php echo $form->textField($model, 'weibo', array('class' => 'text-input-bj  long valid')); ?>
                <?php echo $form->error($model, 'weibo'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'phone'); ?></th>
            <td>
                <?php echo $form->textField($model, 'phone', array('class' => 'text-input-bj  long')); ?>
                <?php echo $form->error($model, 'phone'); ?>
                <?php echo Yii::t('home', '（多个电话用半角“,”分割）'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'service_time'); ?></th>
            <td>
                <?php echo $form->textField($model, 'service_time', array('class' => 'text-input-bj  long')); ?>
                <?php echo $form->error($model, 'service_time'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'qq'); ?></th>
            <td>
                <?php echo $form->textArea($model, 'qq', array('class' => 'text-input-bj', 'cols' => 60)); ?>
                <?php echo $form->error($model, 'qq'); ?>
                <br/>
                <?php echo Yii::t('home', " (多个QQ以','号隔开，每个QQ都可以设置标题，标题和QQ号之间以':'隔开，例如：售后服务:12878463,售前服务:289137323) "); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'description'); ?></th>
            <td>
                <?php
                $this->widget('manage.extensions.editor.WDueditor', array(
                    'model' => $model,
                    'attribute' => 'description',
                ));
                ?>
                <?php echo $form->error($model, 'description'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'copyright'); ?></th>
            <td>
                <?php echo $form->textArea($model, 'copyright', array('class' => 'text-input-bj', 'cols' => 60)); ?>
                <?php echo $form->error($model, 'copyright'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'icp'); ?></th>
            <td>
                <?php echo $form->textArea($model, 'icp', array('class' => 'text-input-bj', 'cols' => 60)); ?>
                <?php echo $form->error($model, 'icp'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'iconScript'); ?></th>
            <td>
                <?php echo $form->textArea($model, 'iconScript', array('class' => 'text-input-bj', 'cols' => 60)); ?>
                <?php echo $form->error($model, 'iconScript'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'statisticsScript'); ?></th>
            <td>
                <?php echo $form->textArea($model, 'statisticsScript', array('class' => 'text-input-bj', 'cols' => 60)); ?>
                <?php echo $form->error($model, 'statisticsScript'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'notice'); ?></th>
            <td>
                <?php echo $form->textArea($model, 'notice', array('class' => 'text-input-bj', 'cols' => 60)); ?>
                <?php echo $form->error($model, 'notice'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'automaticallySignTimeOrders'); ?></th>
            <td>
                <?php echo $form->textField($model, 'automaticallySignTimeOrders', array('class' => 'text-input-bj  middle valid')); ?><?php echo Yii::t('home', '（天）'); ?>
                <?php echo $form->error($model, 'automaticallySignTimeOrders'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'extendMaximumNum'); ?></th>
            <td>
                <?php echo $form->textField($model, 'extendMaximumNum', array('class' => 'text-input-bj  middle valid')); ?><?php echo Yii::t('home', '（次）'); ?>
                <?php echo $form->error($model, 'extendMaximumNum'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'ordersActivistTime'); ?></th>
            <td>
                <?php echo $form->textField($model, 'ordersActivistTime', array('class' => 'text-input-bj  middle valid')); ?><?php echo Yii::t('home', '（天）'); ?>
                <?php echo $form->error($model, 'ordersActivistTime'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'duration'); ?></th>
            <td>
                <?php echo $form->textField($model, 'duration', array('class' => 'text-input-bj  middle valid')); ?><?php echo Yii::t('home', '（天）'); ?>
                <?php echo $form->error($model, 'duration'); ?>
            </td>
        </tr>
        
         -->
  
  	<tr>
            <th><?php echo $form->labelEx($model, 'api_distance'); ?></th>
            <td>
                <?php echo $form->textField($model, 'api_distance', array('class' => 'text-input-bj  middle valid')); ?><?php echo Yii::t('home', '（米）设置为0的话则获取接收参数或默认为1000 '); ?>
                <?php echo $form->error($model, 'api_distance'); ?>
            </td>
        </tr>
        
          	<tr>
            <th><?php echo $form->labelEx($model, 'kefu_mobile'); ?></th>
            <td>
                <?php echo $form->textField($model, 'kefu_mobile', array('class' => 'text-input-bj  middle valid')); ?>
                <?php echo $form->error($model, 'kefu_mobile'); ?>
            </td>
        </tr>
  
        <tr>
            <th></th>
            <td><?php echo CHtml::submitButton(Yii::t('home', '保存'), array('class' => 'reg-sub')); ?></td>
        </tr>
    </tbody>
</table>
<?php $this->endWidget(); ?>