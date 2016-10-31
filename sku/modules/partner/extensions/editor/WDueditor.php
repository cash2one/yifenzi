<?php

/**
 * WDueditor for Yii extensions
 *
 * @example  单独使用图片上传功能
  $this->widget('comext.wdueditor.WDueditor', array(
  'model' => $model,
  'attribute' => 'content',
  'uploadValue' => '上传图片',
  // 'uploadCallBack' =>'alert(123);' ,
  'separate' => '|', //多张图片之间的分隔符
  'maxNum' => 3, //最多选择的图片数
  'htmlOptions' => array(
  'style' => 'display:none',
  ),
  //'save_path' => 'uploads/UE_uploads',  //默认是'attachments/UE_uploads'
  //'url' => IMG_DOMAIN.'/UE_uploads'	  //默认是ATTR_DOMAIN.'/UE_uploads'
  ));
 *
 * @ueditorSite http://ueditor.baidu.com
 * @ueditor  https://github.com/campaign/ueditor
 * @author WindsDeng <winds@dlf5.com> QQ:620088997 WindsDeng's Blog http://www.dlf5.com
 * @license BSD许可证
 */
class WDueditor extends CInputWidget {

    /**
     * Editor language
     * Supports: zh-cn  or en
     */
    public $language = 'zh-cn';

    /**
     * Editor toolbars
     * Supports:
     */
    public $toolbars = '';

    /**
     * Html options that will be assigned to the text area
     */
    public $htmlOptions = array();

    /**
     * Editor options that will be passed to the editor
     */
    public $editorOptions = array();

    /**
     * Debug mode
     * Used to publish full js file instead of min version
     */
    public $debug = YII_DEBUG;

    /**
     * Editor width
     */
    public $width = '100%';

    /**
     * Editor height
     * 此处不带单位，最小高度是220px
     */
    public $height = '220';

    /**
     * Editor theme
     * Supports: default
     */
    public $theme = 'default';

    /*
     * 上传的目录
     */
    public $save_path = ''; //配置上传目录，起始在根目录的uploads下,路径必须在根目录的attachments或者uploads下，如'attachments/UE_uploads';

    /*
     * 访问图片的url
     */
    public $url = '';  //图片访问的地址

    /**
     * 上传按钮的显示，如果为空，则是普通的编辑器应用,否则是单独使用图片上传功能
     * @var string “上传图片”
     */
    public $uploadValue = '';

    /**
     * 单独使用图片上传功能，上传图片后 js 回调
     * @var sting js
     */
    public $uploadCallBack = '';

    /**
     * 单独使用图片上传功能的时候，多个图片之间的分隔符，默认为空，只选择第一张选择的图片
     * @var string
     */
    public $separate = '';

    /**
     * 最大的图片选择个数
     * @var int
     */
    public $maxNum = '100000';

    /**
     * 允许的最大字符数
     * @var int
     */
    public $maximumWords = '100000';

    /**
     * 双击删除图片的处理相对地址
     * @var string
     */
    public $imageDeleteUrl = '/?r=ueditor/fileDelete';

    /*
     * 网站的域名
     */
    public $base_url = DOMAIN_PARTNER;    //默认是后台域名

    /**
     * Display editor
     */
    public function run() {

    	//设置语言
    	if(Yii::app()->language=='en'){
    		$this->language = Yii::app()->language;
    	}

        // Resolve name and id
        list($name, $id) = $this->resolveNameID();

        // Get assets dir
//        $baseDir = dirname(__FILE__);
//        $assets = Yii::app()->getAssetManager()->publish($baseDir.DIRECTORY_SEPARATOR.'ueditor1_2_5');
        $base_url = $this->base_url;
        $assets = $base_url . "/ueditor";
        // Publish required assets
        $cs = Yii::app()->getClientScript();

        $jsFile = $this->debug ? 'editor_all.js' : 'editor_all_min.js';

        $cs->registerScriptFile($assets . '/' . $jsFile);
        $cs->registerScriptFile($assets . '/editor_config.js');

        $this->htmlOptions['id'] = $id;

        if (!array_key_exists('style', $this->htmlOptions)) {
            $this->htmlOptions['style'] = "width:{$this->width};";
        }

        if ($this->toolbars) {
            $this->editorOptions['toolbars'][] = $this->toolbars;
        } else {
            //设置默认的工具栏
            $this->editorOptions['toolbars'][] = array(
                'fullscreen', 'source', '|', 'undo', 'redo', '|',
                'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
                'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                'directionalityltr', 'directionalityrtl', 'indent', '|',
                'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
                'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                'insertimage', 'emotion', 'scrawl', 'map', 'gmap', 'insertframe', 'pagebreak', 'template', '|',
                'horizontal', 'date', 'time', 'spechars', '|',
                'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|',
                'print', 'preview', 'searchreplace', 'help'
            );
        }

        if ($this->url === '' && empty($this->uploadValue)) {
            $this->url = ATTR_DOMAIN . '/UE_uploads';
        }
        /*
         * 对上传地址和图片访问地址进行配置
         */
        $config = array(
            'theme' => $this->theme, 'lang' => $this->language, 'UEDITOR_HOME_URL' => "$assets/", 'initialFrameWidth' => $this->width, 'initialFrameHeight' => $this->height,
            //图片配置区
            'imageUrl' => $base_url . '/ueditor/imageUp/',
            'imagePath' => $this->url,
            //涂鸦配置区
            'scrawlUrl' => $base_url . '/ueditor/scrawlUp/',
            'scrawlPath' => $this->url,
            //图片在线管理配置区
            'imageManagerUrl' => $base_url . '/ueditor/imageManager/',
            'imageManagerPath' => $this->url,
            //双击删除图片的处理地址
            'imageDeleteUrl' => $base_url . $this->imageDeleteUrl,
            //允许的最大字符数
            'maximumWords' => $this->maximumWords,
        );
        //如果是单独上传图片，则只保留 图片上传工具栏
        if (!empty($this->uploadValue)) {
            $this->editorOptions['toolbars'][0] = array('insertimage');
        }
        $options = CJSON::encode(array_merge($config, $this->editorOptions));
        if ($this->save_path === '') {
            $this->save_path = 'attachments/UE_uploads';
        }
        if(!UPLOAD_REMOTE){
            $this->save_path = str_replace('\\','/',dirname(Yii::getPathOfAlias('att')) .'/'. $this->save_path) ; //图片资源已经被移动到项目外面的source目录
        }else{
            $this->save_path = UPLOAD_REMOTE.$this->save_path;
        }

        $csrfToken = Yii::app()->request->csrfToken;
        //为了多个ueditor在一个页面使用不受影响， 添加图片窗口延迟侦听事件
        $js = <<<EOP
editor_{$id} = UE.getEditor('$id',$options);
UEDITOR_CONFIG.savePath = "$this->save_path";
UEDITOR_CONFIG.csrfToken = '$csrfToken';
setTimeout(function() {
    var dialog_{$id} = editor_{$id}.getDialog("insertimage");
    dialog_{$id}.addListener('show', function() {
       UEDITOR_CONFIG.savePath = "$this->save_path";
       UEDITOR_CONFIG.csrfToken = '$csrfToken';
    });
}, 5000);
EOP;
        //只是使用editor图片上传接口。为了兼容ie,点击后才实例化，并延时300
        $domain = $this->url;
        if (!empty($this->uploadValue)) {
            $js = <<<EOP
    $("#{$id}_button").click(function(){
    var editor_{$id} = UE.getEditor('{$id}_x',$options);
    UEDITOR_CONFIG.savePath = "$this->save_path";
    UEDITOR_CONFIG.csrfToken = '$csrfToken';
       setTimeout(function(){
        var dialog{$id} = editor_{$id}.getDialog("insertimage");
        dialog{$id}.open();
        editor_{$id}.addListener('beforeInsertImage', function(t, arg) {
            var imgSrc = "";
            if(arg.length > 0 && arg[0].src && "{$this->separate}".length>0){
                if("{$this->maxNum}".length>0){
                    if(arg.length>{$this->maxNum}){
                        alert("本次操作做多只能选择{$this->maxNum}张图片");
                        arg.length = {$this->maxNum};
                        }
                }
                var imgs = [];
               for(var i=0; i<arg.length; i++){
                   imgs.push(arg[i].src);
                }
                imgSrc = imgs.join("{$this->separate}");
            }else{
                if(arg.length>0){
                    imgSrc = arg.src ? arg.src : arg[0].src ;
                }
            }
            //替换成短路径
            var regS = new RegExp("{$this->url}","gi");
            imgSrc = imgSrc.replace(regS,"");

            inputImg = $("#{$id}").val();
            var imgSrc2=inputImg.length>0 ? inputImg+"{$this->separate}"+imgSrc : imgSrc;
            //去除重复
            var array_unique = function (inputArr) {
                var key = '',
                    tmp_arr2 = {},
                    val = '';

                var __array_search = function (needle, haystack) {
                    var fkey = '';
                    for (fkey in haystack) {
                        if (haystack.hasOwnProperty(fkey)) {
                            if ((haystack[fkey] + '') === (needle + '')) {
                                return fkey;
                            }
                        }
                    }
                    return false;
                };

                for (key in inputArr) {
                    if (inputArr.hasOwnProperty(key)) {
                        val = inputArr[key];
                        if (false === __array_search(val, tmp_arr2)) {
                            tmp_arr2[key] = val;
                        }
                    }
                }
                var last_tmp = [];
                  for(x in tmp_arr2){
                    last_tmp.push(tmp_arr2[x]);
                  }
                  return last_tmp;
            }// end array_unique function
            imgSrc2 = array_unique(imgSrc2.split("{$this->separate}"));
            imgSrc2.length = imgSrc2.length > {$this->maxNum} ? {$this->maxNum} : imgSrc2.length;
            imgSrc2 = imgSrc2.join("{$this->separate}");
            $("#{$id}").val(imgSrc2);
                //显示图片
                imgSrc = imgSrc2.split("{$this->separate}");
                var imgHtml = "<script>function uploadifyRemove2(fileId,attrName){ "+
                "$(\"#\"+attrName+fileId).remove();"+
                "var allLi = $(\"#imgUploadList{$id} li\"),tmp_arr = [];"+
                "for(i=0;i<allLi.length;i++){if($(allLi[i]).attr('data-src')!=$(\"#\"+attrName+fileId).attr('data-src')) tmp_arr.push($(allLi[i]).attr('data-src'));}"+
                "$(\"#{$id}\").val(tmp_arr.join(\"{$this->separate}\"));"
                +"}<\/script>";
                for(var i=0; i<imgSrc.length; i++){
                    imgHtml += "<li id=img_"+i+" data-src=\""+imgSrc[i]+"\"><img width=\"80\"  style=\"display:inline-block\" src={$domain}"+imgSrc[i]+
                    " /><input type=\"hidden\" value="+imgSrc[i]+
                    " name=\"imgList[]\" > <a href='javascript:uploadifyRemove2("+i+",\"img_\")'>X</a></li>"
                }
                imgHtml += "<br />";
            $("#imgUploadList{$id}").html(imgHtml);
            {$this->uploadCallBack};
        });
            },300);
   });
EOP;
        }
        // Register js code
        $cs->registerScript('Yii.' . get_class($this) . '#' . $id, $js, CClientScript::POS_READY);

        if (empty($this->uploadValue)) {
            // Do we have a model
            if ($this->hasModel()) {
                $html = CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
            } else {
                $html = CHtml::textArea($name, $this->value, $this->htmlOptions);
            }
        } else {
            // 如果 effectValue 不为空，则只是显示文件上传
            if ($this->hasModel()) {
                $html_2 = CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
            } else {
                $html_2 = CHtml::textField($name, $this->value, $this->htmlOptions);
            }
            $html = $html_2 . '<input id="' . $id . '_button" type="button" class="regm-sub" value="' .
                    $this->uploadValue . '" /><ul id="imgUploadList' . $id . '" class="imgList"></ul>';
        }

        echo $html;
    }

}