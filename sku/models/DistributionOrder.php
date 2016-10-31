<?php

/**
 * This is the model class for table "{{distribution_order}}".
 *
 * The followings are the available columns in table '{{distribution_order}}':
 * @property integer $id
 * @property integer $order_id
 * @property integer $status
 * @property integer $create_time
 * @property string $order_code
 * @property integer $distribution_id
 */
class DistributionOrder extends CActiveRecord
{
	//配送订单状态
	const STATUS_OK = 1;                //已完成
	const STATUS_WAITING_SEND = 2;      //待送达
	const STATUS_PICK_UP = 3;           //待取货
	const STATUS_NEW = 0; 				//新订单
	const STATUS_CANCEL = -1;           //已取消
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{distribution_order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, order_id, status, create_time, order_code, distribution_id', 'required'),
			array('id, order_id, status, create_time, distribution_id', 'numerical', 'integerOnly'=>true),
			array('order_code', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, status, create_time, order_code, distribution_id', 'safe', 'on'=>'search'),
            array('order_code', 'required','on' => 'getMobile'),
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
                                        'orders' => array(self::BELONGS_TO, 'Order', 'order_id'),
                                         'distribution' => array(self::BELONGS_TO, 'Distribution', 'distribution_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'order_id' => 'Order',
			'status' => 'Status',
			'create_time' => 'Create Time',
			'order_code' => 'Order Code',
			'distribution_id' => 'Distribution',
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
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('order_code',$this->order_code,true);
		$criteria->compare('distribution_id',$this->distribution_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DistributionOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}



    /*
     * 查询配送员信息
     * @param $order_code 订单编号
     */

    public function getDistributionByCode($order_code)
    {

        if(empty($order_code)) return false;

        $sql = 'SELECT d.member_id,d.mobile FROM {{distribution_order}} as t LEFT JOIN {{distribution}} as d ON t.distribution_id = d.id WHERE t.order_code = '.$order_code;

        $result = Yii::app()->db->createCommand($sql)->queryRow();

        return $result;
    }

	/**
	 * 抢单功能处理
	 * @param int orderCode 订单编号
	 * @param int orderId 订单id
	 * @param int memberOrderId 配送员的id
	 * @param int store_id 商户的id
	 * @author yuanmei.chen
	 */
	public function grabOrderHandle($orderCode,$orderId,$memberOrderId,$store_id,$trans=true)
	{
		try{
			$return = array();
			if ($trans) {
				$transaction = Yii::app()->db->beginTransaction();
			}
			if(empty($store_id))
			{
				throw new Exception('商户的id缺少!');
			}
			//查询该订单是否已经被抢了
			$grabSql = 'SELECT id,order_id FROM {{distribution_order}} WHERE order_id ='.intval($orderId).' AND order_code = '.$orderCode;
			$isGrab = Yii::app()->db->createCommand($grabSql)->limit(1)->queryRow();
			if(!empty($isGrab))
				throw new Exception('此订单已经被抢!');

			//检测当前配送员是否90分钟内已经有4个订单未配送 未完成
			$sqlCheck = 'SELECT COUNT(t.id) as orderNum FROM {{distribution_order}} as t LEFT JOIN {{distribution}} as d ON t.distribution_id = d.id WHERE t.distribution_id = '.intval($memberOrderId).' AND t.`status` IN(0,2,3) AND unix_timestamp(current_timestamp) < (SELECT (tmp.create_time+5400) as tmp_time FROM {{distribution_order}} as tmp WHERE tmp.`status` NOT IN(-1,1) ORDER BY tmp.create_time ASC LIMIT 1) HAVING orderNum >= 4';
			$hadFull = Yii::app()->db->createCommand($sqlCheck)->queryAll();

			if(count($hadFull) > 0)
				throw new Exception('90分钟内同时进行的订单最多为4单，请先完成配送任务再进行抢单操作!');

			//检测商户是否开启自动接单
			$store_id = intval($store_id);
			$sql = 'SELECT id,is_automatic_order FROM {{supermarkets}} WHERE id ='.intval($store_id);
			$is_automatic_order = Yii::app()->db->createCommand($sql)->limit(1)->queryRow();
			if(empty($is_automatic_order))
			{
				throw new Exception('此订单找不到对应的商户信息!');
			}

			//插入新的配送订单
			$data = array(
				'order_id'        => $orderId,
				'status'          => empty($is_automatic_order['is_automatic_order']) ? self::STATUS_NEW : self::STATUS_PICK_UP,
				'create_time'     => time(),
				'order_code'      => $orderCode,
				'distribution_id' => $memberOrderId
			);
			$result = Yii::app()->db->createCommand()->insert('{{distribution_order}}',$data);
			if(!$result)
				throw new Exception('生成配送订单失败!');

			//更改订单状态
			if($is_automatic_order['is_automatic_order'] > 0)
			{
				$rs = Order::model()->updateByPk(intval($orderId),array('status'=> Order::STATUS_AUTO_ORDER_TAKENRS));
				if($rs < 0)
					throw new Exception('修改订单状态失败!');
			}

			$return['result'] = true;

			$transaction->commit();

		}catch (Exception $e){
			$transaction->rollback();
			$return['result'] = false;
			$return['msg'] = $e->getMessage();

		}

		return $return;
	}
}
