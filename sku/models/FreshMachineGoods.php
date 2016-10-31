<?php

/**
 * This is the model class for table "{{fresh_machine_goods}}".
 *
 * The followings are the available columns in table '{{fresh_machine_goods}}':
 * @property integer $id
 * @property string $goods_id
 * @property string $machine_id
 * @property integer $line_id
 * @property string $line_code
 * @property integer $status
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Goods $goods
 */
class FreshMachineGoods extends CActiveRecord {

	public $partner_id;
    public $goodsCode;//条形码
    public $goodsPrice;//销售价
	
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{fresh_machine_goods}}';
    }

    public $name;
    public $line;
    public $stock;

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
            self::STATUS_ENABLE => Yii::t('freshMachine', '上架'),
            self::STATUS_DISABLE => Yii::t('freshMachine', '下架'),
            self::STATUS_DELETE => Yii::t('freshMachine', '已删除'),
        );
        if (is_numeric($status)) {
            return isset($arr[$status]) ? $arr[$status] : Yii::t('freshMachine', '未知状态');
        } else {
            return $arr;
        }
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.specifications,weight,goods_address
        return array(
            array('goods_id', 'required'),
            array('line_id', 'required','message'=>Yii::t('freshMachine', '货道必填')),
            array('stock', 'required', 'on' => 'stock', 'message' => Yii::t('freshMachine', '库存必填')),
            array('stock','length','max'=>6),         
            array('line_id, status, create_time,stock,weight', 'numerical', 'integerOnly' => true),
            array('stock,weight', 'compare', 'compareValue' => 0, 'operator' => '>=', 'message' =>Yii::t('freshMachine', '必须是正整数')),
            array('stock,weight', 'numerical', 'message' =>Yii::t('freshMachine', '库存必须是数字')),
            array('goodsCode','match', 'pattern' =>'/^[a-z|A-Z|0-9]{13,16}$/', 'message' =>Yii::t('freshMachine', '必须为13或16位字母或数字')),
            array('goodsPrice', 'match', 'pattern' => '/^\d+(\.\d{1,2}){0,1}$/u', 'message' => Yii::t('freshMachine', '只能保留两位小数！')),
            /*array('goodsPrice','match', 'pattern' => '/^\d+(\.\d{1,2}){0,1}$/u',
                'message' => '最多只能保留两位小数！'),*/
            array('goods_id, machine_id,partner_id', 'length', 'max' => 11),
            array('line_code,expr_time', 'length', 'max' => 32),
            array('specifications', 'length', 'max' => 100),
            array('goods_address', 'length', 'max' => 200),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, goods_id, machine_id, line_id, line_code, status, create_time,weight,name, line,stock,partner_id,goodsCode,goodsPrice', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'goods' => array(self::BELONGS_TO, 'Goods', 'goods_id'),
        	'lines' => array(self::BELONGS_TO, 'FreshMachineLine', 'line_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('freshMachine', 'ID'),
            'goods_id' =>Yii::t('freshMachine', '所属商家'),
            'machine_id' => Yii::t('freshMachine', '所属超市'),
            'line_id' =>Yii::t('freshMachine', '货道id'),
            'line_code' => Yii::t('freshMachine', '货道编码'),
            'weight' =>Yii::t('freshMachine', '重量'),
            'status' => Yii::t('freshMachine', '状态'),
            'create_time' =>Yii::t('freshMachine', '创建时间'),
            'specifications'=> Yii::t('freshMachine', '商品规格'),
            'expr_time'=> Yii::t('freshMachine', '商品有效时间'),
            'goods_address'=> Yii::t('freshMachine', '商品产地'),
            'goodsCode'=> Yii::t('freshMachine', '条形码'),
            'goodsPrice'=> Yii::t('freshMachine', '销售价'),
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('goods_id', $this->goods_id, true);
        $criteria->compare('machine_id', $this->machine_id, true);
        $criteria->compare('line_id', $this->line_id);
        $criteria->compare('line_code', $this->line_code, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('create_time', $this->create_time);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection() {
        return Yii::app()->db;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return FreshMachineGoods the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 
     * @return CActiveDataProvider
     */
    public function freshSearch() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;
        $criteria->with = array('goods','lines');
       if(!empty($this->id)) $criteria->compare('t.id', $this->id);
        if(!empty($this->goods_id)) $criteria->compare('t.goods_id', $this->goods_id);
        if(!empty($this->machine_id)) $criteria->compare('t.machine_id', $this->machine_id);
        if(!empty($this->status)) $criteria->compare('t.status', $this->status);
        if(!empty($this->name)) $criteria->compare('goods.name', $this->name, true);
        if(!empty($this->goodsCode)) $criteria->compare('goods.barcode', $this->goodsCode, true);
        if(!empty($this->goodsPrice)) $criteria->compare('goods.price', $this->goodsPrice);
        
        if (!empty($this->partner_id)) {
        	$criteria->addCondition('lines.rent_partner_id= '.$this->partner_id);
        }
        
        $criteria->order = 't.status ASC, lines.code ASC';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        	'pagination'=>array('pageSize'=>30),
        ));
    }

}
