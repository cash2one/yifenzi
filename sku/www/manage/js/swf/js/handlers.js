/**
 * fileQueued(file object)
 * 文件加载到队列的事件
 * 当选择好文件，文件选择对话框关闭消失时，如果选择的文件成功加入待上传队列，那么针对每个成功加入的文件都会触发一次该事件（N个文件成功加入队列，就触发N次此事件）。
 * 对应设置中的自定义事件file_queued_handler
 */
function fileQueued(file) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("等待上传...");
		progress.toggleCancel(true, this);

	} catch (ex) {
		this.debug(ex);
	}
}


/**
 * fileQueueError(file object, error code, message)
 * 文件加载到队列失败的事件
 * 当选择文件对话框关闭时，如果选择的文件加入到上传队列中失败，那么针对每个出错的文件都会触发一次该事件(此事件和fileQueued事件是二选一触发，文件添加到队列只有两种可能，成功和失败)。
 * 文件入队出错的原因可能有：1.超过了上传大小限制，2.文件为零字节，3.超过文件队列数量限制，4.允许外的无效文件类型。
 * (yukon:经测试，message所含的内容如下：
 * 1.超过了上传大小限制：message=File size exceeds allowed limit.
 * 2.文件为零字节：message=File is zero bytes and cannot be uploaded.
 * 3.超过文件队列数量限制：message=int（指你设定的队列大小限制数）
 * 4.允许外的无效文件类型：message=File is not an allowed file type.
 * 如果你要改这些消息，请在开源包里的swfupload.as里改，然后重新编译成swfupload.swf。
 * )
 * 具体的出错原因可由error code参数来获取，error code的类型可以查看SWFUpload.QUEUE_ERROR中的定义。
 * 提醒：对应设置中的自定义事件file_queue_error_handler
 * 注意：如果选择入队的文件数量超出了设置中的数量限制，那么所有文件都不入队，此事件只触发一次。如果没有超出数目限制，那么会对每个文件进行文件类型和大小的检测，对于不通过的文件触发此事件，通过的文件成功入队
 */
function fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("对不起，每次最多允许选择"+message+"个文件");
			return;
		}

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			progress.setStatus("文件太大.");
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus("不允许上传0字节的文件.");
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus("未知文件类型.");
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:
			if (file !== null) {
				progress.setStatus("未知错误！");
			}
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

/**
 * fileDialogComplete(number of files selected, number of files queued, total number of files in the queued)
 * 选择文件弹出框关闭之后的事件
 * 当选择文件对话框关闭，并且所有选择文件已经处理完成（加入上传队列成功或者失败）时，此事件被触发，number of files selected是选择的文件数目，number of files queued
 * 是此次选择的文件中成功加入队列的文件数目。
 * 提醒：对应设置中的自定义事件file_dialog_complete_handler
 * 注意：如果你希望文件在选择以后自动上传，那么在这个事件中调用this.startUpload() 是一个不错的选择。 如果需要更严格的判断，在调用上传之前，可以对入队文件的个数做一个判断，
 * 如果大于0，那么可以开始上传。
 */
function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesSelected > 0) {
			document.getElementById('btnUpload').disabled = false;
			document.getElementById(this.customSettings.cancelButtonId).disabled = false;
		}
	} catch (ex) {
		this.debug(ex);
	}
}

/**
 * uploadStart(file object)
 * 上传图片事件
 * 在文件开始向服务端上传之前触发uploadStart事件，这个事件处理函数可以完成上传前的最后验证以及其他你需要的操作，例如添加、修改、删除post数据等。
 * 在完成最后的操作以后，如果函数返回false，那么这个上传不会被启动，如果返回true或者无返回，那么将正式启动上传.
 * 提醒：对应设置中的自定义事件upload_start_handler
 */
function uploadStart(file) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("上传中...");
		progress.toggleCancel(true, this);
	}
	catch (ex) {}

	return true;
}

/**
 * uploadProgress(file object, bytes complete, total bytes)
 * 上传过程中的事件：上传进度
 * uploadProgress事件由flash控件定时触发，提供三个参数分别访问上传文件对象、已上传的字节数，总共的字节数。因此可以在这个事件中来定时更新页面中的UI元素，以达到及时显示上传进度的效果。
 * 注意: 在Linux下，Flash Player只在整个文件上传完毕以后才触发一次该事件，官方指出这是Linux Flash Player的一个bug，目前SWFpload库无法解决。
 * 提醒：对应设置中的自定义事件upload_progress_handler
 */
function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		progress.setStatus("上传中...");
	} catch (ex) {
		this.debug(ex);
	}
}

/**
 * uploadSuccess(file object, server data, received response)
 * 上传成功。当文件上传的处理已经完成（这里的完成只是指向目标处理程序发送完了Files信息，只管发，不管是否成功接收），并且服务端返回了200的HTTP状态时，触发uploadSuccess事件。
 * server data指的是服务器发出的一些数据（比如你自己echo出的）而received response是服务器自己发出的HTTP状态码
 * 由于一些Flash Player的bug，HTTP状态码可能不会被获取到，从而导致uploadSuccess事件不能被触发。由于这个原因，2.50版在设置对象中增加了一个新属性assume_success_timeout 
 * 用来设置是否超过了等待接收HTTP状态码的最长时间，超过即触发 uploadSuccess。
 * 在这种情况下，（received response）参数会无效。
 * 设置对象中的 http_success 允许设置在HTTP状态码为非200的其他值时也触发uploadSuccess事件。In this case no server data is available from the Flash Player.
 * 在
 * 提醒：对应设置中的自定义事件upload_success_handler
 * 注意：
 * 1.server data是服务端处理程序返回的数据。
 * 2.此时文件上传的周期还没有结束，不能在这里开始下一个文件的上传。
 * 3.在window平台下，那么服务端的处理程序在处理完文件存储以后，必须返回一个非空值，否则此事件不会被触发，随后的uploadComplete事件也无法执行。
 */
function uploadSuccess(file, serverData) {
	try {
		if (serverData.indexOf("suc")=='0') {//如果提示成功
			var progress = new FileProgress(file, this.customSettings.progressTarget);
			progress.setComplete();
			var status = "恭喜你，文件上传成功！ <br />";
			var endnum = serverData.indexOf("|");
			
			var content = serverData.substring(4,endnum);
			var contents = content.split("->,");
			var url = contents[1];										//图片上传之后路径
			var randnum = contents[0];												//保存图片信息的缓存主键
			
			showImage(url,randnum);
			progress.setStatus(status);
			progress.toggleCancel(false);
		}else{
			var progress = new FileProgress(file, this.customSettings.progressTarget);
			progress.setError();
			var endnum = serverData.indexOf("|");
			progress.setStatus("上传失败："+serverData.substring(3,endnum));
			progress.toggleCancel(false);
		}

	} catch (ex) {
		this.debug(ex);
	}
}

/**
 * uploadComplete(file object)
 * 上传完成事件
 * 当上传队列中的一个文件完成了一个上传周期，无论是成功(uoloadSuccess触发)还是失败(uploadError触发)，uploadComplete事件都会被触发，这也标志着一个文件的上传完成，可以进行下一个文件的上传了。
 * 如果要下个文件自动上传，那么在这个时候调用this.startUpload()来启动下一个文件的上传是不错的选择。不过要小心使用。参见注意
 * 提醒：对应设置中的自定义事件upload_complete_handler
 * 注意：当在进行多文件上传的时候，中途用cancelUpload取消了正在上传的文件，或者用stopUpload停止了正在上传的文件，那么在uploadComplete中就要很小心的使用this. startUpload()，
 * 因为在上述情况下，uploadError和uploadComplete会顺序执行，因此虽然停止了当前文件的上传，但会立即进行下一个文件的上传，你可能会觉得这很奇怪，但事实上程序并没有错。
 * 如果你希望终止整个队列的自动上传，那么你需要做额外的程序处理了。
 */
function uploadComplete(file) {
	try {
		/*  I want the next upload to continue automatically so I'll call startUpload here */
		if (this.getStats().files_queued === 0) {
			document.getElementById('btnUpload').disabled = true;
			document.getElementById(this.customSettings.cancelButtonId).disabled = true;
		}
	} catch (ex) {
		this.debug(ex);
	}
}

/**
 * uploadError(file object, error code, message)
 * 上传出错
 * 无论什么时候，只要上传被终止或者没有成功完成，那么uploadError事件都将被触发。error code参数表示了当前错误的类型，更具体的错误类型可以参见SWFUpload.UPLOAD_ERROR中的定义。
 * Message参数表示的是错误的描述。File参数表示的是上传失败的文件对象。
 * 例如，我们请求一个服务端的一个不存在的文件处理页面，那么error code会是-200，message会是404。 
 * 停止、退出、uploadStart返回false、HTTP错误、IO错误、文件上传数目超过限制等，都将触发该事件，
 * Upload error will not fire for files that are cancelled but still waiting in the queue。
 * （对于官方的这句话我还存在疑问，文件退出以后怎么还会保留在文件上传队列中保留呢？）
 * 提醒：对应设置中的自定义事件upload_error_handler
 * 注意：此时文件上传的周期还没有结束，不能在这里开始下一个文件的上传。
 */
function uploadError(file, errorCode, message) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);
//		alert(message+'======'+errorCode);
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus("文件上传失败: " + message);
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("文件上传失败");
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("服务器IO错误");
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("安全错误");
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus("文件大小超过限制");
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus("验证失败，上传已被跳过");
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			// If there aren't any files left (they were all cancelled) disable the cancel button
			if (this.getStats().files_queued === 0) {
				document.getElementById('btnUpload').disabled = true;
				document.getElementById(this.customSettings.cancelButtonId).disabled = true;
			}
			progress.setStatus("已取消上传");
			progress.setCancelled();
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("已停止上传");
			break;
		default:
			progress.setStatus("未知错误: " + errorCode);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function showImage(src,num){
	var img = "<li onclick='choose(this)'><a href='javascript:;'><img src='"+src+"' id='' height='100px' width='100px'></a><input type='hidden' value='"+num+"' /></li>";
	var linum = $('.list').find('li').length;
	//设定显示高度
	if(linum==0)$('.list').addClass("heightdiv");
	if(linum==20)$('.list').addClass("overdiv");
	
	$('.list').prepend(img);
}

/**
 * 添加图片，这个需要在上传的时候的代码的配合
 */
function addImage(src) {
	var newImg = document.createElement("img");
	newImg.style.margin = "5px";

	document.getElementById("thumbnails").appendChild(newImg);
	if (newImg.filters) {
		try {
			newImg.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 0;
		} catch (e) {
			// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
			newImg.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + 0 + ')';
		}
	} else {
		newImg.style.opacity = 0;
	}

	newImg.onload = function () {
		fadeIn(newImg, 0);
	};
	newImg.src = src;
}

/**
 * 将添加的图片显示出来，同样需要上传图片代码配合
 */
function fadeIn(element, opacity) {
	var reduceOpacityBy = 5;
	var rate = 30;	// 15 fps


	if (opacity < 100) {
		opacity += reduceOpacityBy;
		if (opacity > 100) {
			opacity = 100;
		}

		if (element.filters) {
			try {
				element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
				element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
			}
		} else {
			element.style.opacity = opacity / 100;
		}
	}

	if (opacity < 100) {
		setTimeout(function () {
			fadeIn(element, opacity);
		}, rate);
	}
}

//This event comes from the Queue Plugin
function queueComplete(numFilesUploaded) {
	var status = document.getElementById("divStatus");
	status.innerHTML = numFilesUploaded + " 个文件被上传.";
}