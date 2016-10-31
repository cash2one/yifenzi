<?php

/**
 * This is the model class for table "{{guadan_relation}}".
 *
 * The followings are the available columns in table '{{guadan_relation}}':
 * @property string $collect_id
 * @property string $guadan_id
 * @property string $amount
 * @property string $amount_remain
 * @property integer $type
 */
class GuadanRelation extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{guadan_relation}}';
	}
	
	const TYPE_TOBIND = 1;
	const TYPE_UNBIND = 2;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('collect_id, guadan_id, type', 'required'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('collect_id, guadan_id', 'length', 'max'=>10),
			array('amount, amount_remain', 'length', 'max'=>18),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('collect_id, guadan_id, amount, amount_remain, type', 'safe', 'on'=>'search'),
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
			'collect_id' => '挂单提取id',
			'guadan_id' => '积分挂单id',
			'amount' => '积分',
			'amount_remain' => '剩余积分',
			'type' => '积分类型（1.待绑定积分 2.非绑定积分）',
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

		$criteria->compare('collect_id',$this->collect_id,true);
		$criteria->compare('guadan_id',$this->guadan_id,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('amount_remain',$this->amount_remain,true);
		$criteria->compare('type',$this->type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GuadanRelation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
