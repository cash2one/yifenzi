/**
 * 上传图片	
 * @param openUrl 		访问图片地址
 * @param picNum  		选择多少个图片
 * @param uploadUrl		上传地址
 * @param oid			保存图片id的控件id
 * @param csrfToken		crsf验证码
 */
function _fileUpload(openUrl,uploadUrl,picNum,oid,csrfToken){
	art.dialog.open(openUrl,{
		title: L.imgManage,
		lock:true,
		//在open方法中，init会等待iframe加载完毕后执行
		init:function(){//可以再这个地方写窗体加载之后的事件
		},
		ok:function(){
			location.reload();
			return ;
		},
        cancel:true,
        okVal: L.ok,
        cancelVal: L.cancel
	});
}


/**
 * 点击图片显示放大图
 * @param obj 链接对象
 */
function _showBigPic(obj)
{
	$.fancybox({
		href: $(obj).attr("href"),
		'overlayShow'	: true,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic'
	});
	return false;
}