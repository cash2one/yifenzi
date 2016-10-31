<?php

/**
 * This is the model class for table "{{member_personal_authentication}}".
 *
 * The followings are the available columns in table '{{member_personal_authentication}}':
 * @property integer $id
 * @property integer $member_id
 * @property string $real_name
 * @property string $identification
 * @property string $bank_card_number
 * @property integer $status
 */
class MemberPersonalAuthentication extends CActiveRecord
{

	const STATUS_NO = 0;//审核中
	const STATUS_PASS = 1;//审核通过
	const STATUS_NOT_PASS = 2;//审核不通过
	
	//自动认证状态
	const AUTO_STATUS_PASS = 1;//自动认证通过
	const AUTO_STATUS_NOT_PASS = 0;//自动认证未通过

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{member_personal_authentication}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, status', 'numerical', 'integerOnly'=>true),
			array('real_name', 'length', 'max'=>50),
			array('identification', 'length', 'max'=>18),
			array('bank_card_number', 'length', 'max'=>30),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, real_name, identification, bank_card_number, status,auto_status,mobile,bank_name', 'safe', 'on'=>'search'),
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
                                          'member' => array(self::BELONGS_TO, 'Member', 'member_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '主键自增',
			'member_id' => '会员ID',
			'real_name' => '真实姓名',
			'identification' => '身份证号',
			'bank_card_number' => '银行卡号',
			'status' => '审核状态',
			'auto_status' => '自动认证状态',
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
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('real_name',$this->real_name,true);
		$criteria->compare('identification',$this->identification,true);
		$criteria->compare('bank_card_number',$this->bank_card_number,true);
		$criteria->compare('status',$this->status);
		$criteria->order = 'id DESC';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MemberPersonalAuthentication the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * 审核状态
	 * @param null $k
	 * @return array|null
	 */
	public static function status($k = null) {
		$arr = array(
			self::STATUS_NO => Yii::t('site', '审核中'),
			self::STATUS_PASS=> Yii::t('site','审核通过'),
			self::STATUS_NOT_PASS => Yii::t('site','审核不通过'),
		);
		return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
	}
	
    /**
     * 审核状态
     * @param null $k
     * @return array|null
     */
    public static function autoStatus($k = null) {
    	$arr = array(
    			self::AUTO_STATUS_PASS=> Yii::t('site','自动认证通过'),
    			self::AUTO_STATUS_NOT_PASS => Yii::t('site','未通过自动认证'),
    	);
    	return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }
	
}
