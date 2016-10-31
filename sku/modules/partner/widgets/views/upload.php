<?php 
	$baseUrl = Yii::app()->baseUrl;
	$cs = Yii::app()->clientScript;
	//样式
	$cs->registerCssFile($baseUrl. "/js/swf/css/machine.css?v=1");
	
	//弹出框JS插件
	$cs->registerScriptFile($baseUrl. "/js/swf/js/artDialog.js?skin=blue");
	$cs->registerScriptFile($baseUrl. "/js/swf/js/artDialog.iframeTools.js");
	
	//显示原图的JS插件
	$cs->registerCssFile($baseUrl. "/js/swf/js/fancybox/jquery.fancybox-1.3.4.css"); 			
	$cs->registerScriptFile($baseUrl. "/js/swf/js/fancybox/jquery.fancybox-1.3.4.pack.js", CClientScript::POS_END);			
	
	//自己写的上茶u年处理插件 
	$cs->registerScriptFile($baseUrl. "/js/swf/js/uploadImg.js");					

?>


<?php
echo $form->hiddenField($model,$attribute,!empty($tag_id)?array('id'=>$tag_id):array());//保存数据的隐藏控件，图片分割是使用符号|?>

<p>
<?php 
	$toid = get_class($model)."_".$attribute;
	if (!empty($tag_id)) $toid = $tag_id;
	echo CHtml::link('<span>'.Yii::t('franchisee','设置列表图片').'</span>','javascript:;',array(
			"class"=>'sellerBtn02',
			"onclick"=>"_fileUpload('".Yii::app()->createUrl('/partner/upload/index',array('height'=>$height,'width'=>$width,'img_format'=>$img_format))."','".Yii::app()->createUrl('/partner/upload/sure',array('imgarea'=>$img_area,'foldername'=>$folder_name,'isdate'=>$isdate))."',$num,'".$toid."','".Yii::app()->request->csrfToken."','".session_id()."')",
		));
?>
&nbsp;&nbsp;<span class="gray">(<?php echo Yii::t('franchisee','请上传730*280像素的图片');?>)</span></p>


<div class="mt10">
	<ul class="clearfix"  id="_imgshow<?php echo get_class($model).'_'.$attribute?>">
	<?php
		if($model->$attribute!=''){
			$imgPathData = array();		//初始化循环数据
			
			if (is_string($model->$attribute)){ 
				$imgPathData = explode($explode_str,$model->$attribute);
			}else if (is_array($model->$attribute)){
				$imgPathData = $model->$attribute;
			}
			
			foreach($imgPathData as $key=>$val){
				if(empty($val))continue;
	?>
	
	<li class="fl mr10">
		<p class="mt10"><a href="javascript:;" onclick="delImgById(this,'<?php echo $val?>','<?php echo !empty($tag_id)?$tag_id:get_class($model).'_'.$attribute?>')" class="sellerBtn02"><span><?php echo Yii::t('sellerUpload', '删除图片');?></span></a></p>
		<p class="mt10">
			<a class='imga' href='<?php echo $imgarea."/".$val?>' onclick='return _showBigPic(this)' >
				<img src="<?php echo $imgarea."/".$val?>" width="85" height="85"/>
			</a>
		</p>
	</li>
	
	
	<?php 
			}
		}
	?>
	</ul>
</div>



