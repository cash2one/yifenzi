<?php

/**
 * This is the model class for table "{{guadan_jifen_order_detail}}".
 *
 * The followings are the available columns in table '{{guadan_jifen_order_detail}}':
 * @property string $id
 * @property string $order_id
 * @property string $to_score
 * @property string $to_amount
 * @property string $to_time
 * @property integer $status
 */
class GuadanJifenOrderDetail extends CActiveRecord
{
	
	//订单状态
	const STATUS_NEW = 0;   //未到账
	const STATUS_FINISH = 1;   //已到账
	public static function getStatus($k = null) {
		$arr = array(
				self::STATUS_NEW => Yii::t('order', '未到账'),
				self::STATUS_FINISH=> Yii::t('order','已到账'),
		);
		return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{guadan_jifen_order_detail}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, to_score, to_amount, to_time, status', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('order_id, to_time', 'length', 'max'=>11),
			array('to_score, to_amount', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, to_score, to_amount, to_time, status', 'safe', 'on'=>'search'),
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
			'order_id' => '用户购买积分订单表-id',
			'to_score' => '到账积分',
			'to_amount' => '到账金额',
			'to_time' => '到账时间',
			'status' => '状态（0未到账 1到账）',
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
		$criteria->compare('order_id',$this->order_id,true);
		$criteria->compare('to_score',$this->to_score,true);
		$criteria->compare('to_amount',$this->to_amount,true);
		$criteria->compare('to_time',$this->to_time,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GuadanJifenOrderDetail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
