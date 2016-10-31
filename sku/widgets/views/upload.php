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
	
	//自己写的上传处理插件 
	$cs->registerScriptFile($baseUrl. "/js/swf/js/uploadImg.js");					
		
?>
<div class="ImgList" id="_imgshow<?php echo get_class($model).'_'.$attribute?>">


<?php 
	echo CHtml::button($btn_value,array(
			"style"=>"cursor:pointer",
			"class"=>$btn_class,
			"onclick"=>"_fileUpload('".Yii::app()->createUrl('upload/index',array('height'=>$height,'width'=>$width,'img_format'=>$img_format))."','".Yii::app()->createUrl('upload/sure',array('imgarea'=>$img_area,'foldername'=>$folder_name,'isdate'=>$isdate))."',$num,'".get_class($model)."_".$attribute."','".Yii::app()->request->csrfToken."')",
		)
	)
?>
<div style="width:100%;padding:5px;"></div>


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
	<div class="imgbox"> 
		<div class="w_upload">
			<a href="javascript:;" onclick="delImgById(this,'<?php echo $val?>','<?php echo get_class($model).'_'.$attribute?>')" class="item_close">删除</a>
			<span class="item_box">
				<a class='imga' href='<?php echo $imgarea."/".$val?>' onclick='return _showBigPic(this)' >
					<img src="<?php echo $imgarea."/".$val?>" class="imgThumb" />
				</a>
			</span>
		</div>
	</div>
	<?php 
			}
		}
	?>
</div>
<?php echo $form->hiddenField($model,$attribute);//保存数据的隐藏控件，图片分割是使用符号|?>
