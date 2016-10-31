<?php
/* @var $this AssistantController */
/* @var $model Assistant */
/* @var $form CActiveForm */
Yii::app()->clientScript->registerScriptFile(DOMAIN . '/js/artDialog/plugins/iframeTools.js', CClientScript::POS_END);
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
<h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.superGoods', '基本信息'); ?></h3>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
));
?>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
    
    	<tr>
                <th><?php echo $form->labelEx($model,'source_cate_id'); ?></th>
                <td>
                    <?php echo $form->hiddenField($model,'source_cate_id'); ?>
                    <?php
                    $this->widget('zii.widgets.CBreadcrumbs', array(
                        'homeLink' => false,
                        'separator' => ' > ',
                        'links' => Tool::categoryBreadcrumb($model->source_cate_id),
                        'activeLinkTemplate'=>'{label}',
                        ));
                    echo CHtml::link(Yii::t('partnerModule.superGoods', '选择'), $this->createUrl('selectCategory',array('id'=>$model->id)));
                ?>
            </td>
        </tr>
    
         <tr>
        <th><?php echo $form->labelEx($model, 'barcode'); ?></th>
        <td>
            <?php echo $form->textField($model, 'barcode', array('class' => 'inputtxt1', 'style' => 'width:300px',
                )); ?>
            <?php echo $form->error($model, 'barcode'); ?>
        </td>
    </tr>
    <tr>
        <th width="10%"><?php echo $form->labelEx($model, 'name'); ?></th>
        <td width="90%">
            <?php echo $form->textField($model, 'name', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
            <?php echo $form->error($model, 'name'); ?>
        </td>
    </tr>

    
        <tr>
        <th width="10%"><?php echo $form->labelEx($model, 'sec_title'); ?></th>
        <td width="90%">
            <?php echo $form->textField($model, 'sec_title', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
            <?php echo $form->error($model, 'sec_title'); ?>
        </td>
    </tr>
    

       <tr>
        <th><?php echo $form->labelEx($model, 'supply_price'); ?></th>
        <td>
            <?php echo $form->textField($model, 'supply_price', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
            <?php echo $form->error($model, 'supply_price'); ?>
        </td>
    </tr>
    
    <tr>
        <th><?php echo $form->labelEx($model, 'price'); ?></th>
        <td>
            <?php echo $form->textField($model, 'price', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
            <?php echo $form->error($model, 'price'); ?>
        </td>
    </tr>

        <tr>
        <th><?php echo $form->labelEx($model, 'thumb'); ?></th>
        <td>
            <p>
                <?php echo $form->fileField($model, 'thumb') ?>&nbsp;&nbsp;
                <span class="gray"><?php echo Yii::t('partnerModule.superGoods', '请上传不大于1M的图片'); ?></span>
            </p>
            <?php echo $form->error($model, 'thumb',array('style' => 'position: relative; display: inline-block'), false, false) ?>

            <p class="mt10" id="img_area">
            <?php 
			if (!$model->isNewRecord){
				echo CHtml::image( ATTR_DOMAIN. '/' . $model->thumb, $model->name, array('width' => '200px'));
			}
			?>
			</p>
        </td>
    </tr>

    
    <tr>
        <th><?php echo $form->labelEx($model, 'cate_id'); ?></th>
        <td>
            <?php echo $form->dropDownList($model, 'cate_id', GoodsCategory::getGoodsCategoryList($model->member_id));?>
            <?php echo $form->error($model, 'cate_id'); ?>
        </td>
    </tr>
    
    
    <tr>
            <th><?php echo $form->labelEx($imgModel, 'path'); ?></th>
            <td>
                <?php
                $this->widget('partner.widgets.CUploadPic', array(
                    'attribute' => 'path',
                    'model' => $imgModel,
                    'form' => $form,
                    'num' => 6,
                    'btn_value' => Yii::t('sellerGoods', '上传图片'),
                    'render' => '_upload',
                    'folder_name' => 'files',
					'img_area'=>1,
                    'include_artDialog' => false,
                ));
                ?>
                <?php echo $form->error($imgModel, 'path') ?>
                &nbsp;<span class="gray">(<?php echo Yii::t('sellerGoods', '请根据app图片规格上传,建议上传3张'); ?>)</span>
            </td>
        </tr>
    
    
        <tr>
            <th><?php echo $form->labelEx($model, 'is_one'); ?></th>
            <td>
                <?php echo $form->radioButtonList($model, 'is_one', $model::gender(), array('separator' => '&nbsp')) ?>
                <?php echo $form->error($model, 'is_one') ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'is_promo'); ?></th>
            <td>
                <?php echo $form->radioButtonList($model, 'is_promo', $model::getIsProme(), array('separator' => '&nbsp')) ?>
                <?php echo $form->error($model, 'is_promo') ?>
            </td>
        </tr>
        
        <tr>
            <th><?php echo $form->labelEx($model, 'is_for'); ?></th>
            <td>
                <?php echo $form->radioButtonList($model, 'is_for', $model::getIsFor(), array('separator' => '&nbsp')) ?>
                <?php echo $form->error($model, 'is_for') ?>
            </td>
        </tr>
    
  <tr>
        <th ><?php echo $form->labelEx($model, 'content'); ?>：</th>
        <td >
         
            <?php
                    $this->widget('partner.extensions.editor.WDueditor', array(
                        'model' => $model,
                        'base_url' =>DOMAIN_PARTNER,
                        'attribute' => 'content',
                'save_path' => 'uploads/files', //默认是'attachments/UE_uploads'
                'url' => IMG_DOMAIN . '/files' //默认是ATTR_DOMAIN.'/UE_uploads'
                ));
                ?>
                <?php echo $form->error($model, 'content'); ?>
        </td>
    </tr>
    

   
    

    </tbody>
</table>


<div class="profileDo mt15">
    <a href="#" class="sellerBtn03 submitBt"><span><?php echo Yii::t('partnerModule.superGoods', '保存'); ?></span></a>&nbsp;&nbsp;
    <?php if(!$model->isNewRecord){?>
    <a href="javascript:history.go(-1);" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.superGoods', '返回'); ?></span></a>
    <?php }?>
</div>
<?php $this->endWidget(); ?>

<script type="text/javascript">
	$("#Goods_barcode").blur(function(){
		var url = "<?php echo $this->createUrl('/partner/goods/BarcodeGoods')?>";
		$.post(url, { barcode: $(this).val(),YII_CSRF_TOKEN:"<?php echo Yii::app()->request->csrfToken?>"},function(data){
			var is_create = '<?php echo $model->isNewRecord?>'*1;
     		var img_html = "";
     		$("#Goods_is_barcode").val("0");
     		$("#img_area").html("");
     		if(data.name){
     			$("#Goods_name").val(data.name);
                $("#ytGoods_thumb").val(data.thumb);           
     		               
                if(is_create) $("#Goods_price").val(Number(data.default_price));
     			img_html += '<?php echo CHtml::hiddenField('Goods[is_barcode]',1)?>';
     			img_html += '<br/><img width="200"  id="barcode_img"  src="<?php echo ATTR_DOMAIN?>'+data.thumb+'">';
     			$("#barcode_img").remove();
     			$("#img_area").html("");
     			$("#img_area").append(img_html);
			}
		},'json')
	});

    $(".submitBt").click(function () {
        $("form").submit();
    });

	//ytGoods_thumb
	$("#Goods_thumb").change(function(){
		$("#ytGoods_thumb").val($(this).val());
	});

//setInterval(function(){$("#Goods_content").html($(document.getElementById('ueditor_0').contentWindow.document.body).html());},1000);
    
</script>