<?php

/**
 * This is the model class for table "{{orders_goods}}".
 *
 * The followings are the available columns in table '{{orders_goods}}':
 * @property integer $id
 * @property integer $order_id
 * @property integer $name
 * @property integer $gid
 * @property integer $num
 * @property string $price
 * @property string $total_price
 * @property integer $status
 * @property integer $create_time
 */
class OrdersGoods extends CActiveRecord
{
	
	public $weight;
	public $line_id;
	public $line_code;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{orders_goods}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time', 'required'),
			array('order_id,sgid, gid, num, status, create_time,weight', 'numerical', 'integerOnly'=>true),
			array('price, total_price,sgid,supply_price,line_id', 'length', 'max'=>11),
			array('sg_outlets', 'length', 'max'=>16),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, gid, num, price, total_price, status, create_time,sgid,supply_price,weight,name,sg_outlets,line_id', 'safe', 'on'=>'search'),
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
			 'goods'=>array(self::BELONGS_TO,'Goods','gid'),
			 'order' => array(self::BELONGS_TO, 'Order','order_id'),
                     'freshMachineline'=>array(self::BELONGS_TO,'FreshMachineline','line_id'),
                    'VendingMachineGoods' => array(self::BELONGS_TO, 'VendingMachineGoods','sgid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' =>Yii::t('ordersGoods','ID'),
			'order_id' => Yii::t('ordersGoods','订单id'),
			'name' => Yii::t('ordersGoods','商品名称'),
            'sgid' =>Yii::t('ordersGoods','门店的商品id'),
			'gid' => Yii::t('ordersGoods','商品id'),
			'num' => Yii::t('ordersGoods','数量'),
			'price' => Yii::t('ordersGoods','单价'),
			'total_price' => Yii::t('ordersGoods','支付价格'),
			'status' => Yii::t('ordersGoods','商品状态'),
			'weight' =>Yii::t('ordersGoods','单位重量'),
			'create_time' =>Yii::t('ordersGoods','创建时间'),
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
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('name',$this->name);
		$criteria->compare('gid',$this->gid);
		$criteria->compare('num',$this->num);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('total_price',$this->total_price,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrdersGoods the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
