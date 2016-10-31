<?php

/**
 * 商家客户端专用接口控制器
 *
 * @author leo8705
 *
 */

class PGoodsController extends PAPIController {



    /**
     * 获取店铺商品列表
     *
     * 按分类分组
     *
     *
     */
    public function actionList() {
//     	$sid = $this->getParam('sid');				//门店id                   
//     	$sid =  $this->rsaObj->decrypt($this->getParam('sid'))*1;
        $page = $this->getParam('page')?$this->getParam('page'):1;
        $pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):99999;

        $this->_checkStore();
        $store = $this->store;

        if ($store->member_id!=$this->member) {
            $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
        }

        $cri = new CDbCriteria();
        $cri->select = 't.*,concat("'.ATTR_DOMAIN.'",t.thumb) AS thumb, g.id as id,  t.id as gid,t.id as goods_id,g.create_time';
        $cri->join = ' LEFT JOIN  '.SuperGoods::model()->tableName().' as g  ON g.goods_id = t.id ';

        $cri->compare('g.super_id', $this->store['id']);
//     	$cri->compare('t.status', Goods::STATUS_PASS);
        $cri->order = 'g.id DESC';

        //分页
        $cri->limit = $pageSize;
        $cri->offset = ($page-1)*$pageSize;

        $list = Goods::model()->findAll($cri);

        //遍历取库存
        $good_ids = array();
        foreach ($list as $data){
            $good_ids[] = $data->gid;
        }

        $stocks = ApiStock::goodsStockList($this->store['id'], $good_ids,API_PARTNER_SUPER_MODULES_PROJECT_ID);

        foreach ($list as $key=>$val){
            $list[$key] = array_merge($val->attributes,$stocks[$val['gid']]);
        }


        $cates = GoodsCategory::getGoodsCategoryList($store->member_id);
        $rs_list = array();

        foreach ($list as $val){
            $rs_list[$val['cate_id']]['cate_name'] = $cates[$val['cate_id']];
            $rs_list[$val['cate_id']]['cate_id'] =$val['cate_id'];
            $rs_list[$val['cate_id']]['goods_items'][] = $val;
        }

        $rs_list = array_values($rs_list);
        $this->_success($rs_list);

    }

    /**
     * 获取超市店铺商品分类列表
     *
     *
     */
    public function actionStoreGoodsCateList() {
        $tag_name = 'GoodsList';
        if ($this->getParam('onlyTest')==1) {
            $sid =  $this->getParam('sid')*1;
            $sg_status = $this->getParam('sg_status')*1;
        }else{
            $sg_status =  $this->rsaObj->decrypt($this->getParam('sg_status'))*1;
        }

        $this->_checkStore();
        $store = $this->store;
        if ($store->member_id!=$this->member) {
            $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
        }

        $sg_status_where = '';
        if(!empty($sg_status)) $sg_status_where = ' AND g.status = '.$sg_status.' ';

        $sql = 'SELECT c.id,c.name,count(g.id) as count FROM '.GoodsCategory::model()->tableName(). ' as c  LEFT JOIN  '.Goods::model()->tableName().' as t  ON t.cate_id = c.id LEFT JOIN  '.SuperGoods::model()->tableName().' as g  ON g.goods_id = t.id
					 WHERE g.super_id = '.$store['id'].' '.$sg_status_where.' AND t.status='.Goods::STATUS_PASS .' GROUP BY c.id ' ;

        $list= Yii::app()->db->createCommand($sql)->queryAll();

        $this->_success($list,$tag_name);

    }

    /**
     * 获取店铺商品列表
     *
     * 按分类分组
     *
     *
     */
    public function actionStoreGoodsList() {
        $tag_name = 'GoodsList';
        $page = $this->getParam('page')?$this->getParam('page'):1;
        $pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):20;

        if ($this->getParam('onlyTest')==1) {
            $cateId =  $this->getParam('cateId');
            $sg_status = $this->getParam('sg_status')*1;
        }else{
            $cateId =  $this->rsaObj->decrypt($this->getParam('cateId'))*1;
            $sg_status =  $this->rsaObj->decrypt($this->getParam('sg_status'))*1;
        }

        //lastId 上条记录id
        $lastId = $this->getParam('lastId') ? $this->getParam('lastId')*1 : -1;

//     	$store = Supermarkets::model()->findByPk($sid);
//     	if (empty($sid) || empty($store)) {
//     		$this->_error('门店不存在');
//     	}

//     	if ($store['status']!=Supermarkets::STATUS_ENABLE) {
//     		$this->_error('当前门店状态为禁用或者未审核，禁止使用。');
//     	}
        $this->_checkStore();
        $store = $this->store;
        if ($store->member_id!=$this->member) {
            $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
        }

        $cri = new CDbCriteria();
        $cri->select = 't.*,concat("'.ATTR_DOMAIN.'/",t.thumb) AS thumb, g.id as id,t.id as gid,t.id as goods_id';
        $cri->join = ' LEFT JOIN  '.SuperGoods::model()->tableName().' as g  ON g.goods_id = t.id ';

        $cri->compare('g.super_id', $store['id']);
        if (!empty($sg_status)) $cri->compare('g.status', $sg_status);
        $cri->compare('t.status', Goods::STATUS_PASS);
        $cri->compare('t.member_id', $this->member);
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

        $stocks = ApiStock::goodsStockList($store['id'], $good_ids,API_PARTNER_SUPER_MODULES_PROJECT_ID);

        foreach ($list as $key=>$val){
            $list[$key] = array_merge($val->attributes,$stocks[$val['gid']]);
            $list[$key]['gid'] = $val['gid'];
        }
        
        $this->_success($list,$tag_name);

    }


    /**
     * 更新库存
     * @param array stock
     * @param array $goods_id
     */
    public function actionUpdateStocks(){
//   	  $sid = $this->getParam('sid');
//   	  $sid =  $this->rsaObj->decrypt($this->getParam('sid'))*1;
        $stocks = CJSON::decode(str_replace('\"', '"', $this->getParam('stocks')));   //商品列表

        if (empty($stocks)) {
            $this->_error(Yii::t('apiModule.goods','库存参数解析错误'),ErrorCode::COMMOM_ERROR);
        }

//       $store = Supermarkets::model()->findByPk($sid);
//       if (empty($store)) {
//       	$this->_error('店铺不存在',ErrorCode::COMMOM_ERROR);
//       }

//       if ($store['status']!=Supermarkets::STATUS_ENABLE) {
//       	$this->_error('当前门店状态为禁用或者未审核，禁止使用。');
//       }
        $this->_checkStore();
        $store = $this->store;
        if ($store->member_id != $this->member) {
            $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
        }

        $gids = array();
        $nums = array();
        foreach ($stocks as $v){
            $gids[] = $v['id']*1;
            $nums[$v['id']] = $v['num']*1;
        }

        //查处原始的goods_id列表
        $goods = Yii::app()->db->createCommand()->select('id,goods_id')
            ->from(SuperGoods::model()->tableName())
            ->where('id IN ( '.implode(',', $gids).' ) AND super_id=:sid ',array(':sid'=>$store['id']))
            ->queryAll();

        if(empty($goods)){
            $this->_error(Yii::t('apiModule.goods','部分商品不存在'),ErrorCode::COMMOM_ERROR);
        }

        $ogids = array();
        $onums = array();
        foreach ($goods as $g){
            $ogids[] = $g['goods_id'];
            $onums[] = $nums[$g['id']];
        }

        $stocks_rs = ApiStock::stockSetList($store['id'], $ogids,$onums,API_PARTNER_SUPER_MODULES_PROJECT_ID);


        if ($stocks_rs['result']==true) {
            $this->_success(Yii::t('apiModule.goods','更新成功'));
        }else{
            $this->_success(Yii::t('apiModule.goods','更新失败'));
        }

    }




    /**
     * 商品上架
     * @param array stock
     * @param array $goods_id
     */
    public function actionEnable(){
// 		$sid =  $this->rsaObj->decrypt($this->getParam('sid'))*1;
// 		$sid = $this->getParam('sid');
// 		$barcode =  $this->getParam('barcode');
        $stock =  $this->getParam('stock')*1;		//库存
        $price = $this->getParam("price")*1;//价格
        if($price <= 0 || $stock <= 0) $this->_error("参数错误");
        if ($this->getParam('onlyTest')==1) {
            $barcode = $this->getParam('barcode');    //条码
        }else{
            $barcode = $this->rsaObj->decrypt($this->getParam('barcode'));    //条码
        }

// 		if (empty($sid)) {
// 			$this->_error('门店不能为空');
// 		}
        if (empty($barcode)) {
            $this->_error(Yii::t('apiModule.goods','条码不能为空'));
        }

// 		$store = Supermarkets::model()->findByPk($sid);
// 		if (empty($sid) || empty($store)) {
// 			$this->_error('门店不存在');
// 		}

        $this->_checkStore();
        $store = $this->store;

        if ($store->member_id!=$this->member) {
            $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
        }


        $goods = Goods::model()->find('member_id=:member_id AND barcode=:barcode',array(':member_id'=>$this->member,':barcode'=>$barcode));
        if (empty($goods)) {
            $this->_error(Yii::t('apiModule.goods','商品不存在'));
        }

        if ($goods->status!=Goods::STATUS_PASS) {
            $this->_error(Yii::t('apiModule.goods','商品未通过审核'));
        }
        //更新商品价格
        Yii::app()->db->createCommand()->update(Goods::model()->tableName(),array('price'=>$price),'member_id=:id AND barcode=:barcode',array(':id'=>$this->member,':barcode'=>$barcode));
        $sotreGoods = SuperGoods::model()->find('super_id=:super_id AND goods_id=:goods_id',array(':super_id'=>$store->id,':goods_id'=>$goods->id));

        $isNew = false;
        if (empty($sotreGoods)) {
            $isNew = true;
            $sotreGoods = new SuperGoods();
            $sotreGoods->goods_id = $goods->id;
            $sotreGoods->super_id = $store->id;
            $sotreGoods->create_time = time();
        }
        $sotreGoods->status = SuperGoods::STATUS_ENABLE;
        $sotreGoods->save();

        //设置库存
        if ($isNew) {
            ApiStock::stockIn($sotreGoods->super_id, $sotreGoods->goods_id,$stock,API_PARTNER_SUPER_MODULES_PROJECT_ID);
        }else{
            ApiStock::stockSet($sotreGoods->super_id, $sotreGoods->goods_id,$stock,API_PARTNER_SUPER_MODULES_PROJECT_ID);
        }
         $page =1;
        $pageSize=100000;
        $cache_key = md5($this->store['id'].$page.$pageSize);
         Tool::cache(Goods::CACHE_DIR_API_CGOODS_STORE_GOODS_LIST)->delete($cache_key);
        $this->_success(Yii::t('apiModule.goods','设置成功'));
    }


    /**
     * 商品条形码
     * @param barcode
     */
    public function actionBarcodeGoods(){

//      $sid = $this->rsaObj->decrypt($this->getParam('sid'));    //店铺id

        if ($this->getParam('onlyTest')==1) {
            $barcode = $this->getParam('barcode');
            $sid = $this->getParam('sid');
        }else{
            $barcode = $this->rsaObj->decrypt($this->getParam('barcode'));    //条码
        }

//      $store = Supermarkets::model()->findByPk($sid);
//      if (empty($store)) {
//      	$this->_error('店铺不存在');
//      }

//      if ($store['status']!=Supermarkets::STATUS_ENABLE) {
//      	$this->_error('当前门店状态为禁用或者未审核，禁止使用。');
//      }
        $this->_checkStore();
        $store = $this->store;
        if ($store->member_id!=$this->member) {
            $this->_error(Yii::t('apiModule.goods','不能修改他人的店铺'));
        }

        $stock = 0;

        if (!empty($this->store['id'])) {
            $goods = Yii::app()->db->createCommand()
                ->select('sg.id as id,g.id as gid,g.name,g.cate_id,CONCAT("'.ATTR_DOMAIN.'/",thumb) as thumb,g.price,g.supply_price,g.barcode,g.status,sg.status as enable_status')
                ->from('{{goods}} as g')
                ->leftJoin(SuperGoods::model()->tableName().' as sg', 'g.id=sg.goods_id')
                ->where('g.member_id=:member_id AND sg.super_id=:super_id',array(':member_id'=>$this->member,':super_id'=>$this->store['id']))
                ->andwhere('g.barcode =:barcode',array(':barcode'=>$barcode))
                ->queryRow();
        }

        if (!empty($goods)) {
//      	$goods['price'] = $goods['price']*1;
            $goods['enable_status_name'] = SuperGoods::getStatus($goods['enable_status']);

            $stock_rs = ApiStock::goodsStockOne($this->store['id'], $goods['gid'],API_PARTNER_SUPER_MODULES_PROJECT_ID);
            if (isset($stock_rs['result']['result']) && $stock_rs['result']['result']==true) {
                $stock =$stock_rs['result']['stock'];
            }
        }else{
            $goods = Yii::app()->db->createCommand()
                ->select('g.name,g.cate_id,CONCAT("'.ATTR_DOMAIN.'/",thumb) as thumb,g.price,g.supply_price,g.barcode,g.status,g.id as gid')
                ->from('{{goods}} as g')
                ->where('g.member_id=:member_id',array(':member_id'=>$this->member))
                ->andwhere('g.barcode =:barcode',array(':barcode'=>$barcode))
                ->queryRow();

            if(empty($goods)){
                $this->_error(Yii::t('apiModule.goods','商品不存在'),ErrorCode::COMMOM_ERROR);
            }

            $goods['status_name'] = Goods::getStatus($goods['status']);
        }

        $goods['barcode'] = $goods['barcode'];
        $goods['stock'] = $stock*1;
        $sid = $this->store['id'];
             $page =1;
        $pageSize=100000;
        $cache_key = md5($sid.$page.$pageSize);
        Tool::cache(Goods::CACHE_DIR_API_CGOODS_STORE_GOODS_LIST)->delete($cache_key);
        $this->_success($goods);
    }


    /**
     * 商品下架
     * @param array stock
     * @param array $goods_id
     */
    public function actionDisable(){

        if ($this->getParam('onlyTest')==1) {
            $goods_id = $this->getParam('goodsId')*1;    //商品id
        }else{
            $goods_id = $this->rsaObj->decrypt($this->getParam('goodsId'));    //商品id
        }

        if (empty($goods_id)) {
            $this->_error(Yii::t('apiModule.goods','id不能为空'));
        }

        $goods = SuperGoods::model()->findByPk($goods_id);

        if (empty($goods)) {
            $this->_error(Yii::t('apiModule.goods','商品不存在'));
        }

        $store = Supermarkets::model()->findByPk($goods->super_id);
        if (empty($store)) {
            $this->_error(Yii::t('apiModule.goods','门店不存在'));
        }

        if ($store['status']!=Supermarkets::STATUS_ENABLE) {
            $this->_error(Yii::t('apiModule.goods','当前门店状态为禁用或者未审核，禁止使用。'));
        }

        if ($store->member_id!=$this->member) {
            $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
        }

        $tran = Yii::app()->db->beginTransaction();
        //取消相关未支付订单
        $orders = Yii::app()->db->createCommand()
            ->select('t.code')
            ->from(Order::model()->tableName().' t')
            ->leftJoin(OrdersGoods::model()->tableName().' g', 't.id=g.order_id')
            ->where('t.type='.Order::TYPE_SUPERMARK.'  AND t.status='.Order::STATUS_NEW.' AND t.pay_status='.Order::PAY_STATUS_NO.' AND g.sgid=:sgid ',array(':sgid'=>$goods_id))
            ->queryAll();
        foreach ($orders as $o){
            Order::orderCancel($o['code'],true,'由于部分商品下架，本订单已自动取消',false);
        }

        $goods->status = SuperGoods::STATUS_DISABLE;
        $goods->save();

        $tran->commit();
         $page =1;
        $pageSize=100000;
        $cache_key = md5($this->store['id'].$page.$pageSize);
        Tool::cache(Goods::CACHE_DIR_API_CGOODS_STORE_GOODS_LIST)->delete($cache_key);
         
        $this->_success(Yii::t('apiModule.goods','设置成功'));
    }





    /**
     * 生鲜机商品列表
     *
     * 按分类分组
     *
     */
    public function actionFreshMachineGoodsList() {
        $tag_name = 'FreshMachineGoodsList';
        if ($this->getParam('onlyTest')==1) {
            $fmid = $this->getParam('fmid');    //商品id
        }else{
            $fmid = $this->rsaObj->decrypt($this->getParam('fmid'));				//机器id
        }
        $page = $this->getParam('page')?$this->getParam('page'):1;
        $pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):1000;

        $this->_getFreshMachine();

        $machine = $this->freshMachine;

        if (empty($fmid) || empty($machine) || $machine->status != FreshMachine::STATUS_ENABLE) {
            $this->_error(Yii::t('apiModule.goods','生鲜机机不存在'));
        }

        $where = 'g.machine_id='.$fmid.' AND t.status='.Goods::STATUS_PASS;


        $list = Yii::app()->db->createCommand()
            ->select( 't.*,t.id as goods_id, g.id as id ,concat("'.ATTR_DOMAIN.'/",t.thumb) as thumb,t.id as gid,t.is_one,t.is_for,t.is_promo,g.status as sgStatus,g.line_code,t.barcode,g.line_id')
            ->from(Goods::model()->tableName().' as t')
            ->leftJoin(FreshMachineGoods::model()->tableName().' as g', 'g.goods_id = t.id')
            ->where($where)
            ->limit($pageSize)
            ->offset(($page-1)*$pageSize)
            ->queryAll();


        if (!empty($list)) {
            //遍历取库存
            $good_ids = array();
            $line_ids = array();
            foreach ($list as $data){
                $good_ids[] = $data['gid'];
                $line_ids[] = $data['line_id'];
            }

            $stocks = ApiStock::goodsStockList($fmid, $line_ids,API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
            foreach ($list as $key=>$val){
                $list[$key] = array_merge($val,$stocks[$val['line_id']]);
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

        }else{
            $rs_list = $list;
        }

        $this->_success($rs_list,$tag_name);
    }



    /**
     * 更改商品价格
     * goodsId  商品id
     * price  商品价格
     */
    public function actionChangePrice(){


        if ($this->getParam('onlyTest')==1) {
            $goods_id = $this->getParam('goodsId')*1;    //商品id
            $price = $this->getParam('price')*1;
        }else{
            $goods_id = $this->rsaObj->decrypt($this->getParam('goodsId'));    //商品id
            $price =  $this->rsaObj->decrypt($this->getParam('price'));//商品价格
        }
        $goods_id = $goods_id*1;
        //可以小于供货价，做促销
                $goods = Yii::app()->db->createCommand()
                    ->select('supply_price')
                    ->from("{{goods}}")
                    ->where('id=:id AND member_id=:member_id',array(':id'=>$goods_id,':member_id'=>$this->member))
                    ->queryRow();
                
                if(empty($goods)){
                	$this->_error(Yii::t('storeGoods','商品不存在'));
                }
                
                if($price < $goods['supply_price']){
                    $this->_error(Yii::t('storeGoods','销售价不能小于供货价'));
                }

        $sql = "UPDATE {{goods}} SET price='{$price}' WHERE id  = ".$goods_id;

        $rs = Yii::app()->db->createCommand($sql)->execute();
        if($rs){
             $page =1;
        $pageSize=100000;
        $cache_key = md5($this->store['id'].$page.$pageSize);
         Tool::cache(Goods::CACHE_DIR_API_CGOODS_STORE_GOODS_LIST)->delete($cache_key);
            $this->_success( Yii::t('storeGoods','价格更新成功'));
        }
    }
    /**
     * 更改生鲜机商品货道
     * goodsId  商品id
     * cargoRoadId 货道ID
     * mid  机器id
     */
    public function actionChangeCargoRoad(){

        if ($this->getParam('onlyTest')==1) {
            $goods_id = $this->getParam('goodsId')*1;    //商品id
            $cargoRoadId = $this->getParam('cargoRoadId')*1;
            $mid = $this->getParam('fmid')*1;
        }else{
            $goods_id = $this->rsaObj->decrypt($this->getParam('goodsId'));    //商品id
            $cargoRoadId =  $this->rsaObj->decrypt($this->getParam('cargoRoadId'));//货道ID
            $mid =  $this->rsaObj->decrypt($this->getParam('fmid'));//machine_id机器ID
        }
        $this->_getFreshMachine();
        $m_model = $this->freshMachine;
        $line = FreshMachineLine::model()->findByPk($cargoRoadId);
        $model = FreshMachineGoods::model()->find('goods_id=:gid and machine_id=:mid', array(':gid' => $goods_id, ':mid' => $mid));

        //检查货道是否可用以及权限
        if ($line['status'] != FreshMachineLine::STATUS_ENABLE || $line['rent_partner_id'] != $this->partner) {
            $this->_error( Yii::t('freshMachine', '货道不可用'));
        }

        if(!empty($model)){
            if($model->status == FreshMachineGoods::STATUS_ENABLE ){
                $this->_error(Yii::t('freshMachine', '商品上架中，不能修改货道！'));
            }
        }

        $oldLine = FreshMachineLine::model()->findByPk($model->line_id);
        if (!empty($line->status) && $line->status == FreshMachineLine::STATUS_EMPLOY) {
            $this->_error( Yii::t('freshMachine', '货道已被其他商品已占用，请重新选择货道'));

        }

        if (!empty($line->status) && $line->status == FreshMachineLine::STATUS_DISABLE) {
            $this->_error( Yii::t('freshMachine', '货道已禁用'));

        }
        //判断货道是否过期
        if( !empty($line->expir_time)){
            if($line->expir_time <time()){
                $this->_error( Yii::t('freshMachine', '货道已失效'));
            }
        }

        $sql = "UPDATE {{fresh_machine_goods}} SET line_id = {$cargoRoadId},line_code ={$line->code} WHERE goods_id  = ".$goods_id." AND machine_id = ".$mid;

        $rs = Yii::app()->db->createCommand($sql)->execute();
        if($rs){
            $this->_success(Yii::t('freshMachine', '更新货道成功'));
        }
    }
    /**
     * 更改生鲜机商品库存
     * goodsId  商品id
     * num   库存数量
     */
    public function actionChangeFreshMachineStock(){

        if ($this->getParam('onlyTest')==1) {
            $goods_id = $this->getParam('goodsId')*1;    //商品id
            $num = $this->getParam('num')*1;
        }else{
            $goods_id = $this->rsaObj->decrypt($this->getParam('goodsId'));    //商品id
            $num =  $this->rsaObj->decrypt($this->getParam('num'));//库存数量
        }
        $this->_getFreshMachine();
        //      var_dump($store);exit;
        $stock_config = $this->params('stock');
        if ($stock_config['maxStock']<= $num) {
            $this->_error('error', Yii::t('storeGoods', '不能超过最大库存，最大库存为').$stock_config['maxStock']);
        }
        
        
        $line = Yii::app()->db->createCommand()
        ->select('t.line_id,t.id.t.goods_id')
        ->from(FreshMachineGoods::model()->tableName())
        ->where('id ='.$goods_id)
        ->queryRow();
        	
        
        //接口创建库存
        $stocks_rs = ApiStock::stockSet($this->freshMachine['id'], $line['line_id'],$num,API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);


        if (isset($stocks_rs['result']) && $stocks_rs['result']==true) {
            $this->_success( Yii::t('storeGoods','库存更新成功'));
        } else {
            $this->_error( Yii::t('storeGoods','库存更新失败'));
        }
    }
    /**
     * 设置是否参加一元购、专供、促销活动
     * type 设置活动的类型  is_one ：一元购 、is_for：专供  、is_promo：促销活动
     * status 是否参加活动  0 表示不参加  、1 表示参加
     */
    public function actionChangeIsActivities(){

        if ($this->getParam('onlyTest')==1) {
            $goods_id = $this->getParam('goodsId')*1;    //商品ids
            $type = $this->getParam('type');
            $status = $this->getParam('status')*1;
        }else{
            $goods_id = $this->rsaObj->decrypt($this->getParam('goodsId'));    //商品id
            $type =  $this->rsaObj->decrypt($this->getParam('type'));//活动类型
            $status = $this->rsaObj->decrypt($this->getParam('status'));//活动状态
        }

        if($type == 'is_one'){
            $sql = "UPDATE {{goods}} SET is_one='{$status}' WHERE id  = ".$goods_id;
        }
        if($type == 'is_for'){
            $sql = "UPDATE {{goods}} SET is_for='{$status}' WHERE id  = ".$goods_id;
        }
        if($type == 'is_promo'){
            $sql = "UPDATE {{goods}} SET is_promo='{$status}' WHERE id  = ".$goods_id;
        }
        $res = Yii::app()->db->createCommand($sql)->execute();
        if($res){
            if($status){
                $this->_success(Yii::t('storeGoods','设置成功'));
            }else{
                $this->_success(Yii::t('storeGoods','取消成功'));
            }

        }

    }


    /**
     * 生鲜机可用货道列表
     * fmid  机器id
     */
    public function actionFreshLineList(){
        $this->_getFreshMachine();

        $list = Yii::app()->db->createCommand()
            ->select('id,name,code')
            ->from(FreshMachineLine::model()->tableName())
            ->where('machine_id=:mid AND (expir_time=0 OR expir_time>:expir_time)',array(':mid'=>$this->freshMachine['id'],':expir_time'=>time()))
            ->queryAll();

        $this->_success($list);
    }


    /**
     * 盖鲜生商品上架
     */
    public function actionFreshMachineGoodsEnable(){

        if ($this->getParam('onlyTest')==1) {
            $id = $this->getParam('id');
            $fmid = $this->getParam('fmid');
        }else{
            $id = $this->rsaObj->decrypt($this->getParam('id'));
            $fmid = $this->rsaObj->decrypt($this->getParam('fmid'));
        }

        $this->_getFreshMachine();

        $machine_goods = FreshMachineGoods::model()->findByPk($id);
        $goods = Goods::model()->find('id=:gid', array(':gid' => $machine_goods->goods_id));

        if ($goods['member_id']!=$this->member) {
            $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
        }

        //判断是否存在货道
        if(!empty($machine_goods->line_id)){
            $line = FreshMachineLine::model()->findByPk($machine_goods->line_id);
        }else{
            $this->_error(Yii::t('apiModule.goods','请先设定货道'));
        }

        if ($goods->status == Goods::STATUS_NOPASS || $goods->status == Goods::STATUS_AUDIT) {
            $this->_error(Yii::t('apiModule.goods','商品审核未通过，不能上架'));
        }
        if (!empty($line->status) && $line->status == FreshMachineLine::STATUS_EMPLOY) {
            $this->_error(Yii::t('apiModule.goods','货道已被其他商品已占用，请重新选择货道'));
        }
        if (!empty($line->status) && $line->status == FreshMachineLine::STATUS_DISABLE) {
            $this->_error(Yii::t('apiModule.goods','货道已禁用，不能上架'));
        }
        //判断货道是否过期
        if(!empty($machine_goods->line_id) && !empty($line->expir_time)){
            if($line->expir_time <time()){
                $this->_error(Yii::t('apiModule.goods','货道已失效，不能上架'));
            }
        }
        $update_rs = Yii::app()->db->createCommand()->update(FreshMachineGoods::model()->tableName(),array('status'=>FreshMachineGoods::STATUS_ENABLE),'id=:id',array(':id'=>$id));
        if ($update_rs) {
            //自动下架其他商品
            Yii::app()->db->createCommand()->update(FreshMachineGoods::model()->tableName(), array('status'=>FreshMachineGoods::STATUS_DISABLE),'machine_id=:machine_id AND machine_id=:machine_id AND line_id=:line_id AND goods_id!=:goods_id  ',array(':machine_id'=>$this->freshMachine->id,':line_id'=>$machine_goods->line_id,':goods_id'=>$machine_goods->goods_id));

            //设置货道为占用状态
            $line->status = FreshMachineLine::STATUS_EMPLOY;
            $line->save();
            $this->_success(Yii::t('apiModule.goods','设置成功'));
        } else {
            $this->_error(Yii::t('apiModule.goods','上架失败'));
        }

    }



    /**
     * 盖鲜生商品下架
     */
    public function actionFreshMachineGoodsDisable(){

        if ($this->getParam('onlyTest')==1) {
            $id = $this->getParam('id');
            $fmid = $this->getParam('fmid');
        }else{
            $id = $this->rsaObj->decrypt($this->getParam('id'));
            $fmid = $this->rsaObj->decrypt($this->getParam('fmid'));
        }

        $this->_getFreshMachine();

        $machine_goods = FreshMachineGoods::model()->findByPk($id);

        if ($machine_goods['status']==FreshMachineGoods::STATUS_DISABLE) {
            $this->_error(Yii::t('apiModule.goods','商品已下架'));
        }

        $line = FreshMachineLine::model()->findByPk($machine_goods->line_id);

//         $model = FreshMachineGoods::model()->findByPk($id);
        $m_model = $this->freshMachine;

        $tran = Yii::app()->db->beginTransaction();

        $update_rs = Yii::app()->db->createCommand()->update(FreshMachineGoods::model()->tableName(),array('status'=>FreshMachineGoods::STATUS_DISABLE),'id=:id',array(':id'=>$id));

        if ($update_rs) {
            $line->status = FreshMachineLine::STATUS_ENABLE;
            $line->save();
            //取消相关未支付订单
            $orders = Yii::app()->db->createCommand()
                ->select('t.code')
                ->from(Order::model()->tableName() . ' t')
                ->leftJoin(OrdersGoods::model()->tableName() . ' g', 't.id=g.order_id')
                ->where('t.type=' . Order::TYPE_FRESH_MACHINE . '  AND (t.status=' . Order::STATUS_NEW . ' AND t.pay_status=' . Order::PAY_STATUS_NO .  ' AND g.sgid=:sgid )  OR (t.pay_status='.Order::PAY_STATUS_YES. '  AND t.status=' . Order::STATUS_PAY .  ' AND g.sgid=:sgid )',array(':sgid' => $id))
                ->queryAll();
            foreach ($orders as $o) {
                Order::orderCancel($o['code'], true, Yii::t('partnerModule.freshMachine','生鲜机部分商品下架，订单自动取消'), false);
            }

            $stock_rs = ApiStock::stockSet($machine_goods->machine_id, $machine_goods->line_id, 0, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);

            if (isset($stock_rs['result']) && $stock_rs['result']) {
                $tran->commit();
                $this->_success(Yii::t('apiModule.goods','设置成功'));
            } else {
                $tran->rollback();
                $this->_error(Yii::t('apiModule.goods','设置生鲜机商品下架失败，库存更新失败'));
            }
        } else {
            $this->_error(Yii::t('apiModule.goods','下架失败'));
        }

    }



    /**
     * 添加或更新商品
     */
    public function actionSave(){
        try {
            $post = $this->getParams();
                Yii::log('files:'. var_export($_FILES,true).'data:'.  var_export($post,true));
//            $barcode = $post['barcode'];
            $act = $post['act'];
            $goodsId = isset($post['goodsId'])?$post['goodsId']:'';
            $isBarcode = isset($post['isBarcode'])?$post['isBarcode']:'';
            if($act == 1){
                $model = new Goods;
                $model->scenario = 'create';
                $is_create = true;
                if (isset($isBarcode) && $isBarcode == 1) {
                    $model->scenario = 'barcode_add';
                    $is_create = false;
                }
                $model->attributes = $post;
                
                if ($model->supply_price>$model->price) {
                	$this->_error('供货价必须小于售价');
                }
                
                $model->member_id = $this->member;
                $model->partner_id = $this->partner;
                $model->create_time = time();
                $model->status = Goods::STATUS_AUDIT;//编辑和更新后须对商品进行审核
                $model->name = rtrim($model->name);
                $saveDir = 'partnerGoods/' . date('Y/n/j');
                $images = $_FILES;
                $_FILES = Tool::appUploadPic($model);

                if ($is_create) $model = UploadedFile::uploadFile($model, 'thumb', $saveDir, Yii::getPathOfAlias('att'));
            }
            if($act == 2 && !empty($goodsId)){
                $model = Goods::model()->findByPk($goodsId);
                if ($model->member_id!=$this->member) {
                    $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
                }
                $model->scenario = 'update';
                $is_update = true;
                if (isset($isBarcode) && $isBarcode == 1) {
                    $model->scenario = 'barcode_update';
                    $is_update = false;
                }
                $oldFile = $model->thumb;
                $model->attributes = $post;
                
                if ($model->supply_price>$model->price) {
                	$this->_error('供货价必须小于售价');
                }
                
                $model->status = Goods::STATUS_AUDIT;//编辑和更新后须对商品进行审核
                $model->name = trim($model->name);
                $model->member_id = $this->member;
                $model->partner_id = $this->partner;
                $saveDir = 'partnerGoods/' . date('Y/n/j');
                $images = $_FILES;
                $_FILES = Tool::appUploadPic($model);
                if ($is_update) $model = UploadedFile::uploadFile($model, 'thumb', $saveDir, Yii::getPathOfAlias('att'), $oldFile);  // 上传图片
            }

            if($model->save()){
                if(isset($oldFile)){
                    if ($is_update) UploadedFile::saveFile('thumb', $model->thumb, $oldFile, true);
                    unset($images['thumb']);
                    foreach($images as $k =>$v){
                        if(empty($v['name'])) continue;
                        $goodsPicId = substr($k,7);
                        $array = array('A','B','C');
                       //表示新增图片
                        if(in_array($goodsPicId,$array)){
                            $goodsPicModel = new GoodsPicture();
                            $model_name = get_class($goodsPicModel);
                            $newFiles = array();
                            $newFiles[$model_name]['name']['path']=$v['name'];
                            $newFiles[$model_name]['type']['path']=$v['type'];
                            $newFiles[$model_name]['tmp_name']['path']=$v['tmp_name'];
                            $newFiles[$model_name]['error']['path']=$v['error'];
                            $newFiles[$model_name]['size']['path']=$v['size'];
                            $_FILES = $newFiles;
                            $fileName = Tool::generateSalt() . '.' . pathinfo($_FILES[$model_name]['name']['path'], PATHINFO_EXTENSION);
                            $filePath = 'files/'.$fileName;
                            $sql ="INSERT INTO {{goods_picture}} (`goods_id`, `path`) VALUES ('".$goodsId."', '".$filePath."')";
                            Yii::app()->db->createCommand($sql)->execute();
                            UploadedFile::upload_file($_FILES[$model_name]['tmp_name']['path'],$filePath,'','uploads');
                        }else{
                            $goodsPicModel = GoodsPicture::model()->findByAttributes(array("id"=>$goodsPicId));
                            if(empty($goodsPicModel)) continue;
                            $oldFilePic = $goodsPicModel->path;
                            $newFiles = array();
                            $model_name = get_class($goodsPicModel);
                            $newFiles[$model_name]['name']['path']=$v['name'];
                            $newFiles[$model_name]['type']['path']=$v['type'];
                            $newFiles[$model_name]['tmp_name']['path']=$v['tmp_name'];
                            $newFiles[$model_name]['error']['path']=$v['error'];
                            $newFiles[$model_name]['size']['path']=$v['size'];
                            $_FILES = $newFiles;
                            $fileName = Tool::generateSalt() . '.' . pathinfo($_FILES[$model_name]['name']['path'], PATHINFO_EXTENSION);
                            $filePath = 'files/'.$fileName;
                            $goodsPicModel->path = $filePath;
                            if($goodsPicModel->save()){
                                UploadedFile::upload_file($_FILES[$model_name]['tmp_name']['path'],$filePath,$oldFilePic,'uploads');
                            }
                        }

                    }
                     $page =1;
                    $pageSize=100000;
                    $cache_key = md5($this->store['id'].$page.$pageSize);
                     $ss = Tool::cache(Goods::CACHE_DIR_API_CGOODS_STORE_GOODS_LIST)->delete($cache_key);
                    $this->_success(Yii::t('goods', '商品编辑成功'));
                }else{

                    if ($is_create) UploadedFile::saveFile('thumb', $model->thumb);
                    $goodsId = Yii::app()->db->getLastInsertID();
                    unset($images['thumb']);
                    foreach($images as $k =>$v){
                        $goodsPicModel = new GoodsPicture();
                        $model_name = get_class($goodsPicModel);
                        if(empty($v['name'])) continue;
                        $newFiles = array();
                        $newFiles[$model_name]['name']['path']=$v['name'];
                        $newFiles[$model_name]['type']['path']=$v['type'];
                        $newFiles[$model_name]['tmp_name']['path']=$v['tmp_name'];
                        $newFiles[$model_name]['error']['path']=$v['error'];
                        $newFiles[$model_name]['size']['path']=$v['size'];
                        $_FILES = $newFiles;
                        $fileName = Tool::generateSalt() . '.' . pathinfo($_FILES[$model_name]['name']['path'], PATHINFO_EXTENSION);
                        $filePath = 'files/'.$fileName;
                        $sql ="INSERT INTO {{goods_picture}} (`goods_id`, `path`) VALUES ('".$goodsId."', '".$filePath."')";
                        Yii::app()->db->createCommand($sql)->execute();
                        UploadedFile::upload_file($_FILES[$model_name]['tmp_name']['path'],$filePath,'','uploads');

                    }
                    $this->_success(Yii::t('goods', '商品创建成功'));
                }
            }else{
              Yii::log('errors::'. var_export($model->getErrors(),true));
                $this->_error("数据保存失败！");
            }

        }catch (Exception $e){

            $this->_error($e->getMessage());

        }
    }

    /**
     * 获取商品库商品列表
     *
     */
    public function actionProductList() {
        $tag_name = 'ProductList';
        $page = $this->getParam('page')?$this->getParam('page'):1;
        $pageSize = $this->getParam('pageSize')?$this->getParam('pageSize'):20;

        //lastId 上条记录id
        $lastId = $this->getParam('lastId') ? $this->getParam('lastId')*1 : -1;

        $list = Yii::app()->db->createCommand()
            ->from(Goods::model()->tableName().' AS t')
            ->select('t.*,concat("'.ATTR_DOMAIN.'/",t.thumb) AS thumb, t.id as goods_id')
            ->where('member_id=:member_id AND t.status=:status AND t.id>:lastId',array(':member_id'=>$this->member,':status'=>Goods::STATUS_PASS,':lastId'=>$lastId))
            ->limit($pageSize)
            ->offset( ($page-1)*$pageSize)
            ->queryAll();

        $this->_success($list,$tag_name);
    }

    /**
     * 获取单个商品库商品信息
     */
    public function actionProductInfo(){
        try{
            $post = $this->getParams();
            $goodsId =$post['goodsId'];

            $data = Yii::app()->db->createCommand()
                ->select("g.id as goods_id, g.name, g.sec_title, g.content, g.supply_price, g.barcode, g.is_one, g.is_promo, g.is_for,  g.price,g.thumb,g.status,gc.name cate_name,gc.id cate_id,c.name as sys_cate_name,c.id as sys_cate_id")
                ->from("{{goods}} as g")
                ->leftjoin("{{goods_category}} as gc","g.cate_id = gc.id")
                ->leftjoin("{{category}} as c","g.source_cate_id = c.id")
                ->where('g.member_id=:member_id AND g.id = :id',array(':member_id'=>$this->member,':id'=>$goodsId))
                ->queryRow();
            if (empty($data)) {
                $this->_error(Yii::t('apiModule.goods', '商品不存在'));
            }
            if(!empty($data['thumb'])){
                $data['thumb'] = ATTR_DOMAIN . '/' .$data['thumb'];
            }
            $imgs = Yii::app()->db->createCommand()
                ->select("id,CONCAT('".IMG_DOMAIN."','/',path) as path,sort")
                ->from(GoodsPicture::model()->tableName()." as t")
                ->where('t.goods_id=:goods_id',array(':goods_id'=>$goodsId))
                ->queryAll();

            $data['imgs'] = $imgs;
            $data['sys_cates'] = Tool::categoryBreadcrumb($data['sys_cate_id']);

            $this->_success(array('goods'=>$data));

        }catch (Exception $e){

            $this->_error($e->getMessage());
        }

    }

    /**
     * 获取商家商品分类接口
     */
    public function actionGoodsCateList(){
        try{
            $list= Yii::app()->db->createCommand()
                ->from(GoodsCategory::model()->tableName())
                ->select('name,sort,id')
                ->where('member_id=:member_id',array(':member_id'=>$this->member))
                ->order('sort ASC')
                ->queryAll();
            $this->_success(array('list'=>$list));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }


    /**
     * 添加或更新商家商品分类接口
     */
    public function actionGoodsCateSave(){
        try{
            $post = $this->getParams();
            if ($post['act']==1) {
                $cate = new GoodsCategory();
                $cate->name = $post['name'];
                $cate->sort = isset($post['sort'])?$post['sort']:255;//排序默认255
                $cate->member_id = $this->member;
                $cate->save();
            }
            if ($post['act']==2 && !empty($post['cateId'])) {
                $cate = GoodsCategory::model()->findByPk($post['cateId']);
                if (empty($cate) || $cate->member_id!=$this->member) {
                    $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
                }
                $cate->name = $post['name'];
                $cate->sort = isset($post['sort'])?$post['sort']:255;//排序默认255
                $cate->member_id = $this->member;
                $cate->save();
            }
            $this->_success('成功');
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }

    /**
     * 删除商品分类接口
     */
    public function actionDelGoodsCate(){
        try{
            $post = $this->getParams();
            $model = GoodsCategory::model()->findByPk($post['cateId']);
            $goods = Yii::app()->db->createCommand()
                ->from(Goods::model()->tableName())
                ->select('id')
                ->where('cate_id=:cate_id',array(':cate_id'=>$post['cateId']))
                ->queryRow();
            if(!empty($goods)) {
                $this->_error("该分类下有商品，不能删除！");
            }
            if (empty($model) || $model->member_id!=$this->member) {
                $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
            }
            if($model->delete()){
                 $page =1;
        $pageSize=100000;
        $cache_key = md5($this->store['id'].$page.$pageSize);
         Tool::cache(Goods::CACHE_DIR_API_CGOODS_STORE_GOODS_LIST)->delete($cache_key);
                $this->_success('删除成功');
            }else{
                $this->_error("删除失败");
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }


    }

    /**
     * 获取系统商品顶级分类
     */
    public function actionSysCates(){
        try{
            $cateId=$this->getParam('cateId',0);
            $list = Category::getCates($cateId);
            $this->_success(array('list'=>$list));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }

    /**
     * 商品详情图片删除接口
     */
    public function actionDelPic(){
        try{
            if ($this->getParam('onlyTest')==1) {
                $id = $this->getParam('id');
            }else{
                $id =  $this->rsaObj->decrypt($this->getParam('id'))*1;//商品图片id
            }
            if(!is_numeric($id) || empty($id)) $this->_error("参数错误");
            $model = GoodsPicture::model()->findByAttributes(array("id"=>$id));
            if(empty($model)) $this->_error("参数错误");
            $path = $model->path;
            $model->delete();
            @UploadedFile::delete(Yii::getPathOfAlias('uploads') . '/' . $path);
            $this->_success("图片删除成功");
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }
    
    
    /**
     * 条形库商品详情
     * @param barcode
     */
    public function actionBarcodeGoodsInfo(){
        if ($this->getParam('onlyTest')==1) {
            $barcode = $this->getParam('barcode');
        }else{
            $barcode = $this->rsaObj->decrypt($this->getParam('barcode'));    //条码
        }

    	if ($this->getParam('onlyTest')==1) {
    		$barcode = $this->getParam('barcode');
    	}
    	
    	$goods = Yii::app()->db->createCommand()
    	->from(BarcodeGoods::model()->tableName())
    	->where('barcode=:barcode',array(':barcode'=>$barcode))
    	->queryRow();
    	
    	if ($goods) {
    		$goods['thumb'] = IMG_DOMAIN.$goods['thumb'];
    		$this->_success($goods);
    	}else {
    		$this->_error('条码库商品不存在');
    	}
    	
    	
    }
    
    
    
    /**
     * 超市门店商品批量上架
     */
    public function actionMultEnableStoreGoods() {
    	if ($this->getParam('onlyTest')==1) {
    		$ids = $this->getParam('idArr');
    	}else{
    		$ids = $this->rsaObj->decrypt($this->getParam('idArr'));    //条码
    	}
    	
    	$ids = explode(',', $ids);
    	if (empty($ids)) {
    		$this->_error('参数错误');
    	}
    	
    	foreach ($ids as $k=>$v){
    		$ids[$k] = $v*1;
    	}
    
    	$rs = Yii::app()->db->createCommand(
    			'UPDATE '.SuperGoods::model()->tableName().' as t , '.Goods::model()->tableName() .' as g
			SET
				t.status='.SuperGoods::STATUS_ENABLE.'
			WHERE
				t.id IN('.implode(',', $ids).') AND g.member_id='.$this->member.' AND t.goods_id=g.id  AND g.status='.Goods::STATUS_PASS.' AND t.status!='.SuperGoods::STATUS_ENABLE.' 
			 '
    	)->execute();
    
    	if ($rs) {
            
			$this->_success('批量上架成功');
    	} else {
    		$this->_error('批量上架失败');
    	}
    }
    
    
    
}