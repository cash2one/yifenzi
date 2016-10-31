<?php
/* @var $this FranchiseeArtileController */
/* @var $model FranchiseeArtile */
/* @var $form CActiveForm */
$this->breadcrumbs = array(
    Yii::t('partnerModule.machine', '售货机管理') ,
    Yii::t('partnerModule.machine', '修改售货机商品'),
);
?>
<h3 class="mt15 tableTitle"><?php echo Yii::t('superStore', '修改售货机商品'); ?></h3>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' => false,
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

        
        <tr>
            <th width="10%"><?php echo Yii::t('partnerModule.machine', '货道'); ?></th>
            <td width="90%"><?php echo $form->textField($model, 'line', array('class' => 'inputtxt1', 'style' => 'width:300px;')); ?> <?php echo $form->error($model,'line') ?></td>
        </tr>

    </tbody>
</table>
<?php $this->endWidget(); ?>

<div class="profileDo mt15">
    <a href="javascript:void(0);" class="sellerBtn03" onclick="$('#machine-form').submit();"><span><?php echo Yii::t('partnerModule.machine', '修改'); ?></span></a>&nbsp;&nbsp;<a href="<?php echo $this->createAbsoluteUrl('machine/machineGoodsList',array('mid'=>$mid)) ?>" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.machine', '返回'); ?></span></a>
</div>

<script type="text/javascript" language="javascript" src="<?php echo DOMAIN ?>/js/iframeTools.source.js"></script>
<?php
Yii::app()->clientScript->registerScript('machineGoods', "
var dialog = null;
jQuery(function($) {
    $('#seachRefMem').click(function() {
        dialog = art.dialog.open('" . $this->createAbsoluteUrl('goods/searchList') . "', { 'id': 'selectGoods', title: '".Yii::t('partnerModule.machine','搜索商品')."', width: '1000px', height: '520px', lock: true });
    })
})
 var doClose = function() {
                if (null != dialog) {
                    dialog.close();
                }
            };
var onSelectGoods = function (id,name,thumb,spec_id) {
    if (id) {
        $('#VendingMachineGoods_goods_id').val(id);
        $('#RefGoodsName').val(name);
    }
};
	                		        		
", CClientScript::POS_HEAD);
?>
