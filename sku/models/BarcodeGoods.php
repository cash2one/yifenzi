<?php

/**
 * This is the model class for table "{{sku_barcode_goods}}".
 *
 * The followings are the available columns in table '{{sku_barcode_goods}}':
 * @property integer $id
 * @property integer $barcode
 * @property string $name
 * @property string $thumb
 * @property integer $store
 * @property integer $outlets
 * @property integer $create_time
 */
class BarcodeGoods extends CActiveRecord {

	public $pic;
        
   //是否自定义
    const NO_CUSTOM = 0; //否
    const EN_CUSTOM =1;  //是
  
    public static function getCustom($key = null) {
        $data = array(
            self::NO_INPUT => Yii::t('barcodeGoods', '否'),
            self::EN_INPUT => Yii::t('barcodeGoods', '是'),
        );
        return $key === null ? $data : $data[$key];
    }
    
    //审核状态
    const STATUS_APPLY = 1; //待审核
    const STATUS_PASS = 2; //已审核
  public static function getStatus($key = null) {
        $data = array(
            self::STATUS_APPLY => Yii::t('barcodeGoods', '待审核'),
            self::STATUS_PASS => Yii::t('barcodeGoods', '已审核'),
        );
        return $key === null ? $data : $data[$key];
    }
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{barcode_goods}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('barcode, name, unit', 'required'),
            array('store, outlets, create_time,cate_id,apply_num', 'numerical', 'integerOnly' => true),
            array('default_price', 'numerical', 'min' => 0),
            array('thumb,cate_name', 'length', 'max' => 128),
            array('brand', 'length', 'max' => 64),
            array('status','length','max'=>32),
            array('name', 'length', 'max' => 20),
            array('model, unit', 'length', 'max' => 5),
            array('barcode', 'length', 'max' => 13, 'min' => 13, 'message' =>Yii::t('barcodeGoods', '必须为13位数字')),
            array('barcode','match', 'pattern' =>'/^[0-9]{13}$/', 'message' =>Yii::t('barcodeGoods', '必须为13位数字')),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, barcode, name, thumb, store, brand,outlets, create_time,cate_id,cate_name,is_custom,status,apply_num', 'safe', 'on' => 'search'),
            array('barcode','checkcode','on'=>'create,update'),
            array('name','checkname','on'=>'create,update'),
            array('thumb', 'required', 'on' => 'create,update', 'message' => Yii::t('barcodeGoods', '请选择上传图片'), 'safe'=>true),
            array('thumb', 'file', 'types' => 'jpg,gif,png', 'maxSize' => 1024*1024, 'on' => 'create,update', 'tooLarge' => Yii::t('barcodeGoods', '文件大于1M，上传失败！请上传小于1M的文件！'), 'allowEmpty' => true, 'safe'=>true),
 
        );
    }

    /*
     * 检查条形码是否存在
     */

    public function checkcode($attribute, $params) {

        if ($this->scenario == 'create') {
            $count = BarcodeGoods::model()->count('barcode=:barcode', array(':barcode' => $this->$attribute));
            if ($count > 0) {
                $this->addError($attribute, Yii::t('barcodeGoods', '条形码已存在'));
            }
        }

        if ($this->scenario == 'update') {
            $rs = Yii::app()->db->createCommand()
                    ->select('id')
                    ->from('{{barcode_goods}}')
                    ->where('barcode=:barcode', array(':barcode' => $this->$attribute))
                    ->queryRow();
            if (!empty($rs) && $rs['id'] != $this->id) {
                $this->addError($attribute, Yii::t('barcodeGoods', '条形码已存在'));
            }
        }

        return true;
    }

    /*
     * 检查商品名是否重复
     */

    public function checkname($attribute, $params) {

        if ($this->scenario == 'create') {
            $count = BarcodeGoods::model()->count('name=:name', array(':name' => $this->$attribute));
            if ($count > 0) {
                $this->addError($attribute, Yii::t('barcodeGoods', '商品名已存在'));
            }
        }

        if ($this->scenario == 'update') {
            $rs = Yii::app()->db->createCommand()
                    ->select('id')
                    ->from('{{barcode_goods}}')
                    ->where('name=:name', array(':name' => $this->$attribute))
                    ->queryRow();
            if (!empty($rs) && $rs['id'] != $this->id) {
                $this->addError($attribute, Yii::t('barcodeGoods', '商品名已存在'));
            }
        }

        return true;
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        		'goodsPicture' => array(self::HAS_MANY, 'BarcodeGoodsPicture', 'goods_id'), //商品与多图片
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'barcode' => Yii::t('barcodeGoods', '条形码'),
            'name' => Yii::t('barcodeGoods', '商品名称'),
            'thumb' => Yii::t('barcodeGoods', '缩略图'),
            'model' => Yii::t('barcodeGoods', '规格'),
            'store' => Yii::t('barcodeGoods', '店铺id'),
            'unit' => Yii::t('barcodeGoods', '单位'),
        		'brand' => Yii::t('barcodeGoods', '品牌'),
            'default_price' => Yii::t('barcodeGoods', '默认售价'),
            'outlets' => Yii::t('barcodeGoods', '网点id（售货机、超市等 ）'),
            'create_time' => Yii::t('barcodeGoods', '创建时间'),
        	'cate_name' => Yii::t('barcodeGoods', '分类'),
            'status'=>Yii::t('barcodeGoods','状态')
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
        $criteria->compare('barcode', $this->barcode,true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('thumb', $this->thumb, true);
         $criteria->compare('cate_name', $this->cate_name,true);
        $criteria->compare('store', $this->store);
        $criteria->compare('outlets', $this->outlets);
        $criteria->compare('create_time', $this->create_time);
        $criteria->compare('status', $this->status);
        $criteria->compare('is_custom', $this->is_custom);
        $criteria->order = 'create_time desc';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    public function searchInputGoods(){
        
         $criteria = new CDbCriteria;    
         $criteria->select = ' t.*';
         $criteria->join = 'join {{apply_barcode_goods}} as a on a.goods_id = t.id';
         $criteria->group = 't.barcode';
         $criteria->order = 't.apply_time desc';
//        $criteria->compare('t.barcode', $this->barcode,true);
//        $criteria->condition = 't.status ='.$this->name;
//        $criteria->compare('t.name', $this->name,true);

         if($this->status ==BarcodeGoods::STATUS_APPLY){
         $criteria->condition = 't.status ='.BarcodeGoods::STATUS_APPLY;
         }elseif ($this->status ==BarcodeGoods::STATUS_PASS) {
            $criteria->condition = 't.status ='.BarcodeGoods::STATUS_PASS;
        }elseif (!empty($this->name)) {
             $criteria->compare('t.name', $this->name,true);
        }
        elseif (!empty($this->barcode)) {
             $criteria->compare('t.barcode', $this->barcode,true);
        }else{
         $criteria->condition ="t.status =".BarcodeGoods::STATUS_PASS." AND t.is_custom = ".BarcodeGoods::NO_CUSTOM." OR t.status = ".BarcodeGoods::STATUS_APPLY." AND t.is_custom = ".BarcodeGoods::EN_CUSTOM;
         }
         
//         var_dump($criteria)
         return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BarcodeGoods the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_time = time();
            }
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 批量插入商品图片数据表
     * @param array $data
     * @return bool
     * @author zhenjun_xu <412530435@qq.com>
     */
    public function addGoodsPicture(Array $data)
    {
    	return BarcodeGoodsPicture::addArray($data, $this->id);
    }

}
