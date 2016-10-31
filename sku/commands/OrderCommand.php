<?php

// use order;
/**
 * 订单定时任务脚本
 *
 * 
 *
 * @author csj leo8705
 */
class OrderCommand extends CConsoleCommand {
	public $cparams;
	
	public function beforeAction($action, $params){
		parent::beforeAction($action, $params);
		$this->cparams = require_once Yii::getPathOfAlias('appl').DS.'config'.DS.'params.php';                                 
		set_time_limit(3600);
		return  true;
	}
	
	/**
	 * 自动取消未支付订单
	 *
	 * 包含3部分处理  1 超时未支付的订单自动取消 并返回冻结库存  2 超时未签收的订单自动签收  3 超时未发货的订单自动退款
	 *
	 */
	public function autoCancelUnpayOrder($id=0){
	
		//查询超时前未支付的订单
		$cri = new CDbCriteria();
		$cri->select = 't.id,t.code,t.store_id,t.machine_id,t.type';
		$cri->with = 'ordersGoods';
		$cri->compare('t.father_id', 0);
		$cri->compare('t.status', Order::STATUS_NEW);
		$cri->compare('t.pay_status', Order::PAY_STATUS_NO);
		$cri->compare('t.is_auto_cancel', Order::IS_AUTO_CANCEL_NO);
		$orderExpireTime=Tool::getConfig('orderExpireTime', 'orderExpireTime');
		$cri->addCondition(' t.create_time<= '.(time()-$orderExpireTime));
		$cri->addCondition(' t.id>'.$id);
		$cri->order = 't.id';
		$order = Order::model()->find($cri);
	
	
		//自动取消  归还库存
		if (!empty($order)) {
			Order::orderCancel($order->code);
			$rs = Yii::app()->db->createCommand('UPDATE '.Order::model()->tableName().' SET  is_auto_cancel='.Order::IS_AUTO_CANCEL_YES.' , seller_remark= "'.$order->seller_remark.' 超时未支付，自动取消。"
					 WHERE id='.$order->id)->execute();
			$this->autoCancelUnpayOrder($order['id']);
			
		}else{
			return true;
		}
	
	}
	
	/**
	 * 自动取消未支付订单
	 * 
	 * 包含3部分处理  1 超时未支付的订单自动取消 并返回冻结库存  2 超时未签收的订单自动签收  3 超时未发货的订单自动退款
	 * 
	 */
	public function actionAutoCancelUnpayOrder(){
		$this->autoCancelUnpayOrder();
		echo 'finish';
	}
	
	
	/**
	 * 自动取消已支付订单  仅限于超市订单  售货机订单另外处理
	 *
	 *取消已支付未及时发货的订单
	 *
	 */
	public function autoCancelPayedOrder($id=0){
	//查询超时前已支付未发货的订单
		$cri = new CDbCriteria();
		$cri->select = 't.id,t.code,t.store_id,t.machine_id,t.type';
		$cri->with = 'ordersGoods';
	
		$cri->compare('t.father_id', 0);
		$cri->compare('t.type', Order::TYPE_SUPERMARK);
		$cri->compare('t.status', Order::STATUS_PAY);
		$cri->compare('t.is_auto_cancel', Order::IS_AUTO_CANCEL_NO);
		$cri->compare('t.shipping_type', Order::SHIPPING_TYPE_SEND);
        $orderUnsendAutoRefundTime=Tool::getConfig('orderExpireTime', 'orderUnsendAutoRefundTime');
		$cri->addCondition(' t.pay_time<= '.(time()-$orderUnsendAutoRefundTime));
		$cri->addCondition(' t.id>'.$id);
		$cri->order = 't.id';
		$order = Order::model()->find($cri);
	
		//自动取消  归还库存
		if (!empty($order)) {
			$ids = array();
			$nums = array();
			
			foreach ($order->ordersGoods as $g){
				$ids[] = $g->gid;
				$nums[] = $g->num;
			}
				
			$cancel_rs = Order::orderCancel($order->code,true);				//取消订单
			if ($cancel_rs['success']!=true) {
				echo 'error: '.$order->code.'  | ';
				$rs = Yii::app()->db->createCommand('UPDATE '.Order::model()->tableName().' SET  is_auto_cancel='.Order::IS_AUTO_CANCEL_YES.' , seller_remark= "'.$order->seller_remark.'  超时未发货，自动取消失败，请联系客服。"
					 WHERE id='.$order->id)->execute();
				
			}else{
				$rs = Yii::app()->db->createCommand('UPDATE '.Order::model()->tableName().' SET  is_auto_cancel='.Order::IS_AUTO_CANCEL_YES.' , seller_remark= "'.$order->seller_remark.'   超时未发货，自动取消。"
					 WHERE id='.$order->id)->execute();
			}
				
			$this->autoCancelPayedOrder($order->id);
    	
		}else{
			return true;
		}
	
	}
	
	/**
	 * 自动取消已支付订单
	 *
	 *取消已支付未及时发货的订单
	 *
	 */
	public function actionAutoCancelPayedOrder(){
		$this->autoCancelPayedOrder();
		echo 'finish';
	}
	
	
	
	public function autoCancelMachineOrder($id=0){

		$cri = new CDbCriteria();
		$cri->select = 't.id,t.code,t.machine_id,t.type';
		$cri->with = 'ordersGoods';
	
		$cri->compare('t.father_id', 0);
		$cri->addCondition('t.type IN ( '.Order::TYPE_MACHINE.','.Order::TYPE_FRESH_MACHINE.','.Order::TYPE_MACHINE_CELL_STORE.' )');
		$cri->compare('t.machine_status', Order::MACHINE_STATUS_NO);                                  
		$cri->compare('t.pay_status', Order::PAY_STATUS_YES);
		$cri->compare('t.status', Order::STATUS_PAY);
		$cri->compare('t.is_auto_cancel', Order::IS_AUTO_CANCEL_NO);
                $cri->addCondition('t.goods_status !='.Order::GOODS_STATUS_YES);  //机器时间验证失败 不可出货状态
		$cri->addCondition(' t.id>'.$id);
		 
        $machineAutoCancelTime=Tool::getConfig('orderExpireTime', 'machineAutoCancelTime');
		$cri->addCondition(' t.pay_time<= '.(time()-$machineAutoCancelTime));
                                  
		$cri->order = 't.id';
		$order = Order::model()->find($cri);
	
		if (!empty($order)) {
			$ids = array();
			$nums = array();

			$outlets = $order->machine_id;
			 
			foreach ($order->ordersGoods as $g){
				$ids[] = $g->gid;
				$nums[] = $g->num;
			}
			
			$total_price = $order['total_price'];
			 
			$cancel_rs = Order::orderCancel($order->code,true,'售货机、生鲜机备货失败，自动取消订单');				//取消订单
			$apiMember = new ApiMember();

			$member_info = Member::model()->findByPk($order['member_id']);
			if ($cancel_rs['success']!=true) {
				echo 'error: '.$order->code.' | ';
				$rs = Yii::app()->db->createCommand('UPDATE '.Order::model()->tableName().' SET  is_auto_cancel='.Order::IS_AUTO_CANCEL_YES.' , seller_remark= "'.$order->seller_remark.'  售货机、生鲜机备货失败，自动取消订单['.$order['code'].']失败，请联系客服。"
					 WHERE id='.$order->id)->execute();
				//发送短信
				$msg = '售货机、生鲜机备货失败，自动取消订单['.$order['code'].']失败，请联系客服。';
                $apiMember->sendSms($member_info['mobile'], $msg,  ApiMember::SMS_TYPE_ONLINE_ORDER,0,  ApiMember::SKU_SEND_SMS,array($order['code']),  ApiMember::STOCK_UP_CANCLE_ORDER_FAIL);
			}else{
				$rs = Yii::app()->db->createCommand('UPDATE '.Order::model()->tableName().' SET  is_auto_cancel='.Order::IS_AUTO_CANCEL_YES.' , seller_remark= "'.$order->seller_remark.'   售货机、生鲜机备货失败，订单自动取消成功，货款已退还到您的账户"
					 WHERE id='.$order->id)->execute();
				
				//通知售货机退货
				$order_info = array();
				$order_info['orderID'] = $order->code;
				$order_info['time'] = time()*1000;
				$store = VendingMachine::model()->findByPk($order['machine_id']);
				if (isset($store['device_id'])) {
					$push_rs = JPushTool::vendingMachinePush($store['device_id'],'pushOrderCancel',$order_info);
				}
				
				//发送短信
				$msg = ' 售货机、生鲜机备货失败，订单['.$order['code'].']自动取消成功，货款已退还到您的账户' ;
                $apiMember->sendSms($member_info['mobile'],$msg,  ApiMember::SMS_TYPE_ONLINE_ORDER,0,  ApiMember::SKU_SEND_SMS,array($order['code']),  ApiMember::STOCK_UP_CANCLE_ORDER);
			}

			$this->autoCancelMachineOrder($order->id);
	
		}else{
			return true;
		}
		 
	}
	
	
	/**
	 * 自动取消售货机未出货订单
	 *
	 * 取消售货机未出货订单
	 *
	 */
	public function actionAutoCancelMachineOrder(){
		$this->autoCancelMachineOrder();
		echo 'finish';
	}
	
	

	/**
	 * 自动取消售货机超时未提货订单
	 *
	 * 取消售货机未出货订单
	 * 
	 * @param $takeType  下单方式
	 *
	 */
	public function autoCancelUnTakeMachineOrder($id=0,$takeType=Order::MACHINE_TAKE_TYPE_WITH_CODE){
		$cri = new CDbCriteria();
		$cri->select = 't.id,t.code,t.machine_id,t.type';
		$cri->with = 'ordersGoods';
	
		$cri->compare('t.father_id', 0);
		$cri->addCondition('t.type IN ( '.Order::TYPE_MACHINE.','.Order::TYPE_FRESH_MACHINE.','.Order::TYPE_MACHINE_CELL_STORE.' )');
		
		$cri->compare('t.machine_status', Order::MACHINE_STATUS_YES);
		$cri->compare('t.pay_status', Order::PAY_STATUS_YES);
		$cri->compare('t.status', Order::STATUS_PAY);
		$cri->compare('t.is_auto_cancel', Order::IS_AUTO_CANCEL_NO);
		$cri->compare('t.machine_take_type', $takeType);
	
		$cri->addCondition(' t.id>'.$id);
			
		if ($takeType==Order::MACHINE_TAKE_TYPE_WITH_CODE) {
			$machineUnTakeAutoCancelTime=Tool::getConfig('orderExpireTime', 'machineUnTakeAutoCancelTime');
		}elseif ($takeType==Order::MACHINE_TAKE_TYPE_AFTER_PAY){
			$machineUnTakeAutoCancelTime=Tool::getConfig('orderExpireTime', 'machineScanOrderUnTakeAutoCancelTime');
		}else {
			exit('takeTypeError');
		}
        
		$cri->addCondition(' t.pay_time<= '.(time()-$machineUnTakeAutoCancelTime));
		$cri->order = 't.id';
		$order = Order::model()->find($cri);
	
		if (!empty($order)) {
			$ids = array();
			$nums = array();
			
			foreach ($order->ordersGoods as $g){
				$ids[] = $g->gid;
				$nums[] = $g->num;
			}
	
			$cancel_rs = Order::orderCancel($order->code,true,'由于您超时未到售货机、生鲜机取货，自动取消订单');				//取消订单
			$apiMember = new ApiMember();
			//$member_info = $apiMember->getInfo($order['member_id']);
			$member_info = Member::model()->findByPk($order['member_id']);
			if ($cancel_rs['success']!=true) {
				echo 'error: '.$order->code.' | ';
				$rs = Yii::app()->db->createCommand('UPDATE '.Order::model()->tableName().' SET  is_auto_cancel='.Order::IS_AUTO_CANCEL_YES.' , seller_remark= "'.$order->seller_remark.'  用户超时未到售货机、生鲜机取货，自动取消订单['.$order['code'].']失败。"
					 WHERE id='.$order->id)->execute();
				//发送短信
				$msg = ' 由于您超时未到售货机、生鲜机取货，自动取消订单['.$order['code'].']失败，请联系客服。';
                $apiMember->sendSms($member_info['mobile'], $msg, ApiMember::SMS_TYPE_ONLINE_ORDER,0,  ApiMember::SKU_SEND_SMS,array($order['code']),  ApiMember::OVER_TIME_CANCLE_ORDER_FAIL);
			}else{
				$rs = Yii::app()->db->createCommand('UPDATE '.Order::model()->tableName().' SET  is_auto_cancel='.Order::IS_AUTO_CANCEL_YES.' , seller_remark= "'.$order->seller_remark.'   用户超时未到售货机取货，订单自动取消成功，货款已退还到您的账户"
					 WHERE id='.$order->id)->execute();
	
				//通知售货机退货
				$order_info = array();
				$order_info['orderID'] = $order->code;
				$order_info['time'] = time()*1000;
				$store = VendingMachine::model()->findByPk($order['machine_id']);
				
				if (isset($store['device_id'])) {
					$push_rs = JPushTool::vendingMachinePush($store['device_id'],'pushOrderCancel',$order_info);
				}
	
				//发送短信
				$msg =  ' 由于您超时未到售货机、生鲜机取货，订单['.$order['code'].']自动取消成功，货款已退还到您的账户';
                $apiMember->sendSms($member_info['mobile'], $msg, ApiMember::SMS_TYPE_ONLINE_ORDER,0,  ApiMember::SKU_SEND_SMS,array($order['code']),  ApiMember::OVER_TIME_CANCLE_ORDER);
			}
	
			$this->autoCancelUnTakeMachineOrder($order->id,$takeType);
	
		}else{
			return true;
		}
			
	}
	
	
	/**
	 * 自动取消售货机未提货订单  在线下单方式
	 *
	 * 取消售货机未出货订单
	 *
	 */
	public function actionAutoCancelUnTakeMachineOrder(){
		$this->autoCancelUnTakeMachineOrder();
		echo 'finish';
	}
	
	/**
	 * 自动取消售货机未提货订单  扫码下单方式
	 *
	 * 取消售货机未出货订单
	 *
	 */
	public function actionAutoCancelUnTakeMachineScanOrder(){
		$this->autoCancelUnTakeMachineOrder(0,Order::MACHINE_TAKE_TYPE_AFTER_PAY);
		echo 'finish';
	}

}
