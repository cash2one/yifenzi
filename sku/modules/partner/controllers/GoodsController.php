<?php

/**
 * 商品管理
 *
 * 操作(增删查改)
 * @author leo8705
 */
class GoodsController extends PController {

    public function init() {
        $this->pageTitle = Yii::t('partnerGoods', '_商品管理_') . Yii::app()->name;
        $this->curr_menu_name = '/partner/goods/index';
    }

    /**
     * 检查当前商品是否属于当前商家
     * @param unknown $model
     */
    private function _checkAccess($model) {
        if ($model->member_id !== $this->curr_act_member_id) {
            throw new CHttpException(403, '你没有权限修改别人的数据！');
        }
    }

    /**
     * 添加商品
     */
    public function actionCreate() {
        $this->pageTitle = Yii::t('partnerMachine', '添加商品 _ ') . $this->pageTitle;
        $this->curr_menu_name = '';
        $model = new Goods;
        $model->member_id = $this->curr_act_member_id;
        
        $model->scenario = 'create';
        $is_create = true;
        if (isset($_POST['Goods']['is_barcode']) && $_POST['Goods']['is_barcode']==1 && !empty($_POST['Goods']['thumb'])) {
        	$model->thumb = $_POST['Goods']['thumb'];
        	$model->scenario = 'barcode_add';
        	$is_create = false;
        }
        
        $source_cate_id = $this->getParam('source_cate_id');
        $model->source_cate_id = $source_cate_id;
        
        $imgModel = new GoodsPicture;
        
        $this->performAjaxValidation($model);

        if (isset($_POST['Goods'])) {
            $model->attributes = $_POST['Goods'];
            $model->member_id = $this->curr_act_member_id;
            $model->partner_id = $this->curr_act_partner_id;
            $model->create_time = time();
            $model->name = rtrim($model->name);
            $saveDir = 'partnerGoods/' . date('Y/n/j');
           if ($is_create) $model = UploadedFile::uploadFile($model, 'thumb', $saveDir, Yii::getPathOfAlias('att'));

            if ($model->save()) {
                if ($is_create) UploadedFile::saveFile('thumb', $model->thumb);
                
                /**
                 * 修改商品图片列表数据保存 goods_picture
                 */
                $imgList = explode('|', $_POST['GoodsPicture']['path']);
                $model->addGoodsPicture($imgList);
                
                $this->setFlash('success', Yii::t('partnerGoods', '添加商品成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeInsert, $model->id, '添加商品:' . $model->name );
                $this->redirect(array('index'));
            } else {
                $this->setFlash('error', Yii::t('partnerGoods', '添加商品失败'));
            }
        }

        $this->render('create', array(
            'model' => $model,
        	'imgModel' => $imgModel,
        ));
    }

    /**
     * 修改
     */
    public function actionUpdate($id) {
        $this->pageTitle = Yii::t('partnerMachine', '修改商品信息 _ ') . $this->pageTitle;
        $model = $this->loadModel($id);
        $model->member_id = $this->curr_act_member_id;
        $mid = $this->getParam('mid');
        $sid = $this->getParam('sid');
        $model->scenario = 'update';
        $is_update = true;
        $old_data = $model->attributes;     
        $old_name = $model->name;
        //$model->expr_time = $model->expr_time > 0 ? date('Y-m-d H:i:s',$model->expr_time) : '0000-00-00 00:00:00';

        if (isset($_POST['Goods']['is_barcode']) && $_POST['Goods']['is_barcode']==1 && !empty($_POST['Goods']['thumb'])) {
        	$model->thumb = $_POST['Goods']['thumb'];
        	$model->scenario = 'barcode_update';
        	$is_update = false;
        	//         	$_FILES['Goods']['thumb'] = ATTR_DOMAIN.DS.$_POST['Goods']['thumb'];
        }
        
//         $model->content = stripslashes($model->content);			//反转义
        
        $source_cate_id = $this->getParam('source_cate_id');
        if (!empty($source_cate_id)) $model->source_cate_id = $source_cate_id;
        
        $this->_checkAccess($model);
        $this->performAjaxValidation($model);
        if (isset($_POST['Goods'])) {
            $oldFile = $model->thumb;
            $model->attributes = $_POST['Goods'];
            $model->name = trim($model->name);
            $saveDir = 'partnerGoods/' . date('Y/n/j');
            if ($is_update) $model = @UploadedFile::uploadFile($model, 'thumb', $saveDir, Yii::getPathOfAlias('att'), $oldFile);  // 上传图片

            $model->status = Goods::STATUS_AUDIT;
            if ($model->save()) {
                if ($is_update) @UploadedFile::saveFile('thumb', $model->thumb, $oldFile, true);
                
//                 //压缩图片
//                 $img_path = Yii::getPathOfAlias('att').DS.$model->thumb;
//                 Tool::resize_pic($img_path);
                
                //下架已关联商品
                Yii::app()->db->createCommand()->update(SuperGoods::model()->tableName(), array('status'=>SuperGoods::STATUS_DISABLE),'goods_id='.$model->id);
                Yii::app()->db->createCommand()->update(VendingMachineGoods::model()->tableName(), array('status'=>VendingMachineGoods::STATUS_DISABLE),'goods_id='.$model->id);    
                //对生鲜鸡已占用的货道解除占用
                $freshmachinegoods = FreshMachineGoods::model()->findAll('goods_id=:id',array(':id'=>$model->id));
                if(!empty($freshmachinegoods)){
                    foreach($freshmachinegoods as $v){
                        if($v->status ==FreshMachineGoods::STATUS_ENABLE){
                            Yii::app()->db->createCommand()->update(FreshMachineLine::model()->tableName(), array('status'=>  FreshMachineLine::STATUS_ENABLE),'id='.$v->line_id);
                        }
                    }
                }
                 Yii::app()->db->createCommand()->update(FreshMachineGoods::model()->tableName(), array('status'=>FreshMachineGoods::STATUS_DISABLE),'goods_id='.$model->id);
                /**
                 * 修改商品图片列表数据保存 goods_picture
                 */
                  $Goods_picture = GoodsPicture::model()->findAll('goods_id=:gid',array(':gid'=>$model->id));
                  $o_pic = array();
                  foreach($Goods_picture as $v){
                      $o_pic[] = $v->path;
                  }

//                var_dump($_POST['GoodsPicture']['path']);
                $imgList = explode('|', $_POST['GoodsPicture']['path']);
                  $pic_content = '';
                if (array_diff($o_pic, $imgList)) {
                  $pic_content = '修改图片列表';
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

              $diff = array_diff($model->attributes,$old_data);
                 $str = $model->attributeLabels();
                 $content = '';
              foreach($diff as $k=>$v){
                  if($k=='source_cate_id'){
                      $o_cate = Category::model()->findByPk($old_data[$k]);
                      $o_name = empty($o_cate)?'无原始分类':$o_cate->name;
                      $n_cate = Category::model()->findByPk($v);
                      $n_name = $n_cate->name;
                      
                      $content.=$str[$k].':'.$o_name.'->'.$n_name.' | ';
                  }elseif($k=='cate_id'){
                      $so_cate = GoodsCategory::model()->findByPk($old_data[$k]);
                      $so_name = empty($so_cate)?'无商家分类':$so_cate->name;
                      $sn_cate = GoodsCategory::model()->findByPk($v);
                      $sn_name = $sn_cate->name;
                      $content .=$str[$k].':'.$so_name.'->'.$sn_name.' | ';
                  }elseif($k =='thumb' || $k=='content'){
                      $content.= ($k =='thumb')?'修改缩略图 | ':'修改商品详情 | ';
                  }elseif($k=='sec_title'){
                      $content.= empty($old_data['sec_title'])?($str[$k].':无次标题->'.$v).' | ':$str[$k].':'.$old_data[$k].'->'.$v.' | ';
                  }elseif($k =='is_one'){
                      $content .='是否一元购商品:'.Goods::gender($old_data['is_one']).'->'.Goods::gender($model->is_one).' | ';
                  }elseif($k=='is_for'){
                       $content .='是否促销商品:'.Goods::gender($old_data['is_promo']).'->'.Goods::getIsProme($model->is_promo).' | ';
                  }elseif($k=='is_promo'){
                      $content .='是否促销商品:'.Goods::gender($old_data['is_promo']).'->'.Goods::getIsProme($model->is_promo).' | ';
                  }
                  else{
                  $content.=$str[$k].':'.$old_data[$k].'->'.$v.' | ';
                  }
              }
              if($pic_content){
                  $content.=$pic_content;
              }
//              if(!in_array('is_one', $diff)){
//                  if($old_data['is_one'] != $model->is_one){
//                      $content .='是否一元购商品:'.Goods::gender($old_data['is_one']).'->'.Goods::gender($model->is_one).' | ';
//                  }
//              }
//              if(!in_array('is_for', $diff)){
//                  if($old_data['is_for'] != $model->is_for){
//                      $content .='是否专供商品:'.Goods::gender($old_data['is_for']).'->'.Goods::getIsFor($model->is_for).' | ';
//                  }
//              }
//              if(!in_array('is_promo', $diff)){
//                  if($old_data['is_promo'] != $model->is_promo){
//                      $content .='是否促销商品:'.Goods::gender($old_data['is_promo']).'->'.Goods::getIsProme($model->is_promo).' | ';
//                  }
//              }
              $content = rtrim($content,' | ');
              $content = empty($content)?'无操作内容':$content;
                    $page =1;
                    $pageSize=100000;
                    $cache_key = md5($sid.$page.$pageSize);
                     Tool::cache(Goods::CACHE_DIR_API_CGOODS_STORE_GOODS_LIST)->delete($cache_key);
                $this->setFlash('success', Yii::t('partnerGoods', '修改商品成功'));
                ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '修改商品('.$old_name.')内容:' .$content );
                $this->redirect(isset($_GET['returnUrl']) ? array($_GET['returnUrl'].'/index') : array('index'));
 
            } else {
                $model->thumb = $oldFile;
                $this->setFlash('error', Yii::t('partnerGoods', '修改商品失败'));
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
        ));
    }

    /**
     * 商品列表
     */
    public function actionIndex() {
        $this->pageTitle = Yii::t('partnerMachine', '商品列表 _ ') . $this->pageTitle;
        $model = new Goods('search');
        $model->unsetAttributes(); // clear any default values
        $model->member_id = $this->curr_act_member_id;

        if (isset($_GET['Goods'])) {
            $model->attributes = $this->getQuery('Goods');
        }

        $lists = $model->search();
        $goods_data = $lists->getData();
        $pager = $lists->pagination;

        $this->render('index', array(
            'model' => $model,
            'goods_data' => $goods_data,
            'pager' => $pager,
        ));
    }

    /**
     * 获取商品列表
     */
    public function actionSearchList() {
        $model = new Goods('search');
        $model->unsetAttributes(); // clear any default values
        $model->member_id = $this->curr_act_member_id;

        if (isset($_GET['Goods'])) {
            $model->attributes = $this->getQuery('Goods');
        }
        $this->layout = 'dialog';
        $this->render('searchList', array(
            'model' => $model,
        		'type'=>$this->getParam('type'),
        		'sid'=>$this->getParam('sid'),
        		'all'=>$this->getParam('all'),
        ));
    }

    /*
     * ajax 查询商品
     */

    public function actionBarcodeGoods() {
        if ($this->isPost()) {
            $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';
            $model = BarcodeGoods::model()->find('barcode=:bc', array(':bc' => $barcode));
            $data = array();
            if (!empty($model)) {
                foreach ($model as $k => $v) {
                    $data[$k] = $v;
                }
                echo json_encode($data);
            }
        }
    }
    
    
    /**
     * 商品添加第一步.选择商品分类
     * @author zhenjun_xu <412530435@qq.com>
     */
    public function actionSelectCategory()
    {
    	if ($this->getParam('class_id')) {
    		if ($this->getParam('id') > 0) {
    			$url = $this->createAbsoluteUrl('goods/update', array(
    					'source_cate_id' => $this->getParam('class_id'),
    					'type_id' => $this->getParam('t_id'),
    					'id' => $this->getParam('id'),
    			));
    		} else {
    			$url = $this->createAbsoluteUrl('goods/create', array(
    					'source_cate_id' => $this->getParam('class_id'),
    					'type_id' => $this->getParam('t_id'),
    			));
    		}
    		$this->redirect($url);
    	}
    	$this->render('selectCategory');
    }
    
    /**
     * ajax调用分类json数据
     * @author zhenjun_xu <412530435@qq.com>
     */
    public function actionGetJson()
    {
    	if (!$this->isAjax()) die;
    	$cid = $this->getParam('cid');
    	//参数cid为空时，不返回任何数据
    	if(empty($cid)) die;
    	exit(Category::getCategory($cid));
    }

}
