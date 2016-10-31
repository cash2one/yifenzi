<?php
/* @var $this FranchiseeArtileController */
/* @var $model FranchiseeArtile */

$this->breadcrumbs = array(
    Yii::t('partnerModule.freshMachine', '生鲜机管理') => array('FreshGoods?mid=' . $mid),
    Yii::t('partnerModule.freshMachine', '添加生鲜机商品'),
);
?>

<div class="toolbar">
    <h3><?php echo Yii::t('partnerModule.freshMachine', '添加生鲜机商品'); ?></h3>
</div>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' =>true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true, //客户端验证
    ),
        ));
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
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>


        <?php if ($model->isNewRecord) : ?>

            <tr>
                <th><?php echo Yii::t('partnerModule.freshMachine', '选择商品'); ?><span class="required">*</span></th>

                <td>
                    <?php echo $form->hiddenField($model, 'goods_id') ?>
                    <input class="inputtxt1" id="RefGoodsName" name="RefGoodsName" style='width:300px;'
                           readonly="readonly" type="text" value="" />
                    <input type="button" value="<?php echo Yii::t('partnerModule.freshMachine', '选择'); ?>" id="seachRefMem"  class="reg-sub" />
                    <input type="button" value="<?php echo Yii::t('partnerModule.freshMachine', '清空'); ?>" id="clearRefMem"  class="reg-sub" />
                    <?php echo $form->error($model, 'goods_id') ?>

                    <script type="text/javascript">
                        $("#clearRefMem").click(function () {
                            $("#RefGoodsName").val('');
                            $("#SuperGoods_goods_id").val('');
                        });
                    </script>

                </td>

            </tr>

            <tr>
                <th><?php echo $form->labelEx($model, 'specifications'); ?></th>
                <td>
                    <?php echo $form->textField($model, 'specifications', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
                    <?php echo $form->error($model, 'specifications'); ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $form->labelEx($model, 'expr_time'); ?></th>
                <td >
                    <?php $this->widget('comext.timepicker.timepicker', array('model' => $model, 'name' => 'expr_time', 'options' => array(), 'cssClass' => 'inputtxt1')); ?><b class="red">（<?php echo Yii::t('partnerModule.freshMachine','不填则表示永不过期');?>）
                        <?php echo $form->error($model, 'expr_time'); ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $form->labelEx($model, 'goods_address'); ?></th>
                <td>
                    <?php echo $form->textField($model, 'goods_address', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
                    <?php echo $form->error($model, 'goods_address'); ?>
                </td>
            </tr>

        <?php endif; ?>

        <tr>
            <th width="10%"><?php echo Yii::t('partnerModule.freshMachine', '初始库存'); ?><span class="required">*</span></th>
            <td width="90%">
                <?php echo CHtml::hiddenField('mid', $m_model->id) ?>
                <?php echo $form->textField($model, 'stock', array('class' => 'inputtxt1', 'style' => 'width:300px;')); ?>
                <?php echo $form->error($model, 'stock'); ?>
            </td>
        </tr>
        <tr>
            <th width="10%"><?php echo Yii::t('partnerModule.freshMachine', '货道'); ?><span class="required">*</span></th>
            <td width="90%">  
                <?php echo $form->dropDownList($model, 'line_id', CHtml::listData(FreshMachineLine::model()->findAll('status=:status and machine_id=:mid and rent_partner_id=:pid', array(':status' => FreshMachineLine::STATUS_ENABLE, ':mid' => $mid, ':pid' => $this->curr_act_partner_id)), 'id', 'name'), array('class' => 'inputtxt1', 'empty' => Yii::t('partnerModule.freshMachine', '请选择'))); ?>
                <?php echo $form->error($model, 'line_id'); ?>
            </td>

        </tr>
        <tr>

            <th  width="10%"><?php echo $form->labelEx($model, 'weight'); ?></th>
            <td  width="90%"> <?php echo $form->textField($model, 'weight', array('class' => "inputtxt1")); ?> (<?php echo Yii::t('partnerModule.freshMachine', '单位'); ?>：g)
                <?php echo $form->error($model, 'weight'); ?></td>
        </tr>

    </tbody>
</table>
<?php $this->endWidget(); ?>

<div class="profileDo mt15">
    <a href="javascript:void(0);" class="sellerBtn03" onclick="$('#freshMachine-form').submit();"><span><?php echo $model->isNewRecord ? Yii::t('partnerModule.freshMachine', '添加') : Yii::t('partnerModule.freshMachine', '确定'); ?></span></a>&nbsp;&nbsp;<a href="javascript:history.go(-1);" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.freshMachine', '返回'); ?></span></a>
</div>

<script type="text/javascript" language="javascript" src="<?php echo DOMAIN ?>/js/iframeTools.source.js"></script>
<?php
Yii::app()->clientScript->registerScript('machineGoods', "
var dialog = null;
jQuery(function($) {
    $('#seachRefMem').click(function() {
        dialog = art.dialog.open('" . $this->createAbsoluteUrl('goods/searchList',array('type'=>Stores::FRESH_MACHINE,'sid'=>$mid,'all'=>true)) . "', { 'id': 'selectGoods', title:'".Yii::t('partnerModule.freshMachine','搜索商品')."', width: '1000px', height: '520px', lock: true });
    })
})
 var doClose = function() {
                if (null != dialog) {
                    dialog.close();
                }
            };
var onSelectGoods = function (id,name,thumb,spec_id) {
    if (id) {
        $('#FreshMachineGoods_goods_id').val(id);
        $('#RefGoodsName').val(name);
    }
};
	                		        		
", CClientScript::POS_HEAD);
?>