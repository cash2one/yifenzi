<?php

/**
 * This is the model class for table "{{super_goods}}".
 *
 * The followings are the available columns in table '{{super_goods}}':
 * @property integer $id
 * @property string $goods_id
 * @property string $super_id
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Goods $goods
 * @property Supermarkets $super
 */
class SuperGoods extends CActiveRecord
{
	
	public $name;
	public $stock  = 0;
	
	
	const STATUS_ENABLE = 1;
	const STATUS_DISABLE = 2;
	const STATUS_DELETE = 3;
	
	/**
	 * 状态用文字标示
	 * @param null|int $status 查询出来的状态
	 * @return array|null
	 */
	
	public static function getStatus($status = null) {
		$arr = array(
				self::STATUS_ENABLE => Yii::t('superGoods', '上架'),
				self::STATUS_DISABLE => Yii::t('superGoods', '下架'),
				self::STATUS_DELETE => Yii::t('superGoods', '已删除'),
		);
		if (is_numeric($status)) {
			return isset($arr[$status]) ? $arr[$status] : Yii::t('superGoods', '未知状态');
		} else {
			return $arr;
		}
	}
	
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{super_goods}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('goods_id, super_id,status', 'required'),
			array('stock', 'required','on'=>'stock'),
			array('create_time,status', 'numerical', 'integerOnly'=>true),
                                                     array('stock','numerical','message'=>Yii::t('superGoods', '库存必须是整数')),
                                                     array('stock','compare','compareValue'=>'0', 'operator'=>'>=','message'=>Yii::t('superGoods', '库存不能为负'),'on'=>'stock,add'),
                                                 
			array('goods_id, super_id', 'length', 'max'=>11),
                                                     array('stock', 'length', 'max'=>6),
			array('status', 'length', 'max'=>2),
			array('name', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, goods_id, super_id, create_time,status,name,stock', 'safe', 'on'=>'search'),
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
			'super' => array(self::BELONGS_TO, 'Supermarkets', 'super_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('superGoods', 'ID'),
			'goods_id' =>Yii::t('superGoods', '关联商品'),
			'super_id' => Yii::t('superGoods', '所属超市'),
			'create_time' => Yii::t('superGoods', '创建时间'),
            'status'=>Yii::t('superGoods', '是否上架')
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

		$criteria->with = array('super','goods');
		$criteria->compare('id',$this->id);
		$criteria->compare('goods_id',$this->goods_id);
		$criteria->compare('super_id',$this->super_id);
		$criteria->compare('create_time',$this->create_time);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SuperGoods the static model class
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
		$criteria->with = array('super','goods');
        if(!empty($this->id)) $criteria->compare('t.id',$this->id);
        if(!empty($this->goods_id)) $criteria->compare('t.goods_id',$this->goods_id);
        if(!empty($this->super_id)) $criteria->compare('t.super_id',$this->super_id);
        if(!empty($this->status)) $criteria->compare('t.status',$this->status);
        if(!empty($this->name)) $criteria->compare('goods.name',$this->name,true);
                                   $criteria->order = 't.status ASC , t.create_time DESC' ;
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
				'pagination'=>array('pageSize'=>20),
		));
	}
	
	/**
	 * 保存商品
	 */
	public function addGoods(){
		
		
		
		
		
	}
	
	
}
