<?php

/**
 * This is the model class for table "{{sku_goods_stock_balance}}".
 *
 * The followings are the available columns in table '{{sku_goods_stock_balance}}':
 * @property integer $id
 * @property integer $s_id
 * @property integer $node
 * @property integer $node_type
 * @property integer $num
 * @property integer $balance
 * @property integer $cur_balance
 * @property integer $create_time
 * @property string $data
 *
 * The followings are the available model relations:
 * @property GoodsStock $s
 */
class GoodsStockBalance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{goods_stock_balance}}';
	}
	
	
	const  NODE_STOCK_CREATE = 1001;											//创建库存
	const  NODE_STOCK_FROZEN= 1002;											//冻结库存
	const  NODE_STOCK_IN = 1003;													//入货
	const  NODE_STOCK_OUT= 1004;													//出货
	const  NODE_FROZEN_STOCK_RESTORE= 1005;							//冻结库存还原
	const  NODE_FROZEN_STOCK_OUT= 1006;									//冻结库存扣除
	const  NODE_STOCK_RESTORE= 1007;											//库存还原
	
	const  NODE_TYPE_IN= 1;
	const  NODE_TYPE_OUT= 2;
	

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('s_id, node, node_type, num, balance, cur_balance, create_time', 'required'),
			array('s_id, node, node_type, num, balance, cur_balance,cur_frozen, create_time', 'numerical', 'integerOnly'=>true),
			array('data,remark', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, s_id, node, node_type, num, balance, cur_balance, cur_frozen,create_time, data,remark', 'safe', 'on'=>'search'),
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
			's' => array(self::BELONGS_TO, 'GoodsStock', 's_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('goodsStockBalance','ID'),
			's_id' => Yii::t('goodsStockBalance','sku关联id'),
			'node' => Yii::t('goodsStockBalance','业务节点'),
			'node_type' => Yii::t('goodsStockBalance','节点类型'),
			'num' => Yii::t('goodsStockBalance','操作数量'),
			'balance' =>Yii::t('goodsStockBalance','最新库存'),
			'cur_balance' =>Yii::t('goodsStockBalance','当前操作量'),
			'create_time' => Yii::t('goodsStockBalance','添加时间'),
			'data' =>Yii::t('goodsStockBalance','相关数据'),
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
		$criteria->compare('s_id',$this->s_id);
		$criteria->compare('node',$this->node);
		$criteria->compare('node_type',$this->node_type);
		$criteria->compare('num',$this->num);
		$criteria->compare('balance',$this->balance);
		$criteria->compare('cur_balance',$this->cur_balance);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('data',$this->data,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GoodsStockBalance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
