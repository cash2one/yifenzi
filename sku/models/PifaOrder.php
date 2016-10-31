<?php

/**
 * This is the model class for table "{{pifa_order}}".
 *
 * The followings are the available columns in table '{{pifa_order}}':
 * @property integer $id
 * @property string $code
 * @property integer $member_id
 * @property string $amount
 * @property integer $status
 * @property integer $pay_status
 * @property integer $create_time
 * @property integer $pay_time
 */
class PifaOrder extends CActiveRecord
{
    //订单类型
    const TYPE_JIFENPIFA = 7; //商家积分批发订单
    //订单状态
    const STATUS_NEW = 1;   //新订单
    const STATUS_PAY = 2;   //交易完成
    //支付状态
    const IS_PAY_NO = 1 ;//未支付
    const IS_PAY_YES = 2;//已支付
    

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{guadan_pifa_order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('buy_score,amount', 'required'),
			array('id, member_id, status, pay_status, create_time, pay_time,collect_id', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>32),
			array('amount', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, code, member_id,amount,buy_score,buy_amount, status, pay_status, create_time, pay_time,collect_id', 'safe'),
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
			'amount' => '购买积分金额',
            'price'=>"实际支付金额",
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

		$criteria->compare('id',$this->id);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('amount',$this->amount,true);
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
	 * @return PifaOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * 商家积分批发创建订单
     * @param $member
     * @param $moeny
     */
    public static function _createOrder($member,$amount,$score,$collect_id=0){
        $order = new PifaOrder();
        $order->code = Order::_createCode(self::TYPE_JIFENPIFA);
        $order->member_id = $member;
        $order->amount = $amount;
        $order->buy_score = $score;
        $order->buy_amount = $score*CashHistory::MONEY_POINT_RADIO;
        $order->status = self::STATUS_NEW;
        $order->pay_status = self::IS_PAY_NO;
        $order->create_time = time();
        $order->collect_id = $collect_id;
        
        $rs = array();
        if($order->save()){
            $orderId = Yii::app()->db->getLastInsertID();
            $rs['success'] = true;
            $rs['data'] = $order->getAttributes();
            $rs['data']['id'] = $orderId;
        }else{
             $rs['success'] = false;
             Yii::log('创建积分批发订单失败'.var_export($order->getErrors(),true));
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
    static  function paySuccess($code,$type=CommonAccount::TYPE_GUADAN_SALE_BINDING,$trans = true){
    
    	$payrs = array('result'=>false,'msg'=>'系统错误');
    	
    	// 执行事务
    	if ($trans==true)  $transaction = Yii::app()->db->beginTransaction();
    	try {
    		$order = self::getByCode($code);
    			
    		if (empty($order)) {
    			$payrs['msg'] = '订单不存在';
				return $payrs;
    		}
    
    		//支付逻辑
    		$order->status = self::STATUS_PAY;
    		$order->pay_status = self::IS_PAY_YES;
    		$rs = $order->save();
    		
    		if ($order['collect_id']>0) {
                    //判断账户类型
                    if($type == CommonAccount::TYPE_GUADAN_SALE_BINDING){
    			//记录售卖政策售出金额
//    			Yii::app()->db->createCommand()->update(GuadanCollect::model()->tableName(), array('sale_amount_bind'=>'+'.$order['buy_amount']),'id=:id',array(':id'=>$order['collect_id']));
                        $sql = "update {{guadan_collect}} set sale_amount_bind = sale_amount_bind+{$order['buy_amount']} where id ={$order['collect_id']}";
                         Yii::app()->db->createCommand($sql)->execute();
                        //更新关系库
    			$rel_rs = Yii::app()->db->createCommand()
    			->from(GuadanRelation::model()->tableName())
    			->where(" collect_id ={$order['collect_id']} AND type=".GuadanRelation::TYPE_TOBIND .' AND amount_remain>='.$order['buy_amount'])
    			->order('guadan_id asc')
    			->queryRow();
    			
    			if (!empty($rel_rs)) {
    				$sql_rel = "update ".GuadanRelation::model()->tableName()." set amount_remain=amount_remain-{$order['buy_amount']} where collect_id ={$rel_rs['collect_id']} AND guadan_id={$rel_rs['guadan_id']} AND type=".GuadanRelation::TYPE_TOBIND;
    				Yii::app()->db->createCommand($sql_rel)->execute();
    			}
                    }else{
//                        Yii::app()->db->createCommand()->update(GuadanCollect::model()->tableName(), array('sale_amount_unbind'=>'+'.$order['buy_amount']),'id=:id',array(':id'=>$order['collect_id']));
                         $sql = "update {{guadan_collect}} set sale_amount_unbind = sale_amount_unbind+{$order['buy_amount']} where id ={$order['collect_id']}";
                         Yii::app()->db->createCommand($sql)->execute();
                        //更新关系库
    			$rel_rs = Yii::app()->db->createCommand()
    			->from(GuadanRelation::model()->tableName())
    			->where(" collect_id ={$order['collect_id']} AND type=".GuadanRelation::TYPE_UNBIND .' AND amount_remain>='.$order['buy_amount'])
    			->order('guadan_id asc')
    			->queryRow();
    			
    			if (!empty($rel_rs)) {
    				$sql_rel = "update ".GuadanRelation::model()->tableName()." set amount_remain=amount_remain-{$order['buy_amount']} where collect_id ={$rel_rs['collect_id']} AND guadan_id={$rel_rs['guadan_id']} AND type=".GuadanRelation::TYPE_UNBIND;
    				Yii::app()->db->createCommand($sql_rel)->execute();
    			}
                    }
                    
    		}
    		
    		//记录流水 账户扣钱
    		AccountBalance::guadanPifaPay($order->buy_amount,$order->amount,$order['member_id'],$order,$type);
    			
    	}catch (Exception $e) {
    		if ($trans==true)  $transaction->rollBack();
    		$payrs['msg'] = $e->getMessage();
    		return $payrs;
    	}
    		
    	if ($trans==true)  $transaction->commit();
    	$payrs['result'] = true;
    	$payrs['msg'] = '';
		return $payrs;
    }
    
    
    
}
