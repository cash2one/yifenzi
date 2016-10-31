<?php
/* @var $this FranchiseeArtileController */
/* @var $model FranchiseeArtile */
/* @var $form CActiveForm */
?>
<h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.storeGoods', '添加超市商品'); ?></h3>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'superStoreGoddsForm',
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
		
		
		<?php if($model->isNewRecord) :?>
			
			<tr>
				<th><?php echo  Yii::t('partnerModule.storeGoods','选择商品'); ?><span class="required">*</span></th>

				<td>
					<?php echo $form->hiddenField($model,'goods_id') ?>
	                <input class="inputtxt1" id="RefGoodsName" name="RefGoodsName" style='width:300px;'
	                       readonly="readonly" type="text" value="" />
	                <input type="button" value="<?php echo Yii::t('partnerModule.storeGoods','选择'); ?>" id="seachRefMem"  class="reg-sub" />
	                <input type="button" value="<?php echo Yii::t('partnerModule.storeGoods','清空'); ?>" id="clearRefMem"  class="reg-sub" />
	                <?php echo $form->error($model,'goods_id') ?>
	                
	                <script type="text/javascript">
	                	$("#clearRefMem").click(function(){
							$("#RefGoodsName").val('');
							$("#SuperGoods_goods_id").val('');
	                        });
	                </script>
					
				</td>
				
			</tr>
			
			<?php endif;?>
			
			<tr>
				<th width="10%"><?php echo Yii::t('partnerModule.storeGoods','初始库存'); ?></th>
				<td width="90%">
					<?php echo $form->textField($model, 'stock', array('class' => 'inputtxt1','style'=>'width:300px;')); ?>
                                                                                         <?php echo $form->error($model, 'stock'); ?>
				</td>
			</tr>
		
		</tbody>
	</table>
	<?php $this->endWidget(); ?>
	
	<div class="profileDo mt15">
		<a href="javascript:void(0);" class="sellerBtn03" onclick="$('#superStoreGoddsForm').submit();"><span><?php echo $model->isNewRecord?Yii::t('partnerModule.storeGoods', '添加'):Yii::t('partnerModule.storeGoods', '确定');  ?></span></a>&nbsp;&nbsp;<a href="javascript:history.go(-1);" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.storeGoods','返回'); ?></span></a>
	</div>
	
<script type="text/javascript" language="javascript" src="<?php echo DOMAIN ?>/js/iframeTools.source.js"></script>
<?php
Yii::app()->clientScript->registerScript('superStoreGoods', "
var dialog = null;
jQuery(function($) {
    $('#seachRefMem').click(function() {
        dialog = art.dialog.open('" . $this->createAbsoluteUrl('goods/searchList',array('type'=>Stores::SUPERMARKETS,'sid'=>$this->super_id)) . "', { 'id': 'selectGoods', title: '".Yii::t('partnerModule.storeGoods','搜索商品')."', width: '1000px', height: '520px', lock: true });
    })
})
 var doClose = function() {
                if (null != dialog) {
                    dialog.close();
                }
            };
var onSelectGoods = function (id,name,thumb,spec_id) {
    if (id) {
        $('#SuperGoods_goods_id').val(id);
        $('#RefGoodsName').val(name);
    }
};
	                		        		
", CClientScript::POS_HEAD);
?>
