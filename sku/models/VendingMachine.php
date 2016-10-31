<?php

/**
 * This is the model class for table "{{vending_machine}}".
 *
 * The followings are the available columns in table '{{vending_machine}}':
 * @property integer $id
 * @property string $code
 * @property string $activation_code
 * @property string $name
 * @property integer $status
 * @property integer $is_activate
 * @property string $symbol
 * @property integer $country_id
 * @property integer $province_id
 * @property integer $city_id
 * @property integer $district_id
 * @property string $address
 * @property integer $user_id
 * @property string $user_ip
 * @property integer $member_id
 * @property string $setup_time
 * @property string $remark
 * @property string $private_key
 * @property string $public_key
 * @property string $create_time
 * @property string $update_time
 */
class VendingMachine extends CActiveRecord {

    public $gai_number;
    
     //最大限额
    const MAX_MONEY = 2000;

    const VSHOPKEEPER = 1;    //掌柜接口版本号
    const OS_TYPE_ANDROID = 1;
    const OS_TYPE_IOS = 2;
    //状态
    const STATUS_APPLY = 0;   //申请
    const STATUS_ENABLE = 1;  //启用
    const STATUS_DISABLE = 2; //禁用

    public static function getStatus($key = null) {
        $data = array(
            self::STATUS_APPLY => Yii::t('vendingMachine', '申请'),
            self::STATUS_ENABLE => Yii::t('vendingMachine', '启用'),
            self::STATUS_DISABLE => Yii::t('vendingMachine', '禁用'),
        );
        return $key === null ? $data : $data[$key];
    }
    
       //是否推荐
    const RECOMMEND_NO = 0;    //不推荐
    const RECOMMEND_YES = 1;   //推荐
    
       public static function getIsRencommend($key = null) {
        $data = array(
            self::RECOMMEND_NO => Yii::t('vendingMachine', '不推荐'),
            self::RECOMMEND_YES => Yii::t('vendingMachine', '推荐'),
        );
        return $key === null ? $data : $data[$key];
    }

    //是否激活
    const IS_ACTIVATE_YES = 1;
    const IS_ACTIVATE_NO = 0;

    public static function getIsActivate($key = null) {
        $data = array(
            self::IS_ACTIVATE_NO => Yii::t('vendingMachine', '否'),
            self::IS_ACTIVATE_YES => Yii::t('vendingMachine', '是'),
            
        );
        return $key === null ? $data : $data[$key];
    }

    //币种类型
    const RENMINBI = "RMB";
    const HONG_KONG_DOLLAR = "HKD";
    const DOLLAR = "USD";
    const EN_DOLLAR = "EN";

    public static function getMoney($key = null) {
        $data = array(
            self::RENMINBI => Yii::t('vendingMachine', '人民币'),
            self::HONG_KONG_DOLLAR => Yii::t('vendingMachine', '港币'),
            self::EN_DOLLAR => Yii::t('vendingMachine', '英镑'),
        );
        return $key === null ? $data : $data[$key];
    }
    
        //是否开启防闪退
    const IS_BACK_YES = 1;
    const IS_BACK_NO =0;
    
       public static function getIsBack($key = null) {
        $data = array(
            self::IS_BACK_NO => Yii::t('freshMachine', '未启用'),
            self::IS_BACK_YES => Yii::t('freshMachine', '已启用'),
        );
        return $key === null ? $data : $data[$key];
    }
    
    /**
     * 获取店铺分类
     */
    public static function getCategory($category_id = null){
        if($category_id ==null){
            return false;
        }
        $model = StoreCategory::model()->find(array('select'=>'name','condition'=>'id=:id','params'=>array(':id'=>$category_id)));
        return $model['name'];
    }
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{vending_machine}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, status,  province_id, city_id, district_id, address, member_id,partner_id,create_time,category_id', 'required'),
            array('status, is_activate,  user_id, member_id,category_id,update_time,fee', 'numerical', 'integerOnly' => true),
            array('max_amount_preday','numerical'),
            array('code', 'length', 'max' => 12),
            array('activation_code,device_id', 'length', 'max' => 50),
            array('thumb,password,lat,lng', 'length', 'max' => 128),
            array('name','length','max'=>15),
            array('symbol', 'length', 'max' => 20),
            array('address', 'length', 'max' => 225),
            array('user_ip', 'length', 'max' => 11),
            array('setup_time, create_time, update_time', 'length', 'max' => 10),
            array('remark', 'length', 'max' => 200),
            array('mobile', 'match', 'pattern' => '/^(13[0-9]|15[7-9]|153|156|18[7-9])[0-9]{8}$/', 'message' =>Yii::t('vendingMachine', '请填写正确的手机号码')),
            array('mobile,activation_code,code','unique'),
            array('max_amount_preday','length','max'=>11),
            array('fee','length','max'=>2),
            
//             array('max_amount_preday','checkMoney'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, code, activation_code, password,name, status, is_activate, symbol, country_id, province_id, city_id, district_id, address, user_id, user_ip, member_id, setup_time, remark, private_key, public_key, create_time, update_time,biz_name,lat,lng,thumb,category_id,device_id,is_recommend,gai_number,max_amount_preday,fee', 'safe', 'on' => 'search'),
             array('name','checkName','on'=>'create,update'),
             array('thumb', 'required', 'on' => 'create,update', 'message' => Yii::t('vendingMachine', '请选择上传图片'),'safe'=>true),
          array('thumb', 'file', 'types' => 'jpg,gif,png','maxSize' => 1024*1024 , 'on' => 'create,update' ,'tooLarge' => Yii::t('vendingMachine', '文件大于1M，上传失败！请上传小于1M的文件！'),'safe'=>true, 'allowEmpty' => true),
        );
    }
    
     /**
     * 门店最大限额
     */
    public function  checkMoney($attribute, $params){
        if($this->$attribute>self::MAX_MONEY){
            $this->addError($attribute, Yii::t('vendingMachine', '最大限额为{max}',array('{max}'=>self::MAX_MONEY)));
        }
    }
    
  /**
     * 检查售货机名称是否重复
     * @param type $attribute
     * @param type $params
     */
    public function checkName($attribute, $params) {
    	
    	if ($this->scenario=='create') {
    		$count = VendingMachine::model()->count('member_id=:member_id AND name=:name',array(':member_id'=>$this->member_id,':name'=>$this->$attribute));
    		if ($count>0) {
    			$this->addError($attribute, Yii::t('vendingMachine', '售货机名已存在'));
    		}
    	}
    	
    	if ($this->scenario=='update') {
    		$rs =	Yii::app()->db->createCommand()
    			->select('id')
		    	->from('{{vending_machine}}')
		    	->where('member_id=:member_id AND name=:name', array(':member_id'=>$this->member_id,':name'=>$this->$attribute))
		    	->queryRow();
    		if (!empty($rs) && $rs['id']!=$this->id) {
    			$this->addError($attribute, Yii::t('vendingMachine', '售货机名已存在'));
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
//             'storeCategory' => array(self::HAS_ONE, StoreCategory, 'id')
            'partner' => array(self::BELONGS_TO, 'Partners', 'partner_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('vendingMachine', 'ID'),
            'code' => Yii::t('vendingMachine', '装机编码，由系统自动生成,12位数字组成'),
            'activation_code' => Yii::t('vendingMachine', '系统生成的激活码'),
            'category_id' => Yii::t('vendingMachine', '店铺分类'),
            'thumb'=>Yii::t('vendingMachine', '缩略图'),
            'name' => Yii::t('vendingMachine', '售货机名称'),
            'status' => Yii::t('vendingMachine', '状态'),
            'is_activate' => Yii::t('vendingMachine', '是否激活（0未激活、1已激活）'),
            'password'=>Yii::t('vendingMachine', '管理密码'),
            'symbol' => Yii::t('vendingMachine', '币种(RMB、HKD)'),
            'country_id' => Yii::t('vendingMachine', '国家'),
            'province_id' => Yii::t('vendingMachine', '省份'),
            'city_id' => Yii::t('vendingMachine', '城市'),
            'district_id' => Yii::t('vendingMachine', '区县'),
            'mobile'=>Yii::t('vendingMachine', '管理员手机'),
            'address' => Yii::t('vendingMachine', '地址'),
            'user_id' => Yii::t('vendingMachine', '管理员id'),
            'user_ip' => Yii::t('vendingMachine', '管理员ip'),
            'device_id'=>Yii::t('vendingMachine', '设备id'),
            'member_id' => Yii::t('vendingMachine', '加盟商id'),
            'setup_time' => Yii::t('vendingMachine', '安装时间'),
            'remark' =>Yii::t('vendingMachine', '备注'),
            'private_key' => Yii::t('vendingMachine', '私钥'),
            'public_key' => Yii::t('vendingMachine', '公钥'),
            'lng' =>Yii::t('vendingMachine', '经度'),
            'lat' =>Yii::t('vendingMachine', '纬度'),
            'is_recommend'=>Yii::t('vendingMachine', '是否推荐'),
            'create_time' =>Yii::t('vendingMachine', '创建时间'),
            'update_time' => Yii::t('vendingMachine', '修改时间'),
            'category_id' => Yii::t('vendingMachine', '店铺分类'),
            'max_amount_preday' => Yii::t('vendingMachine', '每日最大营业额'),
            'is_over_amount' => Yii::t('vendingMachine', '是否超过每日限额'),
            'gai_number'=>Yii::t('vendingMachine', '盖网号'),
        	'fee' => Yii::t('freshMachine', '服务费百分比'),
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
        $criteria->compare('t.code', $this->code, true);
        $criteria->compare('t.activation_code', $this->activation_code, true);
        $criteria->compare('t.name', $this->name, true);
        $criteria->compare('t.status', $this->status);
        $criteria->compare('t.is_activate', $this->is_activate);
        $criteria->compare('t.symbol', $this->symbol, true);
        $criteria->compare('t.country_id', $this->country_id);
        $criteria->compare('t.province_id', $this->province_id);
        $criteria->compare('t.city_id', $this->city_id);
        $criteria->compare('t.district_id', $this->district_id);
        $criteria->compare('t.address', $this->address, true);
        $criteria->compare('t.user_id', $this->user_id);
        $criteria->compare('t.user_ip', $this->user_ip, true);
        $criteria->compare('t.member_id', $this->member_id);
        $criteria->compare('t.setup_time', $this->setup_time, true);
        $criteria->compare('t.remark', $this->remark, true);
        $criteria->compare('t.private_key', $this->private_key, true);
        $criteria->compare('t.public_key', $this->public_key, true);
        $criteria->compare('t.create_time', $this->create_time, true);
        $criteria->compare('t.update_time', $this->update_time, true);
        
        $criteria->with = 'partner';
        
        //根据gw查找
        $criteria->join = ' LEFT JOIN  '.Partners::model()->tableName().' AS p ON t.partner_id=p.id ';
        $criteria->compare('p.gai_number', $this->gai_number,true);
        $criteria->order = 't.create_time DESC';
        $pagination = array();
        if (!empty($this->isExport)) {
            $pagination['pageVar'] = $this->exportPageName;
            $pagination['pageSize'] = $this->exportLimit;
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => $pagination,
        ));
    }

    public function psearch() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('member_id', $this->member_id);

        $pagination = array();
        if (!empty($this->isExport)) {
            $pagination['pageVar'] = $this->exportPageName;
            $pagination['pageSize'] = $this->exportLimit;
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => $pagination,
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
    public function backendSearch() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;
        $criteria->select = "t.id,t.name,t.province_id,t.city_id,t.district_id,f.name as biz_name,r.name as city_name";
        $criteria->join = "left join gaiwang.gw_franchisee f on f.id = t.member_id";
        $criteria->join.= " left join gaiwang.gw_region r on r.id = t.province_id";

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.code', $this->code, true);
        $criteria->compare('t.activation_code', $this->activation_code, true);
        $criteria->compare('t.name', $this->name, true);
        $criteria->compare('t.status', $this->status);
        $criteria->compare('t.is_activate', $this->is_activate);
        $criteria->compare('t.symbol', $this->symbol, true);
        $criteria->compare('t.country_id', $this->country_id);
        $criteria->compare('t.province_id', $this->province_id);
        $criteria->compare('t.city_id', $this->city_id);
        $criteria->compare('t.district_id', $this->district_id);
        $criteria->compare('t.address', $this->address, true);
        $criteria->compare('t.user_id', $this->user_id);
        $criteria->compare('t.user_ip', $this->user_ip, true);
        $criteria->compare('t.member_id', $this->member_id);
        $criteria->compare('t.setup_time', $this->setup_time, true);
        $criteria->compare('t.remark', $this->remark, true);
        $criteria->compare('t.private_key', $this->private_key, true);
        $criteria->compare('t.public_key', $this->public_key, true);
        $criteria->compare('t.create_time', $this->create_time, true);
        $criteria->compare('t.update_time', $this->update_time, true);

        $criteria->compare('f.name', $this->biz_name, true);

        $pagination = array();
        if (!empty($this->isExport)) {
            $pagination['pageVar'] = $this->exportPageName;
            $pagination['pageSize'] = $this->exportLimit;
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => $pagination,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Shopkeeper the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function afterSave(){
    	if ($this->isNewRecord) {
    		$stores = new Stores();
    		$stores->stype = Stores::MACHINE;
    		$stores->target_id = $this->id;
    		$stores->create_time = $this->create_time;
    		$stores->lat = $this->lat;
    		$stores->lng = $this->lng;
    		$stores->status = $this->status;
    		$stores->is_recommend = $this->is_recommend;
    		$stores->save();
    	}else {
	    	$sql  =  ' UPDATE  '.Stores::model()->tableName().' SET lat='.$this->lat .' , lng= '.$this->lng.' , status= '.$this->status.' , is_recommend= '.$this->is_recommend.' WHERE stype = '.Stores::MACHINE.' AND target_id= '.$this->id;
	    	$rs  =Yii::app()->db->createCommand($sql)->execute();
    	
    	}
    	parent::afterSave();
    	return true;
    	
    }

}
