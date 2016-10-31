<?php

/**
 * This is the model class for table "{{store_category}}".
 *
 * The followings are the available columns in table '{{store_category}}':
 * @property integer $id
 * @property string $name
 */
class StoreCategory extends CActiveRecord
{
	
	const CACHE_DIR = 'StoreCategory';
	const CACHE_LIST_KEY = 'list';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{store_category}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,style', 'required'),
			array('name', 'length', 'max'=>12),
				array('sort', 'length', 'max'=>4),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
//			array('id, name,style', 'safe', 'on'=>'search'),
            array('style', 'required', 'on' => 'create', 'message' => Yii::t('goods', '请选择上传图片'), 'safe'=>true),
            array('style', 'file', 'types' => 'jpg,gif,png', 'maxSize' => 1024*1024, 'on' => 'create,update', 'tooLarge' => Yii::t('goods', '文件大于1M，上传失败！请上传小于1M的文件！'), 'allowEmpty' => true, 'safe'=>true),
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
			'id' => '主键',
			'name' => '名称',
            'style'=>'分类图标',
			'sort'=>'排序',
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return StoreCategory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	
	/**
	 * 获取店铺分类
	 * @return array
	 */
	public static  function getCategoryList($key = null) {
		$list = Tool::cache(self::CACHE_DIR)->get(self::CACHE_LIST_KEY);
		if (empty($list)) {
			$cri = new CDbCriteria();
			$cri->order = 'sort';
			$model = self::model()->findAll($cri);
			$list = array();
			foreach ($model as $val){
                $val = $val->attributes;
                $val['storeCateImg'] = ATTR_DOMAIN . '/'.$val['style'];
                unset($val['style']);
				$list[$val['id']] = $val;
			}
			Tool::cache(self::CACHE_DIR)->set(self::CACHE_LIST_KEY,$list);
		}
		if(!empty($key) && isset($list[$key])){
            return $list[$key]['storeCateImg'];
        }
		return $list;
	}
	
	/**
	 * 获取店铺分类  按id->name对应
	 * @return array
	 */
	public static  function getCategorys($key = null) {
		$list = Tool::cache(self::CACHE_DIR)->get('keyList');
		if (empty($list)) {
			$model = self::model()->findAll();
			$list = array();
			foreach ($model as $val){
				$list[$val['id']] = $val['name'];
			}
			Tool::cache(self::CACHE_DIR)->set('keyList',$list);
		}
		if(!empty($key)){
			return $list[$key]['storeCateImg'];
		}
		return $list;
	}
	
	/**
	 * 获取分类名称
	 * @param type $id
	 * @return string
	 */
	public static function getCategoryName($id) {
		$list = self::getCategorys();
		return isset($list[$id])?$list[$id]:Yii::t('interestCategory', '未知分类');
	}
	
	//更新缓存
	public function afterSave(){
		parent::afterSave();
		Tool::cache(self::CACHE_DIR)->flush();
		return true;
	}
	
                //删除缓存
     public function afterDelete(){
            parent::afterDelete();
            Tool::cache(self::CACHE_DIR)->flush();
            return true;
     }
}
