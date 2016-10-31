<?php 
/**
   * 盖网一份子项目及商品后台管理
   * ==============================================
   * Derek写于2016年3月25日 
   * ------------------------------------------------------------------------------------
   * 公司源码文件，未经授权不许任何使用和传播。
   * ==============================================
   * @date: 2016年3月25日
   * @version: Onepart 1.0
   * @return: Obj
   **/
class OnepartGoodsController extends MController
{
    public function filters() {
        return array(
            'rights',
        );
    }  
    
    /**
     * 产品列表
     */
    public function actionAdmin()
    {     
        $model = new YfzGoods('search');
        if(isset($_GET['YfzGoods'])){
            $model->attributes = $_GET['YfzGoods'];
            //$model->startTime = $_GET['YfzGoods']['startTime'];
           // $model->endTime = $_GET['YfzGoods']['endTime'];
        }
        $this->render('admin',array('model'=>$model));
    }
    
    /**
       * 添加一个商品
       * ==============================================
       * 编码时间:2016年3月29日 
       * ------------------------------------------------------------------------------------
       * 公司源码文件，未经授权不许任何使用和传播。
       * ==============================================
       * @date: 2016年3月29日
       * @author: Derek
       * @version: G-emall child One Parts 1.0.0
       * @return: Array|Boolear
       **/
    public function actionInsert()
    {
        $goodsModel = new YfzGoods();
        $imageModel = new YfzGoodsImage();
        $this->performAjaxValidation($goodsModel);
        $this->performAjaxValidation($imageModel);
        if(isset($_POST['YfzGoods']) && isset($_POST['YfzGoodsImage'])){
            $_POST['YfzGoods']['announced_time'] = ($_POST['day'] * 24 * 60 * 60) + ($_POST['hour'] * 60 * 60) + ($_POST['minute'] * 60);
            $_POST['YfzGoods']['max_nper']  = 60000;
//            print_r($_POST['YfzGoods']);exit;
            $goodsModel->attributes = $_POST['YfzGoods'];
            $imageModel->attributes = $_POST['YfzGoodsImage'];
            $goodsModel->sales_time = strtotime($_POST['YfzGoods']['sales_time']);
            $goodsModel->add_time = time();
            $goodsModel->is_on_sale = 0;
            $goodsModel->goods_number = ceil($goodsModel->shop_price/$goodsModel->single_price);
            $connection = Yii::app()->gwpart;
//            print_r($goodsModel->attributes);exit;
            $trans = $connection->beginTransaction();
            try {
                if($goodsModel->validate() && $imageModel->validate()){
                    $goodsModel->save();
                    $imageModel['goods_id'] = $connection->getLastInsertID();
                     // $result = $this->_newGoodsImage($imageModel);
                  // if(!$result) throw new CException('产品图片保存失败');
				  $imageModel->goods_id = $connection->getLastInsertID();
				  $imageModel->goods_thumb = $_POST['YfzGoodsImage']['goods_thumb'];
				  $imageModel->show_image1 = $_POST['YfzGoodsImage']['show_image1'];
				  $imageModel->show_image2 = $_POST['YfzGoodsImage']['show_image2'];
				  $imageModel->show_image3 = $_POST['YfzGoodsImage']['show_image3'];
				  $imageModel->save();
				    
                }
                $trans->commit();
                $this->setFlash('success','添加成功');
                $this->redirect(array('admin'));
            } catch (CException $e) {
                $trans->rollback();
                $this->setFlash('error',$e->getMessage());
                $this->redirect(array('admin'));
            }
        }
        $this->render("adds", array(
            "model"=>$goodsModel,
            "imgModel"=>$imageModel
        ));
    }

//    public function actionTests(){
//        $imageModel = new YfzGoodsImage();
//
//        $this->performAjaxValidation($imageModel);
//        $this->render("tests", array(
//            "imgModel"=>$imageModel
//        ));
//    }
    /**
     * 更新产品
     * @param type $id
     */
    public function actionUpdate($id)
    {
        if(!is_numeric($id)) throw new CException('错误的请求参数');
        $model = new YfzGoods('update');
        $model = $model->findByPk($id);
        if(!$model) {
            $this->setFlash('error','该商品不存在');
            $this->redirect('/onepartGoods/admin');
        }
        if($model->is_closed == YfzGoods::IS_CLOSED_TRUE){
            $this->setFlash('error','该商品已经删除');
            $this->redirect('/onepartGoods/admin');
        }
        if(($model->is_on_sale == YfzGoods::IS_SALES_FALSE || $model->current_nper == $model->max_nper) && !isset($_POST['YfzGoods'])){
            $this->setFlash('error','注意！该商品已经停售或期数已满');
        }
        $imageModel = new YfzGoodsImage();
        $imageModel = $imageModel->find('goods_id=:id',array(':id'=>$id));
        if(isset($_POST['YfzGoods']) && isset($_POST['YfzGoodsImage'])){
            //拼装成开奖时间
            $_POST['YfzGoods']['announced_time'] = ($_POST['day'] * 24 * 60 * 60) + ($_POST['hour'] * 60 * 60) + ($_POST['minute'] * 60);
            $model->attributes = $_POST['YfzGoods'];
			$model->sales_time = strtotime($model->sales_time);
            $imageModel->attributes = $_POST['YfzGoodsImage'];
//            print_r($_POST);exit;
            $connection = Yii::app()->gwpart;
            $trans = $connection->beginTransaction();
            try {
                if($model->validate() && $imageModel->validate()){
                    $modelResult = $model->save(false);
					if($imageModel->save()){
						$showImage1 = $imageModel['show_image1'];
						//$showImage1 = explode('|', $imageModel['show_image1']);
						$showImage2 = $imageModel['show_image2'];
						$showImage3 = $imageModel['show_image3'];
						$imageResult = Yii::app()->gwpart->createCommand()->update('{{goods_image}}', array('show_image1'=>$showImage1,'show_image2'=>$showImage2,'show_image3'=>$showImage3), 'goods_id=:id', array(':id' =>$id));
						
					}
                    //$imageResult = $this->_newGoodsImage($imageModel);
                    if(!$modelResult && !$imageResult) throw new CException('编辑产品失败');
                }
                $trans->commit();
                //缓存清理
                $this->setFlash('success','编辑产品成功');
                $this->redirect('/onepartGoods/admin');
            } catch (CException $e) {
                $trans->rollback();
                $this->setFlash('error',$e->getMessage());
                $this->redirect(array('admin'));
            }
        }
		$model->sales_time = date('Y-m-d H:i:s', $model->sales_time);
        $this->render('update',array(
            "model"=>$model,
           "imgModel"=>$imageModel
        ));
    }
    /**
     * 停售产品
     * @param type $id 
     */
    public function actionDisable($id)
    {
        if(is_numeric($id)){
            $model = YfzGoods::model()->findByPk($id);
            if(!$model) throw new CException('商品不存在');
            $this->_changeSale($model); //停售
        } else{
            throw new CHttpException(404,'错误的请求参数');
        }
        $this->redirect('/onepartGoods/admin');
    }
    /**
     * 商品启用 //权限控制必须要两个控制器方法
     * @param type $id
     * @throws CException
     * @throws CHttpException
     */
    public function actionEnable($id)
    {
        if(is_numeric($id)){
            $model = YfzGoods::model()->findByPk($id);
            if(!$model) throw new CException('该商品不存在');
            if($model->current_nper == $model->max_nper){
                $this->setFlash('error','该商品期数已满，不可启用');
                $this->redirect('/onepartGoods/admin');
            }
            $this->_changeSale($model, YfzGoods::IS_SALES_TRUE); //起售
        } else{
            throw new CHttpException(404,'错误的请求参数');
        }
        $this->redirect('/onepartGoods/admin');
    }
    
    /**
     * 删除产品
     * @param type $id
     * @throws CException
     * @throws CHttpException
     */
    public function actionDelete($id)
    {
        if(is_numeric($id) && $this->isAjax()){
            $model = YfzGoods::model()->findByPk($id);
            if(!$model) throw new CException('商品不存在');
            if($model->is_closed == YfzGoods::IS_CLOSED_TRUE){
                $this->setFlash('error','该商品已经删除');
            } else {
                $model->is_closed = YfzGoods::IS_CLOSED_TRUE;
                if($model->save(false)){
                    $this->setFlash('success','该商品删除成功');
                } else{
                    $this->setFlash('error','该商品删除失败');
                }
            }
        } else{
            throw new CHttpException(404,'错误的请求参数');
        }
        $this->redirect('/onepartGoods/admin');
    }
    /**
     * 处理goods image模型
     * @param object $imageModel
     */
    protected function _newGoodsImage($imageModel)
    {
        if($imageModel instanceof YfzGoodsImage) {
            if ($imageModel->validate()) {
                $showImages = explode('|', $imageModel->show_image1);
                foreach ($showImages as $key => $image) {
                    if ($key >= 3)
                        break;
                    $k = $key + 1;
                    $imageModel->{'show_image' . $k} = $image;
                }
                return $imageModel->save();
            }
        }
        return false;
    }
    
    /**
     * 商品启用和禁用
     * @param type $model
     * @param type $status
     */
    protected function _changeSale($model,$status=YfzGoods::IS_SALES_FALSE)
    {
        $message = array(
            YfzGoods::IS_SALES_FALSE => '停用',
            YfzGoods::IS_SALES_TRUE => '启用'
        );
        if($model->is_on_sale == $status){
            $this->setFlash('error','该商品已经'.$message[$status]);
        } else {
            $model->is_on_sale = $status;
            if($model->save(false)){
                $this->setFlash('success','该商品'.$message[$status].'成功');
            } else{
                $this->setFlash('error','该商品'.$message[$status].'失败');
            }
        }
    }


    /**
     * 商品复制
     * @param type $id
     */
    public function actionCopy($id)
    {
        if(is_numeric($id)){
            $model = new YfzGoods; //新模型
            $imageModel = new YfzGoodsImage; //图片新模型
            $copyModel = YfzGoods::model()->findByPk($id); //得到产品
//            Tool::dump($copyModel);exit;
            if(!$copyModel){
                $this->setFlash('error','商品不存在');
                $this->redirect('/onepartGoods/admin');
            } //判断商品是否存在
            $copyImageModel = YfzGoodsImage::model()->find('goods_id=:id',array(':id'=>$copyModel->goods_id));
            if(!$copyImageModel){
                $this->setFlash('error','缺少必要数值，该商品不可复制'); //判断商品图片是否存在
                $this->redirect('/onepartGoods/admin');
            }
            $model->attributes = $copyModel->attributes;
            $imageModel->attributes = $copyImageModel->attributes;
            unset($model->goods_id);
            unset($imageModel->image_id);
            if($model->validate() && $imageModel->validate()){ //验证数据是否完整
                $connect = Yii::app()->gwpart;
                $trans = $connect->beginTransaction();
                try {
                    $model->add_time = time(); //更新添加时间
                    $model->current_nper = 1; // 更新期数
                    if($model->save()){
                        $goods_id = $connect->getLastInsertID();
                        $imageModel->goods_id = $goods_id; //更新商品id
                        if(!$imageModel->save()) throw new CException('复制失败');
                        $this->setFlash('success','复制成功');
                        $trans->commit();
                    } 
                } catch (CException $e) {
                    $trans->rollback();
                    $this->setFlash('error',$e->getMessage());
                }
            } else {
                $this->setFlash('error','缺少必要数值，该商品复制不成功'); //判断商品图片是否存在
                $this->redirect('/onepartGoods/admin');
            }
        } else{
            throw new CHttpException(403,'参数错误');
        }
        $this->redirect('/onepartGoods/admin');
    }
    
    /**
     * 产品排序 
     */
    public function actionSort()
    {
        if($this->isAjax()){
            $sort = Yii::app()->request->getParam('sort_order');
            if(is_array($sort)){
                $fail = 0;
                foreach ($sort as $s){
                    $model = $this->loadModel($s['id']);
                    if(!$model){ $fail++; break;}
                    $model->sort_order = (int)$s['sort'];
                    if(!$model->save(false)){ $fail++; break; }
                }
                exit(CJSON::encode(array('result'=>'success','fail'=>$fail)));
            }
        }
        throw new CHttpException(404,'找不到该方法');
    }
    
    /**
     * 设置推荐限制数
     */
    public function actionLimit()
    {
        $model = new WebConfig('limit');
        $this->renderPartial('limit',array('model'=>$model));
    }
    /**
     * 推荐产品设置与取消
     * @param type $id
     */
    public function actionRecommend($id)
    {
        if(is_numeric($id)){
            $recommend = Yii::app()->request->getParam('recommend',0);
            $isRecommend = $recommend ? YfzGoods::IS_RECOMMENDED_FASE : YfzGoods::IS_RECOMMENDED_TRUE ; //切换推荐
            $model = new YfzGoods;
            $model->goods_id = $id;
            $result = $model->updateByPk($id, array('recommended'=>$isRecommend));
            if($result)
                $this->setFlash ('success','设置成功');
            else
                $this->setFlash ('error','设置失败');
            $this->redirect('/onepartGoods/admin');
        }
        throw new CException("无效参数");
    }
    
    /**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
    public function loadModel($id)
    {
        return $model=  YfzGoods::model()->findByPk((int)$id);
    }
}