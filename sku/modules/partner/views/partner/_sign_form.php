<?php
/* @var $this AssistantController */
/* @var $model Assistant */
/* @var $form CActiveForm */
?>
<script src="<?php echo DOMAIN; ?>/js/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script src="<?php echo DOMAIN; ?>/js/timePicker/timePicker.css" type="text/javascript"></script>
<script src="<?php echo DOMAIN; ?>/js/timePicker/jquery.timePicker.min.js" type="text/javascript"></script>
<h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.partner', '商家基本信息'); ?></h3>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
));
?>

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
    <tr>
        <th width="20%"><?php echo $form->labelEx($model, 'name'); ?></th>
        <td >
            <?php echo $form->textField($model, 'name', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
            <?php echo $form->error($model, 'name'); ?>
        </td>
    </tr>

    
    <tr>
        <th><?php echo $form->labelEx($model, 'head'); ?></th>
        <td>
            <p>
                <?php echo $form->fileField($model, 'head') ?>&nbsp;&nbsp;
                <span class="gray"><?php echo Yii::t('partnerModule.partner', '请上传不大于1M的图片'); ?></span>
            </p>
              <?php echo $form->error($model, 'head', array('style' => 'position: relative; display: inline-block'), false, false) ?>
            <?php if (!empty($model->head)): ?>
                <p class="mt10">
                    <img src="<?php echo ATTR_DOMAIN . '/' . $model->head ?>" width="120"/>
                </p>
            <?php endif; ?>
        </td>
    </tr>
    
     <tr>
            <th><?php echo $form->labelEx($model, 'mobile'); ?></th>
            <td>
                <?php echo $form->textField($model, 'mobile', array('class' => 'inputtxt1', 'style' => 'width:125px')); ?>(如需加区号，格式如'xxx-xxxxxxxx')
                <?php echo $form->error($model, 'mobile'); ?>
            </td>
        </tr>
    
     <tr>
            <th><?php echo Yii::t('partnerModule.partner', '所在地区'); ?><b class="red">*</b></th>
            <td>
                <?php
                echo $form->dropDownList($model, 'province_id', Region::getRegionByParentId(Region::PROVINCE_PARENT_ID), array(
                    'prompt' => Yii::t('partnerModule.partner', '选择省份'),
                    'class' => 'selectTxt1',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createUrl('/partner/region/updateCity'),
                        'dataType' => 'json',
                        'data' => array(
                            'province_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                        'success' => 'function(data) {
                            $("#Partners_city_id").html(data.dropDownCities);
                            $("#Partners_district_id").html(data.dropDownCounties);
                        }',                   
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'city_id', Region::getRegionByParentId($model->province_id), array(
                    'prompt' => Yii::t('partnerModule.partner', '选择城市'),
                    'class' => 'selectTxt1',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createUrl('/partner/region/updateArea'),
                        'update' => '#Partners_district_id',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'district_id', Region::getRegionByParentId($model->city_id), array(
                    'prompt' => Yii::t('partnerModule.partner', '选择区/县'),
                    'class' => 'selectTxt1',
                    'ajax' => array(
                        'type' => 'POST',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                )));
                ?>


                <div style="display:block;width:300px;float:left;margin-left:400px;">
                    <?php echo $form->error($model, 'district_id'); ?> 
                    <?php echo $form->error($model, 'city_id'); ?>
                    <?php echo $form->error($model, 'province_id'); ?>
                </div>

            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'street'); ?></th>
            <td>
                <?php echo $form->textField($model, 'street', array('class' => 'inputtxt1', 'style' => 'width:340px')); ?>
                <?php echo $form->error($model, 'street'); ?>
            </td>
        </tr>
        
         <tr>
            <th><?php echo $form->labelEx($model, 'zip_code'); ?></th>
            <td>
                <?php echo $form->textField($model, 'zip_code', array('class' => 'inputtxt1', 'style' => 'width:125px')); ?>
                <?php echo $form->error($model, 'zip_code'); ?>
            </td>
        </tr>
	</tbody>
	</table>
        
        
        
         <h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.partner', '网签信息'); ?></h3>
        
        
        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
        
       
        <tr>
            <th width="20%"><?php echo $form->labelEx($model, 'bank_account'); ?></th>
            <td>
                <?php echo $form->textField($model, 'bank_account', array('class' => 'inputtxt1', 'style' => 'width:225px')); ?>
                <?php echo $form->error($model, 'bank_account'); ?>
            </td>
        </tr>
        
        <tr>
            <th><?php echo $form->labelEx($model, 'bank_account_name'); ?></th>
            <td>
                <?php echo $form->textField($model, 'bank_account_name', array('class' => 'inputtxt1', 'style' => 'width:225px')); ?>
                <span class="gray"> &nbsp;&nbsp;&nbsp; <?php echo Yii::t('partnerModule.partner', '如：张三'); ?></span>
                <?php echo $form->error($model, 'bank_account_name'); ?>
            </td>
        </tr>
        
        <tr>
            <th><?php echo $form->labelEx($model, 'bank_name'); ?></th>
            <td>
                <?php echo $form->textField($model, 'bank_name', array('class' => 'inputtxt1', 'style' => 'width:225px')); ?>
                <?php echo $form->error($model, 'bank_name'); ?>
            </td>
        </tr>
        
        <tr>
            <th><?php echo Yii::t('partnerModule.partner', '银行所属地'); ?><b class="red">*</b></th>
            <td>
                <?php
                echo $form->dropDownList($model, 'bank_province_id', Region::getRegionByParentId(Region::PROVINCE_PARENT_ID), array(
                    'prompt' => Yii::t('partnerModule.partner', '选择省份'),
                    'class' => 'selectTxt1',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createUrl('/partner/region/updateCity'),
                        'dataType' => 'json',
                        'data' => array(
                            'province_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                        'success' => 'function(data) {
                            $("#Partners_bank_city_id").html(data.dropDownCities);
                            $("#Partners_bank_district_id").html(data.dropDownCounties);
                        }',                   
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'bank_city_id', Region::getRegionByParentId($model->bank_province_id), array(
                    'prompt' => Yii::t('partnerModule.partner', '选择城市'),
                    'class' => 'selectTxt1',
                    'ajax' => array(
                        'type' => 'POST',
						'url' => $this->createUrl('/partner/region/updateArea'),
						'update' => '#Partners_bank_district_id',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                )));
                ?>

                <?php
                echo $form->dropDownList($model, 'bank_district_id', Region::getRegionByParentId($model->bank_city_id), array(
                    'prompt' => Yii::t('partnerModule.partner', '选择区/县'),
                    'class' => 'selectTxt1',
                    'ajax' => array(
                        'type' => 'POST',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                )));
                ?>

                <div style="display:block;width:300px;float:left;margin-left:400px;">
                    <?php echo $form->error($model, 'bank_city_id'); ?>
                    <?php echo $form->error($model, 'bank_province_id'); ?>
                    <?php echo $form->error($model, 'bank_district_id'); ?>
                </div>

            </td>
        </tr>
        
       <tr>
            <th><?php echo $form->labelEx($model, 'bank_account_branch'); ?></th>
            <td>
                <?php echo $form->textField($model, 'bank_account_branch', array('class' => 'inputtxt1', 'style' => 'width:225px')); ?>
                <?php echo $form->error($model, 'bank_account_branch'); ?>
            </td>
        </tr>
        
        
   <tr>
        <th><?php echo $form->labelEx($model, 'bank_card_img'); ?></th>
        <td>
            <p>
                <?php echo $form->fileField($model, 'bank_card_img') ?>&nbsp;&nbsp;
                <span class="gray"><?php echo Yii::t('partnerModule.partner', '请上传不大于1M的图片'); ?></span>
            </p>
              <?php echo $form->error($model, 'bank_card_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
            <?php if (!empty($model->bank_card_img)): ?>
                <p class="mt10">
                    <img src="<?php echo ATTR_DOMAIN . '/' . $model->bank_card_img ?>" width="120"/>
                </p>
            <?php endif; ?>
        </td>
    </tr>
    
     <tr>
            <th><?php echo $form->labelEx($model, 'idcard'); ?></th>
            <td>
                <?php echo $form->textField($model, 'idcard', array('class' => 'inputtxt1', 'style' => 'width:225px')); ?>
                <?php echo $form->error($model, 'idcard'); ?>
            </td>
        </tr>
    
    
        <tr>
        <th><?php echo $form->labelEx($model, 'idcard_img_font'); ?></th>
        <td>
            <p>
                <?php echo $form->fileField($model, 'idcard_img_font') ?>&nbsp;&nbsp;
                <span class="gray"><?php echo Yii::t('partnerModule.partner', '请上传不大于1M的图片'); ?></span>
            </p>
              <?php echo $form->error($model, 'idcard_img_font', array('style' => 'position: relative; display: inline-block'), false, false) ?>
            <?php if (!empty($model->idcard_img_font)): ?>
                <p class="mt10">
                    <img src="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_font ?>" width="120"/>
                </p>
            <?php endif; ?>
        </td>
    </tr>
    
        <tr>
        <th><?php echo $form->labelEx($model, 'idcard_img_back'); ?></th>
        <td>
            <p>
                <?php echo $form->fileField($model, 'idcard_img_back') ?>&nbsp;&nbsp;
                <span class="gray"><?php echo Yii::t('partnerModule.partner', '请上传不大于1M的图片'); ?></span>
            </p>
              <?php echo $form->error($model, 'idcard_img_back', array('style' => 'position: relative; display: inline-block'), false, false) ?>
            <?php if (!empty($model->idcard_img_back)): ?>
                <p class="mt10">
                    <img src="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_back ?>" width="120"/>
                </p>
            <?php endif; ?>
        </td>
    </tr>
        <tr>
            <th rowspan="2"><?php echo $form->labelEx($model, 'license_img'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'license_img') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partnerModule.partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'license_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->license_img)): ?>
                    <p class="mt10">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->license_img ?>" width="120"/>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        <tr id="licenseTime">
            <td height="25" colspan="2" class="dtFff pdleft20">
                <?php echo $form->labelEx($model, 'license_expired_time'); ?>：
                <?php echo $form->textField($model, 'license_expired_time', array('class'=>'inputtxt1', 'onfocus' => 'WdatePicker()'))?>
                <?php echo $form->error($model, 'license_expired_time') ?>
            </td>
        </tr>
        
        
        
        	</tbody>
	</table>
        
        
        
         <h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.partner', '如需申请生鲜机，请补充以下信息'); ?></h3>
        
        
        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="mt10 sellerT3">
    <tbody>
        
        <tr>
            <th rowspan="2"><?php echo $form->labelEx($model, 'meat_inspection_certificate_img'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'meat_inspection_certificate_img') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partnerModule.partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'meat_inspection_certificate_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->meat_inspection_certificate_img)): ?>
                    <p class="mt10">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->meat_inspection_certificate_img ?>" width="120"/>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        <tr id="licenseTime">
            <td height="25" colspan="2" class="dtFff pdleft20">
                <?php echo $form->labelEx($model, 'meat_inspection_expired_time'); ?>：
                <?php echo $form->textField($model, 'meat_inspection_expired_time', array('class'=>'inputtxt1', 'onfocus' => 'WdatePicker()'))?>
                <?php echo $form->error($model, 'meat_inspection_expired_time') ?>
            </td>
        </tr>
        <tr>
            <th rowspan="2"><?php echo $form->labelEx($model, 'health_permit_certificate_img'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'health_permit_certificate_img') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partnerModule.partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'health_permit_certificate_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->health_permit_certificate_img)): ?>
                    <p class="mt10">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->health_permit_certificate_img ?>" width="120"/>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        <tr id="licenseTime">
            <td height="25" colspan="2" class="dtFff pdleft20">
                <?php echo $form->labelEx($model, 'health_permit_expired_time'); ?>：
                <?php echo $form->textField($model, 'health_permit_expired_time', array('class'=>'inputtxt1', 'onfocus' => 'WdatePicker()'))?>
                <?php echo $form->error($model, 'health_permit_expired_time') ?>
            </td>
        </tr>
        <tr>
            <th rowspan="2"><?php echo $form->labelEx($model, 'food_circulation_permit_certificate_img'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'food_circulation_permit_certificate_img') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partnerModule.partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'food_circulation_permit_certificate_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->food_circulation_permit_certificate_img)): ?>
                    <p class="mt10">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->food_circulation_permit_certificate_img ?>" width="120"/>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        <tr id="licenseTime">
            <td height="25" colspan="2" class="dtFff pdleft20">
                <?php echo $form->labelEx($model, 'food_circulation_expired_time'); ?>：
                <?php echo $form->textField($model, 'food_circulation_expired_time', array('class'=>'inputtxt1', 'onfocus' => 'WdatePicker()'))?>
                <?php echo $form->error($model, 'food_circulation_expired_time') ?>
            </td>
        </tr>
        <tr>
            <th rowspan="2"><?php echo $form->labelEx($model, 'stock_source_certificate_img'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'stock_source_certificate_img') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partnerModule.partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'stock_source_certificate_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->stock_source_certificate_img)): ?>
                    <p class="mt10">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->stock_source_certificate_img ?>" width="120"/>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        <tr id="licenseTime">
            <td height="25" colspan="2" class="dtFff pdleft20">
                <?php echo $form->labelEx($model, 'stock_source_expired_time'); ?>：
                <?php echo $form->textField($model, 'stock_source_expired_time', array('class'=>'inputtxt1', 'onfocus' => 'WdatePicker()'))?>
                <?php echo $form->error($model, 'stock_source_expired_time') ?>
            </td>
        </tr>
    
        
<tr>
<td colspan=2>
<div class="itemLast agreement" style="float: left;" >
          <input class="fl" type="checkbox" id="checkboxFourInput" name="" style="width: 18px;height:18px;">
                <div class="fr">我同意<a href="<?php echo DOMAIN_API?>/html/partnerjoinauditingabout.html" target="_blank">《SKU平台商户协议》</a>，
                成为平台注册商户。 并保证经营主体符合<a href=<?php echo DOMAIN_API?>/html/partnerjoinauditingzhuti.html " target="_blank">《SKU商户主体资质要求》</a>、<a href="<?php echo DOMAIN_API?>/html/partnerjoinauditingleimu.html" target="_blank">《SKU商品类目资质要求》</a>，服从<a href="<?php echo DOMAIN_API?>/html/partnerjoinauditingjinshou.html" target="_blank">《禁售商品管理规范》</a>。</div>
            </div>
</td>
</tr>
        
    </tbody>
</table>




<div class="profileDo mt15">
    <input type="hidden" id="isf"  value="0" />
    <a href="#" class="sellerBtn03 submitBt"><span><?php echo Yii::t('partnerModule.partner', '保存'); ?></span></a>&nbsp;&nbsp;
</div>
<?php $this->endWidget(); ?>

<script>
    $(".submitBt").click(function () {
         if(document.getElementById("checkboxFourInput").checked != true){
                           alert('请先勾选《SKU平台商户协议》!!');exit;
                       }
	      $("#Supermarkets_name").blur();
	      setTimeout(function(){
	    	  <?php if(!$model->isNewRecord){?>
	          $("#isf").val("0");
	          $(".errorMessage[style='']").each(function(i){
	       	   if($(this).html()!=""){
	  				$("#isf").val($("#isf").val()+1);
	           	   }
	       	 });
	          if($("#isf").val()=="0"){
	        	  if(confirm('<?php echo Yii::t('partnerModule.partner','编辑将会重新审核商家信息，确定要编辑吗？');?>')){
	          		$("form").submit(); 
	             }else{
	                    return false;
	            	}
	          }
	        	
	      		<?php }?>
	      		$("form").submit();      
	      },500);           
                      
    });
</script>