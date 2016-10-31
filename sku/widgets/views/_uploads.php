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
<script src="<?php echo Yii::app()->request->hostInfo."/manage/js/swf/js/uploadImg.js"?>"></script>
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
$oid2 = empty($model) ? CHtml::getIdByName($attribute2) : get_class($model) . '_' . $attribute2;
$oid3 = empty($model) ? CHtml::getIdByName($attribute3) : get_class($model) . '_' . $attribute3;
$toid = $oid;
$toid2 = $oid2;
$toid3 = $oid3;
if (!empty($tag_id)) $toid = $tag_id;
if (!empty($tag_id2)) $toid2 = $tag_id2;
if (!empty($tag_id3)) $toid3 = $tag_id3;
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
	
	<?php
    $imgPathData2 = array(); //初始化循环数据
    if (!empty($model) && $model->$attribute2 != '') {
        if (is_string($model->$attribute2)) {
            $imgPathData2 = explode($explode_str, $model->$attribute2);
        } else if (is_array($model->$attribute2)) {
            $imgPathData2 = $model->$attribute2;
        }
    } else {
        if (is_string($value2)) {
            $imgPathData2 = explode($explode_str, $value2);
        } else if (is_array($value2)) {
            $imgPathData2 = $value2;
        }
    }
    if (!empty($imgPathData2)) {
        foreach ($imgPathData2 as $key => $val2) {
            if (empty($val2)) continue;
            ?>
            <div class="imgbox">
                <div class="w_upload">
                    <a href="javascript:;"
                       onclick="delImgById(this,'<?php echo $val2 ?>','<?php echo !empty($tag_id2)?$tag_id2:get_class($model).'_'.$attribute2?>')"
                       class="item_close"><?php echo Yii::t('franchisee','删除'); ?></a>
			<span class="item_box">
				<a class='imga' href='<?php echo $imgarea . "/" . $val2 ?>' onclick='return _showBigPic(this)'>
                    <img src="<?php echo $imgarea . "/" . $val2 ?>" class="imgThumb"/>
                </a>
			</span>
                </div>
            </div>
        <?php
        }
    }
    ?>
	
	<?php
    $imgPathData3 = array(); //初始化循环数据
    if (!empty($model) && $model->$attribute3 != '') {
        if (is_string($model->$attribute3)) {
            $imgPathData3 = explode($explode_str, $model->$attribute3);
        } else if (is_array($model->$attribute3)) {
            $imgPathData3 = $model->$attribute3;
        }
    } else {
        if (is_string($value3)) {
            $imgPathData3 = explode($explode_str, $value3);
        } else if (is_array($value3)) {
            $imgPathData3 = $value3;
        }
    }
    if (!empty($imgPathData3)) {
        foreach ($imgPathData3 as $key => $val3) {
            if (empty($val3)) continue;
            ?>
            <div class="imgbox">
                <div class="w_upload">
                    <a href="javascript:;"
                       onclick="delImgById(this,'<?php echo $val3 ?>','<?php echo !empty($tag_id3)?$tag_id3:get_class($model).'_'.$attribute3?>')"
                       class="item_close"><?php echo Yii::t('franchisee','删除'); ?></a>
			<span class="item_box">
				<a class='imga' href='<?php echo $imgarea . "/" . $val3 ?>' onclick='return _showBigPic(this)'>
                    <img src="<?php echo $imgarea . "/" . $val3 ?>" class="imgThumb"/>
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
       // "onclick" => "_fileUpload('" . Yii::app()->createAbsoluteUrl($uploadUrl, array('height' => $height, 'width' => $width, 'img_format' => $img_format)) . "','" . Yii::app()->createAbsoluteUrl($uploadSureUrl, array('imgarea' => $img_area, 'foldername' => $folder_name, 'isdate' => $isdate)) . "',$num,'" .$toid . "','" . Yii::app()->request->csrfToken . "','".session_id()."')",
        "id" => "fileUpload",
	)
)
?>

<?php if ($model): ?>
    <?php echo $form->hiddenField($model, $attribute,!empty($tag_id)?array('id'=>$tag_id):array()); //保存数据的隐藏控件，图片分割是使用符号|?>
	<?php echo $form->hiddenField($model, $attribute2,!empty($tag_id2)?array('id'=>$tag_id2):array()); //保存数据的隐藏控件，图片分割是使用符号|?>
	<?php echo $form->hiddenField($model, $attribute3,!empty($tag_id3)?array('id'=>$tag_id3):array()); //保存数据的隐藏控件，图片分割是使用符号|?>
<?php else: ?>
    <?php echo CHtml::hiddenField($attribute, $value,!empty($tag_id)?array('id'=>$tag_id):array()) ?>
	<?php echo CHtml::hiddenField($attribute2, $value2,!empty($tag_id2)?array('id'=>$tag_id2):array()) ?>
	<?php echo CHtml::hiddenField($attribute3, $value3,!empty($tag_id3)?array('id'=>$tag_id3):array()) ?>
<?php endif; ?>

<script type ="text/javascript">
$("#fileUpload").bind('click',function() {  
   $imgCount = $('.imga').children('.imgThumb').length;
   
   var openUrl ='<?php echo Yii::app()->createAbsoluteUrl($uploadUrl, array('height' => $height, 'width' => $width, 'img_format' => $img_format)) ?>';
   var uploadUrl = '<?php echo Yii::app()->createAbsoluteUrl($uploadSureUrl, array('imgarea' => $img_area, 'foldername' => $folder_name, 'isdate' => $isdate))?>';
   var picNum = 3;
   var oid = '<?php echo $toid?>';
   var oid2 = '<?php echo $toid2?>';
   var oid3 = '<?php echo $toid3?>';
  // var arr=["oid","oid2","oid3"]; 
  var arr = new Array(oid,oid2,oid3);

   var csrfToken ='<?php echo Yii::app()->request->csrfToken?>';
   if($imgCount<4){
	   _fileUpload(openUrl,uploadUrl,picNum,arr,csrfToken);
   }else{
	   alert('已上传3张图片，请删除后再上传');
   }
})
</script>
