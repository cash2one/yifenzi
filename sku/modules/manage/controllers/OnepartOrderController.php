<?php

/**
 * 后台订单管理 控制器
 * @author qiuye.xu<qiuye.xu@g-mall.com>
 * @since 2016-04-28
 */
class OnepartOrderController extends MController
{
    /**
     * 订单列表
     */
    /*public function actionAdmin()
    {
        $model = new YfzOrderGoodsNper('search');
        if(isset($_GET['YfzOrderGoodsNper'])) {
            $get = $_GET['YfzOrderGoodsNper'];
            $model->member_id = $get['member_id'];
            $model->order_status = $get['order_status'];
            $model->order_sn = $get['order_sn'];
            $model->goods_id = $get['goods_id'];
        }
        $this->render('admin',array('model'=>$model));
    }*/
    
	/**
     * 订单列表
     */
    public function actionAdmin()
    {
		$memberId = Yii::app()->request->getParam('member_id');
        $orderSn = Yii::app()->request->getParam('order_sn');
		$goodsId = Yii::app()->request->getParam('goods_id');
        $orderStatus = Yii::app()->request->getParam('order_status');
		$isDelivery = Yii::app()->request->getParam('is_delivery');
		$selectOrder = Yii::app()->request->getParam('select_order');
		$isInvoice = Yii::app()->request->getParam('is_invoice');
		
        $model = new YfzOrderGoodsNper;
		$criteria = new CDbCriteria();
		$criteria->select = 'og.addtime,g.shop_price,g.single_price,og.goods_number,t.goods_id,t.order_id,t.current_nper,t.goods_name,t.sumlotterytime,t.member_id,o.order_status,o.is_address,o.invoice_no,o.order_sn,o.is_delivery,og.winning_code';
		$criteria->join = 'left join {{order}} o on o.order_id=t.order_id 
		left join {{order_goods}} og on og.goods_id = t.goods_id and og.current_nper = t.current_nper and og.order_id = t.order_id
        left join {{yfzgoods}} g on g.goods_id = og.goods_id';
		$criteria->addSearchCondition('t.member_id',$memberId);
        $criteria->addSearchCondition('o.order_sn',$orderSn);
		$criteria->addSearchCondition('t.goods_id',$goodsId);
		if($isDelivery ==0 || $isDelivery ==1){
		    $criteria->addSearchCondition('o.is_delivery',$isDelivery);
		}
		if($orderStatus ==0 || $orderStatus ==1 || $orderStatus ==2 ){
            $criteria->addSearchCondition('o.order_status',$orderStatus);
		}
        if($isInvoice ==0 || $isInvoice ==1){
            $criteria->addSearchCondition('o.is_invoice',$isInvoice);
        }
		/*if($isInvoice == 0){
		    $criteria->compare('LENGTH(trim(o.invoice_no))', "<" . 1);
		}
		if($isInvoice == 1){
		    $criteria->compare('LENGTH(trim(o.invoice_no))', ">=" . 1);
		}*/
		if($selectOrder == 1){
		    $criteria->order = 'og.addtime DESC' ;//排序条件
		}
		if($selectOrder == 2){
		    $criteria->order = 'g.shop_price DESC' ;//排序条件
		}
		if($selectOrder == 3){
		    $criteria->order = 'og.goods_number DESC' ;//排序条件
		}else{
            $criteria->order = 't.sumlotterytime DESC,og.addtime DESC' ;//排序条件
        }
		$count = $model->count($criteria);
        $pages = new CPagination($count);
	    $pages->pageSize = 10;
        $pages->applyLimit($criteria);
        $data = $model->findAll($criteria);
	
		/*if ($this->isAjax()) {
            exit(CJSON::encode(array('result' => true, 'data' => $data)));
            Yii::app()->end();
        }*/

        $this->render('admin',array('data'=>$data,'pages'=>$pages,'model'=>$model));
    }
    /**
     * 查看中奖订单信息
     */
    public function actionView()
    {
        $model = new YfzOrderGoodsNper('view'); // 查看信息
        $id = (int)  Yii::app()->request->getParam('id');
        $order_id = (int)  Yii::app()->request->getParam('order_id');
        $currentNper = (int)  Yii::app()->request->getParam('nper');
        $model = $model->getDetail($id,$currentNper); //中奖 信息
        
        $orderGoods = new YfzOrderGoods(); //  中奖产品信息
        $orderGoods->goods_id = $model->goods_id;
        $orderGoods->current_nper = $model->current_nper;
        $orderGoods->order_id = $order_id;
        $orderGoods = $orderGoods->getWinningCode($model->member_id);
//		print_r($orderGoods);exit;
        
        $member = new Member();
        $member = $member->findByPk($model->member_id);

        $order = new YfzOrder(); //订单信息
        $order = $order->findByPk($model->order_id);

        $order->setScenario('shippingUpdate');
        $this->render('view',array(
            'model'=>$model,
            'orderGoods'=>$orderGoods,
            'member'=>$member,
            'order'=>$order
        ));
    }
    /**
     * 
     */
    public function actionUpdateShipping()
    {
        if(isset($_POST['YfzOrder'])){
            $order = new YfzOrder('shippingUpdate');
            $post = Yii::app()->request->getParam('YfzOrder');
            $id = $post['order_id'];
            $order = $order->findByPk($id);
            $order->attributes = $post;
            $order->is_invoice = 1;
            if($order->validate()){
                if($order->save()) {
                    $rs = YfzOrderExpress::add($order); //添加物流信息推送
                    $this->setFlash('success', '快递信息添加成功');
                } else {
                    $this->setFlash('error', '快递信息添加失败');
                }
            } else {
                var_dump($order->getErrors());exit;
            }
        }
        $this->redirect('/onepartOrder/admin');
    }

    /*
     * 根据条件查询最后100条订单信息，并计算出幸运码
     * */
    public function actionCheck(){
        $_goods_number = Yii::app()->request->getParam('goods_number');
        $model = new YfzOrder();
        $retArr = array();
        if(!empty($_GET) && isset($_GET) && is_numeric($_goods_number)) {
            $model['addtime'] = $_GET['YfzOrder']['addtime'];
        $_addtimeF = strtotime($_GET['YfzOrder']['addtime']);
            $_addtimeC = $_addtimeF.'.'.'999';
            //取该商品最后购买时间前网站 所有商品的最后100条购买记录；
            $sql = "select order_id,addtime,user_name,member_id from " . YIFENZI . '.gw_yifenzi_order where order_status > 0 and addtime < '.$_addtimeC.' order by addtime desc limit 99';
            $Data = Yii::app()->db->createCommand($sql)->queryAll();
            if ($Data) {
                array_unshift($Data,array('addtime'=>$_addtimeF.'.000','member_id'=>'','order_id'=>'a'));
                $retArr['yffdata'] = array();
                $retArr['hisdata'] = array();
                $retArr['sumhisdata'] = array();
                $retArr['formuladata'] = array();
                $retArr['allusername'] = array();

                foreach ($Data as $k => $v) {
                    list($date, $sec) = explode(".", $v['addtime']);
                    $retArr['yffdata'][$v['order_id']] = date('Y-m-d', $date);
                    $retArr['hisdata'][$v['order_id']] = date("H:i:s", $date) . '.' . $sec;
                    $member = Member::getMemberInfo($v['member_id']);
                    $retArr['allusername'][$v['order_id']] = !empty($member['gai_number'])?$member['gai_number']:'';
                    list($h, $i, $s) = explode(":", date('H:i:s', $date));
                    $retArr['sumhisdata'][$v['order_id']] = $h . $i . $s . $sec;
                }
                //H:i:s.sec时间总和
                $retArr['formuladata']['h_i_s_sum'] = array_sum($retArr['sumhisdata']);
                $retArr['formuladata']['nperall'] = $_goods_number;
//        print_r($retArr['formuladata']['nperall']);
                // 其值=除数×(整商+1)-被除数
                //时间总和'/'商品总需人数.取该商品最后购买时间前风站所有商品的最后100条购买计录
                $sumceil = floor($retArr['formuladata']['h_i_s_sum'] / $retArr['formuladata']['nperall']);
//        $sumceil = intval($retArr['formuladata']['h_i_s_sum'] / 4);
                $oldsumceil = $retArr['formuladata']['nperall'] * $sumceil;
//        $oldsumceil = 4 * $sumceil;
                $winning_code = abs($retArr['formuladata']['h_i_s_sum'] - $oldsumceil);

                if ($winning_code == 0 || $winning_code == false) $winning_code = 0;

                if ($retArr['formuladata']['nperall'] == 1) $winning_code = 0;

                if ($retArr['formuladata']['nperall'] < $winning_code)
                    throw new Exception('订单提交失败');
                $retArr['formuladata']['winning_code'] = $winning_code;
                $retArr['formuladata']['lucky_code'] = 10000001;
                $new_winning_code = ($retArr['formuladata']['winning_code'] * 1) + ($retArr['formuladata']['lucky_code'] * 1);
            }
        }
        $this->render('check',array(
            'retArr'=>!empty($retArr)?$retArr:'',
            'model'=>$model,
        ));
    }
    public function actionTest()
    {
        var_dump(Region::getName(5,6,9));
    }
	
}
