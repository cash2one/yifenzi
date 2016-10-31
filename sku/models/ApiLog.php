<?php

/**
 * This is the model class for table "gw_api_log".
 *
 * The followings are the available columns in table 'gw_api_log':
 * @property string $id
 * @property string $code_id
 * @property string $code
 * @property string $operate_type
 * @property string $transaction_type
 * @property string $member_id
 * @property string $gai_number
 * @property string $money
 * @property string $freight
 * @property string $remark
 * @property integer $is_callback
 * @property string $callback
 * @property integer $callback_response
 * @property integer $callback_count
 * @property string $data
 * @property integer $status
 * @property string $create_time
 */
class ApiLog extends CActiveRecord
{
	const STATUS_WRITED = 1;   //代表流水已经插入|0代表未插入

	const IS_CALLBACK_YES = 1;  //需要执行回调
	const IS_CALLBACK_NO = 0;   //不需要执行回调

	const CALLBACK_RESPONSE_YES = 1;    //已经得到回调响应
	const CALLBACK_RESPONSE_NO = 0;		//未得到回调响应
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gw_api_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code_id, code, operate_type, transaction_type, member_id, gai_number, money, freight, remark, is_callback, callback, callback_response, callback_count, data, status, create_time', 'required'),
			array('is_callback, callback_response, callback_count, status', 'numerical', 'integerOnly'=>true),
			array('code_id, member_id, create_time', 'length', 'max'=>11),
			array('code', 'length', 'max'=>64),
			array('operate_type, transaction_type', 'length', 'max'=>4),
			array('gai_number', 'length', 'max'=>32),
			array('money, freight', 'length', 'max'=>18),
			array('callback', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, code_id, code, operate_type, transaction_type, member_id, gai_number, money, freight, remark, is_callback, callback, callback_response, callback_count, data, status, create_time', 'safe', 'on'=>'search'),
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
			'code_id' => '订单id',
			'code' => '订单号',
			'operate_type' => '订单操作类型',
			'transaction_type' => '事务类型',
			'member_id' => '会员id',
			'gai_number' => 'GW号',
			'money' => '订单的金额（包含运费）',
			'freight' => '运费',
			'remark' => '备注',
			'is_callback' => '是否需要回调，0否、1是',
			'callback' => '回调地址',
			'callback_response' => '0未响应 、1响应',
			'callback_count' => '回调次数',
			'data' => '访问的参数记录',
			'status' => '状态（0.金额已经变动 1流水已经写入）',
			'create_time' => '创建时间',
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
		$criteria->compare('code_id',$this->code_id,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('operate_type',$this->operate_type,true);
		$criteria->compare('transaction_type',$this->transaction_type,true);
		$criteria->compare('member_id',$this->member_id,true);
		$criteria->compare('gai_number',$this->gai_number,true);
		$criteria->compare('money',$this->money,true);
		$criteria->compare('freight',$this->freight,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('is_callback',$this->is_callback);
		$criteria->compare('callback',$this->callback,true);
		$criteria->compare('callback_response',$this->callback_response);
		$criteria->compare('callback_count',$this->callback_count);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()->ac;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ApiLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
