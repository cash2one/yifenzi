<?php
/**
 * 盖付通商品接口控制器
 * 
 * @author leo8705
 *
 */

class CGoodsController extends COpenAPIController {

 
    /**
     * 获取店铺商品列表
     *
     *
     */
    public function actionStoreGoodsList() {
        try{
            $this->params = array('token','sid','cateId','page','pageSize');
            $requiredFields = array('token','sid');
            $decryptFields = array('token','sid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);

            $sid = $post['sid'];				//门店id
            if(isset($post['cateId'])){
                $cateId = $post['cateId'];
            }
            $page = isset($post['page'])?$post['page']:1;
            $pageSize = isset($post['pageSize'])?$post['pageSize']:20;
            if (empty($sid)) {
                $this->_error(Yii::t('goods','sid不能为空'));
            }

            $cri = new CDbCriteria();
            $cri->select = 't.*,concat("'.ATTR_DOMAIN.'/",t.thumb) AS thumb, g.id as id,t.id as gid';
            $cri->join = ' LEFT JOIN  '.SuperGoods::model()->tableName().' as g  ON g.goods_id = t.id ';

            $cri->compare('g.super_id', $sid);
            $cri->compare('g.status', SuperGoods::STATUS_ENABLE);
            $cri->compare('t.status', Goods::STATUS_PASS);
            if (!empty($cateId)) $cri->compare('t.cate_id', $cateId);
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
            }
//            var_dump($list);exit;
            $this->_success($list);

        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    	
    }
    
     /**
     * 获取店铺商品列表
     * 
     * 按分类分组
     *
     *
     */
    public function actionStoreCateGoodsList() {
    	try{
            $this->params = array('token','sid','page','pageSize');
            $requiredFields = array('token','sid');
            $decryptFields = array('token','sid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $sid = $post['sid'];				//门店id
            $page = isset($post['page'])?$post['page']:1;
            $pageSize = isset($post['pageSize'])?$post['pageSize']:20;

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
                $list[$key] = array_merge($val->attributes,$stocks[$val['gid']]);
            }


            $cates = GoodsCategory::getGoodsCategoryList($store->member_id);
            $rs_list = array();

            foreach ($list as $val){
                $rs_list[$val['cate_id']]['cate_name'] = isset($cates[$val['cate_id']])?$cates[$val['cate_id']]:'未知分类';
                $rs_list[$val['cate_id']]['cate_id'] =$val['cate_id'];
                $rs_list[$val['cate_id']]['goods_items'][] = $val;
            }

            $rs_list = array_values($rs_list);
            $this->_success($rs_list);

        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }
    
    
    
    
    
    /**
     * 获取售货机商品列表
     *
     *
     */
    public function actionMachineGoodsList() {
        try{
            $this->params = array('token','mid','page','pageSize');
            $requiredFields = array('token','mid');
            $decryptFields = array('token','mid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $mid = $post['mid'];				//门店id
            $page = isset($post['page'])?$post['page']:1;
            $pageSize = isset($post['pageSize'])?$post['pageSize']:100;
            if (empty($mid)) {
                $this->_error(Yii::t('goods','mid不能为空'));
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
                $list[$key] = array_merge($val->attributes,$stocks[$val['gid']]);
            }

            $this->_success($list);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    
    }
    
    

    /**
     * 获取超市店铺商品分类列表
     *
     *
     */
    public function actionStoreGoodsCateList() {
        try{
            $this->params = array('token','sid');
            $requiredFields = array('token','sid');
            $decryptFields = array('token','sid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $sid = $post['sid'];				//门店id
            $sql = 'SELECT c.id,c.name FROM '.GoodsCategory::model()->tableName(). ' as c  LEFT JOIN  '.Goods::model()->tableName().' as t  ON t.cate_id = c.id LEFT JOIN  '.SuperGoods::model()->tableName().' as g  ON g.goods_id = t.id
					 WHERE g.super_id = '.$sid.' AND g.status = '.SuperGoods::STATUS_ENABLE.' AND t.status='.Goods::STATUS_PASS .' GROUP BY c.id ' ;
            $list= Yii::app()->db->createCommand($sql)->queryAll();
            $this->_success($list);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

        
    }
    
    /**
     * 获取售货机商品列表
     *
     * 按分类分组
     *
     */
    public function actionMachineCateGoodsList() {
        try{
            $this->params = array('token','mid','page','pageSize');
            $requiredFields = array('token','mid');
            $decryptFields = array('token','mid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $mid = $post['mid'];				//门店id
            $page = isset($post['page'])?$post['page']:1;
            $pageSize = isset($post['pageSize'])?$post['pageSize']:100;
            $machine = VendingMachine::model()->findByPk($mid);
            if (empty($mid) || empty($machine) || $machine->status != VendingMachine::STATUS_ENABLE) {
                $this->_error(Yii::t('goods','售货机不存在'));
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
                $list[$key] = array_merge($val->attributes,$stocks[$val['gid']]);
            }

//     	$cates = GoodsCategory::getGoodsCategoryList($machine->member_id);

            $cates = Yii::app()->db->createCommand()
                ->from(GoodsCategory::model()->tableName().' as t')
                ->select('t.*')
                ->leftJoin(Goods::model()->tableName().' as g', 'g.cate_id=t.id')
                ->where('g.id IN('.implode(',', $good_ids).')')
                ->queryAll();
            $cates = CHtml::listData($cates,'id','name');

            $rs_list = array();

            foreach ($list as $val){
                if (!isset($cates[$val['cate_id']])) {
                    continue;
                }
                $rs_list[$val['cate_id']]['cate_name'] = $cates[$val['cate_id']];
                $rs_list[$val['cate_id']]['cate_id'] =$val['cate_id'];
                $rs_list[$val['cate_id']]['goods_items'][] = $val;
            }

            $rs_list = array_values($rs_list);
            $this->_success($rs_list);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }
    
    /**
     * 获取售货机商品分类列表
     *
     *
     */
    public function actionMachineGoodsCateList() {
        try{
            $this->params = array('token','mid');
            $requiredFields = array('token','mid');
            $decryptFields = array('token','mid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $mid = $post['mid'];				//机器id
            $sql = 'SELECT c.id,c.name FROM '.GoodsCategory::tableName(). ' as c  LEFT JOIN  '.Goods::tableName().' as t  ON t.cate_id = c.id LEFT JOIN  '.VendingMachineGoods::tableName().' as g  ON g.goods_id = t.id
					 WHERE g.machine_id = '.$mid.' AND g.status = '.VendingMachineGoods::STATUS_ENABLE.' AND t.status='.Goods::STATUS_PASS .' GROUP BY c.id ' ;
            $list= Yii::app()->db->createCommand($sql)->queryAll();
            $this->_success($list);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }
    
    /**
     * 获取商品评价
     */
    public function actionGoodsComment(){
        try{
            $this->params = array('token','gid','page','pageSize');
            $requiredFields = array('token','gid');
            $decryptFields = array('token','gid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $gid = $post['gid'];				//门店id
            $page = isset($post['page'])?$post['page']:1;
            $pageSize = isset($post['pageSize'])?$post['pageSize']:100;
            $cri = new CDbCriteria();
            $cri->select = '*';
            $cri->compare('goods_id', $gid);	//分页
            $cri->limit = $pageSize;
            $cri->offset = ($page-1)*$pageSize;
            $list = GoodsComment::model()->findAll($cri);
            if(empty($list)){
                $this->_error(Yii::t('goods','该商品暂无评价'));
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
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

      
    }
    
        /**
     * 获取生鲜机商品列表
     *
     * 按分类分组
     *
     */
    public function actionFreshMachineGoodsList() {
    	try{
            $this->params = array('token','mid','page','pageSize','is_one','is_for','is_promo');
            $requiredFields = array('token','mid');
            $decryptFields = array('token','mid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $mid = $post['mid'];				//机器id
            $page = isset($post['page'])?$post['page']:1;
            $pageSize = isset($post['pageSize'])?$post['pageSize']:100;
            $is_one = isset($post['is_one'])?$post['is_one']:'';						//是否一元购
            $is_for = isset($post['is_for'])?$post['is_for']:'';							//是否促销
            $is_promo = isset($post['is_promo'])?$post['is_promo']:'';				//是否促销
            $machine = FreshMachine::model()->findByPk($mid);
            if (empty($mid) || empty($machine) || $machine->status != FreshMachine::STATUS_ENABLE) {
                $this->_error(Yii::t('goods','生鲜机机不存在'));
            }
            $where = 'g.machine_id='.$mid.' AND g.status='.FreshMachineGoods::STATUS_ENABLE.' AND t.status='.Goods::STATUS_PASS;
            if (!empty($is_one)) $where .= ' AND t.is_one='.Goods::IS_ONE;
            if (!empty($is_for)) $where .= ' AND t.is_for='.Goods::IS_FOR;
            if (!empty($is_promo)) $where .= ' AND t.is_promo='.Goods::IS_PROMO;

            $list = Yii::app()->db->createCommand()
                ->select( 't.*, g.id as id ,concat("'.ATTR_DOMAIN.'/",t.thumb) as thumb,t.id as gid,t.is_one,t.is_for,t.is_promo')
                ->from(Goods::model()->tableName().' as t')
                ->leftJoin(FreshMachineGoods::model()->tableName().' as g', 'g.goods_id = t.id')
                ->where($where)
                ->limit($pageSize)
                ->offset(($page-1)*$pageSize)
                ->queryAll();
            if (!empty($list)) {
                //遍历取库存
                $good_ids = array();
                foreach ($list as $data){
                    $good_ids[] = $data['gid'];
                }

                $stocks = ApiStock::goodsStockList($mid, $good_ids,API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
                foreach ($list as $key=>$val){
                    $list[$key] = array_merge($val,$stocks[$val['gid']]);
                }
                $cates = Yii::app()->db->createCommand()
                    ->from(GoodsCategory::model()->tableName().' as t')
                    ->select('t.*')
                    ->leftJoin(Goods::model()->tableName().' as g', 'g.cate_id=t.id')
                    ->where('g.id IN('.implode(',', $good_ids).')')
                    ->queryAll();
                $cates = CHtml::listData($cates,'id','name');

                $rs_list = array();
                foreach ($list as $val){
                    if (!isset($cates[$val['cate_id']])) {
                        continue;
                    }
                    $rs_list[$val['cate_id']]['cate_name'] = $cates[$val['cate_id']];
                    $rs_list[$val['cate_id']]['cate_id'] =$val['cate_id'];
                    $rs_list[$val['cate_id']]['goods_items'][] = $val;
                }

                $rs_list = array_values($rs_list);
            }else{
                $rs_list = $list;
            }
            $this->_success($rs_list);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }
    
    
    
    
    /**
     * 商品详情接口
     * 
     * 
     * 
     */
    public function actionGoodsDetail(){
        try{
            $this->params = array('token','sgid','sid','stype');
            $requiredFields = array('token','sgid','sid','stype');
            $decryptFields = array('token','sgid','sid','stype');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $sgid = $post['sgid'];				//超市商品id
            $sid = $post['sid'];      //门店id
            $stype = $post['stype'];   //门店类型
            $storeClass = self::getStoreClass($stype);
            $storeGoodsClass = self::getStoreGoodsClass($stype);

            if (empty($storeClass)||empty($storeGoodsClass)) {
                $this->_error(Yii::t('apiModule.goods','参数错误'));
            }

            $cri = new CDbCriteria();
//     	$cri->select = 't.name,t.sec_title,t.is_one,is_promo,is_for,t.barcode,t.price,t.supply_price';
            $cri->select = 't.name,t.sec_title,t.is_one,is_promo,is_for,t.barcode,t.price,t.supply_price, t.thumb,t.content,t.create_time,t.id as goods_id';
            $cri->with = 'goodsPicture';

            $cri->join .= ' LEFT JOIN '.$storeGoodsClass::model()->tableName().' AS sg ON sg.goods_id=t.id ';
            $cri->compare('sg.id', $sgid);

            $goods = Goods::model()->find($cri);

            $goods_id = $goods['goods_id'];
            if (empty($goods)) {
                $this->_error(Yii::t('goods','商品不存在'));
            }

            $apiStock = new ApiStock();
            $stock = ApiStock::goodsStockOne($sid, $goods_id,self::getStoreProjectId($stype));


            $goods['stock'] = $stock['result']['stock'];

            $rs['detail'] = $goods;
            $rs['goodsPicture'] = array();

            $rs['detail']['thumb'] = ATTR_DOMAIN.DS.$rs['detail']['thumb'];
            if (!empty($goods->goodsPicture)) {
                foreach ($goods->goodsPicture as $p){
                    $rs['goodsPicture'][] = IMG_DOMAIN.DS.$p['path'];
                }
            }

            $this->_success($rs);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    	
    }



    
    
    
    
    
}