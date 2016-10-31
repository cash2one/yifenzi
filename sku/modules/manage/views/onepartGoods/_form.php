<?php
$form = $this->beginWidget('CActiveForm', array(
            "id" => 'onepartGOods-form',
            "enableAjaxValidation" => false,
            "enableClientValidation" => true,
            "clientOptions" => array(
                'validateOnSubmit' => true,
                'beforeValidate' => 'js:function(form){
                        var sval = $("#YfzGoods_single_price").val();
                        var price = $("#YfzGoods_shop_price").val();
                        var yushu = (price % sval);
                        if(yushu != 0){
                            $("#YfzGoods_single_price_em_").text("总价不能整除单价，请重新输入价格").show();
                            return false;
                        } else {
                            $("#YfzGoods_single_price_em_").hide();
                        }
                        return true;
                    }'
            ),
            "htmlOptions" => array("enctype" => 'multipart/form-data'),
        ));
$list = CHtml::listData(Column::model()->findAll(array("condition" => "is_show=1")), "id", "column_name");
//     print_r($list);exit();
?>
<!-- 
.least{ width:100px;}
.middle{ width:200px;}
.long{ width:300px;}
.longest{ width:600px;}
-->
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="tab-come">
    <tr>
	    <?php if($this->action->id == 'insert'):?>
        <td colspan="2" class="title-th even" align="center">商品添加页面</td>
		<?php endif ?>
		
		<?php if($this->action->id == 'update'):?>
        <td colspan="2" class="title-th even" align="center">商品编辑页面</td>
		<?php endif ?>
		
    </tr>
    <tr>
        <th width="10%" class="odd"><?php echo $form->labelEx($model, 'column_id'); ?></th>
        <td>
            <?php
                $column = Column::model()->findAll(array("condition" => "is_show=1"));
                $column = json_decode(CJSON::encode($column),true);
                echo $form->dropDownList($model,'column_id',CHtml::listData($column, 'id', 'column_name'));
                echo $form->error($model,'column_id');
            ?>
        </td>
    </tr>
    <tr>
        <th width="10%" class="odd"><?php echo $form->labelEx($model, 'brand_id'); ?></th>
        <td>
            <?php
            $brand =  Brand::model()->findAll(array(
                "select"    =>  array('brand_id','brand_name'),
                "condition" =>  "is_show=1",
            ));
                $brand = json_decode(CJSON::encode($brand),true);
                echo $form->dropDownList($model,'brand_id',CHtml::listData($brand, 'brand_id', 'brand_name'));
                echo $form->error($model,'brand_id');
            ?>
        </td>
    </tr>
    <tr>
        <th width="10%" class="even"><?php echo $form->labelEx($model, 'goods_name'); ?></th>
        <td class="odd">
            <?php echo $form->textField($model, 'goods_name', array('class' => 'text-input-bj longest')); ?>
            <span>最长80个字符</span>
            <?php echo $form->error($model, 'goods_name',array('style'=>'display:inline-block')); ?>
        </td>
    </tr>

    <tr>
        <th width="10%" class="odd"><?php echo $form->labelEx($model, 'after_name'); ?></th>
        <td>
            <?php echo $form->textField($model, 'after_name', array("class" => 'text-input-bj longest')); ?>
            <span>最长50个字符</span>
            <?php echo $form->error($model, 'after_name'); ?>
        </td>
    </tr>

    <!-- 关键字 -->
    <tr>
        <th  width="10%" class="even"><?php echo $form->labelEx($model, 'keywords'); ?></th>
        <td>
            <?php echo $form->textField($model, 'keywords', array("class" => 'text-input-bj longest')); ?>
            <span>多个关键字请用；分开</span>
            <?php echo $form->error($model, 'keywords'); ?>
        </td>
    </tr>
    <!-- 商品总价格 -->
    <tr>
        <th width="10%" class="odd"> <?php echo $form->labelEx($model, 'shop_price'); ?></th>
		<?php if($this->action->id == 'update'):?>
        <td>
            <?php echo $form->textField($model, 'shop_price', array('class' => 'text-input-bj least','disabled'=>'disabled')); ?>
            <?php echo $form->error($model, 'shop_price'); ?>
        </td>
		<?php else: ?>
		<td>
            <?php echo $form->textField($model, 'shop_price', array('class' => 'text-input-bj least')); ?>
            <?php echo $form->error($model, 'shop_price'); ?>
        </td>
		<?php endif;?>
    </tr>

    <!-- 每人次单价 -->
    <tr>
        <th width="10%" class="even"> <?php echo $form->labelEx($model, 'single_price'); ?></th>
		<?php if($this->action->id == 'update'):?>
        <td>
            <?php echo $form->textField($model, 'single_price', array('class' => 'text-input-bj least','disabled'=>'disabled')); ?>
            <?php echo $form->error($model, 'single_price'); ?>
        </td>
		<?php else: ?>
		<td>
            <?php echo $form->textField($model, 'single_price', array('class' => 'text-input-bj least','')); ?>
            <?php echo $form->error($model, 'single_price'); ?>
        </td>
		<?php endif;?>
    </tr>
    <!--限购数-->
    <tr>
        <th width="10%" class="odd"> <?php echo $form->labelEx($model, 'limit_number'); ?></th>
        <td>
            <?php echo $form->textField($model, 'limit_number', array('class' => 'text-input-bj least')); ?>
            <?php echo $form->error($model, 'limit_number'); ?>
        </td>
    </tr>
    <!-- 最大期数 -->
    <tr>
        <th width="10%" class="even"> <?php echo $form->labelEx($model, 'max_nper'); ?></th>
        <td>
            <?php echo $form->textField($model, 'max_nper', array('class' => 'text-input-bj least','disabled'=>'disabled',"value"=>60000)); ?>
            <?php echo $form->error($model, 'max_nper'); ?>
        </td>
    </tr>
    <!--缩略图-->
    <tr>
        <th width="10%" class="odd"> <?php echo $form->labelEx($imgModel, 'goods_thumb'); ?></th>
        <td>
            <?php
                $this->widget('application.widgets.CUploadPic', array(
                    'attribute' => 'goods_thumb',
                    'model' => $imgModel,
                    'form' => $form,
                    'upload_height' =>200,
                    'upload_width'  =>200,
                    'num' => 1,
                    'btn_value' => Yii::t('sellerGoods', '上传图片'),
                    'folder_name' => 'files',
                    'include_artDialog' => false,
                ));
                ?>
            <?php echo $form->error($imgModel, 'goods_thumb',array('style'=>'bottom:26px;left:93px')) ?>
            &nbsp;<div class="gray">(<?php echo Yii::t('YifenGoods', '最多上传1张(宽200*高200),请先删除后上传'); ?>)</div>
        </td>
    </tr>
    <!--图片列表-->
    <tr>
        <th width="10%" class="even"> <?php echo $form->labelEx($imgModel,'show_image1') ?></th>
        <td>
            <?php
                $this->widget('application.widgets.CUploadPic', array(
                    'attribute' => 'show_image1',
					'attribute2' => 'show_image2',
					'attribute3' => 'show_image3',
                    'model' => $imgModel,
//                    'upload_height' =>430,
//                    'upload_width'  =>430,
                    'form' => $form,
                    'num' => 3,
                    'btn_value' => Yii::t('sellerGoods', '上传图片'),
                    'render' => '_uploads',
                    'folder_name' => 'files',
                    'include_artDialog' => false,
                ));
                ?>

            <?php echo $form->error($imgModel, 'show_image1') ?>
            &nbsp;<div class="gray">(<?php echo Yii::t('YifenGoods', '最多上传3张'); ?>)</div>
        </td>
    </tr>
    <!--商品描述-->
    <tr>
        <th width="10%" class="odd"> <?php echo $form->labelEx($model, 'goods_desc'); ?></th>
        <td style="z-index: 0">
            <?php
                $this->widget('manage.extensions.editor.WDueditor', array(
                    'model' => $model,
                    'base_url' => '',
                    'attribute' => 'goods_desc',
                ));
                echo $form->error($model,'goods_desc',array('style'=>'bottom:0;z-index:10000'));
            ?>
        </td>
    </tr>
    <tr>
        <th width="10%" class="even"> <?php echo '商品属性'; ?></th>
        <td>
            <?php echo $form->checkBox($model,'recommended')?> 人气推荐
        </td>
    </tr>
    <tr>
        <th width="10%" class="add"> <?php echo $form->labelEx($model,'announced_time'); ?></th>
        <td>
            <?php
                $time = YfzGoods::getTime($model->announced_time);
            ?>
            <select name="day">
                <?php for($i=0;$i<=3;$i++){?>
                <option value="<?php echo $i?>" <?php if($time['day'] == $i) echo 'selected'?>><?php echo $i?></option>
                <?php } ?>
            </select> 天
            <select name="hour">
                <?php for($i=0;$i<=23;$i++){?>
                <option value="<?php echo $i?>" <?php if($time['hour'] == $i) echo 'selected'?>><?php echo $i?></option>
                <?php } ?>
            </select> 时
            <select name="minute">
                <?php for($i=0;$i<=59;$i++){?>
                <option value="<?php echo $i?>" <?php if($time['minute'] == $i) echo 'selected'?>><?php echo $i?></option>
                <?php } ?>
            </select> 分
        </td>
    </tr>
    <tr>
        <th width="10%" class="even"> <?php echo $form->labelEx($model,'sales_time'); ?></th>
        <td>
        <?php $this->widget('manage.extensions.timepicker.timepicker', array('model' => $model, 'name' => 'sales_time', 'options' => array('value' => date('Y-m-d H:i:s')))); ?>
        </td>
    </tr>
    <tr>
        <th></th>
        <td>
            <?php if($this->action->id == 'update'):?>
            <?php echo CHtml::submitButton(Yii::t('goods', '修改'), array('class' => 'reg-sub')); ?>
            <?php else: ?>
            <?php echo CHtml::submitButton(Yii::t('goods', '添加'), array('class' => 'reg-sub')); ?>
            <?php endif;?>
        </td>
    </tr>
</table>

<?php $this->endWidget(); ?>