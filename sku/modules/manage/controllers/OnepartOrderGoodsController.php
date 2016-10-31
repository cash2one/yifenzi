<?php
/**
 * 订单产品控制器
 * @author qiuye.xu<qiuye.xu@g-emall.com>
 * @since 2016-04-13
 */
class OnepartOrderGoodsController extends MController
{
    public function filters() {
        return array(
            'rights',
        );
    } 
    
    public function actionIndex()
    {
        die('还没有开放呢');
    }
    
    /**
     * 商品的往期管理
     * @param type $id
     */
   /* public function actionPast($id)
    {
        $model = new YfzOrderGoods('past');
        $model->goods_id = (int)$id;
        $this->render('past',array('model'=>$model));
    }*/
	
	 /**
     * 商品的往期管理
     * @param type $id
     */
   public function actionPast($id)
    {
		$criteria=new CDbCriteria();
		//$connection = Yii::app()->gwpart;
		$sql = "SELECT SUM(og.goods_number) AS goods_number, ogn.status,og.goods_id,og.current_nper,g.max_nper,g.goods_name,g.column_id,g.shop_price,g.single_price,g.recommended,g.announced_time
                FROM {{order_goods}} og
				LEFT JOIN {{order}} o ON o.order_id=og.order_id
                LEFT JOIN {{yfzgoods}} g ON g.goods_id = og.goods_id
				LEFT JOIN {{order_goods_nper}} ogn ON ogn.goods_id = og.goods_id AND ogn.current_nper = og.current_nper AND ogn.order_id = og.order_id
                WHERE og.goods_id = {$id} AND o.order_status = 1
			    GROUP BY og.current_nper
			    ORDER BY og.current_nper DESC";
	    $result = Yii::app()->gwpart->createCommand($sql)->queryAll();
		$pages = new CPagination(count($result));  
        $pages->pageSize=10;
        $pages->applyLimit($criteria);
        $result=Yii::app()->gwpart->createCommand($sql." LIMIT :offset,:limit");
        $result->bindValue(':offset', $pages->currentPage*$pages->pageSize);
        $result->bindValue(':limit', $pages->pageSize); 
		$data=$result->queryAll();
        $this->render('past',array('data'=>$data,'pages'=>$pages));
    }
	

    /**
     * 查看某期 商品的购买详情
     * @param type $id
     */
   /*public function actionView($id,$nper)
    {
        if(is_numeric($id) && is_numeric($nper)){
            $connection = Yii::app()->gwpart;
            $sql = "SELECT goods_id,current_nper FROM {{order_goods}} WHERE goods_id = {$id} AND current_nper = {$nper}";
		    $result = $connection->createCommand($sql)->queryRow();
            //print_r($result);exit;
            $memberId = Yii::app()->request->getParam('member_id');
            $model = new YfzOrderGoods('past');
            $model->goods_id = $id;
            $model->current_nper = $nper;
            $model->member_id = $nper;
            $data = $model->searchNper();
			//print_r($data);exit;
            $this->render('view',array('data'=>$data,'result'=>$result));
            Yii::app()->end();
        }
        throw new CHttpException('404','错误的参数');
    }
    */
	public function actionOrderGoodsView($id,$nper)
    {
		$memberId = Yii::app()->request->getParam('member_id');
        $order_sn = Yii::app()->request->getParam('order_sn');
		$connection = Yii::app()->gwpart;
		$sql = "SELECT goods_id,current_nper FROM {{order_goods}} WHERE goods_id = {$id} AND current_nper = {$nper}";
		$result = $connection->createCommand($sql)->queryRow();
       
        if(is_numeric($id) && is_numeric($nper)){
			if( $this->isPost()){
            $_GET['page']="";
            }
            $model = new YfzOrderGoods;
            $criteria = new CDbCriteria();
			$criteria->select = 't.goods_id,t.current_nper,o.member_id as memberId,t.goods_number,o.addtime as finishedTime,o.order_sn as orderSn,t.winning_code,o.addtime';
			$criteria->join = ' left join {{order}} o on t.order_id=o.order_id';
			$criteria->addCondition('t.goods_id = :id');    
            $criteria->params[':id'] = $id;
			$criteria->addCondition('t.current_nper = :current_nper');    
            $criteria->params[':current_nper'] = $nper;
            if(isset($memberId)){
            $criteria->addSearchCondition('o.member_id',$memberId);
            } 
            if(isset($order_sn)){
			$criteria->addSearchCondition('o.order_sn',$order_sn);
            }

            //订单一定要是支付状态
            $criteria->addCondition('o.order_status = :order_status');
            $criteria->params[':order_status'] = YfzOrder::STATUS_PAY_SUCCESS;

            $count = $model->count($criteria);
            $pages = new CPagination($count);
			
		    $pages->pageSize = 10;
            $pages->applyLimit($criteria);
            $data = $model->findAll($criteria);
            $this->render('view',array('data'=>$data,'result'=>$result,'pages'=>$pages));
            Yii::app()->end();
        }
        throw new CHttpException('404','错误的参数');
    }

    /**
     * 开奖设置
     */
    public function actionLottery(){
        $goods_id = Yii::app()->request->getParam('id') ? Yii::app()->request->getParam('id') : 0;
        $gai_number = 'GW38738396';

        $rel_name = array(
            "我很中中",
            "买了那么次从末中过",
            "我要IP6s,IP6,IP7,IP8",
            "好想中一台pro mac All",
            "GO...",
            "一本井道"
        );

        $data = YfzGoods::model()->find(array("condition"=>"goods_id=$goods_id"));
        $sql = "select * from {{yfzgoods}} as g left join {{goods_image}} as gi on g.goods_id = gi.goods_id where g.goods_id=$goods_id and g.is_on_sale=".YfzGoods::IS_SALES_TRUE." and is_closed = ".YfzGoods::IS_CLOSED_FALSE;
        $data = Yii::app()->gwpart->createCommand($sql)->queryRow();

        $allnper = ($data['shop_price'] / $data['single_price']);

//        $data = json_decode(CJSON::encode($data),true);
//        print_r($data);exit;

        if ( $this->isPost() ){
            //计算库存
            $goods_nums = Yii::app()->request->getParam('goods_num');
            $sum = Yii::app()->request->getParam('sum');
            if ($goods_nums && $sum)
            {
                if ( $data['goods_number'] > 1 ){
//                    $limst = (int)(($sum / $data['single_price']) / $goods_nums);
                    $limst = (int)($sum / $goods_nums);

                    $oldSum = $limst * $goods_nums;

                    if ( is_int($limst) || $oldSum == $sum){
                        for ($i=1;$i<=$goods_nums;$i++){

                            //此期商品中所剩下的库存分为$limit次数进行分配。也就在最后一次的时候会进行无病幸运号分配
                            $transaction = Yii::app()->db->beginTransaction();
                            $sql = "select * from ".YIFENZI.".gw_yifenzi_yfzgoods where goods_id={$goods_id} FOR UPDATE ";
                            $forGoodsData = Yii::app()->db->createCommand($sql)->queryRow();

                            $sql = "select * from {{member}} where gai_number='".$gai_number."'";
//                            throw new Exception('操作不成功');
                            $memberData = Yii::app()->db->createCommand($sql)->queryRow();

                            try{
                                //插入订单表中
                                $orderData = array(
                                    "order_sn"  =>  Fun::get_order_sn(),
                                    "member_id" =>  $memberData['id'],
                                    'addtime' => Fun::microtime_float(),
                                    "order_status"  =>  YfzOrder::STATUS_PAY_SUCCESS,
                                    "user_name" =>  $rel_name[array_rand($rel_name)],
                                );

                                Yii::app()->db->createCommand()->insert(YIFENZI.".gw_yifenzi_order", $orderData);
                                $order_id = Yii::app()->db->getLastInsertID();

                                //判断是否有操作成功
                                if (!$order_id)
                                    throw new Exception("插入订单数据失败");
                                $ordergoodsData = array(
                                    "order_id"  =>  $order_id,
                                    "goods_id"  =>  $goods_id,
                                    "goods_name"    =>  $data['goods_name'],
                                    "goods_price"   =>  $data['shop_price'],
                                    "goods_image"   =>  $data['goods_thumb'],
                                    "single_price"  =>  $data['single_price'],
                                    "current_nper"  =>  $data['current_nper'],
                                    "addtime"   =>  time(),
                                    "goods_number"  =>  $limst,
                                );

                                $luckyArr = array();
                                for( $n=1; $n<=$limst; $n++ )
                                {
                                    $luckyArr[] = (10000001 + ($allnper - $forGoodsData['goods_number']) + $n);
                                }

                                $ordergoodsData['winning_code'] = json_encode($luckyArr);

                                Yii::app()->db->createCommand()->insert(YIFENZI.".gw_yifenzi_order_goods", $ordergoodsData);
                                $order_goods_id = Yii::app()->db->getLastInsertID();
                                if (!$order_goods_id)
                                    throw new Exception("插入订单商品数据失败");

                                //更改库存
                                $sql = "update ".YIFENZI.".gw_yifenzi_yfzgoods set goods_number=goods_number-".$limst." where goods_id={$goods_id}";
                                if (!Yii::app()->db->createCommand($sql)->execute())
                                {
                                    throw new Exception("库存更新失败");
                                }

                                //跑到最后一次判断是否为库存为0如果是那么进行下一期
                                if ( $i == $goods_nums ){
                                    $sql = "select goods_number from ".YIFENZI.".gw_yifenzi_yfzgoods where goods_id={$goods_id}";
                                    $tmp_goods_data = Yii::app()->db->createCommand($sql)->queryRow();

                                    if ($tmp_goods_data['goods_number'] == 0){
                                        Fun::calculateWinning($order_id,$goods_id,$data['current_nper']);

                                        $sql = "update ".YIFENZI.'.gw_yifenzi_yfzgoods set goods_number = '.$allnper.',current_nper=current_nper+1 where goods_id='.$data['goods_id'];
                                        if (!Yii::app()->db->createCommand($sql)->execute())
                                            throw new Exception('商品期数更新失败');
                                    }
                                }


                                $transaction->commit();
                            }catch ( Exception $e)
                            {
                                $transaction->rollBack();
                                print_r($e->getMessage());
                                throw new Exception($e->getMessage());
                            }
                        }
                    }
                }
            }
        }
        $this->render('lottery',array('data'=>$data,'model'=>YfzGoods::model()));

    }

    public function actionLook($id,$nper,$memberId)
    {
        if(is_numeric($id) && is_numeric($nper) && is_numeric($memberId)){
            $model = new YfzOrderGoods;
            $model->goods_id = $id;
            $model->current_nper = $nper;
            $winning = $model->getWinningCode($memberId);
			$win_codes = $winning["win_codes"];
            $this->renderPartial('look',array('winning'=>$winning,'win_codes'=>$win_codes));
            Yii::app()->end();
        }
        throw new CHttpException('404','找不到页面');
    }
}