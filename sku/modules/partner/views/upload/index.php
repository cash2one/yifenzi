<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
.list{ height:auto; }
</style>
	<?php 
		Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl."/agent/js/swf/css/uploadimg.css");
	
		$cache_imgdata = Yii::app()->fileCache->get(UploadController::FILE_NAME.$this->getUser()->getId());		//读取会员缓存数据
		$i = 0;
		$cache_html = "";
		if(!empty($cache_imgdata)){		//对于上传的缓存数据，我们保存上传图片的名称、路径、分类
			foreach ($cache_imgdata as $key=>$val){
                                if($height!=0&&$width!=0){
                                    @list($rwidth,$rheight,$rtype,$rattr) = getimagesize($val['path']);
                                    
                                    if($rwidth!=$width|$rheight!=$height)continue;		//解决这边上传和那边上传不尺寸不同
                                }
				$i++;
				$cache_html.="<li onclick='choose(this)'>";
				$cache_html.="<a href='javascript:;'><img src='".$val['path']."' height='100px' width='100px'/></a>";
				$cache_html.="<input type='hidden' value='".$val['randnum']."' />";
				$cache_html.="</li>";
			}
		}
		$cache_over = $i>5?"overflow-X:auto;":"";			//左右滚动条 
		$cache_height = $i>0?"height:110px;":"";
	?>
	<style>
		/*缓存图片*/
		.list{ position:relative; left:0; top:0; width:560px; height:700px; <?php echo $cache_height?><?php echo $cache_over?>}
	</style>
	<div id=con>
		<!-- 标签名称 -->
		<ul id=tags>
		  	<li class="selectTag" ><a onClick="selectTag('tagContent1',this)" href="javascript:void(0)"><?php echo Yii::t('sellerUpload', '添加图片'); ?></a> </li>
            <li ><a onClick="selectTag('tagContent0',this)" href="javascript:void(0)"><?php echo Yii::t('sellerUpload', '图片空间'); ?></a> </li>
        </ul>
		<!-- 标签内容 -->
		<div id=tagContent>

			<div class="tagContent" id=tagContent1>
				<?php
					$this->renderPartial('create',
						array(
							'height'=>$height,
							'width'=>$width,
							'img_format'=>$img_format,
						)
					);
				?>
			</div>

            <div class="tagContent selectTag" id=tagContent0>
                <div class="imgListBox" id="thumbnails">
                    <ul class="list" style="height:420px;">
                        <?php echo $cache_html?>
                    </ul>
                </div>
            </div>

		</div>
	</div>

<script type=text/javascript>
    $(function(){
        //模拟点击添加图片标签
        selectTag('tagContent1',$("#tags li:first").get(0));
        $("#tags li:first").addClass('selectTag');
    });
	//切换标签
	function selectTag(showContent,selfObj){
		// 标签
		var tag = document.getElementById("tags").getElementsByTagName("li");
		var taglength = tag.length;
		for(i=0; i<taglength; i++){
			tag[i].className = "";
		}
		selfObj.parentNode.className = "selectTag";
		// 标签内容
		for(i=0; j=document.getElementById("tagContent"+i); i++){
			j.style.display = "none";
		}
		document.getElementById(showContent).style.display = "block";
	}

	//点击图片
	function choose(obj){
		$(obj).hasClass('imghover')?$(obj).removeClass('imghover'):$(obj).addClass('imghover');
	}
</script>