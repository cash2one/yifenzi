<?php

/**
 * This is the model class for table "{{goods}}".
 *
 * The followings are the available columns in table '{{goods}}':
 * @property string $id
 * @property string $name
 * @property string $thumb
 * @property string $member_id
 * @property string $price
 * @property integer $barcode
 * @property integer $is_barcode
 * @property integer $status
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property GwMember $member
 * @property SuperGoods[] $superGoods
 */
class Goods extends CActiveRecord {

    public $endPrice;
    public $gid;
    public $category;
    
    public $pic;
	public $goods_id;
	public $stock;
	public $line_id;
    public $partner_name;
    public $gai_number;
	
	const CACHE_DIR_API_CGOODS_INDEX = 'CGoodsIndexV3';
	const CACHE_DIR_API_CGOODS_SEARCH = 'CGoodsSearchV3';
	const CACHE_DIR_API_CGOODS_STORE_GOODS_LIST = 'CGoodsStoreGoodsListV3';

    
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{goods}}';
    }

    const STATUS_AUDIT = 0;
    const STATUS_PASS = 1;
    const STATUS_NOPASS = 2;

    /**
     * 审核状态用文字标示
     * @param null|int $status 查询出来的审核状态(0 审核中,1 审核通过 2 未通过)
     * @return array|null
     */
    public static function getStatus($status = null) {
        $arr = array(
            self::STATUS_AUDIT => Yii::t('goods', '审核中'),
            self::STATUS_PASS => Yii::t('goods', '审核通过'),
            self::STATUS_NOPASS => Yii::t('goods', '审核未通过'),
        );
        if (is_numeric($status)) {
            return isset($arr[$status]) ? $arr[$status] : null;
        } else {
            return $arr;
        }
    }
    const IS_ONE = 1;
    const IS_NOT_ONE = 0;

    /**
     * 是否参加活动
     * @param null }int $key
     * @return array|null
     */
    public static function gender($key = null) {
        $arr = array(
            self::IS_NOT_ONE => Yii::t('goods', '否'),
            self::IS_ONE => Yii::t('goods', '是'),
        );
        if (is_numeric($key)) {
            return isset($arr[$key]) ? $arr[$key] : null;
        } else {
            return $arr;
        }
    }

    
    const IS_PROMO = 1;
    const IS_NOT_PROMO = 0;
    
    /**
     * 是否参加活动
     * @param null }int $key
     * @return array|null
     */
    public static function getIsProme($key = null) {
    	$arr = array(
    			self::IS_NOT_PROMO => Yii::t('goods', '否'),
    			self::IS_PROMO => Yii::t('goods', '是'),
    	);
    	if (is_numeric($key)) {
    		return isset($arr[$key]) ? $arr[$key] : null;
    	} else {
    		return $arr;
    	}
    }
    
    const IS_FOR = 1;
    const IS_NOT_FOR = 0;
    
    /**
     * 是否参加活动
     * @param null }int $key
     * @return array|null
     */
    public static function getIsFor($key = null) {
    	$arr = array(
    			self::IS_NOT_FOR => Yii::t('goods', '否'),
    			self::IS_FOR => Yii::t('goods', '是'),
    	);
    	if (is_numeric($key)) {
    		return isset($arr[$key]) ? $arr[$key] : null;
    	} else {
    		return $arr;
    	}
    }
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('member_id,name,price,supply_price,barcode,cate_id,source_cate_id,content,is_one,is_promo,is_for', 'required','on'=>'create,update,barcode_add,barcode_update'),
            array('is_barcode, status,create_time, cate_id,source_cate_id,gid', 'numerical', 'integerOnly' => true),
            array('thumb,category', 'length', 'max' => 128),
            array('name','length','max'=>20),
        	array('sec_title','length','max'=>50),
//            array('name','match', 'pattern' => '/^\S+$/','message'=>'名称间不能含有空格'),
            array('name','match', 'pattern' => '/^[^%&""~\';=?!<>]+$/','message'=>Yii::t('goods', '不能含有特殊字符')),
            array('member_id, price,supply_price', 'length', 'max' => 9),
             //array('barcode', 'length', 'max' => 16, 'min' => 13, 'message' =>Yii::t('barcodeGoods', '必须为13位数字')),
            array('barcode','match', 'pattern' =>'/^[a-z|A-Z|0-9]{13,16}$/', 'message' =>Yii::t('barcodeGoods', '必须为13或16位字母或数字')),
            array('price,supply_price,score', 'numerical'),
             array('price,supply_price', 'compare', 'compareValue' => '0', 'operator' => '>'), //零售价,供货价不能为0
            array('price,supply_price', 'match', 'pattern' => '/^\d+(\.\d{1,2}){0,1}$/u',
                'message' => Yii::t('goods', '只能保留两位小数！')),
            array('price,supply_price', 'compare', 'compareValue' => '0', 'operator' => '>'), //零售价必须大于供货价
            array('price', 'compare', 'compareAttribute' => 'supply_price', 'operator' => '>='), //零售价必须大于供货价
//        		array('price,supply_price', 'compare', 'compareValue' => '10000', 'operator' => '<='), //零售价必须大于供货价
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, gid,endPrice,name, thumb, member_id, price, barcode, is_barcode,price, status, create_time, cate_id,source_cate_id,category,is_one,is_promo,line_id,partner_name,gai_number', 'safe', 'on' => 'search'),

            array('barcode', 'checkUserBarcode','on'=>'create,update,barcode_add,barcode_update'),
            array('name','checkName','on'=>'create,update,barcode_add,barcode_update'),
             array('thumb', 'required', 'on' => 'create,update', 'message' => Yii::t('goods', '请选择上传图片'), 'safe'=>true),
            array('thumb', 'file', 'types' => 'jpg,gif,png', 'maxSize' => 1024*1024, 'on' => 'create,update', 'tooLarge' => Yii::t('goods', '文件大于1M，上传失败！请上传小于1M的文件！'), 'allowEmpty' => true, 'safe'=>true),
        );
    }
    
    

    /**
     * 检查商品名
     * @param type $attribute
     * @param type $params
     */
    public function checkName($attribute, $params) {
    	
    	if ($this->scenario=='create' || $this->scenario=='barcode_add') {
    		$count = Goods::model()->count('member_id=:member_id AND name=:name',array(':member_id'=>$this->member_id,':name'=>$this->$attribute));
    		if ($count>0) {
    			$this->addError($attribute, Yii::t('goods', '商品名已存在'));
    		}
    	}
    	
    	if ($this->scenario=='update' || $this->scenario=='barcode_update') {
    		$rs =	Yii::app()->db->createCommand()
    			->select('id')
		    	->from('{{goods}}')
		    	->where('member_id=:member_id AND name=:name', array(':member_id'=>$this->member_id,':name'=>$this->$attribute))
		    	->queryRow();
    		if (!empty($rs) && $rs['id']!=$this->id) {
    			$this->addError($attribute, Yii::t('goods', '商品名已存在'));
    		}
    	}

    	return true;
    	
    }
    
    

    /**
     * 检查条码
     * @param type $attribute
     * @param type $params
     */
    public function checkUserBarcode($attribute, $params) {
    	 
    	if ($this->scenario=='create'  || $this->scenario=='barcode_add') {
    		$count = Goods::model()->count('member_id=:member_id AND barcode=:barcode',array(':member_id'=>$this->member_id,':barcode'=>$this->$attribute));
    		if ($count>0) {
    			$this->addError($attribute, Yii::t('goods', '条形码已存在'));
    		}
    	}
    	 
    	if ($this->scenario=='update' || $this->scenario=='barcode_update') {
    		$rs =	Yii::app()->db->createCommand()
    		->select('id')
    		->from('{{goods}}')
    		->where('member_id=:member_id AND barcode=:barcode', array(':member_id'=>$this->member_id,':barcode'=>$this->$attribute))
    		->queryRow();
    		if (!empty($rs) && !empty($rs['id']) &&  $rs['id']!=$this->id) {
    			$this->addError($attribute, Yii::t('goods', '条形码已存在'));
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
            'member' => array(self::BELONGS_TO, 'GwMember', 'member_id'),
            'superGoods' => array(self::HAS_MANY, 'SuperGoods', 'goods_id'),
            'goodsCategory' => array(self::BELONGS_TO, 'GoodsCategory', 'cate_id'),
            'partners' => array(self::BELONGS_TO, 'Partners', 'partner_id'),
        	'goodsPicture' => array(self::HAS_MANY, 'GoodsPicture', 'goods_id'), //商品与多图片
        	'category' => array(self::BELONGS_TO, 'Category', 'source_cate_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('goods', 'ID'),
            'name' => Yii::t('goods', '商品名称'),
        	'sec_title' => Yii::t('goods', '次标题'),
            'thumb' => Yii::t('goods', '缩略图'),
            'cate_id' => Yii::t('goods', '所属店铺分类'),
        	'source_cate_id' => Yii::t('goods', '原始商品分类'),
            'category'=>Yii::t('goods', '分类'),
            'member_id' => Yii::t('goods', '商家会员id'),
            'partner_id'=>Yii::t('goods', '所属商家'),
            'supply_price' =>Yii::t('goods', '供货价'),
            'price' =>Yii::t('goods', '销售价格'),
            'content' =>Yii::t('goods', '商品详情'),
            'barcode' =>Yii::t('goods', '条形码'),
            'is_barcode' => Yii::t('goods', '是否条码库商品'),
            'status' =>Yii::t('goods', '状态'),
            'create_time' =>Yii::t('goods', '创建时间'),
            'is_one' =>Yii::t('goods', '是否一元购商品'),
            'is_promo' =>Yii::t('goods', '是否促销商品'),
            'is_for' => Yii::t('goods', '是否专供商品'),
            'partner_name'=> Yii::t('goods', '商家名称'),
            'gai_number'=> Yii::t('goods', '盖网号'),
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

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.name', $this->name, true);
        $criteria->compare('t.thumb', $this->thumb);
        $criteria->compare('t.member_id', $this->member_id);
        $criteria->compare('t.price', '>=' . $this->price);
        $criteria->compare('t.price', '<=' . $this->endPrice);
        $criteria->compare('t.barcode', $this->barcode);
        $criteria->compare('t.is_barcode', $this->is_barcode);
        $criteria->compare('t.status', $this->status);
        $criteria->compare('t.is_one', $this->is_one);
        $criteria->compare('t.is_promo', $this->is_promo);
        $criteria->compare('t.create_time', $this->create_time);
        if(is_numeric($this->endPrice)||is_numeric($this->price)){
             $criteria->order = 'price ASC' ;
        }else{
             $criteria->order = 't.create_time DESC' ;
        }
        // 分类
  
        $criteria->with = array( 'goodsCategory', 'category');

        //$criteria->join = ' LEFT JOIN  '.GoodsCategory::model()->tableName().' AS g ON t.cate_id=g.id ';
        $criteria->join = ' LEFT JOIN  '.GoodsCategory::model()->tableName().' AS g ON t.cate_id=g.id LEFT JOIN '.Partners::model()->tableName().' AS p ON t.partner_id = p.id';
        $criteria->compare('g.name', $this->category,true);
        $criteria->compare('p.name', $this->partner_name,true);
        $criteria->compare('p.gai_number', $this->gai_number,true);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
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
    public function searchByMemberId($member_id) {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('member_id', $member_id, true);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Goods the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    //superGoodsSearch
    public function superGoodsSearch($type=null,$sid=null,$all=false) {
        // @todo Please modify the following code to remove attributes that should not be searched.
    	$type = $type*1;
    	$sid = $sid*1;
        $criteria = new CDbCriteria;

        $criteria->compare('t.name', $this->name, true);
        $criteria->compare('t.price', $this->price, true);
        $criteria->addCondition('t.member_id=:mid and t.status=:status'); 
        $criteria->params[':mid'] = $this->member_id;
        $criteria->params[':status'] = self::STATUS_PASS;
        
        if (!empty($type) && $all==false) {
        	switch ($type){
        		case Stores::SUPERMARKETS:
        			
        			$sgood = Yii::app()->db->createCommand()
        			->from(SuperGoods::model()->tableName())
        			->select('goods_id')
        			->where('super_id=:super_id',array(':super_id'=>$sid))
        			->queryAll();
        			
        			$gids = array();
        			foreach ($sgood as $v){
        				$gids[] = $v['goods_id'];
        			}
        			
        			$criteria->addNotInCondition('id', $gids);
        		break;
        			
        			
        		case Stores::MACHINE:
        				 
        			$sgood = Yii::app()->db->createCommand()
        			->from(VendingMachineGoods::model()->tableName())
        			->select('goods_id')
        			->where('machine_id=:machine_id',array(':machine_id'=>$sid))
        			->queryAll();
        				 
        			$gids = array();
       				foreach ($sgood as $v){
       					$gids[] = $v['goods_id'];
       				}
        				 
       				$criteria->addNotInCondition('id', $gids);
       			break;
        				
       			case Stores::FRESH_MACHINE:
        					 
        			$sgood = Yii::app()->db->createCommand()
        			->from(FreshMachineGoods::model()->tableName())
        			->select('goods_id')
        			->where('machine_id=:machine_id',array(':machine_id'=>$sid))
        			->queryAll();
        					 
        			$gids = array();
        			foreach ($sgood as $v){
        				$gids[] = $v['goods_id'];
        			}
        					 
        			$criteria->addNotInCondition('id', $gids);
        		break;
        	}
        }
        
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    
    /**
     * 批量插入商品图片数据表
     * @param array $data
     * @return bool
     * @author zhenjun_xu <412530435@qq.com>
     */
    public function addGoodsPicture(Array $data)
    {
    	return GoodsPicture::addArray($data, $this->id);
    }
    

}
