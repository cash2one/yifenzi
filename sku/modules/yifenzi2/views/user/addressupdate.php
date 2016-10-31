<style>


</style>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'address-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
    )
));
?>

<header>
		<h2>收货地址</h2>
		<a href="javascript:history.go(-1);" class="goback_btn"></a>
		<a href="<?php echo $this->createUrl('user/addressDel',array('id'=>$model['id']))?>" class="del_address">删除</a>
	</header>
<div class="container">
<div class="add_address">
    <div class="input_list">
        <?php echo $form->textField($model, 'real_name', array('placeholder' => '收件人姓名'));?>
        <span class="tips"><?php echo $form->error($model, 'real_name'); ?></span>
    </div>
    <div class="input_list">
    <?php echo $form->textField($model, 'mobile', array('placeholder' => '手机号码'));?>
        <span class="tips"><?php echo $form->error($model, 'mobile'); ?></span>
    </div>
    <div class="province_choose input_list">
        <?php
        echo $form->dropDownList($model, 'province_id', Region::getRegionByParentId(Region::PROVINCE_PARENT_ID), array(
            'prompt' =>  Yii::t('address', '选择省份'),
            'class' => 'text-input-bj',
            'ajax' => array(
                'type' => 'POST',
                'url' => $this->createUrl('region/updateCity'),
                'dataType' => 'json',
                'data' => array(
                    'province_id' => 'js:this.value',
                    'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                ),
                'success' => 'function(data) {
                            $("#Address_city_id").html(data.dropDownCities);
                        }',
            )));
        ?>
        <?php
        echo $form->dropDownList($model, 'city_id', Region::getRegionByParentId($model->province_id), array(
            'prompt' => Yii::t('address', '选择城市'),
            'class' => 'text-input-bj',
            'ajax' => array(
                'type' => 'POST',
                'url' => $this->createUrl('region/updateArea'),
                'data' => array(
                    'city_id' => 'js:this.value',
                    'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                ),
                'success' => 'function(data) {
                            $("#Address_district_id").html(data);
                        }',
            )));
        ?>
        <?php
        echo $form->dropDownList($model, 'district_id', Region::getRegionByParentId($model->city_id), array(
            'prompt' => Yii::t('address', '选择城市'),
            'class' => 'text-input-bj',
            ));
        ?>
        <div style="display:block;width:300px;float:left;margin-left:380px;">
            <span class="tips">
                <?php echo $form->error($model, 'district_id'); ?>
            </span>
        </div>
    </div>
    <div class="input_list">
        <?php echo $form->textField($model, 'street', array('placeholder' => '详细地址'));?>
        <span class="tips"><?php echo $form->error($model, 'street'); ?></span>
    </div>
</div>
</div>
<footer class="detail ongoing addressCtrl">
		<input type ="submit" class="saveBtns" value="修改">
		<a href="<?php echo $this->createUrl('user/addressSetting',array('id'=>$model['id']))?>">设为默认地址</a>
	</footer>
<?php $this->endWidget(); ?>