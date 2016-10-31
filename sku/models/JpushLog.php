<?php

/**
 * This is the model class for table "{{jpush_log}}".
 *
 * The followings are the available columns in table '{{jpush_log}}':
 * @property string $id
 * @property string $order_code
 * @property string $send_data
 * @property string $get_data
 * @property integer $create_time
 */
class JpushLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{jpush_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_code', 'required'),
			array('create_time', 'numerical', 'integerOnly'=>true),
			array('order_code', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_code, send_data, get_data, create_time', 'safe', 'on'=>'search'),
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
			'order_code' => 'Order Code',
			'send_data' => 'Send Data',
			'get_data' => 'Get Data',
			'create_time' => 'Create Time',
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
		$criteria->compare('order_code',$this->order_code,true);
		$criteria->compare('send_data',$this->send_data,true);
		$criteria->compare('get_data',$this->get_data,true);
		$criteria->compare('create_time',$this->create_time);
		$criteria->order = 'id desc';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return JpushLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	static  function createLog($code,$send_data='',$get_data=''){
		$log = new self();
		
		$log->order_code = $code;
		$log->send_data = $send_data;
		$log->get_data = $get_data;
		$log->create_time = time();
		$log->save();
		return Yii::app()->db->getLastInsertID();
		
	}
	
	static  function updateGetData($id,$get_data,$send_data=''){
		$update_arr = array();
		if (!empty($send_data)) {
			$update_arr['send_data'] = $send_data;
		}
		if (!empty($get_data)) {
			$update_arr['get_data'] = $get_data;
		}
		$rs = Yii::app()->db->createCommand()->update(self::model()->tableName(),$update_arr,'id='.$id);
		return $rs;
	}
	
}
