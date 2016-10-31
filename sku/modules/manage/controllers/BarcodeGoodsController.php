<?php

class BarcodeGoodsController extends MController {
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
//	public $layout='//layouts/column2';

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete'),
                'users' => array('admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * 创建条码
     */
    public function actionCreate() {
        $model = new BarcodeGoods;
        $imgModel = new BarcodeGoodsPicture();
        $this->performAjaxValidation($model);
        $model->scenario = 'create';
        if (isset($_POST['BarcodeGoods'])) {
            $model->attributes = $_POST['BarcodeGoods'];
            $saveDir = 'barcode' . '/' . date('Y/m/d');
            $model = UploadedFile::uploadFile($model, 'thumb', $saveDir);  // 上传图片
            if ($model->save()) {
                UploadedFile::saveFile('thumb', $model->thumb);  // 保存图片
                
                /**
                 * 修改商品图片列表数据保存 goods_picture
                 */
                $imgList = explode('|', $_POST['BarcodeGoodsPicture']['path']);
                $model->addGoodsPicture($imgList);
                
                SystemLog::record($this->getUser()->name . "创建商品条形码：" . $model->name);
                $this->setFlash('success', Yii::t('barcodeGoods', '保存成功'));
                $this->redirect(array('admin'));
            }
        }

        $this->render('create', array(
            'model' => $model,
        	'imgModel' => $imgModel,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $old_file = $model->thumb;
        $this->performAjaxValidation($model);
        $model->scenario = 'update';
        if (isset($_POST['BarcodeGoods'])) {
            $model->attributes = $_POST['BarcodeGoods'];
            $saveDir = 'barcode' . '/' . date('Y/m/d');
            $model = UploadedFile::uploadFile($model, 'thumb', $saveDir,Yii::getPathOfAlias('att'),$old_file);  // 上传图片
            if ($model->save()) {
                UploadedFile::saveFile('thumb', $model->thumb,$old_file,true);  // 保存图片
                
                /**
                 * 修改商品图片列表数据保存 goods_picture
                 */
                $imgList = explode('|', $_POST['BarcodeGoodsPicture']['path']);
                if ($model->pic != $_POST['BarcodeGoodsPicture']['path']) {
                	BarcodeGoodsPicture::model()->deleteAllByAttributes(array('goods_id' => $id)); //删除旧的图片
                	$model->addGoodsPicture($imgList);
                	$oldPicArr = explode('|', $model->pic);
                	//旧的图片
                	foreach ($oldPicArr as $v) {
                		if (!in_array($v, $imgList)) {
                			$deleteImg[] = $v;
                		}
                	}
                }
                
                //删除旧的图片
                if (!empty($deleteImg)) {
                	foreach ($deleteImg as $v) {
                		@UploadedFile::delete(Yii::getPathOfAlias('uploads') . '/' . $v);
                	}
                }
                
                SystemLog::record($this->getUser()->name . "编辑商品条形码：" . $model->name);
                $this->setFlash('success', Yii::t('barcodeGoods', '保存成功'));
                $this->redirect(array('admin'));
            }
        }

        $model->pic = array();
        foreach ($model->goodsPicture as $p) {
        	$model->pic[] = $p->path;
        }
        $model->pic = implode('|', $model->pic);
        $imgModel = new BarcodeGoodsPicture;
        $imgModel->path = $model->pic;
        
        $this->render('update', array(
            'model' => $model,
        	'imgModel' => $imgModel,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $model = $this->loadModel($id);
        if ($model->delete()) {
            SystemLog::record($this->getUser()->name . "删除商品条形码" . $model->name);
            $this->setFlash('success', Yii::t('barcodeGoods', '成功删除'));
        }

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('SkuBarcodeGoods');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * 条形码列表
     */
    public function actionAdmin() {
//		$model=new BarcodeGoods('search');
        $model = new BarcodeGoods;
        $model->unsetAttributes();  // clear any default values

        if (isset($_GET['BarcodeGoods']))
            $model->attributes = $_GET['BarcodeGoods'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return SkuBarcodeGoods the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = BarcodeGoods::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * 获取excel数据，先写import_member_log，再member开户，再 import_member_cash 充值
     */
    public function actionExcelImport() {
        @ini_set('memory_limit', '2048M');
        set_time_limit(0);
        $this->breadcrumbs = array(Yii::t('barcodeGoods', '商品管理 '), Yii::t('barcodeGoods', '条码库商品导入'));
        $model = new UploadForm('excel');
        $this->performAjaxValidation($model);
        $result = array(); // 数据插入结果

        if (isset($_POST['UploadForm'])) {

            $model->attributes = $_POST['UploadForm'];
            $dir = Yii::getPathOfAlias('att');
            $fileName = $_FILES['UploadForm']['name']['file'];
            $model = UploadedFile::uploadFile($model, 'file', 'zip', $dir, pathinfo($fileName, PATHINFO_FILENAME));

            if ($model->validate()) {
                UploadedFile::_saveFile('file', $model->file);
                $dirInfo = pathinfo($model->file);

                //引入PclZip
                require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel/Shared/PCLZip/pclzip.lib.php';
                $archive = new PclZip($dir . '/zip/' . $dirInfo['basename']);
                $zipDir = $dir . '/zip/' . date("Y/m/d/h/i/s");  //压缩包解压路径

                $list = $archive->extract(PCLZIP_OPT_PATH, $zipDir, PCLZIP_OPT_REMOVE_PATH, 'install/release');

                if ($list) {
                    $excelFile = array();
                    $tmpArray = array();
                    
                    $xls=array();
                    $xlsx=array();
                    $imgs = array();
                    foreach ($list as $k=>$v){
                    	if (preg_match('#.*\.{1}xls#', $v['filename'])) {
                    		$xls[] = $v['filename'];
                    	}
                    	
                    	elseif (preg_match('#.*\.{1}xlsx#', $v['filename'])) {
                    		$xlsx[] = $v['filename'];
                                 
                    	}
                    	
                    	elseif (preg_match('#.*\.[jpg|png]#i', $v['filename'])) {
                    		$imgs[] = $v['filename'];
                    	}
                    	
                    }
                    
//                     $xls = glob($zipDir . '*.xls');  //xls格式文件
//                     $xlsx = glob($zipDir . '*.xlsx'); //xlsxg格式文件
                    
//                     print_r($imgs);exit();

                    $excelFile = array_merge($xls, $xlsx);
                    if (!empty($excelFile)) {
                        //引入phpExcel
                        require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel/Shared/String.php';
                        require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel.php';
                        Yii::registerAutoloader(array('PHPExcel_Autoloader', 'Register'), true);
                        
                        foreach ($excelFile as $v) {
                            $excel = PHPExcel_IOFactory::load($v);
                            $excel->setActiveSheetIndex(0);
                            $objWorksheet = $excel->getActiveSheet();
                            $highestRow = $objWorksheet->getHighestRow(); // 取得总行数
                            $highestColumn = array('barcode', 'name', 'model', 'default_price', 'unit','cate_name','brand','imgs');
                            $excelData = array(); //excel 数据
                            for ($row = 2; $row <= $highestRow; $row++) {
                                foreach ($highestColumn as $k => $v) {
                                    $value = $objWorksheet->getCellByColumnAndRow($k, $row)->getValue();
                                    $excelData[$row - 2][$v] = trim(is_object($value) ? $value->getPlainText() : $value);
                                }

                                if(empty($excelData[$row - 2]['barcode']) && empty($excelData[$row - 2]['name']) && empty($excelData[$row - 2]['model']) &&empty($excelData[$row - 2]['default_price']) && empty($excelData[$row - 2]['unit'])){
                                    unset($excelData[$row - 2]);
                                }elseif(empty($excelData[$row - 2]['barcode'])){
                                    $excelData[$row - 2]['thumb'] = '';
                                    $excelData[$row - 2]['status'] = 1;
                                    $excelData[$row - 2]['mark'] = '条形码不能为空';
                                }else{
                                    $type = $this->_photoExist($excelData[$row - 2]['barcode'], $imgs);  //判断图片是否存在和图片格式
                                    if ($type) {
                                        $excelData[$row - 2]['thumb'] = 'barcode/' . date("Y/m/d", time()) . '/' . $type;
                                        
                                    } else {
//                                         $excelData[$row - 2]['thumb'] = '';
//                                         $excelData[$row - 2]['status'] = 1;
//                                         $excelData[$row - 2]['mark'] = '图片不存在，只支持JPG或PGN格式';
                                        $excelData[$row - 2]['thumb'] ='';
                                    }
                                    $excelData[$row - 2]['status'] = 0;
                                    $excelData[$row - 2]['mark'] = '';
                                }
                            }

                            if (!empty($excelData)) {
                                foreach ($excelData as $v) {
                                    $rs = $this->_insertBarcode($v);

                                    @$moveRs = $this->_movePhotos($rs['barcode'], $imgs);
//                                     if (!isset($rs['status'] ) || !$rs['status']) {
//                                         $moveRs = $this->_movePhotos($rs['barcode'], $imgs);
//                                         $rs['mark'] = $moveRs ? '' : '图片不存在，只支持JPG或PGN格式';
//                                     }
                                    $tmpArray[] = $rs;
                                }
                                if (!empty($tmpArray)) {
                                    $result = array_merge($result, $tmpArray);
                                    unset($tmpArray);
                                }
                            } else {
                                $this->setFlash('error', 'exel文件没有数据');
                            }
                        }

                        @SystemLog::record(Yii::app()->user->name . "导入条形码 成功");
                    } else {
                        $this->setFlash('error', '不存在exel文件');
                    }
                } else {
//                    die("Error : ".$archive->errorInfo(true));
                    $this->setFlash('error', '解压失败');
                }
            } else {
                @SystemLog::record(Yii::app()->user->name . "导入会条形码文件失败");
                $this->setFlash('error', '上传文件失败');
            }
        }

        $this->render('index', array('model' => $model, 'result' => $result));
    }

    /**
     *
     * @param array $data
     * @param $id
     */
    private function _insertBarcode(Array $data) {
//         if($data['status'] == 1){//图片不存在直接返回数据
//             return $data;
//         }

        $check = $this->_checkBarcode($data);

        if (!empty($check)) {
            if($check =='已覆盖'){
                 $data['status'] = 0;
                 $data['mark'] = $check;
                 return $data;
            }else{
            $data['status'] = 1;
            $data['mark'] = $check;
            return $data;
            }
        } else {
            $trans = Yii::app()->db->beginTransaction();
            try {
                Yii::app()->db->createCommand()->insert('{{barcode_goods}}', array(
                    'barcode' => $data['barcode'],
                    'name' => $data['name'],
                    'default_price' => $data['default_price'],
                    'thumb' => isset($data['thumb'])?$data['thumb']:'',
                    'model' => $data['model'],
                    'unit' => $data['unit'],
                    'cate_name' => $data['cate_name'],
                    'brand' => $data['brand'],
                    'create_time' => time(),
                    'is_custom'=>  BarcodeGoods::EN_CUSTOM,
                    'status'=>''
                ));
                $goods_id = Yii::app()->db->getLastInsertID();
//                 var_dump($data['imgs']);exit();
                $imgs = explode(',', $data['imgs']);
                if (!empty($imgs)) {
                	foreach ($imgs as $img){
                		$img_model = new BarcodeGoodsPicture();
                		$img_model->goods_id = $goods_id;
                		$img_model->path = 'barcode/imgs/'.$img;
                		$img_model->save();
                	}
                }
                
                $trans->commit();
            } catch (Exception $e) {
                $trans->rollback();
                $data['status'] = 1;
                $data['mark'] = $e->getMessage();
            }
        }
        return $data;
    }

    /**
     * 检查数据的合法性
     * @param array $data
     * @return string
     */
    private function _checkBarcode(array $data) {
        $msg = '';

        if (empty($msg) && empty($data['barcode'])) {
            $msg = '条形码不能为空';
        }
//         if (empty($msg) && !preg_match("/^[0-9]\d*$/",$data['barcode'])) {
//             $msg = '条形码必须为整数类型';
//         }
        if (empty($msg) && strlen($data['barcode']) <> 13) {
            $msg = '条形码长度必须为13位';
        }
           if (empty($msg) && $this->_cover($data)=='已覆盖') {
            $msg = '已覆盖';
        }
         if (empty($msg) && $this->_cover($data)=='已存在') {
            $msg = '条码已存在';
        }
//        if (empty($msg) && $this->_exists($data['barcode'])) {
//            $msg = '条形码已经存在';
//        }
//         if (empty($msg) && !preg_match('/[\x{4e00}-\x{9fa5}\w]+$/u', $data['name'])) {
//             $msg = '名称只能是数字、字母、中文和下划线，并不能为空';
//         }
//         if (empty($msg)) {
//             preg_match_all("/./us", $data['name'], $match);
//             if(count($match[0]) > 15){
//                 $msg = '商品名称长度不能大于15';
//             }
//         }
//         if(empty($msg)){
//             preg_match_all("/./us",$data['model'],$match);
//             if(count($match[0]) > 10){
//                 $msg = '规格长度不能大于10';
//             }
//         }
        if (empty($msg) && !is_numeric($data['default_price'])) {
            $msg = '价格必须为数字类型';
        }
        if (empty($msg) && $data['default_price'] < 0) {
            $msg = '价格不能小于0';
        }
        if (empty($msg) && empty($data['unit'])) {
            $msg = '单位不能为空';
        }
        if (empty($msg)) {
            preg_match_all("/./us",$data['unit'],$match);
            if(count($match[0]) > 5){
                $msg = '单位长度不能大于5';
            }
        }
//        if(empty($msg) && empty($data['store'])){
//            $msg = '店铺不能为空';
//        }
//        if(empty($msg) && empty($data['outlets'])){
//            $msg = '网点不能为空';
//        }
   
        return $msg;
    }

    /*
     * 检查条形码是否存在
     */

    private function _exists($barcode) {
        $model = BarcodeGoods::model()->findByAttributes(array('barcode' => $barcode));
        if ($model) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 移动图片
     */

    private function _movePhotos($num, $imgs) {
        $newDir = Yii::getPathOfAlias('att') . '/barcode/' . date("Y/m/d", time());
        
        $old = '';
        $type = '';
        foreach ($imgs as $img){
        	preg_match('#'.$num.'\.([jpg|png]+)$#i', $img,$match);
        	if ($match) {
        		$type =  $match[0];
        		$old = $img;
        		break;
        	}
        		
        }
        
        $new = $newDir . '/'  . $type;

        if (isset($match[0])) {
            //如果配置了远程图片服务器目录，则ftp上传到远程图片服务器
            if (UPLOAD_REMOTE) {
                UploadedFile::_movePhotos('file', $num . $type);
                return true;
            } else {
                !is_dir($newDir) ? $this->_mkdirs($newDir) : '';  //目录不存在则创建目录
                rename($old, $new);  //移动文件

                //压缩图片
                @Tool::resize_pic($new);
                
                return true;
            }
        } else {
            return false;
        }
    }
    

    /*
     * 判断图片是否存在,返回图片名称带格式
     */

    private function _photoExist($num, $imgs) {

		foreach ($imgs as $img){
			preg_match('#'.$num.'\.([jpg|png]+)$#i', $img,$match);
			if ($match) {
				return $match[0];
			}
			
		}
    	
    }

    /*
     * 创建目录
     */

    private function _mkdirs($dir) {
        if (!is_dir($dir)) {
            if (!$this->_mkdirs(dirname($dir))) {
                return false;
            }
            if (!UploadedFile::mkdir($dir)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 导出excel
     * @param string $file
     */
    public function actionTemplate() {
//        $data = CJSON::decode($data);
        //引入phpExcel
        require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel/Shared/String.php';
        require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel.php';
        Yii::registerAutoloader(array('PHPExcel_Autoloader', 'Register'), true);
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue('A1', '条形码')
                ->setCellValue('B1', '商品名称')
                ->setCellValue('C1', '规格')
                ->setCellValue('D1', '单价')
                ->setCellValue('E1', '单位')
        		->setCellValue('F1', '分类')
        		->setCellValue('G1', '品牌')
        		->setCellValue('H1', '大图列表，用英文逗号（,）隔开路径+文件名。');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="barcodeGoods.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        @SystemLog::record(Yii::app()->user->name . "导出条形码成功");
        unset($objPHPExcel, $objWriter);
    }
 /*
     * 检查条形码是否存在,存在null的则覆盖
     */

    private function _cover($data) {
        $model = BarcodeGoods::model()->findByAttributes(array('barcode' => $data['barcode']));
        $sore = 0;
        if ($model) {
            if (empty($model['name']) && !empty($data['name'])) {
                $model['name'] = $data['name'];
                 $sore++;
            }
            elseif (empty($model['barcode']) && !empty($data['barcode'])) {
                $model['barcode'] = $data['barcode'];
                $sore++;
            }
            elseif (empty($model['default_price']) && !empty($data['default_price'])) {
                $model['default_price'] = isset($data['default_price'])?$data['default_price']:'';
                $sore++;
            }
            elseif (empty($model['thumb']) && !empty($data['thumb'])) {
                $model['thumb'] = isset($data['thumb'])?$data['thumb']:'';
                $sore++;
            }
            elseif (empty($model['model']) && !empty($data['model'])) {
                $model['model'] = $data['model'];
                $sore++;
            }
            elseif (empty($model['unit']) && !empty($data['unit'])) {
                $model['unit'] = $data['unit'];
                $sore++;
            }
            elseif (empty($model['cate_name']) && !empty($data['cate_name'])) {
                $model['cate_name'] = $data['cate_name'];
                $sore++;
            }
            elseif (empty($model['brand']) &&!empty($data['brand'])) {
                $model['brand'] = $data['brand'];
                $sore++;
            }
            if ($sore>0) {
                if ($model->update()) {
                    return '已覆盖';
                }
            } else {
                return '已存在';
            }
        } else {
            return false;
        }
    }

}
