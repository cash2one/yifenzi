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
class VendingMachineGoods extends CActiveRecord
{
	
	public $name;
	public $stock;
//	public $line; //货道
	
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
				self::STATUS_ENABLE => Yii::t('vendingMachine', '上架'),
				self::STATUS_DISABLE => Yii::t('vendingMachine', '下架'),
				self::STATUS_DELETE => Yii::t('vendingMachine', '已删除'),
		);
		if (is_numeric($status)) {
			return isset($arr[$status]) ? $arr[$status] : Yii::t('vendingMachine', '未知状态');
		} else {
			return $arr;
		}
	}
	
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{vending_machine_goods}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('goods_id, machine_id,status', 'required'),
			array('stock', 'required','on'=>'stock','message'=>Yii::t('vendingMachine', '库存必填')),
                                                         array('line','required','on'=>'create,update','message'=>Yii::t('vendingMachine', '请提供货道')),
			array('create_time,status,stock', 'numerical', 'integerOnly'=>true,'message'=>Yii::t('vendingMachine', '库存必须是整数')),
                                                         array('stock','compare', 'compareValue' => 0, 'operator' => '>=','message'=>Yii::t('vendingMachine', '库存必须是正整数')),
                                                         array('stock','numerical','message'=>Yii::t('vendingMachine', '库存必须是数字')),
			array('goods_id, machine_id,stock', 'length', 'max'=>11),
			array('status', 'length', 'max'=>2),
			array('name,line', 'length', 'max'=>128),
                                                        array('line','length','max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
                                                    array('line','checkLine','on'=>'create,update'),
			array('id, goods_id, machine_id, create_time,status,name,stock', 'safe', 'on'=>'search'),
		);
	}
        
        /**
         * 检查售货机货道是否重复
         */
        public function checkLine($attribute,$params){
   	
    	if ($this->scenario=='create') {
    		$count = VendingMachineGoods::model()->count('machine_id=:mid AND line=:line',array(':mid'=>$this->machine_id,':line'=>$this->$attribute));
    		if ($count>0) {
    			$this->addError($attribute, Yii::t('vendingMachine', '货道已占用'));
    		}
    	}
    	
    	if ($this->scenario=='update') {
    		$rs =	Yii::app()->db->createCommand()
    			->select('id')
		    	->from('{{vending_machine_goods}}')
		    	->where('machine_id=:mid AND line=:line', array(':mid'=>$this->machine_id,':line'=>$this->$attribute))
		    	->queryRow();
    		if (!empty($rs) && $rs['id']!=$this->id) {
    			$this->addError($attribute, Yii::t('vendingMachine', '货道已占用'));
    		}
    	}

    	return true;
    	
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
			'id' => Yii::t('vendingMachine', 'ID'),
			'goods_id' =>Yii::t('vendingMachine', '关联商品'),
			'machine_id' => Yii::t('vendingMachine', '所属超市'),
			'create_time' => Yii::t('vendingMachine', '创建时间'),
            'line'=>Yii::t('vendingMachine', '货道'),
            'status'=>Yii::t('vendingMachine', '是否上架')
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
	
        $criteria->order = 't.status ASC, t.id DESC';
        
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
		));
	}
	
	/**
	 * 保存商品
	 */
	public function addGoods(){
		
		
		
		
		
	}
	
	
}
