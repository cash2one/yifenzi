<?php

/**
 * 商品分类管理控制器
 * 操作(删除，修改，列表)
 * @author leo8705
 */
class GoodsController extends MController {

    public function filters() {
        return array(
            'rights',
        );
    }
    
   	public function allowedActions(){
   		return   'importTemplate';
   	}
    
   // 列表
    public function actionAdmin() {
        $model = new Goods('search');
         //获取商品分类
        $goodscategory = $model->goodsCategory;
        $model->unsetAttributes();
        if (isset($_GET['Goods']))
            $model->attributes = $_GET['Goods'];
   

        $this->render('admin', array(
            'model' => $model,
           'goodscategory'=>$goodscategory
        ));
    }
    
    /*
     * 编辑页
     */
    public function actionUpdate($id){
        $model = $this->loadModel($id);
        //获取商品分类
//        $goodscategory = $model->goodsCategory;
        
        //获取超市商品表
        $supergoods = $model->superGoods;
        $supermarkets = array();
         foreach($supergoods as $k=>$v){
              $supermarkets[$k] = $v->super;
        }
        
        $cate = Category::model()->findByPk($model->source_cate_id);
        
        $this->performAjaxValidation($model);
        if(isset($_POST['Goods'])){
            $oldThumbnail = $model->thumb;
            $data = $_POST['Goods'];
            $model->attributes=$data;    
            $model->name = trim($model->name);
          if($model->save()){
              @SystemLog::record(Yii::app()->user->name . "编辑商品成功：" . $model->name);
              
              /**
               * 修改商品图片列表数据保存 goods_picture
               */
              $imgList = explode('|', $_POST['GoodsPicture']['path']);
              if ($model->pic != $_POST['GoodsPicture']['path']) {
              	GoodsPicture::model()->deleteAllByAttributes(array('goods_id' => $id)); //删除旧的图片
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
              
              
             $this->setFlash('success', Yii::t('Goods', '编辑商品') . $model->name . Yii::t('Goods', '成功'));
             $this->redirect(array('admin'));
          }
        }
        $model->pic = array();
        foreach ($model->goodsPicture as $p) {
        	$model->pic[] = $p->path;
        }
        $model->pic = implode('|', $model->pic);
        $imgModel = new GoodsPicture;
        $imgModel->path = $model->pic;

        $this->render('update', array(
            'model' => $model,
        	'imgModel' => $imgModel,
        	'cate'=>$cate,
        ));
        
    }
    
    /**
     * 审核商品
     */
    public function actionApply($id){
          $model = $this->loadModel($id);
        if ($this->getParam('apply') == 'pass') {
            $model->status = Goods::STATUS_PASS;
            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name . "商品审核通过：" . $model->name);
                $this->setFlash('success', Yii::t('goods', '商品审核通过：') . $model->name);
                $this->redirect(array('/goods/admin'));
            }
        } 
        if ($this->getParam('apply') == 'unpass') {
            $model->status = Goods::STATUS_NOPASS;
            $super_goods = SuperGoods::model()->findAll('goods_id=:id',array(':id'=>$id));
            $machine_goods = VendingMachineGoods::model()->findAll('goods_id=:id',array(':id'=>$id));
  
            isset($machine_goods)?$machine_goods:'';
            isset($super_goods)?$super_goods:'';
;
            if(!empty($super_goods)){
                foreach($super_goods as $v){
               $v['status'] = SuperGoods::STATUS_DISABLE;
               $v->save();
                }
            }
            if(!empty($machine_goods)){
                 foreach($machine_goods as $v){
                    $v['status']=  VendingMachineGoods::STATUS_DISABLE;      
                    $v->save();
                 }
            }
//            var_dump($model->attributes);die;
            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name . "商品审核不通过：" . $model->name);
                $this->setFlash('success', Yii::t('goods', '商品审核不通过：') . $model->name);
                $this->redirect(array('/goods/admin'));
            }
        }
        
        $cate = Category::model()->findByPk($model->source_cate_id);
        
        $this->render('apply', array('model' => $model,'cate'=>$cate,));
    
    }
    
    
    
    
    
    
    /**
     * 获取excel数据
     */
    public function actionExcelImport() {
    	@ini_set('memory_limit', '2048M');
    	set_time_limit(0);
    	$this->breadcrumbs = array(Yii::t('barcodeGoods', '商品管理 '), Yii::t('barcodeGoods', '商家商品导入'));
    	$model = new UploadForm('excel');
    	$this->performAjaxValidation($model);
    	$result = array(); // 数据插入结果
    
    	if (isset($_POST['UploadForm'])) {
    
    		$model->attributes = $_POST['UploadForm'];
    		$dir = Yii::getPathOfAlias('cache');
    		$fileName = $_FILES['UploadForm']['name']['file'];
    		$model = UploadedFile::uploadFile($model, 'file', 'zip', $dir, pathinfo($fileName, PATHINFO_FILENAME));
    
    		if ($model->validate()) {
    			$rs = UploadedFile::_saveFile('file', $model->file);
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
    						$highestColumn = array('cateId','cateName','gai_number','barcode', 'name','sec_title', 'supply_price' , 'price','isUp','storeName','stock');
    						$excelData = array(); //excel 数据
    						for ($row = 2; $row <= $highestRow; $row++) {
    							foreach ($highestColumn as $k => $v) {
    								$value = $objWorksheet->getCellByColumnAndRow($k, $row)->getValue();
    								$excelData[$row - 2][$v] = trim(is_object($value) ? $value->getPlainText() : $value);
    							}
    
    							if(empty($excelData[$row - 2]['barcode']) && empty($excelData[$row - 2]['name'])){
    								unset($excelData[$row - 2]);
    							}elseif(empty($excelData[$row - 2]['barcode'])){
    								$excelData[$row - 2]['thumb'] = '';
    								$excelData[$row - 2]['status'] = 1;
    								$excelData[$row - 2]['mark'] = '条形码不能为空';
    							}else{
    								$type = $this->_photoExist($excelData[$row - 2]['barcode'], $imgs);  //判断图片是否存在和图片格式
    								if ($type) {
    									$excelData[$row - 2]['thumb'] = 'goods/' . date("Y/m/d", time()) . '/' . $type;
    
    								} else {
    									$excelData[$row - 2]['thumb'] ='';
    								}
    								$excelData[$row - 2]['status'] = 0;
    								$excelData[$row - 2]['mark'] = '';
    							}
    						}
    
    						if (!empty($excelData)) {
    							$member_info = Member::getMemberInfoByGaiNumber($excelData[0]['gai_number']);
    							$partner_info = !empty($member_info)?Partners::model()->find('member_id=:member_id',array(':member_id'=>$member_info['id'])):null;
    							
    							if (empty($partner_info)) {
    								$this->setFlash('error', '商家不存在');
    								$this->refresh();
    							}
    							
    							
    							//查询商家所有商品
    							$goods_list = Yii::app()->db->createCommand()->select('id,barcode')
    							->from(Goods::model()->tableName())
    							->where('partner_id='.$partner_info['id'])
    							->queryAll()
    							;
    							
    							$barcode_list = array();
    							if (!empty($goods_list)) {
    								foreach ($goods_list as $val){
    									$barcode_list[] = $val['barcode'];
    								}
    							}
    							
    							//查询商家的自定义商品分类
    							$goods_cates = Yii::app()->db->createCommand()->select('id,name')
    							->from(GoodsCategory::model()->tableName())
    							->where('member_id='.$partner_info['member_id'])
    							->queryAll()
    							;
    							$goods_cates_values = array();
    							
    							if (!empty($goods_cates)) {
    								foreach ($goods_cates as $c){
    									$goods_cates_values[$c['name']] = $c['id'];
    								}
    							}
    							
    							
    							
    							$store_list = Yii::app()->db->createCommand()->select('id,name')
    							->from(Supermarkets::model()->tableName())
    							->where('member_id='.$partner_info['member_id'])
    							->queryAll()
    							;
    							
    							$store_values = array();
    							if (!empty($store_list)) {
    								foreach ($store_list as $s){
    									$store_values[$s['name']] = $s['id'];
    								}
    							}

    							
    							$stocks = array();
    							foreach ($excelData as $v) {
    								$v['cateName'] = trim($v['cateName']);
    								//判断分类
    								
    								if (!isset($goods_cates_values[$v['cateName']])) {
    									$goods_cate = new GoodsCategory();
    									$goods_cate->member_id = $partner_info['member_id'];
    									$goods_cate->parent_id =0;
    									$goods_cate->name = $v['cateName'];
    									$goods_cate->sort = 1;
    									$goods_cate->save(false);
    									$goods_cates_values[$goods_cate->name] = $goods_cate->id;
    								}
    								
    								$v['selfCateId'] = isset($goods_cates_values[$v['cateName']])?$goods_cates_values[$v['cateName']]:0;
    								
    								$rs = $this->_insertGoods($v,$partner_info,$barcode_list,$store_values,$stocks);
    
    								$moveRs = $this->_movePhotos($rs['barcode'], $imgs);

    								$tmpArray[] = $rs;
    							}
    							
    							
    							//批量更新库存
    							if (!empty($stocks)) {
    								$api_stock = new ApiStock();
    								foreach ($stocks as $store_id=>$store_stock){
    									$store_stock_ids_list = $store_stock['ids'];
    									$store_stock_num_list = $store_stock['stock'];
    									$stocks = $api_stock->goodsStockList($store_id,$store_stock_ids_list,API_PARTNER_SUPER_MODULES_PROJECT_ID);		//创建库存
    									$rs = $api_stock->stockSetList($store_id,$store_stock_ids_list,$store_stock_num_list,API_PARTNER_SUPER_MODULES_PROJECT_ID);			//设置库存
    								}
    							}
    							
    							
    							if (!empty($tmpArray)) {
    								$result = array_merge($result, $tmpArray);
    								unset($tmpArray);
    							}
    						} else {
    							$this->setFlash('error', 'exel文件没有数据');
    							$this->refresh();
    						}
    					}
    
    					@SystemLog::record(Yii::app()->user->name . "导入商家商品 成功");
    				} else {
    					$this->setFlash('error', '不存在exel文件');
    					$this->refresh();
    				}
    			} else {
    				//                    die("Error : ".$archive->errorInfo(true));
    				$this->setFlash('error', '解压失败');
    				$this->refresh();
    			}
    		} else {
    			@SystemLog::record(Yii::app()->user->name . "导入商家商品文件失败");
    			$this->setFlash('error', '上传文件失败');
    			$this->refresh();
    		}
    	}
    
    	$this->render('import', array('model' => $model, 'result' => $result));
    }
    
    /**
     * 插入商品库
     * @param array $data
     * @param $id
     */
    private function _insertGoods(Array $data,$partner_info,$barcode_list = array(),$store_values=array(),&$stocks=array()) {
    	//         if($data['status'] == 1){//图片不存在直接返回数据
    	//             return $data;
    	//         }
    
    	$check = $this->_checkBarcode($data,$barcode_list);
    
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
    			Yii::app()->db->createCommand()->insert(Goods::model()->tableName(), array(
    			'barcode' => $data['barcode'],
    			'name' => $data['name'],
    			'sec_title' => $data['sec_title'],
    			'partner_id' => $partner_info['id'],
    			'member_id' => $partner_info['member_id'],
    			'price' => $data['price'],
    			'supply_price' => $data['supply_price'],
    			'thumb' => isset($data['thumb'])?$data['thumb']:'',
    			'source_cate_id' => $data['cateId'],
    			'cate_id' => $data['selfCateId'],
    			'content' => $data['name'],
    			'create_time' => time(),
    			'status'=>Goods::STATUS_PASS
    			));
    			
    			$goods_id = Yii::app()->db->getLastInsertID();
    			
    			if(isset($data['thumb'])&&!empty($data['thumb'])){
    				$new_pic = str_replace('.', '_img.', $data['thumb']);
    				
    				@copy(Yii::getPathOfAlias('att').DS . $data['thumb'],Yii::getPathOfAlias('uploads').DS . $new_pic);
    				$img_model = new GoodsPicture();
    				$img_model->goods_id = $goods_id;
    				$img_model->path = $new_pic;
    				$img_model->save();
    			}
    			
    			
    			
    			//店铺上架  设置库存
    			$data['isUp'] = trim($data['isUp']);
    			$data['storeName'] = trim($data['storeName']);
    			if (!empty($data['isUp']) && isset($store_values[$data['storeName']])) {
    				$supeGoods = new SuperGoods();
    				$supeGoods->goods_id = $goods_id;
    				$supeGoods->super_id = $store_values[trim($data['storeName'])];
    				$supeGoods->status = SuperGoods::STATUS_ENABLE;
    				$supeGoods->create_time = time();
    				$supeGoods->save(false);
    				
    				//库存关系按店铺分组
    				$stocks[$supeGoods->super_id]['ids'][$goods_id] = $goods_id;
    				$stocks[$supeGoods->super_id]['stock'][$goods_id] = $data['stock'];
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
    private function _checkBarcode(array $data,$excets_barcodes=array()) {
    	$msg = '';

    	if (empty($msg) && empty($data['barcode'])) {
    		$msg = '条形码不能为空';
    	}
    	if (empty($msg) && !preg_match("/^[0-9|a-z|A-Z]{13,16}$/",$data['barcode'])) {
    	     $msg = '条形码必须为13-16位的字母或数字';
    	}
    	if (empty($msg) && (strlen($data['barcode']) < 13 || strlen($data['barcode']) > 16)) {
    		$msg = '条形码长度必须为13-16位';
    	}
//     	if (empty($msg) && $this->_cover($data)=='已覆盖') {
//     		$msg = '已覆盖';
//     	}
//     	if (empty($msg) && $this->_cover($data)=='已存在') {
//     		$msg = '条码已存在';
//     	}
    	//        if (empty($msg) && $this->_exists($data['barcode'])) {
    	//            $msg = '条形码已经存在';
    	//        }
    	        if (empty($msg) && !preg_match('/[\x{4e00}-\x{9fa5}\w]+$/u', $data['name'])) {
    	            $msg = '名称只能是数字、字母、中文和下划线，并不能为空';
    	        }
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
    	if (empty($msg) && !is_numeric($data['price'])) {
    		$msg = '价格必须为数字类型';
    	}
    	if (empty($msg) && $data['price'] < 0) {
    		$msg = '价格不能小于0';
    	}

    	if (empty($msg) && !is_numeric($data['supply_price'])) {
    		$msg = '供货价必须为数字类型';
    	}
    	if (empty($msg) && $data['supply_price'] < 0) {
    		$msg = '供货价不能小于0';
    	}

    	if (in_array($data['barcode'], $excets_barcodes)) {
    		$msg = '条形码已存在';
    	}
    	
    	 
    	
    	return $msg;
    }
    
    /*
     * 检查条形码是否存在
     * 
     * 暂时先不判断
     * 
    */
    
    private function _exists($barcode) {
    	return true;
//     	$model = BarcodeGoods::model()->findByAttributes(array('barcode' => $barcode));
//     	if ($model) {
//     		return true;
//     	} else {
//     		return false;
//     	}
    }
    
    /*
     * 移动图片
    */
    
    private function _movePhotos($num, $imgs) {
    	$newDir = Yii::getPathOfAlias('att') . '/goods/' . date("Y/m/d", time());
    
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
    			UploadedFile::_movePhotos('file', $num . $type,$old,true);
    			return true;
    		} else {
    			!is_dir($newDir) ? $this->_mkdirs($newDir) : '';  //目录不存在则创建目录
    			@rename($old, $new);  //移动文件
    			
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
    public function actionImportTemplate() {

    	//        $data = CJSON::decode($data);
    	//引入phpExcel
    	require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel/Shared/String.php';
    	require Yii::getPathOfAlias('comext') . '/PHPExcel/PHPExcel.php';
    	Yii::registerAutoloader(array('PHPExcel_Autoloader', 'Register'), true);
    	$objPHPExcel = new PHPExcel();
    	$objPHPExcel->setActiveSheetIndex(0)
    
    	->setCellValue('A1', '系统商品分类ID')
    	->setCellValue('B1', '商家自定义分类名称')
    	->setCellValue('C1', '商家GW号')
    	->setCellValue('D1', '条形码')
    	->setCellValue('E1', '商品名称')
    	->setCellValue('F1', '次标题')
    	->setCellValue('G1', '供货价')
    	->setCellValue('H1', '销售价')
    	->setCellValue('I1', '是否上架（不上架请求空）')
    	->setCellValue('J1', '门店名（不存在不上架）')
    	->setCellValue('K1', '库存')
    	
    	->setCellValue('A2', '57')
    	->setCellValue('B2', '促销区a')
    	->setCellValue('C2', 'GW60000002')
    	->setCellValue('D2', 'a12345678912345')
    	->setCellValue('E2', '圣洁洗面奶')
    	->setCellValue('F2', '圣洁洗面奶的次标题')
    	->setCellValue('G2', '50')
    	->setCellValue('H2', '99')
    	->setCellValue('I2', '是')
    	->setCellValue('J2', 'xxx童叟皆欺店')
    	->setCellValue('K2', '99')
    	;
    
    	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $ua = $_SERVER["HTTP_USER_AGENT"];
                $filename = '商家商品导入excel模板.xls';
                $encoded_filename = urlencode($filename);
                $encoded_filename = str_replace("+", "%20", $encoded_filename);
    	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    	header('Pragma: public');
    	header('Content-type: application/vnd.ms-excel;charset=UTF-8');
//    	header('Content-Disposition: attachment; filename="商家商品导入excel模板.xls"');
                if (preg_match("/MSIE/", $ua)) {  
                header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
                } else if (preg_match("/Firefox/", $ua)) {  
                header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
                } else {  
                header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
                }
    	header('Cache-Control: max-age=0');

    	$objWriter->save('php://output');
    	@SystemLog::record(Yii::app()->user->name . "下载商家商品导入模板");
    	unset($objPHPExcel, $objWriter);
    }
  
    
}
