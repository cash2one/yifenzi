<?php

/**
 * 前端产品控制器
 * @author xuqiuye<416652337@qq.com>
 * @since 2016-04-07
 */
class GoodsController extends YfzController
{

    public $footerPage = 2;
    public $layout = 'goods'; //当前的布局文件
    protected $model; //定义model保存访问当前的产品
    public $itemsPerPage = 10;  //下拉的界面每页显示的条数

    /**
     * 产品详情页
     * @param type $id
     */

    public function actionView($id,$nper)
    {
        $cache = $this->caches();
        $this->_checkProduct();
        $this->footerDisplay = false;
        $model = $this->model; //判断期数
        $this->pageTitle = Yii::t('yfzGoods', '一份子 商品详情');
        if (is_numeric($id)) {
            //if (!$orderGoods || !$modelImage) {
            if ($cache->get("product_modelimage".$id)){
                $modelImage = $cache->get("product_modelimage".$id);
            }else{
                $modelImage = YfzGoodsImage::model()->find('goods_id=:id', array(':id' => $id)); //产品图片
                $cache->set("product_modelimage".$id, $modelImage, 24*60*60);
            }

            $showImage1 = $modelImage['show_image1'];
            $showImage1Exp = explode('|',$showImage1);
            if ($cache->get("product_orderGoods".$id)){
                $modelImage = $cache->get("product_ordergoods".$id);
            }else{
                $orderGoods = YfzOrderGoods::getGoodsNumber($id, $model->current_nper);
                $cache->set("product_ordergoods".$id, $orderGoods, 24*60*60);
            }

            //检测 产品是否当期产品
            $oldNper = (int) Yii::app()->request->getParam('nper', 0);
			$nowNper = $model['current_nper'];
            GoodsStatistics::addViews($model->goods_id); //点击量
            $sumlotterytime = 0 ;
            if ($oldNper && $oldNper < $model->current_nper) { //期数参数存在而且小于当期的时候 检测是否已经开奖
                $nper = YfzOrderGoodsNpers::getResult($model->goods_id, $oldNper);

                $time = time(); //当前时间
                $sumlotterytime = $nper['sumlotterytime'] ;
                if (!($nper && $sumlotterytime > $time)){ //产品还没有开奖
                    $this->redirect(array('/yifenzi2/goods/result', 'id' => $model->goods_id, 'nper' => $oldNper));
				}
            }
        }
        
        $this->render('view', array(
            'model' => $model,
            'modelImage' => $modelImage,
            'orderGoods' => $orderGoods,
            'oldNper' => $oldNper,
			'nowNper' => $nowNper,
            'sumlotterytime'=> $sumlotterytime,
            'showImage1Exp' => $showImage1Exp,
        ));
    }

    /**
     * 产品图文详情
     * @param type $id
     */
    public function actionViewDesc($id)
    {
        $this->_checkProduct();
        $model = $this->model;
        $this->pageTitle = Yii::t('yfzGoods', '一份子 图文详情');
        if (is_numeric($id)) {
//            $cache = Yii::app()->redis;
//            $orderGoods = $cache->get($this->id . $this->action->id . $id . 'order');
//            if (!$orderGoods) {
//                $orderGoods = YfzOrderGoods::getGoodsNumber($id, $model->current_nper);
//            } else {
//                $orderGoods = unserialize($orderGoods);
//            }
        }
        $this->render('viewDesc', array(
            'model' => $model,
                //'orderGoods' => $orderGoods
                )
        );
    }
    
    /**
     * 中奖分母页面
     */
    public function actionDenominator($id,$nper){
        $this->_checkProduct();
        
        $goods_id = Yii::app()->request->getParam("id");
        $nper = Yii::app()->request->getParam("nper");
        
        //呓中奖表中是否存在此数据
        $sql = "select * from {{order_goods_nper}} where goods_id=$goods_id and current_nper=$nper";
        $nperData = Yii::app()->gwpart->createCommand( $sql )->queryRow();
        
        if( !$nperData ) 
            throw new CHttpException('404','无对应奖品');
        $sql = "SELECT
                	og.*,
                	o.addtime
                FROM
                	gw_yifenzi_order_goods AS og
                LEFT JOIN gw_yifenzi_order AS o ON og.order_id = o.order_id
                WHERE
                	og.goods_id = $goods_id
                AND og.current_nper = $nper";
        $allData = Yii::app()->gwpart->createCommand($sql)->queryAll();
        
        if( !$allData ) 
            throw new CHttpException('404','无对应奖品');
        
        $allnper = array();
        foreach ($allData as $k=>$v){
            $array_winning = json_decode($v['winning_code']);
        }
    }

    public function actionSearch(){
        print_r('ad');exit;
    }
    /**
     * 当前购买记录列表
     */
    public function actionRecord($id, $nper)
    {
        $pageSize = $this->itemsPerPage;
        $this->_checkProduct();
        $record = array();
        if($this->isAjax())
        {
            $page = $this->getParam('page');
            $id = $this->getParam('id');
            $nper =  $this->getParam('nper');
            $offer = ($page - 1) * $pageSize;
                $sql = "SELECT t.goods_number,o.member_id,o.addtime FROM {{order}} as o
                        left join {{order_goods}} as t on o.order_id = t.order_id
                        where t.goods_id={$id} and t.current_nper={$nper} and o.order_status=" . YfzOrder::STATUS_PAY_SUCCESS . " limit $offer,$pageSize"; //已支付订单
                $connection = Yii::app()->gwpart;
                $data = $connection->createCommand($sql)->queryAll();
            $model = $this->model;
            if(!empty($data)) {
                foreach ($data as $key => $val) {
                    $address = '';
                    $member = Member::getMemberInfo($val['member_id']);
                    $GWnumber = ($member && $member['gai_number']) ? substr_replace($member['gai_number'], '****', 4, 4) : '一份子';
                    $price = ceil($model->shop_price / $model->single_price);
                    $adress = Tool::GetIpLookup($val['member_id']);
                    if (!empty($adress)) {
                        $address = $adress['province'] . ' ' . $adress['city'];
                    } else {
                        $address = '广东 广州';
                    }
                    $addtime = date('Y-m-d H:i:s', $val['addtime']) . substr($val['addtime'], strpos($val['addtime'], '.'));
                    $record[$key]['GWnumber'] = $GWnumber;
                    $record[$key]['price'] = $price;
                    $record[$key]['address'] = $address;
                    $record[$key]['addtime'] = $addtime;
                    $record[$key]['goods_number'] = $val['goods_number'];
                }
            }else{
                exit(CJSON::encode(array('result' => false)));
            }
               /// $data = $model->getMemberBuyRecord($memberId, $page, $this->itemsPerPage);
            exit(CJSON::encode(array('result' => true, 'data' => $record)));
                Yii::app()->end();
        }
        if (is_numeric($id) && $nper) {
            $sql = "SELECT t.goods_number,o.member_id,o.addtime FROM {{order}} as o
                        left join {{order_goods}} as t on o.order_id = t.order_id
                        where t.goods_id={$id} and t.current_nper={$nper} and o.order_status=" . YfzOrder::STATUS_PAY_SUCCESS." ORDER BY o.addtime DESC limit {$pageSize}"; //已支付订单
            $connection = Yii::app()->gwpart;
            $record = $connection->createCommand($sql)->queryAll();
        }
        $this->render('recordNper', array('record' => $record, 'model' => $this->model,'limit'=>$pageSize,'goods_id'=>$id));
    }
    /**
     * 往期 记录
     */
    public function actionPeriods($id)
    {
        $this->_checkProduct();
        $this->pageTitle = Yii::t('yfzGoods', '一份子 期数');
        $model = $this->model;
        $this->render('periods', array('model' => $this->model));
    }

    /**
     * 往期中奖信息
     * @param type $id
     * @param type $nper
     * @return object Description
     */
    public function actionResult($id, $nper)
    {
        $this->_checkProduct();
        //$this->layout = 'main';
        $this->pageTitle = Yii::t('yfzGoods', '参与记录');
        if (!is_numeric($nper))
            throw new CHttpException(403, '错误的请求');
        $result = YfzOrderGoodsNpers::getResult($id, $nper);

        if($result['current_nper'] && $result['sumlotterytime'] > time()){
            $this->redirect(array('/yifenzi2/goods/view', 'id' => $id, 'nper' => $result['current_nper']));
        }

        if (!$result)
            throw new CHttpException(404, '找不到中奖用户');
        $this->render('result', array(
            'model' => $this->model,
            'winning' => $result,
        ));
    }

    /**
     * 限购专区
     */
    public function actionLimit()
    {
        $pageLimit = CPagination::DEFAULT_PAGE_SIZE;
        $model = new YfzGoods();
        $model->unsetAttributes();
        $model->attributes = $_GET;
        $this->layout = 'main';
        $limits = $model->searchAnnoucned($pageLimit); //得到限制条件
        if (empty($limits) && $this->isAjax())
            exit(CJSON::encode(array('result' => false)));
        $limit = array();
        foreach ($limits as $k => $d) { //重组数组
            $limit[$k] = array_filter($d->attributes);
            $limit[$k]['goods_thumb'] = $d->goods_thumb;
            $limit[$k]['salesTotal'] = YfzGoods::getCurrentSales($d->goods_id, $d->current_nper);
        }

        if ($this->isAjax()) {
            exit(CJSON::encode(array('result' => true, 'data' => $limit)));
            Yii::app()->end();
        }

        $this->render('limit', array('limit' => $pageLimit, 'limits' => $limit));
    }

    public function actionAnnounced()
    {
        $this->layout = 'main';

        $this->footerPage = 3;
        if (Yii::app()->request->getParam('retUrl')) $this->footerPage=2;

        $page = Yii::app()->request->getParam('page', 1);
        $this->pageTitle = '最新揭晓';
        $limit = CPagination::DEFAULT_PAGE_SIZE;
        $announced = YfzOrderGoodsNpers::getAnnounced($limit, $page);
		//print_r($announced);
		//exit;
        if ($this->isAjax()) {
            if (!$announced)
                exit(CJSON::encode(array('result' => false)));
            exit(CJSON::encode(array('result' => true, 'data' => $announced)));
        }
        $this->render('announce', array('announce' => $announced));
    }

    /**
     * 最新揭晓 揭露最后结果
     */
    public function actionPast()
    {
        if ($this->isAjax()) {
            $id = (int) Yii::app()->request->getPost('id'); //期数表id
//开奖 
            $result = YfzOrderGoodsNpers::goAnnounced($id);
            exit(CJSON::encode(array('result' => $result)));
        }
    }

    /**
     * 检查产品 是否存在
     */
    protected function _checkProduct()
    {
        $id = Yii::app()->request->getParam('id');
        $model = new YfzGoods;
        //$cache = Yii::app()->redis;
        //$goods = $cache->get($this->id . $this->action->id . $id);
        //if (!$goods) {
        $this->model = $model->findByPk($id, 'is_closed=:closed and is_on_sale=:on', array(':closed' => YfzGoods::IS_CLOSED_FALSE, 'on' => YfzGoods::IS_SALES_TRUE));
        //$cache->set($this->id . $this->action->id . $id, serialize($this->model), 3600);
        //} else {
        // $this->model = unserialize($goods);
        //}
        if (!$this->model)
            throw new CHttpException('404', '找不到商品');
        return true;
    }

    public function actionSend()
    {
        $i = 0;
        sleep(1);
        while (true) {
            $i++;
            //循环 50次后，无数据告诉客户端没有数据，断开连接     
            if ($i == 50) {
                echo json_encode(array('success' => false));
                exit();
            }
            $time = (int) Yii::app()->request->getPost('time');
//
            $npers = YfzOrderGoodsNpers::getSendAnnounce($time);
//            //若得到数据则马上返回数据给客服端，并结束本次请求      
            if ($npers) {
                $arr = array('success' => true, 'data' => $npers);
                echo json_encode($arr);
                exit();
            } 
        }
    }

    /**
     * 商品列表
     */
    /*public function actionList()
    {
//         echo (number_format(ceil(5888 / 1), 2));exit;
        $pageLimit = CPagination::DEFAULT_PAGE_SIZE;
        $model = new YfzGoods;
        $column = Yii::app()->request->getParam('column_id', 0);
        $model->column_id = $column;
        $this->pageTitle = '全部商品';
        $lists = $model->searchAnnoucned($pageLimit = CPagination::DEFAULT_PAGE_SIZE);
        if (empty($lists) && $this->isAjax())
            exit(CJSON::encode(array('result' => false)));
        $list = array();
        foreach ($lists as $k => $d) { //重组数组
            $list[$k] = array_filter($d->attributes);
            $list[$k]['goods_thumb'] = $d->goods_thumb;
            $list[$k]['salesTotal'] = YfzGoods::getCurrentSales($d->goods_id, $d->current_nper);
        }

        if ($this->isAjax()) {
            exit(CJSON::encode(array('result' => true, 'data' => $list)));
            Yii::app()->end();
        }
        $this->render('list', array('model' => $model, 'limit' => $pageLimit, 'lists' => $list));
    }*/
    
	/**
     * 商品列表
     */
    public function actionList()
    {
//         echo (number_format(ceil(5888 / 1), 2));exit;
        $pageLimit = CPagination::DEFAULT_PAGE_SIZE;
        $model = new YfzGoods;
        $column = Yii::app()->request->getParam('column_id', 0);
        $model->column_id = $column;
        $this->pageTitle = '全部商品';
        $goods_name = strtolower(Yii::app()->request->getParam('goods_name'));
        if( $this->isPost()){
            $_GET['page']="";
        }
		$criteria = new CDbCriteria();
		$criteria->select = 't.goods_id,t.goods_name,g.goods_thumb,t.shop_price,t.single_price,t.current_nper,t.add_time,t.sort_order,t.column_id';
		$criteria->join = 'LEFT JOIN {{goods_image}} AS g ON g.goods_id = t.goods_id';
		$criteria->addCondition('t.is_closed = :closed');    
        $criteria->params[':closed'] = YfzGoods::IS_CLOSED_FALSE; 
        $criteria->addCondition('t.is_on_sale = :sale');
        $criteria->params[':sale'] = YfzGoods::IS_SALES_TRUE;
		
        if ($column){
            $criteria->addCondition('t.column_id = :column_id');
            $criteria->params[':column_id'] = $column;
        }
		$criteria->addSearchCondition('t.goods_name',$goods_name);
		$criteria->order='t.sort_order desc,t.add_time desc';
        $count = $model->count($criteria);
        $pages = new CPagination($count);
		$pages->pageSize = CPagination::DEFAULT_PAGE_SIZE;
        $pages->applyLimit($criteria);

        $tempPages = ceil($count /  CPagination::DEFAULT_PAGE_SIZE);

        //临时用
        if (isset($_GET['page'])){
            if ($_GET['page'] > $tempPages ){
                exit(CJSON::encode(array('result' => false)));
            }
        }

        $lists = $model->findAll($criteria);
        if (empty($lists) && $this->isAjax())
            exit(CJSON::encode(array('result' => false)));
        $list = array();
        foreach ($lists as $k => $d) { //重组数组
            $list[$k] = array_filter($d->attributes);
            $list[$k]['goods_thumb'] = ATTR_DOMAIN . '/' . $d->goods_thumb;
            $list[$k]['salesTotal'] = YfzGoods::getCurrentSales($d->goods_id, $d->current_nper);
        }

        if ($this->isAjax()) {
            exit(CJSON::encode(array('result' => true, 'data' => $list)));
            Yii::app()->end();
        }
        $this->render('list', array('model' => $model, 'limit' => $pageLimit, 'lists' => $list));
    }
	
    /**
     * 云计算公式
     */
    public function actionCloud()
    {
        $this->pageTitle = '云计算公式';
        $id = (int)Yii::app()->request->getParam('id');
        $nper = (int)  Yii::app()->request->getParam('nper');
        $sql = 'select order_id_log,id,winning_code,member_id,sumlotterytime from {{order_goods_nper}} where goods_id=:id and current_nper=:nper';
        $connection = Yii::app()->gwpart;
        $command = $connection->createCommand($sql);
        $command->bindParam(':id',$id);
        $command->bindParam(':nper',$nper);
        $orderNper = $command->queryRow();
        if(!$orderNper) throw new CHttpException('404','找不到该产品的中奖数据');
        $this->render('cloud',array('orderNper'=>$orderNper));
    }

	/**
	* 幸运分子所有号码
	**/
	public function actionWinningAll()
	{
		$id = (int)Yii::app()->request->getParam('id');
        $nper = (int)  Yii::app()->request->getParam('nper');
		$winning_result = YfzOrderGoodsNpers::getResult($id, $nper);
        if (!$winning_result)
            throw new CHttpException(404, '找不到中奖用户');
		
		$win_code = $winning_result["winning_code"];
		
		$sql = "SELECT ogn.member_id, o.addtime, ogn.sumlotterytime, og.winning_code FROM {{order_goods_nper}} AS ogn 
		LEFT JOIN {{order_goods}} AS og ON og.goods_id = ogn.goods_id AND og.current_nper = ogn.current_nper AND og.order_id = ogn.order_id
		LEFT JOIN {{order}} AS o ON o.order_id = ogn.order_id
		WHERE ogn.goods_id = :gid AND ogn.current_nper = :nper AND ogn.winning_code = :win_code";

		$connection = Yii::app()->gwpart;
        $command = $connection->createCommand($sql);
        $command->bindParam(':gid',$id);
        $command->bindParam(':nper',$nper);
		$command->bindParam(':win_code',$win_code);
        $winningAll = $command->queryRow();
		$addtime = $winningAll["addtime"];
	
		$addtime_micr = $this->udate('Y-m-d H:i:s.u',$addtime);//格式化云购时间为毫秒

		$sumlotterytime = $winningAll["sumlotterytime"];
		//$sumtime_micr = $this->udate('Y-m-d H:i:s.u',$sumlotterytime);//格式化揭晓时间为毫秒

		$winningEach = trim($winningAll["winning_code"],'[]');

		$winningEachs = explode(',',$winningEach); 
		
		$page = Yii::app()->request->getParam('page',1);//当前页（第一页）
		$data = $this->page_array('80',$page,$winningEachs,0);

        if ($this->isAjax()) {
            exit(CJSON::encode(array('result' => true, 'data' => $data)));
            Yii::app()->end();
        }
		
		$sql2 = "SELECT shop_price,single_price FROM {{yfzgoods}} WHERE goods_id={$id}";
        $active = $connection->createCommand($sql2)->queryRow();
		$shop_price = $active["shop_price"];
		$single_price = $active["single_price"];
		$active_num = ceil($shop_price/$single_price);

		$this->render('winningAll',array('datas'=>$data,'winningEachs'=>$winningEachs,'active_num'=>$active_num,'addtime_micr'=>$addtime_micr,'sumlotterytime'=>$sumlotterytime,
		'model' => $this->model));
		
		
	}
	
	/*
	 格式化时间包含毫秒
	 */
    public function udate($format = 'u', $time) {
        $timestamp = floor($time);
        $milliseconds = round(($time - $timestamp) * 1000);
        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }
   
	
	/** 
    * 数组分页函数  核心函数  array_slice 
    * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中 
    * $count   每页多少条数据 
    * $page   当前第几页 
    * $array   查询出来的所有数组 
    * order 0 - 不变     1- 反序 
     */   
	public function page_array($count,$page,$array,$order){  
        $countpage =array(); //定全局变量  
        $page=(empty($page))?'1':$page; //判断当前页面是否为空 如果为空就表示为第一页面   
        $start=($page-1)*$count; //计算每次分页的开始位置  
        if($order==1){  
            $array=array_reverse($array);  
        }     
        $totals=count($array);    
        $countpage=ceil($totals/$count); //计算总页面数  
        $pagedata=array();  
        $pagedata=array_slice($array,$start,$count);  
         return $pagedata;  //返回查询数据  
    }  
	
 
}
