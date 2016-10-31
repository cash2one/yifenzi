<?php
/**
 * 订单后端处理接口控制器
 * 
 * 服务器间处理订单
 * 
 * @author leo8705
 *
 */

class SOrderController extends SAPIController {

	/**
	 * 订单支付流程
	 */
	public function actionPay(){

		set_time_limit(300);
		$sql_first = "set interactive_timeout=300";
		Yii::app()->db->createCommand($sql_first)->execute();
		Yii::log('sorder/pay  --before -   '. json_encode($this->data) );
		$code = $this->data['code'];
		$pay_type = isset($this->data['pay_type'])?$this->data['pay_type']*1:1;
                $pos = isset($this->data['pay_status'])?$this->data['pay_status']:'other';
                $pay_time = isset($this->data['pay_time'])?$this->data['pay_time']:'';
		Yii::log('sorder/pay --after  -   '.  json_encode($this->data) );
		
		$order = Order::getDetailByCode($code);
		
		if (empty($order)) {
			$this->_error(Yii::t('apiModule.order','订单不存在'),ErrorCode::ORDER_UNEXCIT);
		}
		
		//判断状态
		if ($order->pay_status==Order::PAY_STATUS_YES) {
			$this->_error(Yii::t('apiModule.order','订单已支付'),ErrorCode::ORDER_PAYED);
		}
		
		//保存支付状态
		$order->pay_type = $pay_type;
		$order->pay_status = Order::PAY_STATUS_YES;
		$order->pay_price = $order['total_price'];
                if(empty($pay_time)){//pos 消费
                    $order->pay_time = $pay_time;
                }else{
                    $order->pay_time = time();
                }   
                if($order->machine_take_type==Order::MACHINE_TAKE_TYPE_WITH_CODE){
                $order->machine_status = Order::MACHINE_STATUS_YES;
                } 

		$order->save();
		
		
		//判断是否超过限额
		if ($order->type==Order::TYPE_SUPERMARK) {
			$project_id = API_PARTNER_SUPER_MODULES_PROJECT_ID;
			$store = Supermarkets::model()->findByPk($order->store_id);
		}elseif ($order->type==Order::TYPE_MACHINE) {
			$project_id = API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID;
			$store = VendingMachine::model()->findByPk($order->machine_id);
		}elseif ($order->type==Order::TYPE_FRESH_MACHINE) {
			$project_id = API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID;
			$store = FreshMachine::model()->findByPk($order->machine_id);
		}elseif ($order->type==Order::TYPE_MACHINE_CELL_STORE) {
			$project_id = API_MACHINE_CELL_STORE_PROJECT_ID;
			$store = VendingMachine::model()->findByPk($order->machine_id);
		}
		
		//判断状态
		if (empty($store)) {
			$this->_error(Yii::t('apiModule.order','售货机或门店不存在！'));
		}
		
		//查询库存
		$ids = array();
		$gids = array();
		$numbers = array();
		$nums = array();
		
		foreach ($order->ordersGoods as $g){
			$ids[] = $g->gid;
			$gids[] = $g->sgid;
			$nums[$g->sgid] = $g->num;
			$numbers[$g->gid] = $g->num;
		}
                $nums1 = $nums;
		$type_name = Order::type($order->type);
		
		//查询商品状态 是否有下架商品
		$g_data = array();
		if ($order->type==Order::TYPE_SUPERMARK) {
			$g_data = Yii::app()->db->createCommand()
			->select('count(1) as count')
			->from(SuperGoods::model()->tableName().'  sg')
			->leftJoin(Goods::model()->tableName().'  g', 'sg.goods_id=g.id')
			->where('sg.id IN ( '.implode(',', $gids).' )  AND sg.status='.SuperGoods::STATUS_ENABLE.' AND g.status='.Goods::STATUS_PASS)
			->queryRow();
		}elseif ($order->type==Order::TYPE_MACHINE) {
			$g_data = Yii::app()->db->createCommand()
			->select('count(1) as count')
			->from(VendingMachineGoods::model()->tableName().'  sg')
			->leftJoin(Goods::model()->tableName().' g', 'sg.goods_id=g.id')
			->where('sg.id IN ( '.implode(',', $gids).' )  AND sg.status='.VendingMachineGoods::STATUS_ENABLE.' AND g.status='.Goods::STATUS_PASS)
			->queryRow();
		}elseif ($order->type==Order::TYPE_FRESH_MACHINE) {
			$g_data = Yii::app()->db->createCommand()
			->select('count(1) as count')
			->from(FreshMachineGoods::model()->tableName().'  sg')
			->leftJoin(Goods::model()->tableName().' g', 'sg.goods_id=g.id')
			->where('sg.id IN ( '.implode(',', $gids).' )  AND sg.status='.FreshMachineGoods::STATUS_ENABLE.' AND g.status='.Goods::STATUS_PASS)
			->queryRow();
		}elseif ($order->type==Order::TYPE_MACHINE_CELL_STORE) {
			$g_data = Yii::app()->db->createCommand()
			->select('count(1) as count')
			->from(VendingMachineCellStore::model()->tableName().'  sg')
			->leftJoin(Goods::model()->tableName().' g', 'sg.goods_id=g.id')
			->where('sg.id IN ( '.implode(',', $gids).' )  AND sg.status='.VendingMachineCellStore::STATUS_ENABLE.' AND g.status='.Goods::STATUS_PASS)
			->queryRow();
		}
		
	
		//扣除冻结库存
		if ($project_id == API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID && $order->machine_take_type==Order::MACHINE_TAKE_TYPE_AFTER_PAY) {
			$lines = Yii::app()->db->createCommand()
			->select('line_id,id,goods_id')
			->from(FreshMachineGoods::model()->tableName())
			->where('id IN ('.implode(',', $gids).')')
			->queryAll();
			
			
			$line_ids = array();
			foreach ($lines as $l){
				$line_ids[$l['id']] = $l['line_id'];
			}
			
			ksort($nums);
			ksort($line_ids);
			$line_ids = array_values($line_ids);
//                        $nums = array_values($nums);
//                        ApiStock::stockFrozenOutList($store->id, $line_ids, $nums,$project_id);
	
		}
                 $nums = array_values($nums);
		ApiStock::stockFrozenOutList($store->id, ($project_id == API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID&&$order->machine_take_type==Order::MACHINE_TAKE_TYPE_AFTER_PAY)?$line_ids:$ids, $nums,$project_id);
		//处理订单
		$rs = Order::model()->orderPaySuccess($code,null,true);
		
		$ApiMember = new ApiMember();
// 		$member_info = $ApiMember->getInfo($order['member_id']);
		$member_info = Member::model()->findByPk($order['member_id']);
		
		//判断消费限额
		$limit_config = Tool::getConfig('amountlimit');
		
		if ($limit_config['isEnable']) {
			$point_amount = Order::getMemberTodayPointPayAmount($order['member_id'],$store['id'],$order['type']);
			
			if ($pay_type==Order::PAY_TYPE_POINT  && $point_amount>$limit_config['memberPointPayPreStoreLimit']) {
				$cancel_rs = Order::orderCancel($order['code']);
				//发送短信
				if (isset($member_info['mobile'])) {
				
					if($cancel_rs['success']==true){
//						$ApiMember->sendSms($member_info['mobile'], '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于积分消费不能超过'.$limit_config['memberPointPayPreStoreLimit'].'元，订单已自动取消。');
						$msg = '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于积分消费不能超过'.$limit_config['memberPointPayPreStoreLimit'].'元，订单已自动取消。';
                        $ApiMember->sendSms($member_info['mobile'], $msg,  ApiMember::SMS_TYPE_ONLINE_ORDER,0,  ApiMember::SKU_SEND_SMS,array($type_name,$order['code'],$order['total_price'],$limit_config['memberPointPayPreStoreLimit']),  ApiMember::CANCLE_ORDER_POINT_SUCCESS);                                                
					}else{
//						$ApiMember->sendSms($member_info['mobile'], '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于积分消费不能超过'.$limit_config['memberPointPayPreStoreLimit'].'元，订单自动取消失败，请联系客服。');
						$msg = '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于积分消费不能超过'.$limit_config['memberPointPayPreStoreLimit'].'元，订单自动取消失败，请联系客服。';
                        $ApiMember->sendSms($member_info['mobile'], $msg,  ApiMember::SMS_TYPE_ONLINE_ORDER,0,  ApiMember::SKU_SEND_SMS,array($type_name,$order['code'],$order['total_price'],$limit_config['memberPointPayPreStoreLimit']), ApiMember::CANCLE_ORDER_POINT_FAIL);
                        }
				}
				$this->_success(Yii::t('apiModule.order','成功'));
			}
			
			$total_amount = Order::getMemberTodayAmount($order['member_id'],$store['id'],$order['type']);
			$max_amount = $limit_config['memberTotalPayPreStoreLimit']; //默认获取后台限额
			if ($total_amount>$max_amount) {
				$cancel_rs = Order::orderCancel($order['code'],true,'',true);
				//发送短信
				if (isset($member_info['mobile'])) {
			
					if($cancel_rs['success']==true){
//						$ApiMember->sendSms($member_info['mobile'], '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于消费总额不能超过'.$max_amount.'元，订单已自动取消。');
                         $msg = '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于消费总额不能超过'.$max_amount.'元，订单已自动取消。';
                         $ApiMember->sendSms($member_info['mobile'], $msg,ApiMember::SMS_TYPE_ONLINE_ORDER,0,  ApiMember::SKU_SEND_SMS,array($type_name,$order['code'],$order['total_price'],$max_amount),  ApiMember::CANCLE_EXCESS_ORDER_SUCCESS);
					}else{
//						$ApiMember->sendSms($member_info['mobile'], '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于消费总额不能超过'.$max_amount.'元，订单自动取消失败，请联系客服。');
                        $msg ='您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于消费总额不能超过'.$max_amount.'元，订单自动取消失败，请联系客服。';
						$ApiMember->sendSms($member_info['mobile'],$msg ,ApiMember::SMS_TYPE_ONLINE_ORDER,0,  ApiMember::SKU_SEND_SMS,array($type_name,$order['code'],$order['total_price'],$max_amount),  ApiMember::CANCLE_EXCESS_ORDER_FAIL);                                    
					}
				}
				$this->_success(Yii::t('apiModule.order','成功'));
			}
			
		}
		
		//订单商品不存在或下架、异常，自动取消订单		达到消费限额
		if (!isset($g_data['count']) || $g_data['count']!=count($order->ordersGoods)) {
			$cancel_rs = Order::orderCancel($order['code'],true,'',true);
			//发送短信
			if (isset($member_info['mobile'])) {
				
				if($cancel_rs['success']==true){
//					$ApiMember->sendSms($member_info['mobile'], '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于商品下架等原因未能完成支付，订单自动取消，款项已退回您的账户，请查收。');
                    $msg =  '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于商品下架等原因未能完成支付，订单自动取消，款项已退回您的账户，请查收。';
                    $ApiMember->sendSms($member_info['mobile'],$msg,  ApiMember::SMS_TYPE_ONLINE_ORDER,0,  ApiMember::SKU_SEND_SMS,array($type_name,$order['code'],$order['total_price']),  ApiMember::CANCLE_ABNORMAL_GOODS_ORDER);                                   
				}else{
//					$ApiMember->sendSms($member_info['mobile'], '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于商品下架等原因未能完成支付，订单自动取消失败，请联系客服。');
					$msg = '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'，由于商品下架等原因未能完成支付，订单自动取消失败，请联系客服。';
                    $ApiMember->sendSms($member_info['mobile'], $msg, ApiMember::SMS_TYPE_ONLINE_ORDER,0 ,  ApiMember::SKU_SEND_SMS, array($type_name,$order['code'],$order['total_price']), ApiMember::CANCLE_ABNORMAL_GOODS_ORDER_FAIL);
                 }
				
			}
			
		}else{
			if ($rs) {
					
				//发送短信
				if (isset($member_info['mobile'])) {
//					$ApiMember->sendSms($member_info['mobile'], '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'。');
                    $msg = '您已成功支付微小企'.$type_name.'订单['.$order['code'].'],总金额为RMB '.$order['total_price'].'。' ;
                    $ApiMember->sendSms($member_info['mobile'],$msg,  ApiMember::SMS_TYPE_ONLINE_ORDER, 0, ApiMember::SKU_SEND_SMS, array($type_name, $order['code'], $order['total_price']), ApiMember::PAY_ORDER_SUCCESS);
				}
					
				//判断订单类型
				if ($order->type==Order::TYPE_MACHINE) {
					if ($order->machine_take_type==Order::MACHINE_TAKE_TYPE_WITH_CODE) {
						//发送推送通知  保留订单信息
						$order_info = array();
						$order_info['orderID'] = $order->code;
						$order_info['code'] = $order->goods_code;
						$order_info['time'] = time()*1000;
						foreach ($order->ordersGoods as $k=>$g){
							$order_info['goodsList'][$k]['goodsId'] = $g->sgid;
							$order_info['goodsList'][$k]['goodsNum'] = $g->num;
						}
						$push_rs = JPushTool::vendingMachinePush($store->device_id,'pushOrder',$order_info);

					}elseif ($order->machine_take_type==Order::MACHINE_TAKE_TYPE_AFTER_PAY){
						//马上出货  当面支付
						$order_info = array();
						$order_info['orderID'] = $order->code;
						$order_info['time'] = time()*1000;
						foreach ($order->ordersGoods as $k=>$g){
							$order_info['goodsList'][$k]['goodsId'] = $g->sgid;
							$order_info['goodsList'][$k]['goodsNum'] = $g->num;
						}
						$push_rs = JPushTool::vendingMachinePush($store->device_id,'pushGoodsOut',$order_info);

					}
						
				}
				
				
				//判断订单类型
				if ($order->type==Order::TYPE_FRESH_MACHINE) {
					if ($order->machine_take_type==Order::MACHINE_TAKE_TYPE_WITH_CODE) {
						
						$goods_data = Yii::app()->db->createCommand()
						->select('sg.id,sg.goods_id,sg.line_id,l.code as line_code,g.name')
						->from(FreshMachineGoods::model()->tableName().'  sg')
						->leftJoin(FreshMachineLine::model()->tableName().' as l', ' sg.line_id=l.id ')
						->leftJoin(Goods::model()->tableName().' as g', ' sg.goods_id=g.id ')
						->where('sg.id IN ( '.implode(',', $gids).' )')
						->queryAll();
						
						$goods_ids = array();
						$line_ids = array();
						foreach ($goods_data as $data){
							$goods_ids[] = $data['goods_id'];
							$line_ids[] = $data['line_id'];
						}
						
						$stock = ApiStock::goodsStockList($order['machine_id'], $line_ids,API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
                                                
						
						//发送推送通知  保留订单信息
						$order_info = array();
						$order_info['orderID'] = $order->code;
						$order_info['code'] = $order->goods_code;
						$order_info['time'] = time()*1000;
                                                 $machine = FreshMachine::model()->findByPk($order['machine_id']);
                                                 if($machine['type'] == FreshMachine::FRESH_MACHINE_SMALL){
                                                    $goodsids = array();
                                                    $temp_arr = array();
                                                    foreach ($order->ordersGoods as $k=>$v){
                                                     $goodsids[] = $v['gid'];
                                                    }
                                                    $goodsids = array_unique($goodsids);
                                                    foreach ($goodsids as $k=>$v){
                                                        $order_info['goodsList'][$k]['goodsId'] = $v;
                                                        $order_info['goodsList'][$k]['goodsNum'] = 0;
                                                        foreach ($order->ordersGoods as $v1){
                                                            if($v1['gid'] == $v){
                                                                $order_info['goodsList'][$k]['goodsNum'] += $v1->num;
                                                            }
                                                        }
                                                        foreach ($goods_data as $v2){
                                                            if($v2['goods_id'] == $v){
//                                                                $temp_arr[]['line_id'] = $v2['line_id'];
//								$temp_arr[]['line_code'] = $v2['line_code'];
//                                                                $temp_arr[]['count'] = isset($stock[$v2['line_id']]['stock'])?$stock[$v2['line_id']]['stock']:0;
                                                            
                                                                $temp_arr[$k][]=array('line_id'=>$v2['line_id'],'line_code'=>$v2['line_code'],'count'=>$nums1[$v2['id']],'sgid'=>$v2['id']);
                                                     
                                                            }
                                                        }
                                                        $order_info['goodsList'][$k]['Hds'] = array_values($temp_arr[$k]);
                                                    }
                                                }else{
                                                   
						foreach ($order->ordersGoods as $k=>$g){
							$order_info['goodsList'][$k]['goodsId'] = $g->sgid;
							$order_info['goodsList'][$k]['goodsNum'] = $g->num;
							
							
							$temp_arr = array();
							foreach ($goods_data as $goods){
								if ($g['sgid']==$goods['id']) {
									$order_info['goodsList'][$k]['goodsName'] = $goods['name'];
									$temp_arr['line_id'] = $goods['line_id'];
									$temp_arr['line_code'] = $goods['line_code'];
								}
							}
							
							$temp_arr['count'] = isset($stock[$g['line_id']]['stock'])?$stock[$g['line_id']]['stock']:0;
							$order_info['goodsList'][$k]['Hds'][] = $temp_arr;
							
						}
                                                }
//						$push_rs = JPushTool::vendingMachinePush($store->device_id,'pushOrder',$order_info);
                                                     array_values($order_info['goodsList']);                                                          
				
					}elseif ($order->machine_take_type==Order::MACHINE_TAKE_TYPE_AFTER_PAY){
						//马上出货  当面支付
						
						$goods_data = Yii::app()->db->createCommand()
						->select('sg.id,sg.goods_id,sg.line_id,l.code as line_code')
						->from(FreshMachineGoods::model()->tableName().'  sg')
						->leftJoin(FreshMachineLine::tableName().' as l', ' sg.line_id=l.id ')
						->where('sg.id IN ( '.implode(',', $gids).' )')
						->queryAll();
						
						$goods_ids = array();
						$line_ids = array();
						foreach ($goods_data as $data){
							$goods_ids[] = $data['goods_id'];
							$line_ids[] = $data['line_id'];
						}
						
						$stock = ApiStock::goodsStockList($order['machine_id'], $line_ids,API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
												
						$order_info = array();
						$order_info['orderID'] = $order->code;
						$order_info['time'] = time()*1000;
                                                $machine = FreshMachine::model()->findByPk($order->machine_id);
                                                if($machine['type'] == FreshMachine::FRESH_MACHINE_SMALL){
                                                    $goodsids = array();
                                                    $temp_arr = array();
                                                    foreach ($order->ordersGoods as $k=>$v){
                                                     $goodsids[] = $v['gid'];
                                                    }
                                                    $goodsids = array_unique($goodsids);
                                                    foreach ($goodsids as $k=>$v){
                                                        $order_info['goodsList'][$k]['goodsId'] = $v;
                                                        $order_info['goodsList'][$k]['goodsNum'] = 0;
                                                        foreach ($order->ordersGoods as $v1){
                                                            if($v1['gid'] == $v){
                                                                $order_info['goodsList'][$k]['goodsNum'] += $v1->num;
                                                            }
                                                        }
                                                        foreach ($goods_data as $v2){
                                                            if($v2['goods_id'] == $v){
                                                                
                                                                $temp_arr[$k][]=array('line_id'=>$v2['line_id'],'line_code'=>$v2['line_code'],'count'=>$nums1[$v2['id']],'sgid'=>$v2['id']);
                                                             
//								$temp_arr[]['line_code'] = $v2['line_code'];
//                                                                $temp_arr[]['count'] = isset($stock[$v2['line_id']]['stock'])?$stock[$v2['line_id']]['stock']:0;
                                                            }
                                                        }
                                                        $order_info['goodsList'][$k]['Hds'] = array_values($temp_arr[$k]);
                                                    }
                                                   array_values($order_info['goodsList']);    
                                                }else{
						foreach ($order->ordersGoods as $k=>$g){
							$order_info['goodsList'][$k]['goodsId'] = $g->sgid;
							$order_info['goodsList'][$k]['goodsNum'] = $g->num;
							
							$temp_arr = array();
							foreach ($goods_data as $goods){
								if ($g['sgid']==$goods['id']) {
									$temp_arr['line_id'] = $goods['line_id'];
									$temp_arr['line_code'] = $goods['line_code'];
								}
							}
	
							$temp_arr['count'] = isset($stock[$goods['line_id']]['stock'])?$stock[$goods['line_id']]['stock']:0;
							$order_info['goodsList'][$k]['Hds'][] = $temp_arr;
						}
                                                }
                                                                                                      if($pos !='pos'){
						$push_rs = JPushTool::vendingMachinePush($store->device_id,'pushGoodsOut',  $order_info);
                                                                                                        }
					}
				
				}
				
				
				//判断订单类型  格仔铺  推送
				if ($order->type==Order::TYPE_MACHINE_CELL_STORE) {

					
					if ($order->machine_take_type==Order::MACHINE_TAKE_TYPE_WITH_CODE) {
						//发送推送通知  保留订单信息
						$order_info = array();
						$order_info['orderID'] = $order->code;
						$order_info['code'] = $order->goods_code;
						$order_info['time'] = time()*1000;
						foreach ($order->ordersGoods as $k=>$g){
							$temp_arr = array();
							$temp_arr['code'] = $g['sg_outlets'];
							$temp_arr['id'] = $g['sgid'];
							$temp_arr['num'] = $g['num'];
							$order_info['goodsList'][] = $temp_arr;
						}
//						$push_rs = JPushTool::vendingMachinePush($store->device_id,'pushCellStoreOrder',$order_info);
				
					}elseif ($order->machine_take_type==Order::MACHINE_TAKE_TYPE_AFTER_PAY){
						//马上出货  当面支付
						$order_info = array();
						$order_info['orderID'] = $order->code;
						$order_info['time'] = time()*1000;
						foreach ($order->ordersGoods as $k=>$g){
							$temp_arr = array();
							$temp_arr['code'] = $g['sg_outlets'];
							$temp_arr['id'] = $g['sgid'];
							$temp_arr['num'] = $g['num'];
							$order_info['goodsList'][] = $temp_arr;
						}
//						$push_rs = JPushTool::vendingMachinePush($store->device_id,'pushCellStoreGoodsOut',$order_info);
				
					}
				}
				
				
				$this->_success(Yii::t('apiModule.order','成功'));
			}else{
				$this->_error(Yii::t('apiModule.order','失败2'));
			}
		}
		
		$this->_success(Yii::t('apiModule.order','成功'));
		
	}
	
	
	/**
	 * 挂单系统批发支付
	 * 
	 * 
	 */
	public function actionGuadanPifaPay(){

		set_time_limit(3600);
		$code = $this->data['code'];
		$order = PifaOrder::getByCode($code);
    
		if (empty($order)) {
			$this->_error(Yii::t('apiModule.order','订单不存在'),ErrorCode::ORDER_UNEXCIT);
		}
	
		//判断状态
		if ($order->pay_status==PifaOrder::IS_PAY_YES) {
			$this->_error(Yii::t('apiModule.order','订单已支付'),ErrorCode::ORDER_PAYED);
		}
		
		//保存支付状态
//		Yii::app()->db->createCommand()->update(PifaOrder::model()->tableName(), array('pay_status'=>PifaOrder::IS_PAY_YES,'pay_time'=>time()),'code=:code',array(':code'=>$code));

		
		//绑定和非绑定，优先绑定，不够扣就从非绑定出，两个都没钱就不能买
//		$gaiAmount = 0;
		
//		$bindingAccount =  AccountBalance::getGuadanCommonAmount(CommonAccount::TYPE_GUADAN_SALE_BINDING);
//		if ($bindingAccount<$order['buy_amount']) {
//		    $gaiAmount = AccountBalance::getGuadanCommonAmount(CommonAccount::TYPE_GUADAN_SALE_UNBUNDLING);
//		    $type = CommonAccount::TYPE_GUADAN_SALE_UNBUNDLING;
//		}else{
//		    $gaiAmount = $bindingAccount;
//			$type = CommonAccount::TYPE_GUADAN_SALE_BINDING;
//		}
		
                   //记录售卖政策售出金额
                    //查询售卖情况
                $collect = Yii::app()->db->createCommand()
                        ->from(GuadanCollect::model()->tableName())
                        ->select('amount_bind, amount_unbind, sale_amount_bind, sale_amount_unbind')
                        ->where('id='.$order['collect_id'])
                        ->queryRow();
                $amount_tol = $collect['sale_amount_bind'] + $order['buy_amount'];
                $unamount_tol = $collect['sale_amount_unbind'] +$order['buy_amount'];
                if($collect['amount_bind'] < $amount_tol && $collect['amount_unbind'] >= $unamount_tol){                 
                    $type = CommonAccount::TYPE_GUADAN_SALE_UNBUNDLING;
                }elseif($collect['amount_bind'] < $amount_tol && $collect['amount_unbind'] < $unamount_tol){
                        $this->_error('挂单资金池不足');
                }else{                   
                    $type = CommonAccount::TYPE_GUADAN_SALE_BINDING;
                }
                
		
//		if ($gaiAmount<$order['buy_amount']) {
//			$this->_error('挂单资金池不足');
//		}
	
		$rs = PifaOrder::paySuccess($code,$type);
	
		if ($rs) {
			$this->_success(Yii::t('apiModule.order','成功'));
		}else{
			$this->_error(Yii::t('apiModule.order','失败'));
		}
		
	}
	
	
	/**
	 * 挂单系统用户购买积分支付
	 *
	 */
	public function actionGuadanPointOrderPay(){
		set_time_limit(3600);
		$code = $this->data['code'];
		$order = GuadanJifenOrder::getByCode($code);

	
		if (empty($order)) {
			$this->_error(Yii::t('apiModule.order','订单不存在'),ErrorCode::ORDER_UNEXCIT);
		}
	
		//判断状态
		if ($order->pay_status==GuadanJifenOrder::PAY_STATUS_YES) {
			$this->_error(Yii::t('apiModule.order','订单已支付'),ErrorCode::ORDER_PAYED);
		}

        $transaction = Yii::app()->db->beginTransaction();
		//保存支付状态
		Yii::app()->db->createCommand()->update(GuadanJifenOrder::model()->tableName(), array('pay_status'=>GuadanJifenOrder::PAY_STATUS_YES,'pay_time'=>time()),'code=:code',array(':code'=>$code));

		$rs = GuadanJifenOrder::paySuccess($code,false);

		if ($rs['result']) {                  
            $transaction->commit();
                    $model_order = new Order();
                    $model_order->giveBackAmountFirstConsume($code,$order['member_id'],$type='guadan');
			$this->_success(Yii::t('apiModule.order','成功'));
		}else{
            $transaction->rollBack();
			$this->_error(Yii::t('apiModule.order','失败: ').$rs['msg']);
		}
	
	}

    
}