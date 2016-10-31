<?php
/* @var $this FreshMachineController */
/* @var $model FreshMachine */


?>
<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => $this->id . '-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
    'clientOptions' => array(
        'validateOnSubmit' => true, // 客户端验证
    ),
));
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come">
    <tr>
        <td colspan="2" class="title-th">
            <?php echo Yii::t('FreshMachine', '生鲜机机信息'); ?>
        </td>
    </tr> 
     <?php if(!$model->isNewRecord): ?>
    <tr>
        <th align="right">
            <?php echo Yii::t('FreshMachine','装机编码')?>：
        </th>
        <td>
            <?php echo $form->textField($model, 'code', array('class' => 'text-input-bj long')); ?>
            <?php echo $form->error($model, 'code'); ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo Yii::t('FreshMachine','激活编码')?>：
        </th>
        <td>
            <?php echo $form->textField($model, 'activation_code', array('class' => 'text-input-bj long')); ?>
            <?php echo $form->error($model, 'activation_code'); ?>
        </td>
        </tr>
        <?php        endif;?>
        
        
             <tr>
         <th align="right">
            <?php echo $form->labelEx($model, 'gai_number'); ?>：
        </th>
        <td>
             <?php echo $form->textField($model, 'gai_number', array('class' => 'text-input-bj long')); ?><b style="color:red">(注意，请确保GW号填写正确，此处为关联商家的GW号)</b>
            <?php echo $form->error($model, 'gai_number'); ?>
        </td>
         
    </tr>
        
    <tr>
         <th align="right">
            <?php echo $form->labelEx($model, 'name'); ?>：
        </th>
        <td>
             <?php echo $form->textField($model, 'name', array('class' => 'text-input-bj long')); ?>
            <?php echo $form->error($model, 'name'); ?>
        </td>
         
    </tr>

    <tr class="showMore">
            <th align="right"><?php echo $form->labelEx($model, 'thumb'); ?>：</th>
            <td>
                <?php
                $this->widget('widgets.CUploadPic', array(
                    'attribute' => 'thumb',
                    'model'=>$model,
                    'form'=>$form,
                    'num' => 1,
                    'btn_value'=> Yii::t('FreshMachine', '上传图片'),
                    'folder_name' => 'FreshMachine',
                ));
                ?>
              <span><?php echo $form->error($model, 'thumb', array('style' => 'position: relative; display: inline-block'), false, false); ?> </span>
    
            </td>
        </tr> 
        <tr>
        <th align="right">
        <?php echo $form->labelEx($model, 'type'); ?>：
        </th>
        <td>
             <?php echo $form->dropDownList($model,'type', FreshMachine::getType(),
                     array('class' => 'text-input-bj','empty' => Yii::t('FreshMachine', '请选择'))); ?>
                <?php echo $form->error($model, 'type'); ?>
        </td>
        </tr>
    <tr>
        <th align="right">
          <?php echo $form->labelEx($model, 'category_id'); ?>：
        </th>
        <td>
            <?php
            echo $form->dropDownList($model, 'category_id', CHtml::listData(StoreCategory::model()->findAll(), 'id', 'name'), array('class' => 'text-input-bj', 'empty' => Yii::t('FreshMachine', '请选择'))
            );
            ?>
            <?php echo $form->error($model, 'category_id'); ?>
        </td>
        </tr>
    <tr>
        <th>
         <?php echo $form->labelEx($model, 'status'); ?>：
        </th>          
        <td>
             <?php echo $form->radioButtonList($model, 'status', VendingMachine::getStatus(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'status'); ?>
        </td>
        </tr>
    <tr>
        <th><?php echo Yii::t('FreshMachine','是否激活')?>：</th>
        <td>
          <?php echo $form->radioButtonList($model, 'is_activate', VendingMachine::getIsActivate(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'is_activate'); ?>
        </td>
        
        
    </tr>
    <tr>
        <th><?php echo Yii::t('FreshMachine','是否推荐')?>：</th>
        <td>
            <?php echo $form->radioButtonList($model, 'is_recommend', VendingMachine::getIsRencommend(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'is_recommend'); ?>
        </td>
        </tr>
          <tr>
        <th><?php echo Yii::t('FreshMachine','币种')?>：</th>
        <td>
            <?php echo $form->radioButtonList($model, 'symbol', VendingMachine::getMoney(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'symbol'); ?>
        </td>
        </tr>
    <tr>
          <th><?php echo $form->labelEx($model, 'province_id'); ?>：</th>
            <td>
                <?php
                echo $form->dropDownList($model, 'province_id', Region::getRegionByParentId(Region::PROVINCE_PARENT_ID), array(
                    'prompt' => Yii::t('FreshMachine', Yii::t('address', '选择省份')),
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
                            $("#FreshMachine_city_id").html(data.dropDownCities);
                            $("#FreshMachine_district_id").html(data.dropDownCounties);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'city_id', Region::getRegionByParentId($model->province_id), array(
                    'prompt' => Yii::t('FreshMachine', Yii::t('address', '选择城市')),
                    'class' => 'text-input-bj',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createUrl('/region/updateArea'),
                        'update' => '#FreshMachine_district_id',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),  
                        'success' => 'function(data) {                          
                            $("#FreshMachine_district_id").html(data);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'district_id', Region::getRegionByParentId($model->city_id), array(
                    'prompt' => Yii::t('FreshMachine', Yii::t('address', '选择区/县')),
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
          <th><?php echo $form->labelEx($model, 'address'); ?>：</th>
          <td>
              <?php echo $form->textField($model, 'address', array('class' => 'text-input-bj long')); ?>
            <?php echo $form->error($model, 'address'); ?>
          </td>        
      </tr>
      <tr>
          <th><?php echo Yii::t('FreshMachine','管理员id')?>：</th>
          <td><?php echo $form->textField($model, 'user_id', array('class' => 'text-input-bj long'));?></td>
        </tr>
    <tr>  
          <th><?php echo Yii::t('FreshMachine','管理员ip')?>：</th>
          <td><?php echo $form->textField($model, 'user_ip', array('class' => 'text-input-bj long'));?></td>
       </tr>
    <tr>   
         <tr>  
          <th><?php echo Yii::t('FreshMachine','管理员手机')?>：</th>
          <td><?php echo $form->textField($model, 'mobile', array('class' => 'text-input-bj long'));?>
          <?php echo $form->error($model, 'mobile'); ?>
          </td>
          
       </tr>
   
      
      <tr>
          <th><?php echo Yii::t('FreshMachine','安装时间')?>：</th>
          <td><?php echo empty($model->setup_time) ? '':date('Y-m-d H:i:s',$model->setup_time);?></td>
          </tr>
    <tr>
          <th><?php echo Yii::t('FreshMachine','设备id')?>：</th>
          <td>
               <?php echo $form->textField($model, 'device_id', array('class' => 'text-input-bj long')); ?>
            <?php echo $form->error($model, 'device_id'); ?>
          </td>
       </tr>
    <tr>   
          <th><?php echo Yii::t('FreshMachine','备注')?>：</th>
          <td>
                <?php echo $form->textField($model, 'remark', array('class' => 'text-input-bj long')); ?>
            <?php echo $form->error($model, 'remark'); ?>
          </td>
      </tr>
                
      <tr>
          <th><?php echo Yii::t('FreshMachine','创建时间')?>：</th>
         <td><?php echo empty($model->create_time) ? '':date('Y-m-d H:i:s',$model->create_time);?></td>
         </tr>
    <tr> 
          <th><?php echo Yii::t('FreshMachine','修改时间')?>：</th>
          <td><?php echo empty($model->update_time) ? '':date('Y-m-d H:i:s',$model->update_time);?></td>
      </tr>
  
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'lng'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'lng', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'lng'); ?>
                 (已启用地址自动搜索坐标功能，如无法定位坐标，请进入此地址手动添加数据：<a href="http://api.map.baidu.com/lbsapi/getpoint/" target="_blank">http://api.map.baidu.com/lbsapi/getpoint/</a>)
            </td>
        </tr>
        
         <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'lat'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'lat', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'lat'); ?>
            </td>
        </tr>
         <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'max_amount_preday'); ?>：</th>
             <td>
                <?php echo $form->textField($model, 'max_amount_preday', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'max_amount_preday'); ?>
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
            <?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('FreshMachine', '新增') : Yii::t('FreshMachine', '保存'), array('class' => 'reg-sub')); ?>
          </td>
      </tr>
      <?php $this->endWidget();?>
</table>
<script type="text/javascript">
$("#FreshMachine_address").change(function () {
    var address = $("#FreshMachine_province_id").find("option:selected").text() + $("#FreshMachine_city_id").find("option:selected").text() + $("#FreshMachine_district_id").find("option:selected").text() + $(this).val();
    var apiurl = '<?php echo Yii::app()->createAbsoluteUrl('region/searchLocation') ?>?address=' + address;
    $.getJSON(apiurl, function (data) {
        if (data.status == 0) {
            $("#FreshMachine_lng").val(data.result.location.lng);
            $("#FreshMachine_lat").val(data.result.location.lat);
        }
    });

});
</script>