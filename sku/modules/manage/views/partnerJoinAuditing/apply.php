<?php $this->breadcrumbs = array(
		Yii::t('order', 'SKU商户加盟审核'),
		Yii::t('order', 'SKU商户加盟审核详情'),
);?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come" id="tab1">
    <tr>
        <td colspan="6" class="title-th">
            <?php echo Yii::t('PartnerJoinAuditing', 'sku商户加盟信息'); ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('name') ?>：
        </th>
        <td>
            <?php echo $model->name ?>
        </td>
     </tr>
      <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('store_name') ?>：
        </th>
        <td>
            <?php echo $model->store_name ?>
        </td>
     </tr>
    <tr>
        <th align="right">
            <?php echo Yii::t('PartnerJoinAuditing', '联系电话'); ?>：
        </th>
        <td>
        	<?php echo $model->mobile; ?>
        </td>
	</tr>
        <?php if($model->status != PartnerJoinAuditing::STATUS_ENABLE){?>
           <?php $api = new ApiMember(); $rs = $api->getInfo($model->mobile);         

            if(isset($rs['0'])){
                $gai = array('请选择GW号');
                $status =0;
                $select =0;
                foreach($rs as $k=>$v){
                    if($v['status'] =='1' ||  $v['status']=='0'){
                    $gai[$k+1] = $v['gai_number'];
                    $status++;
                    $select +=$k+1;
                    }
                }
                if($status>0){
    ?>
            <tr>
        <th align="right">
            <?php echo Yii::t('PartnerJoinAuditing', '电话绑定的GW号'); ?>：
        </th>
        <td>
         
        	 <?php echo CHtml::dropDownList( 'mobile',$status==1?$select:0,$gai,array('onchange'=>'getGw(this)')); ?>
          
        </td>
	</tr>

    <tr>
                <?php }}?>
        <?php }?>
        <th align="right">
            <?php echo $model->getAttributeLabel('gai_number') ?>：
        </th>
        <td>
            <?php echo $model->gai_number; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('referrals_gai_number') ?>：
        </th>
        <td>
            <?php echo $model->referrals_gai_number; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('id_name') ?>：
        </th>
        <td>
            <?php echo $model->id_name; ?>
        </td>
    </tr>
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('id_card') ?>：
        </th>
        <td>
            <?php echo $model->id_card; ?>
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('id_card_font_img') ?>：
        </th>
        <td>
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->id_card_font_img ?>" width="250"/>
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('id_card_back_img') ?>：
        </th>
        <td>
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->id_card_back_img ?>" width="250"/>
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('id_card_to_time') ?>：
        </th>
        <td>
            <?php echo date("Y-m-d",$model->id_card_to_time); ?>
        </td>
    </tr>
    <tr>
        <th align="right">
           <?php echo Yii::t('PartnerJoinAuditing', '商户联系地址') ?>：
        </th>
        <td>
        <?php echo Region::getName($model->store_province_id, $model->store_city_id, $model->store_district_id) ?>
          </br>
            <?php echo CHtml::encode($model->store_address) ?>
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo Yii::t('PartnerJoinAuditing', '店铺联系电话');?>：
        </th>
        <td>
            <?php echo $model->store_mobile; ?>
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('bank') ?>：
        </th>
        <td>
            <?php echo $model->bank; ?>
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('bank_account') ?>：
        </th>
        <td>
            <?php echo $model->bank_account; ?>
        </td>
    </tr>
    
      <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('bank_account_name') ?>：
        </th>
        <td>
            <?php echo $model->bank_account_name ?>
        </td>
     </tr>
    
    <tr>
        <th align="right">
           <?php echo Yii::t('PartnerJoinAuditing', '银行开户地址') ?>：
        </th>
        <td>
        <?php echo Region::getName($model->bank_province_id, $model->bank_city_id, $model->bank_district_id) ?>
          </br>
            <?php echo CHtml::encode($model->bank_branch) ?>
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('bank_img') ?>：
        </th>
        <td>
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->bank_img ?>" width="250"/>
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('status') ?>：
        </th>
        <td>
            <?php echo PartnerJoinAuditing::getStatus($model->status); ?>
        </td>
    </tr>
    
     <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('license_to_time') ?>：
        </th>
        <td>
            <?php echo date("Y-m-d",$model->license_to_time); ?>
        </td>
    </tr>
    
     <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('head') ?>：
        </th>
        <td>
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->head ?>" width="250"/>
        </td>
    </tr>
    
    <tr>
        <th align="right">
            <?php echo $model->getAttributeLabel('license_img') ?>：
        </th>
        <td>
            <img src="<?php echo ATTR_DOMAIN . '/' . $model->license_img ?>" width="250"/>
        </td>
    </tr>

	
	<tr>
        <th align="right">
            <?php echo Yii::t('order', '操作'); ?>：
        </th>
        <td>
        <?php if (Yii::app()->user->checkAccess('Manage.PartnerJoinAuditing.Apply') ):?>
        <?php if($model->status == PartnerJoinAuditing::STATUS_APPLY):?>
            <?php if(isset($rs['0'])&&$status>0){?>
            <a id="Btn_Add"  class="regm-sub" href = '<?php echo $this->createAbsoluteUrl('/manage/partnerJoinAuditing/apply',array('id'=>$model->id,'apply'=>'pass','gw'=>''));?>'>审核通过</a>
            <?php }else{?>
            <a id="Btn_Add"  class="regm-sub" href = '<?php echo $this->createAbsoluteUrl('/manage/partnerJoinAuditing/apply',array('id'=>$model->id,'apply'=>'pass'));?>'>审核通过</a>
            <?php } ?>		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       		<input id="Btn_Add1" type="button" value="审核不通过" class="regm-sub" onclick="location.href = '<?php echo $this->createAbsoluteUrl('/manage/partnerJoinAuditing/apply',array('id'=>$model->id,'apply'=>'unpass'));?>'">
        <?php endif;?>
        <?php if($model->status == PartnerJoinAuditing::STATUS_UNPASS):?>
        	 <?php if(isset($rs['0'])&&$status>0){?>
            <a id="Btn_Add"  class="regm-sub" href = '<?php echo $this->createAbsoluteUrl('/manage/partnerJoinAuditing/apply',array('id'=>$model->id,'apply'=>'pass','gw'=>''));?>'>审核通过</a>
            <?php }else{?>
            <a id="Btn_Add"  class="regm-sub" href = '<?php echo $this->createAbsoluteUrl('/manage/partnerJoinAuditing/apply',array('id'=>$model->id,'apply'=>'pass'));?>'>审核通过</a>
            <?php } ?>		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <?php endif;?>
         <?php if($model->status == PartnerJoinAuditing::STATUS_ENABLE):?>
        	已审核通过,不能有其余操作
        <?php endif;?>
        <?php endif;?>
        </td>
	</tr>
	
</table>
<script>
     window.onLoad();
function getGw(o){
    var id = o.name;
    var gw = $("#"+id).find("option:selected").text(); //选中的值
    var or = document.getElementById('Btn_Add');
//var href = or.href;
var s =" <?php echo $this->createAbsoluteUrl('/manage/partnerJoinAuditing/apply',array('id'=>$model->id,'apply'=>'pass'));?>";

    or.href = s+"&gw="+gw;
    }
    
    function onLoad(){
        var val = $("#mobile").find("option:selected").val(); //选中的值
        if(val && val>0){
            var obj = document.getElementById('mobile');
           getGw(obj);
        }
    }
</script>