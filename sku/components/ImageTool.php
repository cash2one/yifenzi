<?php

/**
 * 工具类文件
 * 整理一些常用的函数，封装成工具类，方便调用
 * @author qinghao.ye <qinghaoye@sina.com>
 */
class ImageTool {
    /*
     * 对图片进行等比例的缩放
     * 传入图片地址，返回需要的缩略图的地址
     * @param string $path 图片的路径,例如2013\13030300BF1E102F199D46D88112DA7B87E82277.jpg
     * @param string $width 图片的宽度
     * @param string $height 图片的高度
     * @return string   返回图片的url
     */

    public static function resizeImage($path, $maxwidth, $maxheight) {
        //新的图片路径
        $base_path = Yii::getPathOfAlias('att');
        $path = str_replace('/attachments/', '', $path);
        $image = $base_path . DS . $path;  //图片的地址

        if (!file_exists($image)) {
            return '#';
        }
       
        $new_file_name = 'thumb' . DS . $maxwidth . 'x' . $maxheight . DS . $path;
        $image_info = pathinfo($new_file_name);
        $new_dirname = $image_info['dirname'];
        if (!is_dir($new_dirname)) {
            Tool::createDir($new_dirname,Yii::getPathOfAlias('att'));
        }
        $filename = $base_path .DS.'thumb'. DS . $maxwidth . 'x' . $maxheight . DS . $path;    //新图片地址
       
        if (file_exists($filename)) {
             $url = ATTR_DOMAIN .DS. 'thumb'.'/' . $maxwidth . 'x' . $maxheight . '/' . $path;
            $url = str_replace('\\', '/', $url);         
            return $url;
        }
        $img = getimagesize($image);
       
        switch ($img[2]) {
            case 1:
                $im = imagecreatefromgif($image);     //根据不同的格式创建图片对象
                break;
            case 2:
                $im = imagecreatefromjpeg($image);
                break;
            case 3:
                $im = imagecreatefrompng($image);
                break;
            case 6:
                $im = imagecreatefromwbmp($image);
                break;
        }

        $pic_width = imagesx($im);
        $pic_height = imagesy($im);
        
        if (($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
            $resizewidth_tag = false;
            $resizeheight_tag = false;
            if ($maxwidth && $pic_width > $maxwidth) {
                $widthratio = $maxwidth / $pic_width;
                $resizewidth_tag = true;
            }

            if ($maxheight && $pic_height > $maxheight) {
                $heightratio = $maxheight / $pic_height;
                $resizeheight_tag = true;
            }

            if ($resizewidth_tag && $resizeheight_tag) {
                if ($widthratio < $heightratio)
                    $ratio = $widthratio;
                else
                    $ratio = $heightratio;
            }

            if ($resizewidth_tag && !$resizeheight_tag)
                $ratio = $widthratio;
            if ($resizeheight_tag && !$resizewidth_tag)
                $ratio = $heightratio;

            $newwidth = $pic_width * $ratio;
            $newheight = $pic_height * $ratio;
            
            $newim = imagecreatetruecolor($newwidth, $newheight);
//            var_dump($newim);die;
            $color = imagecolorallocate($im, 255, 255, 255);    //设置一个颜色
            imagefill($newim, 0, 0, $color);
//            $dst_x = ($maxwidth - $newwidth) / 2;
//            $dst_y = ($maxheight - $newheight) / 2;
            if (function_exists("imagecopyresampled")) {
                imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            } else {
                imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            }
            $output_fun = self::myOutputImage($img[2]);
//            echo $filename;

            call_user_func($output_fun, $newim, $filename);
  
            imagejpeg($newim, $filename);
            imagedestroy($newim);

            $url = ATTR_DOMAIN .DS. 'thumb'.'/' . $maxwidth . 'x' . $maxheight . $path;
// var_dump($url);die;
            $url = str_replace('\\', '/', $url);
        } else {
            //输出原图
            $url = ATTR_DOMAIN . $path;
        }

        return $url;
    }

    /*
     * 根据图片id得到图片，用于盖网通接口
     * @author:lc
     */

    public static function outPutImageById($path, $maxwidth = 0, $maxheight = 0) {
        $base_path = dirname(Yii::getPathOfAlias('uploads'));
        $image = $base_path . DS . $path;  //图片的地址
        if(!file_exists($image))die('没有此图片');

        $img = getimagesize($image);
        switch ($img[2]) {
            case 1:
                $im = imagecreatefromgif($image);     //根据不同的格式创建图片对象
                header("content-type:image/gif");
                break;
            case 2:
                $im = imagecreatefromjpeg($image);
                header("content-type:image/jpeg");
                break;
            case 3:
                $im = imagecreatefrompng($image);
                header("content-type:image/png");
                break;
            case 6:
                $im = imagecreatefromwbmp($image);
                header("content-type:image/bmp");
                break;
            default:
                die('格式不正确');
                exit;
        }
        if ($maxwidth != 0 && $maxheight != 0) {
            $pic_width = $img[0];
            $pic_height = $img[1];

            if (($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
                $resizewidth_tag = false;
                $resizeheight_tag = false;
                if ($pic_width > $maxwidth) {
                    $widthratio = $maxwidth / $pic_width;
                    $resizewidth_tag = true;
                }

                if ($pic_height > $maxheight) {
                    $heightratio = $maxheight / $pic_height;
                    $resizeheight_tag = true;
                }

                if ($resizewidth_tag && $resizeheight_tag) {
                    if ($widthratio < $heightratio)
                        $ratio = $widthratio;
                    else
                        $ratio = $heightratio;
                }

                if ($resizewidth_tag && !$resizeheight_tag)
                    $ratio = $widthratio;
                if ($resizeheight_tag && !$resizewidth_tag)
                    $ratio = $heightratio;

                $newwidth = $pic_width * $ratio;
                $newheight = $pic_height * $ratio;

                $newim = imagecreatetruecolor($maxwidth, $maxheight);
                if($img[2] == 3){//png 背景透明
                    $color = imagecolorallocatealpha($newim, 0, 0, 0, 127);
                } else {
                    $color = imagecolorallocate($im, 255, 255, 255);    //设置一个颜色
                }
                imagefill($newim, 0, 0, $color);
                $dst_x = ($maxwidth - $newwidth) / 2;
                $dst_y = ($maxheight - $newheight) / 2;
                if (function_exists("imagecopyresampled")) {
                    imagecopyresampled($newim, $im, $dst_x, $dst_y, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
                } else {
                    imagecopyresized($newim, $im, $dst_x, $dst_y, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
                }

                if($img[2] == 3){
                    imagesavealpha($newim, true);//png 背景透明
                }
                $output_fun = self::myOutputImage($img[2]);
                call_user_func($output_fun, $newim);
                imagedestroy($im);
                imagedestroy($newim);
            } else {
                $output_fun = self::myOutputImage($img[2]);
                call_user_func($output_fun, $im);
                imagedestroy($im);
            }
        } else {
            $output_fun = self::myOutputImage($img[2]);
            call_user_func($output_fun, $im);
            imagedestroy($im);
        }
    }

    /*
     * 根据不同格式调用不同方法
     */

    public static function myOutputImage($im_type) {
        $fun = '';
        switch ($im_type) {
            case 1:
                $fun = 'imagegif';
                break;
            case 2:
                $fun = 'imagejpeg';
                break;
            case 3:
                $fun = 'imagepng';
                break;
            case 6:
                $fun = 'imagewbmp';
                break;
        }
        return $fun;
    }

    /**
     * 反解生成略缩图的地址为图片原地址
     * @param unknown_type $path		生成略缩图后的地址
     * @param unknown_type $maxwidth	尺寸宽
     * @param unknown_type $maxheight	尺寸高
     */
    public static function unUrlImageForUploads($path, $maxwidth = 0, $maxheight = 0) {
        if ($maxwidth == 0 && $maxheight == 0) {
            return $path;
        } else {
            return str_replace($maxwidth . 'x' . $maxheight . "/", "", $path);
        }
    }

    /*
     * 返回uploads文件夹中的图片的url或者缩略图的url
     * $maxwidth、$maxheight  不设置就不缩放
     */

    public static function urlImageForUploads($path, $maxwidth = 0, $maxheight = 0) {
        if ($maxwidth == 0 && $maxheight == 0) {
            return IMG_DOMAIN . str_replace('/uploads', '', $path);
        } else {
            return self::resizeImage($path, $maxwidth, $maxheight);
        }
    }

    /**
     * ip转整形，数据库字段必须是无符号型int(11)
     * @param string $ip 要转换的ip
     * @return int
     */
    public static function ip2int($ip = '') {
        if (!$ip)
            return NULL;
        return sprintf("%u", ip2long($ip));
    }

    /*
     * 将ip整形转化成字符串
     */

    public static function int2ip($ip) {
        return long2ip((float) $ip);
    }

    /**
     * 输出图片，点击图片显示真是图片
     * @param string $path	图片路径(可能是略缩图)
     * @param int $maxwidth	宽度
     * @param int $maxheight	高度
     */
    public static function showRealImg($path, $maxwidth = 0, $maxheight = 0) {
        $html = "<a href='" . Tool::urlImageForUploads($path) . "' onclick='return _showBigPic(this)' >";
        if ($maxwidth == 0 && $maxheight == 0) {
            $html.= "<img src='" . Tool::urlImageForUploads($path) . "' />";
        } else {
            $html.= "<img src='" . Tool::urlImageForUploads($path, $maxwidth, $maxheight) . "' />";
        }
        $html.="</a>";
        echo $html;
    }

}
