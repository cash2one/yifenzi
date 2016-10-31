<?php 
	//引入JS和CSS
	$cs = Yii::app()->clientScript;
	$baseUrl = Yii::app()->baseUrl;
	$cs->registerCssFile($baseUrl."/js/swf/css/default.css");
       $cs->registerCoreScript('jquery');
	$cs->registerScriptFile($baseUrl."/js/swf/swfupload/swfupload.js");
	$cs->registerScriptFile($baseUrl."/js/swf/js/swfupload.queue.js");
	$cs->registerScriptFile($baseUrl."/js/swf/js/fileprogress.js");
	$cs->registerScriptFile($baseUrl."/js/swf/js/handlers.js");
?>

<script type="text/javascript">
	var swfu;
	$(function() {
		var settings = {
			flash_url : "<?php echo $baseUrl;?>/js/swf/swfupload/swfupload.swf",	
			upload_url: "<?php echo Yii::app()->createUrl('upload/upload');?>",	// Relative to the SWF file
			post_params: {"PHPSESSID" : "<?php echo session_id(); ?>","YII_CSRF_TOKEN":'<?php echo Yii::app()->request->csrfToken;?>'},
			file_size_limit : "2 MB",
			file_types : "<?php echo $img_format?>",
			file_types_description : "图像",
			file_upload_limit : 100,
			file_post_name : '<?php echo UploadController::FILE_UPLOAD_NAME?>',
			file_queue_limit : 10,
			custom_settings : {
				progressTarget : "fsUploadProgress",
				cancelButtonId : "btnCancel"
			},
			debug: false,

			// Button settings
			button_image_url: "/js/swf/images/btn1.gif",	//Relative to the Flash file
			button_width: "125",
			button_height: "25",
			button_placeholder_id: "spanButtonPlaceHolder",
			button_text: '<b><span class="redText" ><?php echo Yii::t('sellerUpload','选择上传文件') ?></span></b>',
			button_text_style: ".redText{color:#ffffff;font-size:14px;text-align:center}",
			button_text_left_padding: 0,
			button_text_top_padding: 0,

			// The event handler functions are defined in handlers.js
			file_queued_handler : fileQueued,//当选好文件，文件选择对话框关闭消失时，如果选择的文件成功加入待上传队列，那么针对每个成功加入的文件都会触发一次该事件。对应：file_queued_handler
			file_queue_error_handler : fileQueueError,//同上，文件加入队列失败的时候调用，对应file_queue_error_handler，超过载入限制那么指出发一次所有文件都不入队列
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			queue_complete_handler : queueComplete	// Queue plugin event
		};

		swfu = new SWFUpload(settings);
     });

     function uploadStart(obj){
		swfu.addPostParam("HEIGHT",<?php echo $height?>);
		swfu.addPostParam("WIDTH",<?php echo $width?>);
		return true;
     }
</script>
	<div class="fieldset flash" id="fsUploadProgress"><span class="legend">快速上传</span></div>
		<div id="divStatus">0 个文件已上传</div>
	<div>
		<span id="spanButtonPlaceHolder"></span>
		<input id="btnUpload" type="button" value="" onclick="swfu.startUpload();" disabled="disabled" style="margin-left: 2px; width:121px; height: 25px; background:url(/js/swf/images/btn2.gif) no-repeat; border:0;" />
		<input id="btnCancel" type="button" value="取消所有上传" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
	</div>

