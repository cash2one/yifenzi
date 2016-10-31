<?php
/* @var $this PartnersController */
/* @var $model Partners */
/** @var  $form CActiveForm */


$this->breadcrumbs = array(
    '商户' => array('admin'),
    '编辑'
);
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'partners-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
       
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));
?>
    <script src="<?php echo DOMAIN; ?>/js/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
    <script src="<?php echo DOMAIN; ?>/js/timePicker/timePicker.css" type="text/javascript"></script>
    <script src="<?php echo DOMAIN; ?>/js/timePicker/jquery.timePicker.min.js" type="text/javascript"></script>

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


        <tr>
            <th ><?php echo $form->labelEx($model, 'head'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'head') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'head', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->head)): ?>
                    <p class="mt10">
                    <a href="<?php echo ATTR_DOMAIN . '/' . $model->head ?>" target="_blank">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->head ?>" width="120"/>
                        </a>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
    
        <tr>
            <th style="width: 120px" align="right"><?php echo Yii::t('partners','盖网号'); ?>：</th>
            <td>
                <?php echo $model->gai_number; ?>             
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
          <th><?php echo $form->labelEx($model, 'province_id'); ?>：</th>
            <td>
                <?php
                echo $form->dropDownList($model, 'province_id', Region::getRegionByParentId(Region::PROVINCE_PARENT_ID), array(
                    'prompt' => Yii::t('partners', Yii::t('address', '选择省份')),
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
                            $("#Partners_city_id").html(data.dropDownCities);
                            $("#Partners_district_id").html(data.dropDownCounties);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'city_id', Region::getRegionByParentId($model->province_id), array(
                    'prompt' => Yii::t('partners', Yii::t('address', '选择城市')),
                    'class' => 'text-input-bj',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createUrl('/region/updateArea'),
                        'update' => '#Partners_district_id',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),  
                        'success' => 'function(data) {                          
                            $("#Partners_district_id").html(data);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'district_id', Region::getRegionByParentId($model->city_id), array(
                    'prompt' => Yii::t('partners', Yii::t('address', '选择区/县')),
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
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'idcard'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'idcard', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'idcard'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'bank_account'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'bank_account', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'bank_account'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'bank_account_name'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'bank_account_name', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'bank_account_name'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'bank_area'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'bank_area', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'bank_area'); ?>
            </td>
        </tr>
        
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'bank_name'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'bank_name', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'bank_name'); ?>
            </td>
        </tr>
        
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'bank_account_branch'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'bank_account_branch', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'bank_account_branch'); ?>
            </td>
        </tr>
        <tr>
            <th ><?php echo $form->labelEx($model, 'bank_card_img'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'bank_card_img') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'bank_card_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->bank_card_img)): ?>
                    <p class="mt10">
                    	<a href="<?php echo ATTR_DOMAIN . '/' . $model->bank_card_img ?>" target="_blank">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->bank_card_img ?>" width="120"/>
                        </a>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        <tr>
            <th ><?php echo $form->labelEx($model, 'idcard_img_font'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'idcard_img_font') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'idcard_img_font', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->idcard_img_font)): ?>
                    <p class="mt10">
                    <a href="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_font ?>" target="_blank">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_font ?>" width="120"/>
                        </a>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        <tr class="odd">
            <th ><?php echo $form->labelEx($model, 'idcard_img_back'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'idcard_img_back') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'idcard_img_back', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->idcard_img_back)): ?>
                    <p class="mt10">
                    <a href="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_back ?>" target="_blank">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->idcard_img_back ?>" width="120"/>
                        </a>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        


        <tr class="odd">
            <th><?php echo $form->labelEx($model, 'license_img'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'license_img') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'license_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->license_img)): ?>
                    <p class="mt10">
                    <a href="<?php echo ATTR_DOMAIN . '/' . $model->license_img ?>" target="_blank">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->license_img ?>" width="120"/>
                        </a>
                    </p>
                <?php endif; ?>
            </td>

        </tr>

        <tr id="licenseTime">
            <td height="25" colspan="2" class="dtFff pdleft20">
                <?php echo $form->labelEx($model, 'license_expired_time'); ?>：
                <?php $model->license_expired_time = date('Y-m-d',(int)$model->license_expired_time)?>
                <?php $model->license_expired_time = $model->license_expired_time =='1970-01-01'?"":$model->license_expired_time;?>
                <?php echo $form->textField($model, 'license_expired_time', array('class'=>'inputtxt1 inputbox width200 datefield', 'onclick'=>'WdatePicker({dateFmt:"yyyy-MM-dd"})'))?>
                <?php echo $form->error($model, 'license_expired_time') ?>
            </td>
        </tr>
        <tr>
            <th ><?php echo $form->labelEx($model, 'meat_inspection_certificate_img'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'meat_inspection_certificate_img') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'meat_inspection_certificate_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->meat_inspection_certificate_img)): ?>
                    <p class="mt10">
                    <a href="<?php echo ATTR_DOMAIN . '/' . $model->meat_inspection_certificate_img ?>" target="_blank">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->meat_inspection_certificate_img ?>" width="120"/>
                        </a>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        <tr id="licenseTime">
            <td height="25" colspan="2" class="dtFff pdleft20">
                <?php echo $form->labelEx($model, 'meat_inspection_expired_time'); ?>：
                <?php $model->meat_inspection_expired_time = date('Y-m-d',(int)$model->meat_inspection_expired_time)?>
                <?php $model->meat_inspection_expired_time = $model->meat_inspection_expired_time =='1970-01-01'?"":$model->meat_inspection_expired_time;?>
                <?php echo $form->textField($model, 'meat_inspection_expired_time', array('class'=>'inputtxt1', 'onfocus' => 'WdatePicker({dateFmt:"yyyy-MM-dd"})'))?>
                <?php echo $form->error($model, 'meat_inspection_expired_time') ?>
            </td>
        </tr>
        <tr class="odd">
            <th><?php echo $form->labelEx($model, 'health_permit_certificate_img'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'health_permit_certificate_img') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'health_permit_certificate_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->health_permit_certificate_img)): ?>
                    <p class="mt10">
                    <a href="<?php echo ATTR_DOMAIN . '/' . $model->health_permit_certificate_img ?>" target="_blank">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->health_permit_certificate_img ?>" width="120"/>
                        </a>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        <tr id="licenseTime">
            <td height="25" colspan="2" class="dtFff pdleft20">
                <?php echo $form->labelEx($model, 'health_permit_expired_time'); ?>：
                <?php $model->health_permit_expired_time = date('Y-m-d',(int)$model->health_permit_expired_time)?>
                <?php $model->health_permit_expired_time = $model->health_permit_expired_time =='1970-01-01'?"":$model->health_permit_expired_time;?>
                <?php echo $form->textField($model, 'health_permit_expired_time', array('class'=>'inputtxt1', 'onfocus' => 'WdatePicker({dateFmt:"yyyy-MM-dd"})'))?>
                <?php echo $form->error($model, 'health_permit_expired_time') ?>
            </td>
        </tr>
        <tr>
            <th ><?php echo $form->labelEx($model, 'food_circulation_permit_certificate_img'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'food_circulation_permit_certificate_img') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'food_circulation_permit_certificate_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->food_circulation_permit_certificate_img)): ?>
                    <p class="mt10">
                    <a href="<?php echo ATTR_DOMAIN . '/' . $model->food_circulation_permit_certificate_img ?>" target="_blank">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->food_circulation_permit_certificate_img ?>" width="120"/>
                        </a>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        <tr id="licenseTime">
            <td height="25" colspan="2" class="dtFff pdleft20">
                <?php echo $form->labelEx($model, 'food_circulation_expired_time'); ?>：
                <?php $model->food_circulation_expired_time = date('Y-m-d',(int)$model->food_circulation_expired_time)?>
                <?php $model->food_circulation_expired_time = $model->food_circulation_expired_time =='1970-01-01'?"":$model->food_circulation_expired_time;?>
                <?php echo $form->textField($model, 'food_circulation_expired_time', array('class'=>'inputtxt1', 'onfocus' => 'WdatePicker({dateFmt:"yyyy-MM-dd"})'))?>
                <?php echo $form->error($model, 'food_circulation_expired_time') ?>
            </td>
        </tr>
        <tr class="odd">
            <th ><?php echo $form->labelEx($model, 'stock_source_certificate_img'); ?></th>
            <td>
                <p>
                    <?php echo $form->fileField($model, 'stock_source_certificate_img') ?>&nbsp;&nbsp;
                    <span class="gray"><?php echo Yii::t('partner', '请上传不大于1M的图片'); ?></span>
                </p>
                <?php echo $form->error($model, 'stock_source_certificate_img', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php if (!empty($model->stock_source_certificate_img)): ?>
                    <p class="mt10">
                    <a href="<?php echo ATTR_DOMAIN . '/' . $model->stock_source_certificate_img ?>" target="_blank">
                        <img src="<?php echo ATTR_DOMAIN . '/' . $model->stock_source_certificate_img ?>" width="120"/>
                        </a>
                    </p>
                <?php endif; ?>
            </td>

        </tr>
        <tr id="licenseTime">
            <td height="25" colspan="2" class="dtFff pdleft20">
                <?php echo $form->labelEx($model, 'stock_source_expired_time'); ?>：
                <?php $model->stock_source_expired_time = date('Y-m-d',(int)$model->stock_source_expired_time)?>
                <?php $model->stock_source_expired_time = $model->stock_source_expired_time =='1970-01-01'?"":$model->stock_source_expired_time;?>
                <?php echo $form->textField($model, 'stock_source_expired_time', array('class'=>'inputtxt1', 'onfocus' => 'WdatePicker({dateFmt:"yyyy-MM-dd"})'))?>
                <?php echo $form->error($model, 'stock_source_expired_time') ?>
            </td>
        </tr>
        <tr>
            <th style="width: 120px" align="right"><?php echo Yii::t('partners','创建时间') ?>：</th>
            <td>
                <?php echo date('Y-m-d H:i:s',$model->create_time); ?>
                <?php echo $form->error($model, 'create_time'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'status'); ?>：</th>
            <td>
                <?php echo $form->dropDownList($model,'status',  Partners::getStatus()); ?>
                <?php echo $form->error($model, 'status'); ?>
            </td>
        </tr>

          
        <tr>
            <th></th>
            <td>
                <?php echo CHtml::submitButton(Yii::t('goods', '编辑'), array('class' => 'reg-sub')); ?>
            </td>
        </tr>
    </tbody>
</table>
<?php $this->endWidget(); ?>