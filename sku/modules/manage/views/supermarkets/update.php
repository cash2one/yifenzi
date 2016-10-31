<?php
/* @var $this ProductController */
/* @var $model Product */
/** @var  $form CActiveForm */

$this->breadcrumbs = array(
    '门店管理' => array('admin'),
    '编辑'
);
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'supermarkets-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
       
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come">
    <tbody>
        <tr>
            <th colspan="2" class="title-th odd">商家信息</th>
        </tr>
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'name'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'name', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'name'); ?>
            </td>
        </tr>
      
    <tr class="showMore">
            <th align="right"><?php echo $form->labelEx($model, 'logo'); ?> <span class="required"></span>：</th>
            <td>
                <?php
                $this->widget('widgets.CUploadPic', array(
                    'attribute' => 'logo',
                    'model'=>$model,
                    'form'=>$form,
                    'num' => 1,
                    'btn_value'=> Yii::t('supermarkets', '上传图片'),
                    'folder_name' => stristr($model->logo,'/',true),
                ));
                ?>（如需修改图片请先删除原图片）
              <span><?php echo $form->error($model, 'logo', array('style' => 'position: relative; display: inline-block'), false, false); ?> </span>
    
            </td>
        </tr> 
   <tr>
          <th><?php echo $form->labelEx($model, 'province_id'); ?>：</th>
            <td>
                <?php
                echo $form->dropDownList($model, 'province_id', Region::getRegionByParentId(Region::PROVINCE_PARENT_ID), array(
                    'prompt' => Yii::t('supermarkets', Yii::t('address', '选择省份')),
                    'class' => 'text-input-bj',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createUrl('/region/updateCity'),
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
                    'prompt' => Yii::t('vendingMachine', Yii::t('address', '选择城市')),
                    'class' => 'text-input-bj',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createUrl('/region/updateArea'),
                        'update' => '#Supermarkets_district_id',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),  
                        'success' => 'function(data) {                          
                            $("#Supermarkets_district_id").html(data);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'district_id', Region::getRegionByParentId($model->city_id), array(
                    'prompt' => Yii::t('supermarkets', Yii::t('address', '选择区/县')),
                    'class' => 'text-input-bj',
                    'ajax' => array(
                        'type' => 'POST',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                )));
                ?>
                
                <div style="display:block;width:300px;float:left;margin-left:380px;">
                    <?php echo $form->error($model, 'district_id', array('style' => 'position: absolute;top:6px;right:132px;')); ?> 
                    <?php echo $form->error($model, 'city_id', array('style' => 'position: absolute;top:6px;right:259px')); ?>
                    <?php echo $form->error($model, 'province_id', array('style' => 'position: absolute;top:6px;')); ?>
                </div>
            </td>
       </tr>
      
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'street'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'street', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'street'); ?>
            </td>
        </tr> 
          <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'zip_code'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'zip_code', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'zip_code'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'mobile'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'mobile', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'mobile'); ?>
            </td>
        </tr>

        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'status'); ?>：</th>
            <td>
             <?php echo $form->radioButtonList($model, 'status', Supermarkets::getStatus(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'status'); ?>
        </td>
        </tr> 
        
        <tr>   
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'category_id'); ?>：</th>
            <td>
                <?php
                echo $form->dropDownList($model, 'category_id', StoreCategory::getCategorys());
                 ?>
                <?php echo $form->error($model, 'category_id'); ?>
            </td>
        </tr> 
        
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'is_delivery'); ?>：</th>
            <td>
             <?php echo $form->radioButtonList($model, 'is_delivery', Supermarkets::getDelivery(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'is_delivery'); ?>
        </td>
        </tr> 
        
        
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'open_time'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'open_time', array('class' => "text-input-bj  long valid")); ?>(例:06:00-24:00 | 6:00-24:00)
                <?php echo $form->error($model, 'open_time'); ?>
            </td>
        </tr>
        
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'delivery_time'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'delivery_time', array('class' => "text-input-bj  long valid")); ?>(例:06:00-24:00 | 6:00-24:00)
                <?php echo $form->error($model, 'delivery_time'); ?>
            </td>
        </tr>
        
           <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'is_recommend'); ?>：</th>
            <td>
             <?php echo $form->radioButtonList($model, 'is_recommend', Supermarkets::getIsRencommend(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'is_recommend'); ?>
        </td>
        </tr> 
        
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'recommend_side'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'recommend_side', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'recommend_side'); ?>
            </td>
        </tr>
         <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'lng'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'lng', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'lng'); ?>
            </td>
        </tr>
        
         <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'lat'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'lat', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'lat'); ?>
            </td>
        </tr>
        <tr id="tr_delivery_start_amount">
            <th style="width: 135px" align="right"><?php echo $form->labelEx($model, 'delivery_start_amount'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'delivery_start_amount', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'delivery_start_amount'); ?>
            </td>
        </tr>
         <tr id="tr_delivery_mini_amount">
            <th style="width: 135px" align="right"><?php echo $form->labelEx($model, 'delivery_mini_amount'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'delivery_mini_amount', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'delivery_mini_amount'); ?>
            </td>
        </tr>
         <tr id="tr_delivery_fee">
            <th style="width: 150px" align="right"><?php echo $form->labelEx($model, 'delivery_fee'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'delivery_fee', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'delivery_fee'); ?>
            </td>
        </tr>
         <tr>
            <th style="width: 150px" align="right"><?php echo $form->labelEx($model, 'max_amount_preday'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'max_amount_preday', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'max_amount_preday'); ?>
            </td>
        </tr>
          <tr>
            <th style="width: 150px" align="right"><?php echo $form->labelEx($model, 'referrals_id'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'referrals_gai_number', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'referrals_gai_number'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'fee'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'fee', array('class' => "text-input-bj  valid")); ?>%
                <?php echo $form->error($model, 'fee'); ?>
            </td>
        </tr>
        
        <tr>
            <th></th>
            <td>
                <?php echo CHtml::submitButton(Yii::t('supermarkets', '编辑'), array('class' => 'reg-sub')); ?>
            </td>
        </tr>
    </tbody>
</table>

<?php $this->endWidget(); ?>

<script>
    <?php
if ($model->is_delivery != Supermarkets::DELIVERY_YES) {
    ?>
        $("#tr_delivery_start_amount").hide();
        $("#tr_delivery_mini_amount").hide();
        $("#tr_delivery_fee").hide();

    <?php
}
?>
    
	$("#Supermarkets_is_delivery").change(function(){
		if(document.getElementById("Supermarkets_is_delivery_0").checked){
                                                    $("#tr_delivery_start_amount").hide();
			$("#tr_delivery_mini_amount").hide();
			$("#tr_delivery_fee").hide();
                        
                                                    $("#Supermarkets_delivery_start_amount").val("0.00");
                                                    $("#Supermarkets_delivery_mini_amount").val("0.00");
                                                    $("#Supermarkets_delivery_fee").val("0.00");

			
		}else{
                                                    $("#tr_delivery_start_amount").show();
			$("#tr_delivery_mini_amount").show();
			$("#tr_delivery_fee").show();
        
		}
	});

    $("#Supermarkets_street").change(function () {
        var address = $("#Supermarkets_province_id").find("option:selected").text() + $("#Supermarkets_city_id").find("option:selected").text() + $("#Supermarkets_district_id").find("option:selected").text() + $(this).val();
        var apiurl = '<?php echo Yii::app()->createAbsoluteUrl('region/searchLocation') ?>?address=' + address;
        $.getJSON(apiurl, function (data) {
            if (data.status == 0) {
                $("#Supermarkets_lng").val(data.result.location.lng);
                $("#Supermarkets_lat").val(data.result.location.lat);
            }
        });

    });

	
    </script>