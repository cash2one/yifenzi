<?php
$this->breadcrumbs = array(
    Yii::t('appAdvertPicture', '广告位图片') => array('admin', 'advert_id' => $model->advert_id),
    $model->isNewRecord ? Yii::t('appAdvertPicture', '新增') : Yii::t('appAdvertPicture', '修改')
);
?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'appAdvertPicture-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));
?>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tbody><tr><td colspan="2" class="title-th even" align="center"><?php echo $model->isNewRecord ? Yii::t('appAdvertPicture', '添加广告') : Yii::t('appAdvertPicture', '修改广告'); ?></td></tr></tbody>
    <tbody>
        <tr>
            <th style="width: 220px" class="odd">
                <?php echo $form->labelEx($model, 'advert_id'); ?>
            </th>
            <td class="odd">
                <?php echo $advert->name; ?>
            </td>
        </tr>
        <tr>
            <th style="width: 220px" class="even">
                <?php echo $form->labelEx($model, 'name'); ?>
            </th>
            <td class="even">
                <?php echo $form->textField($model, 'name', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'name'); ?>
            </td>
        </tr>
        <tr>
            <th><?php echo Yii::t('partner', '广告所属地'); ?></th>
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
                            $("#AppAdvertPicture_city_id").html(data.dropDownCities);

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
//                        'update' => '#Partners_district_id',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                        'success' => 'function(data) {

                        }',
                    )));
                ?>


                <div style="display:block;width:300px;float:left;margin-left:380px;">
<!--                    --><?php //echo $form->error($model, 'district_id', array('style' => 'position: absolute;top:6px;right:132px;')); ?>
                    <?php echo $form->error($model, 'city_id', array('style' => 'position: absolute;top:6px;right:259px')); ?>
                    <?php echo $form->error($model, 'province_id', array('style' => 'position: absolute;top:6px;')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <th style="width: 220px" class="odd">
                <?php echo $form->labelEx($model, 'start_time'); ?>
            </th>
            <td class="odd">
                <?php $this->widget('comext.timepicker.timepicker', array('model' => $model, 'name' => 'start_time', 'options' => array('value' => date('Y-m-d H:i:s')))); ?>
                <?php echo $form->error($model, 'start_time'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 220px" class="even">
                <?php echo $form->labelEx($model, 'end_time'); ?>
            </th>
            <td class="even">
                <?php $this->widget('comext.timepicker.timepicker', array('model' => $model, 'name' => 'end_time', 'options' => array())); ?>
                <?php echo $form->error($model, 'end_time'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 220px" class="odd">
                <?php echo $form->labelEx($model, 'status'); ?>
            </th>
            <td class="odd">
                <?php echo $form->radioButtonList($model, 'status', AppAdvertPicture::getStatus(), array('separator' => '')); ?>
                <?php echo $form->error($model, 'status'); ?>
            </td>
        </tr>
<!--        <tr>
            <th style="width: 220px" class="even">
                <?php //echo $form->labelEx($model, 'link'); ?>
            </th>
            <td class="even">
                <?php //echo $form->textField($model, 'link', array('class' => 'text-input-bj  middle')); ?>
                <?php //echo $form->error($model, 'link'); ?>
            </td>
        </tr>-->
        <!--根据广告位类型,现在内容-->
        <?php if (1 == $advert->type || 2 == $advert->type): ?>
<!--            <tr>-->
<!--                <th style="width: 220px" class="odd">-->
<!--                    --><?php //echo $form->labelEx($model, 'picture'); ?>
<!--                </th>-->
<!--                <td class="odd">-->
<!--                    --><?php //echo $form->fileField($model, 'picture','') ?>
<!--                    --><?php //echo $form->error($model, 'picture'); ?>
<!--                    <span style="color: Red;">* </span>--><?php //Yii::t('appAdvertPicture', '请上传图片'); ?><!-- -->
<!--                    --><?php //if ($model->picture): ?>
<!--                        --><?php //echo CHtml::hiddenField('oldFile', $model->picture); ?>
<!--                        --><?php //echo CHtml::image(ATTR_DOMAIN . '/' . $model->picture, $model->name, array('width' => '200px')); ?>
<!--                    --><?php //endif; ?>
<!--                </td>-->
<!--            </tr>-->
            <tr>
                <th ><?php echo $form->labelEx($model, 'picture'); ?></th>
                <td>
                    <p>
                        <?php echo $form->fileField($model, 'picture') ?>&nbsp;&nbsp;
                        <span class="gray"><?php echo Yii::t('appAdvertPicture', '请上传不大于1M的图片'); ?></span>
                    </p>
                    <?php echo $form->error($model, 'picture', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                    <?php if (!empty($model->picture)): ?>
                        <p class="mt10">
                            <img src="<?php echo ATTR_DOMAIN . '/' . $model->picture ?>" width="120"/>
                        </p>
                    <?php endif; ?>
                </td>

            </tr>
        <?php elseif (3 == $advert->type): ?>
            <tr>
                <th style="width: 220px" class="odd">
                    <?php echo $form->labelEx($model, 'text'); ?>
                </th>
                <td class="odd">
                    <?php echo $form->textField($model, 'text', array('class' => 'text-input-bj  middle')); ?>
                    <?php echo $form->error($model, 'text'); ?>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <th style="width: 220px" class="odd">
                <?php echo $form->labelEx($model, 'group'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textField($model, 'group', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'group'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 220px" class="odd">
                <?php echo $form->labelEx($model, 'seat'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textField($model, 'seat', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'seat'); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 220px" class="odd">
                <?php echo $form->labelEx($model, 'target_type'); ?>
            </th>
            <td class="odd">
                <?php echo $form->radioButtonList($model, 'target_type', AppAdvertPicture::getTargetType(), array('separator' => '')); ?>
            </td>
        </tr>
        <tr>
            <th style="width: 220px" class="odd">
                <?php echo $form->labelEx($model, 'target'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textField($model, 'target', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'target'); ?>
                <br/>
                
                <span style="color: red">注意：<b>店铺跳转类型</b>广告 请设置为此格式[ 商铺类型-商铺id ]  如："3-2"。（店铺类型对照：1是门店，2售货机，3是生鲜机。）</span>
                
            </td>
        </tr>
        <tr>
            <th style="width: 220px" class="odd">
                <?php echo $form->labelEx($model, 'sort'); ?>
            </th>
            <td class="odd">
                <?php echo $form->textField($model, 'sort', array('class' => 'text-input-bj  middle')); ?>
                <?php echo $form->error($model, 'sort'); ?>
            </td>
        </tr>
        <tr>
            <th class="odd"></th>
            <td colspan="2" class="odd">
                <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('appAdvertPicture', '新增') : Yii::t('appAdvertPicture', '保存'), array('class' => 'reg-sub')); ?>
            </td>
        </tr>
    </tbody>
</table>    
<?php $this->endWidget(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        toggleTr($('input[name="Advert[direction]"]:checked').val());
    });
    function toggleTr(value) {
        $("#cityTr").hide();
        $("#categoryTr").hide();
        if (value === '1') {
            $("#cityTr").show();
        } else if (value === '2') {
            $("#categoryTr").show();
        }
    }
    
$(document).ready(function() {
$("#AppAdvertPicture_picture").change(function() {
        $("#AppAdvertPicture_picture_em_").remove();
    });
   });
</script>