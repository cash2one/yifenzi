<script>
    //js 多语言显示
    var L = {
        imgManage:"<?php echo Yii::t('franchisee','图片管理') ?>",
        iframeNoLoad:"<?php echo Yii::t('franchisee','iframe还没有加载完毕') ?>",
        onlySelect:"<?php echo Yii::t('franchisee','只能选择') ?>",
        picNumber:"<?php echo Yii::t('franchisee','个图片!') ?>",
        delete:"<?php echo Yii::t('franchisee','删除') ?>",
        ok:"<?php echo Yii::t('franchisee','确定') ?>",
        cancel:"<?php echo Yii::t('franchisee','取消') ?>"
    };
</script>

<?php
/**
 * 默认，原洪华斌写的
 */
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->clientScript;
//样式
$cs->registerCssFile($baseUrl . "/js/swf/css/machine.css?v=1");

if($include_artDialog){
    //弹出框JS插件
    $cs->registerScriptFile($baseUrl . "/js/artDialog/jquery.artDialog.js?skin=blue");
    $cs->registerScriptFile($baseUrl . "/js/artDialog/plugins/iframeTools.js");
}

//显示原图的JS插件
$cs->registerCssFile($baseUrl . "/js/swf/js/fancybox/jquery.fancybox-1.3.4.css");
$cs->registerScriptFile($baseUrl . "/js/swf/js/fancybox/jquery.fancybox-1.3.4.pack.js", CClientScript::POS_END);

//自己写的上茶u年处理插件
$cs->registerScriptFile($baseUrl . "/js/swf/js/uploadImgDefault.js",CClientScript::POS_END );
//图片隐藏域的id
$oid = empty($model) ? CHtml::getIdByName($attribute) : get_class($model) . '_' . $attribute;

$toid = $oid;
if (!empty($tag_id)) $toid = $tag_id;

?>

<div class="ImgList" id="_imgshow<?php echo $toid ?>">

    <div style="width:100%;padding:5px;"></div>

    <?php
    $imgPathData = array(); //初始化循环数据
    if (!empty($model) && $model->$attribute != '') {
        if (is_string($model->$attribute)) {
            $imgPathData = explode($explode_str, $model->$attribute);
        } else if (is_array($model->$attribute)) {
            $imgPathData = $model->$attribute;
        }
    } else {
        if (is_string($value)) {
            $imgPathData = explode($explode_str, $value);
        } else if (is_array($value)) {
            $imgPathData = $value;
        }
    }
    if (!empty($imgPathData)) {
        foreach ($imgPathData as $key => $val) {
            if (empty($val)) continue;
            ?>
            <div class="imgbox">
                <div class="w_upload">
                    <a href="javascript:;"
                       onclick="delImgById(this,'<?php echo $val ?>','<?php echo !empty($tag_id)?$tag_id:get_class($model).'_'.$attribute?>')"
                       class="item_close"><?php echo Yii::t('franchisee','删除'); ?></a>
			<span class="item_box">
				<a class='imga' href='<?php echo $imgarea . "/" . $val ?>' onclick='return _showBigPic(this)'>
                    <img src="<?php echo $imgarea . "/" . $val ?>" class="imgThumb"/>
                </a>
			</span>
                </div>
            </div>
        <?php
        }
    }
    ?>
</div>
<?php

echo CHtml::button($btn_value, array(
        "style" => "cursor:pointer",
        "class" => $btn_class,
        "onclick" => "_fileUpload('" . Yii::app()->createAbsoluteUrl($uploadUrl, array('height' => $height, 'width' => $width, 'img_format' => $img_format)) . "','" . Yii::app()->createAbsoluteUrl($uploadSureUrl, array('imgarea' => $img_area, 'foldername' => $folder_name, 'isdate' => $isdate)) . "',$num,'" .$toid . "','" . Yii::app()->request->csrfToken . "','".session_id()."')",
    )
)
?>
<?php if ($model): ?>
    <?php echo $form->hiddenField($model, $attribute,!empty($tag_id)?array('id'=>$tag_id):array()); //保存数据的隐藏控件，图片分割是使用符号|?>
<?php else: ?>
    <?php echo CHtml::hiddenField($attribute, $value,!empty($tag_id)?array('id'=>$tag_id):array()) ?>
<?php endif; ?>
