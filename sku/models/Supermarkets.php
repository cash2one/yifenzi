<?php

/**
 * This is the model class for table "{{supermarkets}}".
 *
 * The followings are the available columns in table '{{supermarkets}}':
 * @property string $id
 * @property string $name
 * @property string $member_id
 * @property string $mobile
 * @property string $logo
 * @property integer $type
 * @property integer $province_id
 * @property integer $city_id
 * @property integer $district_id
 * @property string $street
 * @property string $zip_code
 * @property string $lng
 * @property string $lat
 * @property integer $status
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property SuperGoods[] $superGoods
 * @property SupermarketStaffs[] $supermarketStaffs
 * @property GwMember $member
 */
class Supermarkets extends CActiveRecord {

    public $gai_number;
    public $referrals_gai_number;
    public $DISTRIBUTION_OPTION = array(0 => '到店消费', 1 => '商家配送',2 => '人人配送');
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{supermarkets}}';
    }
    
    //最大限额
    const MAX_MONEY = 2000;
    
    //是否推荐
    const RECOMMEND_NO = 0;    //不推荐
    const RECOMMEND_YES = 1;   //推荐

    public static function getIsRencommend($key = null) {
        $data = array(
            self::RECOMMEND_NO => Yii::t('supermarkets', '不推荐'),
            self::RECOMMEND_YES => Yii::t('supermarkets', '推荐'),
        );
        return $key === null ? $data : $data[$key];
    }

    //状态
    const STATUS_APPLY = 0;   //申请
    const STATUS_ENABLE = 1;  //启用
    const STATUS_DISABLE = 2; //禁用

    public static function getStatus($key = null) {
        $data = array(
            self::STATUS_APPLY => Yii::t('supermarkets', '申请'),
            self::STATUS_ENABLE => Yii::t('supermarkets', '启用'),
            self::STATUS_DISABLE => Yii::t('supermarkets', '禁用'),
        );
        return $key === null ? $data : $data[$key];
    }

    //是否送货上门
    const DELIVERY_NO = 0;
    const DELIVERY_YES = 1;
    const PP_DELIVERY = 2;

    public static function getDelivery($key = null) {
        $data = array(
            self::DELIVERY_NO => Yii::t('supermarkets', '自提'),
            self::DELIVERY_YES => Yii::t('supermarkets', '商家配送'),
//            self::PP_DELIVERY =>Yii::t('supermarkets', '人人配送')
        );
        return $key === null ? $data : $data[$key];
    }
    
    //是否固定配送费
    const IS_FIXED_NO = 0;  
    const IS_FIXED_YES =1;
    
     public static function getFixed($key = null) {
        $data = array(
            self::IS_FIXED_NO => Yii::t('supermarkets', '按配送费百分比分配'),
            self::IS_FIXED_YES => Yii::t('supermarkets', '固定配送费'),
        );
        return $key === null ? $data : $data[$key];
    }
    
    //是否自动接单
    const IS_AUTOMATIC_ORDER_NO = 0;
    const IS_AUTOMATIC_ORDER_YES = 1;
    
    public static function getAuto($key = null) {
        $data = array(
            self::IS_AUTOMATIC_ORDER_NO => Yii::t('supermarkets', '否'),
            self::IS_AUTOMATIC_ORDER_YES => Yii::t('supermarkets', '是'),
        );
        return $key === null ? $data : $data[$key];
    }
    
    //是否按配送分成分成配送费
    const IS_DISTRIBUTION_NO = 0;
    const IS_DISTRIBUTION_YES = 1;

    
        public static function getDistribution($key = null) {
        $data = array(
            self::IS_DISTRIBUTION_NO => Yii::t('supermarkets', '否'),
            self::IS_DISTRIBUTION_YES => Yii::t('supermarkets', '是'),
        );
        return $key === null ? $data : $data[$key];
    }
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,member_id,partner_id,mobile,province_id, city_id, district_id, street,zip_code,category_id,is_delivery,delivery_mini_amount,delivery_fee,delivery_start_amount,star,open_time,max_amount_preday', 'required'),
            array('member_id,type, status, create_time,category_id,is_recommend,delivery_mini_amount,delivery_fee,max_amount_preday,delivery_start_amount', 'numerical',),
        	array('fee', 'numerical', 'integerOnly' => true),
            array('logo, street, lng, lat', 'length', 'max' => 128),
            array('name','length','max'=>20),
            array('member_id', 'length', 'max' => 11),
            array('recommend_side', 'length', 'max' => 64),
            array('max_amount_preday','length','max'=>11),
        	array('fee','length','max'=>2),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('mobile','length','max'=>16),
            array('mobile', 'match', 'pattern' =>'/(^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$)|(^((\(\d{3}\))|(\d{3}\-))?(1[34578]\d{9})$)/', 'message' =>Yii::t('supermarkets', '请填写正确的手机号码或电话号码')),
            array('open_time,delivery_time', 'match', 'pattern' => '/^([0-1][0-9]|[2][0-4]|[0-9]):([0-5][0-9])-([0-1][0-9]|[2][0-4]|[0-9]):([0-5][0-9])$/ ', 'message' =>Yii::t('supermarkets', '请填写正确的时间格式')),
            array('delivery_mini_amount,delivery_fee', 'compare', 'compareValue' => 0, 'operator' => '>=', 'message' => Yii::t('supermarkets', '价格必须是正整数')),
            array('zip_code', 'numerical', 'message' => Yii::t('supermarkets', '邮编是6位数字')),
            array('zip_code', 'length', 'min' => 6, 'max' => 6, 'tooShort' =>Yii::t('supermarkets', '邮编长度为6位数'), 'tooLong' => Yii::t('supermarkets', '邮编长度为6位数')),
            array('name', 'checkName', 'on' => 'create,update'),
//            array('delivery_start_amount', 'compare', 'compareAttribute' => 'delivery_mini_amount', 'operator' => '<'), //零售价必须大于供货价
            array('logo', 'required', 'on' => 'create,update', 'message' => Yii::t('supermarkets', '请选择上传图片'), 'safe'=>true),
//            array('max_amount_preday','checkMoney'),
            array('delivery_mini_amount','checkDeliveryMoney'),
            array('logo', 'file', 'types' => 'jpg,gif,png', 'maxSize' => 1024*1024, 'on' => 'create,update', 'tooLarge' => Yii::t('supermarkets', '文件大于1M，上传失败！请上传小于1M的文件！'), 'allowEmpty' => true, 'safe'=>true),
            array('id, name, member_id, mobile, logo, type, mobile,province_id, city_id, district_id, street, zip_code, lng, lat, status, create_time,category_id,is_delivery,delivery_mini_amount,delivery_fee,delivery_start_amount,star,open_time,max_amount_preday,gai_number,fee,delivery_time', 'safe', 'on' => 'search,update,create'),
            array('referrals_gai_number','match', 'pattern' => '/^GW\d+$/','message'=>Yii::t('supermarkets','请输入正确的盖网号')),
            array('referrals_gai_number','checkReferrals',  'on' => 'create,update'),
         );
    }
    /*
     * 自己不能成为自己的推荐人
     */
public function checkReferrals($attribute, $params){    
     $referrals_id = Member::getByGwNumber($this->$attribute);   
      if ($this->scenario == 'create') {
        if(!empty($referrals_id) && $referrals_id['id'] == yii::app()->user->id){
                $this->addError($attribute, Yii::t('supermarkets', '自己不能成为自己的推荐人'));
          }     
    }
    
    if ($this->scenario == 'update') {
        if(!empty($referrals_id) && $referrals_id['id'] == $this->member_id){
                $this->addError($attribute, Yii::t('supermarkets', '自己不能成为自己的推荐人'));
          } 
    }
}
    /**
     * 最低免费起送金额大于起送金额
     */
    public function checkDeliveryMoney($attribute, $params){
        if($this->$attribute < $this->delivery_start_amount){
             $this->addError($attribute, Yii::t('supermarkets', '最低免费配送金额必须大于起送金额'));
        }
    }

    /**
     * 根据门店id和商家id查询信息
     */
    public function getBySidAndPartner($params){
        if (empty($params) && !is_array($params)) {
            return false;
        }

        $cri = new CDbCriteria();
        $cri->compare('id', $params['sid']);
        $cri->compare('partner_id', $params['partner_id']);

        return self::model()->find($cri);

    }
    /**
     * 门店最大限额
     */
    public function  checkMoney($attribute, $params){
        if($this->$attribute>self::MAX_MONEY){
            $this->addError($attribute, Yii::t('supermarkets', '最大限额为{max}',array('{max}'=>self::MAX_MONEY)));
        }
    }

    /**
     * 检查超市门店名称是否重复
     * @param type $attribute
     * @param type $params
     */
    public function checkName($attribute, $params) {

        if ($this->scenario == 'create') {
            $count = Supermarkets::model()->count('member_id=:member_id AND name=:name', array(':member_id' => $this->member_id, ':name' => $this->$attribute));
            if ($count > 0) {
                $this->addError($attribute, Yii::t('supermarkets', '超市门店名已存在'));
            }
        }

        if ($this->scenario == 'update') {
            $rs = Yii::app()->db->createCommand()
                    ->select('id')
                    ->from('{{supermarkets}}')
                    ->where('member_id=:member_id AND name=:name', array(':member_id' => $this->member_id, ':name' => $this->$attribute))
                    ->queryRow();
            if (!empty($rs) && $rs['id'] != $this->id) {
                $this->addError($attribute, Yii::t('supermarkets', '超市门店名已存在'));
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
            'superGoods' => array(self::HAS_MANY, 'SuperGoods', 'super_id'),
            'supermarketStaffs' => array(self::HAS_MANY, 'SupermarketStaffs', 'super_id'),
            'member' => array(self::BELONGS_TO, 'GwMember', 'member_id'),
        	'partner' => array(self::BELONGS_TO, 'Partners', 'partner_id'),
            'referrals'=>array(self::BELONGS_TO, 'Member', 'referrals_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('supermarkets', 'ID'),
            'name' => Yii::t('supermarkets', '名称'),
            'member_id' => Yii::t('supermarkets', '所属用户'),
            'mobile' => Yii::t('supermarkets', '电话'),
            'logo' => Yii::t('supermarkets', '店铺头像'),
            'type' => Yii::t('supermarkets', '超市类型'),
            'province_id' => Yii::t('supermarkets', '省份'),
            'city_id' => Yii::t('supermarkets', '城市'),
            'district_id' => Yii::t('supermarkets', '区域'),
            'street' => Yii::t('supermarkets', '街道地址'),
            'zip_code' => Yii::t('supermarkets', '邮编'),
            'lng' => Yii::t('supermarkets', '经度'),
            'lat' => Yii::t('supermarkets', '纬度'),
            'status' =>Yii::t('supermarkets', '状态'),
            'create_time' => Yii::t('supermarkets', '申请时间'),
            'category_id' => Yii::t('supermarkets', '店铺分类'),
            'is_delivery' =>  Yii::t('supermarkets', '是否送货上门'),
            'is_recommend' => Yii::t('supermarkets', '是否推荐'),
            'recommend_side' => Yii::t('supermarkets', '推荐位置'),
            'delivery_mini_amount' =>Yii::t('supermarkets', '免费配送最低金额'),
            'delivery_fee' => Yii::t('supermarkets', '送货上门附加服务费'),
            'delivery_start_amount'=>Yii::t('supermarkets', '起送金额'),
            'star' => Yii::t('supermarkets', '五星指数'),
            'open_time' =>Yii::t('supermarkets', '营业时间'),
            'max_amount_preday' => Yii::t('supermarkets', '每日最大营业额'),
            'is_over_amount' => Yii::t('supermarkets', '是否超过每日限额'),
            'gai_number'=>Yii::t('supermarkets', '盖网号'),
        	'fee' => Yii::t('freshMachine', '服务费百分比'),
        	'delivery_time' => Yii::t('freshMachine', '送货时间'),
            'referrals_id'=>Yii::t('freshMachine','推荐人'),
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

        $criteria->compare('t.id', $this->id, true);
        $criteria->compare('t.name', $this->name, true);
        $criteria->compare('t.member_id', $this->member_id, true);
        $criteria->compare('t.mobile', $this->mobile, true);
        $criteria->compare('t.logo', $this->logo, true);
        $criteria->compare('t.type', $this->type);
        $criteria->compare('t.province_id', $this->province_id);
        $criteria->compare('t.is_delivery', $this->is_delivery);
        $criteria->compare('t.is_recommend', $this->is_recommend);
        $criteria->compare('t.city_id', $this->city_id);
        $criteria->compare('t.district_id', $this->district_id);
        $criteria->compare('t.street', $this->street, true);
        $criteria->compare('t.zip_code', $this->zip_code, true);
        $criteria->compare('t.lng', $this->lng, true);
        $criteria->compare('t.lat', $this->lat, true);
        $criteria->compare('t.status', $this->status);
        $criteria->compare('t.create_time', $this->create_time);
        
        $criteria->with = 'partner';
        $criteria->with = 'referrals';
        
        //根据gw查找
        $criteria->join = ' LEFT JOIN  '.Partners::model()->tableName().' AS p ON t.partner_id=p.id ';
        $criteria->compare('p.gai_number', $this->gai_number,true);
        $criteria->order = 't.create_time DESC';
          

//         $partners = Yii::app()->db->createCommand()
// 					        ->select('id')
// 					        ->from(Partners::model()->tableName())
// 					        ->where('gai_number LIKE ":gnum"',array(':gnum'=>$this->gai_number))
// 					        ->queryAll();

//         $ids = array();
//         foreach ($partners as $val){
//         	$ids[] = $val['id'];
//         }
        
//         if(!empty($ids)){
//         	$criteria->addInCondition('t.partner_id', $ids);
//         }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Supermarkets the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function getFirstSuperByMemberId($member_id) {
        return self::model()->find('member_id=:mid', array(':mid' => $member_id));
    }

    public static function getAllSuperByMemberId($member_id) {
        return self::model()->findAll('member_id=:mid', array(':mid' => $member_id));
    }
    
    public function afterSave(){
    	
    	if ($this->isNewRecord) {
    		$stores = new Stores();
    		$stores->stype = Stores::SUPERMARKETS;
    		$stores->target_id = $this->id;
    		$stores->create_time = $this->create_time;
    		$stores->lat = $this->lat;
    		$stores->lng = $this->lng;
    		$stores->status = $this->status;
    		$stores->is_recommend = $this->is_recommend;
    		
    		$stores->save();
    	}else {
    		$sql  =  ' UPDATE  '.Stores::model()->tableName().' SET lat='.$this->lat .' , lng= '.$this->lng.' , status= '.$this->status.' , is_recommend= '.$this->is_recommend.' WHERE stype = '.Stores::SUPERMARKETS.' AND target_id= '.$this->id;
    		$rs  =Yii::app()->db->createCommand($sql)->execute();
    	}
    	
    	parent::afterSave();
    	return true;
    	
    	
    	
    }
    

}
