<?php

/**
 * This is the model class for table "{{partners}}".
 *
 * The followings are the available columns in table '{{partners}}':
 * @property string $id
 * @property string $name
 * @property string $member_id
 * @property string $mobile
 * @property string $head
 * @property integer $type
 * @property integer $province_id
 * @property integer $city_id
 * @property integer $district_id
 * @property string $street
 * @property string $zip_code
 * @property integer $status
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property GwMember $member
 */
class Partners extends CActiveRecord {

    protected $partner_cache_path = 'PAPICACHE_PARTNER';
    
    public $bank_province_id;
    public $bank_city_id;
    public $bank_district_id;
    
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{partners}}';
    }

    //状态
    const STATUS_APPLY = 0;   //申请
    const STATUS_ENABLE = 1;  //审核通过
    const STATUS_DISABLE = 2; //禁用
    const STATUS_UNPASS = 3;  //审核不通过

    public static function getStatus($key = null) {
        $data = array(
            self::STATUS_APPLY => Yii::t('partner', '申请中'),
            self::STATUS_ENABLE => Yii::t('partner', '审核通过'),
            self::STATUS_DISABLE => Yii::t('partner', '禁用'),
            self::STATUS_UNPASS => Yii::t('partner', '审核不通过'),
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
            array('name,zip_code, create_time, province_id, city_id, district_id,street,mobile', 'required'),
            array('bank_province_id','required','on'=>'sellerSign','message'=>Yii::t('partner','省份不能为空')),
            array('bank_city_id','required','on'=>'sellerSign','message'=>Yii::t('partner','城市不能为空')),
            array('bank_district_id','required','on'=>'sellerSign','message'=>Yii::t('partner','县区不能为空')),
            array('bank_account,bank_card_img,bank_name,bank_account_branch,idcard,idcard_img_font,idcard_img_back,bank_account_name,bank_area', 'required','on'=>'update'),
        	array('bank_account,bank_card_img,bank_name,bank_account_branch,idcard,idcard_img_font,idcard_img_back', 'required','on'=>'sellerSign'),
            array('type, status, create_time,bank_account,bank_province_id, bank_city_id, province_id, city_id, district_id,member_id', 'numerical', 'integerOnly' => true),
            array('head, street,bank_card_img,bank_account_branch,idcard_img_font,idcard_img_back,license_img,meat_inspection_certificate_img,health_permit_certificate_img,food_circulation_permit_certificate_img,stock_source_certificate_img', 'length', 'max' => 128),
            array('name', 'length', 'max' => 20),
        	array('bank_account,bank_account_name,gai_number', 'length', 'max' => 32),
        	array('bank_name', 'length', 'max' => 64),
        	array('idcard', 'length', 'max' => 18),
            array('apply_remark,bank_area', 'length', 'max' => 256),
            array('member_id,bank_province_id, bank_city_id, bank_district_id, province_id, city_id, district_id', 'length', 'max' => 11),
            array('mobile, zip_code', 'length', 'max' => 16),
           array('mobile', 'match', 'pattern' => '/(^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$)|(^((\(\d{3}\))|(\d{3}\-))?(1[34578]\d{9})$)/', 'message' => Yii::t('partner', '请填写正确的手机号码或电话号码')),
            array('name,mobile,gai_number', 'unique'),
            array('zip_code', 'match', 'pattern' => '/^[1-9]\d{5}$/'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('head', 'file', 'types' => 'jpg,gif,png', 'maxSize' => 1024 * 1024, 'on' => 'create,update', 'tooLarge' => Yii::t('partner', '文件大于1M，上传失败！请上传小于1M的文件！'), 'allowEmpty' => true, 'safe' => true),
            array('head', 'required', 'on' => 'create,update', 'message' => Yii::t('partner', '请选择上传图片'), 'safe' => true),
            array('head,bank_card_img,idcard_img_font,idcard_img_back,license_img,meat_inspection_certificate_img,health_permit_certificate_img,food_circulation_permit_certificate_img,stock_source_certificate_img', 'file', 'types' => 'jpg,gif,png', 'maxSize' => 1024 * 1024, 'on' => 'create,update', 'tooLarge' => Yii::t('partner', '文件大于1M，上传失败！请上传小于1M的文件！'), 'allowEmpty' => true, 'safe' => true,'on'=>'sellerSign'),
            array('head,bank_card_img,idcard_img_font,idcard_img_back', 'required', 'on' => 'create,update', 'message' => Yii::t('partner', '请选择上传图片'), 'safe' => true,'on'=>'sellerSign'),
//            array('head', 'file', 'types' => 'jpg,gif,png', 'maxSize' => 1024*1024, 'on' => 'create,update', 'tooLarge' => Yii::t('member', '文件大于1M，上传失败！请上传小于1M的文件！'), 'allowEmpty' => true, 'safe'=>true),
            array('id, name, member_id, mobile, head, type, province_id, city_id, district_id, street, zip_code, status, create_time,apply_remark,gai_number,bank_account,bank_card_img,bank_name,bank_account_branch,idcard,idcard_img_font,idcard_img_back,bank_province_id, bank_city_id, bank_district_id,bank_area,license_img,license_expired_time,meat_inspection_certificate_img,meat_inspection_expired_time,health_permit_certificate_img,health_permit_expired_time,food_circulation_permit_certificate_img,food_circulation_expired_time,stock_source_certificate_img,stock_source_expired_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'member' => array(self::BELONGS_TO, 'Member', 'member_id'),
             'operatorRelation' => array(self::HAS_ONE, 'OperatorRelation', 'partner_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('partner', 'ID'),
            'name' => Yii::t('partner', '名称'),
            'member_id' => Yii::t('partner', '所属用户'),
            'gai_number' => Yii::t('partner', '盖网号'),
            'mobile' => Yii::t('partner', '电话'),
            'head' => Yii::t('partner', '商家头像'),
            'type' => Yii::t('partner', '合作类型 1为企业  2为个人'),
            'province_id' => Yii::t('partner', '省份'),
            'city_id' => Yii::t('partner', '城市'),
            'district_id' =>Yii::t('partner', '区域'),
            'street' => Yii::t('partner', '街道地址'),
            'zip_code' => Yii::t('partner', '邮编'),
            'status' => Yii::t('partner', '状态'),
            'create_time' => Yii::t('partner', '创建时间'),
            'apply_remark' => Yii::t('partner', '申请备注'),
        	'bank_area' => Yii::t('partner', '银行所在地'),
        	'bank_account' => Yii::t('partner', '银行卡号'),
        	'bank_account_name' => Yii::t('partner', '银行卡账户名'),
        	'bank_card_img' => Yii::t('partner', '银行卡正面照片'),
        	'bank_name' => Yii::t('partner', '银行名称'),
        	'bank_account_branch' => Yii::t('partner', '开户支行名称'),
        	'idcard' => Yii::t('partner', '身份证号'),
        	'idcard_img_font' => Yii::t('partner', '身份证正面照片'),
        	'idcard_img_back' => Yii::t('partner', '身份证反面照片'),
        	'is_enterprise' => Yii::t('partner', '是否盖象商家'),
            'license_img' => Yii::t('partner', '营业执照'),
            'license_expired_time' => Yii::t('partner', '营业执照过期时间'),
            'meat_inspection_certificate_img' => Yii::t('partner', '肉菜检验证明'),
            'meat_inspection_expired_time' => Yii::t('partner', '肉菜检验证明过期时间'),
            'health_permit_certificate_img' => Yii::t('partner', '卫生许可证明'),
            'health_permit_expired_time' => Yii::t('partner', '卫生许可证明过期时间'),
            'food_circulation_permit_certificate_img' => Yii::t('partner', '食品流通许可证明'),
            'food_circulation_expired_time' => Yii::t('partner', '食品流通许可证明过期时间'),
            'stock_source_certificate_img' => Yii::t('partner', '进货来源证明'),
            'stock_source_expired_time' => Yii::t('partner', '进货来源证明过期时间'),
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

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('member_id', $this->member_id, true);
        $criteria->compare('gai_number', $this->gai_number, true);
        $criteria->compare('mobile', $this->mobile, true);
        $criteria->compare('head', $this->head, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('province_id', $this->province_id);
        $criteria->compare('city_id', $this->city_id);
        $criteria->compare('district_id', $this->district_id);
        $criteria->compare('street', $this->street, true);
        $criteria->compare('zip_code', $this->zip_code, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('create_time', $this->create_time);
        $criteria->order='id DESC';
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Partners the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function afterSave() {
        parent::afterSave();
// 		PartnerToken::destoryToken($this->member_id);
        //清楚盖掌柜的商家缓存信息
        Tool::cache($this->partner_cache_path)->set($this->member_id, $this);
        return true;
    }

    //获取SKU号ID编号
    public static function getIDByGW($skuNumber){
        $result = Yii::app()->db->createCommand()->select("id,member_id")->from("{{partners}}")
            ->where('gai_number=:gai_number and status=:status',array(':gai_number'=>$skuNumber,':status'=>self::STATUS_ENABLE))->queryRow();
        return $result;
    }

    /**
     * @param array $where 条件语句
     * @param string $fields 选择字段
     * @return mixed
     */
    public static function getPartnersInfo($where,$fields = "*"){
        $whereTmp = $whereVal = array();
        $whereStr = '';
        foreach($where as $key =>$value){
            $whereTmp[] = $key.'=:'.$key;
            $whereVal[':'.$key] = $value;
        }
        if(count($whereTmp) > 1) {
            $whereStr = implode(' and ',$whereTmp);
        }else{
            $whereStr = $whereTmp[0];
        }
        $result = Yii::app()->db->createCommand()->select($fields)->from("{{partners}}")->where($whereStr,$whereVal)->queryRow();
        return $result;
    }
    
    /**
     * 查询运营方
     */
    public static function OperatorRelation($pid){
        $operatorRelation = OperatorRelation::model()->find('partner_id = :pid',array(':pid'=>$pid));
        $operator_partner_id = $operatorRelation['operator_partner_id'];
        $partner = self::model()->findByPk($operator_partner_id);
        $gai_number = $partner['gai_number'];
        if($gai_number){
        return $gai_number;
        }else{
            $gai_number = '暂无';
            return $gai_number;
        }
    }
}
