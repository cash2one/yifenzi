<?php

/**
 * This is the model class for table "{{super_goods}}".
 *
 * The followings are the available columns in table '{{super_goods}}':
 * @property integer $id
 * @property string $goods_id
 * @property string $machine_id
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Goods $goods
 * @property Supermarkets $super
 */
class VendingMachineCellStore extends CActiveRecord
{
	
	public $name;
	
	const STATUS_ENABLE = 1;
	const STATUS_DISABLE = 2;
	const STATUS_DELETE = 3;
	
	const MAX_NUM = 72;
	
	/**
	 * 状态用文字标示
	 * @param null|int $status 查询出来的状态
	 * @return array|null
	 */
	
	public static function getStatus($status = null) {
		$arr = array(
				self::STATUS_ENABLE => Yii::t('goods', '上架'),
				self::STATUS_DISABLE => Yii::t('goods', '下架'),
				self::STATUS_DELETE => Yii::t('goods', '已删除'),
		);
		if (is_numeric($status)) {
			return isset($arr[$status]) ? $arr[$status] : Yii::t('goods', '未知状态');
		} else {
			return $arr;
		}
	}
	
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{vending_machine_cell_store}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('goods_id, machine_id,status,code', 'required'),
			array('goods_id, machine_id', 'length', 'max'=>11),
			array('status', 'length', 'max'=>2),
			array('code', 'length', 'max'=>6),
			array('name', 'length', 'max'=>128),
			array('id, goods_id, machine_id, create_time,status,code', 'safe', 'on'=>'search'),
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
			'goods' => array(self::BELONGS_TO, 'Goods', 'goods_id'),
// 			'super' => array(self::BELONGS_TO, 'Supermarkets', 'machine_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('goods', 'ID'),
			'goods_id' => Yii::t('goods', '关联商品'),
			'machine_id' =>Yii::t('goods', '所属售货机'),
			'create_time' => Yii::t('goods', '创建时间'),
            'cdoe'=>Yii::t('goods', '编码'),
            'status'=>Yii::t('goods', '是否上架')
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
		$criteria->with = array('goods');
		$criteria->compare('id',$this->id);
		$criteria->compare('goods_id',$this->goods_id);
		$criteria->compare('machine_id',$this->machine_id);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return VendingMachineGoods the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * 
	 * @return CActiveDataProvider
	 */
	public function superSearch()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
	
		$criteria=new CDbCriteria;
		$criteria->with = array('goods');
        if(!empty($this->id)) $criteria->compare('t.id',$this->id);
        if(!empty($this->goods_id)) $criteria->compare('t.goods_id',$this->goods_id);
        if(!empty($this->machine_id)) $criteria->compare('t.machine_id',$this->machine_id);
        if(!empty($this->status)) $criteria->compare('t.status',$this->status);
        if(!empty($this->name)) $criteria->compare('goods.name',$this->name,true);
	
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
		));
	}

	
	
}
