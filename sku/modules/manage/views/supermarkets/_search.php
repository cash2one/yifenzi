<?php
/* @var $this ProductController */
/* @var $model Product */
/* @var $form CActiveForm */
?>

<div class="border-info clearfix search-form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
                <th>
                    <?php echo $form->label($model, 'name'); ?>：
                </th>
                <td>
                    <?php echo $form->textField($model, 'name', array('size' => 11, 'maxlength' => 11, 'class' => 'text-input-bj  least')); ?> 
                </td>    
            </tr>
        </tbody>
    </table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
                <th>
                    <?php echo $form->label($model, 'gai_number'); ?>：
                </th>
                <td>
                    <?php echo $form->textField($model, 'gai_number', array('size' => 11, 'maxlength' => 11, 'class' => 'text-input-bj  least')); ?> 
                </td>    
            </tr>
        </tbody>
    </table>
    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <?php echo Yii::t('supermarkets','状态'); ?>：
            </th>
            <td id="tdPay">
                <?php echo $form->radioButtonList($model,'status', Supermarkets::getStatus(),
                    array('empty'=>Yii::t('order','全部'),'separator'=>'')) ?>
            </td>
        </tr>
        </tbody></table>
  <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <?php echo Yii::t('supermarkets','是否送货上门'); ?>：
            </th>
            <td id="tdPay">
                <?php echo $form->radioButtonList($model,'is_delivery', Supermarkets::getDelivery(),
                    array('empty'=>Yii::t('order','全部'),'separator'=>'')) ?>
            </td>
        </tr>
        </tbody>
  </table>
     <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
            <th align="right">
                <?php echo Yii::t('supermarkets','是否推荐'); ?>：
            </th>
            <td id="tdPay">
                <?php echo $form->radioButtonList($model,'is_recommend', Supermarkets::getIsRencommend(),
                    array('empty'=>Yii::t('order','全部'),'separator'=>'')) ?>
            </td>
        </tr>
        </tbody>
  </table>
 <table cellspacing="0" cellpadding="0" class="searchTable">
     <tbody><tr>
          <th><?php echo Yii::t('supermarkets','地区'); ?>：</th>
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
                    'prompt' => Yii::t('supermarkets', Yii::t('address', '选择城市')),
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
            </td>
         </tr></tbody>
 </table>

    <table cellspacing="0" cellpadding="0" class="searchTable">
        <tbody><tr>
                <td colspan="2">
                    <?php echo CHtml::submitButton('搜索', array('class' => 'reg-sub')) ?>
                </td>
            </tr></tbody>
    </table>

    <?php $this->endWidget(); ?>

</div>
