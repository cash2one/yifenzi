<?php

/**
 * 超市商品管理
 * 操作(查看，添加，修改)
 * @author zsj
 */
class StoreGoodsController extends SuperController{
    /**
     * 超市商品列表
     * status  门店商品状态用于搜索
     * lastId
     * num  每次请求数据条数
     * goodsName   商品名称用于搜索
     */
    public function actionIndex(){
        try{
            $fields = array('token','status','lastId','num','goodsName');
            $this->params = $fields;
            $requiredFields = array('token');
            $decryptFields = array('token');

            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $lastId = ( isset($post['lastId']) && is_numeric($post['lastId']) ) ? $post['lastId'] :-1;
            $limit = ( isset($post['num']) && is_numeric($post['num']) )? $post['num'] : 8;
            $limit = ($limit > 20) ?  20 : $limit;     //显示多少条
            $status = isset($post['status'])?$post['status']:"";
            $goodsName = isset($post['goodsName'])?$post['goodsName']:"";
            if(empty($this->super_id)){
                $this->_error(Yii::t('storeGoods','请先添加门店'));
            }
            $super = Supermarkets::model()->findByPk($this->super_id);

            if($super->status ==  Supermarkets::STATUS_APPLY || $super->status ==  Supermarkets::STATUS_DISABLE){
                $this->_error(Yii::t('storeGoods','该门店未通过审核或被禁用！'));
            }
            $where = "";
            if(!empty($goodsName)){
                $where .= " g.name like '%".$goodsName."%'";
                if(!empty($status)){
                    $where .=" AND t.status = ".$status." AND g.id >".$lastId." AND t.super_id =".$this->super_id;
                }
            }else{
                if(!empty($status)){
                    $where .= "t.status =".$status." AND g.id > ".$lastId." AND t.super_id =".$this->super_id;
                }else{
                    $where .= "g.id > ".$lastId." AND t.super_id =".$this->super_id;
                }
            }
            $data = Yii::app()->db->createCommand()
                ->select("t.goods_id,t.status,g.status goodStatus,g.name,g.thumb,g.price")
                ->from("{{super_goods}} t")
                ->leftjoin("{{goods}} g","t.goods_id = g.id")
                ->where($where)
                ->order('g.id ASC')
                ->limit($limit)
                ->queryAll();
            //查询库存
            $list = array();
            if(!empty($data)){
                $good_ids = array();
                foreach ($data as $v){
                    $good_ids[] = $v['goods_id'];
                }
                $stocks = ApiStock::goodsStockList($this->super_id, $good_ids);

                foreach($data as $v2){
                    if(isset($stocks[$v2['goods_id']])){
                        $v2['stock']=$stocks[$v2['goods_id']]['stock'];
                        $v2['frozenStock']=$stocks[$v2['goods_id']]['frozenStock'];
                        $v2['thumb'] = ATTR_DOMAIN . '/' .$v2['thumb'];
                        $v2['status'] = SuperGoods::getStatus($v2['status']);
                        $v2['goodStatus'] = Goods::getStatus($v2['goodStatus']);
                        $list[]=$v2;
                    }
                }
            }
            $this->_success(array('list'=>$list));
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }
    /**
     * 超市商品进货
     * status为1表示请求数据  2表示提交数据
     * goods_id 商品id
     * num  进货数
     */
    public function actionStockIn(){
        try{
            $fields = array('token','goods_id','status','num');
            $this->params = $fields;
            $requiredFields = array('token','goods_id','status');
            $decryptFields = array('token');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            if(isset($post['goods_id'])){
                $id = $post['goods_id'];
            }else{
                $this->_error('参数错误');
            }
            $status = $post['status'];
            $num = isset($post['num'])?$post['num']:'';
            $model = SuperGoods::model()->findByPk($id);
            $this->_checkGoodsAccess($model);
            $list = array();
            $list['id'] = $model->id;
            $list['goodsName'] = $model->goods->name;

            if($status == 1){
                $this->_success($list);
            }elseif($status == 2 && !empty($num)){
                $model->scenario = 'stock';
                $stock = ApiStock::goodsStockOne($this->super_id, $model->goods_id);
                $stock = isset($stock['result']['stock'])?$stock['result']['stock']*1:0;
                $stock_config = $this->params('stock');
                if ($stock_config['maxStock']<=($stock+ $num)) {
                    $this->_error(Yii::t('storeGoods', '不能超过最大库存，最大库存为').$stock_config['maxStock']);
                }
                //接口创建库存
                $stocks_rs = ApiStock::stockIn($this->super_id, $model->goods_id,$num);
                if (isset($stocks_rs['result']) && $stocks_rs['result']==true) {
                    ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'超市商品进货:'.$model->name.'| id->'.$model->id.'| num->'.$num);
                    $this->_success( Yii::t('storeGoods','超市商品库存进货成功！'));
                } else {
                    $this->_error(Yii::t('storeGoods','超市商品库存进货失败'));
                }
            }else{
                $this->_error('参数错误');
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }
    /**
     * 超市商品出货
     * status为1表示请求数据  2表示提交数据
     * goods_id 商品id
     * num  出货数
     */
    public function actionStockOut(){
        try{
            $fields = array('token','goods_id','status','num');
            $this->params = $fields;
            $requiredFields = array('token','goods_id','status');
            $decryptFields = array('token');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            if(isset($post['goods_id'])){
                $id = $post['goods_id'];
            }else{
                $this->_error('参数错误');
            }
            $status = $post['status'];
            $num = isset($post['num'])?$post['num']:'';
            $model = SuperGoods::model()->findByPk($id);
            $this->_checkGoodsAccess($model);
            $list = array();
            $list['id'] = $model->id;
            $list['goodsName'] = $model->goods->name;
            if($status == 1){
                $this->_success($list);
            }elseif($status == 2 && !empty($num)){
                //接口创建库存
                $stocks_rs = ApiStock::stockOut($this->super_id, $model->goods_id,$num);
                if (isset($stocks_rs['result']) && $stocks_rs['result']==true) {
                    ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeUpdate,$model->id,'超市商品出货:'.$model->goods->name.'| id->'.$model->id.'| num->'.$num);
                    $this->_success(Yii::t('storeGoods','超市商品库存出货成功！'));
                } else {
                    $this->_error(Yii::t('storeGoods','超市商品库存出货失败').(isset($stocks_rs['msg'])?'|'.$stocks_rs['errorCode'].':'.$stocks_rs['msg']:''));
                }
            }else{
                $this->_error('参数错误');
            }

        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }
    /**
     * 超市门店商品上架
     * goods_id 商品id
     */
    public function actionEnable(){
        try{
            $fields = array('token','goods_id');
            $this->params = $fields;
            $requiredFields = array('token','goods_id');
            $decryptFields = array('token');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            if(isset($post['goods_id'])){
                $id = $post['goods_id'];
            }else{
                $this->_error('参数错误');
            }
            $model = SuperGoods::model()->findByPk($id);
            $this->_checkGoodsAccess($model);

            if($model->goods->status == Goods::STATUS_NOPASS || $model->goods->status == Goods::STATUS_AUDIT){
                $this->_error( Yii::t('storeGoods','商品审核未通过，不能上架'));

            }
            $model->status = SuperGoods::STATUS_ENABLE;
            if($model->save()){
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'设置超市商品上架：:'.$model->goods->name.'| id->'.$model->id);
                $this->_success(Yii::t('storeGoods','超市商品上架成功'));

            } else {
                $this->_error(Yii::t('storeGoods','设置超市商品上架'));
            }

        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }

    /**
     * 超市门店商品下架
     * goods_id   商品id
     */
    public function actionDisable(){
        try{
            $fields = array('token','goods_id');
            $this->params = $fields;
            $requiredFields = array('token','goods_id');
            $decryptFields = array('token');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            if(isset($post['goods_id'])){
                $id = $post['goods_id'];
            }else{
                $this->_error('参数错误');
            }
            $model = SuperGoods::model()->findByPk($id);
            $this->_checkGoodsAccess($model);
            if($model->status == SuperGoods::STATUS_DISABLE){
                $this->_error(Yii::t('storeGoods','商品审核未通过，已自动下架'));
            }
            $model->status = SuperGoods::STATUS_DISABLE;
            $tran = Yii::app()->db->beginTransaction();
            if($model->save()){
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
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'设置超市商品下架：:'.$model->goods->name.'| id->'.$model->id);
                $this->_success(Yii::t('storeGoods','超市商品下架成功'));

            } else {
                $this->_error(Yii::t('storeGoods','设置超市商品下架'));
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }
    /**
     * 添加超市门店商品
     * goods_id 商品id
     * num  库存数量
     */
    public function actionAdd(){
        try{
            $fields = array('token','goods_id','num');
            $this->params = $fields;
            $requiredFields = array('token','goods_id');
            $decryptFields = array('token');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $goodsId = $post['goods_id']*1;
            $num = $post['num']*1;
            $model = new SuperGoods();
            $model->scenario = 'add';
            $model->goods_id = $goodsId;
            $model->super_id = $this->super_id;
            $model->create_time = time();

            //检查重复商品
            if (SuperGoods::model()->count(' super_id=:super_id AND goods_id=:goods_id  ',array(':super_id'=>$this->super_id,':goods_id'=>$model->goods_id))) {
                $this->_error( Yii::t('storeGoods','超市商品已存在！'));
            }

            //接口创建库存
            $stocks_rs = ApiStock::createStock($this->super_id, $model->goods_id,$num);
            if (!isset($stocks_rs['result']) || $stocks_rs['result']!=true) {
                $this->_error(Yii::t('storeGoods','添加库存失败！'));
            }
            if ($model->save()) {
                ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'添加超市商品:'.$model->name.'| id->'.$model->id);
                $this->_success( Yii::t('storeGoods','添加超市商品成功'));


            } else {
                $this->_error(Yii::t('storeGoods','添加超市商品失败'));
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }

    /**
     * 搜索商品列表接口
     * type  商品类型（1 表示超市门店；2 表示售货机  3 表示生鲜机
     * lastId
     * num  列表每次请求条数
     * goodsName  商品名称  用于搜索商品
     * sid  机器或者门店id
     */
    public function actionSearchList(){
        try{
            $fields = array('token','type','lastId','num','goodsName','sid');
            $this->params = $fields;
            $requiredFields = array('token');
            $decryptFields = array('token');
            if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                $post = $_REQUEST;
            }else{
                $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);
            }
            $lastId = ( isset($post['lastId']) && is_numeric($post['lastId']) ) ? $post['lastId'] :-1;
            $limit = ( isset($post['num']) && is_numeric($post['num']) )? $post['num'] : 8;
            $limit = ($limit > 20) ?  20 : $limit;     //显示多少条
            $type = isset($post['type'])?$post['type']:"";//商品类型（属于什么机器）
            $sid = isset($post['sid'])?$post['sid']:"";//门店id或机器id
            $goodsName = isset($post['goodsName'])?$post['goodsName']:"";//商品名称用于搜索商品
            $where = "t.member_id= ".$this->member." AND t.status= ".Goods::STATUS_PASS." AND t.id > ".$lastId;
            if(!empty($goodsName)){
                $where .= " AND t.name like '%".$goodsName."%'";
            }
            if (!empty($type)) {
                switch ($type){
                    case Stores::SUPERMARKETS:
                        $sgood = Yii::app()->db->createCommand()
                            ->from(SuperGoods::model()->tableName())
                            ->select('goods_id')
                            ->where('super_id=:super_id',array(':super_id'=>$sid))
                            ->queryAll();

                       if(isset($sgood)){
                           $gids = array();
                           foreach ($sgood as $v){
                               $gids[] = $v['goods_id'];
                           }
                           $str =implode(',',$gids);
                           $str = trim($str, ",");
                           $where .= " AND t.id in (".$str.")";
                       }
                        break;


                    case Stores::MACHINE:

                        $sgood = Yii::app()->db->createCommand()
                            ->from(VendingMachineGoods::model()->tableName())
                            ->select('goods_id')
                            ->where('machine_id=:machine_id',array(':machine_id'=>$sid))
                            ->queryAll();

                        if(isset($sgood)){
                            $gids = array();
                            foreach ($sgood as $v){
                                $gids[] = $v['goods_id'];
                            }
                            $str =implode(',',$gids);
                            $str = trim($str, ",");
                            $where .= " AND t.id in (".$str.")";
                        }
                        break;

                    case Stores::FRESH_MACHINE:

                        $sgood = Yii::app()->db->createCommand()
                            ->from(FreshMachineGoods::model()->tableName())
                            ->select('goods_id')
                            ->where('machine_id=:machine_id',array(':machine_id'=>$sid))
                            ->queryAll();

                        if(isset($sgood)){
                            $gids = array();
                            foreach ($sgood as $v){
                                $gids[] = $v['goods_id'];
                            }
                            $str =implode(',',$gids);
                            $str = trim($str, ",");
                            $where .= " AND t.id in (".$str.")";
                        }
                        break;
                }
            }

            $data = Yii::app()->db->createCommand()
                ->select("t.id,t.name")
                ->from("{{goods}} t")
                ->where($where)
                ->order('t.id ASC')
                ->limit($limit)
                ->queryAll();
            if(!empty($data)){
                $lastList = end($data);
            }
            $this->_success(array('list'=>$data,'lastId'=>isset($lastList['id'])?$lastList['id']:'-1'));

        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }
}