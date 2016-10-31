<?php
/**
 * 文件上传 公共控制器
 * @author huabin_hong <huabin.hong@gwitdepartment.com>
 */
class CUploadController extends Controller{
	const FILE_NAME = "cache_imgpaths";			//缓存文件名称
	const FILE_UPLOAD_NAME = "Filedata";		//文件上传名称
	const FILE_TMP_PATH = "tmp";				//文件上传临时目录
	const NAMERANDMIN = 1000;					//名称随机尾数最小值
	const NAEMRANDMAX = 9999;					//名称随机尾数最大值
	/**
	 * @var string $url 当前上传地址
	 */
	public $uploadUrl = 'upload/index';
	/**
	 * 显示上传控件
	 */
	public function actionIndex()
	{
		$this->layout = 'upload';
		$height = $_GET['height'];						//上传图片设定的高度
		$width = $_GET['width'];						//上传图片设定的宽度
		$img_format = $_GET['img_format']==""?self::getImgFormat():$_GET['img_format'];;	//图片保存的域
        
		$this->render('application.views.upload.index',array(
			'height' => $height,
			'width' => $width,
			'img_format' => $img_format,
		));
	}
	
	/**
	 * 图片上传的方法
	 */
	public function actionUpload(){
		//处理上传图片的session问题
		if (isset($_POST["PHPSESSID"])) {
			session_id($_POST["PHPSESSID"]);
		} else if (isset($_GET["PHPSESSID"])) {
			session_id($_GET["PHPSESSID"]);
		}
		$upload_height = $_POST['HEIGHT'];
		$upload_width = $_POST['WIDTH'];

        if(!self::valiFileRightSize($upload_height,$upload_width))exit(0);
		               
		if(!self::valiFileSize())exit(0);				//验证尺寸，这里实际上是是否超出php设定大小
		
		if(!self::valiFileUpload())exit(0);				//验证上传
		
        if(!self::valiFileLaw())exit(0);				//验证合法
            
		if(!self::valiFileBig())exit(0);				//验证大小
		
		if(!self::valiFileName())exit(0);				//验证文件名称
		
		$path_info = pathinfo($_FILES[self::FILE_UPLOAD_NAME]['name']);

		$file_extension = $path_info["extension"];
        $save_path = str_replace('\\','/',Yii::getPathOfAlias('uploads').DS.self::FILE_TMP_PATH.DS);  //图片保存的路径

		//创建目录
		if(!UPLOAD_REMOTE && !self::create_folders($save_path)){
			self::HandleError("文件夹创建失败|");
			exit(0);
		}
		
		$fileName = md5(uniqid('',true).rand(self::NAMERANDMIN, self::NAEMRANDMAX)).".".$file_extension;

		if(!UPLOAD_REMOTE && !move_uploaded_file($_FILES[self::FILE_UPLOAD_NAME]["tmp_name"], $save_path.$fileName)) {
			self::HandleError("文件移动失败|");
			exit(0);
	 	}
        if(UPLOAD_REMOTE){
            $ftp = Yii::app()->ftp;
            if(!$ftp->createDir($save_path)) self::HandleError('create dir error');
            $ftp->put($save_path.$fileName,$_FILES[self::FILE_UPLOAD_NAME]["tmp_name"]);
        }
        $randnum = rand(1000, 9999);					//随机码的目的是为了后来删除缓存数据
        self::HandleSuc($randnum."->,".IMG_DOMAIN."/".self::FILE_TMP_PATH."/$fileName|");

        $newCacheData = array(			//本次缓存数据
            'path' => IMG_DOMAIN."/".self::FILE_TMP_PATH.'/'.$fileName,			//临时文件路径(网络路径)
            'localpath' => $save_path.$fileName,
            'randnum' => $randnum
        );
		$uid = $this->getPost('uid');
		//获取缓存数据
		$cache_name = self::FILE_NAME.$uid;
        $cache_data = Yii::app()->fileCache->get($cache_name);				//根据代号获取会员缓存数据
        $cache_data[$randnum] = $newCacheData;								//将新的缓存数据加入
        Yii::app()->fileCache->set($cache_name, $cache_data, 60*30);		//重写缓存
        exit(0);

	}

	/**
	 * 选择图片的时候，如果是选择的是临时图片，那么就进行移动
	 */
	public function actionSure(){
		$imgdata =$_POST['imgdata'];
		$imgarea = $_GET['imgarea'];
		$foldername = $_GET['foldername'];
		$isdate = $_GET['isdate'];
		$imgs = explode("||", $imgdata);

		$imgsData = array();

		$uid = Yii::app()->user->id;
		//获取缓存数据
		$cache_name = self::FILE_NAME.$uid;
		$cache_imgpaths = Yii::app()->fileCache->get($cache_name);

		//移动图片并且保存数据
		foreach ($imgs as $img){		//每个循环对象里面有两个值，图片url和id
			if (empty($img))continue;
			$imgarr = explode(",", $img);
			
			//移动图片
			$imgAreaData = self::getImgArea($imgarea);
			
			$fileNameArr = explode("/", $imgarr[0]);
			$filename = $fileNameArr[count($fileNameArr)-1];
			
			$path = $foldername==""?DS:DS.$foldername;
			$path.= $isdate?DS.date('Y').DS.date('m').DS.date('d').DS:DS;

			//创建文件夹
            if(!UPLOAD_REMOTE){
                self::create_folders($imgAreaData['path'].$path);
            }
			$newpath = str_replace('\\','/',$imgAreaData['path'].$path.$filename);				//	D:/wamp/www/source/uploads/123.jpg		D:/wamp/www/uploads/2013/12/12/123.jpg
            if(UPLOAD_REMOTE){
                $ftp = Yii::app()->ftp;
                if(!$ftp->createDir(dirname($newpath))) self::HandleError('create dir error');
                $ftp->rename($cache_imgpaths[$imgarr[1]]['localpath'],$newpath);
            }else{
                rename($cache_imgpaths[$imgarr[1]]['localpath'], $newpath);
            }
            //清楚对应缓存记录
            unset($cache_imgpaths[$imgarr[1]]);
            Yii::app()->fileCache->set($cache_name, $cache_imgpaths, 60*30);
			$realpath = 
			$imgsData[] = array(
				'path' => str_replace("\\", "/", substr($path.$filename, 1)),
				'realpath' =>  str_replace("\\", "/",$imgAreaData['area'].$path.$filename),
		);
		}
		echo json_encode($imgsData);
	}
	
	/**
	 * 验证是否是指定尺寸
	 * @param int $height
	 * @param int $width
	 */
	public function valiFileRightSize($upload_height,$upload_width){
		$upload_name = self::FILE_UPLOAD_NAME;
		if($upload_height==0&&$upload_width==0)return true;
		list($width,$height,$type,$attr) = getimagesize($_FILES[$upload_name]['tmp_name']);
	 	if($upload_width==$width && $upload_height== $height){
	 		return true;
	  	}
  		self::HandleError("上传图片不是指定尺寸|");
  		return false;
	}
	
	/**
	 * 验证上传
	 */
	public function valiFileUpload(){
		$upload_name = self::FILE_UPLOAD_NAME;
		if (!isset($_FILES[$upload_name])) {
			self::HandleError("没有发现上传 \$_FILES for " . $upload_name . "|");
			return false;
		} else if (isset($_FILES[$upload_name]["error"]) && $_FILES[$upload_name]["error"] != 0) {
			self::HandleError(FileManage::getUploadError($_FILES[$upload_name]["error"]));
			return false;
		} else if (!isset($_FILES[$upload_name]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
			self::HandleError("Upload failed is_uploaded_file test.");
			return false;
		} else if (!isset($_FILES[$upload_name]['name'])) {
			self::HandleError("文件没有名字|");
			return false;
		}
		return true;
	}
	
	/**
	 * 验证文件
	 */
	public function valiFileSize(){
		//检验post的最大上传的大小
		$POST_MAX_SIZE = ini_get('post_max_size');
		$unit = strtoupper(substr($POST_MAX_SIZE, -1));
		$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
	
		if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
			self::HandleError("超过最大允许的尺寸|");
			return false;
		}
		return true;
	}
	
	/**
	 * 验证合法
	 * 当不是一张合法图片时，$width、$height、$type、$attr 的值就全都为空，以此来判断图片的真实
	 */
	public static function valiFileLaw(){
		$upload_name = self::FILE_UPLOAD_NAME;
		list($width,$height,$type,$attr) = getimagesize($_FILES[$upload_name]['tmp_name']);
	 	if(empty($width) || empty($height) || empty($type) || empty($attr)){
	  		self::HandleError("上传图片为非法内容|");
	  		return false;
	  	}
	  	return true;
	}
	
	/**
	 * 验证大小
	 * 警告:最大的文件支持这个代码2 GB
	 */
	public static function valiFileBig(){
		$upload_name = self::FILE_UPLOAD_NAME;
		$max_file_size_in_bytes = 2147483647;				// 2GB in bytes 最大上传的文件大小为2G
		$file_size = @filesize($_FILES[$upload_name]["tmp_name"]);
		if (!$file_size || $file_size > $max_file_size_in_bytes) {
			self::HandleError("超过最高允许的文件的大小|");
			return false;
		}
		
		if ($file_size <= 0) {
			self::HandleError("超出文件的最小大小|");
			return false;
		}
		return true;
	}
	
	/**
	 * 验证文件名称
	 */
	public static function valiFileName(){
		$upload_name = self::FILE_UPLOAD_NAME;
		$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-'; //允许在文件名字符(在一个正则表达式格式)
		$MAX_FILENAME_LENGTH = 260;
		$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES[$upload_name]['name']));
		if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
			self::HandleError("无效的文件|");
			return false;
		}	
		return true;
	}
	
	/**
	 * 判断是否存在目录，不存在递归创建目录
	 */
	public static function create_folders($dir){ 
		return is_dir($dir) or (self::create_folders(dirname($dir)) and mkdir($dir, 0777));
	}
     
	/**
	 * 输出失败信息
	 * @param string $message
	 */
	public static function HandleError($message) {
		echo "fai:$message";  
	}
	
	/**
	 * 输出信息
	 * @param string $message
	 */
	public static function HandleSuc($message) {
		echo "suc:$message";  
	}
	
	/**
	 * 获取能够上传的文件扩展名
	 */
	public static function getImgFormat(){
		return "*.jpg;*.jpeg;*.gif;*.png";
	}
	
	/**
	 * 定义图片显示域以及位置
	 */
	public static function getImgArea($key){
		$imgAreaArr = array(
			1 => array(
				'area' => IMG_DOMAIN,
				'path' => UPLOAD_REMOTE ? UPLOAD_REMOTE.'uploads' : Yii::getPathOfAlias('uploads'),
			),
			2 => array(
				'area' => ATTR_DOMAIN,
				'path' => UPLOAD_REMOTE ? UPLOAD_REMOTE.'attachments' :  Yii::getPathOfAlias('att'),
			),
		);
		return $imgAreaArr[$key];
	}
}
