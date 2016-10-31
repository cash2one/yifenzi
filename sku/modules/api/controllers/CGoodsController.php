<?php
/**
 * 盖付通商品接口控制器
 * 
 * @author leo8705
 *
 */

class CGoodsController extends CAPIController {

	const CACHE_DIR = 'CGoodsController';
 
    /**
     * 获取店铺商品列表
     *
     *
     */
    public function actionStoreGoodsList() {
    	$tag_name = 'GoodsList';
    	$sid = $this->getParam('sid');				//门店id
    	$cateId = $this->getParam('cateId');			//分类
    	$page = $this->getParam('page')?$this->getParam('page'):1;
    	$pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):20;
    	
    	//lastId 上条记录id
    	$lastId = $this->getParam('lastId') ? $this->getParam('lastId')*1 : -1;
    	if (empty($sid)) {
    		$this->_error(Yii::t('apiModule.goods','sid不能为空'),$tag_name);
    	}
    	 
    	$cri = new CDbCriteria();
    	$cri->select = 't.*,concat("'.ATTR_DOMAIN.'/",t.thumb) AS thumb, g.id as id,t.id as gid';
    	$cri->join = ' LEFT JOIN  '.SuperGoods::model()->tableName().' as g  ON g.goods_id = t.id ';
    	
    	$cri->compare('g.super_id', $sid);
    	$cri->compare('g.status', SuperGoods::STATUS_ENABLE);
    	$cri->compare('t.status', Goods::STATUS_PASS);
    	if (!empty($cateId)) $cri->compare('t.cate_id', $cateId);
    	if ($lastId>0) {
    		$cri->addCondition('g.id>'.$lastId);
    	}
    	//分页
    	$cri->limit = $pageSize;
    	$cri->offset = ($page-1)*$pageSize;
    	 
    	$list = Goods::model()->findAll($cri);
    	 
    	//遍历取库存
    	$good_ids = array();
    	foreach ($list as $data){
    		$good_ids[] = $data->gid;
    	}
    	
    	$stocks = ApiStock::goodsStockList($sid, $good_ids,API_PARTNER_SUPER_MODULES_PROJECT_ID);
    	
    	foreach ($list as $key=>$val){
    		$list[$key] = array_merge($val->attributes,$stocks[$val['gid']]);
            $list[$key]['goodsDetails']['content'] = $list[$key]['content'];
            if (!empty($val->goodsPicture)) {
              foreach ($val->goodsPicture as $p){
                 $list[$key]['goodsDetails']['goodsPic'][] = IMG_DOMAIN.DS.$p['path'];
              }
          }else{
                $list[$key]['goodsDetails']['goodsPic'] = '';
            }
    	}

    	$this->_success($list,$tag_name);
    	
    }
    
     /**
     * 获取店铺商品列表
     * 
     * 按分类分组
     *
     *
     */
    public function actionStoreCateGoodsList() {
    	$tag_name = 'GoodsList';
    	$sid = $this->getParam('sid');				//门店id
    	$page = $this->getParam('page')?$this->getParam('page'):1;
    	$pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):100;
    	
    	$store = Supermarkets::model()->findByPk($sid);
    	if (empty($sid) || empty($store) || $store->status != Supermarkets::STATUS_ENABLE) {
    		$this->_error(Yii::t('apiModule.goods','门店不存在'));
    	}
    	
    	$cri = new CDbCriteria();
    	$cri->select = 't.*,concat("'.ATTR_DOMAIN.'/",t.thumb) AS thumb, g.id as id,t.id as gid';
    	$cri->join = ' LEFT JOIN  '.SuperGoods::model()->tableName().' as g  ON g.goods_id = t.id ';
    	
    	$cri->compare('g.super_id', $sid);
    	$cri->compare('g.status', SuperGoods::STATUS_ENABLE);
    	$cri->compare('t.status', Goods::STATUS_PASS);
    	
    	
    	//分页
    	$cri->limit = $pageSize;
    	$cri->offset = ($page-1)*$pageSize;
    	
    	$list = Goods::model()->findAll($cri);
    	
    	//遍历取库存
    	$good_ids = array();
    	foreach ($list as $data){
    		$good_ids[] = $data->gid;
    	}
    	
    	$stocks = ApiStock::goodsStockList($sid, $good_ids,API_PARTNER_SUPER_MODULES_PROJECT_ID);
    	
    	foreach ($list as $key=>$val){

    		if (!isset($stocks[$val['gid']])) {
    			$stocks[$val['gid']] = array('sotck'=>0,'frozenStock'=>0);
    		}
            $matches = array();
            $partern ="/<p>(.*?)<\/p>/";
            preg_match_all($partern, $list[$key]['content'], $matches);
            $html= isset($matches[1])?$matches[1]:"";
            $str ="";
            if(!empty($html)){
                foreach($html as $k => $v){
                    $str .= $v;
                }
            }
    		$list[$key] = array_merge($val->attributes,$stocks[$val['gid']]);
            $list[$key]['goodsDetails']['content'] = $str;
            $goodsPic = Yii::app()->db->createCommand()
                ->select('concat("'.IMG_DOMAIN.'/",path) AS path')
                ->from('{{goods_picture}}')
                ->where('goods_id = :id',array(':id'=>$val['gid']))
                ->queryAll();
            if (!empty($goodsPic)) {
                    $list[$key]['goodsDetails']['goodsPic'] = $goodsPic;
            }else{
                $list[$key]['goodsDetails']['goodsPic'] = array();
            }
    	}
    	 
    	 
    	$cates = GoodsCategory::getGoodsCategoryList($store->member_id);
    	$rs_list = array();
    	
    	foreach ($list as $val){
    		$rs_list[$val['cate_id']]['cate_name'] = isset($cates[$val['cate_id']])?$cates[$val['cate_id']]:'未知分类';
    		$rs_list[$val['cate_id']]['cate_id'] =$val['cate_id'];
            unset($val['content']);
    		$rs_list[$val['cate_id']]['goods_items'][] = $val;
    	}
    	 
    	$rs_list = array_values($rs_list);


    	$this->_success($rs_list,$tag_name);
    }
    
    /**
     * 第三版
     */
    public function actionStoreCateGoodsListV3() {
    	$tag_name = 'GoodsList';
    	$sid = $this->getParam('sid');				//门店id
    	$page = $this->getParam('page')?$this->getParam('page'):1;
    	$pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):100000;
    	
    	$noCache = $this->getParam('noCache');
    	
    	$withStore = $this->getParam('withStore',null);
    	 
    	$store = Supermarkets::model()->findByPk($sid);
    	if (empty($sid) || empty($store) || $store->status != Supermarkets::STATUS_ENABLE) {
    		$this->_error(Yii::t('apiModule.goods','门店不存在'));
    	}
    	
    	if (!empty($withStore)) {
    		$storeInfo = $store->attributes;
    		$storeInfo['logo'] = ATTR_DOMAIN.DS.$store['logo'];
    		$storeInfo['province_name'] = Region::getName($storeInfo['province_id']);
    		$storeInfo['city_name'] = Region::getName($storeInfo['city_id']);
    		$storeInfo['district_name'] = Region::getName($storeInfo['district_id']);
    		$storeCateImg = StoreCategory::getCategoryList($storeInfo['category_id']);
    		$storeInfo['storeCateImg'] = $storeCateImg;
    	
    		$storeInfo['stype'] = Stores::SUPERMARKETS;
    	
    		$data_rs['storeInfo'] = $storeInfo;
    	}
    	
    	
    	$cache_key = md5($sid.$page.$pageSize);
    	
    	$rs_list = !empty($noCache)?null:Tool::cache(Goods::CACHE_DIR_API_CGOODS_STORE_GOODS_LIST)->get($cache_key);
    	if (empty($rs_list)) { 

    		$cri = new CDbCriteria();
    		$cri->select = 't.*,concat("'.ATTR_DOMAIN.'/",t.thumb) AS thumb, g.id as id,t.id as gid';
    		$cri->join = ' LEFT JOIN  '.SuperGoods::model()->tableName().' as g  ON g.goods_id = t.id ';
    		
    		$cri->compare('g.super_id', $sid);
    		$cri->compare('g.status', SuperGoods::STATUS_ENABLE);
    		$cri->compare('t.status', Goods::STATUS_PASS);
    		
    		 
    		 
    		//分页
    		$cri->limit = $pageSize;
    		$cri->offset = ($page-1)*$pageSize;
    		
    		$list = Goods::model()->findAll($cri);
    		
    		//遍历取库存
    		$good_ids = array();
    		foreach ($list  as  $key=>$val){
    			$good_ids[] = $val->gid;
    		}
    		 
    		$goodsPics = Yii::app()->db->createCommand()
    		->select('goods_id,concat("'.IMG_DOMAIN.'/",path) AS path')
    		->from('{{goods_picture}}')
    		->where('goods_id IN ('.implode(',', array_merge(array('0'),$good_ids)).')')
    		->queryAll();
    		 
    		foreach ($list as $key=>$val){
//     			$matches = array();
//     			$partern ="/<p>(.*?)<\/p>/";
//     			preg_match_all($partern, $list[$key]['content'], $matches);
//     			$html= isset($matches[1])?$matches[1]:"";
//     			$str ="";
//     			if(!empty($html)){
//     				foreach($html as $k => $v){
//     					$str .= $v;
//     				}
//     			}
    			$gid = $list[$key]['gid'];
    			$list[$key] = $val->attributes;
    			$list[$key]['gid'] = $gid;
    			$list[$key]['goodsDetails']['content'] = $list[$key]['content'];
    		
    			$goodsPic = array();
    			if (!empty($goodsPics)) {
    				foreach ($goodsPics as $pic){
    					if ($val['gid']==$pic['goods_id']) {
    						$goodsPic[] = array('path'=>$pic['path']);
    					}
    				}
    			}
    			
    		
    			$list[$key]['goodsDetails']['goodsPic'] = $goodsPic;
    		}
    		
    		
    		$cates = GoodsCategory::getGoodsCategoryList($store->member_id);
    		$rs_list = array();
    		
    		foreach ($list as $val){
    			$rs_list[$val['cate_id']]['cate_name'] = isset($cates[$val['cate_id']])?$cates[$val['cate_id']]:'未知分类';
    			$rs_list[$val['cate_id']]['cate_id'] =$val['cate_id'];
    			unset($val['content']);
    			$rs_list[$val['cate_id']]['goods_items'][] = $val;
    		}
    		 
    		
    		Tool::cache(Goods::CACHE_DIR_API_CGOODS_STORE_GOODS_LIST)->set($cache_key,$rs_list,900);
    		
    		
    	}
    	
    	if (!isset($good_ids)) {
    		$good_ids = array();
    		foreach ($rs_list as $keyf=> $dataf){
    			foreach ($dataf['goods_items'] as $keys=>$goods){
    				$good_ids[] = $goods['gid'];
    			}
    		}
    	}
    	
    	//获取库存
    	$stocks = ApiStock::goodsStockList($sid, $good_ids,API_PARTNER_SUPER_MODULES_PROJECT_ID);
    	
    	foreach ($rs_list as $keyf=> $dataf){
    		foreach ($dataf['goods_items'] as $keys=>$goods){
    			if (!isset($stocks[$goods['gid']])) {
    				$stocks[$goods['gid']] = array('sotck'=>0,'frozenStock'=>0);
    			}
    			
    			$rs_list[$keyf]['goods_items'][$keys] = array_merge($goods,$stocks[$goods['gid']]);
    		}
    	}
    	
    	$rs_list = array_values($rs_list);
    	$data_rs['goodsList'] = $rs_list;
    	 
    	//查询积分充值商品列表
    	$point_list = GuadanJifenGoods::getFromatListByMemberId($store['member_id']);
    	if (!empty($point_list)) {
    	//查询用户状态
    	$is_old = GuadanJifenOrder::getOrderNums($this->member);
    	
    		if ($is_old) {
    			unset($point_list['new']);
	    	}else{
	    		unset($point_list['old']);
	    	}
	    	
	    	//获取商家可卖的积分余额
	    	if ((isset($point_list['new'] ) && !empty($point_list['new'])) || ( isset($point_list['old'] ) && !empty($point_list['old']))) {
	    		$data_rs['pointSelling']['selling_discount'] = $point_list['selling_discount'];
	    		$data_rs['pointSelling']['partner_amount'] = AccountBalance::getPartnerGuadanScorePoolBalance($store['member_id'])*1;
	    		unset($point_list['selling_discount']);
	    		
	    		
	    		
	    		if ($is_old) {
	    			foreach ($point_list['old'] as $k=>$listData){
	    				$sellable_amount = ($listData['amount_limit']-$listData['sale_amount'])>$data_rs['pointSelling']['partner_amount']?$data_rs['pointSelling']['partner_amount']:($listData['amount_limit']-$listData['sale_amount']);
	    				$point_list['old'][$k]['stock'] = floor($sellable_amount/$listData['amount']);
                                        $point_list['old'][$k]['percent'] =  ($point_list['old'][$k]['point_give']/$point_list['old'][$k]['point']);
	    			}
	    		}else{
	    			foreach ($point_list['new'] as $k=>$listData){
	    				$sellable_amount = ($listData['amount_limit']-$listData['sale_amount'])>$data_rs['pointSelling']['partner_amount']?$data_rs['pointSelling']['partner_amount']:($listData['amount_limit']-$listData['sale_amount']);
	    				$point_list['new'][$k]['stock'] = floor($sellable_amount/$listData['amount']);
                                        $point_list['new'][$k]['percent'] =  ($point_list['new'][$k]['point_give']/$point_list['new'][$k]['point']);
	    			}
	    		}
	    		
	    		
	    		$data_rs['pointSelling']['goodsList'] = $point_list;
	    		
	    	}
	    	
    	}
    	
    	$this->_success($data_rs,$tag_name);
    }
    
    
    
    /**
     * 获取售货机商品列表
     *
     *
     */
    public function actionMachineGoodsList() {
    	$tag_name = 'GoodsList';
    	$mid = $this->getParam('mid');				//机器id
    	$page = $this->getParam('page')?$this->getParam('page'):1;	
    	$pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):100;

    	if (empty($mid)) {
    		$this->_error(Yii::t('apiModule.goods','mid不能为空'),$tag_name);
    	}
    	
    	$cri = new CDbCriteria();
    	$cri->select = 't.*, concat("'.ATTR_DOMAIN.'/",t.thumb) AS thumb, g.id as id,t.id as gid';
    	$cri->join = ' LEFT JOIN  '.VendingMachineGoods::tableName().' as g  ON g.goods_id = t.id ';

    	$cri->compare('g.machine_id', $mid);
    	$cri->compare('g.status', VendingMachineGoods::STATUS_ENABLE);
    	$cri->compare('t.status', Goods::STATUS_PASS);

    	$cri->limit = $pageSize;
    	$cri->offset = ($page-1)*$pageSize;
    	
    	$list = Goods::model()->findAll($cri);
    	

    	//遍历取库存
    	$good_ids = array();
    	foreach ($list as $data){
    		$good_ids[] = $data->gid;
    	}
    	 
    	$stocks = ApiStock::goodsStockList($mid, $good_ids,API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID);

        foreach ($list as $key=>$val){
            if(isset($stocks[$val['gid']])){
                $list[$key] = array_merge($val->attributes,$stocks[$val['gid']]);
            }else{
                $list[$key] = $val->attributes;
            }

            $list[$key]['goodsDetails']['content'] = $list[$key]['content'];
            $goodsPic = Yii::app()->db->createCommand()
                ->select('concat("'.IMG_DOMAIN.'/",path) AS path')
                ->from('{{goods_picture}}')
                ->where('goods_id = :id',array(':id'=>$val['gid']))
                ->queryAll();
            if (!empty($goodsPic)) {
                $list[$key]['goodsDetails']['goodsPic'] = $goodsPic;
            }else{
                $list[$key]['goodsDetails']['goodsPic'] = array();
            }
        }
    	
    	$this->_success($list,$tag_name);
    
    }
    
    

    /**
     * 获取售货机商品列表
     *
     *
     */
    public function actionMachineGoodsListV2() {
    	$tag_name = 'GoodsList';
    	$mid = $this->getParam('mid');				//机器id
    	$page = $this->getParam('page')?$this->getParam('page'):1;
    	$pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):100;
    
    	$withStore = $this->getParam('withStore',null);
    	 
    	if (empty($mid)) {
    		$this->_error(Yii::t('apiModule.goods','mid不能为空'),$tag_name);
    	}
    	 
    	if (!empty($withStore)) {
    		$store = VendingMachine::model()->findByPk($mid);
    		if (empty($store)) {
    			$this->_error(Yii::t('apiModule.goods','机器不存在'),$tag_name);
    		}
    		$storeInfo = array();
    
    		$storeInfo['id'] = $store['id'];
    		$storeInfo['name'] = $store['name'];
    		$storeInfo['thumb'] = ATTR_DOMAIN.DS.$store['thumb'];
    		$storeInfo['mobile'] = $store['mobile'];
    		$storeInfo['province_id'] = $store['province_id'];
    		$storeInfo['city_id'] = $store['city_id'];
    		$storeInfo['district_id'] = $store['district_id'];
    		$storeInfo['category_id'] = $store['category_id'];
    		$storeInfo['address'] = $store['address'];
    		$storeInfo['lng'] = $store['lng'];
    		$storeInfo['lat'] = $store['lat'];
    		$storeInfo['status'] = $store['status'];
    
    		$storeInfo['thumb'] = ATTR_DOMAIN.DS.$store['thumb'];
    		$storeInfo['province_name'] = Region::getName($storeInfo['province_id']);
    		$storeInfo['city_name'] = Region::getName($storeInfo['city_id']);
    		$storeInfo['district_name'] = Region::getName($storeInfo['district_id']);
    		$storeCateImg = StoreCategory::getCategoryList($storeInfo['category_id']);
    		$storeInfo['storeCateImg'] = $storeCateImg;
    		
    		$storeInfo['stype'] = Stores::MACHINE;
    		 
    		$data_rs['storeInfo'] = $storeInfo;
    	}
    	 
    	$cri = new CDbCriteria();
    	$cri->select = 't.*, concat("'.ATTR_DOMAIN.'/",t.thumb) AS thumb, g.id as id,t.id as gid';
    	$cri->join = ' LEFT JOIN  '.VendingMachineGoods::tableName().' as g  ON g.goods_id = t.id ';
    
    	$cri->compare('g.machine_id', $mid);
    	$cri->compare('g.status', VendingMachineGoods::STATUS_ENABLE);
    	$cri->compare('t.status', Goods::STATUS_PASS);
    
    	$cri->limit = $pageSize;
    	$cri->offset = ($page-1)*$pageSize;
    	 
    	$list = Goods::model()->findAll($cri);
    	 
    
    	//遍历取库存
    	$good_ids = array();
    	foreach ($list as $data){
    		$good_ids[] = $data->gid;
    	}
    
    	$stocks = ApiStock::goodsStockList($mid, $good_ids,API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID);
    
    	foreach ($list as $key=>$val){
    		if(isset($stocks[$val['gid']])){
    			$list[$key] = array_merge($val->attributes,$stocks[$val['gid']]);
    		}else{
    			$list[$key] = $val->attributes;
    		}
    
    		$list[$key]['goodsDetails']['content'] = $list[$key]['content'];
    		$goodsPic = Yii::app()->db->createCommand()
    		->select('concat("'.IMG_DOMAIN.'/",path) AS path')
    		->from('{{goods_picture}}')
    		->where('goods_id = :id',array(':id'=>$val['gid']))
    		->queryAll();
    		if (!empty($goodsPic)) {
    			$list[$key]['goodsDetails']['goodsPic'] = $goodsPic;
    		}else{
    			$list[$key]['goodsDetails']['goodsPic'] = array();
    		}
    	}
    	 
    	$data_rs['goodsList'] = $list;
    	
    	$this->_success($data_rs,$tag_name);
    
    }
    

    /**
     * 获取超市店铺商品分类列表
     *
     *
     */
    public function actionStoreGoodsCateList() {
    	$tag_name = 'GoodsList';
    	$sid = $this->getParam('sid');				//门店id
    	 $sql = 'SELECT c.id,c.name FROM '.GoodsCategory::model()->tableName(). ' as c  LEFT JOIN  '.Goods::model()->tableName().' as t  ON t.cate_id = c.id LEFT JOIN  '.SuperGoods::model()->tableName().' as g  ON g.goods_id = t.id 
					 WHERE g.super_id = '.$sid.' AND g.status = '.SuperGoods::STATUS_ENABLE.' AND t.status='.Goods::STATUS_PASS .' GROUP BY c.id ' ;
    	 $list= Yii::app()->db->createCommand($sql)->query();
    	$this->_success($list,$tag_name);
        
    }
    
    /**
     * 获取售货机商品列表
     *
     * 按分类分组
     *
     */
    public function actionMachineCateGoodsList() {
    	$tag_name = 'GoodsList';
    	$mid = $this->getParam('mid');				//机器id
    	$page = $this->getParam('page')?$this->getParam('page'):1;
    	$pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):100;
    
    	$machine = VendingMachine::model()->findByPk($mid);

    
    	if (empty($mid) || empty($machine) || $machine->status != VendingMachine::STATUS_ENABLE) {
    		$this->_error(Yii::t('apiModule.goods','售货机不存在'));
    	}
    	 
    	$cri = new CDbCriteria();
    	$cri->select = 't.*, g.id as id ,concat("'.ATTR_DOMAIN.'/",t.thumb) as thumb,t.id as gid';
    	$cri->join = ' LEFT JOIN  '.VendingMachineGoods::tableName().' as g  ON g.goods_id = t.id ';
    
    	$cri->compare('g.machine_id', $mid);
    	$cri->compare('g.status', VendingMachineGoods::STATUS_ENABLE);
    	$cri->compare('t.status', Goods::STATUS_PASS);
    
    	$cri->limit = $pageSize;
    	$cri->offset = ($page-1)*$pageSize;
    	 
    	$list = Goods::model()->findAll($cri);

    	 
    	//遍历取库存
    	$good_ids = array();
    	foreach ($list as $data){
    		$good_ids[] = $data->gid;
    	}
    
    	$stocks = ApiStock::goodsStockList($mid, $good_ids,API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID);

    	foreach ($list as $key=>$val){
            $matches = array();
            $partern ="/<p>(.*?)<\/p>/";
            preg_match_all($partern, $list[$key]['content'], $matches);
            $html= isset($matches[1])?$matches[1]:"";
            $str ="";
            if(!empty($html)){
                foreach($html as $k => $v){
                    $str .= $v;
                }
            }
            if(isset($stocks[$val['gid']])){
                $list[$key] = array_merge($val->attributes,$stocks[$val['gid']]);
            }else{
                $list[$key] = $val->attributes;
            }
            $list[$key]['goodsDetails']['content'] = $str;
            $goodsPic = Yii::app()->db->createCommand()
                ->select('concat("'.IMG_DOMAIN.'/",path) AS path')
                ->from('{{goods_picture}}')
                ->where('goods_id = :id',array(':id'=>$val['gid']))
                ->queryAll();
            if (!empty($goodsPic)) {
                $list[$key]['goodsDetails']['goodsPic'] = $goodsPic;
            }else{
                $list[$key]['goodsDetails']['goodsPic'] = array();
            }
    	}
    	
//     	$cates = GoodsCategory::getGoodsCategoryList($machine->member_id);
       if(!empty($good_ids)){
           $cates = Yii::app()->db->createCommand()
               ->from(GoodsCategory::model()->tableName().' as t')
               ->select('t.*')
               ->leftJoin(Goods::model()->tableName().' as g', 'g.cate_id=t.id')
               ->where('g.id IN('.implode(',', $good_ids).')')
               ->queryAll();

           $cates = CHtml::listData($cates,'id','name');
       }else{
           $cates = array();
       }

    	
    	$rs_list = array();
    	
    	foreach ($list as $val){
    		if (!isset($cates[$val['cate_id']])) {
    			continue;
    		}
    		$rs_list[$val['cate_id']]['cate_name'] = $cates[$val['cate_id']];
    		$rs_list[$val['cate_id']]['cate_id'] =$val['cate_id'];
            unset($val['content']);
    		$rs_list[$val['cate_id']]['goods_items'][] = $val;
    	}
    	 
    	$rs_list = array_values($rs_list);

    	$this->_success($rs_list,$tag_name);
    }
    
    
    
    /**
     * 获取售货机商品列表
     *
     * 按分类分组
     *
     */
    public function actionMachineCateGoodsListV2() {
    	$tag_name = 'GoodsList';
    	$mid = $this->getParam('mid');				//机器id
    	$page = $this->getParam('page')?$this->getParam('page'):1;
    	$pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):100;
    
    	$machine = VendingMachine::model()->findByPk($mid);
    
    	$withStore = $this->getParam('withStore',null);
    	
    
    	if (empty($mid) || empty($machine) || $machine->status != VendingMachine::STATUS_ENABLE) {
    		$this->_error(Yii::t('apiModule.goods','售货机不存在'));
    	}
    	
    	if (!empty($withStore)) {
    		$storeInfo = array();
    
    		$storeInfo['id'] = $machine['id'];
    		$storeInfo['name'] = $machine['name'];
    		$storeInfo['thumb'] = ATTR_DOMAIN.DS.$machine['thumb'];
    		$storeInfo['mobile'] = $machine['mobile'];
    		$storeInfo['province_id'] = $machine['province_id'];
    		$storeInfo['city_id'] = $machine['city_id'];
    		$storeInfo['district_id'] = $machine['district_id'];
    		$storeInfo['category_id'] = $machine['category_id'];
    		$storeInfo['address'] = $machine['address'];
    		$storeInfo['lng'] = $machine['lng'];
    		$storeInfo['lat'] = $machine['lat'];
    		$storeInfo['status'] = $machine['status'];
    
    		$storeInfo['thumb'] = ATTR_DOMAIN.DS.$machine['thumb'];
    		$storeInfo['province_name'] = Region::getName($storeInfo['province_id']);
    		$storeInfo['city_name'] = Region::getName($storeInfo['city_id']);
    		$storeInfo['district_name'] = Region::getName($storeInfo['district_id']);
    		$storeCateImg = StoreCategory::getCategoryList($storeInfo['category_id']);
    		$storeInfo['storeCateImg'] = $storeCateImg;
    		
    		$storeInfo['stype'] = Stores::MACHINE;
    		 
    		$data_rs['storeInfo'] = $storeInfo;
    	}
    	
    
    	$cri = new CDbCriteria();
    	$cri->select = 't.*, g.id as id ,concat("'.ATTR_DOMAIN.'/",t.thumb) as thumb,t.id as gid';
    	$cri->join = ' LEFT JOIN  '.VendingMachineGoods::tableName().' as g  ON g.goods_id = t.id ';
    
    	$cri->compare('g.machine_id', $mid);
    	$cri->compare('g.status', VendingMachineGoods::STATUS_ENABLE);
    	$cri->compare('t.status', Goods::STATUS_PASS);
    
    	$cri->limit = $pageSize;
    	$cri->offset = ($page-1)*$pageSize;
    
    	$list = Goods::model()->findAll($cri);
    
    
    	//遍历取库存
    	$good_ids = array();
    	foreach ($list as $data){
    		$good_ids[] = $data->gid;
    	}
    
    	$stocks = ApiStock::goodsStockList($mid, $good_ids,API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID);
    
    	foreach ($list as $key=>$val){
    		$matches = array();
    		$partern ="/<p>(.*?)<\/p>/";
    		preg_match_all($partern, $list[$key]['content'], $matches);
    		$html= isset($matches[1])?$matches[1]:"";
    		$str ="";
    		if(!empty($html)){
    			foreach($html as $k => $v){
    				$str .= $v;
    			}
    		}
    		if(isset($stocks[$val['gid']])){
    			$list[$key] = array_merge($val->attributes,$stocks[$val['gid']]);
    		}else{
    			$list[$key] = $val->attributes;
    		}
    		$list[$key]['goodsDetails']['content'] = $str;
    		$goodsPic = Yii::app()->db->createCommand()
    		->select('concat("'.IMG_DOMAIN.'/",path) AS path')
    		->from('{{goods_picture}}')
    		->where('goods_id = :id',array(':id'=>$val['gid']))
    		->queryAll();
    		if (!empty($goodsPic)) {
    			$list[$key]['goodsDetails']['goodsPic'] = $goodsPic;
    		}else{
    			$list[$key]['goodsDetails']['goodsPic'] = array();
    		}
    	}
    	 
    	//     	$cates = GoodsCategory::getGoodsCategoryList($machine->member_id);
    	if(!empty($good_ids)){
    		$cates = Yii::app()->db->createCommand()
    		->from(GoodsCategory::model()->tableName().' as t')
    		->select('t.*')
    		->leftJoin(Goods::model()->tableName().' as g', 'g.cate_id=t.id')
    		->where('g.id IN('.implode(',', $good_ids).')')
    		->queryAll();
    
    		$cates = CHtml::listData($cates,'id','name');
    	}else{
    		$cates = array();
    	}
    
    	 
    	$rs_list = array();
    	 
    	foreach ($list as $val){
    		if (!isset($cates[$val['cate_id']])) {
    			continue;
    		}
    		$rs_list[$val['cate_id']]['cate_name'] = $cates[$val['cate_id']];
    		$rs_list[$val['cate_id']]['cate_id'] =$val['cate_id'];
    		unset($val['content']);
    		$rs_list[$val['cate_id']]['goods_items'][] = $val;
    	}
    
    	$rs_list = array_values($rs_list);
    	
    	$data_rs['goodsList'] = $rs_list;
    
    	$this->_success($data_rs,$tag_name);
    }
    
    
    /**
     * 获取超市店铺商品分类列表
     *
     *
     */
    public function actionMachineGoodsCateList() {
    	$tag_name = 'GoodsList';
    	$mid = $this->getParam('mid');				//门店id
    	$sql = 'SELECT c.id,c.name FROM '.GoodsCategory::tableName(). ' as c  LEFT JOIN  '.Goods::tableName().' as t  ON t.cate_id = c.id LEFT JOIN  '.VendingMachineGoods::tableName().' as g  ON g.goods_id = t.id
					 WHERE g.machine_id = '.$mid.' AND g.status = '.VendingMachineGoods::STATUS_ENABLE.' AND t.status='.Goods::STATUS_PASS .' GROUP BY c.id ' ;
    	$list= Yii::app()->db->createCommand($sql)->query();
    
    	$this->_success($list,$tag_name);
    }
    
    /**
     * 获取商品评价
     */
    public function actionGoodsComment(){
        $gid  = $this->getParam('gid');
        $page = $this->getParam('page')?$this->getParam('page'):1;
        $pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):100;
        
        $cri = new CDbCriteria();
        $cri->select = '*';    	
        $cri->compare('goods_id', $gid);	//分页
        $cri->limit = $pageSize;
        $cri->offset = ($page-1)*$pageSize;
    	
        $list = GoodsComment::model()->findAll($cri);
        if(empty($list)){
             $this->_error(Yii::t('apiModule.goods','该商品暂无评价'));
        }
        $data = array();
        foreach ($list as $k=>$v){
            $data[$k]['content'] = $v['content'];
            $data[$k]['score'] = $v['score'];
            $data[$k]['service_score'] = $v['service_score'];
            $data[$k]['quality_score'] = $v['quality_score'];
            $data[$k]['member_id'] = $v['member_id'];
        }
        $this->_success($data,'GoodsComment');
      
    }
    
        /**
     * 获取售货机商品列表
     *
     * 按分类分组
     *
     */
    public function actionFreshMachineGoodsList() {
    	$tag_name = 'FreshMachineGoodsList';
    	$mid = $this->getParam('mid')*1;				//机器id
    	$page = $this->getParam('page')?$this->getParam('page'):1;
    	$pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):100;
    	$withStore = $this->getParam('withStore',null);
    	
    	$is_one = $this->getParam('is_one');						//是否一元购
    	$is_for = $this->getParam('is_for');							//是否促销
    	$is_promo = $this->getParam('is_promo');				//是否促销
    
    	$machine = FreshMachine::model()->findByPk($mid);
    	if (empty($mid) || empty($machine) || $machine->status != FreshMachine::STATUS_ENABLE) {
    		$this->_error(Yii::t('apiModule.goods','生鲜机机不存在'));
    	}
    	
    	
    	if (!empty($withStore)) {
    		$storeInfo = array();
    	
    		$storeInfo['id'] = $machine['id'];
    		$storeInfo['name'] = $machine['name'];
    		$storeInfo['thumb'] = $machine['thumb'];
    		$storeInfo['province_id'] = $machine['province_id'];
    		$storeInfo['city_id'] = $machine['city_id'];
    		$storeInfo['district_id'] = $machine['district_id'];
    		$storeInfo['category_id'] = $machine['category_id'];
    		$storeInfo['address'] = $machine['address'];
    		$storeInfo['lng'] = $machine['lng'];
    		$storeInfo['lat'] = $machine['lat'];
    		$storeInfo['status'] = $machine['status'];
    	
    		$storeInfo['thumb'] = ATTR_DOMAIN.DS.$machine['thumb'];
    		$storeInfo['province_name'] = Region::getName($storeInfo['province_id']);
    		$storeInfo['city_name'] = Region::getName($storeInfo['city_id']);
    		$storeInfo['district_name'] = Region::getName($storeInfo['district_id']);
    		$storeCateImg = StoreCategory::getCategoryList($storeInfo['category_id']);
    		$storeInfo['storeCateImg'] = $storeCateImg;
    		 
    		$data_rs['storeInfo'] = $storeInfo;
    	}
    	
    	$where = 'g.machine_id='.$mid.' AND g.status='.FreshMachineGoods::STATUS_ENABLE.' AND t.status='.Goods::STATUS_PASS;
    	if (!empty($is_one)) $where .= ' AND t.is_one='.Goods::IS_ONE;
    	if (!empty($is_for)) $where .= ' AND t.is_for='.Goods::IS_FOR;
    	if (!empty($is_promo)) $where .= ' AND t.is_promo='.Goods::IS_PROMO;
    	
    	$list = Yii::app()->db->createCommand()
    	->select( 't.*, g.id as id ,concat("'.ATTR_DOMAIN.'/",t.thumb) as thumb,t.id as gid,t.is_one,t.is_for,t.is_promo,g.line_id')
    	->from(Goods::model()->tableName().' as t')
    	->leftJoin(FreshMachineGoods::model()->tableName().' as g', 'g.goods_id = t.id')
    	->where($where)
    	->limit($pageSize)
    	->offset(($page-1)*$pageSize)
    	->queryAll();
    	 
    	if (!empty($list)) {
    		//遍历取库存
    		$line_ids = array();
    		$good_ids = array();
    		foreach ($list as $data){
    			$line_ids[] = $data['line_id'];
    			$good_ids[] = $data['gid'];
    		}
    		
    		$stocks = ApiStock::goodsStockList($mid, $line_ids,API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);

    		foreach ($list as $key=>$val){
                $matches = array();
                $partern ="/<p>(.*?)<\/p>/";
                preg_match_all($partern, $list[$key]['content'], $matches);
                $html= isset($matches[1])?$matches[1]:"";
                $str ="";
                if(!empty($html)){
                    foreach($html as $k => $v){
                        $str .= $v;
                    }
                }
                if(isset($stocks[$val['line_id']])){
                    $list[$key] = array_merge($val,$stocks[$val['line_id']]);
                }else{
                    $list[$key] = $val;
                }
                $list[$key]['goodsDetails']['content'] = $str;
                $goodsPic = Yii::app()->db->createCommand()
                    ->select('concat("'.IMG_DOMAIN.'/",path) AS path')
                    ->from('{{goods_picture}}')
                    ->where('goods_id = :id',array(':id'=>$val['gid']))
                    ->queryAll();
                if (!empty($goodsPic)) {
                    $list[$key]['goodsDetails']['goodsPic'] = $goodsPic;
                }else{
                    $list[$key]['goodsDetails']['goodsPic'] = array();
                }
    		}

    		 
    		//     	$cates = GoodsCategory::getGoodsCategoryList($machine->member_id);
         if(!empty($good_ids)){
             $cates = Yii::app()->db->createCommand()
                 ->from(GoodsCategory::model()->tableName().' as t')
                 ->select('t.*')
                 ->leftJoin(Goods::model()->tableName().' as g', 'g.cate_id=t.id')
                 ->where('g.id IN('.implode(',', $good_ids).')')
                 ->queryAll();
             $cates = CHtml::listData($cates,'id','name');
         }else{
             $cates = array();
         }

    		 
    		$rs_list = array();
    		foreach ($list as $val){
    			if (!isset($cates[$val['cate_id']])) {
    				continue;
    			}
    			$rs_list[$val['cate_id']]['cate_name'] = $cates[$val['cate_id']];
    			$rs_list[$val['cate_id']]['cate_id'] =$val['cate_id'];
                unset($val['content']);
    			$rs_list[$val['cate_id']]['goods_items'][] = $val;
    		}
    		
    		$rs_list = array_values($rs_list);
    	}else{
    		$rs_list = $list;
    	}

    	$this->_success($rs_list,$tag_name);
    }
    
    
    
    public function actionFreshMachineGoodsListV2() {

    	$tag_name = 'FreshMachineGoodsList';
    	$mid = $this->getParam('mid')*1;				//机器id
    	$page = $this->getParam('page')?$this->getParam('page'):1;
    	$pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):100;
    	$withStore = $this->getParam('withStore',null);
    	 
    	$is_one = $this->getParam('is_one');						//是否一元购
    	$is_for = $this->getParam('is_for');							//是否促销
    	$is_promo = $this->getParam('is_promo');				//是否促销
    
    	$machine = FreshMachine::model()->findByPk($mid);
    	if (empty($mid) || empty($machine) || $machine->status != FreshMachine::STATUS_ENABLE) {
    		$this->_error(Yii::t('apiModule.goods','生鲜机机不存在'));
    	}
      
    	 
    	if (!empty($withStore)) {
    		$storeInfo = array();
    		 
    		$storeInfo['id'] = $machine['id'];
    		$storeInfo['name'] = $machine['name'];
    		$storeInfo['thumb'] = ATTR_DOMAIN.DS.$machine['thumb'];
    		$storeInfo['mobile'] = $machine['mobile'];
    		$storeInfo['province_id'] = $machine['province_id'];
    		$storeInfo['city_id'] = $machine['city_id'];
    		$storeInfo['district_id'] = $machine['district_id'];
    		$storeInfo['category_id'] = $machine['category_id'];
    		$storeInfo['address'] = $machine['address'];
    		$storeInfo['lng'] = $machine['lng'];
    		$storeInfo['lat'] = $machine['lat'];
    		$storeInfo['status'] = $machine['status'];
    		 
    		$storeInfo['thumb'] = ATTR_DOMAIN.DS.$machine['thumb'];
    		$storeInfo['province_name'] = Region::getName($storeInfo['province_id']);
    		$storeInfo['city_name'] = Region::getName($storeInfo['city_id']);
    		$storeInfo['district_name'] = Region::getName($storeInfo['district_id']);
    		$storeCateImg = StoreCategory::getCategoryList($storeInfo['category_id']);
    		$storeInfo['storeCateImg'] = $storeCateImg;
    		
    		$storeInfo['stype'] = ($machine['type'] == FreshMachine::FRESH_MACHINE_SMALL)?Stores::FRESH_MACHINE_SMALL:Stores::FRESH_MACHINE;
    		 
    		$data_rs['storeInfo'] = $storeInfo;
    	}
    	 
    	$where = 'g.machine_id='.$mid.' AND g.status='.FreshMachineGoods::STATUS_ENABLE.' AND t.status='.Goods::STATUS_PASS;
    	if (!empty($is_one)) $where .= ' AND t.is_one='.Goods::IS_ONE;
    	if (!empty($is_for)) $where .= ' AND t.is_for='.Goods::IS_FOR;
    	if (!empty($is_promo)) $where .= ' AND t.is_promo='.Goods::IS_PROMO;
    	 
    	$list = Yii::app()->db->createCommand()
    	->select( 't.*, g.id as id ,concat("'.ATTR_DOMAIN.'/",t.thumb) as thumb,t.id as gid,t.is_one,t.is_for,t.is_promo,g.line_id')
    	->from(Goods::model()->tableName().' as t')
    	->leftJoin(FreshMachineGoods::model()->tableName().' as g', 'g.goods_id = t.id')
    	->where($where)
    	->limit($pageSize)
    	->offset(($page-1)*$pageSize)
    	->queryAll();

    	if (!empty($list)) {
            
            //遍历取库存
    		$line_ids = array();
    		$good_ids = array();
    		foreach ($list as $data){
    			$line_ids[] = $data['line_id'];
    			$good_ids[] = $data['gid'];
    		}
    		$stocks = ApiStock::goodsStockList($mid, $line_ids,API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
                $stock1 = $stocks;

                foreach($list as $v){
                    $stock1[$v['line_id']]['gid'] = $v['gid'];
                }

                //小屏生鲜机
       if($machine['type'] == FreshMachine::FRESH_MACHINE_SMALL){
            $gids = array();
            foreach($list as $k=>$gid){
                $gids[$k] = $gid['gid'];
            }
            $gids = array_unique($gids);
            foreach ($gids as $k=>$v){
                $gids[$k] = $list[$k];
            }
            $list = $gids;
        }

    		foreach ($list as $key=>$val){
    			$matches = array();
    			$partern ="/<p>(.*?)<\/p>/";
    			preg_match_all($partern, $list[$key]['content'], $matches);
    			$html= isset($matches[1])?$matches[1]:"";
    			$str ="";
    			if(!empty($html)){
    				foreach($html as $k => $v){
    					$str .= $v;
    				}
    			}
                        if($machine['type']!=FreshMachine::FRESH_MACHINE_SMALL){
    			if(isset($stocks[$val['line_id']])){
    				$list[$key] = array_merge($val,$stocks[$val['line_id']]);
    			}else{
    				$list[$key] = $val;
    			}
                        }
    			$list[$key]['goodsDetails']['content'] = $str;
    			$goodsPic = Yii::app()->db->createCommand()
    			->select('concat("'.IMG_DOMAIN.'/",path) AS path')
    			->from('{{goods_picture}}')
    			->where('goods_id = :id',array(':id'=>$val['gid']))
    			->queryAll();
    			if (!empty($goodsPic)) {
    				$list[$key]['goodsDetails']['goodsPic'] = $goodsPic;
    			}else{
    				$list[$key]['goodsDetails']['goodsPic'] = array();
    			}
    		}
    
    		 
    		//     	$cates = GoodsCategory::getGoodsCategoryList($machine->member_id);
    		if(!empty($good_ids)){
    			$cates = Yii::app()->db->createCommand()
    			->from(GoodsCategory::model()->tableName().' as t')
    			->select('t.*')
    			->leftJoin(Goods::model()->tableName().' as g', 'g.cate_id=t.id')
    			->where('g.id IN('.implode(',', $good_ids).')')
    			->queryAll();
    			$cates = CHtml::listData($cates,'id','name');
    		}else{
    			$cates = array();
    		}
                   
                //小屏生鲜机
                if($machine['type'] == FreshMachine::FRESH_MACHINE_SMALL){
                                foreach($list as $k=>$v){
                                    $list[$k]['stock'] = 0;
                                    foreach($stock1 as $k1=>$v1){
                                        if($v['gid'] == $v1['gid']){                               
                                           $list[$k]['stock'] += $v1['stock']*1; //同种商品库存总数
                                        }
                                    }
                                }
                }

    		$rs_list = array();

    		foreach ($list as $val){
    			if (!isset($cates[$val['cate_id']])) {
    				continue;
    			}
    			$rs_list[$val['cate_id']]['cate_name'] = $cates[$val['cate_id']];
    			$rs_list[$val['cate_id']]['cate_id'] =$val['cate_id'];
    			unset($val['content']);
    			$rs_list[$val['cate_id']]['goods_items'][] = $val;
    		}
    
    		$rs_list = array_values($rs_list);
    	}else{
    		$rs_list = $list;
    	}
    	
    	$data_rs['goodsList'] = $rs_list;
    
    	$this->_success($data_rs,$tag_name);
    } 
   
    
    /**
     * 商品详情接口
     * 
     * 
     * 
     */
    public function actionGoodsDetail(){
    	$id  = $this->getParam('id');
    	$sid  = $this->getParam('sid');		//门店id
    	$stype  = $this->getParam('stype');				//门店类型
    	
    	$tag_name = 'GoodsDetail';
    	
    	$storeClass = self::getStoreClass($stype);
    	$storeGoodsClass = self::getStoreGoodsClass($stype);
    	
    	if (empty($storeClass)||empty($storeGoodsClass)) {
    		$this->_error(Yii::t('apiModule.goods','参数错误'),$tag_name);
    	}
    	
    	$cri = new CDbCriteria();
//     	$cri->select = 't.name,t.sec_title,t.is_one,is_promo,is_for,t.barcode,t.price,t.supply_price';
    	$cri->select = 't.name,t.sec_title,t.is_one,is_promo,is_for,t.barcode,t.price,t.supply_price, t.thumb,t.content,t.create_time,t.id as goods_id';
    	if ( self::getStoreProjectId($stype)==API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID) {
    		$cri->select .= ',sg.line_id';
    	}
    	$cri->with = 'goodsPicture';

    	$cri->join .= ' LEFT JOIN '.$storeGoodsClass::model()->tableName().' AS sg ON sg.goods_id=t.id ';
    	$cri->compare('sg.id', $id);
    	
    	$goods = Goods::model()->find($cri);
    	
    	$goods_id = $goods['goods_id'];
    	if (empty($goods)) {
    		$this->_error(Yii::t('apiModule.goods','商品不存在'),$tag_name);
    	}

    	$apiStock = new ApiStock();
    	$stock = ApiStock::goodsStockOne($sid, self::getStoreProjectId($stype)==API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID?$goods['line_id']:$goods_id,self::getStoreProjectId($stype));
    	$goods['stock'] = $stock['result']['stock'];
		
    	$rs['detail'] = $goods;
    	$rs['goodsPicture'] = array();
    	
    	$rs['detail']['thumb'] = ATTR_DOMAIN.DS.$rs['detail']['thumb'];
    	if (!empty($goods->goodsPicture)) {
    		foreach ($goods->goodsPicture as $p){
    			$rs['goodsPicture'][] = IMG_DOMAIN.DS.$p['path'];
    		}
    	}
    	
    	$this->_success($rs,$tag_name);
    	
    }
    
    
    /**
     * 第三版首页
     */
    //微小企业第三版首页接口

    public function actionIndexV3(){
        try{

            //获取用户1.5km以内的店铺及店铺最近更新库存的4件商品
            $lat = $this->getParam('lat');   //经度
            $lng = $this->getParam('lng');   //纬度
            $default_distance = Tool::getConfig('site','api_distance')*1;
        	$distance = $default_distance>0?$default_distance:($this->getParam('distance') ? $this->getParam('distance') : 1000);   //距离
            $page = $this->getParam('page') ? $this->getParam('page') : 1;
            $pageSize = $this->getParam('pageSize') ? $this->getParam('pageSize') : 6;
            
            
            //数据按位置局部缓存
            $cache_lat = sprintf("%.3f", $lat);
            $cache_lng = sprintf("%.3f", $lng);
            
            $cache_key = 'SearchV3'.$cache_lat.$cache_lng.$distance.$page.$pageSize;
            $cache_time = 900;
            $cache_data = Tool::cache(Goods::CACHE_DIR_API_CGOODS_INDEX)->get($cache_key);
            

            if (empty($cache_data)) {

            	//获取店铺分类
            	$storeCateData = StoreCategory::getCategoryList();
            	$storeCateData = array_values($storeCateData);
            	
            	$mysql_select = 't.stype,
        							s.id as s_id,s.name as s_name,s.mobile as s_mobile, CONCAT("' . ATTR_DOMAIN . '/",s.logo) as s_logo,s.province_id as s_province_id,s.city_id as s_city_id,s.district_id as s_district_id,s.street as s_street,s.zip_code as s_zip_code,s.lng as s_lng,s.lat as s_lat,s.is_delivery as s_is_delivery,s.category_id as s_category_id,s.delivery_mini_amount as s_delivery_mini_amount,s.delivery_start_amount as s_delivery_start_amount,s.delivery_fee as s_delivery_fee,s.star as s_star,s.open_time as s_open_time,s.is_recommend as s_is_recommend,s.max_amount_preday as s_max_amount_preday,s.status as s_status,
        							m.id as m_id,m.name as m_name, CONCAT("' . ATTR_DOMAIN . '/",m.thumb) as m_thumb,m.province_id as m_province_id,m.category_id as m_category_id,m.city_id as m_city_id,m.district_id as m_district_id,m.address as m_address,m.lng as m_lng,m.lat as m_lat,m.status as m_status';
            	
            	$mysql_select .=',f.id as f_id,f.name as f_name,f.type as f_type, CONCAT("' . ATTR_DOMAIN . '/",f.thumb) as f_thumb,f.province_id as f_province_id,f.category_id as f_category_id,f.city_id as f_city_id,f.district_id as f_district_id,f.address as f_address,f.lng as f_lng,f.lat as f_lat,f.status as f_status';
            	
            	$conditions = ' 1=1 ';
            	$order = '';
            	if (!empty($lat) && !empty($lng)) {
            		$vicinity_rs = Tool::GetRange($lat, $lng, $distance);
            		if ($vicinity_rs['maxLat'] > $vicinity_rs['minLat']) {
            			$conditions .= ' AND  ( t.lat  BETWEEN "'.$vicinity_rs['minLat'].'" AND "'.$vicinity_rs['maxLat'].'") ';
            		} else {
            			$conditions .= ' AND  ( t.lat  BETWEEN "'.$vicinity_rs['maxLat'].'" AND "'.$vicinity_rs['minLat'].'") ';
            		}
            		if ($vicinity_rs['maxLng'] > $vicinity_rs['minLng']) {
            	
            			$conditions .= ' AND  ( t.lng  BETWEEN "'.$vicinity_rs['minLng'].'" AND "'.$vicinity_rs['maxLng'].'") ';
            		} else {
            			$conditions .= ' AND  ( t.lng  BETWEEN "'.$vicinity_rs['maxLng'].'" AND "'.$vicinity_rs['minLng'].'") ';
            		}
            	
            		$mysql_select .= ',getDistance('.$lng.','.$lat.',t.lng,t.lat) as distance  ';
            	
            		$order = 'distance ASC';
            	
            	}
            	$conditions .= ' AND t.status ='.Stores::STATUS_ENABLE;
            	$conditions .= ' AND p.status ='.  Partners::STATUS_ENABLE;
            	$data = Yii::app()->db->createCommand()
            	->select($mysql_select)
            	->where($conditions)
            	->from(Stores::model()->tableName().' as t')
            	->leftJoin(Supermarkets::model()->tableName().' as s', 't.target_id=s.id')
            	->leftJoin(VendingMachine::model()->tableName().' as m', 't.target_id=m.id')
            	->leftJoin(FreshMachine::model()->tableName().' as f', 't.target_id=f.id')
            	->leftJoin(Partners::model()->tableName().' as p', 'm.partner_id=p.id or s.partner_id=p.id');
            	$data = $data->order($order)
            	->group('t.id')
            	->limit($pageSize)
            	->offset(($page - 1) * $pageSize)
            	->queryAll();
            	
            	$list = array();
            	foreach ($data as $k => $val){
            		$temp_arr = array();
            		$temp_arr['stype'] = $val['stype'];
            		$temp_arr['distance'] = isset($val['distance'])?round($val['distance']):0;
            		$storeGoodsClass = self::getStoreGoodsClass($val['stype']);
            	
            		if ($val['stype']==Stores::SUPERMARKETS) {
            			$temp_arr['id'] = $val['s_id'];
            			$temp_arr['name'] = $val['s_name'];
            			$temp_arr['mobile'] = $val['s_mobile'];
            			$temp_arr['logo'] = $val['s_logo'];
            			$temp_arr['province_id'] = $val['s_province_id'];
            			$temp_arr['city_id'] = $val['s_city_id'];
            			$temp_arr['district_id'] = $val['s_district_id'];
            			$temp_arr['street'] = $val['s_street'];
            			$temp_arr['zip_code'] = $val['s_zip_code'];
            			$temp_arr['lng'] = $val['s_lng'];
            			$temp_arr['lat'] = $val['s_lat'];
            			$temp_arr['is_delivery'] = $val['s_is_delivery'];
            			$temp_arr['category_id'] = $val['s_category_id'];
            			$temp_arr['delivery_mini_amount'] = $val['s_delivery_mini_amount'];
            			$temp_arr['delivery_start_amount'] = $val['s_delivery_start_amount'];
            			$temp_arr['delivery_fee'] = $val['s_delivery_fee'];
            			$temp_arr['star'] = $val['s_star'];
            			$temp_arr['open_time'] = $val['s_open_time'];
            			$temp_arr['is_recommend'] = $val['s_is_recommend'];
            			$temp_arr['max_amount_preday'] = $val['s_max_amount_preday'];
            			$temp_arr['status'] = $val['s_status'];
            			$temp_arr['preferential'] = array();
            			if($temp_arr['is_delivery'] == Supermarkets::DELIVERY_YES){
            				$preferential = array();
            				$preferential['info'] = $val['s_delivery_mini_amount']==0?"免配送费":"满".$val['s_delivery_mini_amount'].",免配送费";
            				$preferential['img'] = ATTR_DOMAIN;
            				$temp_arr['preferential'][] = $preferential;
            			}
            	
            	
            	
            			$storeGoods = Yii::app()->db->createCommand()
            			->select('g.name,sg.id id,concat("'.ATTR_DOMAIN.'/",g.thumb) AS thumb')
            			->from('{{goods_stock}} as t')
            			->leftjoin('{{goods}} as g','g.id = t.target')
            			->leftjoin($storeGoodsClass::model()->tableName().' as sg','g.id = sg.goods_id')
            			->where(" sg.super_id = :id AND sg.id > 0 AND g.status =".SuperGoods::STATUS_ENABLE." AND sg.status = ".Goods::STATUS_PASS,array(':id'=>$temp_arr['id']))
            			->limit('4')
            			->order('t.create_time desc')
            			->group('sg.id')
            			->queryAll();
            	
            		}elseif ($val['stype']==Stores::MACHINE ){
            			$temp_arr['id'] = $val['m_id'];
            			$temp_arr['name'] = $val['m_name'];
            			$temp_arr['thumb'] = $val['m_thumb'];
            			$temp_arr['province_id'] = $val['m_province_id'];
            			$temp_arr['city_id'] = $val['m_city_id'];
            			$temp_arr['district_id'] = $val['m_district_id'];
            			$temp_arr['category_id'] = $val['m_category_id'];
            			$temp_arr['address'] = $val['m_address'];
            			$temp_arr['lng'] = $val['m_lng'];
            			$temp_arr['lat'] = $val['m_lat'];
            			$temp_arr['status'] = $val['m_status'];
            			$temp_arr['preferential'] = array();
            			$storeGoods = Yii::app()->db->createCommand()
            			->select('g.name,sg.id id,concat("'.ATTR_DOMAIN.'/",g.thumb) AS thumb')
            			->from('{{goods_stock}} as t')
            			->leftjoin('{{goods}} as g','g.id = t.target')
            			->leftjoin($storeGoodsClass::model()->tableName().' as sg','g.id = sg.goods_id')
            			->where("sg.machine_id =:id AND sg.id > 0 AND g.status =".VendingMachineGoods::STATUS_ENABLE." AND sg.status = ".Goods::STATUS_PASS,array(':id'=>$temp_arr['id']))
            			->limit('4')
            			->order('t.create_time desc')
            			->group('sg.id')
            			->queryAll();
            		}elseif ($val['stype']==Stores::FRESH_MACHINE){
            			$temp_arr['id'] = $val['f_id'];
            			$temp_arr['name'] = $val['f_name'];
            			$temp_arr['thumb'] = $val['f_thumb'];
            			$temp_arr['province_id'] = $val['f_province_id'];
            			$temp_arr['city_id'] = $val['f_city_id'];
            			$temp_arr['district_id'] = $val['f_district_id'];
            			$temp_arr['category_id'] = $val['f_category_id'];
            			$temp_arr['address'] = $val['f_address'];
            			$temp_arr['lng'] = $val['f_lng'];
            			$temp_arr['lat'] = $val['f_lat'];
            			$temp_arr['status'] = $val['f_status'];
                                $temp_arr['stype'] = ($val['f_type'] == FreshMachine::FRESH_MACHINE_SMALL)? Stores::FRESH_MACHINE_SMALL:$val['stype'];
            			$temp_arr['preferential'] = array();
                                
            			$storeGoods = Yii::app()->db->createCommand()
            			->select('g.name,sg.id id,concat("'.ATTR_DOMAIN.'/",g.thumb) AS thumb')
            			->from('{{goods_stock}} as t')
            			->leftjoin('{{goods}} as g','g.id = t.target')
            			->leftjoin($storeGoodsClass::model()->tableName().' as sg','g.id = sg.goods_id')
            			->where("sg.machine_id =:id AND sg.id > 0 AND g.status =".FreshMachineGoods::STATUS_ENABLE." AND sg.status = ".Goods::STATUS_PASS,array(':id'=>$temp_arr['id']))
            			->limit('4')
            			->order('t.create_time desc')
            			->group('sg.id')
            			->queryAll();
            		}
            	
            		//获取店铺分类图标
            		$storeCateImg = StoreCategory::getCategoryList($temp_arr['category_id']);
            		$temp_arr['storeCateImg'] = $storeCateImg;
            		$temp_arr['province_name'] = Region::getName($temp_arr['province_id']);
            		$temp_arr['city_name'] = Region::getName($temp_arr['city_id']);
            		$temp_arr['district_name'] = Region::getName($temp_arr['district_id']);
            	
            		$temp_arr['goods'] = isset($storeGoods)?$storeGoods:array();
            	
            		if (!isset($list[$temp_arr['stype'].'_'.$temp_arr['id']])) $list[$temp_arr['stype'].'_'.$temp_arr['id']] = $temp_arr;
            	
            	}
            	
            	$list = array_values($list);
            	
            	$cache_data['list'] = $list;
            	$cache_data['storeCate'] = $storeCateData;
            	
            	Tool::cache(Goods::CACHE_DIR_API_CGOODS_INDEX)->set($cache_key,$cache_data,$cache_time);
            }
           
            $list = isset($cache_data['list'])?$cache_data['list']:array();
            $storeCateData = isset($cache_data['storeCate'])?$cache_data['storeCate']:array();
            
            $this->_success(array('store'=>$list,'storeCate'=>$storeCateData));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };


    }

    /**
     * 第三版按分类标签店铺列表
     */

    public function actionStoreCateListV3(){
        try{
            //获取用户1.5km以内的店铺及店铺最近更新库存的4件商品
            $lat = $this->getParam('lat');   //经度
            $lng = $this->getParam('lng');   //纬度
            $default_distance = Tool::getConfig('site','api_distance')*1;
        	$distance = $default_distance>0?$default_distance:($this->getParam('distance') ? $this->getParam('distance') : 1000);   //距离
            $page = $this->getParam('page') ? $this->getParam('page') : 1;
            $pageSize = $this->getParam('pageSize') ? $this->getParam('pageSize') : 6;
            $stroeCateId = $this->getParam('stroeCateId');
            $sort = $this->getParam('sort');//排序  defaultSort 默认   sales 销量  distance  距离  delivery 送货上门  send O元起送
            $mysql_select = 't.stype,t.solds,
        							s.id as s_id,s.name as s_name,s.is_delivery as is_delivery,s.delivery_start_amount as delivery_start_amount,s.mobile as s_mobile, CONCAT("' . ATTR_DOMAIN . '/",s.logo) as s_logo,s.province_id as s_province_id,s.city_id as s_city_id,s.district_id as s_district_id,s.street as s_street,s.zip_code as s_zip_code,s.lng as s_lng,s.lat as s_lat,s.is_delivery as s_is_delivery,s.category_id as s_category_id,s.delivery_mini_amount as s_delivery_mini_amount,s.delivery_start_amount as s_delivery_start_amount,s.delivery_fee as s_delivery_fee,s.star as s_star,s.open_time as s_open_time,s.is_recommend as s_is_recommend,s.max_amount_preday as s_max_amount_preday,s.status as s_status,
        							m.id as m_id,m.solds m_solds,m.name as m_name, CONCAT("' . ATTR_DOMAIN . '/",m.thumb) as m_thumb,m.province_id as m_province_id,m.category_id as m_category_id,m.city_id as m_city_id,m.district_id as m_district_id,m.address as m_address,m.lng as m_lng,m.lat as m_lat,m.status as m_status';

            $mysql_select .=',f.id as f_id,f.solds f_solds,f.name as f_name, CONCAT("' . ATTR_DOMAIN . '/",f.thumb) as f_thumb,f.province_id as f_province_id,f.category_id as f_category_id,f.city_id as f_city_id,f.district_id as f_district_id,f.address as f_address,f.lng as f_lng,f.lat as f_lat,f.status as f_status';

            $conditions = ' 1=1 ';
            $order = '';
            if (!empty($lat) && !empty($lng)) {
                $vicinity_rs = Tool::GetRange($lat, $lng, $distance);
                if ($vicinity_rs['maxLat'] > $vicinity_rs['minLat']) {
                    $conditions .= ' AND  ( t.lat  BETWEEN "'.$vicinity_rs['minLat'].'" AND "'.$vicinity_rs['maxLat'].'") ';
                } else {
                    $conditions .= ' AND  ( t.lat  BETWEEN "'.$vicinity_rs['maxLat'].'" AND "'.$vicinity_rs['minLat'].'") ';
                }
                if ($vicinity_rs['maxLng'] > $vicinity_rs['minLng']) {

                    $conditions .= ' AND  ( t.lng  BETWEEN "'.$vicinity_rs['minLng'].'" AND "'.$vicinity_rs['maxLng'].'") ';
                } else {
                    $conditions .= ' AND  ( t.lng  BETWEEN "'.$vicinity_rs['maxLng'].'" AND "'.$vicinity_rs['minLng'].'") ';
                }

                $mysql_select .= ',getDistance('.$lng.','.$lat.',t.lng,t.lat) as distance  ';
                $order = 'distance ASC';
                if($sort == "defaultSort"){
                   $order = 'distance ASC';
                }
                if($sort == "sales"){
                    $order = 'solds DESC';
                }
                if($sort == "distance"){
                    $order = 'distance ASC';
                }
                if($sort == "delivery"){
                    $conditions .= " AND t.stype =".Stores::SUPERMARKETS." AND is_delivery =".Supermarkets::DELIVERY_YES;
                }
                if($sort == "send" ){
                    $conditions .= " AND t.stype =".Stores::SUPERMARKETS." AND is_delivery =".Supermarkets::DELIVERY_YES." AND delivery_start_amount = 0 ";
                }
            }
            $conditions .= ' AND t.status ='.Stores::STATUS_ENABLE;
            $conditions .= ' AND p.status ='.  Partners::STATUS_ENABLE;
            if(!empty($stroeCateId)){//
                $conditions .= " AND (s.category_id = ".$stroeCateId." OR m.category_id = ".$stroeCateId." OR f.category_id = ".$stroeCateId.")";
            }
            $data = Yii::app()->db->createCommand()
                ->select($mysql_select)
                ->where($conditions)
                ->from(Stores::model()->tableName().' as t')
                ->leftJoin(Supermarkets::model()->tableName().' as s', 't.target_id=s.id')
                ->leftJoin(VendingMachine::model()->tableName().' as m', 't.target_id=m.id')
                ->leftJoin(FreshMachine::model()->tableName().' as f', 't.target_id=f.id')
                ->leftJoin(Partners::model()->tableName().' as p', 'm.partner_id=p.id or s.partner_id=p.id');
            $data = $data->order($order)
                ->group('t.id')
                ->limit($pageSize)
                ->offset(($page - 1) * $pageSize)
                ->queryAll();
            $list = array();
            foreach ($data as $k => $val){
                $temp_arr = array();
                $temp_arr['stype'] = $val['stype'];
                $temp_arr['distance'] = isset($val['distance'])?round($val['distance']):0;
                $temp_arr['solds'] = $val['solds'];

                if ($val['stype']==Stores::SUPERMARKETS) {
                    if(!empty($stroeCateId)){
                        if($val['s_category_id'] != $stroeCateId) continue;
                    }
                    $temp_arr['id'] = $val['s_id'];
                    $temp_arr['category_id'] = $val['s_category_id'];
                    $temp_arr['name'] = $val['s_name'];
                    $temp_arr['mobile'] = $val['s_mobile'];
                    $temp_arr['logo'] = $val['s_logo'];
                    $temp_arr['province_id'] = $val['s_province_id'];
                    $temp_arr['city_id'] = $val['s_city_id'];
                    $temp_arr['district_id'] = $val['s_district_id'];
                    $temp_arr['street'] = $val['s_street'];
                    $temp_arr['zip_code'] = $val['s_zip_code'];
                    $temp_arr['lng'] = $val['s_lng'];
                    $temp_arr['lat'] = $val['s_lat'];
                    $temp_arr['is_delivery'] = $val['s_is_delivery'];
                    $temp_arr['category_id'] = $val['s_category_id'];
                    $temp_arr['delivery_mini_amount'] = $val['s_delivery_mini_amount'];
                    $temp_arr['delivery_start_amount'] = $val['s_delivery_start_amount'];
                    $temp_arr['delivery_fee'] = $val['s_delivery_fee'];
                    $temp_arr['star'] = $val['s_star'];
                    $temp_arr['open_time'] = $val['s_open_time'];
                    $temp_arr['is_recommend'] = $val['s_is_recommend'];
                    $temp_arr['max_amount_preday'] = $val['s_max_amount_preday'];
                    $temp_arr['status'] = $val['s_status'];
                    $temp_arr['preferential'] = array();
                    if($temp_arr['is_delivery'] == Supermarkets::DELIVERY_YES){
                        $preferential = array();
                        $preferential['info'] = $val['s_delivery_mini_amount']==0?"免配送费":"满".$val['s_delivery_mini_amount'].",免配送费";
                        $preferential['img'] = ATTR_DOMAIN;
                        $temp_arr['preferential'][] = $preferential;
                    }
                }elseif ($val['stype']==Stores::MACHINE ){
                    if(!empty($stroeCateId)){
                        if($val['m_category_id'] != $stroeCateId) continue;
                    }
                    $temp_arr['id'] = $val['m_id'];
                    $temp_arr['name'] = $val['m_name'];
                    $temp_arr['thumb'] = $val['m_thumb'];
                    $temp_arr['province_id'] = $val['m_province_id'];
                    $temp_arr['city_id'] = $val['m_city_id'];
                    $temp_arr['district_id'] = $val['m_district_id'];
                    $temp_arr['category_id'] = $val['m_category_id'];
                    $temp_arr['address'] = $val['m_address'];
                    $temp_arr['lng'] = $val['m_lng'];
                    $temp_arr['lat'] = $val['m_lat'];
                    $temp_arr['status'] = $val['m_status'];
                    $temp_arr['preferential'] = array();
                }elseif ($val['stype']==Stores::FRESH_MACHINE){
                    if(!empty($stroeCateId)){
                        if($val['f_category_id'] != $stroeCateId) continue;
                    }
                    $temp_arr['id'] = $val['f_id'];
                    $temp_arr['name'] = $val['f_name'];
                    $temp_arr['thumb'] = $val['f_thumb'];
                    $temp_arr['province_id'] = $val['f_province_id'];
                    $temp_arr['city_id'] = $val['f_city_id'];
                    $temp_arr['district_id'] = $val['f_district_id'];
                    $temp_arr['category_id'] = $val['f_category_id'];
                    $temp_arr['address'] = $val['f_address'];
                    $temp_arr['lng'] = $val['f_lng'];
                    $temp_arr['lat'] = $val['f_lat'];
                    $temp_arr['status'] = $val['f_status'];
                    $temp_arr['preferential'] = array();
                }
                //获取店铺分类图标
                $storeCateImg = StoreCategory::getCategoryList($temp_arr['category_id']);
                $temp_arr['storeCateImg'] = $storeCateImg;
                $temp_arr['province_name'] = Region::getName($temp_arr['province_id']);
                $temp_arr['city_name'] = Region::getName($temp_arr['city_id']);
                $temp_arr['district_name'] = Region::getName($temp_arr['district_id']);
                if (!isset($list[$temp_arr['stype'].'_'.$temp_arr['id']])) $list[$temp_arr['stype'].'_'.$temp_arr['id']] = $temp_arr;
            }
            $list = array_values($list);
            $this->_success(array('store'=>$list));


        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }

    
    
    /**
     * 第三版店铺商品详情页
     */
    public function actionDetailsV3(){
        try{
            $id  = $this->getParam('id');// 门店商品，售货机，生鲜机商品id
            $goodsId = $this->getParam('goodsId');// 商品id
            $sid  = $this->getParam('sid');		//门店商品，售货机，生鲜机机器id
            $stype  = $this->getParam('stype');				//店铺类型
            $tag_name = 'GoodsDetail';
            $storeClass = self::getStoreClass($stype);
            $storeGoodsClass = self::getStoreGoodsClass($stype);
            if (empty($storeClass)||empty($storeGoodsClass)) {
                $this->_error(Yii::t('apiModule.goods','参数错误'),$tag_name);
            }
            $cri = new CDbCriteria();
//     	$cri->select = 't.name,t.sec_title,t.is_one,is_promo,is_for,t.barcode,t.price,t.supply_price';
            $cri->select = 't.name,t.sec_title,t.is_one,is_promo,is_for,t.barcode,t.price,t.supply_price, t.thumb,t.content,t.create_time,t.id as goods_id';
            if (self::getStoreProjectId($stype)==API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID) {
            	$cri->select .= ',sg.line_id';
            }
            $cri->with = 'goodsPicture';

            $cri->join .= ' LEFT JOIN '.$storeGoodsClass::model()->tableName().' AS sg ON sg.goods_id=t.id ';
            if(!empty($id)){
                $cri->compare('sg.id', $id);
            }
            if(!empty($goodsId)){
                $cri->compare('t.id', $goodsId);
            }
            $goods = Goods::model()->find($cri);



            $goodsData =$goods->attributes;

            $goods_id = $goodsData['id'];
            if (empty($goods)) {
                $this->_error(Yii::t('apiModule.goods','商品不存在'),$tag_name);
            }
            $stock = ApiStock::goodsStockOne($sid, self::getStoreProjectId($stype)==API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID?$goods['line_id']:$goods_id,self::getStoreProjectId($stype));

            $goodsData['stock'] = $stock['result']['stock'];
            $rs['detail'] = $goodsData;
            $rs['goodsPicture'] = array();
            $rs['detail']['thumb'] = ATTR_DOMAIN.DS.$rs['detail']['thumb'];
//             $rs['detail']['content'] = strip_tags($rs['detail']['content']);

//             $str = '<p><img src=\"http:\/\/img.gaiwang.com\/files\/2016\/03\/09\/1457501675802.gif\" data_ue_src=\"http:\/\/img.gaiwang.com\/files\/2016\/03\/09\/1457501675802.gif\" style=\"float:none;\" title=\"6628711bgw1f1och1vwzeg204602a0sp.gif\"><\/p><p><img src=\"http:\/\/img.gaiwang.com\/files\/2016\/03\/09\/14575016753307.jpg\" data_ue_src=\"http:\/\/img.gaiwang.com\/files\/2016\/03\/09\/14575016753307.jpg\" style=\"float:none;\" title=\"1234567891234.jpg\"><\/p><p><img src=\"http:\/\/img.gaiwang.com\/files\/2016\/03\/09\/14575016759966.jpg\" data_ue_src=\"http:\/\/img.gaiwang.com\/files\/2016\/03\/09\/14575016759966.jpg\" style=\"float:none;\" title=\"bfc243a3gw1ezhid7yqxnj20oi0ihgpn.jpg\"><\/p><p>asdsadasd \u00a0asdsadsa\u00a0<\/p><p>\u200b<br><\/p>';
            
//             $rsstr = stripcslashes($str);
            
//             var_dump($rsstr);

            $goodsPic = Yii::app()->db->createCommand()
                ->select('concat("'.IMG_DOMAIN.'/",path) AS path')
                ->from('{{goods_picture}}')
                ->where('goods_id = :id',array(':id'=>$goods_id))
                ->queryAll();
            if (!empty($goodsPic)) {
                $rs['goodsPicture'] = $goodsPic;
            }else{
                $rs['goodsPicture'] = array();
            }
            $this->_success($rs,$tag_name);

        }catch (Exception $e){
            $this->_error($e->getMessage());
        };
    	 
    }
    
    
    /**
     * 第三版搜索页
     */
    function actionSearchV3(){
        try{

            //获取热门搜索关键词
            $keywords = $this->getParam('keywords');
            $lat = $this->getParam('lat');   //经度
            $lng = $this->getParam('lng');   //纬度
            $page = $this->getParam('page') ? $this->getParam('page') : 1;
            $pageSize = $this->getParam('pageSize') ? $this->getParam('pageSize') : 6;
            $supermarket1 = array();
            $supermarket2 = array();
            $vendingMachine1 = array();
            $vendingMachine2 = array();
            $freshMachine1 = array();
            $freshMachine2 = array();
            
            //数据按位置局部缓存
            $cache_lat = sprintf("%.3f", $lat); 
            $cache_lng = sprintf("%.3f", $lng);
            
            $cache_key = 'SearchV3'.$cache_lat.$cache_lng.$keywords.$page.$pageSize;
            $cache_time = 900;
            $list = Tool::cache(Goods::CACHE_DIR_API_CGOODS_SEARCH)->get($cache_key);
            
            if (empty($list)) {

            	//超市门店
            	$supermarket1 = Yii::app()->db->createCommand()
            	->select('t.id')
            	->from(Stores::model()->tableName().' as t')
            	->leftJoin(Supermarkets::model()->tableName().' as s','s.id = t.target_id')
            	->where("t.stype = ".Stores::SUPERMARKETS." AND s.status = ".Supermarkets::STATUS_ENABLE." AND s.name  LIKE '%".$keywords."%'")
            	->group('t.id')
            	->queryAll();
            	$supermarket2 = Yii::app()->db->createCommand()
            	->select('t.id')
            	->from(Stores::model()->tableName().' as t')
            	->leftJoin(Supermarkets::model()->tableName().' as s','s.id = t.target_id')
            	->leftJoin(SuperGoods::model()->tableName().' as sg', 's.id = sg.super_id')
            	->leftJoin(Goods::model()->tableName().' as g', 'g.id = sg.goods_id')
            	->where('g.status ='.Goods::STATUS_PASS." AND sg.status = ".SuperGoods::STATUS_ENABLE." AND t.stype = ".Stores::SUPERMARKETS." AND s.status = ".Supermarkets::STATUS_ENABLE." AND g.name LIKE '%".$keywords."%'")
            	->group('t.id')
            	->queryAll();
            	$supermarket = array_merge($supermarket1,$supermarket2);
            	//售货机
            	$vendingMachine1 = Yii::app()->db->createCommand()
            	->select('t.id')
            	->from(Stores::model()->tableName().' as t')
            	->leftJoin(VendingMachine::model()->tableName().' as vm','vm.id = t.target_id')
            	->where("t.stype = ".Stores::MACHINE." AND vm.status = ".VendingMachine::STATUS_ENABLE." AND vm.name  LIKE '%".$keywords."%'")
            	->group('t.id')
            	->queryAll();
            	$vendingMachine2 = Yii::app()->db->createCommand()
            	->select('t.id')
            	->from(Stores::model()->tableName().' as t')
            	->leftJoin(VendingMachine::model()->tableName().' as vm','vm.id = t.target_id')
            	->leftJoin(VendingMachineGoods::model()->tableName().' as vmg', 'vm.id = vmg.machine_id')
            	->leftJoin(Goods::model()->tableName().' as g', 'g.id = vmg.goods_id')
            	->where('g.status ='.Goods::STATUS_PASS." AND vmg.status = ".VendingMachineGoods::STATUS_ENABLE." AND t.stype = ".Stores::MACHINE." AND vm.status = ".VendingMachine::STATUS_ENABLE." AND g.name LIKE '%".$keywords."%'")
            	->group('t.id')
            	->queryAll();
            	$vendingMachine = array_merge($vendingMachine1,$vendingMachine2);
            	//生鲜机
            	$freshMachine1 = Yii::app()->db->createCommand()
            	->select('t.id')
            	->from(Stores::model()->tableName().' as t')
            	->leftJoin(FreshMachine::model()->tableName().' as fm','fm.id = t.target_id')
            	->where("t.stype = ".Stores::FRESH_MACHINE." AND fm.status = ".FreshMachine::STATUS_ENABLE." AND fm.name  LIKE '%".$keywords."%'")
            	->group('t.id')
            	->queryAll();
            	$freshMachine2 = Yii::app()->db->createCommand()
            	->select('t.id')
            	->from(Stores::model()->tableName().' as t')
            	->leftJoin(FreshMachine::model()->tableName().' as fm','fm.id = t.target_id')
            	->leftJoin(FreshMachineGoods::model()->tableName().' as fmg', 'fm.id = fmg.machine_id')
            	->leftJoin(Goods::model()->tableName().' as g', 'g.id = fmg.goods_id')
            	->where('g.status ='.Goods::STATUS_PASS." AND fmg.status = ".FreshMachineGoods::STATUS_ENABLE." AND t.stype = ".Stores::FRESH_MACHINE." AND fm.status = ".FreshMachine::STATUS_ENABLE." AND fm.name  LIKE '%".$keywords."%'")
            	->group('t.id')
            	->queryAll();
            	$freshMachine = array_merge($freshMachine1,$freshMachine2);
            	//合并3个数组
            	$ids = array_merge($supermarket,$vendingMachine,$freshMachine);
            	
            	
            	
            	$arr =array();
            	if(!empty($ids)){
            		foreach ($ids as $k => $v){
            			$arr[]=$v['id'];
            		}
            	}
            	$ids = implode(',',$arr);
            	if(empty($ids)) $this->_success(array('store'=>array(),'keywords'=>$keywords));
            	
            	$mysql_select = 't.stype,t.id,
        							s.id as s_id,s.name as s_name,s.mobile as s_mobile, CONCAT("' . ATTR_DOMAIN . '/",s.logo) as s_logo,s.province_id as s_province_id,s.city_id as s_city_id,s.district_id as s_district_id,s.street as s_street,s.zip_code as s_zip_code,s.lng as s_lng,s.lat as s_lat,s.is_delivery as s_is_delivery,s.category_id as s_category_id,s.delivery_mini_amount as s_delivery_mini_amount,s.delivery_start_amount as s_delivery_start_amount,s.delivery_fee as s_delivery_fee,s.star as s_star,s.open_time as s_open_time,s.is_recommend as s_is_recommend,s.max_amount_preday as s_max_amount_preday,s.status as s_status,
        							m.id as m_id,m.name as m_name, CONCAT("' . ATTR_DOMAIN . '/",m.thumb) as m_thumb,m.province_id as m_province_id,m.category_id as m_category_id,m.city_id as m_city_id,m.district_id as m_district_id,m.address as m_address,m.lng as m_lng,m.lat as m_lat,m.status as m_status';
            	
            	$mysql_select .=',f.id as f_id,f.name as f_name, CONCAT("' . ATTR_DOMAIN . '/",f.thumb) as f_thumb,f.province_id as f_province_id,f.category_id as f_category_id,f.city_id as f_city_id,f.district_id as f_district_id,f.address as f_address,f.lng as f_lng,f.lat as f_lat,f.status as f_status';
            	
            	
            	$conditions = ' 1=1 ';
            	$order = '';
            	if (!empty($lat) && !empty($lng)) {
            		$mysql_select .= ',getDistance('.$lng.','.$lat.',t.lng,t.lat) as distance  ';
            		$order = 'distance ASC';
            	
            	}
            	if(!empty($ids)){
            		$conditions .= " AND t.id in (".$ids.")";
            	}
            	//            $conditions .= ' AND t.status ='.Stores::STATUS_ENABLE;
            	//            $conditions .= ' AND p.status ='.  Partners::STATUS_ENABLE;
            	$data = Yii::app()->db->createCommand()
            	->select($mysql_select)
            	->where($conditions)
            	->from(Stores::model()->tableName().' as t')
            	->leftJoin(Supermarkets::model()->tableName().' as s', 't.target_id=s.id')
            	->leftJoin(VendingMachine::model()->tableName().' as m', 't.target_id=m.id')
            	->leftJoin(FreshMachine::model()->tableName().' as f', 't.target_id=f.id')
            	->leftJoin(Partners::model()->tableName().' as p', 'm.partner_id=p.id or s.partner_id=p.id')
            	->order($order)
            	->limit($pageSize)
            	->offset(($page - 1) * $pageSize)
            	->queryAll();
            	
            	
            	$list = array();
            	foreach ($data as $k => $val){
            		$temp_arr = array();
            		$temp_arr['stype'] = $val['stype'];
            		$temp_arr['distance'] = isset($val['distance'])?round($val['distance']):0;
            	
            		if ($val['stype']==Stores::SUPERMARKETS) {
            			$temp_arr['id'] = $val['s_id'];
            			$temp_arr['name'] = $val['s_name'];
            			$temp_arr['mobile'] = $val['s_mobile'];
            			$temp_arr['logo'] = $val['s_logo'];
            			$temp_arr['province_id'] = $val['s_province_id'];
            			$temp_arr['city_id'] = $val['s_city_id'];
            			$temp_arr['district_id'] = $val['s_district_id'];
            			$temp_arr['street'] = $val['s_street'];
            			$temp_arr['zip_code'] = $val['s_zip_code'];
            			$temp_arr['lng'] = $val['s_lng'];
            			$temp_arr['lat'] = $val['s_lat'];
            			$temp_arr['is_delivery'] = $val['s_is_delivery'];
            			$temp_arr['category_id'] = $val['s_category_id'];
            			$temp_arr['delivery_mini_amount'] = $val['s_delivery_mini_amount'];
            			$temp_arr['delivery_start_amount'] = $val['s_delivery_start_amount'];
            			$temp_arr['delivery_fee'] = $val['s_delivery_fee'];
            			$temp_arr['star'] = $val['s_star'];
            			$temp_arr['open_time'] = $val['s_open_time'];
            			$temp_arr['is_recommend'] = $val['s_is_recommend'];
            			$temp_arr['max_amount_preday'] = $val['s_max_amount_preday'];
            			$temp_arr['status'] = $val['s_status'];
            			$temp_arr['preferential'] = array();
            			if($temp_arr['is_delivery'] == Supermarkets::DELIVERY_YES){
            				$preferential = array();
            				$preferential['info'] = $val['s_delivery_mini_amount']==0?"免配送费":"满".$val['s_delivery_mini_amount'].",免配送费";
            				$preferential['img'] = ATTR_DOMAIN;
            				$temp_arr['preferential'][] = $preferential;
            			}
            			$storeGoods = array();
            			$storeGoods = Yii::app()->db->createCommand()
            			->select('g.name,sg.id,g.price')
            			->from('{{goods}} as g')
            			->leftjoin('{{super_goods}} as sg','g.id = sg.goods_id')
            			->where("sg.super_id =:id AND sg.status = ".Supermarkets::STATUS_ENABLE." AND g.name LIKE '%".$keywords."%'",array(':id'=>$temp_arr['id']))
            			->queryAll();
            			$temp_arr['goods'] = isset($storeGoods)?$storeGoods:array();
            	
            		}elseif ($val['stype']==Stores::MACHINE ){
            			$temp_arr['id'] = $val['m_id'];
            			$temp_arr['name'] = $val['m_name'];
            			$temp_arr['thumb'] = $val['m_thumb'];
            			$temp_arr['province_id'] = $val['m_province_id'];
            			$temp_arr['city_id'] = $val['m_city_id'];
            			$temp_arr['district_id'] = $val['m_district_id'];
            			$temp_arr['category_id'] = $val['m_category_id'];
            			$temp_arr['address'] = $val['m_address'];
            			$temp_arr['lng'] = $val['m_lng'];
            			$temp_arr['lat'] = $val['m_lat'];
            			$temp_arr['status'] = $val['m_status'];
            			$temp_arr['preferential'] = array();
            			$storeGoods = array();
            			$storeGoods = Yii::app()->db->createCommand()
            			->select('g.name,sg.id,g.price')
            			->from('{{goods}} as g')
            			->leftjoin('{{vending_machine_goods}} as sg','g.id = sg.goods_id')
            			->where("sg.machine_id =:id AND sg.status = ".VendingMachineGoods::STATUS_ENABLE." AND g.name LIKE '%".$keywords."%'",array(':id'=>$temp_arr['id']))
            			->queryAll();
            	
            			$temp_arr['goods'] = isset($storeGoods)?$storeGoods:array();
            		}elseif ($val['stype']==Stores::FRESH_MACHINE){
            			$temp_arr['id'] = $val['f_id'];
            			$temp_arr['name'] = $val['f_name'];
            			$temp_arr['thumb'] = $val['f_thumb'];
            			$temp_arr['province_id'] = $val['f_province_id'];
            			$temp_arr['city_id'] = $val['f_city_id'];
            			$temp_arr['district_id'] = $val['f_district_id'];
            			$temp_arr['category_id'] = $val['f_category_id'];
            			$temp_arr['address'] = $val['f_address'];
            			$temp_arr['lng'] = $val['f_lng'];
            			$temp_arr['lat'] = $val['f_lat'];
            			$temp_arr['status'] = $val['f_status'];
            			$temp_arr['preferential'] = array();
            			$storeGoods = array();
            			$storeGoods = Yii::app()->db->createCommand()
            			->select('g.name,sg.id,g.price')
            			->from('{{goods}} as g')
            			->leftjoin('{{fresh_machine_goods}} as sg','g.id = sg.goods_id')
            			->where("sg.machine_id =:id AND sg.status = ".FreshMachineGoods::STATUS_ENABLE." AND g.name LIKE '%".$keywords."%'",array(':id'=>$temp_arr['id']))
            			->queryAll();
            			$temp_arr['goods'] = isset($storeGoods)?$storeGoods:array();
            		}
            		//获取店铺分类图标
            		$storeCateImg = StoreCategory::getCategoryList($temp_arr['category_id']);
            		$temp_arr['storeCateImg'] = $storeCateImg;
            		$temp_arr['province_name'] = Region::getName($temp_arr['province_id']);
            		$temp_arr['city_name'] = Region::getName($temp_arr['city_id']);
            		$temp_arr['district_name'] = Region::getName($temp_arr['district_id']);
            		if (!isset($list[$temp_arr['stype'].'_'.$temp_arr['id']])) $list[$temp_arr['stype'].'_'.$temp_arr['id']] = $temp_arr;
            	
            	}
            	$list = array_values($list);
            	Tool::cache(CGoodsController::CACHE_DIR)->set($cache_key, $list,$cache_time);
            }
            
            $this->_success(array('store'=>$list,'keywords'=>$keywords));

        }catch (Exception $e){
            $this->_error($e->getMessage());
        };
    
    }
    
    
}