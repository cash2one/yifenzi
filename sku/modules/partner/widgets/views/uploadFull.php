<script>
    //js 多语言显示
    var L = {
        imgManage:"<?php echo Yii::t('franchisee','图片管理') ?>",
        ok:"<?php echo Yii::t('franchisee','确定') ?>",
        cancel:"<?php echo Yii::t('franchisee','取消') ?>"
    };
</script>

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
	$cs->registerScriptFile($baseUrl. "/js/swf/js/uploadImgFull.js");					
		
?>


<?php echo $form->hiddenField($model,$attribute);//保存数据的隐藏控件，图片分割是使用符号|?>

<div class="mt15 clearfix">
<?php 
	echo CHtml::link(Yii::t('seller','编辑图片空间'),'javascript:;',array(
			"class"=>'fl btnSellerEditor',
			"onclick"=>"_fileUpload('".Yii::app()->createUrl('/partner/franchiseeUpload/index',array('height'=>$height,'width'=>$width,'img_format'=>$img_format,'folder_name'=>$folder_name))."','".Yii::app()->createUrl('/partner/upload/sure',array('imgarea'=>$img_area,'foldername'=>$folder_name,'isdate'=>$isdate))."',$num,'".get_class($model)."_".$attribute."','".Yii::app()->request->csrfToken."','".session_id()."')",
		));
?>
&nbsp;&nbsp;<p class="red va_m"></p></div>


<div class="mt10" style="display:none;">
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
		<p class="mt10"><a href="javascript:;" onclick="delImgById(this,'<?php echo $val?>','<?php echo get_class($model).'_'.$attribute?>')" class="sellerBtn02"><span><?php echo Yii::t('seller', '删除图片');?></span></a></p>
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



