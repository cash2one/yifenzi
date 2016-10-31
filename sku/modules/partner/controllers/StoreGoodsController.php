<?php

/**
 * 超市商品管理
 * 操作(查看，添加，修改)
 * @author leo8705
 */
class StoreGoodsController extends SSController {

	public function init()
	{
		$this->curr_menu_name = '/partner/storeGoods/index';
	}
	
	/**
	 * 检查当前商品是否属于当前商家
	 * @param unknown $model
	 */
	protected function _checkGoodsAccess($storeGoods){
		if (empty($storeGoods) || $storeGoods->super_id !== $this->super_id) {
			throw new CHttpException(403,Yii::t('partnerModule.storeGoods','你没有权限修改别人的数据！'));
		}
	}
	
    /**
     * 申请、添加新的超市商品
     */
    public function actionAdd() {
        
        $this->pageTitle = Yii::t('partnerModule.storeGoods','添加门店商品 _ ').$this->pageTitle;
        $model = new SuperGoods();
        $model->scenario = 'add';
        $this->performAjaxValidation($model);
        
        if (isset($_POST['SuperGoods'])) {
        	$model->attributes = $this->getPost('SuperGoods');
        	$model->super_id = $this->super_id;
        	$model->create_time = time();
        	
        	//检查重复商品
        	if (SuperGoods::model()->count(' super_id=:super_id AND goods_id=:goods_id  ',array(':super_id'=>$this->super_id,':goods_id'=>$model->goods_id))) {
        		$this->setFlash('error', Yii::t('partnerModule.storeGoods','超市商品已存在！'));
        		$this->redirect(array('add'));
        		return;
        	} 
        	
        	//接口创建库存
        	$stocks_rs = ApiStock::createStock($this->super_id, $model->goods_id,$model->stock*1);
        	if (!isset($stocks_rs['result']) || $stocks_rs['result']!=true) {
        		$this->setFlash('error', Yii::t('partnerModule.storeGoods','添加库存失败！'));
        		$this->redirect(array('add'));
        		return;
        	}

        	$goods = Goods::model()->findByPk($model->goods_id);
        	if ($model->save()) {
        		$this->setFlash('success', Yii::t('partnerModule.storeGoods','添加超市商品成功'));
        		
        		ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'添加超市('.$this->store->name.')商品:'.$goods->name.'|初始库存:'.$model->stock*1);
        		$this->redirect(array('index'));
        	} else {
        		$this->setFlash('error', Yii::t('partnerModule.storeGoods','添加超市商品失败'));
        	}
        }
        
        $this->render('add', array(
        		'model' => $model,
        ));
        
    }

    /**
     * 查看
     */
    public function actionView() {
        /** @var $model Store */
        $this->layout = 'seller';
        $model = Store::model()->findByPk($this->storeId);
        if (!$model) {
            $this->redirect(array('store/apply'));
        }
        $this->render('view', array('model' => $model));
    }
    

    /**
     * 商品列表
     */
    public function actionIndex()
    {
        $this->pageTitle = Yii::t('partnerModule.storeGoods','超市门店商品管理 _ ').$this->pageTitle;
        $model = new SuperGoods('search');     
        if(empty($this->super_id)){
              $this->setFlash('error', Yii::t('partnerModule.storeGoods','请先添加门店'));
             $this->redirect(array('store/add'));
        }  
        $super = Supermarkets::model()->findByPk($this->super_id);
        $model->unsetAttributes(); // clear any default values
        $model->super_id= $this->super_id;
         if($super->status ==  Supermarkets::STATUS_APPLY || $super->status ==  Supermarkets::STATUS_DISABLE){
            $this->setFlash('error', Yii::t('partnerModule.storeGoods','该门店未通过审核或被禁用！'));
           $this->redirect($_SERVER['HTTP_REFERER']);
        }
        
        if (isset($_GET[get_class($model)]))
        	$model->attributes = $this->getQuery(get_class($model));
        
        $lists = $model->superSearch();
        $goods_data = $lists->getData();
        $pager = $lists->pagination;
        
        //查询库存
        $good_ids = array();
        foreach ($goods_data as $data){
        	$good_ids[] = $data->goods_id;
        }
        
        $stocks = ApiStock::goodsStockList($this->super_id, $good_ids);
        $this->render('index', array(
            'model' => $model,
        	'goods_data'=>$goods_data,
        	'pager'=>$pager,
        	'stocks'=>$stocks,
        ));
    }


    
    /**
     * 进货
     */
    public function actionStockIn($id) {
        
        $this->pageTitle = Yii::t('partnerModule.storeGoods','超市门店进货 _ ').$this->pageTitle;
		$model = SuperGoods::model()->findByPk($id);
		$this->_checkGoodsAccess($model);
		$model->scenario = 'stock';
		$this->performAjaxValidation($model);
	
		if (isset($_POST['SuperGoods'])) {
			$model->attributes = $this->getPost('SuperGoods');
			 
			$stock = ApiStock::goodsStockOne($this->super_id, $model->goods_id);
			$stock = isset($stock['result']['stock'])?$stock['result']['stock']*1:0;
			
			$stock_config = $this->params('stock');
			if ($stock_config['maxStock']<=($stock+ $model->stock)) {
				$this->setFlash('error', Yii::t('partnerModule.storeGoods', '不能超过最大库存，最大库存为').$stock_config['maxStock']);
				$this->redirect(array('index'));
			}
			
			//接口创建库存
			$stocks_rs = ApiStock::stockIn($this->super_id, $model->goods_id,$model->stock*1);
//                                                    $change_stock = empty($model->stock)?'无操作内容':$model->stock;
			$goods = Goods::model()->findByPk($model->goods_id);
			if (isset($stocks_rs['result']) && $stocks_rs['result']==true) {
				$this->setFlash('success', Yii::t('partnerModule.storeGoods','超市商品库存进货成功！'));
				ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'超市('.$this->store->name.')商品进货:'.$goods->name.'| 数量:'.$model->stock);
				$this->redirect(array('index'));
			} else {
				$this->setFlash('error', Yii::t('partnerModule.storeGoods','超市商品库存进货失败'));
			}
		}
	
		$this->render('stockOut', array(
				'model' => $model,
		));
    }



/**
 * 申请、添加新的超市商品
 */
public function actionStockOut($id) {
	$this->pageTitle = Yii::t('partnerModule.storeGoods','超市门店出货 _ ').$this->pageTitle;
	$model = SuperGoods::model()->findByPk($id);
	$this->_checkGoodsAccess($model);
	$model->scenario = 'stock';
	$this->performAjaxValidation($model);

	if (isset($_POST['SuperGoods'])) {
		$model->attributes = $this->getPost('SuperGoods');
		//接口创建库存
		$stocks_rs = ApiStock::stockOut($this->super_id, $model->goods_id,$model->stock*1);
//                                      $change_stock = empty($model->stock)?'无操作内容':$model->stock;
                                    $goods = Goods::model()->findByPk($model->goods_id);
		if (isset($stocks_rs['result']) && $stocks_rs['result']==true) {
			$this->setFlash('success', Yii::t('partnerModule.storeGoods','超市商品库存出货成功！'));
			ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'超市('.$this->store->name.')商品出货:'.$goods->name.'| 数量:'.$model->stock);
			$this->redirect(array('index'));
		} else {
			$this->setFlash('error', Yii::t('partnerModule.storeGoods','超市商品库存出货失败').(isset($stocks_rs['msg'])?'|'.$stocks_rs['errorCode'].':'.$stocks_rs['msg']:''));
		}
	}

	$this->render('stockOut', array(
			'model' => $model,
	));
}


/**
 * 超市门店商品上架
 */
public function actionEnable($id) {
        $store_goods = SuperGoods::model()->findByPk($id);
        $goods = Goods::model()->find('id=:gid',array(':gid'=>$store_goods->goods_id));
//        var_dump($_SERVER['HTTP_REFERER']);die;
        if($goods->status == Goods::STATUS_NOPASS || $goods->status == Goods::STATUS_AUDIT){
            $this->setFlash('error', Yii::t('partnerModule.storeGoods','商品审核未通过，不能上架'));
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
	$this->pageTitle = Yii::t('partnerModule.storeGoods', '小微企业联盟') . $this->pageTitle;
	$model = SuperGoods::model()->findByPk($id);
	$this->_checkGoodsAccess($model);

	$model->status = SuperGoods::STATUS_ENABLE;
	if ($model->save()) {
			$this->setFlash('success', Yii::t('partnerModule.storeGoods','超市('.$this->store->name.')商品上架成功'));
			ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'设置超市商品上架:'.$goods->name);
		} else {
			$this->setFlash('error', Yii::t('partnerModule.storeGoods','设置超市商品上架'));
		}
		$this->redirect($_SERVER['HTTP_REFERER']);
}



/**
 * 超市门店商品下架
 */
public function actionDisable($id) {
       $store_goods = SuperGoods::model()->findByPk($id);
        $goods = Goods::model()->find('id=:gid',array(':gid'=>$store_goods->goods_id));
        if($store_goods->status == SuperGoods::STATUS_DISABLE){
            $this->setFlash('error', Yii::t('partnerModule.storeGoods','商品审核未通过，已自动下架'));
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
		$this->pageTitle = Yii::t('partnerModule.storeGoods', '小微企业联盟') . $this->pageTitle;
		$model = SuperGoods::model()->findByPk($id);
		$this->_checkGoodsAccess($model);

	$model->status = SuperGoods::STATUS_DISABLE;         
	$tran = Yii::app()->db->beginTransaction();
	if ($model->save()) {
		
		//取消相关未支付订单
		$orders = Yii::app()->db->createCommand()
		->select('t.code')
		->from(Order::model()->tableName().' t')
		->leftJoin(OrdersGoods::model()->tableName().' g', 't.id=g.order_id')
		->where('t.type='.Order::TYPE_SUPERMARK.'  AND t.status='.Order::STATUS_NEW.' AND t.pay_status='.Order::PAY_STATUS_NO.' AND g.sgid=:sgid ',array(':sgid'=>$id))
		->queryAll();
		foreach ($orders as $o){
			Order::orderCancel($o['code'],true,Yii::t('partnerModule.storeGoods','由于部分商品下架，本订单已自动取消'),false);
		}
		$tran->commit();
		$this->setFlash('success', Yii::t('partnerModule.storeGoods','超市商品下架成功'));
		ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'设置超市('.$this->store->name.')商品下架:'.$goods->name);
	} else {
		$this->setFlash('error', Yii::t('partnerModule.storeGoods','设置超市商品下架'));
	}
        //下架清缓存
         $page =1;
        $pageSize=100000;
        $cache_key = md5($this->store['id'].$page.$pageSize);
         Tool::cache(Goods::CACHE_DIR_API_CGOODS_STORE_GOODS_LIST)->delete($cache_key);
	$this->redirect($_SERVER['HTTP_REFERER']);
}

/**
 * 超市门店商品批量上架
 */
public function actionMultEnable() {

	$ids = $this->getParam('idArr');
	$ids = explode(',', $ids);

	if (empty($ids)) {
		echo '参数错误';
		exit();
	}
	
	foreach ($ids as $k=>$v){
		$ids[$k] = $v*1;
	}
	
	$rs = Yii::app()->db->createCommand(
			'UPDATE '.SuperGoods::model()->tableName().' as t , '.Goods::model()->tableName() .' as g 
			SET 
				t.status='.SuperGoods::STATUS_ENABLE.'  
			WHERE 
				t.id IN('.implode(',', $ids).') AND  g.member_id='.$this->curr_act_member_id.' AND t.goods_id=g.id  AND g.status='.Goods::STATUS_PASS.' AND t.status!='.SuperGoods::STATUS_ENABLE.' 
			 '
	)->execute();
	
                $sql =  'SELECT g.name FROM '.Goods::model()->tableName(). ' AS g LEFT JOIN '.SuperGoods::model()->tableName(). ' AS f ON g.id = f.goods_id WHERE f.id IN('. implode(',', $ids).')';
                $goods_arr = Yii::app()->db->createCommand($sql)->queryAll();
                $goods_name = '';
                foreach($goods_arr as $val ){
                    $goods_name.=$val['name'].',';
                }
                $goods_name = rtrim($goods_name,',');
	if ($rs) {
		echo '批量上架成功';
		ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,0,'批量上架超市('.$this->store->name.')商品：'.$goods_name);
	} else {
		echo '批量上架失败';
	}
	exit();
}

}