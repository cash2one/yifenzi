<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta content="telephone=no" name="format-detection" />
<meta name="Description" content=""/>
<meta name="Keywords" content=""/>

      <link type="text/css" rel="stylesheet" href="<?php echo DOMAIN ?>/skuwenquan/styles/global.css"/>
    <link type="text/css" rel="stylesheet" href="<?php echo DOMAIN ?>/skuwenquan/styles/mobile-select-area.css"/>

</head>
<body id="main">
<?php 
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'PartnerJoinAuditing-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
       
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));
?>
<header class="header">
	<a href="about.html" class="btnBack"></a>
    加盟申请
</header><!-- 头部 -->
<section>	
	<div class="joinIn">
		<div class="joinTop">
        	<p>请如实填写商户信息，上传照片大小控制在3M以内，我们将会对您的信息进行审核，审核完成会由专人与您联系后续事宜，请耐心等待。</p>
        	<p class="red">* 上传的营业执照、开户许可证必须加盖公章；</p>
            <p class="red">* 个人加盟无需提交营业执照资料，其他用户需提交所有验证材料</p>
        </div>
        <div class="jionItem">
        	<div class="itemTit">商户推荐者</div>
        	<div class="itemLi">
            	<div class="fl">商户推荐者</div>
                <div class="fr">
                <?php echo $form->textField($model, 'referrals_gai_number', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入推荐者GW号")); ?>
<!--                 <input name="" type="text" placeholder="请输入推荐者GW号" id="RecommendGW"> -->
                 <p><?php echo $form->error($model, 'referrals_gai_number',array('class'=>'error')); ?></p>
                </div>
            </div>
            <p class="red">※  商户推荐者每笔交易均可获得折扣差提成奖励</p>
            
            <div class="itemTit">店铺资料</div>
        	<div class="itemLi">
            	<div class="fl">店铺名称 <span class="error">*</span></div>
            	<div class="fr">
            	<?php  echo $form->textField($model, 'store_name', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入店铺名称")); ?>
                 <p><?php echo $form->error($model, 'store_name',array('class'=>'error')); ?></p>
<!-- <!--<input name="" type="text" placeholder="请输入推荐者GW号" id="Sh_RecommendGW"> --> 
            	</div>
            </div>
            <div class="itemLi">
            	<div class="fl">所在地区</div>
            	<div class="fr">
            <?php 
                echo $form->dropDownList($model, 'store_province_id', Region::getRegionByParentId(Region::PROVINCE_PARENT_ID), array(
                    'prompt' => Yii::t('partners', Yii::t('address', '选择省份')),
                    'class' => 'text-input-bj',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createAbsoluteUrl('/api/html/updateCity'),
                        'dataType' => 'json',
                        'data' => array(
                            'province_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                        'success' => 'function(data) {
                            $("#PartnerJoinAuditing_store_city_id").html(data.dropDownCities);
                            $("#PartnerJoinAuditing_store_district_id").html(data.dropDownCounties);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'store_city_id', Region::getRegionByParentId($model->store_province_id), array(
                    'prompt' => Yii::t('partners', Yii::t('address', '选择城市')),
                    'class' => 'text-input-bj',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createAbsoluteUrl('/api/html/updateArea'),
                        'update' => '#Partners_district_id',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),  
                        'success' => 'function(data) {                          
                            $("#PartnerJoinAuditing_store_district_id").html(data);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'store_district_id', Region::getRegionByParentId($model->store_city_id), array(
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
                <p><?php echo $form->error($model, 'store_province_id',array('class'=>'error')); ?></p>
                 <p><?php echo $form->error($model, 'store_city_id',array('class'=>'error')); ?></p>
                  <p><?php echo $form->error($model, 'store_district_id',array('class'=>'error')); ?></p>
            	</div>
            	
            	
            	
            	
            	
<!--                 <div class="fr"><input name="" type="text" class="input-click" id="txt_area_01" placeholder="广东 广州 越秀区"></div> -->
            </div>
            <div class="itemLi">
            	<div class="fl">联系地址 <span class="error">*</span></div>
                <div class="fr">
                <?php echo $form->textField($model, 'store_address', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入联系地址")); ?>
<!--                 <input name="" type="text" placeholder="请输入联系地址" id="Sh_Address"> -->
                 <p><?php echo $form->error($model, 'store_address',array('class'=>'error')); ?></p>
                </div>
            </div>
            <div class="itemLi">
            	<div class="fl">店铺联系电话 <span class="error">*</span></div>
                <div class="fr">
                <?php echo $form->textField($model, 'store_mobile', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入联系电话")); ?>
<!--                 <input name="" type="text" placeholder="请输入联系电话" id="Sh_Phone"> -->
                <p><?php echo $form->error($model, 'store_mobile',array('class'=>'error')); ?></p>
                </div>
            </div>
            <div class="itemAdd">
            	<div class="itemTop">个体工商户执照/企业法人营业执照</div>
                <div class="itemBot">
                	<p>
                	<?php echo  $form->fileField($model, 'license_img');//CHtml::activeFileField($model, 'license_img');?>
<!--                 	<input name="imgfile" type="file" id="license_img" size="40" />  -->
                	</p>
                	<p><?php echo $form->error($model, 'license_img',array('class'=>'error'),false); ?></p>
                	<p class = "error">请上传清晰的个体工商执照照片</p>
                </div>
            </div>
            <div class="itemLi">
            	<div class="fl">执照到期限期 <span class="error">*</span></div>
            	<div class="fr">
            	<?php $model->license_to_time = $model->license_to_time == 0 ? "" : date('Y-m-d',(int)$model->license_to_time)?>
            	 <?php //echo $form->textField($model, 'license_to_time', array('class' => "text-input-bj  long valid",'placeholder'=>"2016-08-08(请输入所示日期格式)")); 
                $this->widget('comext.timepicker.timepicker', array(
                		'model' => $model,
                		'name' => 'license_to_time',
                		'select'=>'date',
                ));
                ?>
<!--                 <input name="" type="text" placeholder="2016-08-08" id="Sh_Time"/> -->
            	 <p><?php echo $form->error($model, 'license_to_time',array('class'=>'error')); ?></p>
                </div>
            </div>
            
            <div class="itemAdd">
            	<div class="itemTop">商户头像</div>
                <div class="itemBot">
                	<p>
                	<?php echo  $form->fileField($model, 'head');//CHtml::activeFileField($model, 'license_img');?>
<!--                 	<input name="imgfile" type="file" id="license_img" size="40" />  -->
                	</p>
                	<p><?php echo $form->error($model, 'head',array('class'=>'error'),false); ?></p>
                	<p class = "error">请上传清晰的商户头像照片</p>
                </div>
            </div>
            
            <div class="itemTit">银行资料</div>
			<div class="itemLi">
            	<div class="fl">开户银行 <span class="error">*</span></div>
            	<div class="fr">
            	<?php 
            	echo $form->dropDownList($model, 'bank', BankAccount::getBankList());
            	?>
            	</div>
<!--                 <div class="fr"><input name="" type="text" class="input-click" id="txt_bank" placeholder="请选择银行"></div> -->
            </div>
            <div class="itemLi">
            	<div class="fl">银行卡号 <span class="error">*</span></div>
            	 
                <div class="fr">
                <?php $model->bank_account = $model->bank_account == '0'? "" : $model->bank_account;?>
                <?php echo $form->textField($model, 'bank_account', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入银行卡号")); ?>
<!--                 <input name="" type="text" placeholder="请输入联系地址" id="bank_account"> -->
               <p><?php echo $form->error($model, 'bank_account',array('class'=>'error')); ?></p>
                
                </div>
            </div>
            
            <div class="itemLi">
            	<div class="fl">银行卡账户名 <span class="error">*</span></div>
            	 
                <div class="fr">
                <?php echo $form->textField($model, 'bank_account_name', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入银行卡账户名")); ?>
<!--                 <input name="" type="text" placeholder="请输入联系地址" id="bank_account"> -->
               <p><?php echo $form->error($model, 'bank_account_name',array('class'=>'error')); ?></p>
                
                </div>
            </div>
            <div class="itemLi">
            	<div class="fl">银行所属地 <span class="error">*</span></div>
            	<div class="fr">
            	<?php 
                echo $form->dropDownList($model, 'bank_province_id', Region::getRegionByParentId(Region::PROVINCE_PARENT_ID), array(
                    'prompt' => Yii::t('partners', Yii::t('address', '选择省份')),
                    'class' => 'text-input-bj',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createAbsoluteUrl('/api/html/updateCity'),
                        'dataType' => 'json',
                        'data' => array(
                            'province_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),
                        'success' => 'function(data) {
                            $("#PartnerJoinAuditing_bank_city_id").html(data.dropDownCities);
                            $("#PartnerJoinAuditing_bank_district_id").html(data.dropDownCounties);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'bank_city_id', Region::getRegionByParentId($model->bank_province_id), array(
                    'prompt' => Yii::t('partners', Yii::t('address', '选择城市')),
                    'class' => 'text-input-bj',
                    'ajax' => array(
                        'type' => 'POST',
                        'url' => $this->createAbsoluteUrl('/api/html/updateArea'),
                        'update' => '#Partners_district_id',
                        'data' => array(
                            'city_id' => 'js:this.value',
                            'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                        ),  
                        'success' => 'function(data) {                          
                            $("#PartnerJoinAuditing_bank_district_id").html(data);
                        }',
                )));
                ?>
                <?php
                echo $form->dropDownList($model, 'bank_district_id', Region::getRegionByParentId($model->bank_city_id), array(
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
            	 <p><?php echo $form->error($model, 'bank_province_id',array('class'=>'error')); ?></p>
            	 <p><?php echo $form->error($model, 'bank_city_id',array('class'=>'error')); ?></p>
            	 <p><?php echo $form->error($model, 'bank_district_id',array('class'=>'error')); ?></p>
            	</div>
            	
            	
            	
            	
<!--                 <div class="fr"><input name="" type="text" class="input-click" id="txt_area_02" placeholder="广东 广州 越秀区"></div> -->
            </div>
            <div class="itemLi">
            	<div class="fl">开户行全称<span class="error">*</span></div>
                <div class="fr">
                 <?php echo $form->textField($model, 'bank_branch', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入开户支行名称")); ?>
<!--                 <input name="" type="text" placeholder="请输入开户支行名称" id="bank_branch"> -->
                 <p><?php echo $form->error($model, 'bank_branch',array('class'=>'error')); ?></p>
                </div>
            </div>
            <div class="itemAdd">
            	<div class="itemTop">银行卡/开户许可证图片</div>
                <div class="itemBot">
                	<p>
                	<?php echo  $form->fileField($model, 'bank_img');//CHtml::activeFileField($model, 'bank_img');?>
<!--                 	<input name="imgfile" type="file" id="bank_img" size="40" />    -->
                	</p>
                	<p><?php echo $form->error($model, 'bank_img',array('class'=>'error'),false); ?></p>
                	<p class = "error">请上传清晰的银行卡/开户许可证图片</p>
                </div>
            </div>
            
            <div class="itemTit">身份资料</div>
			<div class="itemLi">
            	<div class="fl">姓名 <span class="error">*</span></div>
                <div class="fr">
                 <?php echo $form->textField($model, 'id_name', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入您的姓名")); ?>
<!--                 <input name="" type="text" placeholder="请输入您的姓名" id="id_name"> -->
                 <p><?php echo $form->error($model, 'id_name',array('class'=>'error')); ?></p>
                </div>
            </div>
            <div class="itemLi">
            	<div class="fl">身份证号 <span class="error">*</span></div>
                <div class="fr">
                <?php echo $form->textField($model, 'id_card', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入您的身份证号")); ?>
<!--                 <input name="" type="text" placeholder="请输入您的身份证号" id="id_card"> -->
                <p><?php echo $form->error($model, 'id_card',array('class'=>'error')); ?></p>
                </div>
            </div>
            <div class="itemLi">
            	<div class="fl">身份证有效期 <span class="error">*</span></div>
                <div class="fr">
                <?php $model->id_card_to_time = $model->id_card_to_time == 0 ? "" : date('Y-m-d',(int)$model->id_card_to_time)?>
                 <?php 
                           $this->widget('comext.timepicker.timepicker', array(
          		'model' => $model,
          		'name' => 'id_card_to_time',
          		'select'=>'date',
          ));
                //echo $form->textField($model, 'id_card_to_time', array('class' => "text-input-bj  long valid",'placeholder'=>"2016-08-08(请输入所示日期格式)")); ?>
<!--                 <input name="" type="text" placeholder="2016-08-08" id="id_card_to_time"/> -->
                  <p><?php echo $form->error($model, 'id_card_to_time',array('class'=>'error')); ?></p>
                </div>
            </div>
            <div class="itemAdd itemTb">
            	<div class="itemTop">身份证正/反面照</div>
                <div class="itemBot fl">
                	<p>
                	<?php echo $form->fileField($model, 'id_card_font_img');//CHtml::activeFileField($model, 'id_card_font_img');?>
<!--                 	<input name="imgfile" type="file" id="id_card_font_img" size="40" /> -->
                	</p>
                	 <p><?php echo $form->error($model, 'id_card_font_img',array('class'=>'error'),false); ?></p>
                	<p class = "error">请上传身份证正面照</p>
                </div>
                <div class="itemBot fr">
                	<p>
                	<?php echo $form->fileField($model, 'id_card_back_img');//CHtml::activeFileField($model, 'id_card_back_img');?>
<!--                 	<input name="imgfile" type="file" id="id_card_back_img" size="40" />    -->
                	</p>
                	 <p><?php echo $form->error($model, 'id_card_back_img',array('class'=>'error'),false); ?></p>
                	<p class = "error">请上传身份证反面照</p>
                </div>
            </div>
            
            <div class="itemTit">联系方式</div>
			<div class="itemLi">
            	<div class="fl">申请人姓名 <span class="error">*</span></div>
                <div class="fr">
                 <?php echo $form->textField($model, 'name', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入申请人姓名")); ?>
<!--                 <input name="" type="text" placeholder="请输入申请人姓名" id="name"> -->
                 <p><?php echo $form->error($model, 'name',array('class'=>'error')); ?></p>
                </div>
            </div>
            <div class="itemLi">
            	<div class="fl">联系电话 <span class="error">*</span></div>
                <div class="fr">
                <?php echo $form->textField($model, 'mobile', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入申请人联系电话")); ?>
<!--                 <input name="" type="text" placeholder="请输入申请人联系电话" id="mobile"> -->
                 <p><?php echo $form->error($model, 'mobile',array('class'=>'error')); ?></p>
                </div>
            </div>
            <div class="itemLi">
            	<div class="fl">运营方GW号</div>
                <div class="fr">
                 <?php echo $form->textField($model, 'gai_number', array('class' => "text-input-bj  long valid",'placeholder'=>"请输入商户运营方GW号")); ?>
<!--                 <input name="" type="text" placeholder="请输入商户运营方GW号" id="gai_number"> -->
                  <p><?php echo $form->error($model, 'gai_number',array('class'=>'error')); ?></p>
                </div>
            </div>
            <div class="itemLast agreement">
          <input class="fl" type="checkbox" id="checkboxFourInput" name="" style="width: 18px;height:18px;"/>
                <div class="fr">我同意<a href="<?php echo $this->createAbsoluteUrl('/api/html/partnerjoinauditingabout');?>" target=_blank>《SKU平台商户协议》</a>，成为平台注册商户。 并保证经营主体符合<a href="<?php echo $this->createAbsoluteUrl('/api/html/partnerjoinauditingzhuti');?> " target=_blank>《SKU商户主体资质要求》</a>、<a href="<?php echo $this->createAbsoluteUrl('/api/html/partnerjoinauditingleimu');?>" target=_blank>《SKU商品类目资质要求》</a>，服从<a href="<?php echo $this->createAbsoluteUrl('/api/html/partnerjoinauditingjinshou');?>" target=_blank>《禁售商品管理规范》</a>。</div>
            </div>
        </div>
        <div class="btnBox">
        	
        	<label class="kkk"></label>
        	<?php echo CHtml::submitButton(Yii::t('PartnerJoinAuditing', '提交资料'), array('id'=>'btn_submit','class' => 'btn-submit','disabled'=>true)); ?>
<!--         	<input name="" id="btn_submit" class="btn-submit" type="button" value="提交资料"> -->
        </div>
	</div>
	<?php $this->endwidget();?>

 <script type="text/javascript">
 checkboxFourInput = $('#checkboxFourInput');
 checkboxFourInput.on('click',function(){
	 btn_submit = $('#btn_submit');
	 if(checkboxFourInput.is(':checked')){
		 btn_submit.attr("disabled", false);
		 btn_submit.removeClass('btn-submit');
		 btn_submit.addClass('btn-submit2');
	   }else{
		   btn_submit.attr("disabled", true);
		   btn_submit.removeClass('btn-submit2');
		   btn_submit.addClass('btn-submit');
		}
	 });
 </script>

    
</section>
</body>




