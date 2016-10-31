<?php
/* @var $this AssistantController */
/* @var $model Assistant */
/* @var $form CActiveForm */
?>
<h3 class="mt15 tableTitle"><?php echo Yii::t('partnerModule.machine', '基本信息'); ?></h3>
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
                <?php if ($model->isNewRecord): ?>
                    <?php echo $form->textField($model, 'name', array('class' => 'inputtxt1', 'style' => 'width:300px')); ?>
                    <?php echo $form->error($model, 'name'); ?>
                <?php else : ?>

                    <?php echo $model->name; ?>

                <?php endif; ?>
            </td>
        </tr>


        <tr>
            <th><?php echo $form->labelEx($model, 'thumb'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'thumb') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partnerModule.machine', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'thumb', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->thumb)): ?>
                    <p class="mt10">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->thumb ?>" width="120" height="140"/>
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
            <th><?php echo Yii::t('partnerModule.machine', '所在地区'); ?><b class="red">*</b></th>
            <td>
                <?php
                echo $form->dropDownList($model, 'province_id', Region::getRegionByParentId(Region::PROVINCE_PARENT_ID), array(
                    'prompt' => Yii::t('partnerModule.machine', '选择省份'),
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
                            $("#VendingMachine_city_id").html(data.dropDownCities);
                            $("#VendingMachine_district_id").html(data.dropDownCounties);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'city_id', Region::getRegionByParentId($model->province_id), array(
                    'prompt' => Yii::t('partnerModule.machine', '选择城市'),
                    'class' => 'selectTxt1',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createUrl('/partner/region/updateArea'),
                        'update' => '#VendingMachine_district_id',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'district_id', Region::getRegionByParentId($model->city_id), array(
                    'prompt' => Yii::t('partnerModule.machine', '选择区/县'),
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
            <th><?php echo $form->labelEx($model, 'address'); ?></th>
            <td>
                <?php echo $form->textField($model, 'address', array('class' => 'inputtxt1', 'style' => 'width:340px')); ?>
                <?php echo $form->error($model, 'address'); ?>
            </td>
        </tr>


        <tr>
            <th><?php echo $form->labelEx($model, 'lng'); ?></th>
            <td>
                <?php echo $form->textField($model, 'lng', array('class' => 'inputtxt1', 'style' => 'width:340px')); ?>
                <?php echo $form->error($model, 'lng'); ?>        
                <input type="button" value="<?php echo Yii::t('partnerModule.machine', '选择经纬度') ?>" onclick="mark_click()" class="reg-sub"/>
            </td>
        </tr>

        <tr>
            <th><?php echo $form->labelEx($model, 'lat'); ?></th>
            <td>
                <?php echo $form->textField($model, 'lat', array('class' => 'inputtxt1', 'style' => 'width:340px')); ?>
                <?php echo $form->error($model, 'lat'); ?>

            </td>
        </tr>

    </tbody>
</table>


<div class="profileDo mt15">
    <input type="hidden" id="isf"  value="0" />
    <a href="#" class="sellerBtn03 submitBt"><span><?php echo Yii::t('partnerModule.machine', '保存'); ?></span></a>&nbsp;&nbsp;
    <a href="javascript:history.go(-1);" class="sellerBtn01"><span><?php echo Yii::t('partnerModule.machine', '返回'); ?></span></a>
</div>
<?php $this->endWidget(); ?>
<script type="text/javascript" language="javascript" src="/js/iframeTools.source.js"></script>
<script>
                     var mark_click = function () {
                         var url = '<?php echo Yii::app()->createAbsoluteUrl('partner/map/show') ?>';
                         url += '?lng=' + $('#VendingMachine_lng').val() + '&lat=' + $('#VendingMachine_lat').val();
                         dialog = art.dialog.open(url, {
                             'title': '<?php echo Yii::t('partnerModule.machine','设定经纬度');?>',
                             'lock': true,
                             'window': 'top',
                             'width': 740,
                             'height': 600,
                             'border': true
                         });
                     };

                     var onSelected = function (lat, lng) {
                         $('#VendingMachine_lng').val(lng);
                         $('#VendingMachine_lat').val(lat);
                     };

                     var doClose = function () {
                         if (null != dialog) {
                             dialog.close();
                         }
                     };


                     $(".submitBt").click(function () {
                         $("#Supermarkets_name").blur();
                         setTimeout(function () {
<?php if (!$model->isNewRecord) { ?>
                                 $("#isf").val("0");
                                 $(".errorMessage[style='']").each(function (i) {
                                     if ($(this).html() != "") {
                                         $("#isf").val($("#isf").val() + 1);
                                     }
                                 });
                                 if ($("#isf").val() == "0") {
                                     if (confirm('<?php echo Yii::t('partnerModule.machine','编辑将会重新审核商家信息，确定要编辑吗？');?>')) {
                                         $("form").submit();
                                     } else {
                                         return false;
                                     }
                                 }

<?php } ?>
                             $("form").submit();
                         }, 500);
                     });

                     $("#VendingMachine_address").change(function () {
                         var address = $("#VendingMachine_province_id").find("option:selected").text() + $("#VendingMachine_city_id").find("option:selected").text() + $("#VendingMachine_district_id").find("option:selected").text() + $(this).val();
                         var apiurl = '<?php echo Yii::app()->createAbsoluteUrl('partner/region/searchLocation') ?>?address=' + address;
                         $.getJSON(apiurl, function (data) {
                             if (data.status == 0) {
                                 $("#VendingMachine_lng").val(data.result.location.lng);
                                 $("#VendingMachine_lat").val(data.result.location.lat);
                             }
                         });

                     });

</script>