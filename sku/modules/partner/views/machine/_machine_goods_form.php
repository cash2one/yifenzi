<?php
/* @var $this FranchiseeArtileController */
/* @var $model FranchiseeArtile */
/* @var $form CActiveForm */
?>

<?php
$form = $this->beginWidget('CActiveForm', array(
   'id' => $this->id . '-form',
   'enableAjaxValidation' => true,
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
				<th><?php echo  Yii::t('partnerModule.machine','选择商品'); ?><span class="required">*</span></th>

				<td>
					<?php echo $form->hiddenField($model,'goods_id') ?>
	                <input class="inputtxt1" id="RefGoodsName" name="RefGoodsName" style='width:300px;'
	                       readonly="readonly" type="text" value="" />
	                <input type="button" value="<?php echo Yii::t('partnerModule.machine','选择'); ?>" id="seachRefMem"  class="reg-sub" />
	                <input type="button" value="<?php echo Yii::t('partnerModule.machine','清空'); ?>" id="clearRefMem"  class="reg-sub" />
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
			
			<tr style="display:none">
				<th width="10%"><?php echo Yii::t('partnerModule.machine','初始库存'); ?><span class="required">*</span></th>
				<td width="90%">
				<?php echo CHtml::hiddenField('mid',$m_model->id)?>
					<?php echo $form->textField($model, 'stock', array('class' => 'inputtxt1','style'=>'width:300px;','value'=>0)); ?>
            		<?php echo $form->error($model, 'stock'); ?>
				</td>
			</tr>
                        <tr>
                            <th width="10%"><?php echo Yii::t('partnerModule.machine','货道'); ?><span class="required">*</span></th>
                            <td width="90%"><?php echo $form->textField($model, 'line', array('class' => 'inputtxt1','style'=>'width:300px;')); ?> <?php echo $form->error($model,'line') ?></td>
                         
                        </tr>
		
		</tbody>
	</table>
	<?php $this->endWidget(); ?>
	
	<div class="profileDo mt15">
            <a href="javascript:void(0);" class="sellerBtn03" onclick="$('#machine-form').submit();"><span><?php echo $model->isNewRecord?Yii::t('partnerModule.machine', '添加'):Yii::t('partnerModule.machine', '修改');  ?></span></a>&nbsp;&nbsp;<a href="machineGoodsList?mid=<?php echo $mid ?>" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.machine','返回'); ?></span></a>
	</div>
	
<script type="text/javascript" language="javascript" src="<?php echo DOMAIN ?>/js/iframeTools.source.js"></script>
<?php
Yii::app()->clientScript->registerScript('machineGoods', "
var dialog = null;
jQuery(function($) {
    $('#seachRefMem').click(function() {
        dialog = art.dialog.open('" . $this->createAbsoluteUrl('goods/searchList',array('type'=>Stores::MACHINE,'sid'=>$mid)) . "', { 'id': 'selectGoods', title: '搜索商品', width: '1000px', height: '520px', lock: true });
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
