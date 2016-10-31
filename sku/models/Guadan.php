<?php

/**
 * This is the model class for table "{{guadan}}".
 *
 * The followings are the available columns in table '{{guadan}}':
 * @property string $id
 * @property integer $member_id
 * @property string $gai_number
 * @property integer $type
 * @property string $amount
 * @property string $amount_remain
 * @property integer $discount
 * @property integer $create_time
 */
class Guadan extends CActiveRecord
{
	const GAI_NUMBER_UNBIND = 'GW90000006'; //非绑定积分的gw号
	const TYPE_TO_BIND = 1;				//待绑定积分
	const TYPE_NO_BIND = 2;				//非绑定积分
	
	public static function getType($id = null) {
		$arr = array(
				self::TYPE_TO_BIND => Yii::t('goods', '待绑定'),
				self::TYPE_NO_BIND => Yii::t('goods', '非绑定'),
		);
		if (is_numeric($id)) {
			return isset($arr[$id]) ? $arr[$id] : null;
		} else {
			return $arr;
		}
	}
	
	const STATUS_ENABLE = 1;				//正常状态
	const STATUS_FROZEN = 2;				//冻结状态
	const STATUS_DISABLE = 3;				//失效状态
	
	public static function getStatus($id = null) {
		$arr = array(
				self::STATUS_ENABLE => Yii::t('goods', '正常'),
				self::STATUS_FROZEN => Yii::t('goods', '冻结'),
				self::STATUS_DISABLE => Yii::t('goods', '失效'),
		);
		if (is_numeric($id)) {
			return isset($arr[$id]) ? $arr[$id] : null;
		} else {
			return $arr;
		}
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{guadan}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id', 'required'),
			array('member_id, type, discount, status, create_time', 'numerical', 'integerOnly'=>true),
			array('gai_number', 'length', 'max'=>18),
			array('amount, amount_remain', 'length', 'max'=>18),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, gai_number, type, amount, amount_remain, discount, status, create_time', 'safe', 'on'=>'search'),
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
			'code'=>'编号',
			'member_id' => 'Member',
			'gai_number' => '盖网号',
			'type' => '积分类型', // 1为待绑定积分  2为非绑定积分
			'amount' => '挂单金额',
			'amount_remain' => '剩余余额',
			'discount' => '百分比折扣',
			'status' => '状态',
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
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('gai_number',$this->gai_number,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('amount_remain',$this->amount_remain,true);
		$criteria->compare('discount',$this->discount);
		$criteria->compare('status','<>'.self::STATUS_DISABLE);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array('defaultOrder'=>'id DESC' )
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Guadan the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * 计算 积分总额
	 *
	 * @param int $type 积分类型
	 * @param array $ids 挂单id
     * @param  string $field 计算的字段
	 * @return mixed
	 */
	public static function getAmount($type,$ids = array(),$field='amount_remain'){
		if(!empty($ids)){
			$amount =  Yii::app()->db->createCommand()
				->select('sum('.$field.')')->from('{{guadan}}')
				->where(array('in','id',$ids))
				->andWhere('type='.$type.' and status='.self::STATUS_ENABLE)->queryScalar();
		}else{
			$amount =  Yii::app()->db->createCommand()
				->select('sum('.$field.')')->from('{{guadan}}')
				->where('type='.$type.' and status='.self::STATUS_ENABLE)->queryScalar();
		}
		return $amount ? $amount : '0.00';
	}

	/**
	 * 根据id,获取挂单
	 * @param array $ids
	 * @param string $select
	 * @return array
	 */
	public static function getGuadanByIds(array $ids,$select='*'){
		if(empty($ids)){
			$data = Yii::app()->db->createCommand()->select($select)->from('{{guadan}}')
				->where("status=".self::STATUS_ENABLE)->queryAll();
		}else{
			$data = Yii::app()->db->createCommand()->select($select)->from('{{guadan}}')
				->where(array('in','id',$ids))
				->andWhere("status=".self::STATUS_ENABLE)->queryAll();
		}
		return $data;
	}
}
