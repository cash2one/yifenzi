<?php

/**
 * 文件上传
 * @author jianlin_lin <hayeslam@163.com>
 * @example 
 * // 上传
 * $model = UploadedFile::uploadFile($model, 'thumbnail', 'logo'); // 处理上传的文件
 * UploadedFile::saveFile('thumbnail', $model->thumbnail); // 上传
 * 
 * // 更新
 * $oldFile = $model->thumbnail;
 * $model = UploadedFile::uploadFile($model, 'thumbnail', 'logo'); // 处理上传的文件
 * UploadedFile::saveFile('thumbnail', $model->thumbnail, $oldFile, true); 
 * 
 */
class UploadedFile extends CUploadedFile {

    // 上传文件属性
    static private $uploadFiles = array();
    // 保存路径属性
    static private $savePath;

    /**
     * 文件上传
     * @param object $model     AR对象
     * @param string $fileField AR对象中的文件字段
     * @param string $saveDir   保存目录可递归形式 具体查看 Tool::createDir() 方法
     * @param string $savePath  保存路径默认或路径不存在则使用 Yii::getPathOfAlias('att')
     * @param string $fileName  文件名称，默认系统生成唯一的一串字符
     * @param string $name   the name of the file input field. 当文件上传于与 AR对象中的文件字段 不一样时候使用
     * @return object
     */
    public static function uploadFile($model, $fileField, $saveDir = 'files', $savePath = null, $old_path=null,$fileName = null,$name=null) {
    	$model_name = get_class($model);
    	if (empty($_FILES[$model_name]['name'][$fileField])){
    		$model->$fileField = $old_path;
    		return $model;
    	}

    	
        if (!isset(self::$uploadFiles[$fileField]) && empty($name)){
            self::$uploadFiles[$fileField] = CUploadedFile::getInstance($model, $fileField);
        }

        if($name){
            self::$uploadFiles[$fileField] = CUploadedFile::getInstanceByName($name);
        }
        
        if (self::$uploadFiles[$fileField] !== null) {

            self::$savePath[$fileField] = is_null($savePath) ? Yii::getPathOfAlias('att') : $savePath;
            if (is_null($fileName))
                $fileName = Tool::generateSalt() . '.' . self::$uploadFiles[$fileField]->getExtensionName();
            else
                $fileName = $fileName . '.' . self::$uploadFiles[$fileField]->getExtensionName();
            $model->$fileField = $saveDir . '/' . $fileName;

            //压缩图片
            $img_path = self::$savePath[$fileField].DS.$model->$fileField;
            @Tool::resize_pic($img_path);
            
        }
        return $model;
    }

    /**
     * 保存文件
     * @param string $fileField AR对象中的文件字段
     * @param string $file      涵相对路径文件名称(一般为入库名称)
     * @param string $oldFile   旧文件路径，$isUpdate必须为true生效
     * @param boolean $isUpdate 是否更新，为true则执行删除旧文件
     * @return boolean
     * @throws Exception
     */
    public static function saveFile($fileField, $file, $oldFile = null, $isUpdate = false) {
        $dir = pathinfo($file, PATHINFO_DIRNAME);
        $savePath = isset(self::$savePath[$fileField]) ? self::$savePath[$fileField] : null;
        /** @var CUploadedFile $CUploadedFile */
        $CUploadedFile = isset(self::$uploadFiles[$fileField]) ? self::$uploadFiles[$fileField] : null;
        if ($CUploadedFile !== null) {
            //如果配置了远程图片服务器目录，则ftp上传到远程图片服务器
            if(UPLOAD_REMOTE){
                $ftp = Yii::app()->ftp;
                $fullPathFile = $savePath.'/'.$file;
                if(!$ftp->createDir(dirname($fullPathFile))){
                    throw new Exception('create dir error');
                }
                $ftp->put($fullPathFile,$CUploadedFile->tempName);
                if ($isUpdate === true)
                     @$ftp->delete(UPLOAD_REMOTE. $oldFile); // 删除旧文件
                
                
                //压缩图片
                @Tool::resize_pic($fullPathFile);
                
                return true;
            }else{
                Tool::createDir($dir, $savePath);
                $uploadResult = $CUploadedFile->saveAs($savePath . DS . $file); // 保存新文件
                if(!$uploadResult){
                    throw new Exception('save file error');
                }
                if ($isUpdate === true)
                    @unlink($savePath . DS . $oldFile); // 删除旧文件
                
                
                //压缩图片
                @Tool::resize_pic($savePath . DS . $file);
                
                return true;
            }
        }
        return false;
    }

    /**
     * 删除文件
     *
     * @param string $file
     * @return bool
     */
    public static function delete($file){
        $file = str_replace('\\','/',$file);
        if(UPLOAD_REMOTE){
            return @Yii::app()->ftp->delete($file);
        }else{
            return @unlink($file);
        }
    }

    /**
     * 创建目录
     * @param string $dir
     * @return bool
     */
    public static function mkdir($dir){
        $dir = str_replace('\\','/',$dir);
        if(UPLOAD_REMOTE){
            return Yii::app()->ftp->createDir($dir);
        }else{
            $result = mkdir($dir);
            chmod($dir, 0777);
            return $result;
        }
    }

    /**
     * 判断文件是否存在
     * @param $file
     * @return bool
     */
    public static function file_exists($file)
    {
        $file = str_replace('\\','/',$file);
        if(UPLOAD_REMOTE){
            return Yii::app()->ftp->size($file) > 0 ? true : false;
        }else{
            return file_exists($file);
        }
    }

    /**
     * 保存文件，用于条形码压缩包文件上传
     * @param string $fileField AR对象中的文件字段
     * @param string $file      涵相对路径文件名称(一般为入库名称)
     * @param string $oldFile   旧文件路径，$isUpdate必须为true生效
     * @param boolean $isUpdate 是否更新，为true则执行删除旧文件
     * @return boolean
     * @throws Exception
     */
    public static function _saveFile($fileField, $file, $oldFile = null, $isUpdate = false) {
        $dir = pathinfo($file, PATHINFO_DIRNAME);
        $savePath = isset(self::$savePath[$fileField]) ? self::$savePath[$fileField] : null;
        /** @var CUploadedFile $CUploadedFile */
        $CUploadedFile = isset(UploadedFile::$uploadFiles[$fileField]) ? UploadedFile::$uploadFiles[$fileField] : null;
        if ($CUploadedFile !== null) {
            Tool::createDir($dir, $savePath);
            $uploadResult = $CUploadedFile->saveAs($savePath . DS . $file); // 保存新文件
            if(!$uploadResult){
                throw new Exception('save file error');
            }
            if ($isUpdate === true)
                @unlink($savePath . DS . $oldFile); // 删除旧文件
            return true;
        }
        return false;
    }

    /*
     * ftp上传图片，用于条形码
     */
    public static function _movePhotos($fileField, $file, $oldFile = null, $isUpdate = false){
        $ftp = Yii::app()->ftp;
        $fullPathFile = Yii::getPathOfAlias('att') . '/barcode/' . date("Y/m/d",time()) .'/'. $file;
        $CUploadedFile = isset(UploadedFile::$uploadFiles[$fileField]) ? UploadedFile::$uploadFiles[$fileField] : null;
        if($CUploadedFile != null){
            if(!$ftp->createDir(dirname($fullPathFile))){
                throw new Exception('create dir error');
            }
            $ftp->put($fullPathFile,$oldFile);
            if ($isUpdate === true)
                @$ftp->delete(UPLOAD_REMOTE. $oldFile); // 删除旧文件
            
            @Tool::resize_pic($fullPathFile);
            
            return true;
        }
        return false;
    }
    /**
     * 上传普通文件，必须是完整路径
     * @param string $local  本地文件
     * @param string $remote 上传路径
     * @param string $deleteDir 删除文件路径
     * @return boolean
     */
    public static function upload_file($local,$remote,$deleteDir='',$pathAlias = 'att')
    {
        if($local && $remote){
            if (UPLOAD_REMOTE) {
                $ftp = Yii::app()->ftp;
                $remote = Yii::getPathOfAlias($pathAlias).'/'.$remote;
                if(!$ftp->createDir(dirname($remote))) {
                    throw new Exception('create dir error');
                }
                if (!empty($deleteDir)) {
                    @$ftp->delete($deleteDir);
                }
                $ftp->put($remote,$local);
            }else {
                $path = str_replace('\\', '/', Yii::getPathOfAlias($pathAlias));
                $filename = $path . '/' . $remote;
                Tool::createDir(dirname($remote),$path);
                if(!move_uploaded_file($local, $filename)){
                    throw new Exception('保存文件失败');
                }
                if (!empty($deleteDir)) {
                    @unlink($deleteDir);
                }
            }
            return true;
        }
        return false;
    }


}
