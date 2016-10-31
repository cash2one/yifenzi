<?php

/**
 * This is the model class for table "{{checkcode}}".
 *
 * The followings are the available columns in table '{{checkcode}}':
 * @property string $phone
 * @property string $checkcode
 * @property string $create_time
 */
class Checkcode extends CActiveRecord
{       public $log_source_id;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{checkcode}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('phone, create_time', 'required'),
			array('phone', 'length', 'max'=>20),
			array('checkcode', 'length', 'max'=>6),
			array('create_time', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('phone, checkcode, create_time', 'safe', 'on'=>'search'),
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
			'phone' => '手机号',
			'checkcode' => '验证码',
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

		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('checkcode',$this->checkcode,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
    /**
     * 查询手机号码的验证码
     * @param string $phone 手机号码
     */
    public static function searchCheckCode($phone){
        return Yii::app()->db->createCommand()
            	->select('checkcode')
            	->from(self::model()->tableName())
            	->where('phone = :phone', array(':phone' => $phone))
            	->queryScalar();
    }
        
        
        
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Checkcode the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
