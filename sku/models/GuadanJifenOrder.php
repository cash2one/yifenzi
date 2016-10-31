<?php

/**
 * This is the model class for table "{{guadan_jifen_order}}".
 *
 * The followings are the available columns in table '{{guadan_jifen_order}}':
 * @property string $id
 * @property string $code
 * @property integer $member_id
 * @property integer $partner_member_id
 * @property integer $partner_id
 * @property integer $point
 * @property integer $point_give
 * @property integer $status
 * @property integer $pay_status
 * @property integer $create_time
 * @property integer $pay_time
 */
class GuadanJifenOrder extends CActiveRecord
{
	
	//订单状态
    const STATUS_NEW = 1;   //新订单
    const STATUS_PAY = 2;   //交易完成
	public static function getStatus($k = null) {
		$arr = array(
				self::STATUS_NEW => Yii::t('order', '已下单'),
				self::STATUS_PAY=> Yii::t('order','已完成'),
		);
		return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
	}
	
	const PAY_STATUS_NO = 0;
	const PAY_STATUS_YES = 1;
	
	/**
	 * 支付状态
	 * （1未支付，2已支付）
	 * @param null $k
	 * @return array|null
	 */
	public static function payStatus($k = null) {
		$arr = array(
				self::PAY_STATUS_NO => Yii::t('order', '未支付'),
				self::PAY_STATUS_YES => Yii::t('order', '已支付'),
		);
		return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
	}
	
	//订单状态
	const TYPE_OFFICAL = 1;   //官方订单
	const TYPE_PARTNER = 2;   //商家积分
    const TYPE_AIR_RECHARGE_OFFICAL = 3; //空中充值官方积分充值
    const TYPE_AIR_RECHARGE_PARTNER = 4; //空中充值商家积分充值
	public static function getTypes($k = null) {
		$arr = array(
				self::TYPE_OFFICAL => Yii::t('order', '官方订单'),
				self::TYPE_PARTNER=> Yii::t('order','商家订单'),
		);
		return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
	}
	
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{guadan_jifen_order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, partner_member_id, partner_id,status, pay_status, create_time, pay_time', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, code, member_id, partner_member_id, partner_id, point, point_give, status, pay_status, create_time, pay_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'code' => '订单号',
			'member_id' => '会员id',
			'partner_member_id' => '商家会员id',
			'partner_id' => '商家合作id',
			'point' => '积分商品id',
			'point_give' => '赠送积分',
			'status' => '订单状态',
			'pay_status' => '支付状态',
			'create_time' => '创建时间',
			'pay_time' => '支付时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('partner_member_id',$this->partner_member_id);
		$criteria->compare('partner_id',$this->partner_id);
		$criteria->compare('point',$this->point);
		$criteria->compare('point_give',$this->point_give);
		$criteria->compare('status',$this->status);
		$criteria->compare('pay_status',$this->pay_status);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('pay_time',$this->pay_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GuadanJifenOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	

	/**
	 * 用户购买商家积分创建订单
	 * @param $member
	 * @param $goods_id
	 */
	const ORDER_TYPE_PARTNER = 5;
	public static function _createOrder($member,$goods_id,$rule,$num,$type=self::TYPE_PARTNER,$rechargeMemberId = ""){
		//商家折扣
		$selling_setting = Yii::app()->db->createCommand()
		->from(GuadanPartnerSetting::model()->tableName())
		->select('*')
		->where('member_id=:member_id',array(':member_id'=>$rule['member_id']))
		->queryRow();
		$selling_discount = $selling_setting?$selling_setting['selling_discount']:100;
		if($member == $rule['member_id'])  throw new Exception('商家不能将交易积分转赠自己！');
		$order = new GuadanJifenOrder();
		$order->code = Order::_createCode(self::ORDER_TYPE_PARTNER);
		$order->type = $type;
		$order->member_id = $member;
		$order->partner_member_id = $rule['member_id'];
		$order->partner_id =$rule['partner_id'];
		$order->goods_id = $goods_id;
		$order->rule_id = $rule['rule_id'];
		$order->quantity = $num;
		$order->unit_price = floor($rule['amount_pay']*$selling_discount)/100;
		$order->total_price = floor($rule['amount_pay']*$selling_discount*$num)/100;
		$order->buy_amount = floor($rule['amount']*$num*100)/100;
		$order->buy_score = CashHistory::getPoint($order->buy_amount);
		$order->status = self::STATUS_NEW;
		$order->pay_status = self::PAY_STATUS_NO;
		$order->pay_time = '';
        $order->recharge_member_id = $rechargeMemberId;
		$order->create_time = time();
		if($order->save()){
			$orderId = Yii::app()->db->getLastInsertID();
			$rs['success'] = true;
			$rs['data'] = $order->getAttributes();
			$rs['data']['id'] = $orderId;
		}else{
			$rs['success'] = false;
		}
	
		return $rs;
	}
	
	
	/**
	 * 用户购买官方积分创建订单
	 * @param $member
	 * @param $goods_id
	 */
	const ORDER_TYPE_OFFICAL = 6;
	public static function _createOfficalOrder($member,$rule,$num,$type = self::TYPE_OFFICAL,$rechargeMemberId ="" ){
		$order = new GuadanJifenOrder();
		$order->code = Order::_createCode(self::ORDER_TYPE_OFFICAL);
		$order->type = $type;
		$order->member_id = $member;
		$order->partner_member_id = '';
		$order->partner_id ='';
		$order->goods_id = '';
		$order->rule_id = $rule['rule_id'];
		$order->quantity = $num;
		$order->unit_price = $rule['amount_pay'];
		$order->total_price = floor($rule['amount_pay']*$num*100)/100;
		$order->buy_amount = floor($rule['amount']*$num*100)/100;
		$order->buy_score = CashHistory::getPoint($order->buy_amount);
		$order->status = self::STATUS_NEW;
		$order->pay_status = self::PAY_STATUS_NO;
		$order->pay_time = '';
        $order->recharge_member_id = $rechargeMemberId;
		$order->create_time = time();
		if($order->save()){
			$orderId = Yii::app()->db->getLastInsertID();
			$rs['success'] = true;
			$rs['data'] = $order->getAttributes();
			$rs['data']['id'] = $orderId;
		}else{
			$rs['success'] = false;
		}
	
		return $rs;
	}
	
	static function getByCode($code){
		if (empty($code)) {
			return false;
		}
	
		$cri = new CDbCriteria();
		$cri->compare('code', $code);
	
		return self::model()->find($cri);
	
	}
	
	/**
	 * 订单支付成功
	 *
	 * $goods  goods的数组
	 *
	 */
	static  function paySuccess($code,$trans = true){
	
		$rs = array('result'=>false,'msg'=>'系统错误');
		$order = self::getByCode($code);
		if (empty($order)) {
			$rs['msg'] = '订单不存在';
			return $rs;
		}
		$type = CommonAccount::TYPE_GUADAN_SALE_UNBUNDLING;
		// 执行事务
		if ($trans==true)  $transaction = Yii::app()->db->beginTransaction();
		try {
			//支付逻辑
			$order->status = self::STATUS_PAY;
			$order->pay_status = self::PAY_STATUS_YES;
			$savers = $order->save();

			$total_amount = $order['buy_amount']*$order['quantity'];
			
			if ($order['type']==self::TYPE_PARTNER || $order['type'] == self::TYPE_AIR_RECHARGE_PARTNER ) {

				//获取政策规则
				$rule = Yii::app()->db->createCommand()
				->from(GuadanJifenGoods::model()->tableName().' as t')
				->select('r.*,t.member_id')
				->leftJoin(GuadanRule::model()->tableName().' as r', 't.rule_id=r.id')
				->where('t.id=:t_id',array(':t_id'=>$order['goods_id']))
				->queryRow();
                //判断金额余额
                if($rule['amount_limit']!=0 && $rule['amount_limit'] <= $rule['sale_amount']){
                    $sql = "update {{guadan_rule}} set status=".GuadanRule::STAUS_FINISHED." where id ={$rule['id']}";
                    Yii::app()->db->createCommand($sql)->execute();
                    $cache_key = 'getListByMemberId_'.$rule['member_id'];
                    Tool::cache(GuadanJifenGoods::CACHE_DIR_RULES)->set($cache_key,null);
                    
                    $rs['msg'] = '积分包限额已用完';
                    return $rs;
//                     throw new Exception("积分包限额已用完");
                }
                
                $total_amount = $rule['amount']*$order['quantity'];

                if ($rule['amount_limit']!=0 && ($rule['amount_limit']-$rule['sale_amount'])<$total_amount) {
                    $rs['msg'] = '可售余额不足';
                    return $rs;
                }
				//插入定期返还库
				self::_insertReturnRecord($order['id'], $rule,$order['quantity']);
				
				

                //更新积分规则余额
                $sql = "update {{guadan_rule}} set sale_amount=sale_amount+{$total_amount} where id ={$rule['id']}";
//                Yii::app()->db->createCommand()->update(GuadanRule::model()->tableName(), array('sale_amount'=>'+'.$total_amount),'id=:id',array(':id'=>$rule['id']));
                Yii::app()->db->createCommand($sql)->execute();


				//记录售卖政策售出金额
//                 $sql = "update {{guadan_collect}} set sale_amount_bind = sale_amount_bind+{$total_amount} where id ={$rule['collect_id']}";
//                 Yii::app()->db->createCommand($sql)->execute();
                //获取政策规则
                $rule = Yii::app()->db->createCommand()
                    ->from(GuadanJifenGoods::model()->tableName().' as t')
                    ->select('r.*,t.member_id')
                    ->leftJoin(GuadanRule::model()->tableName().' as r', 't.rule_id=r.id')
                    ->where('t.id=:t_id',array(':t_id'=>$order['goods_id']))
                    ->queryRow();
                //判断金额余额
                if($rule['amount_limit']!=0 && (($rule['amount_limit'] <= $rule['sale_amount']) || (($rule['amount_limit']-$rule['sale_amount']) < $rule['amount']) )){
                    $sql = "update {{guadan_rule}} set status=".GuadanRule::STAUS_FINISHED." where id ={$rule['id']}";
                    Yii::app()->db->createCommand($sql)->execute();

                }

                //更新规则缓存
                GuadanJifenGoods::getListByMemberId($rule['member_id'],false);

                //记录流水 账户扣钱
                if(AccountBalance::guadanBuyPointPay($order,$rule)){
                    if ($trans==true)  $transaction->commit();
                    $rs['result'] = true;
                    $rs['msg'] = '';
                    return $rs;
                }else{
                    if ($trans==true)  $transaction->rollBack();
                    return $rs;
                }
			}elseif ($order['type']==self::TYPE_OFFICAL || $order['type']==self::TYPE_AIR_RECHARGE_OFFICAL){
				//获取政策规则
				$rule = Yii::app()->db->createCommand()
				->from(GuadanRule::model()->tableName().' as t')
				->select('t.*')
				->where('t.id=:t_id AND t.status=:status',array(':t_id'=>$order['rule_id'],':status'=>GuadanRule::STATUS_ENABLE))
				->queryRow();
                //判断金额余额

                if($rule['amount_limit']!=0 && $rule['amount_limit'] <= $rule['sale_amount']){
                    $sql = "update {{guadan_rule}} set status=".GuadanRule::STAUS_FINISHED." where id ={$rule['id']}";
                    Yii::app()->db->createCommand($sql)->execute();
                    $cache_key = 'getOfficleGoods';
                    Tool::cache(GuadanJifenGoods::CACHE_DIR_RULES)->set($cache_key,null);
//                     throw new Exception("积分包限额已用完");
                    $rs['msg'] = '积分包限额已用完';
                    return $rs;
                }
                if ($rule['amount_limit']!=0 && ($rule['amount_limit']-$rule['sale_amount'])<$total_amount) {
//                     throw new Exception('可售余额不足');
                    $rs['msg'] = '可售余额不足';
                    return $rs;
                }

				//插入定期返还库
				self::_insertReturnRecord($order['id'], $rule,$order['quantity']);

                //更新积分规则余额
                $sql = "update {{guadan_rule}} set sale_amount=sale_amount+{$total_amount} where id ={$rule['id']}";
//                Yii::app()->db->createCommand()->update(GuadanRule::model()->tableName(), array('sale_amount'=>'+'.$total_amount),'id=:id',array(':id'=>$rule['id']));
                Yii::app()->db->createCommand($sql)->execute();

                //更新关系库
                $rel_rs = Yii::app()->db->createCommand()
                ->from(GuadanRelation::model()->tableName())
                ->where(" collect_id ={$rule['collect_id']} AND type=".GuadanRelation::TYPE_UNBIND .' AND amount_remain>='.$total_amount)
                ->order('guadan_id asc')
                ->queryRow();
                
                if (!empty($rel_rs)) {
                	$sql_rel = "update ".GuadanRelation::model()->tableName()." set amount_remain=amount_remain-{$total_amount} where collect_id ={$rel_rs['collect_id']} AND guadan_id={$rel_rs['guadan_id']} AND type=".GuadanRelation::TYPE_UNBIND;
                	Yii::app()->db->createCommand($sql_rel)->execute();
                }else{
                     $rel_rs = Yii::app()->db->createCommand()
                        ->from(GuadanRelation::model()->tableName())
                        ->where(" collect_id ={$rule['collect_id']} AND type=".GuadanRelation::TYPE_TOBIND .' AND amount_remain>='.$total_amount)
                        ->order('guadan_id asc')
                        ->queryRow();
                     if (!empty($rel_rs)) {
                	$sql_rel = "update ".GuadanRelation::model()->tableName()." set amount_remain=amount_remain-{$total_amount} where collect_id ={$rel_rs['collect_id']} AND guadan_id={$rel_rs['guadan_id']} AND type=".GuadanRelation::TYPE_TOBIND;
                	Yii::app()->db->createCommand($sql_rel)->execute();
                     }
                }
                
				
                //记录售卖政策售出金额
                    //查询售卖情况
                $collect = Yii::app()->db->createCommand()
                        ->from(GuadanCollect::model()->tableName())
                        ->select('amount_bind, amount_unbind, sale_amount_bind, sale_amount_unbind')
                        ->where('id='.$rule['collect_id'])
                        ->queryRow();
                if($collect['amount_unbind'] < ($collect['sale_amount_unbind'] + $total_amount) && $collect['amount_bind'] >= ($collect['sale_amount_bind'] +$total_amount)){
                    $sql = "update {{guadan_collect}} set sale_amount_bind = sale_amount_bind+{$total_amount} where id ={$rule['collect_id']}";
                    $type = CommonAccount::TYPE_GUADAN_SALE_BINDING;
                }else{
                    $sql = "update {{guadan_collect}} set sale_amount_unbind = sale_amount_unbind+{$total_amount} where id ={$rule['collect_id']}";
                    $type = CommonAccount::TYPE_GUADAN_SALE_UNBUNDLING;
                }
                Yii::app()->db->createCommand($sql)->execute();
                //获取政策规则
                $rule = Yii::app()->db->createCommand()
                    ->from(GuadanRule::model()->tableName().' as t')
                    ->select('t.*')
                    ->where('t.id=:t_id AND t.status=:status',array(':t_id'=>$order['rule_id'],':status'=>GuadanRule::STATUS_ENABLE))
                    ->queryRow();
                //判断金额余额
                if($rule['amount_limit']!=0 && (($rule['amount_limit'] <= $rule['sale_amount']) || (($rule['amount_limit']-$rule['sale_amount']) < $rule['amount']) )){
                    $sql = "update {{guadan_rule}} set status=".GuadanRule::STAUS_FINISHED." where id ={$rule['id']}";
                    Yii::app()->db->createCommand($sql)->execute();

                }
                
                //更新规则缓存
                GuadanJifenGoods::getOfficleGoods(false);
				
				//记录流水 账户扣钱

				if(AccountBalance::guadanBuyOfficalPointPay($order,$rule,$type)){
                    if ($trans==true)  $transaction->commit();
//                     return true;
                    $rs['result'] = true;
                    $rs['msg'] = '';
                    return $rs;
                }else{
                    if ($trans==true)  $transaction->rollBack();
//                     return false;
                    return $rs;
                }
			}
			
			
	
		}catch (Exception $e) {
			if ($trans==true)  $transaction->rollBack();
// 			$flag = false;
// 			throw new Exception($e->getMessage());
			$rs['msg'] = $e->getMessage();
			return $rs;
		}
	
		if ($trans==true)  $transaction->commit();
		$rs['result'] = true;
        $rs['msg'] = '';
        return $rs;
	}
	
	
	/**
	 * 定期返还入库
	 * 
	 */
	static function _insertReturnRecord($order_id,$rule,$quantity){
		//存入定期库 本金
		$in_amout = floor($rule['amount']/$rule['amount_installment']*$quantity*100)/100;
		
		for ($m=1;$m<$rule['amount_installment'];$m++){
			$rc = new GuadanJifenOrderDetail();
			$rc->order_id = $order_id;
			$rc->to_score = CashHistory::getPoint($in_amout);
			$rc->to_amount = $in_amout;
			$rc->to_time = strtotime('+'.($m*$rule['installment_time']).' day');
			$rc->status = GuadanJifenOrderDetail::STATUS_NEW;
			$rc->save();
		}
		
		//存入定期库 赠送金额
		$in_amout_give= floor($rule['amount_give']/$rule['give_installment']*$quantity*100)/100;
		
		for ($m=1;$m<$rule['give_installment'];$m++){
			$rc = new GuadanJifenOrderDetail();
			$rc->order_id = $order_id;
			$rc->to_score = CashHistory::getPoint($in_amout_give);
			$rc->to_amount = $in_amout_give;
			$rc->to_time = strtotime('+'.($m*$rule['installment_time']).' day');
			$rc->status = GuadanJifenOrderDetail::STATUS_NEW;
			$rc->save();
		}
		
		return true;
		
	}
	
	/*
	 * 查询已有订单数
	 */
	static function getOrderNums($member_id){
		$rs = Yii::app()->db->createCommand()
				->from(self::model()->tableName().' as t')
				->select('count(1) as c')
				->where('t.member_id=:member_id AND t.status=:status',array(':member_id'=>$member_id,':status'=>self::STATUS_PAY))
				->queryRow();
		
		return $rs['c'];
		
	}
	
}
