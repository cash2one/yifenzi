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
class Address extends CActiveRecord
{
    //默认地址
    const DEFAULT_IS = 1;
    const DEFAULT_NO = 0;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{address}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('real_name','required','message'=>'收货人姓名必填！','on'=>'create'),
			array('mobile','required','message'=>'手机号码必填！','on'=>'create'),
			array('street','required','message'=>'详细地址必填！','on'=>'create'),
			array('province_id','required','message'=>'请选择省份！','on'=>'create'),
			//array('mobile','match','pattern'=>'/^1[34578]\d{9}$/','message'=>'必须为1开头的11位纯数字'),
			array('mobile','comext.validators.isMobile','on'=>'create','errMsg'=>'手机号码不合法'),//检测手机号码的合法性
			array('city_id','required','message'=>'请选择城市！','on'=>'create'),
			array('district_id','required','message'=>'请选择区/县！','on'=>'create'),
			array('default', 'numerical', 'integerOnly'=>true),
			array('member_id, province_id, city_id, district_id', 'length', 'max'=>11),
			array('real_name', 'length', 'max'=>56),
			array('mobile, zip_code', 'length', 'max'=>16),
			array('street', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('member_id, real_name, mobile, province_id, city_id, district_id, street', 'required'),
			array('id, member_id, real_name, mobile, province_id, city_id, district_id, street, zip_code, default', 'safe', 'on'=>'search'),
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
			'member' => array(self::BELONGS_TO, 'GwMember', 'member_id'),
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
			'default' => 'Default',
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
		$criteria->compare('default',$this->default);

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
	
	public function beforeSave()
	{
	    if(parent::beforeSave())
	    {
	        $data = self::model()->findByAttributes(array('member_id'=>$this->member_id,'default'=>self::DEFAULT_IS));
	        if(empty($data))
	        {
	            $this->default = self::DEFAULT_IS;
	        }
	        return true;
	    }
	    else 
	        return false;
	}
}
