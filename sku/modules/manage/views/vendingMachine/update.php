<?php
/* @var $this VendingMachineController */
/* @var $model VendingMachine */

$this->breadcrumbs = array(
    Yii::t('vendingMachine', '售货机管理')=>array('admin'),
    Yii::t('vendingMachine', '售货机信息编辑'),
);
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
            <?php echo Yii::t('vendingMachine', '售货机信息'); ?>
        </td>
    </tr> 
    <tr>
        <th align="right">
            <?php echo Yii::t('vendingMachine','装机编码')?>：
        </th>
        <td>
            <?php echo $form->textField($model, 'code', array('class' => 'text-input-bj long')); ?>
            <?php echo $form->error($model, 'code'); ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo Yii::t('vendingMachine','激活编码')?>：
        </th>
        <td>
            <?php echo $form->textField($model, 'activation_code', array('class' => 'text-input-bj long')); ?>
            <?php echo $form->error($model, 'activation_code'); ?>
        </td>
        </tr>
    <tr>
         <th align="right">
            <?php echo $form->labelEx($model, 'name'); ?>：
        </th>
        <td>
            <?php echo $model->name; ?>
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
                    'btn_value'=> Yii::t('vendmachine', '上传图片'),
                    'folder_name' => stristr($model->thumb,'/',true),
                ));
                ?>（如需修改图片请先删除原图片）
              <span><?php echo $form->error($model, 'thumb', array('style' => 'position: relative; display: inline-block'), false, false); ?> </span>
    
            </td>
        </tr> 
    <tr>
        <th align="right">
          <?php echo $form->labelEx($model, 'category_id'); ?>：
        </th>
        <td>
            <?php
            echo $form->dropDownList($model, 'category_id', CHtml::listData(StoreCategory::model()->findAll(), 'id', 'name'), array('class' => 'text-input-bj', 'empty' => Yii::t('vendingMachine', '请选择'))
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
        <th><?php echo Yii::t('vendingMachine','是否激活')?>：</th>
        <td>
          <?php echo $form->radioButtonList($model, 'is_activate', VendingMachine::getIsActivate(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'is_activate'); ?>
        </td>
        
        
    </tr>
    <tr>
        <th><?php echo Yii::t('vendingMachine','是否推荐')?>：</th>
        <td>
            <?php echo $form->radioButtonList($model, 'is_recommend', VendingMachine::getIsRencommend(), array('separator' => '')); ?>
            <?php echo $form->error($model, 'is_recommend'); ?>
        </td>
        </tr>
          <tr>
        <th><?php echo Yii::t('vendingMachine','币种')?>：</th>
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
                    'prompt' => Yii::t('vendingMachine', Yii::t('address', '选择省份')),
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
                            $("#VendingMachine_city_id").html(data.dropDownCities);
                            $("#VendingMachine_district_id").html(data.dropDownCounties);
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
                        'update' => '#vendingMachine_district_id',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),  
                        'success' => 'function(data) {                          
                            $("#VendingMachine_district_id").html(data);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'district_id', Region::getRegionByParentId($model->city_id), array(
                    'prompt' => Yii::t('vendingMachine', Yii::t('address', '选择区/县')),
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
          <th><?php echo Yii::t('vendingMachine','管理员id')?>：</th>
          <td><?php echo $form->textField($model, 'user_id', array('class' => 'text-input-bj long'));?></td>
        </tr>
    <tr>  
          <th><?php echo Yii::t('vendingMachine','管理员ip')?>：</th>
          <td><?php echo $form->textField($model, 'user_ip', array('class' => 'text-input-bj long'));?></td>
       </tr>
    <tr>   
         <tr>  
          <th><?php echo Yii::t('vendingMachine','管理员手机')?>：</th>
          <td><?php echo $form->textField($model, 'mobile', array('class' => 'text-input-bj long'));?>
          <?php echo $form->error($model, 'mobile'); ?>
          </td>
          
       </tr>
    <tr>
          <th><?php echo Yii::t('vendingMachine','加盟商id')?>：</th>
          <td><?php echo $model->member_id?></td>
      </tr>
      
      <tr>
          <th><?php echo Yii::t('vendingMachine','安装时间')?>：</th>
          <td><?php echo empty($model->setup_time) ? '':date('Y-m-d H:i:s',$model->setup_time);?></td>
          </tr>
    <tr>
          <th><?php echo Yii::t('vendingMachine','设备id')?>：</th>
          <td>
               <?php echo $form->textField($model, 'device_id', array('class' => 'text-input-bj long')); ?>
            <?php echo $form->error($model, 'device_id'); ?>
          </td>
       </tr>
    <tr>   
          <th><?php echo Yii::t('vendingMachine','备注')?>：</th>
          <td><?php echo $model->remark?></td>
      </tr>
                
      <tr>
          <th><?php echo Yii::t('vendingMachine','创建时间')?>：</th>
          <td><?php echo empty($model->create_time) ? '':date('Y-m-d H:i:s',$model->create_time);?></td></th>
         </tr>
    <tr> 
          <th><?php echo Yii::t('vendingMachine','修改时间')?>：</th>
          <td><?php echo empty($model->update_time) ? '':date('Y-m-d H:i:s',$model->update_time);?></td></th>
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
              <?php echo CHtml::submitButton(Yii::t('vendingMachine', '编辑'), array('class' => 'reg-sub')); ?>
          </td>
      </tr>
      <?php $this->endWidget();?>
</table>

<script type="text/javascript">
$("#VendingMachine_address").change(function () {
    var address = $("#VendingMachine_province_id").find("option:selected").text() + $("#VendingMachine_city_id").find("option:selected").text() + $("#VendingMachine_district_id").find("option:selected").text() + $(this).val();
    var apiurl = '<?php echo Yii::app()->createAbsoluteUrl('region/searchLocation') ?>?address=' + address;
    $.getJSON(apiurl, function (data) {
        if (data.status == 0) {
            $("#VendingMachine_lng").val(data.result.location.lng);
            $("#VendingMachine_lat").val(data.result.location.lat);
        }
    });

});
</script>
