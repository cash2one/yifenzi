<?php



class  IndexController extends Controller{
    public function actionShoot(){  
        $this->layout = false;
            if($_FILES){       
                var_dump($_FILES);
                $data = yii::app()->uploadReceive->receive($_FILES['upload'],'/upload/cutimg/');  
                echo json_encode($data);  
            }else{  
                $this->render('shoot');  
            }  
}  
  
public function actionCutimg(){  
        $filename = $_POST['name'];  
        $file = $_SERVER['DOCUMENT_ROOT'].'upload/cutimg/'.$filename;  
        //裁剪后的图片路径  
        $cutPicfolder = '/upload/cutimg/';  
        $cutPicPath = $_SERVER['DOCUMENT_ROOT'].$cutPicfolder;  
  
        $urlPath = yii::app()->uploadReceive->get_current_url();  
        //$urlPath = self::_get_current_url();   
        $urlPath = rtrim($urlPath,'/').'/';  
        $x1 = $_POST['offsetLeft'];  
        $y1 = $_POST['offsetTop'];  
        $width = $_POST['width'];  
        $height = $_POST['height'];  
  
        $type = exif_imagetype($file);  
        $support_type=array(IMAGETYPE_JPEG , IMAGETYPE_PNG , IMAGETYPE_GIF);  
  
        if(!in_array($type, $support_type,true)) {  
            $data['status'] = 0;  
            $data['info'] =  "不支持的格式！";  
            echo json_encode($data);  
            exit;  
        }else{  
            switch($type) {  
            case IMAGETYPE_JPEG :  
                $image = imagecreatefromjpeg($file);  
                break;  
            case IMAGETYPE_PNG :  
                $image = imagecreatefrompng($file);  
                break;  
            case IMAGETYPE_GIF :  
                $image = imagecreatefromgif($file);  
                break;  
            default:  
                $data['status'] = 0;  
                $data['info'] =  "不支持的格式！";  
  
                echo json_encode($data);  
                exit;  
            }  
  
            //图片裁剪  
            $copy = yii::app()->UploadReceive->PIPHP_ImageCrop($image, $x1, $y1, $width, $height);  
            //$copy = self::_PIPHP_ImageCrop($image, $x1, $y1, $width, $height);  
            $newName = 'cut_'.$filename;  
            $targetPic = $cutPicPath.$newName;  
  
            //TODO 目录与写文件检测  
            if(false === imagejpeg($copy, $targetPic) ){  
                $data['status'] = 0;  
                $data['info'] =  "生成裁剪图片失败！请确认保存路径存在且可写！";  
                echo json_encode($data);  
                exit;  
            }   
  
            @unlink($file);  
  
            $data['status'] = 1;  
            $data['path'] = $cutPicfolder.$newName;  
            $data['name'] = $newName;  
            $data['url']  = $urlPath.$data['path'];  
  
            echo json_encode($data);  
            exit;  
  
        }  
}  

}

