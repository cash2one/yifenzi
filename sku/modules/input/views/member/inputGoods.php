<?php
$this->breadcrumbs = array(
    Yii::t('store', '商品录入'),
);
?>

<div class="web-content fl">
    <div class="sidebar-l fl">
        <div class="search-bg">
            <div class="search-main">

                <input name="" class="input-search" type="text" value="输入商品名或者条码进行模糊搜索" style = "color:#999" id="searchName" onblur="if(this.value=='')this.value='输入商品名或者条码进行模糊搜索'; this.style.color='#999'">
                <input name="" class="button-search" type="button" onclick="search()">

            </div>
            <div class="search-list">
                <ul>

                </ul>
            </div>
        </div>
        <div class="web-goods-list" id ="contents">
            <p>
                <span style="float:left">商品条形码</span>
                <span id="reflash" onclick="reflash()">换一批</span>
            </p>
            <ul id="showData">
                <?php if (isset($arr) && !empty($arr)): ?>
                    <?php foreach ($arr as $v): ?>
                        <li onclick="getOne(this)" id="<?php echo $v['id'] ?>"><a style="cursor:pointer"><?php echo $v['barcode'] ?></a></li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="sidebar-r fl">
        <div class="goods-data">
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'action' => Yii::app()->createUrl($this->route),
                'method' => 'post',
                'id'=>'ApplyBarcodeGoods',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'htmlOptions' => array('enctype' => 'multipart/form-data'),
                'clientOptions' => array(
                    'validateOnSubmit' => true,
                ),
            ));
            ?> 
            <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken; ?>" />  
            <div style=" font-size: 14px;display: none" id="timer">
                <span>录入倒计时</span>
                <span id="t_m" style="color: #FF0000"></span>:
                <span id="t_s" style="color: #FF0000"></span>
            </div>
            <p><span><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_BARCODE)?>：</span>
                  <?php if (ApplyBarcodeGoods::IsInput(EnGoodsRule::RULE_BARCODE)) { ?>
                    <?php echo $form->textField($model, 'barcode', array('class' => 'input-data')); ?>
                <?php } else { ?>
                    <?php echo $form->textField($model, 'barcode', array('class' => 'read', 'disabled' => true)); ?>
                <?php } ?>
                <input name="" onclick="getCode()"class="button-data" value="获取条码商品" type="button">
                <input name="" class="button-data" value="清除内容" type="button" onclick="clearData()">

            </p>
            <p><span><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_NAME)?>：</span>

                <?php if (ApplyBarcodeGoods::IsInput(EnGoodsRule::RULE_NAME)) { ?>
                    <?php echo $form->textField($model, 'name', array('class' => 'input-data')); ?>
                <?php } else { ?>
                    <?php echo $form->textField($model, 'name', array('class' => 'read', 'disabled' => true)); ?>
                <?php } ?>
            <p><span><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_MODEL)?>：</span>
                <?php if (ApplyBarcodeGoods::IsInput(EnGoodsRule::RULE_MODEL)) { ?>
                    <?php echo $form->textField($model, 'model', array('class' => 'input-data')); ?>
                <?php } else { ?>
                    <?php echo $form->textField($model, 'model', array('class' => 'input-data', 'disabled' => true)); ?>
                <?php } ?>
            </p>
            <p><span><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_UNIT)?>：</span>
                <?php if (ApplyBarcodeGoods::IsInput(EnGoodsRule::RULE_UNIT)) { ?>
                    <?php echo $form->textField($model, 'unit', array('class' => 'input-data')); ?>
                <?php } else { ?>
                    <?php echo $form->textField($model, 'unit', array('class' => 'input-data', 'disabled' => true)); ?>
                <?php } ?>
            </p>
            <p><span><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_PRICE)?>：</span>
                <?php if (ApplyBarcodeGoods::IsInput(EnGoodsRule::RULE_PRICE)) { ?>
                    <?php echo $form->textField($model, 'default_price', array('class' => 'input-data')); ?>
                <?php } else { ?>
                    <?php echo $form->textField($model, 'default_price', array('class' => 'input-data', 'disabled' => true)); ?>
                <?php } ?>
            </p>
            <p><span><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_DESCRIBE)?>：</span>
                <?php if (ApplyBarcodeGoods::IsInput(EnGoodsRule::RULE_DESCRIBE)) { ?>
                    <?php echo $form->textArea($model, 'describe', array('class' => 'textarea-data')); ?>
                <?php } else { ?>
                    <?php echo $form->textArea($model, 'describe', array('class' => 'textarea-data', 'disabled' => true)); ?>
                <?php } ?>
            </p>
            <p><span><?php echo EnGoodsRule::getName(EnGoodsRule::RULE_THUMB)?>：</span>
                <?php if (ApplyBarcodeGoods::IsInput(EnGoodsRule::RULE_THUMB)) { ?>
                    <?php echo $form->fileField($model,'thumb');?>              
                    <?php echo $form->error($model, 'thumb', array('style' => 'position: relative; display: inline-block'), false, false) ?>
                <?php } else { ?>             
                <?php } ?>
            </p>
            <p class="goods-message"></p>
            <p class="goods-img" id="showimg" onclick="picBig(this)"><img id="showImg" src="<?php echo DOMAIN ?>/images/upday.jpg"></p>
            </p>
                        <div class="btn"></div><div class="files"></div> 
            <?php echo $form->hiddenField($model, 'id'); ?> 
            <p class="goods-submit">
                <input name="" class="button-data" value="提交" type="submit">
                <input name="" class="button-data" value="取消" type="button">
            </p>
 


            <?php $this->endWidget(); ?>
        </div>
    </div>
 <div id="divCenter" align="center" style="position: absolute;display: none;  width:100%;padding-top: 400px "onclick="picClose(this);">
                  
            <?php echo CHtml::image(DOMAIN . "/" . "images/upday.jpg", '点击显示大图', array("style" => "cursor: pointer;border: white  solid 10px;","id"=>"big")) ?>

        </div>
</div>

<?php echo $this->renderPartial('_searchDataJs'); ?>
<script>
    function picBig(o) {
        var id = o.id;
        var v = document.getElementById('divCenter');
        v.style.display = "block";
    }

    function picClose(o) {
        var id = o.id;
        var v = document.getElementById(id);
        v.style.display = "none";
    }
    
    $("#searchName").click(function(){
        this.value ='';
        this.style.color='#000';
    });
    </script>