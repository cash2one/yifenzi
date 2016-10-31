<?php

/**
 * This is the model class for table "{{goods_comment}}".
 *
 * The followings are the available columns in table '{{goods_comment}}':
 * @property string $id
 * @property string $member_id
 * @property string $partner_id
 * @property string $name
 * @property string $tree
 * @property integer $sort
 *
 * The followings are the available model relations:
 * @property GwMember $member
 */
class GoodsComment extends CActiveRecord
{
	const MIN_SCORE = 1;
	const MAX_SCORE = 5;
	
	public $min_score = GoodsComment::MIN_SCORE;
	public $max_score =  GoodsComment::MAX_SCORE;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{goods_comment}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, content,score,service_score,quality_score,create_time,partner_id,goods_id,order_id,store_goods_id', 'required'),
			array('member_id,score,service_score,quality_score,create_time,partner_id,goods_id,order_id,store_goods_id', 'numerical', 'integerOnly'=>true),
			array('member_id, partner_id', 'length', 'max'=>11),
			array('content', 'length', 'max'=>255),
			array('score,service_score,quality_score', 'compare', 'compareAttribute'=>'max_score','operator' => '<='),
			array('score,service_score,quality_score', 'compare', 'compareAttribute'=>'min_score','operator' => '>='),
			array('member_id, content,score,service_score,quality_score,create_time,partner_id,goods_id,order_id,store_goods_id', 'safe', 'on'=>'search'),
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
			'partner' => array(self::BELONGS_TO, 'Partners', 'partner_id'),
            'goods'=>array(self::BELONGS_TO,'Goods','goods_id'),
			'order'=>array(self::BELONGS_TO,'Orders','order_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('goodsComment','主键'),
			'member_id' => Yii::t('goodsComment','会员id'),
			'partner_id' => Yii::t('goodsComment','商家id'),
			'content' => Yii::t('goodsComment','评价内容'),
			'score' => Yii::t('goodsComment','评价分数'),
			'service_score' =>Yii::t('goodsComment','服务评分'),
			'quality_score' =>Yii::t('goodsComment','商品质量评分'),
			'create_time' => Yii::t('goodsComment','时间'),
			'goods_id' => Yii::t('goodsComment','商品ID'),
			'order_id'=>Yii::t('goodsComment','订单ID'),
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
		$criteria->compare('member_id',$this->member_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GoodsComment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
