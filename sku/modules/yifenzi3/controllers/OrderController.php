<?php

/**
 * 订单控制器
 * ==============================================
 * 编码时间:2016年4月7日 
 * ------------------------------------------------------------------------------------
 * 公司源码文件，未经授权不许任何使用和传播。
 * ==============================================
 * @author: Derek
 * @version: G-emall child One Parts 1.0.0
 **/
class OrderController extends YfzController
{
    // Yii::app()->user->checkLogin()
    
    /**
     * 订单进入临时队列表中
     */
    public function actionQueueorder()
    {
        if (! Yii::app()->user->checkLogin()) {
            echo json_encode(array(
                "err" => 3,
                "msg" => "未知操作"
            ));
            exit();
        }
        // 用户只能通ajax来进行订单临时提交到队列表中
        if (Yii::app()->request->isAjaxRequest) {
            $idlist = Yii::app()->request->getParam("idList");
            
            if (! $idlist) {
                echo (json_encode(array(
                    "err" => 3,
                    "msg" => "未知操作"
                )));
                exit();
            }
            
            // 对用户传过来的商品id进行处理
            $idlist = implode(",", $idlist);
            
            $cartModel = new Cart();
            $member_id = Yii::app()->user->id;
            
            $cartDoods = $cartModel->findAll(array(
                "condition" => "member_id={$member_id} and goods_id in ({$idlist})"
            ));
            
            $_cartDoods = json_decode(CJSON::encode($cartDoods), true);
            
            // 得出用户购物车中所选择的商品一一insert到队列数据表中，一个商品一条数据。最后反回一个或者几个insert成功ID。
            if (count($cartDoods) >= 1) {
                
                // 记录insert队列表中的id
                $queue_arr = array();
                foreach ($cartDoods as $k => $v) {
                    // 根据商品ID获取进行当中的期数,目的是为了。商品在生成订单时。购买的期数会大于加入购物车时的期为
                    // 例如：加入购物车中的商品是3期，但商品已经进行第4期了。这时间用户是可以从购物车页面提交订单的
                    // 注意的地方就是在。购物车中的商品是3期，商品已经进行第4期了。但生成订单时已经到第5期了。
                    $goods_t_data = YfzGoods::model()->find(array(
                        "select" => array(
                            "current_nper",
                            "shop_price"
                        ),
                        "condition" => "goods_id={$v['goods_id']}"
                    ));
                    
                    $data = array();
                    $data['goods_id'] = $v['goods_id'];
                    $data["member_id"] = $member_id;
                    $data['single_price'] = $v['single_price'];
                    $data['num'] = $v['num'];
                    $data['shop_price'] = $goods_t_data->shop_price;
                    $data["current_nper"] = $v['current_nper'];
                    $data['current_nper_ing'] = $goods_t_data->current_nper;
                    $data['add_time'] = time();
                    
                    // 验证数据是否多次插入
                    $oldQueueData = QueueOrder::model()->find(array(
                        "condition" => "member_id={$member_id} and goods_id={$v['goods_id']} and num={$v['num']}"
                    ));

                    //进行商品限购判断
                    $sql = "select limit_number from {{yfzgoods}} where goods_id=".$v['goods_id'];
                    $yfzgoodsData = Yii::app()->gwpart->createCommand($sql)->queryRow();

                    if ( $yfzgoodsData['limit_number'] )
                    {
//                        $sql = "select og.goods_number,sum(og.goods_number) as nums from {{order}} as o left join {{order_goods}} as og on o.order_id = og.order_id ";
//                        $sql .= " where o.member_id={$member_id} and og.goods_id={$v['goods_id']} and og.current_nper={$goods_t_data->current_nper} and o.order_status=1";
//                        $numberData  =  Yii::app()->gwpart->createCommand($sql)->queryRow();
                        $numberData = $this->getLimitNums($member_id, $v['goods_id'], $goods_t_data->current_nper);
                        
                        if ( $numberData ){
//                            $tmpNum = $yfzgoodsData['limit_number'] - $numberData['nums'];
                            $tmpNum = $yfzgoodsData['limit_number'] - $numberData;

                            if ( $v['num'] > $tmpNum )
                            {
                                echo (json_encode(array(
                                    "err" => 1,
                                    "msg" => "当期商品购买总数量不能大于限购量",
                                )));
                                exit();
                            }
                        }

                    }

                    if ($oldQueueData) {
                        $oldQueueData->add_time = time();
                        $queue_arr[] = $oldQueueData->id;
                        $oldQueueData->save();
                    } else {
                        // 准备插入临时订单表中
                        $queue_id = Yii::app()->gwpart->createCommand()->insert("gw_yifenzi_queue_order", $data);
                        $queue_id = Yii::app()->gwpart->getLastInsertID();
                        
                        if ($queue_id)
                            $queue_arr[] = $queue_id;
                    }
                }
                
                if ($queue_arr) {
                    echo (json_encode(array(
                        "err" => 2,
                        "msg" => "请稍等",
                        "data" => $queue_arr
                    )));
                    exit();
                }
            }
        }
    }
    
    public function getLimitNums($member_id, $goods_id, $current_nper){
        
        $sql = "select order_id from {{order}} where member_id={$member_id} and order_status = 1";
        $memberOrderData = Yii::app()->gwpart->createCommand($sql)->queryAll();

        $goodsNumber = 0;
        foreach ($memberOrderData as $k=>$v){
            $sql = "select goods_number from {{order_goods}} where order_id={$v['order_id']} and goods_id={$goods_id} and current_nper={$current_nper}";
            $memberOrderGoodsData = Yii::app()->gwpart->createCommand($sql)->queryRow();
            
            if ( $memberOrderGoodsData ){
                $goodsNumber += $memberOrderGoodsData['goods_number'];
            }
        }
        
        return $goodsNumber;
    }
    
    
    /**
     * 订单支付积分支付类型
     * @throws Exception
     */
    public function actionRequestOrderPay(){
        if ( $this->isPost() ){
            try {
//                print_r($this->getPost('pay_type'));exit;
                //用户登陆之后保存的Session,前往Member当读取用户信息
                $member_id = Yii::app()->user->id;
                $memberData = Yii::app()->db->createCommand("select * from gw_sku_member where id={$member_id}")->queryRow();
                //post 过来的数据
                $remark = $this->getPost('remark') ? $this->getPost('remark') : '订单支付';

                //订单数据
                $sql = "select order_id,order_sn,member_id,order_amount from " . YIFENZI . '.gw_yifenzi_order where order_id='.$this->getPost('orderID');
                $OrderData = Yii::app()->db->createCommand( $sql )->queryRow();
                if ( !$OrderData )
                    throw new Exception('操作有误，请联系客服人员。');

                switch( $this->getPost('pay_type') )
                {
                    case 'pay_1':
                        //拼装数据
                        $data['order_id']   = $this->getPost('orderID');
                        $data['order_code'] =   $this->getPost('orderSN');
                        $data['operate_type']   =   AccountFlow::OPERATE_TYPE_SKU_YFZ_PAY;
                        $data['transaction_type'] = AccountFlow::TRANSACTION_TYPE_CONSUME;
                        $data['remark'] = $remark;
                        $data['money'] = $OrderData['order_amount'];
                        $data['account_id'] = $member_id;
                        $data['sku_number'] = $memberData['sku_number'];
                        $data['gai_number'] = $memberData['gai_number'];
                        $data['data'] = json_encode($_POST);
                        $data['freight'] = 0;
                        $data['create_time'] = time();
                        AccountBalance::changeBalance($data);
                        //支付成功返回
                        echo (json_encode(array(
                            "err" => 2,
                            "msg" => "支付成功",
                        )));
                        exit();
                        break;
                    case 'pay_2':
                        $orderGoodsModel = new YfzOrderGoods();
                        $orderGoodsName = $orderGoodsModel->ordersnToGoods($OrderData['order_id']);
                        Yii::import('comext.Ghuitong.*',1);
                        require 'YfzQuickPlay.php';
                        $noteUrl = array(
                            "return_url"    => DOMAIN_YIFENZI3.'/order/returnurl/',
                            "notify_url"    => DOMAIN_YIFENZI3.'/order/notifyurl/',
                        );
                        $Ghuitong = new YfzQuickPlay($noteUrl);

                        $Ghuitong->setParameter('order_no', $OrderData['order_sn']);
                        $Ghuitong->setParameter('amount', $OrderData['order_amount']);
//                         $Ghuitong->setParameter('amount', 0.1);
                        $Ghuitong->setParameter('product_name', $orderGoodsName);
                        $Ghuitong->buildRequestSign();
                        echo (json_encode(array(
                            "err" => 4,
                            "msg" => "支付成功",
                            'params'=> $Ghuitong->getAllParameters(),
                            'baseurl'   =>  $Ghuitong->getGateURL(),
                        )));
                        exit();
                        break;
                }
            } catch (Exception $e) {
//                 print_r($e->getMessage());
                echo (json_encode(array(
                    "err" => 1,
                    "msg" => $e->getMessage(),
                )));
                exit();
            }
        }
    }


    /**
     * 微信支付
     * @param order_id 订单iD
     * @param  order_sn 订单括号
     * @return mixed|null
     */
    public function actionWxpay(){
        $getData = $this->getParams();
        $this->layout=false;

        $order_id = $getData['order_id'];
        if ($order_id){
            $tmpArr = explode('_', $order_id);

            $order_id = $tmpArr[0];
            $order_sn = $tmpArr[1];
        }

//        $order_sn = $getData['order_sn'];

        //商品名称
        $orderGoodsModel = new YfzOrderGoods();
        $orderGoodsName = $orderGoodsModel->ordersnToGoods($order_id);

        $sql = "select order_id,order_sn,member_id,order_amount from " . YIFENZI . ".gw_yifenzi_order where order_id={$order_id}";
        $OrderData = Yii::app()->db->createCommand( $sql )->queryRow();
        if (!$orderGoodsName) {
            throw new Exception(404);
        }

        try{
            Yii::import('comext.WxpayAPI_php_v3.lib.*',1);
            Yii::import('comext.WxpayAPI_php_v3.cert.*',1);
            require_once "WxPay.Api.php";
            require_once "WxPay.JsApiPay.php";
            $jsApiParameters = "";

//            $openId = $this->getSession("openID");
            $openId = Yii::app()->user->getState("openID");
            $sql = "insert into ".YIFENZI.".gw_yifenzi_order_log (remark) values('{$openId}')";
            Yii::app()->db->createCommand($sql)->execute();
            if (!isset($openId)){
                //①、获取用户openid
                $tools = new JsApiPay();
                $openId = $tools->GetOpen2id();
            }


            //②、统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody($orderGoodsName);
            $input->SetAttach($orderGoodsName);
            $input->SetOut_trade_no($order_sn);
            $input->SetTotal_fee(($OrderData['order_amount'] * 100));
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("");
//            $input->SetNotify_url("http://www.gnet-mall.net/reslog/log");
//            $input->SetNotify_url('http://www.g1fz.com/order/wxnotifyurl');
            $input->SetNotify_url(DOMAIN_YIFENZI3.Yii::app()->createUrl('/yifenzi3/order/wxnotifyurl'));

            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);

            //所有流程通了之后进行订单标记
            if ( $jsApiParameters ) {
                $condition = "out_trade_sn='".$order['prepay_id']."',payment_code='".$jsApiParameters."'";
                $sql = "update ".YIFENZI.'.gw_yifenzi_order set '.$condition.' where order_id='.$order_id;
                if (!Yii::app()->db->createCommand($sql)->execute())
                    throw new Exception('订单支付失败，请联系管理人员进行处理');

                //订单商品详情
                $orderGoodsData = $this->getOrderGoodsInfo($order_id);
                if ( !$orderGoodsData )
                    $this->redirect('/carts/index');

                //支付方式
                $PayDataConfig = $this->getPayConfig();
                $today_amount = $this->today_amount();  //账户余额

                $this->render('topayment',
                    array('PayDataConfig'=>$PayDataConfig,"ordergoods"=>$orderGoodsData,'jsApiParameters' => $jsApiParameters,'is_wx'=>true,'today_amount'=>$today_amount));
            }

        }catch(Exception $e){
            print_r($e->getMessage());exit;
        }

    }

    public function actionWxnotifyurl(){
        $getData = $this->getParams();
        $this->layout=false;

        Yii::import('comext.WxpayAPI_php_v3.lib.*',1);
        require_once "WxPay.Data.php";
        //获取通知的数据
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $result = WxPayResults::Init($xml);

        if (!$result['out_trade_no']){
            return false;
        }

        $total_fee = $result['total_fee'];
        $out_trade_no = $result['out_trade_no'];
        try{
            //订单数据
            $sql = "select order_id,order_sn,member_id,order_amount from " . YIFENZI . ".gw_yifenzi_order where order_sn={$result['out_trade_no']}";
            $OrderData = Yii::app()->db->createCommand( $sql )->queryRow();
//            $OrderData = json_encode($OrderData);
//            $sql = "insert into ".YIFENZI.".gw_yifenzi_order_log (remark) values('{$OrderData}')";
//            Yii::app()->db->createCommand($sql)->execute();exit;

            if ( !$OrderData )
                throw new Exception('操作有误，请联系客服人员。');

            $member_id = $OrderData['member_id'];
            $memberData = Yii::app()->db->createCommand("select * from gw_sku_member where id={$member_id}")->queryRow();

            //对比金额
            if (($total_fee * 1) != $OrderData['order_amount'] * 1){
//                throw new Exception('订单支付有误，请联系客服人员');
            }

            $apiLogData['order_id'] = $OrderData['order_id'];
            $apiLogData['order_code'] = $result['out_trade_no'];
            $apiLogData['operate_type'] = AccountFlow::OPERATE_TYPE_EBANK_RECHARGE;
            $apiLogData['transaction_type'] = AccountFlow::TRANSACTION_TYPE_RECHARGE;
            $apiLogData['remark'] = isset($post['remark']) ?  : $result['out_trade_no'].'微信支付';
            $apiLogData['node'] = isset($post['node']) ? $post['node'] : AccountFlow::BUSINESS_NODE_EBANK_GHT;
            $apiLogData['money'] = $OrderData['order_amount'];
            $apiLogData['account_id'] = $member_id;
            $apiLogData['sku_number'] = $memberData['sku_number'];
            $apiLogData['gai_number'] = $memberData['gai_number'];
            $apiLogData['data'] = json_encode(array("orderID"=>$OrderData['order_id'],"pay_type"=>2,"orderSN"=>$result['out_trade_no']));
            $apiLogData['create_time'] = time();
            AccountBalance::changeBalance($apiLogData);

            //积分 支付
            $data['order_id']   = $OrderData['order_id'];
            $data['order_code'] =   $result['out_trade_no'];
            $data['operate_type']   =   AccountFlow::OPERATE_TYPE_SKU_YFZ_PAY;
            $data['transaction_type'] = AccountFlow::TRANSACTION_TYPE_CONSUME;
            $data['remark'] = '订单支付成功，金额为'.$OrderData['order_amount'];
            $data['money'] = $OrderData['order_amount'];
            $data['account_id'] = $member_id;
            $data['sku_number'] = $memberData['sku_number'];
            $data['gai_number'] = $memberData['gai_number'];
            $data['data'] = json_encode($_POST);
            $data['freight'] = 0;
            $data['create_time'] = time();
            AccountBalance::changeBalance($data);
            echo "success";exit;
        }catch(Exception $e){
            Yii::app()->gwpart->createCommand()->insert("gw_yifenzi_order_log",array(
                "remark"    =>  $e->getMessage(),
                "order_sn"  =>  $result['out_trade_no'],
                "order_status"  =>  0,
                "log_time"  =>  time(),
            ));
        }
    }

    /**
     * 支付成功回调URL
     */
    public function actionNotifyurl(){
        $getData = $this->getParams();
        $order_sn = isset($getData['order_no']) ? $getData['order_no'] : "";
        $pay_no = isset($getData['pay_no']) ? $getData['pay_no'] : "";
        $pay_result = isset($getData['pay_result']) ? $getData['pay_result'] : '';
        $amount = isset($getData['amount']) ? $getData['amount'] : '';
        try{

            //订单数据
            $sql = "select order_id,order_sn,member_id,order_amount from " . YIFENZI . '.gw_yifenzi_order where order_sn='.$order_sn;
            $OrderData = Yii::app()->db->createCommand( $sql )->queryRow();

            if ( !$OrderData )
                throw new Exception('操作有误，请联系客服人员。');
            $member_id = $OrderData['member_id'];
            $memberData = Yii::app()->db->createCommand("select * from gw_sku_member where id={$member_id}")->queryRow();

            //对比金额
            if (($amount * 1) != $OrderData['order_amount'] * 1){
                //throw new Exception('订单支付有误，请联系客服人员');
            }

            $apiLogData['order_id'] = $OrderData['order_id'];
            $apiLogData['order_code'] = $order_sn;
            $apiLogData['operate_type'] = AccountFlow::OPERATE_TYPE_EBANK_RECHARGE;
            $apiLogData['transaction_type'] = AccountFlow::TRANSACTION_TYPE_RECHARGE;
            $apiLogData['remark'] = isset($post['remark']) ? $post['remark'] : '网银支付';
            $apiLogData['node'] = isset($post['node']) ? $post['node'] : AccountFlow::BUSINESS_NODE_EBANK_GHT;
            $apiLogData['money'] = $OrderData['order_amount'];
            $apiLogData['account_id'] = $member_id;
            $apiLogData['sku_number'] = $memberData['sku_number'];
            $apiLogData['gai_number'] = $memberData['gai_number'];
            $apiLogData['data'] = json_encode(array("orderID"=>$OrderData['order_id'],"pay_type"=>2,"orderSN"=>$order_sn));
            $apiLogData['create_time'] = time();
            AccountBalance::changeBalance($apiLogData);

            //积分 支付
            $data['order_id']   = $OrderData['order_id'];
            $data['order_code'] =   $order_sn;
            $data['operate_type']   =   AccountFlow::OPERATE_TYPE_SKU_YFZ_PAY;
            $data['transaction_type'] = AccountFlow::TRANSACTION_TYPE_CONSUME;
            $data['remark'] = '订单支付成功，金额为'.$OrderData['order_amount'];
            $data['money'] = $OrderData['order_amount'];
            $data['account_id'] = $member_id;
            $data['sku_number'] = $memberData['sku_number'];
            $data['gai_number'] = $memberData['gai_number'];
            $data['data'] = json_encode($_POST);
            $data['freight'] = 0;
            $data['create_time'] = time();
            AccountBalance::changeBalance($data);
            echo "success";exit;
        } catch(Exception $e){
            Yii::app()->gwpart->createCommand()->insert("gw_yifenzi_order_log",array(
                "remark"    =>  $e->getMessage(),
                "order_sn"  =>  $order_sn,
                "order_status"  =>  0,
                "log_time"  =>  time(),
            ));
        }

    }

    public function actionReturnurl(){
        $getData = $this->getParams();
        $order_sn = isset($getData['order_no']) ? $getData['order_no'] : "";
        $pay_no = isset($getData['pay_no']) ? $getData['pay_no'] : "";
        $pay_result = isset($getData['pay_result']) ? $getData['pay_result'] : '';

        if (!$order_sn || !$pay_no || !$pay_result)
            Fun::errorPrint("错误请求");

        $data = YfzOrder::getOrderOne($order_sn);
        switch( $pay_result ){
            case 1:
                $this->redirect(array('order/ordersuccess','code'=>$data['order_id']."_2","msg"=>"支付成功"));
                break;
            case 2:
                $this->redirect(array('order/ordersuccess','code'=>$data['order_id']."_1","msg"=>"支付失败"));
                break;
            default:
                $this->redirect(array('order/ordersuccess','code'=>$data['order_id']."_1","msg"=>"未支付"));
                break;
        }
    }

    /**
     * 订单支付成功或者失败。此为订单通知页面
     */
    public function actionOrdersuccess(){
        $code = Yii::app()->request->getParam("code") ? Yii::app()->request->getParam("code") : '';
        $msg = Yii::app()->request->getParam("msg") ? Yii::app()->request->getParam("msg") : '';
        if (!$code){
            Fun::errorPrint("错误请求");
        }
        
        $this->layout = false;
        $this->pageTitle = Yii::t('yfzGoods', '一份子 订单结果');
        list($order_id,$order_status) = explode('_', $code);
        $retData = array();



        $sql = "SELECT
                    yo.order_id,
                    yo.addtime,
                    yog.goods_name,
                    yo.order_sn,
                    yog.goods_number AS num,
                    yog.current_nper AS cnper
                FROM
                    gw_yifenzi_order AS yo
                LEFT JOIN gw_yifenzi_order_goods AS yog ON yo.order_id = yog.order_id
                WHERE
                    yo.order_id = {$order_id}
                AND yo.member_id = ".Yii::app()->user->id;
        $orderGoodsData = Yii::app()->gwpart->createCommand($sql)->queryAll();
        
        if (!$orderGoodsData)
            Fun::errorPrint("错误请求");
        $retData['status'] = $order_status;
        $retData['msg'] = $msg;
        $retData['orderdata'] = $orderGoodsData;

        //读取订单log表
        $sql = "select * from {{order_log}} where order_sn='".$orderGoodsData[0]['order_sn']."' order by log_time";
        $logOrder = Yii::app()->gwpart->createCommand($sql)->queryRow();

        if ($logOrder){
            $retData['status'] = 0;
            $retData['msg'] = $logOrder['remark'];
        }

        $this->render("ordersuccess", array("data"=>$retData));
    }


    /**
     * 进程方法，实时监控队列表中数据进行库存验证
     * 每次只取一条数据进行对比如果条件满足那就把这一条数据进行删除否则不动数据
     */
    public function actionOrderautomatic()
    {
        $connection = Yii::app()->gwpart;
        
        // 根据用户进入队列表中的时间先后顺序读取一条信息来进行goods表的商品库存判断
        $queueData = QueueOrder::model()->find(array(
            "order" => "add_time desc",
            "limit" => 1
        ));
        
        if (! $queueData)
            return false;
        
        $bool = true; // 验证条件
                      
        // 事务开启
        $transaction = $connection->beginTransaction();
        
        // 商品表相关数据
        $sql = "select goods_number,current_nper from gw_yifenzi_yfzgoods where goods_id = {$queueData->goods_id} for update";
        $goodsRow = $connection->createCommand($sql)->queryRow();
        
        // 判断用户购物数量是否大于库存，如果大于则不做处理
        if ($goodsRow['goods_number'] < $queueData->num)
            $bool = false;
            
            // 验证下单确定购买期
        if ($goodsRow['current_nper'] > $queueData->current_nper_ing)
            $bool = false;
            
            // 做一个删除数据处理,让用户能下订单
        if ($bool === true) {
            $delRet = $connection->createCommand()->delete("gw_yifenzi_queue_order", "id=:id", array(
                "id" => $queueData->id
            ));
            
            // 判断是否删除成功,这是一个绝对因素；如果没有删除成功前台是无法下订单的
            if (! $delRet)
                $bool = false;
        }
        
        // 程序走到这里就可以知道这个事务是否成功
        if ($bool === true) {
            $transaction->commit(); // 提交事务
        } else {
            $transaction->rollBack(); // 事务回滚
        }
    }

    /**
     * 确认订单，入库生效
     * 如果代码要走到这里。要确保Queueorder和Orderautomatic都处理完成。缺一不可
     * 包括功能点：首生程序跑到这里证明商品购买库存是够的
     * 1、拼装数据准备放入订单表中
     * 2、把订单中的商品也同步到订单商品表中
     * 3、把商品统计表做好记录
     * 4、扣除商品库存（成功支付后操作）
     * 5、删除购物车中数据（成功支付后操作）
     * 6、判断订单中商品是否为最后一个库存，如果是则要做对应的基数更新（成功支付后操作）
     */
    public function actionToorder()
    {
        if (Yii::app()->request->isAjaxRequest) {
            
            $queueID = Yii::app()->request->getParam("queueID") ? Yii::app()->request->getParam("queueID") : 0;
            $idList = Yii::app()->request->getParam("idList") ? Yii::app()->request->getParam("idList") : 0;
            
            $queueBool = $this->dealWithQueue($queueID); // 如果反回值为true那么可以进行生成订单
            
            //如果队列中，检验购买数量后开始写入订单表中
            if ($queueBool === true) {
                $retOrder = $this->addOrder($idList);
                
                if (is_array($retOrder)){
                    echo(json_encode($retOrder));
                    exit;
                }
                
                echo(json_encode(array("err"=>2,"msg"=>$retOrder)));
                exit;
            }else{
                echo(json_encode(array("err"=>1,"msg"=>"请稍等")));
                exit;
            }
        }
    }

    /**
     * 处理用户选择商品，进入队列之后的情况、如果返回true那么可以提交订单否则不
     *
     * @param array $data            
     * @return bool
     */
    protected function dealWithQueue(array $data)
    {
        $retBool = true; // 返回值
        
        if (is_array($data) && count($data) >= 1) {
            
            foreach ($data as $k => $v) {
                $sql = "
                    SELECT id 
                    
                        FROM gw_yifenzi_queue_order 
                    
                        WHERE id={$v}
                    ";
                
                // execute
                $queueOne = Yii::app()->gwpart->createCommand($sql)->queryRow();
                if ($queueOne) {
                    $retBool = false;
                    break;
                }
            }
        }
        
        return $retBool;
    }

    /**
     * 队列那里返回ture之后进行一个订单生成操作
     * 生成订单成功之后那么下一步是进行订单商品Insert
     *
     * @param array $data
     *            购物车中，用户选择下订单的商品
     * @return boolean
     */
    protected function addOrder(array $data)
    {
        if (is_array($data) && count($data) >= 1) {
            $orderData = array();
            $member_id = Yii::app()->user->id;
            
            $orderData['order_sn'] = $this->get_order_sn();
            $orderData['member_id'] = Yii::app()->user->id;
            $orderData['order_amount'] = $this->countOrderPrice($data);
            $orderData['addtime'] = Fun::microtime_float();
            $orderData['order_channe'] = 3;

            //用户名
            $sql = "select username from {{member}} where id=".Yii::app()->user->id;
            $skuMember = Yii::app()->db->createCommand($sql)->queryRow();
            $orderData['user_name'] = $skuMember ? $skuMember['username'] : '';
            
            if (Yii::app()->gwpart->createCommand()->insert("gw_yifenzi_order", $orderData)){
                $order_id = Yii::app()->gwpart->getLastInsertID();
                
                //把订单的商品插入到订单商品表中
                if ( $order_id ){
                        foreach ( $data as $k=>$v ){
                            $sql = "
                                SELECT 
                                    g.*,gm.goods_thumb as goods_image,c.num 
                                FROM 
                                    gw_yifenzi_yfzgoods as g 
                                LEFT JOIN 
                                    gw_yifenzi_goods_image as gm 
                                ON 
                                    g.goods_id = gm.goods_id 
                                LEFT JOIN 
                                    gw_yifenzi_cart as c 
                                ON 
                                    c.goods_id = gm.goods_id 
                                WHERE 
                                    g.goods_id = {$v} and c.member_id = {$member_id}
                                ";
                            $goodsData = Yii::app()->gwpart->createCommand( $sql )->queryRow();
                            
                            $orderGoods = array();
                            $orderGoods['order_id'] = $order_id;
                            $orderGoods['goods_id'] = $v;
                            $orderGoods['goods_name'] = $goodsData['goods_name'];
                            $orderGoods['goods_price'] = $goodsData['shop_price'];
                            $orderGoods['goods_image'] = $goodsData['goods_image'];
                            $orderGoods['goods_number'] = $goodsData['num'];
                            $orderGoods['addtime'] = time();
                            $orderGoods['single_price'] = $goodsData['single_price'];
                            $orderGoods['current_nper'] = $goodsData['current_nper'];
                            
                            if ( !Yii::app()->gwpart->createCommand()->insert("gw_yifenzi_order_goods", $orderGoods)){
                                Yii::app()->gwpart->createCommand()->delete("gw_yifenzi_order",
                                    "order_id=:order_id",
                                    array("order_id"=>$order_id));
                                return array("err"=>1,"msg"=>"订单提交失败");
                            }
                                                        
                            //把订单成功的数据统计写入到商品统计表中
                            $goodsStat = Goods_statistics::model()->find(array("condition"=>"goods_id={$v}"));
                            $goodsStat->orders += 1;
                            $goodsStat->save();
                        }
                        
                        return $order_id;
                }
                
            }else{
                return array("err"=>1,"msg"=>"订单提交失败");
            }
        }
    }

    /**
     * 用户选择商品下订单之后准备去支付操作
     */
    public function actionTopayment(){
        $order_id = Yii::app()->request->getParam("order_id") ? intval(Yii::app()->request->getParam("order_id")) : 0;
        $member_id = Yii::app()->user->id;
        $this->layout=false;
//        print_r(Yii::app()->request->getHostInfo().Yii::app()->request->url);exit;
        
        //验证数据,是否有此订单
        $orderGoodsData = $this->getOrderGoodsInfo($order_id);

        if ( !$orderGoodsData )
            $this->redirect('/carts/index');

        $today_amount = $this->today_amount();  //账户余额
        $PayDataConfig = $this->getPayConfig();
        $this->render("topayment", array("ordergoods"=>$orderGoodsData,'PayDataConfig'=>$PayDataConfig,'today_amount'=>$today_amount));
    }


    /**
     * 一份子支付方式
     * @return array|void
     */
    public function getPayConfig(){
        $PayData = Yii::app()->gwpart->createCommand()
            ->select('payment_code,enabled')
            ->from('{{payment}}')
            ->queryAll();

        if (!$PayData) return;

        $PayDataConfig = array();
        if(!empty($PayData)){
            foreach($PayData as $k=>$v){
                $PayDataConfig[$v['payment_code']] = $v['enabled'];
            }
        }
        return $PayDataConfig;
    }

    /**
     * 获取用户余额
     */
    public function today_amount(){
        $member_id = Yii::app()->user->id ? Yii::app()->user->id : 0;
        $data = Member::model()->findByPk($member_id)->sku_number;
        $accountBalance = AccountBalance::getTodayAmountByGaiNumber($data);
        return $accountBalance;
    }

    /**
     * 获取订单商品数据
     * @param $order_id
     * @return mixed
     */
    public function getOrderGoodsInfo($order_id){
        $member_id = Yii::app()->user->id;
        if(!$member_id){
            $this->redirect('/member/login');
        }
        $sql = "
            SELECT

             o.order_id,o.order_sn,o.member_id,o.order_status,o.order_amount,og.current_nper,og.goods_name,og.goods_price,og.goods_image,og.goods_number,og.single_price

            FROM gw_yifenzi_order as o

            LEFT JOIN gw_yifenzi_order_goods as og

            ON o.order_id = og.order_id

            WHERE o.order_id = {$order_id}

            AND o.member_id = {$member_id}

            AND o.order_status = 0
            ";
        $orderGoodsData = Yii::app()->gwpart->createCommand($sql)->queryAll();

        return $orderGoodsData;
    }

    /**
     * 用户在提交订单，准备支付金额。所选积分支付应该判断是否有支付密码
     */
    public function actionCheckPayPass(){
        if ( $this->isAjax() )
        {
//            throw new Exception('操作有误，请联系客服人员。');
            try{
                $member_id = Yii::app()->user->id;
                $sql = "select id,gai_number,gai_member_id from {{member}} where id = $member_id";
                $skuMember = Yii::app()->db->createCommand( $sql )->queryRow();
                if ( !$skuMember )
                    throw new Exception('未知错误，请联系管理人员.');

                //盖像会员数据
                $sql = "select * from {{member}} where id=".$skuMember['gai_member_id'];
                $gwMember = Yii::app()->gw->createCommand( $sql )->queryRow();
                if ( !$gwMember )
                    throw new Exception('未知错误，请联系管理人员.');
                if ( $gwMember['password3'] == false ){
                    throw new Exception('未设置支付密码');
                }

            }catch(Exception $e){
                echo (json_encode(array(
                    "err" => 1,
                    "msg" => $e->getMessage(),
                )));
                exit();
            }
        }
    }

    /**
     * 校验密码是否正确
     */
    public function actionPayPassCorrect(){
        if ( $this->isAjax() ){
            try{
                if ( !Yii::app()->user->id )
                    throw new Exception('未知操作');

                $model = new Member();
                $code =  $this->getParam('code');
                $sql = "select * from {{member}} where id=".Yii::app()->user->id;
                $skuMember = Yii::app()->db->createCommand($sql)->queryRow();
                //校验时先同步Gw数据到Sku
                Member::syncPassword($skuMember['gai_number']);
                $model->salt = $skuMember['salt'];
                $model->password3 = $skuMember['password3'];
                if ( !$model->validatePassword3($code) ){
                    throw new Exception('密码错误');
                }

            }catch(Exception $e){
                echo (json_encode(array(
                    "err" => 1,
                    "msg" => $e->getMessage(),
                )));
                exit();
            }
        }
    }
    /**
     * 计算生成订单中的商品应该金额
     * 
     * @param array $data
     *            商品ID
     * @return Number
     */
    protected function countOrderPrice(array $data)
    {
        $retNum = array();
        
        if ( !is_array($data) ) return;
        $strId = implode(',', $data);
        $member_id = Yii::app()->user->id;
        
        $sql = "
            SELECT single_price,num 
            
            FROM gw_yifenzi_cart 
            
            WHERE goods_id in({$strId}) and member_id = {$member_id}
            ";
        $cartData = Yii::app()->gwpart->createCommand( $sql )->queryAll();
        
        
        foreach ( $cartData as $k=>$v ){
            $retNum[] = (($v['single_price'] * 100) * ($v['num'] * 100)) / 10000;
        }
        
        return array_sum($retNum);
    }

    /**
     * 生成订单号检验订单号唯一性
     * 
     * @return string
     */
    protected function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        $timestamp = time() - date('Z');
        $y = date('y', $timestamp);
        $z = date('z', $timestamp);
        $order_sn = $y . str_pad($z, 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        
        // 检验是否有此单号
        $sql = "SELECT order_sn
                
                FROM gw_yifenzi_order 
            
                WHERE order_sn='{$order_sn}'";
        
        $boolear = Yii::app()->gwpart->createCommand($sql)->execute();
        
        if ($boolear) {
            return $this->get_order_sn();
        }
        
        return $order_sn;
    }
    /*
     * 测试登录
     * */
    public function actionPayWei(){
        $this->getWeixingOpenId();
        $openid = Yii::app()->user->getState(WeixinMember::MEMBER_OPENID);
        $result = WeixinMember::processMember($openid); //同步微信用户登陆
        var_dump($openid);exit;
    }
}
