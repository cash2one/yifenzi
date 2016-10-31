<?php
/* @var $this OrderController */
/* @var $model Orders */
/* @var $form CActiveForm */
?>
<?php $form=$this->beginWidget('CActiveForm', array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>
<div class="border-info clearfix">

    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <?php echo Yii::t('partners','广告图片名称'); ?>：
            </th>
            <td>
                <?php echo $form->textField($model,'name',array('size'=>11,'maxlength'=>11,'class'=>'text-input-bj  least')); ?>
            </td>
        </tr>
        <input name="AppAdvertPicture[advert_id]" type="hidden" value="<?php echo $id ; ?>" >
        </tbody>
    </table>
     <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
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
            </td>
        </tr>
        <tr>
        <tr><?php echo CHtml::submitButton('搜索',array('class'=>'reg-sub')) ?></tr>
        </tbody>
    </table>

<div class="c10">
</div>
<?php $this->endWidget(); ?>
