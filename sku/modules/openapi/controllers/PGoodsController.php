<?php
/**
 * 商家客户端专用接口控制器
 *
 * @author leo8705
 *
 */

class PGoodsController extends POpenAPIController {


    /**
     * 获取店铺商品列表
     *
     * 按分类分组
     *
     *
     */
    public function actionList() {
        try{
            $this->params = array('token','sid','page','pageSize');
            $requiredFields = array('token');
            $decryptFields = array('token','sid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $page = isset($post['page'])?$post['page']:1;
            $pageSize = isset($post['pageSize'])?$post['pageSize']:20;
            $this->_checkStore();
            $store = $this->store;
            if ($store->member_id!=$this->member) {
                $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
            }

            $cri = new CDbCriteria();
            $cri->select = 't.*,concat("'.ATTR_DOMAIN.'",t.thumb) AS thumb, g.id as id,t.id as gid,g.create_time';
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
            $tag_name = 'GoodsList';
            $this->params = array('token','sid','sg_status');
            $requiredFields = array('token','sid');
            $decryptFields = array('token','sid','sg_status');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $sid = $post['sid'];
            $sg_status =  isset($post['sg_status'])?$post['sg_status']:null;
            if ($this->getParam('onlyTest')==1) {
                $sid =  $this->getParam('sid')*1;
                $sg_status = $this->getParam('sg_status')*1;
            }
            $this->_checkStore();
            $store = $this->store;
            if ($store->member_id!=$this->member) {
                $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
            }

            $sg_status_where = '';
            if(!empty($sg_status)) $sg_status_where = ' AND g.status = '.$sg_status.' ';

            $sql = 'SELECT c.id,c.name FROM '.GoodsCategory::model()->tableName(). ' as c  LEFT JOIN  '.Goods::model()->tableName().' as t  ON t.cate_id = c.id LEFT JOIN  '.SuperGoods::model()->tableName().' as g  ON g.goods_id = t.id
					 WHERE g.super_id = '.$store['id'].' '.$sg_status_where.' AND t.status='.Goods::STATUS_PASS .' GROUP BY c.id ' ;

            $list= Yii::app()->db->createCommand($sql)->queryAll();

            $this->_success($list,$tag_name);
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
    public function actionStoreGoodsList() {
        try{
            $tag_name = 'GoodsList';
            $this->params = array('token','sid','cateId','sg_status','page','pageSize','lastId');
            $requiredFields = array('token','sid');
            $decryptFields = array('token','sid','sg_status','cateId');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $sid = $post['sid'];

            $cateId = isset($post['cateId'])?$post['cateId']*1:null;
            $page = isset($post['page'])?$post['page']*1:1;
            $pageSize = isset($post['pageSize'])?$post['pageSize']*1:20;
            $sg_status = isset($post['sg_status'])?$post['sg_status']*1:null;
            $lastId = isset($post['lastId'])?$post['lastId']*1:-1;
            $this->_checkStore();
            $store = $this->store;
            if ($store->member_id!=$this->member) {
                $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
            }

            $cri = new CDbCriteria();
            $cri->select = 't.*,concat("'.ATTR_DOMAIN.'/",t.thumb) AS thumb, g.id as id,t.id as gid';
            $cri->join = ' LEFT JOIN  '.SuperGoods::model()->tableName().' as g  ON g.goods_id = t.id ';

            $cri->compare('g.super_id', $store['id']);
            if (!empty($sg_status)) $cri->compare('g.status', $sg_status);
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

            $stocks = ApiStock::goodsStockList($store['id'], $good_ids,API_PARTNER_SUPER_MODULES_PROJECT_ID);

            foreach ($list as $key=>$val){
                $list[$key] = array_merge($val->attributes,isset($stocks[$val['gid']])?$stocks[$val['gid']]:array());
            }

            $this->_success($list,$tag_name);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };


    }


    /**
     * 更新库存
     * @param array stock
     * @param array $goods_id
     */
    public function actionUpdateStocks(){
        try{
            $this->params = array('token','sid','stocks');
            $requiredFields = array('token','sid','stocks');
            $decryptFields = array('token','sid');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $sid = $post['sid'];
            $stocks = $post['stocks'];
            $stocks = CJSON::decode(str_replace('\"', '"', $stocks));   //商品列表
            if (empty($stocks)) {
                $this->_error(Yii::t('goods','库存参数解析错误'),ErrorCode::COMMOM_ERROR);
            }
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
                $this->_error(Yii::t('goods','部分商品不存在'),ErrorCode::COMMOM_ERROR);
            }
            $ogids = array();
            $onums = array();
            foreach ($goods as $g){
                $ogids[] = $g['goods_id'];
                $onums[] = $nums[$g['id']];
            }
            $stocks_rs = ApiStock::stockSetList($store['id'], $ogids,$onums,API_PARTNER_SUPER_MODULES_PROJECT_ID);
            if ($stocks_rs['result']==true) {
                $this->_success(Yii::t('goods','更新成功'));
            }else{
                $this->_success(Yii::t('goods','更新失败'));
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };


    }




    /**
     * 商品上架
     * @param array stock
     * @param array $goods_id
     */
    public function actionEnable(){
        try{
            $this->params = array('token','sid','barcode','stock');
            $requiredFields = array('token','barcode','stock');
            $decryptFields = array('token','sid','barcode');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $stock = $post['stock'];
            $barcode = $post['barcode'];
            if ($this->getParam('onlyTest')==1) {
                $barcode = $this->getParam('barcode');    //条码
            }
            if (empty($barcode)) {
                $this->_error(Yii::t('goods','条码不能为空'));
            }
            $this->_checkStore();
            $store = $this->store;
            if ($store->member_id!=$this->member) {
                $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
            }

            $goods = Goods::model()->find('member_id=:member_id AND barcode=:barcode',array(':member_id'=>$this->member,':barcode'=>$barcode));
            if (empty($goods)) {
                $this->_error(Yii::t('goods','商品不存在'));
            }

            if ($goods->status!=Goods::STATUS_PASS) {
                $this->_error(Yii::t('goods','商品未通过审核'));
            }

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

            $this->_success(Yii::t('goods','设置成功'));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }


    /**
     * 商品条形码
     * @param barcode
     */
    public function actionBarcodeGoods(){
        try{
            $this->params = array('token','sid','barcode');
            $requiredFields = array('token','sid','barcode');
            $decryptFields = array('token','sid','barcode');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $barcode = $post['barcode'];
            if ($this->getParam('onlyTest')==1) {
                $barcode = $this->getParam('barcode');
                $sid = $this->getParam('sid');
            }
            $this->_checkStore();
            $store = $this->store;
            if ($store->member_id!=$this->member) {
                $this->_error(Yii::t('goods','不能修改他人的店铺'));
            }

            $stock = 0;
            if (!empty($this->store['id'])) {
                $goods = Yii::app()->db->createCommand()
                    ->select('sg.id as id,g.id as gid,g.name,g.cate_id,CONCAT("'.ATTR_DOMAIN.'/",thumb) as thumb,g.price,g.barcode,g.status,sg.status as enable_status')
                    ->from('{{goods}} as g')
                    ->leftJoin(SuperGoods::model()->tableName().' as sg', 'g.id=sg.goods_id')
                    ->where('g.member_id=:member_id AND sg.super_id=:super_id',array(':member_id'=>$this->member,':super_id'=>$this->store['id']))
                    ->andwhere('g.barcode =:barcode',array(':barcode'=>$barcode))
                    ->queryRow();
            }

            if (!empty($goods)) {

                $goods['enable_status_name'] = SuperGoods::getStatus($goods['enable_status']);

                $stock_rs = ApiStock::goodsStockOne($this->store['id'], $goods['gid'],API_PARTNER_SUPER_MODULES_PROJECT_ID);
                if (isset($stock_rs['result']['result']) && $stock_rs['result']['result']==true) {
                    $stock =$stock_rs['result']['stock'];
                }
            }else{
                $goods = Yii::app()->db->createCommand()
                    ->select('g.name,g.cate_id,CONCAT("'.ATTR_DOMAIN.'/",thumb) as thumb,g.price,g.barcode,g.status')
                    ->from('{{goods}} as g')
                    ->where('g.member_id=:member_id',array(':member_id'=>$this->member))
                    ->andwhere('g.barcode =:barcode',array(':barcode'=>$barcode))
                    ->queryRow();

                if(empty($goods)){
                    $this->_error(Yii::t('goods','商品不存在'),ErrorCode::COMMOM_ERROR);
                }

                $goods['status_name'] = Goods::getStatus($goods['status']);
            }

            $goods['barcode'] = $goods['barcode'];
            $goods['stock'] = $stock*1;

            $this->_success($goods);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }


    /**
     * 商品下架
     * @param array stock
     * @param array $goods_id
     */
    public function actionDisable(){
        try{
            $this->params = array('token','sid','goodsId');
            $requiredFields = array('token','sid','goodsId');
            $decryptFields = array('token','sid','goodsId');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $goods_id = $post['goodsId'];
            if ($this->getParam('onlyTest')==1) {
                $goods_id = $this->getParam('goodsId')*1;    //商品id
            }

            if (empty($goods_id)) {
                $this->_error(Yii::t('goods','id不能为空'));
            }

            $goods = SuperGoods::model()->findByPk($goods_id);

            if (empty($goods)) {
                $this->_error(Yii::t('goods','商品不存在'));
            }

            $store = Supermarkets::model()->findByPk($goods->super_id);
            if (empty($store)) {
                $this->_error(Yii::t('goods','门店不存在'));
            }

            if ($store['status']!=Supermarkets::STATUS_ENABLE) {
                $this->_error(Yii::t('goods','当前门店状态为禁用或者未审核，禁止使用。'));
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

            $this->_success(Yii::t('goods','设置成功'));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }



    /**
     * 更改商品价格
     * goodsId  商品id
     * price  商品价格
     */
    public function actionChangePrice(){
        try{
            $this->params = array('token','goodsId','price');
            $requiredFields = array('token','goodsId','price');
            $decryptFields = array('token','goodsId','price');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $goods_id = $post['goodsId'];				//商品id
            $price = $post['price'];   //商品价格
            if ($this->getParam('onlyTest')==1) {
                $goods_id = $this->getParam('goodsId')*1;    //商品id
                $price = $this->getParam('price')*1;
            }
            $sql = "UPDATE {{goods}} SET price='{$price}' WHERE id  = ".$goods_id;
            $rs = Yii::app()->db->createCommand($sql)->execute();
            if($rs){
                $this->_success( Yii::t('storeGoods','价格更新成功'));
            }else{
                $this->_success( Yii::t('storeGoods','价格更新成功',null));
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }


    /**
     * 设置是否参加一元购、专供、促销活动
     * type 设置活动的类型  is_one ：一元购 、is_for：专供  、is_promo：促销活动
     * status 是否参加活动  0 表示不参加  、1 表示参加
     */
    public function actionChangeIsActivities(){
        try{
            $this->params = array('token','goodsId','type','status');
            $requiredFields = array('token','goodsId','type','status');
            $decryptFields = array('token','goodsId','type','status');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);

            $goods_id = $post['goodsId'];				//商品id
            $type =  $post['type'];                                            //活动类型
            $status = $post['status'];                                         //活动状态
            if ($this->getParam('onlyTest')==1) {
                $goods_id = $this->getParam('goodsId')*1;    //商品ids
                $type = $this->getParam('type');
                $status = $this->getParam('status')*1;
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
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };


    }



    /**
     * 添加或更新商品
     */
    public function actionSave(){
        try {
            $fields = array('token','barcode','act','goodsId','isBarcode','name','supply_price','price','thumb','cate_id','content');
            $this->params = $fields;
            $requiredFields = array('token','barcode','act','name','supply_price','price','thumb','cate_id','content');
            $decryptFields = array('token','barcode','act','goodsId','price','cate_id');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $barcode = $post['barcode'];
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
                $model->member_id = $this->member;
                $model->partner_id = $this->partner;
                $model->create_time = time();
                $model->name = rtrim($model->name);
                $saveDir = 'partnerGoods/' . date('Y/n/j');
//                if ($is_create) $model = UploadedFile::uploadFile($model, 'thumb', $saveDir, Yii::getPathOfAlias('att'));
            }
            if($act == 2 && !empty($goodsId)){

                $model = $this->loadModel($goodsId);

                $model->member_id = $this->member;
                $model->scenario = 'update';
                $is_update = true;

                if (isset($isBarcode) && $isBarcode == 1) {
                    $model->scenario = 'barcode_update';
                    $is_update = false;
                }
                $oldFile = $model->thumb;
                $model->attributes = $post;
                $model->name = trim($model->name);
                $saveDir = 'partnerGoods/' . date('Y/n/j');

//                if ($is_update) $model = UploadedFile::uploadFile($model, 'thumb', $saveDir, Yii::getPathOfAlias('att'), $oldFile);  // 上传图片
            }

            if($model->save()){

                if(isset($oldFile)){
//                    if ($is_update) UploadedFile::saveFile('thumb', $model->thumb, $oldFile, true);
                    $this->_success(Yii::t('goods', '商品编辑成功'));
                }else{
//                    if ($is_create) UploadedFile::saveFile('thumb', $model->thumb);
                    $this->_success(Yii::t('goods', '商品创建成功'));
                }
            }else{
                var_dump($model->errors);
            }

        }catch (Exception $e){

            $this->_error($e->getMessage());
        }
    }

    /**
     * 验证商品条形码
     */
    public function actionCheckBarcode(){
        try{
            $fields = array('token','barcode','status','goodsId');
            $this->params = $fields;
            $requiredFields = array('token','barcode','status');
            $decryptFields = array('token','barcode','status','goodsId');

            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $barcode = $post['barcode'];
            $status = $post['status'];
            $goodsId = isset($post['goodsId'])?$post['goodsId']:'';
            //            添加商品时
            if($status == 1){
                $count = Goods::model()->count('member_id=:member_id AND barcode=:barcode',array(':member_id'=>$this->member_id,':barcode'=>$this->$attribute));
                if ($count>0) {
                    $this->_error(Yii::t('goods', '条形码已存在'));
                }else{
                    $model = BarcodeGoods::model()->find('barcode=:bc', array(':bc' => $barcode));
                    if(!empty($model)){
                        $this->_success($model);
                    }
                }

            }
            //            更新商品时
            if($status == 2 && !empty($goodsId)){
                $rs =	Yii::app()->db->createCommand()
                    ->select('id')
                    ->from('{{goods}}')
                    ->where('member_id=:member_id AND barcode=:barcode AND id != :id', array(':member_id'=>$this->member_id,':barcode'=>$barcode,':id'=>$goodsId))
                    ->queryRow();
                if (!empty($rs)) {
                    $this->_error(Yii::t('goods', '条形码已存在'));
                }else{
                    $model = BarcodeGoods::model()->find('barcode=:bc', array(':bc' => $barcode));
                    if(!empty($model)){
                        $this->_success($model);
                    }
                }

            }
        }catch (Exception $e){

            $this->_error($e->getMessage());
        }

    }

    /**
     * 获取单个商品信息
     */
    public function actionProductInfo(){
        try{
            $fields = array('token','goodsId');
            $this->params = $fields;
            $requiredFields = array('token','goodsId');
            $decryptFields = array('token','goodsId');

            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $goodsId =$post['goodsId'];
            $data = Yii::app()->db->createCommand()
                ->select("g.id, g.name goodName,g.price,gc.name cate_name,g.thumb,g.status,gc.id cate_id")
                ->from("{{goods}} as g")
                ->leftjoin("{{goods_category}} as gc","g.cate_id = gc.id")
                ->where('g.member_id=:member_id AND g.id = :id',array(':member_id'=>$this->member,':id'=>$goodsId))
                ->queryRow();

            if(!empty($data)){
                if(!empty($data['thumb'])){
                    $data['thumb'] = ATTR_DOMAIN . '/' .$data['thumb'];
                }
            }
            //            获取该商户商品分类
            $goodsCategory = GoodsCategory::model()->findAllByAttributes(array('member_id'=>$this->member));
            $this->_success(array('goods'=>$data,'cate'=>$goodsCategory));

        }catch (Exception $e){

            $this->_error($e->getMessage());
        }

    }
    /**
     * 商品列表
     * num  每页显示商品数
     * cateId  商品分类id
     * page  页码数
     */
    public function actionIndex(){
        try {
            $fields = array('token','sid','num','cateId','page');
            $this->params = $fields;
            $requiredFields = array('token');
            $decryptFields = array('token','cateId','sid');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $this->_checkStore();
            $store = $this->store;
            $cateId = ( isset($post['cateId']) && is_numeric($post['cateId']) ) ? $post['cateId'] :'';
            $page = ( isset($post['page']) && is_numeric($post['page']) ) ? $post['page'] : 1;          //页码
            $limit = ( isset($post['num']) && is_numeric($post['num']) )? $post['num'] : 8;
            $limit = ($limit > 20) ?  20 : $limit;     //显示多少条
            $where = "g.member_id = ".$this->member." AND sg.super_id = ".$store['id'];
            if(!empty($cateId)){
                $where .= " AND gc.id = ".$cateId;
            }
            $data = Yii::app()->db->createCommand()
                ->select("g.id, g.name goodName,g.price,gc.name cate_name,g.thumb,g.status,sg.status superStatus")
                ->from("{{goods}} as g")
                ->leftjoin("{{goods_category}} as gc","g.cate_id = gc.id")
                ->leftjoin("{{super_goods}} as sg","sg.goods_id = g.id")
                ->where($where)
                ->order('sg.status ASC,g.id ASC')
                ->limit($limit)
                ->offset(($page-1)*$limit)
                ->queryAll();
            $goodsArray = array();
            if(isset($data)){
                foreach($data as $k => $v){
                    $v['thumb'] = ATTR_DOMAIN . '/' .$v['thumb'];
                    $v['status'] = Goods::getStatus($v['status']);
                    $v['superStatus'] = SuperGoods::getStatus($v['superStatus']);
                    $goodsArray[] = $v;
                }
            }
            $this->_success(array('list'=>$goodsArray,'page'=>$page));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }

    /**
     * 获取商家商品分类接口
     */
    public function actionGoodsCateList(){
        try{
            $fields = array('token');
            $this->params = $fields;
            $requiredFields = array('token');
            $decryptFields = array('token');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $list= Yii::app()->db->createCommand()
                ->from(StoreCategory::model()->tableName())
                ->select('name,id')
                ->where('member_id=:member_id',array(':member_id'=>$this->member))
                ->queryAll();
            $this->_success(array('list'=>$list));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }

    /**
     * 添加自定义商品分类接口
     */
    public function actionAddStoreCate(){
        try{

            $fields = array('token','name','sort');
            $this->params = $fields;
            $requiredFields = array('token','name','sort');
            $decryptFields = array('token','sort');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            }

            $name = $post['name'];
            $sort = $post['sort'];
            if($sort>255){
                $this->_error('最大排序为255!');
            }
            $count = GoodsCategory::model()->count('member_id=:id AND name=:name',array(':id'=> $this->member,':name'=>$name));
            if ($count>0) {
                $this->_error('分类名已存在！');
            }
            $model = new GoodsCategory;
            $model->scenario = 'create';
            $model->attributes = $post;
            $model->member_id = $this->member;
            if($model->save()){
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'添加商家分类:'.$model->name.'| id->'.$model->id);
                $this->_success('添加商家分类成功！');
            }else{
                $this->_error('添加商家分类失败！');
            }

        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }

    /**
     * 自定义商品分类接口
     */
    public function actionEditStoreCate(){
        try{
            $fields = array('token','name','sort','cateId');
            $this->params = $fields;
            $requiredFields = array('token','name','sort','cateId');
            $decryptFields = array('token','sort','cateId');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            }
            $name = $post['name'];
            $sort = $post['sort'];
            $id = $post['cateId'];

            if($sort>255){
                $this->_error('最大排序为255!');
            }
            $rs =	Yii::app()->db->createCommand()
                ->select('id')
                ->from('{{goods_category}}')
                ->where('member_id=:id AND name=:name', array(':id' => $this->member,':name'=>$name))
                ->queryRow();
            if (!empty($rs) && $rs['id']!=$id) {
                $this->_error('分类名已存在！');
            }
            $model = GoodsCategory::model()->findByPk($id);
            $model->name = $name;
            $model->sort = $sort;
            if($model->save()){
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'添加商家分类:'.$model->name.'| id->'.$model->id);
                $this->_success('更新商家分类成功！');
            }else{
                $this->_error('更新商家分类失败！');
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }

    /**
     * 删除商家自定义分类
     */
    public function actionDelStoreCate(){
        try{
            $fields = array('token','cateId');
            $this->params = $fields;
            $requiredFields = array('token','cateId');
            $decryptFields = array('token','cateId');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $id = $post['cateId'];
            $model = GoodsCategory::model()->findByPk($id);

            if(empty($model->goods)){
                if($model->delete()){
                    ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeDel,$model->id,'删除商家分类:'.$model->name.'| id->'.$model->id);
                    $this->_success('success','删除商家分类成功');
                }
            }else{
                $this->_error('该分类下有商品，不可删除！');
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }

    /**
     * 获取商品库商品分类列表
     */
    public function actionCateList(){
        try{
            $this->params = array('token');
            $requiredFields = array('token');
            $decryptFields = array('token');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $data = Yii::app()->db->createCommand()
                ->select('*')
                ->from('{{goods_category}}')
                ->where('member_id=:member_id',array(':member_id'=>$this->member))
                ->queryAll();
            $this->_success($data);
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }

    /**
     * 商品库商品列表接口
     */
    public function actionGoodsList(){
        try{
            $this->params = array('token','sid','page','pageSize','cateId');
            $requiredFields = array('token');
            $decryptFields = array('token','sid','cateId');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $this->_checkStore();
            $store = $this->store;
            if ($store->member_id!=$this->member) {
                $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
            }
            $cateId = ( isset($post['cateId']) && is_numeric($post['cateId']) ) ? $post['cateId'] :'';
            $page = ( isset($post['page']) && is_numeric($post['page']) ) ? $post['page'] : 1;          //页码
            $limit = ( isset($post['pageSize']) && is_numeric($post['pageSize']) )? $post['pageSize'] : 20;
            $limit = ($limit > 20) ?  20 : $limit;     //显示多少条
            $where = "g.member_id = ".$this->member;
            if(!empty($cateId)){
                $where .= " AND gc.id = ".$cateId;
            }
            $data = Yii::app()->db->createCommand()
                ->select("g.id, g.name goodName,g.price,gc.name cate_name,g.thumb,g.status")
                ->from("{{goods}} as g")
                ->leftjoin("{{goods_category}} as gc","g.cate_id = gc.id")
                ->where($where)
                ->order('g.id ASC')
                ->limit($limit)
                ->offset(($page-1)*$limit)
                ->queryAll();
            $goodsArray = array();
            if(isset($data)){
                foreach($data as $k => $v){
                    $v['thumb'] = ATTR_DOMAIN . '/' .$v['thumb'];
                    $v['status'] = Goods::getStatus($v['status']);
                    $goodsArray[] = $v;
                }
            }
            $this->_success(array('list'=>$goodsArray,'page'=>$page));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }

    /**
     * 添加或更新商家商品分类接口
     */
    public function actionGoodsCateSave(){
        try{
            $fields = array('token','act','cateId','name');
            $this->params = $fields;
            $requiredFields = array('token','cateId','name','act');
            $decryptFields = array('token','cateId','name');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $this->_checkStore();
            $store = $this->store;

            if ($store->member_id!=$this->member) {
                $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
            }

            if ($post['act']==1) {
                $cate = new GoodsCategory();
                $cate->name = $post['name'];
                $cate->member_id = $this->member;
                $cate->save();
            }

            if ($post['act']==2 && !empty($post['cateId'])) {
                $cate = GoodsCategory::model()->findByPk($post['cateId']);

                if (empty($cate) || $cate->member_id!=$this->member) {
                    $this->_error(ErrorCode::getErrorStr(ErrorCode::CLIENT_NO_ACCESS),ErrorCode::CLIENT_NO_ACCESS);
                }

                $cate->name = $post['name'];
                $cate->member_id = $this->member;
                $cate->save();
            }

            $this->_success('');
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }



}