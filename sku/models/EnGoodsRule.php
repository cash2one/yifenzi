<?php

/**
 * This is the model class for table "{{en_goods_rule}}".
 *
 * The followings are the available columns in table '{{en_goods_rule}}':
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $upload_bonus
 * @property string $adopt_bonus
 * @property integer $is_input
 */
class EnGoodsRule extends CActiveRecord
{
    //是否录入
    const NO_INPUT = 0; //否
    const EN_INPUT =1;  //是
  
    public static function getInput($key = null) {
        $data = array(
            self::NO_INPUT => Yii::t('inputGoods', '否'),
            self::EN_INPUT => Yii::t('inputGoods', '是'),
        );
        return $key === null ? $data : $data[$key];
    }
    
    //项目类型
    const TYPE_TEXT = 0; //单行文本框
    const TYPE_TEXTS = 1;    //多行文本框
    const TYPE_IMAGE = 2 ;   //图片
    
    //名称
    const RULE_NAME = 'name';
    const RULE_BARCODE = 'barcode';
    const RULE_CATE_NAME = 'cate_name';
    const RULE_PRICE = 'default_price';
    const RULE_THUMB = 'thumb';
    const RULE_MODEL = 'model';
    const RULE_DESCRIBE ='describe';
    const RULE_UNIT = 'unit';

    public static function getType($key = null){
        $data = array(
            self::TYPE_TEXT => Yii::t('inputGoods', '单行文本框'),
            self::TYPE_TEXTS => Yii::t('inputGoods', '多行文本框'),
            self::TYPE_IMAGE =>Yii::t('inputGoods', '图片'),
        );
        return $key === null ? $data : $data[$key];
    }
    
    public static  function getName($key = null){
        $data = array(
            self::RULE_BARCODE =>Yii::t('inputGoods', '商品条形码'),
            self::RULE_NAME => Yii::t('inputGoods', '商品名称'),
            self::RULE_CATE_NAME => Yii::t('inputGoods', '商品分类'),
            self::RULE_MODEL =>Yii::t('inputGoods', '商品规格'),
            self::RULE_PRICE=>Yii::t('inputGoods', '商品价格'),
             self::RULE_DESCRIBE=>Yii::t('inputGoods', '商品描述'),
             self::RULE_UNIT=>Yii::t('inputGoods', '商品单位'),
            self::RULE_THUMB=>Yii::t('inputGoods', '商品标题图'),
        );
    if(!empty($key)&&isset($data[$key])){
            return $data[$key];
        }
      if($key===null){
          return $data;
      }
    }


    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{en_goods_rule}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_input', 'numerical', 'integerOnly'=>true),
			array('name, type', 'length', 'max'=>128),
                                                     array('name, type, is_input','required'),
                                                     array('name','unique'),
			array('upload_bonus, adopt_bonus', 'length', 'max'=>8),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, type, upload_bonus, adopt_bonus, is_input', 'safe', 'on'=>'search'),
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
			'name' => '项目',
			'type' => '项目类型',
			'upload_bonus' => '上传奖励',
			'adopt_bonus' => '采纳奖励',
			'is_input' => '是否开放录入',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('upload_bonus',$this->upload_bonus,true);
		$criteria->compare('adopt_bonus',$this->adopt_bonus,true);
		$criteria->compare('is_input',$this->is_input);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EnGoodsRule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
