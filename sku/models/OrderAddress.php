<?php

/**
 * This is the model class for table "{{address}}".
 *
 * The followings are the available columns in table '{{address}}':
 * @property string $id
 * @property string $member_id
 * @property string $real_name
 * @property string $mobile
 * @property string $province_id
 * @property string $city_id
 * @property string $district_id
 * @property string $street
 * @property string $zip_code
 * @property integer $default
 *
 * The followings are the available model relations:
 * @property GwMember $member
 */
class OrderAddress extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{order_address}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, real_name, mobile, province_id, city_id, district_id, street', 'required'),
			array('member_id, province_id, city_id, district_id', 'length', 'max'=>11),
			array('real_name', 'length', 'max'=>56),
			array('mobile, zip_code', 'length', 'max'=>16),
			array('street', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, real_name, mobile, province_id, city_id, district_id, street, zip_code', 'safe', 'on'=>'search'),
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
// 			'order' => array(self::BELONGS_TO, 'Order', 'member_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'member_id' => 'Member',
			'real_name' => 'Real Name',
			'mobile' => 'Mobile',
			'province_id' => 'Province',
			'city_id' => 'City',
			'district_id' => 'District',
			'street' => 'Street',
			'zip_code' => 'Zip Code',
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
		$criteria->compare('member_id',$this->member_id,true);
		$criteria->compare('real_name',$this->real_name,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('province_id',$this->province_id,true);
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('district_id',$this->district_id,true);
		$criteria->compare('street',$this->street,true);
		$criteria->compare('zip_code',$this->zip_code,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Address the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
