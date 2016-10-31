<?php

/**
 * 挂单处理脚本
 *
 *定时返还金额
 *
 * @author csj leo8705
 */
class GuadanCommand extends CConsoleCommand {

	public function beforeAction($action, $params){
		parent::beforeAction($action, $params);
		set_time_limit(3600);
		return  true;
	}
	
	/**
	 * 返还最旧的一条返款记录
	 *
	 */
	public function returnAmount($last_time=0){ 
		$time_before = strtotime(date('Y-m-d'))+(3600*24);  	//明天零点前的都返还
		$rc = Yii::app()->db->createCommand()
		->from(GuadanJifenOrderDetail::model()->tableName())
		->where('status='.GuadanJifenOrderDetail::STATUS_NEW.' AND  to_time>='.$last_time.' AND to_time<='.$time_before)
		->order('to_time ASC')
		->limit(1)
		->queryRow();
	
	
		if (!empty($rc)) {
			$trans = Yii::app()->db->beginTransaction();
			try {
				$order_info = Yii::app()->db->createCommand()
				->from(GuadanJifenOrder::model()->tableName())
				->where('id='.$rc['order_id'])
				->limit(1)
				->queryRow();
				
				//执行流水
				AccountBalance::guadanReturnInstallmentAmount($order_info['member_id'],$rc['to_amount'],$order_info,'定期返还金额');
				$rs = Yii::app()->db->createCommand('UPDATE '.GuadanJifenOrderDetail::model()->tableName().' SET  status='.GuadanJifenOrderDetail::STATUS_FINISH.'
					 WHERE id='.$rc['id'])->execute();
				$trans->commit();
			}catch (Exception $e){
				$trans->rollback();
				echo 'error->id: '.$rc['id'].'    MSG:'.$e->getMessage();
			}
			
			$this->returnAmount($rc['to_time']);
				
		}else{
			return true;
		}
	
	}
	
	/**
	 * 自动返还金额
	 *
	 */
	public function actionAutoReturnAmount(){
		$this->returnAmount();
		echo 'finish';
	}
	
	
	
	/**
	 * 启动政策
	 * @param number $last_time
	 * @return boolean
	 */
	public function startCollect($last_time=0){
    	$time_before = strtotime(date('Y-m-d'))+(3600*24);
    	//查询未启用政策
    	$rc = Yii::app()->db->createCommand()
    	->from(GuadanCollect::model()->tableName())
    	->where('status='.GuadanCollect::STATUS_NEW.' AND  time_start>='.$last_time.' AND time_start<='.$time_before)
    	->order('time_start ASC')
    	->queryRow();
    
    
    	if (!empty($rc)) {
    		$trans = Yii::app()->db->beginTransaction();
    		try {
    			$oldList = GuadanCollect::model()->findAll('status='.GuadanCollect::STATUS_ENABLE);
    
    			if (!empty($oldList)) {
    				foreach ($oldList as $collect){
    					//查relation中此售卖的用户信息
    					$db = Yii::app()->db;
    					$sql = "SELECT * FROM {{guadan_relation}} WHERE collect_id=" . $collect->id .' FOR UPDATE';
    
    					//计算总额  非绑定积分总额
    					$amount_unbind_total  = Yii::app()->db->createCommand()
    					->from('{{guadan_relation}}')
    					->select('sum(amount) as amount')
    					->where('collect_id=:collect_id AND type='.GuadanRelation::TYPE_UNBIND,array(':collect_id'=>$collect['id']))
    					->queryRow();
    					$amount_unbind_total = $amount_unbind_total['amount']*1;
    
    					//带绑定积分总额
    					$amount_tobind_total  = Yii::app()->db->createCommand()
    					->from('{{guadan_relation}}')
    					->select('sum(amount) as amount')
    					->where('collect_id=:collect_id AND type='.GuadanRelation::TYPE_TOBIND,array(':collect_id'=>$collect['id']))
    					->queryRow();
    					$amount_tobind_total = $amount_tobind_total['amount']*1;
    
    					//批发已售总额
    					$pifa_amout = Yii::app()->db->createCommand()
    					->from(PifaOrder::model()->tableName())
    					->select('sum(buy_amount) as amount')
    					->where('collect_id=:collect_id AND status='.PifaOrder::STATUS_PAY,array(':collect_id'=>$collect['id']))
    					->queryRow();
    					$amout_tobing = $pifa_amout['amount']*1;
    						
    					//官方充值消费总额
    					$officel_sell_amout = Yii::app()->db->createCommand()
    					->from(GuadanJifenOrder::model()->tableName().' as t')
    					->leftJoin(GuadanRule::model()->tableName().' as r', 't.rule_id=r.id')
    					->select('sum(t.buy_amount*t.quantity) as amount')
    					->where('r.collect_id=:collect_id AND t.type='.GuadanJifenOrder::TYPE_OFFICAL.' AND t.status='.GuadanJifenOrder::STATUS_PAY,array(':collect_id'=>$collect['id']))
    					->queryRow();
    					$amout_unbing = $officel_sell_amout['amount']*1;
    
    					$relation = $db->createCommand($sql)->queryAll();
    					if(is_array($relation) && !empty($relation)){
    						foreach ($relation as $r){
    							//获取挂单模型
    							$sql = 'SELECT member_id,gai_number FROM {{guadan}} where id='.$r['guadan_id'];
    							$guadan = $db->createCommand($sql)->queryRow();
    							// 更新关系表，余额清零
    							$sql = 'UPDATE {{guadan_relation}} SET amount_remain = 0 WHERE collect_id = ' . $r['collect_id'] .' AND guadan_id = ' . $r['guadan_id'];
    							$db->createCommand($sql)->execute();
    								
    							//积分返回挂单表
    							//中止后的售卖计划不可再次启动，未出售的积分将按出资比例返回至会员挂单中。
    							//已批发积分 为带绑定积分    官方充值的为非绑定积分
    							$return_amount = 0;
    							if ($r['type']==GuadanRelation::TYPE_TOBIND) {
    								$return_amount = ($amount_tobind_total-$amout_tobing)*($r['amount']/$amount_tobind_total);
    							}elseif ($r['type']==GuadanRelation::TYPE_UNBIND){
    								$return_amount = ($amount_unbind_total-$amout_unbing)*($r['amount']/$amount_unbind_total);
    							}
    
    							$sql = 'UPDATE {{guadan}} set amount_remain = amount_remain+ '. $return_amount  .' WHERE id=' .$r['guadan_id'];
    							$db->createCommand($sql)->execute();
    							//流水//////
    							if ($r['amount_remain']>0) $collect->stopFlow($guadan['gai_number'],$r['amount_remain'],$r['type']);
    						}
    						$collect->status = GuadanCollect::STATUS_DISABLE;
    						if($collect->save(false)){
    							//更新规则表及商品表  清除缓存
    							$db->createCommand()->update(GuadanRule::model()->tableName(),array('status'=>GuadanRule::STATUS_DISABLE),'collect_id='.$collect->id);
    								
    							$update_sql = 'UPDATE '.GuadanRule::model()->tableName().' as r  ,  '.GuadanJifenGoods::model()->tableName().' as g
                    			 SET g.status='.GuadanJifenGoods::STATUS_DISABLE.' WHERE r.collect_id = ' . $collect->id .' AND g.rule_id = r.id ';
    							$db->createCommand($update_sql)->execute();
    								
    							GuadanJifenGoods::clearCache();
    							$collect_code = $collect->code;
    							@SystemLog::record("系统自动中止售卖计划<{$collect_code}>",  SystemLog::LOG_TYPE_SHOUMAI);
    						} else {
    							$result = false;
    						}
    					}
    
    				}
    			}
    
    			//开启当前政策
    			Yii::app()->db->createCommand()->update(GuadanCollect::model()->tableName(), array('status'=>GuadanCollect::STATUS_ENABLE),'id='.$rc['id']);
    			Yii::app()->db->createCommand()->update(GuadanRule::model()->tableName(),array('status'=>GuadanRule::STATUS_ENABLE),'collect_id='.$rc['id']);
    			$trans->commit();
    		}catch (Exception $e){
    			$trans->rollback();
    			echo 'error->id: '.$rc['id'].'    MSG:'.$e->getMessage();
    		}
    
    		$this->startCollect($rc['time_start']);
    
    	}else{
    		return true;
    	}
    
    }

	/**
	 * 自动启用政策
	 *
	 */
	public function actionAutoStartCollect(){
		$this->startCollect();
		echo 'finish';
	}
	
}
