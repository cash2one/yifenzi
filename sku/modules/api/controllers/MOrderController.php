<?php

/**
 * 售货机订单接口
 * 
 * 
 * @author leo8705
 */
class MOrderController extends VMAPIController {

    public function actionPushConfirmOld() {


        try {
            if ($this->getParam('onlyTest') == 1) {
                $post = $this->getParams();
            } else {
                $this->params = array('shopId', 'code', 'successMoney', 'goodsList');
                $requiredFields = array('shopId', 'code', 'successMoney', 'goodsList');
                $decryptFields = array('shopId', 'code');          //需要解密的字段
                $post = $this->decrypt($_REQUEST, $requiredFields, $decryptFields, true);
            }

            $post['goodsList'] = json_decode(str_replace("\\\"", "\"", $post['goodsList']), true);

            $store = VendingMachine::model()->find('code=:shopId', array(':shopId' => $post['shopId']));
            if (empty($store)) {
                $this->_error(Yii::t('apiModule.order', '售货机不存在！'));
            }

            $order = Order::model()->with('ordersGoods')->find('code=:code', array(':code' => $post['code']));
            //            var_dump($order);
            if (empty($order) || $store->id != $order->machine_id) {
                $this->_error(Yii::t('apiModule.order', '订单不存在'));
            }

            $pay_price = $order->pay_price;
            $member_id = $order->member_id;
            $Member = new ApiMember();
            $GaiInfo = $Member->getInfo($member_id);
            $mobile = $GaiInfo['mobile'];
            //            var_dump($GaiInfo);
            $type = $GaiInfo['type_id'] == 2 ? 2 : 1;
            if ($type == 2) {
                $poin = 0.9;
            } elseif ($type == 1) {
                $poin = 0.45;
            }
            if ($post['successMoney'] != substr(($pay_price / $poin), 0, stripos(($pay_price / $poin), '.') + 3)) {
                //金额差
                $excess_integral = $pay_price - $post['successMoney'] * $poin;
                //查询订单商品信息
                $goods = OrdersGoods::model()->findAll('order_id=:id', array(':id' => $order->id));

                $goods_list = array();
                $fail = array();
                foreach ($goods as $k => $v) {
                    $goods_list[$k]['id'] = $v['gid'];
                    $goods_list[$k]['num'] = $v['num'];
                }
                foreach ($goods_list as $k => $v) {
                    if (!in_array($v, $post['goodsList'])) {
                        $fail[$k] = $v;
                    }
                }
                foreach ($fail as $k => $v) {
                    foreach ($post['goodsList'] as $v1) {
                        if ($v1['id'] == $v['id']) {
                            $fail[$k]['num'] = $v['num'] - $v1['num'];
                        }
                    }
                }
                //                    var_dump($fail);
                //创建出货失败订单
                //                    $fail_price = $order->total_price - $post['successMoney'];

                $fail_order = new Order();

                $fail_order->code = Order::_createCode($order->type, $order->member_id);
                $fail_order->goods_code = Order::_createGoodsCode();
                $fail_order->member_id = $order->member_id;
                $fail_order->partner_id = $order->partner_id;
                $fail_order->store_id = $order->store_id;
                $fail_order->machine_id = $order->machine_id;
                $fail_order->address_id = $order->address_id;
                $fail_order->shipping_type = $order->shipping_type;
                $fail_order->shipping_fee = $order->shipping_fee;
                $fail_order->shipping_time = $order->shipping_time;
                $fail_order->machine_status = $order->machine_status;
                $fail_order->machine_take_type = $order->machine_take_type;
                $fail_order->total_price = $excess_integral;
                $fail_order->pay_price = $excess_integral;
                $fail_order->pay_status = Order::PAY_STATUS_YES;
                $fail_order->status = Order::STATUS_PAY;
                $fail_order->create_time = time();

                //修改原订单
                $order->total_price = $post['successMoney'];
                $order->pay_price = $post['successMoney'];

                if ($fail_order->save() && $order->save()) {
                    //                        echo  2;
                    foreach ($fail as $v) {
                        $fail_order_goods = OrdersGoods::model()->find('gid=:gid and  order_id=:oid', array(':gid' => $v['id'], ':oid' => $order->id));
                        $goods_count = $fail_order_goods->num;
                        //                            var_dump($fail_order_goods->num);
                        //                            var_dump($v['num']);
                        if ($fail_order_goods->num != $v['num']) {
                            //                                echo 3;
                            //创建成功订单商品
                            $success_order_goods = new OrdersGoods();
                            $success_order_goods->order_id = $order->id;
                            $success_order_goods->sgid = $fail_order_goods->sgid;
                            $success_order_goods->gid = $fail_order_goods->gid;
                            $success_order_goods->num = $goods_count - $v['num'];
                            $success_order_goods->supply_price = $fail_order_goods->supply_price;
                            $success_order_goods->price = $fail_order_goods->price;
                            $success_order_goods->total_price = ($fail_order_goods->price) * ($goods_count - $v['num']);
                            $success_order_goods->status = $fail_order_goods->status;
                            $success_order_goods->create_time = time();
                            $success_order_goods->save();
                            //解冻库存
                            $goods_stock = GoodsStock::model()->find('outlets=:mid and target=:gid', array(':gid' => $v['id'], ':mid' => $order->machine_id));
                            $goods_stock->stock = $goods_stock->stock + $v['num'];
                            $goods_stock->frozen_stock = $goods_stock->frozen_stock - $v['num'];
                            $goods_stock->save();
                            //失败商品
                            $fail_order_goods->order_id = $fail_order->id;
                            $fail_order_goods->num = $v['num'];
                            $fail_order_goods->total_price = $fail_order_goods->price * $v['num'];
                            $fail_order_goods->save();
                        } else {
                            //                                echo 3;
                            $fail_order_goods->order_id = $fail_order->id;
                            $goods_stock = GoodsStock::model()->find('outlets=:mid and target=:gid', array(':gid' => $v['id'], ':mid' => $fail_order->machine_id));
                            //                            var_dump($goods_stock);
                            //出货失败商品库存解冻
                            $goods_stock->stock = $goods_stock->stock + $v['num'];
                            $goods_stock->frozen_stock = $goods_stock->frozen_stock - $v['num'];
                            $fail_order_goods->save();
                            $goods_stock->save();
                        }
                    }
                }
                Order::orderCancel($fail_order->code);
                $fail_order->status = Order::STATUS_REFUNDED;
                $fail_order->machine_status = Order::MACHINE_STATUS_NO;
                //生成失败订单 并发短信
                if ($fail_order->save()) {
                    $sms = new ApiMember();
                    $goods_name = Goods::model()->findByPk($fail_order_goods->gid);
                    $msg = "您好，由于库存等原因,商品:" . $goods_name . ",不能及时出货，退还您" . $GaiInfo['type_id'] == 2 ? substr(($excess_integral / 0.9), 0, stripos(($excess_integral / 0.9), '.') + 3) : substr(($excess_integral / 0.45), 0, stripos(($excess_integral / 0.45), '.') + 3) . "的积分";
                    $fen = $GaiInfo['type_id'] == 2 ? substr(($excess_integral / 0.9), 0, stripos(($excess_integral / 0.9), '.') + 3) : substr(($excess_integral / 0.45), 0, stripos(($excess_integral / 0.45), '.') + 3);
                    $data = array($goods_name, $fen);
                    $a = $sms->sendSms($mobile, $msg, $type = ApiMember::SKU_SEND_SMS, 0, ApiMember::SKU_SEND_SMS, $data, ApiMember::MACHINE_FAIL);
                }

                $sign_rs = @Order::orderSign($post['code']);
                if ($sign_rs) {
                    //首次充值 返回10%金额
                    $modelOrder = new Order();
                    $modelOrder->giveBackAmountFirstConsume($post['code'], $member_id);
                }
                $order->status = Order::STATUS_COMPLETE;
                $order->machine_status = Order::MACHINE_STATUS_YES;
                $order->save();

                $this->_success();
            }

            $sign_rs = @Order::orderSign($post['code']);
            if ($sign_rs) {
                //首次充值 返回10%金额
                $modelOrder = new Order();
                $modelOrder->giveBackAmountFirstConsume($post['code'], $member_id);
            }
            $order->status = Order::STATUS_COMPLETE;
            $order->machine_status = Order::MACHINE_STATUS_YES;
            $order->save();
            $this->_success();
        } catch (Exception $e) {
            $this->_error($e->getMessage());
        }
    }

    /**
     * 推送订单确认
     */
    public function actionPushConfirm() {

        set_time_limit(1800);

        if ($this->getParam('onlyTest') == 1) {
            $post = array();
            $post['shopId'] = $this->getParam('shopId');
            $post['code'] = $this->getParam('code');
//         		$post['successMoney'] = $this->getParam('successMoney');
            $post['goodsList'] = $this->getParam('goodsList');
            $post['goodsListO'] = $post['goodsList'];
            $post['goodsList'] = json_decode(str_replace("\\\"", "\"", $post['goodsList']), true);
        } else {
            $this->params = array('shopId', 'code', 'goodsList');
            $requiredFields = array('shopId', 'code', 'goodsList');
            $decryptFields = array('shopId', 'code', 'goodsList');          //需要解密的字段
            $post = $this->decrypt($_REQUEST, $requiredFields, $decryptFields, true);
//            Yii::log('pushConfirm' . var_dump($post, TRUE));
            $post['goodsList'] = json_decode(str_replace("\\\"", "\"", $post['goodsList']), true);
//            Yii::log('pushConfirm' . var_dump($post, TRUE));
        }

        if ($this->getParam('onlyTest2') == 1) {

            $this->params = array('shopId', 'code', 'goodsList');
            $requiredFields = array('shopId', 'code', 'goodsList');
            $decryptFields = array('shopId', 'code', 'goodsList');          //需要解密的字段

            $post = $this->decrypt($_REQUEST, $requiredFields, $decryptFields, true);
            $post['goodsListO'] = $post['goodsList'];
            $post['goodsList'] = json_decode(str_replace("\\\"", "\"", $post['goodsList']), true);
//         		var_dump($_REQUEST,$post,$post['goodsListO'],$post['goodsList']);
        }

        $className = $this->className;
        $goodsClassName = $this->goodsClassName;

        $store = $className::model()->find('code=:shopId', array(':shopId' => $post['shopId']));
        if (empty($store)) {
            $this->_error(Yii::t('apiModule.order', '售货机不存在！'));
        }

        $order = Order::model()->with('ordersGoods')->find('code=:code', array(':code' => $post['code']));
//            var_dump($order);
        if (empty($order) || $store->id != $order->machine_id) {
            $this->_error(Yii::t('apiModule.order', '订单不存在'));
        }

        //判断订单状态
        if ($order['status'] == Order::STATUS_COMPLETE) {
            $this->_error(Yii::t('apiModule.order', '订单已完成'));
        }

        //判断订单状态
        if ($order['pay_status'] != Order::PAY_STATUS_YES || $order['status'] != Order::STATUS_PAY) {
            $this->_error(Yii::t('apiModule.order', '订单未支付'));
        }

        if (empty($post['goodsList'])) {
            Order::orderCancel($order['code'], true, '由于售货机或生鲜机商品不足，订单已自动取消。');
            $this->_success(Yii::t('apiModule.order', '由于售货机或生鲜机商品不足，订单已自动取消。'));
        }


        $order_goods = $order->ordersGoods;
        $o_total_num = 0;  //原始商品总数
        $success_total_num = 0;
        $gids = array();
        $nums = array();

        foreach ($order->ordersGoods as $g) {
            $gids[] = $g->sgid;
            $nums[$g->sgid] = $g->num;
        }

        //创建失败商品订单
        $success_goods_sort = array();
        $success_goods_weight_sort = array();
        foreach ($post['goodsList'] as $val) {
            $success_goods_sort[$val['id']] = $val['num'];
            if (isset($val['weight']))
                $success_goods_weight_sort[$val['id']] = $val['weight'];
            $success_total_num += $val['num'] * 1;
        }

        $success_goods_list = array();
        $fail_goods_list = array();
        $fail_goods_list_for_create = array();
        $fail_total_price = 0;
        $fail_total_num = 0;
        $weight_less_remark = '';   //缺秤的备注
        $weight_less_price = 0;   //缺秤需要补回的金额
        //机器
        $machine = FreshMachine::model()->findByPk($order['machine_id']);
        //格式化数据
        foreach ($order_goods as $val) {
            $o_total_num += $val['num'];
            if (!isset($success_goods_sort[$val['sgid']])) {
                $success_goods_sort[$val['sgid']] = 0;
            }

            if ($success_goods_sort[$val['sgid']] == $val['num']) {
                $success_goods_list[] = $val;
            } else {
                //失败的商品列表
                $fail_temp_arr = $val;
                $fail_temp_arr['num'] = $val['num'] - $success_goods_sort[$val['sgid']];
                $fail_total_num += $fail_temp_arr['num'];
                if ($fail_temp_arr['num'] < 0) {
                    $this->_error(Yii::t('apiModule.order', '商品数量错误'));
                }

                $fail_goods_list[] = $fail_temp_arr;
                if($machine['type']== FreshMachine::FRESH_MACHINE_SMALL){
                    $fail_goods_list_for_create[] = array('id' => $val['gid'], 'num' => $fail_temp_arr['num'], 'price' => $val['price'], 'supply_price' => $val['supply_price']);
                }else{
                $fail_goods_list_for_create[] = array('id' => $val['sgid'], 'num' => $fail_temp_arr['num'], 'price' => $val['price'], 'supply_price' => $val['supply_price']);
                }
                $fail_total_price += $val['price'] * $fail_temp_arr['num'];

                //部分成功的商品列表
                if ($success_goods_sort[$val['sgid']] > 0) {
                    $success_temp_arr = $val;
                    $success_temp_arr['num'] = $success_goods_sort[$val['sgid']];
                    $success_goods_list[] = $success_temp_arr;
                }
            }

            //生鲜机计算重量 判断退款
            $deal_num = $success_goods_sort[$val['sgid']];
            if ($order['type'] == Order::TYPE_FRESH_MACHINE && isset($success_goods_weight_sort[$val['sgid']]) && $success_goods_weight_sort[$val['sgid']] < $deal_num * $val['weight']) {
                $less_price = ($deal_num * $val['weight'] - $success_goods_weight_sort[$val['sgid']]) / $val['weight'] * $val['price'];
                $less_price = sprintf("%0.2f", $less_price);
                $weight_less_price += $less_price;
                $weight_less_remark .= $val['name'] . '缺' . ($deal_num * $val['weight'] - $success_goods_weight_sort[$val['sgid']]) . '克，退还' . $less_price . '元，';
            }
        }

//             if ($this->getParam('onlyTest3')==1) {
//             	var_dump($fail_goods_list_for_create);exit();
//             }
        //如果有失败商品
        if (!empty($fail_goods_list_for_create) && $o_total_num != $fail_total_num) {
            //事务处理
            $transaction = Yii::app()->db->beginTransaction();

            try {

                $store_id = 0;
                if ($order['type'] == Order::TYPE_SUPERMARK) {
                    $store_id = $order['store_id'];
                } elseif ($order['type'] == Order::TYPE_MACHINE || $order['type'] == Order::TYPE_MACHINE_CELL_STORE || $order['type'] == Order::TYPE_FRESH_MACHINE) {
                    $store_id = $order['machine_id'];
                }

                //下待退款订单
                $create_rs = $order->createOrder($order['type'], $store_id, $order['member_id'], $fail_goods_list_for_create, $order['address_id'], $order['shipping_type'], $order['shipping_time'], $order['machine_take_type'], '商品不足，退款订单，原始订单号为：' . $order['code'], false, false);

                if ($create_rs['success'] != true) {
                    $transaction->rollBack();
                    $this->_error(Yii::t('apiModule.order', '创建退款订单失败'));
                }

                //取消退款订单 先更新商品价格
                $fail_order = $create_rs['data'];

                foreach ($fail_goods_list_for_create as $g) {
                    $g_total_price = $g['num'] * $g['price'];
                    Yii::app()->db->createCommand('UPDATE ' . OrdersGoods::model()->tableName() . ' SET total_price="' . $g_total_price . '"  ,status= ' . Order::PAY_STATUS_YES . ' , price= ' . $g['price'] . ' , supply_price= ' . $g['supply_price'] . ' WHERE order_id="' . $fail_order['id'] . '" AND sgid= ' . $g['id'])->execute();
                }
                $update_rs = Yii::app()->db->createCommand('UPDATE ' . Order::model()->tableName() . ' SET total_price="' . $fail_total_price . '"  ,status= ' . Order::STATUS_PAY . ' , pay_status= ' . Order::PAY_STATUS_YES . ' WHERE code="' . $fail_order['code'] . '" ')->execute();
//                Yii::log('失败单' . var_dump($total_price, TRUE));

                $cancel_rs = Order::orderCancel($fail_order['code'], true, '退款订单', false, false);
                if ($cancel_rs['success'] != true) {
                    $transaction->rollBack();
                    $this->_error(Yii::t('apiModule.order', '订单退款失败'));
                }

                //更新现有订单
                $total_price = 0;
                foreach ($success_goods_list as $val) {
                    //计算金额
                    $total_price += $val['num'] * $val['price'];
                    Yii::app()->db->createCommand('UPDATE ' . OrdersGoods::model()->tableName() . ' SET num= ' . $val['num'] . ' , total_price= ' . $val['num'] * $val['price'] . ' WHERE id= ' . $val['id'])->execute();
                }

                $total_price += $order['shipping_fee'];

                //生鲜机计算重量  更新原始订单金额
// 	                    if ($order['type']==Order::TYPE_FRESH_MACHINE) {
// 	                    }

                $order_rs = Yii::app()->db->createCommand('UPDATE ' . Order::model()->tableName() . ' SET total_price= ' . $total_price . ' WHERE id= ' . $order['id'])->execute();
                if (!$order_rs) {
                    $this->_error(Yii::t('apiModule.order', '订单商品更新失败'));
                }
                //订单签收
                $sign_rs = Order::orderSign($order['code'], true, null, false);
//                 Yii::log('签收' . var_dump($total_price, TRUE));
// 	                    var_dump($sign_rs);exit();
                if ($sign_rs['success'] != true) {
                    $transaction->rollBack();
                    $error_msg = empty($sign_rs['error_msg']) ? ErrorCode::getErrorStr($sign_rs['code']) : $sign_rs['error_msg'];
                    $this->_error(Yii::t('apiModule.order', '完成订单失败:') . $error_msg, $sign_rs['code']);
                }


                //首次充值 返回10%金额
                $modelOrder = new Order();
                $modelOrder->giveBackAmountFirstConsume($order['code'], $order['member_id']);

// 	                    $order->machine_status = Order::MACHINE_STATUS_YES;
// 	                    $order->save();
                $update_rs = Yii::app()->db->createCommand()->update(Order::model()->tableName(), array('machine_status' => Order::MACHINE_STATUS_YES), 'id=' . $order['id']);
            } catch (Exception $e) {
                $transaction->rollBack();
                $this->_error($e->getMessage());
            }

            //生鲜机计算重量  重量不足退款
            if ($order['type'] == Order::TYPE_FRESH_MACHINE && $weight_less_price > 0) {
                $weightLessOrderCode = Order::createNoGoodsOrder($order['type'], $order['machine_id'], $order['partner_id'], $order['member_id'], $weight_less_price, $weight_less_remark, true);
                $weightLessOrderCancelRs = Order::orderCancel($weightLessOrderCode['code'], true, null, false, false, false);
            }

            $transaction->commit();
            $this->_success('success1');
        } elseif ($o_total_num == $fail_total_num) {
            //全部商品都失败 直接退原单
            $cancel_rs = Order::orderCancel($order['code'], true, '售货机、生鲜机备货失败，订单自动取消', true, false);
            if ($cancel_rs['success'] != true) {
                $this->_error(Yii::t('apiModule.order', '订单全单退款失败'));
            }

            $this->_success('fullCancelSuccess');
        }

        $sign_rs = Order::orderSign($order['code']);

        if ($sign_rs['success'] === true) {
            if ($order->machine_take_type == Order::MACHINE_TAKE_TYPE_WITH_CODE) {
                $lines = Yii::app()->db->createCommand()
                        ->select('line_id,id,goods_id')
                        ->from(FreshMachineGoods::model()->tableName())
                        ->where('id IN (' . implode(',', $gids) . ')')
                        ->queryAll();

                if (!empty($lines)) {
                    $line_ids = array();
                    foreach ($lines as $l) {
                        $line_ids[$l['id']] = $l['line_id'];
                    }

                    ksort($nums);
                    ksort($line_ids);
                    $line_ids = array_values($line_ids);

                    $nums = array_values($nums);
                    $OKRS = ApiStock::stockFrozenOutList($store->id, $line_ids, $nums, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
                }
            }
            //首次充值 返回10%金额
            $modelOrder = new Order();
            $modelOrder->giveBackAmountFirstConsume($order['code'], $order['member_id']);

            $update_rs = Yii::app()->db->createCommand()->update(Order::model()->tableName(), array('machine_status' => Order::MACHINE_STATUS_YES), 'id=' . $order['id']);

            //生鲜机计算重量  重量不足退款
            if ($order['type'] == Order::TYPE_FRESH_MACHINE && $weight_less_price > 0) {
                $weightLessOrderCode = Order::createNoGoodsOrder($order['type'], $order['machine_id'], $order['partner_id'], $order['member_id'], $weight_less_price, $weight_less_remark, true);
                $weightLessOrderCancelRs = Order::orderCancel($weightLessOrderCode, true, null, false, false, false);

//                     var_dump($weightLessOrderCancelRs);exit();
            }

            $this->_success('success2');
        } else {
            $error_msg = empty($sign_rs['error_msg']) ? ErrorCode::getErrorStr($sign_rs['code']) : $sign_rs['error_msg'];
            $this->_error(Yii::t('apiModule.order', '完成订单失败:') . $error_msg, $sign_rs['code']);
        }
    }

    /**
     * 确认订单
     */
    public function actionOrderConfirm() {
        if ($this->getParam('onlyTest') == 1) {
            $post = $this->getParams();
        } else {
            $this->params = array('shopId', 'code', 'status');
            $requiredFields = array('shopId', 'code', 'status');
            $decryptFields = array('shopId', 'code', 'status');          //需要解密的字段
            $post = $this->decrypt($_REQUEST, $requiredFields, $decryptFields, true);
        }

        $className = $this->className;
        $goodsClassName = $this->goodsClassName;

        $store = $className::model()->find('code=:shopId', array(':shopId' => $post['shopId']));
        if (empty($store)) {
            $this->_error(Yii::t('apiModule.order', '售货机不存在！'));
        }

        $order = Order::model()->with('ordersGoods')->find('code=:code', array(':code' => $post['code']));
        if (empty($order) || $store->id != $order->machine_id) {
            $this->_error(Yii::t('apiModule.order', '订单不存在'));
        }

        //判断状态
        if ($post['status'] == 'success') {
            Yii::app()->db->createCommand('UPDATE ' . Order::model()->tableName() . ' SET machine_status= ' . Order::MACHINE_STATUS_YES . ' WHERE id= ' . $order['id'])->execute();
        } else {
            //没货自动取消订单
            $cancel_rs = Order::orderCancel($order->code, true, '售货机出货失败');
            if ($cancel_rs['success'] == true) {
                $Member = new ApiMember();
                //$GaiInfo = $Member->getInfo($order->member_id);
                $member_info = Member::model()->findByPk($order['member_id']);
                $msg = "您好，由于库存等原因,您的售货机订单：{$order->code} 已自动取消，订单金额已返还到您的账户，请注意查收";
//    			$a = $Member->sendSms($member_info['mobile'], $msg, $type = ApiMember::SKU_SEND_SMS);
                $a = $Member->sendSms($member_info['mobile'], $msg, $type = ApiMember::SKU_SEND_SMS, 0, ApiMember::SKU_SEND_SMS, array($order->code), ApiMember::MACHINE_RETURN_MONEY);
            }
        }

        $this->_success(Yii::t('apiModule.order', '设置成功'));
    }

    /**
     * 创建订单
     *
     * goods_id 用逗号分开
     * 
     * 需要支持无登录购买
     *
     */
    public function actionCreate() {
        $tag_name = 'CreadOrder';
//     	$sid = $this->getParam('shopId');        //门店id
//     	$type = $this->getParam('type');       //订单类型  1为门店  2为售货机  3为生鲜机  4为售货机格子铺
        if ($this->getParam('onlyTest') == 1) {
            $post = $this->getParams();
            $address_id = $this->getParam('addressId');
            $shipping_type = $this->getParam('shippingType');
            $shipping_time = $this->getParam('shippingTime');
        } else {
            $address_id = $this->getParam('addressId');
            $shipping_type = $this->getParam('shippingType');
            $shipping_time = $this->getParam('shippingTime');
            $this->params = array('shopId', 'type', 'machineTakeType', 'remark', 'goods', 'token');
            $requiredFields = array('shopId', 'type', 'goods');
            $decryptFields = array('shopId', 'type', 'machineTakeType', 'remark', 'goods', 'token');          //需要解密的字段
            $post = $this->decrypt($_REQUEST, $requiredFields, $decryptFields, true);
        }

//     	$machineTakeType = $this->getParam('machineTakeType',1)*1;
//     	$remark = $this->getParam('remark');
//     	$goods_arr = CJSON::decode(str_replace('\"', '"', $this->getParam('goods')));   //商品列表



        $machineTakeType = isset($post['machineTakeType']) ? $post['machineTakeType'] : 1;
        $remark = isset($post['remark']) ? $post['remark'] : '';
        $goods_arr = CJSON::decode(str_replace('\"', '"', $post['goods']));   //商品列表
        $sid = $this->vending['id'];
        $type = $this->type;
        $token = isset($post['token']) ? $post['token'] : "";
        $member = $this->vending['member_id'];
        if (!empty($token)) {
            $memberInfo = Tool::cache(self::CK_MEMBER_INFO)->get($token);
            if (!empty($memberInfo)) {
                $member = $memberInfo['id'];
            } else {
                $this->_error('用户无效');
            }
        }


        //         if ($type==Order::TYPE_SUPERMARK) {
        $limit_config = Tool::getConfig('amountlimit');
        if ($limit_config['isEnable']) {
            $amount_rs = $this->_getMemberToStoreAmount($sid, $member, null, $type);

            if ($amount_rs['isOver'] == true) {
                $this->_error(Yii::t('apiModule.order', '您的当日消费金额已超过每日最大限额，请明天再消费。'), ErrorCode::ORDER_OVER_MAX_AMOUNT_PREDAY_ERROR, $tag_name);
            }
        }

        //         }
        //处理格仔铺数据格式
        if ($type == Order::TYPE_MACHINE_CELL_STORE) {
            $ids = array();
            $num_arr = array();
            foreach ($goods_arr as $val) {
                $ids[] = $val['id'];
                $num_arr[$val['id']] = $val['num'];
            }
            $goods_list = Yii::app()->db->createCommand()
                    ->from(VendingMachineCellStore::model()->tableName())
                    ->where('machine_id=:machine_id AND  code IN ("' . implode('","', $ids) . '")', array(":machine_id" => $sid))
                    ->select('id,code')
                    ->queryAll();

            if (empty($goods_list)) {
                $this->_error(Yii::t('apiModule.order', '商品无效,'), null, $tag_name);
            }

            $goods_arr = array();
            foreach ($goods_list as $val) {
                $temp_arr = array();
                $temp_arr['id'] = $val['id'];
                $temp_arr['num'] = $num_arr[$val['code']];
                $goods_arr[] = $temp_arr;
            }
        }
        //下订单
        $order_rs = Order::model()->createOrder($type, $sid, $member, $goods_arr, $address_id, $shipping_type, $shipping_time, $machineTakeType, $remark);
        if ($order_rs['success'] == true) {
            $order = $order_rs['data'];
            $this->_success(array('id' => $order['id'], 'code' => $order['code'], 'goods_code' => $order['goods_code'], 'create_time' => $order['create_time'], 'amount' => $order['total_price'], 'store_name' => $order['store_name']), $tag_name);
        } else {
            $this->_error(Yii::t('apiModule.order', '下单失败,') . ErrorCode::getErrorStr($order_rs['code']), $order_rs['code'], $tag_name);
        }
    }

    /**
     * 获取订单信息
     * 
     * 根据提货码
     * 
     */
    public function actionInfo() {
        if ($this->getParam('onlyTest') == 1) {
            $post = $this->getParams();
        } else {
            $this->params = array('shopId', 'code');
            $requiredFields = array('shopId', 'code');
            $decryptFields = array('shopId', 'code');          //需要解密的字段

            $post = $this->decrypt($_REQUEST, $requiredFields, $decryptFields, true);
        }


        //根据提货码
        $orderInfo = Order::model()->getByGoodsCode($post['code'], $this->vending['id'], $this->type);
        if (!empty($orderInfo) && $orderInfo['status'] == Order::STATUS_COMPLETE) {
            $this->_error('订单已完成，请勿重复取货操作');
        }
        if (!empty($orderInfo) && $orderInfo['status'] == Order::STATUS_CANCEL) {
            $this->_error('订单已取消');
        }
        if (empty($orderInfo)) {
            $this->_error('订单不存在');
        }

        $rs['status'] = $orderInfo['status'];
        $rs['status_name'] = Order::status($orderInfo['status']);

        $rs['pay_status'] = $orderInfo['pay_status'];
        $rs['pay_status_name'] = Order::payStatus($orderInfo['pay_status']);

//     	var_dump($orderInfo->ordersGoods);exit();
        //俊鹏机格式
        $machine = FreshMachine::model()->findByPk($this->vending['id']);
        if ($machine->type == FreshMachine::FRESH_MACHINE_SMALL) {
            $nums = array();

            $order_info = array();
            if ($machine['type'] == FreshMachine::FRESH_MACHINE_SMALL) {
                $goodsids = array();
                $sgid = array();
                $temp_arr = array();
                foreach ($orderInfo->ordersGoods as $k => $v) {
                    $goodsids[] = $v['gid'];
                    $sgid[] = $v['sgid'];
                    $nums[$v->sgid] = $v->num;
                }
                $goods_data = Yii::app()->db->createCommand()
                        ->select('sg.id,sg.goods_id,sg.line_id,l.code as line_code,g.name')
                        ->from(FreshMachineGoods::model()->tableName() . '  sg')
                        ->leftJoin(FreshMachineLine::model()->tableName() . ' as l', ' sg.line_id=l.id ')
                        ->leftJoin(Goods::model()->tableName() . ' as g', ' sg.goods_id=g.id ')
                        ->where('sg.id IN ( ' . implode(',', $sgid) . ' )')
                        ->queryAll();
                $goodsids = array_unique($goodsids);
                foreach ($goodsids as $k => $v) {
                    $order_info['goodsList'][$k]['goodsId'] = $v;
                    $order_info['goodsList'][$k]['goodsNum'] = 0;
                    foreach ($orderInfo->ordersGoods as $v1) {
                        if ($v1['gid'] == $v) {
                            $order_info['goodsList'][$k]['goodsNum'] += $v1->num;
                        }
                    }
                    foreach ($goods_data as $v2) {
                        if ($v2['goods_id'] == $v) {
                            $temp_arr[$k][] = array('line_id' => $v2['line_id'], 'line_code' => $v2['line_code'], 'count' => $nums[$v2['id']], 'sgid' => $v2['id']);
                        }
                    }
                    $order_info['goodsList'][$k]['Hds'] = array_values($temp_arr[$k]);
                }
            }
//            var_dump($order_info);
            $order_info['goodsList'] = array_values($order_info['goodsList']);
//            var_dump($order_info['goodsList']);
            $order_info['code'] = $orderInfo['code'];
            $order_info['status'] = $orderInfo['status'];
        }
        $goods = array();
        foreach ($orderInfo->ordersGoods as $g) {
            $temp_arr = array();
            $temp_arr['id'] = $g['sgid'];
            $temp_arr['name'] = $g['name'];
            $temp_arr['code'] = $g['sg_outlets'];
            $temp_arr['goods_id'] = $g['gid'];
            $temp_arr['num'] = $g['num'];
            $temp_arr['total_price'] = $g['total_price'];
            $temp_arr['create_time'] = $g['create_time'];
            $goods[] = $temp_arr;
        }
        if (isset($order_info) && !empty($order_info)) {
            $this->_success($order_info);
        }
        $rs['goodsList'] = $goods;
        $rs['code'] = $orderInfo['code'];

        $this->_success($rs);
    }

    /*
     * 新出货机制时间验证
     * status  code
     */

    public function actionConfirmTime() {
        if ($this->getParam('onlyTest') == 1) {
            $post = array(
                'code' => $this->getParam('code'),
                'goodsStatus' => $this->getParam('goodsStatus')
            );
        } else {
            $this->params = array('shopId', 'type', 'code', 'goodsStatus');
            $requiredFields = array('shopId', 'type', 'code', 'goodsStatus');
            $decryptFields = array('shopId', 'type', 'code', 'goodsStatus');          //需要解密的字段
            $post = $this->decrypt($_REQUEST, $requiredFields, $decryptFields, true);
        }
        $order = Order::model()->find('code = :code', array(':code' => $post['code']));
//         var_dump($order);die;
        if ($post['goodsStatus'] == Order::GOODS_STATUS_YES) {
            if ($order) {
                if ($order->pay_status == Order::PAY_STATUS_YES && $order->status != Order::STATUS_COMPLETE) {
                    $order->goods_status = Order::GOODS_STATUS_YES;
                    $order->machine_status = Order::MACHINE_STATUS_YES;
                    if ($order->save()) {
                        $this->_success('停止计数器成功');
                    } else {
                        $this->_error($order->errors);
                    }
                } elseif ($order->status == Order::STATUS_CANCEL) {
                    $this->_error('订单已关闭');
                } else {
                    $this->_error('订单状态不对应');
                }
            } else {
                $this->_error('订单不存在');
            }
        } elseif ($post['goodsStatus'] == Order::GOODS_STATUS_NO) {
            if ($order) {
                if ($order->status != Order::STATUS_COMPLETE && $order->pay_status == Order::PAY_STATUS_YES) {
                    $remark = '出货失败';
                    $rs = Order::orderCancel($post['code'], true, $remark);
                    if ($rs['success'] == true) {
                        $order_rs = Order::model()->findByPk($order->id);
                        $order_rs->goods_status = Order::GOODS_STATUS_NO;
                        $this->_success('退款成功');
                    } elseif ($order && $order->status = Order::STATUS_CANCEL) {
                        $this->_error('订单已关闭');
                    } else {
                        $this->_error(ErrorCode::getErrorStr($rs['code']));
                    }
                } elseif ($order && $order->status = Order::STATUS_CANCEL) {
                    $this->_error('订单已关闭');
                } else {
                    $this->_error('订单状态不对应');
                }
            } else {
                $this->_error('订单不存在');
            }
        } elseif ($post['goodsStatus'] == Order::GOODS_STATUS_FAIL_LOCK) {
            if ($order) {
                if ($order && $order->status != Order::STATUS_COMPLETE && $order->pay_status == Order::PAY_STATUS_YES) {
                    $remark = '机器电子锁没锁好，退款。';
                    $rs = Order::orderCancel($post['code'], true, $remark);
                    if ($rs['success'] == true) {
                        $order_rs = Order::model()->findByPk($order->id);
                        $order_rs->goods_status = Order::GOODS_STATUS_NO;
                        $this->_success('机器电子锁没锁好，退款成功');
                    } else {
                        $this->_error(ErrorCode::getErrorStr($rs['code']));
                    }
                } elseif ($order && $order->status = Order::STATUS_CANCEL) {
                    $this->_error('订单已关闭');
                } else {
                    $this->_error('订单状态不对应');
                }
            } else {
                $this->_error('订单不存在');
            }
        } elseif ($post['goodsStatus'] == Order::GOODS_STATUS_FAIL) {
            if ($order) {
                if ($order && $order->status != Order::STATUS_COMPLETE && $order->pay_status == Order::PAY_STATUS_YES) {
                    $remark = '出货指令没有收到，退款。';
                    $rs = Order::orderCancel($post['code'], true, $remark);
                    if ($rs['success'] == true) {
                        $order_rs = Order::model()->findByPk($order->id);
                        $order_rs->goods_status = Order::GOODS_STATUS_NO;
                        $this->_success('出货指令没有收到，退款成功');
                    } else {
                        $this->_error(ErrorCode::getErrorStr($rs['code']));
                    }
                } elseif ($order && $order->status = Order::STATUS_CANCEL) {
                    $this->_error('订单已关闭');
                } else {
                    $this->_error('订单状态不对应');
                }
            } else {
                $this->_error('订单不存在');
            }
        }
    }

}
