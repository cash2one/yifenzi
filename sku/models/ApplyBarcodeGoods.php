<?php

/**
 * This is the model class for table "{{apply_barcode_goods}}".
 *
 * The followings are the available columns in table '{{apply_barcode_goods}}':
 * @property integer $id
 * @property string $barcode
 * @property string $name
 * @property integer $cate_id
 * @property string $cate_name
 * @property string $default_price
 * @property string $thumb
 * @property string $model
 * @property string $unit
 * @property integer $create_time
 * @property integer $goods_id
 */
class ApplyBarcodeGoods extends CActiveRecord {

    //路径
    public $path;
    //录入限制数目
    const APPLY_COUNT = 3;

     //状态
    const STATUS_APPLY = 0; //已提交
    const STATUS_PASS=1;  //已采纳
    const STATUS_TIMEOUT=2; //提交超时
    const STATUS_UNPASS = 3 ;//未采纳
   const STATUS_TEMP = 4 ; //暂存
  
    public static function getInput($key = null) {
        $data = array(
            self::STATUS_APPLY => Yii::t('inputGoods', '已提交'),
            self::STATUS_PASS => Yii::t('inputGoods', '已采纳'),
            self::STATUS_TIMEOUT => Yii::t('inputGoods', '提交超时'),
            self::STATUS_UNPASS => Yii::t('inputGoods', '未采纳'),
            self::STATUS_TEMP => Yii::t('inputGoods', '暂存'),
        );
        return $key === null ? $data : $data[$key];
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{apply_barcode_goods}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('goods_id', 'required'),
            array('cate_id, create_time, goods_id', 'numerical', 'integerOnly' => true),
            array('barcode', 'length', 'max' => 13),
            array('name, cate_name, thumb,temp_id', 'length', 'max' => 128),
            array('default_price', 'length', 'max' => 8),
            array('model, unit', 'length', 'max' => 25),
            array('status','length','max'=>32),
            array('describe','length','max'=>128),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, barcode, name, cate_id, cate_name, default_price, thumb, model, unit, create_time, goods_id,status,describe,temp_id', 'safe', 'on' => 'search'),
             array('thumb', 'file', 'types' => 'jpg,png', 'maxSize' => 1024*1024, 'on' => 'create', 'tooLarge' => Yii::t('inputGoods', '文件大于1M，上传失败！请上传小于1M的文件！'), 'allowEmpty' => true, 'safe'=>true),
           
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'barcode' => '条形码',
            'name' => '商品名称',
            'cate_id' => '分类id',
            'cate_name' => '分类',
            'default_price' => '售价',
            'thumb' => '缩略图',
            'model' => '规格',
            'unit' => '单位',
            'create_time' => '创建时间',
            'goods_id' => '产品库商品id',
            'describe'=>'商品描述',
            'status'=>'状态',
            'temp_id'=>'暂存id',
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
        $criteria->compare('barcode', $this->barcode, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('cate_id', $this->cate_id);
        $criteria->compare('cate_name', $this->cate_name, true);
        $criteria->compare('default_price', $this->default_price, true);
        $criteria->compare('thumb', $this->thumb, true);
        $criteria->compare('model', $this->model, true);
        $criteria->compare('unit', $this->unit, true);
        $criteria->compare('create_time', $this->create_time);
        $criteria->compare('goods_id', $this->goods_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    
    public function searchData(){
         $criteria = new CDbCriteria;
         $criteria->select = '*';
         $criteria->group ='goods_id';
         return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
             ));
    }
    
    /**
     * 获取已录入商品条数
     * @param type $id
     * @return type
     */
    public static function number($id){
        $rs = ApplyBarcodeGoods::model()->count('goods_id=:gid and status=:s',array(':gid'=>$id,':s'=>  self::STATUS_APPLY));;
        return $rs;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ApplyBarcodeGoods the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    /**
     * 判断是否录入
     * 
     */
    public static function IsInput($name){
        $rule = EnGoodsRule::model()->find('name=:name',array(':name'=>$name));
        if($rule['is_input'] == EnGoodsRule::EN_INPUT){
            return true;
        }else{
            return false;
        }
    }
}
