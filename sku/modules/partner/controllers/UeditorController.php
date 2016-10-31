<?php

/*
 * 将百度编辑器服务端的php处理抽到控制器中
 * @author chen.luo <lqahnh@qq.com>
 */

class UeditorController extends SSController {
    /*
     * 图片上传处理,对应的是imageUp.php
     */

    public function actionImageUp() {
        header("Content-Type: text/html; charset=utf-8");
        error_reporting(E_ERROR | E_WARNING);

        include_once ConfigDir.DS.'..'.DS.'www'.DS.'ueditor'.DS.'php'.DS.'Uploader.class.php';
       
        //上传图片框中的描述表单名称，
        $title = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
        $path = htmlspecialchars($_POST['dir'], ENT_QUOTES);

        //上传配置
        $config = array(
            "savePath" => $path,
            "maxSize" => 1000, //单位KB
            "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp")
        );

        //生成上传实例对象并完成上传
        $up = new Uploader("upfile", $config);

        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "originalName" => "",   //原始文件名
         *     "name" => "",           //新文件名
         *     "url" => "",            //返回的地址
         *     "size" => "",           //文件大小
         *     "type" => "" ,          //文件类型
         *     "state" => ""           //上传状态，上传成功时必须返回"SUCCESS"
         * )
         */
        $info = $up->getFileInfo();

        //压缩图片
        @Tool::resize_pic($info['url']);
        
        /**
         * 向浏览器返回数据json数据
         * {
         *   'url'      :'a.jpg',   //保存后的文件路径
         *   'title'    :'hello',   //文件描述，对图片来说在前端会添加到title属性上
         *   'original' :'b.jpg',   //原始文件名
         *   'state'    :'SUCCESS'  //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
         * }
         */
        $info["url"] = str_replace($config['savePath'], '', $info["url"]);
        echo "{'url':'" . $info["url"] . "','title':'" . $title . "','original':'" . $info["originalName"] . "','state':'" . $info["state"] . "'}";
    }

    /*
     * 涂鸦上传处理，对应scrawlUp.php
     */

    public function actionScrawlUp() {
        header("Content-Type:text/html;charset=utf-8");
        error_reporting(E_ERROR | E_WARNING);
        Yii::import('comvendor.Uploader');
        //上传配置
        $path = 'UE_uploads/';
        $config = array(
            "savePath" => '../../attachments/' . $path, //存储文件夹
            "maxSize" => 1000, //允许的文件最大尺寸，单位KB
            "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp")  //允许的文件格式
        );
        //临时文件目录
        $tmpPath = "tmp/";

        //获取当前上传的类型
        $action = htmlspecialchars($_GET["action"]);
        if ($action == "tmpImg") { // 背景上传
            //背景保存在临时目录中
            $config["savePath"] = $tmpPath;
            $up = new Uploader("upfile", $config);
            $info = $up->getFileInfo();
            /**
             * 返回数据，调用父页面的ue_callback回调
             */
            echo "<script>parent.ue_callback('" . $info["url"] . "','" . $info["state"] . "')</script>";
        } else {
            //涂鸦上传，上传方式采用了base64编码模式，所以第三个参数设置为true
            $up = new Uploader("content", $config, true);
            //上传成功后删除临时目录
            if (file_exists($tmpPath)) {
                $this->delDir($tmpPath);
            }
            $info = $up->getFileInfo();
            $info["url"] = str_replace("../../attachments/", '', $info["url"]);
            echo "{'url':'" . $info["url"] . "',state:'" . $info["state"] . "'}";
        }
    }

    /**
     * 删除整个目录
     * @param $dir
     * @return bool
     */
    private function delDir($dir) {
        //先删除目录下的所有文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->delDir($fullpath);
                }
            }
        }
        closedir($dh);
        //删除当前文件夹：
        return rmdir($dir);
    }

    /*
     * 图片管理器，对应imageManager.php
     */

    public function actionImageManager() {
        header("Content-Type: text/html; charset=utf-8");
        error_reporting(E_ERROR | E_WARNING);

        //需要遍历的目录列表，最好使用缩略图地址，否则当网速慢时可能会造成严重的延时
        $path = htmlspecialchars($_POST['dir'], ENT_QUOTES);
        $paths = array('../../' . $path);
        $action = htmlspecialchars($_POST["action"]);
        if ($action == "get") {
            $files = array();
            foreach ($paths as $path) {
                $tmp = $this->getfiles($path);
                if ($tmp) {
                    $files = array_merge($files, $tmp);
                }
            }
            if (!count($files))
                return;
            rsort($files, SORT_STRING);
            $str = "";
            foreach ($files as $file) {
                $file = str_replace($paths, '', $file);
                $str .= $file . "ue_separate_ue";
            }
            echo $str;
        }
    }

    public function actionFileDelete() {
        $action = htmlspecialchars($_POST["action"]);
        $file = htmlspecialchars($_POST["file"]);
        if ($action == 'del') {
            //获取文件的绝对地址
            $path = '';
            if (stripos($file, IMG_DOMAIN) !== false) {
                $path = str_replace(IMG_DOMAIN, dirname(Yii::app()->basePath) . '/uploads', $file);
            } elseif (stripos($file, ATTR_DOMAIN) !== false) {
                $path = str_replace(ATTR_DOMAIN, dirname(Yii::app()->basePath) . '/attachments', $file);
            }

            if (is_file($path) && file_exists($path)) {
                echo unlink($path);
            }
        }
    }

    private function getfiles($path, &$files = array()) {
        if (!is_dir($path))
            return null;
        $handle = opendir($path);
        while (false !== ( $file = readdir($handle) )) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . '/' . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $files);
                } else {
                    if (preg_match("/\.(gif|jpeg|jpg|png|bmp)$/i", $file)) {
                        $files[] = $path2;
                    }
                }
            }
        }
        return $files;
    }

}

?>
