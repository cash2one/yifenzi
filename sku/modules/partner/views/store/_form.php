<?php
/* @var $this AssistantController */
/* @var $model Assistant */
/* @var $form CActiveForm */
?>
<h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.store', '基本信息'); ?></h3>
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
            <th width="10%"><?php echo $form->labelEx($model, 'name'); ?></th>
            <td width="90%">
                <?php echo $form->textField($model, 'name', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
                <?php echo $form->error($model, 'name'); ?>
            </td>
        </tr>


        <tr>
            <th><?php echo $form->labelEx($model, 'logo'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'logo') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partnerModule.store', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'logo', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->logo)): ?>
                    <p class="mt10">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->logo ?>" width="250"/>
                    </p>
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <th><?php echo $form->labelEx($model, 'category_id'); ?></th>
            <td>
                <?php
                echo $form->dropDownList($model, 'category_id', StoreCategory::getCategorys());
                ?>
                <?php echo $form->error($model, 'category_id'); ?>
            </td>
        </tr>

        <tr>
            <th><?php echo $form->labelEx($model, 'mobile'); ?></th>
            <td>
                <?php echo $form->textField($model, 'mobile', array('class' => 'inputtxt1', 'style' => 'width:125px')); ?>(<?php echo Yii::t("partnerModule.partner","如需加区号，格式如'xxx-xxxxxxxx'");?>)
                <?php echo $form->error($model, 'mobile'); ?>
            </td>
        </tr>

        <tr>
            <th><?php echo $form->labelEx($model, 'is_delivery'); ?></th>
            <td>
                <?php
                echo $form->dropDownList($model, 'is_delivery', Supermarkets::getDelivery());
                ?>
                <?php echo $form->error($model, 'is_delivery'); ?>
            </td>
        </tr>
        
        <tr id="tr_delivery_time">
            <th><?php echo $form->labelEx($model, 'delivery_time'); ?></th>
            <td>
                <?php echo $form->textField($model, 'delivery_time', array('class' => 'inputtxt1', 'style' => 'width:125px')); ?>（<?php echo Yii::t("partnerModule.partner","如: 06:00-24:00 | 6:00-24:00");?>）
                <?php echo $form->error($model, 'delivery_time'); ?>
            </td>
        </tr>
        
        <tr id="tr_delivery_start_amount">
            <th><?php echo $form->labelEx($model, 'delivery_start_amount'); ?></th>
            <td>
                <?php echo $form->textField($model, 'delivery_start_amount', array('class' => 'inputtxt1', 'style' => 'width:125px')); ?>
                <?php echo $form->error($model, 'delivery_start_amount'); ?>
            </td>
        </tr>

        <tr id="tr_delivery_mini_amount">
            <th><?php echo $form->labelEx($model, 'delivery_mini_amount'); ?></th>
            <td>
                <?php echo $form->textField($model, 'delivery_mini_amount', array('class' => 'inputtxt1', 'style' => 'width:125px')); ?>
                <?php echo $form->error($model, 'delivery_mini_amount'); ?>
            </td>
        </tr>

        <tr id="tr_delivery_fee">
            <th><?php echo $form->labelEx($model, 'delivery_fee'); ?></th>
            <td>
                <?php echo $form->textField($model, 'delivery_fee', array('class' => 'inputtxt1', 'style' => 'width:125px')); ?>
                <?php echo $form->error($model, 'delivery_fee'); ?>
            </td>
        </tr>

        <tr>
            <th><?php echo $form->labelEx($model, 'open_time'); ?></th>
            <td>
                <?php echo $form->textField($model, 'open_time', array('class' => 'inputtxt1', 'style' => 'width:125px')); ?>（<?php echo Yii::t("partnerModule.partner","如: 06:00-24:00 | 6:00-24:00");?>）
                <?php echo $form->error($model, 'open_time'); ?>
            </td>
        </tr>

        <tr>
            <th><?php echo Yii::t('partnerModule.store', '所在地区'); ?><b class="red">*</b></th>
            <td>
                <?php
                echo $form->dropDownList($model, 'province_id', Region::getRegionByParentId(Region::PROVINCE_PARENT_ID), array(
                    'prompt' => Yii::t('partnerModule.store', '选择省份'),
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
                            $("#Supermarkets_city_id").html(data.dropDownCities);
                            $("#Supermarkets_district_id").html(data.dropDownCounties);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'city_id', Region::getRegionByParentId($model->province_id), array(
                    'prompt' => Yii::t('partnerModule.store', '选择城市'),
                    'class' => 'selectTxt1',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createUrl('/partner/region/updateArea'),
                        'update' => '#Supermarkets_district_id',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'district_id', Region::getRegionByParentId($model->city_id), array(
                    'prompt' => Yii::t('partnerModule.store', '选择区/县'),
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


        <tr>
            <th><?php echo $form->labelEx($model, 'lng'); ?></th>
            <td>
                <?php echo $form->textField($model, 'lng', array('class' => 'inputtxt1', 'style' => 'width:340px')); ?>
                <?php echo $form->error($model, 'lng'); ?>               
                  <input type="button" value="<?php echo Yii::t('partnerModule.store', '选择经纬度') ?>" onclick="mark_click()" class="reg-sub"/>
            </td>
        </tr>

        <tr>
            <th><?php echo $form->labelEx($model, 'lat'); ?></th>
            <td>
                <?php echo $form->textField($model, 'lat', array('class' => 'inputtxt1', 'style' => 'width:340px')); ?>
                <?php echo $form->error($model, 'lat'); ?>
              
            </td>
        </tr>
         <tr>
            <th ><?php echo $form->labelEx($model, 'referrals_id'); ?>GW</th>
             <td>
                <?php echo $form->textField($model, 'referrals_gai_number', array('class' => 'inputtxt1', 'style' => 'width:340px')); ?>
                <?php echo $form->error($model, 'referrals_gai_number'); ?>
            </td>
        </tr>

    </tbody>
</table>


<div class="profileDo mt15">
    <input type="hidden" id="isf"  value="0" />
    <a href="#"  class="sellerBtn03 submitBt" ><span><?php echo Yii::t('partnerModule.store', '保存'); ?></span></a>
<a href="view" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.store', '返回'); ?></span></a>
</div>
<?php $this->endWidget(); ?>
<script type="text/javascript" language="javascript" src="/js/iframeTools.source.js"></script>
<script>
        var mark_click = function() {
                    var url = '<?php echo Yii::app()->createAbsoluteUrl('partner/map/show') ?>';
                    url += '?lng=' + $('#Supermarkets_lng').val() + '&lat=' + $('#Supermarkets_lat').val();
                    dialog = art.dialog.open(url, {
                        'title':'<?php echo Yii::t('partnerModule.store','设定经纬度');?>',
                        'lock': true,
                        'window': 'top',
                        'width': 740,
                        'height': 600,
                        'border': true
                    });
                };

                var onSelected = function(lat, lng) {
                    $('#Supermarkets_lng').val(lng);
                    $('#Supermarkets_lat').val(lat);
                };

                var doClose = function() {
                    if (null != dialog) {
                        dialog.close();
                    }
                };
     $(".submitBt").click(function () {
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
	        	  if(confirm('<?php echo Yii::t('partnerModule.store','编辑关键信息有可能需要重新审核店铺，确定要编辑吗？');?>')){
	          		$("form").submit(); 
	             }else{
	                    return false;
	            	}
	          }
	        	
	      		<?php }?>
	      		$("form").submit();      
	      },500);
        
    
        
    });

<?php
if ($model->is_delivery != Supermarkets::DELIVERY_YES) {
    ?>
        $("#tr_delivery_start_amount").hide();
        $("#tr_delivery_mini_amount").hide();
        $("#tr_delivery_fee").hide();
         $("#tr_delivery_time").hide();

    <?php
}
?>

    $("#Supermarkets_street").change(function () {
        var address = $("#Supermarkets_province_id").find("option:selected").text() + $("#Supermarkets_city_id").find("option:selected").text() + $("#Supermarkets_district_id").find("option:selected").text() + $(this).val();
        var apiurl = '<?php echo Yii::app()->createAbsoluteUrl('partner/region/searchLocation') ?>?address=' + address;
        $.getJSON(apiurl, function (data) {
            if (data.status == 0) {
                $("#Supermarkets_lng").val(data.result.location.lng);
                $("#Supermarkets_lat").val(data.result.location.lat);
            }
        });

    });


    $("#Supermarkets_is_delivery").change(function () {
        if ($(this).val() == '<?php echo Supermarkets::DELIVERY_NO ?>') {
            $("#tr_delivery_start_amount").hide();
            $("#tr_delivery_mini_amount").hide();
            $("#tr_delivery_fee").hide();
            $("#tr_delivery_time").hide();
            $("#Supermarkets_delivery_start_amount").val("0.00");
            $("#Supermarkets_delivery_mini_amount").val("0.00");
            $("#Supermarkets_delivery_fee").val("0.00");
        } else {
            $("#tr_delivery_start_amount").show();
            $("#tr_delivery_mini_amount").show();
            $("#tr_delivery_fee").show();
             $("#tr_delivery_time").show();
        }
    });


</script>