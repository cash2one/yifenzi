<?php

/**
 * This is the model class for table "{{goods_category}}".
 *
 * The followings are the available columns in table '{{goods_category}}':
 * @property string $id
 * @property string $member_id
 * @property string $parent_id
 * @property string $name
 * @property string $tree
 * @property integer $sort
 *
 * The followings are the available model relations:
 * @property GwMember $member
 */
class GoodsCategory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{goods_category}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id,  name', 'required'),          
			array('sort', 'numerical', 'integerOnly'=>true),
			array('member_id, parent_id', 'length', 'max'=>11),
			array('name', 'length', 'max'=>12),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, parent_id, name, tree, sort', 'safe', 'on'=>'search'),
                    array('name','checkName','on'=>'create,update')
		);
	}
        /**
     * 商品分类不能重复
     * @param type $attribute
     * @param type $params
     */
    public function checkName($attribute, $params){
            	
    	if ($this->scenario=='create') {
    		$count = GoodsCategory::model()->count('member_id=:id AND name=:name',array(':id'=> Yii::app()->user->id,':name'=>$this->$attribute));
    		if ($count>0) {
    			$this->addError($attribute, Yii::t('goodsCategory', '分类{attr}已存在',array('{attr}'=>$this->$attribute)));
    		}
    	}
    	
    	if ($this->scenario=='update') {
    		$rs =	Yii::app()->db->createCommand()
    			->select('id')
		    	->from('{{goods_category}}')
		    	->where('member_id=:id AND name=:name', array(':id'=>Yii::app()->user->id,':name'=>$this->$attribute))
		    	->queryRow();
    		if (!empty($rs) && $rs['id']!=$this->id) {
    			$this->addError($attribute, Yii::t('goodsCategory', '分类{attr}已存在',array('attr'=>$this->$attribute)));
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
			'member' => array(self::BELONGS_TO, 'GwMember', 'member_id'),
                                                          'goods'=>array(self::HAS_MANY,'Goods','cate_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('goodsCategory','主键'),
			'member_id' =>Yii::t('goodsCategory', '所属线下商家'),
			'parent_id' =>Yii::t('goodsCategory', '父级'),
			'name' => Yii::t('goodsCategory','名称'),
			'tree' =>Yii::t('goodsCategory','节点'),
			'sort' => Yii::t('goodsCategory','排序'),
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

//		$criteria->compare('id',$this->id,true);
		$criteria->compare('member_id',$this->member_id);
//		$criteria->compare('parent_id',$this->parent_id,true);
		$criteria->compare('name',$this->name,true);
//		$criteria->compare('tree',$this->tree,true);
//		$criteria->compare('sort',$this->sort);
                                     $criteria->order = 'sort ASC' ;
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /*
     * 提取所属当前商家分类
     */
    public static function getGoodsCategoryList($member_id){
        $model = self::model()->findAllByAttributes(array('member_id'=>$member_id));
        return CHtml::listData($model,'id','name');
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GoodsCategory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
