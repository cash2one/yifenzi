<?php
/** @var $form CActiveForm */
/** @var $model CashHistory */
$form = $this->beginWidget('CActiveForm', array(
    'action' => Yii::app()->createUrl($this->route),
    'method' => 'get',
));
?>
<div class="border-info clearfix">
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th>
                <?php echo $form->label($model,'account_name'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'account_name',array('class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody></table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th>
                <?php echo Yii::t('cashHistory','盖网编号'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'sku_number',array('class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody></table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th>
                <?php echo Yii::t('cashHistory','手机号'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'mobile',array('class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        </tbody></table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th>
                <?php echo $form->label($model,'account'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'account',array('class'=>'text-input-bj  middle')); ?>
            </td>
        </tr>
        </tbody></table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th>
                <?php echo $form->label($model,'status'); ?>：
            </th>
            <td id="tdOrderStatus">
                <?php
                //在状态数组头部，加入 “全部”，非0下标开始的数组，用radioButtonList 的 empty ，会导致下标改变
                $status = $model::status();
                unset($status[1]);
                $status = array_reverse($status,true);
                $status[''] = Yii::t('cashHistory','全部');
                $status = array_reverse($status,true);
                ?>
                <?php echo $form->radioButtonList($model,'status',$status,array('separator'=>'')) ?>
            </td>
        </tr>
        </tbody></table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th>
                <?php echo $form->label($model,'apply_time'); ?>：
            </th>
            <td>
                <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'model' => $model,
                    'name' => 'apply_time',
                ));
                ?>  -  <?php
                $this->widget('comext.timepicker.timepicker', array(
                    'model' => $model,
                    'name' => 'end_time',
                ));
                ?>
            </td>
        </tr>
        </tbody></table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th>
                <?php echo Yii::t('cashHistory','排序'); ?>：
            </th>
            <td>
                <?php echo $form->dropDownList($model,'order',$model::orderShow(),array('class'=>'text-input-bj  middle')) ?>
            </td>
            <td>
                <?php echo Yii::t('cashHistory','提现类型'); ?>：
                <?php echo $form->dropDownList($model,'type',$model::getType(),array('empty'=>array(''=>'全部'))) ?>
            </td>
        </tr>
        </tbody>
    </table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th>
                id：
            </th>
            <td>
                <?php echo $form->textField($model,'id',array('class'=>'text-input-bj  middle')) ?>
            </td>
        </tr>
        </tbody>
    </table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th>
                <?php echo Yii::t('cashHistory','审核状态'); ?>：
            </th>
            <td>
                <?php
                //在状态数组头部，加入 “全部”，非0下标开始的数组，用radioButtonList 的 empty ，会导致下标改变
                $status = $model::is_check();
                $status = array_reverse($status,true);
                $status[''] = Yii::t('cashHistory','全部');
                $status = array_reverse($status,true);
                ?>
                <?php echo $form->radioButtonList($model,'is_check',$status,array('separator'=>'')) ?>
            </td>
        </tr>
        </tbody>
    </table>
        
        <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <td>
                <?php echo CHtml::submitButton(Yii::t('cashHistory','搜索'),array('class'=>'regm-sub')) ?>
            </td>
        </tr>
        </tbody></table>
        
        
    
</div>
<?php $this->endWidget(); ?>