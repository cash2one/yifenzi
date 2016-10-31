<?php
/* @var $this GoodsController */
/* @var $model Goods */
/** @var  $form CActiveForm */

$this->breadcrumbs = array(
    '商品' => array('admin'),
    '编辑'
);

$form = $this->beginWidget('CActiveForm', array(
    'id' => 'goods-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'afterValidate'=>'js:function(form, data, hasError){
            if(hasError==false){
                $(".showMore:hidden").find("td").html("");
                return true;
            }
            return false;
        }',
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-come">
    <tbody>
        <tr>
            <th colspan="2" class="title-th odd">基本信息</th>
        </tr>
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'name'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'name', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'name'); ?>
            </td>
        </tr>
        
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'sec_title'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'sec_title', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'sec_title'); ?>
            </td>
        </tr>
        
        <tr>
            <th align="right"><?php echo $form->labelEx($model, 'source_cate_id'); ?>：</th>
            <td>
            <?=  $cate?$cate->name:'未知分类' ?>
            </td>
        </tr>
        
        <tr>
            <th align="right"><?php echo $form->labelEx($model, 'cate_id'); ?>：</th>
            <td>
             
            <?php
            echo $form->dropDownList($model, 'cate_id', CHtml::listData(GoodsCategory::model()->findAll('member_id=:mid',array(':mid'=>$model->member_id)), 'id', 'name'), array('class' => 'text-input-bj', 'empty' => Yii::t('goods', '请选择'))
            );
            ?>
            <?php echo $form->error($model, 'cate_id'); ?>
       
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'is_one'); ?></th>
            <td>
                <?php echo $form->radioButtonList($model, 'is_one', $model::gender(), array('separator' => '&nbsp')) ?>
                <?php echo $form->error($model, 'is_one') ?>
            </td>
        </tr>
        <tr>
            <th><?php echo $form->labelEx($model, 'is_promo'); ?></th>
            <td>
                <?php echo $form->radioButtonList($model, 'is_promo', $model::getIsProme(), array('separator' => '&nbsp')) ?>
                <?php echo $form->error($model, 'is_promo') ?>
            </td>
        </tr>
        
        <tr>
            <th><?php echo $form->labelEx($model, 'is_for'); ?></th>
            <td>
                <?php echo $form->radioButtonList($model, 'is_for', $model::getIsFor(), array('separator' => '&nbsp')) ?>
                <?php echo $form->error($model, 'is_for') ?>
            </td>
        </tr>
        
      <tr>
        <th width="120px" align="right"><?php echo $form->labelEx($model, 'content'); ?>：</th>
        <td class="odd">
         
            <?php $this->widget('manage.extensions.editor.WDueditor', array(
            		'model' => $model,
            		 'attribute' => 'content',
            		
            )); ?>
            <?php echo $form->error($model, 'content'); ?><br/>
        </td>
    </tr>	
        
    <tr class="showMore">
            <th align="right"><?php echo $form->labelEx($model, 'thumb'); ?>：<span class="required"></span></th>
            <td>
                <?php
                $this->widget('widgets.CUploadPic', array(
                    'attribute' => 'thumb',
                    'model'=>$model,
                    'form'=>$form,
                    'num' => 1,
                    'btn_value'=> Yii::t('sellerGoods', '上传图片'),
                    'folder_name' => stristr($model->thumb,'/',true),
                ));
                ?>
              <span id="file_label"></span>
                <?php echo $form->error($model, 'thumb'); ?>
                <?php
                    echo CHtml::hiddenField('oldThumb', $model->thumb);
                    echo "<br>";
                ?>
            </td>
        </tr> 
        
        
        <tr>
            <th><?php echo $form->labelEx($imgModel, 'path'); ?></th>
            <td>
                <?php
                $this->widget('application.widgets.CUploadPic', array(
                    'attribute' => 'path',
                    'model' => $imgModel,
                    'form' => $form,
                    'num' => 6,
                    'btn_value' => Yii::t('sellerGoods', '上传图片'),
                    'render' => '_upload',
                    'folder_name' => 'files',
					'img_area'=>1,
                    'include_artDialog' => false,
                ));
                ?>
                <?php echo $form->error($imgModel, 'path') ?>
                &nbsp;<span class="gray">(<?php echo Yii::t('sellerGoods', '请根据app图片规格上传,建议上传3张'); ?>)</span>
            </td>
        </tr>
    
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'supply_price'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'supply_price', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'supply_price'); ?>
            </td>
        </tr> 
        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'price'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'price', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'price'); ?>
            </td>
        </tr>

        <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'status'); ?>：</th>
            <td>
                <?php echo $form->dropDownList($model,'status',Goods::getStatus()); ?>
                <?php echo $form->error($model, 'status'); ?>
            </td>
        </tr> 
        
          <tr>
            <th style="width: 120px" align="right"><?php echo $form->labelEx($model, 'barcode'); ?>：</th>
            <td>
                <?php echo $form->textField($model, 'barcode', array('class' => "text-input-bj  long valid")); ?>
                <?php echo $form->error($model, 'barcode'); ?>
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

<script type="text/javascript">
setInterval(function(){$("textarea[name='Goods[content]']").html($(document.getElementById('ueditor_0').contentWindow.document.body).html());},1000);
                

                </script>