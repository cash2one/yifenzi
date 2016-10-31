<?php

/**
 *  会员银行账号 模型
 *  @author zhenjun_xu <412530435@qq.com>
 * The followings are the available columns in table '{{bank_account}}':
 * @property string $id
 * @property string $member_id
 * @property string $account_name
 * @property string $province_id
 * @property string $city_id
 * @property string $district_id
 * @property string $street
 * @property string $bank_name
 * @property string $account
 * @property string $licence_image
 *
 */
class BankAccount extends CActiveRecord {
    const DEFAULT_NO = 0;
    const DEFAULT_YES = 1;
    
    const STATUS_NO = 0;//审核中
    const STATUS_PASS = 1;//审核通过
    const STATUS_NOT_PASS = 2;//审核不通过
    
    //自动审核状态
    const AUTO_STATUS_PASS = 1;//审核通过
    const AUTO_STATUS_NOT_PASS = 0;//审核不通过
    
    const BANKS = '中国工商银行,招商银行,中国农业银行,中国银行,中国建设银行,中国邮政储蓄银行,中国光大银行,中信银行,交通银行,兴业银行,浦发银行,华夏银行,深圳发展银行,广东发展银行,中国民生银行,平安银行,中国农业发展银行,中国银联';

    public function tableName() {
        return '{{bank_account}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('account_name, bank_name, account,cardno', 'required', 'on' => 'insert'),
            array('account_name, bank_name, account', 'required', 'on' => 'enterpriseLog,enterpriseLog2'),
            array('licence_image','required','on'=>'enterpriseLog'),
            array('account_name, bank_name, account', 'required', 'on' => 'update'),
            //不做限制了
//                        array('account_name','match','pattern'=>'/([\s]+)/','message'=>'含有非法字符'),
//            array('bank_name', 'match', 'pattern' => '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u'),
//            array('bank_name', 'match', 'pattern' => '/[\(|（][\s|\S]+[\)|）]/'),  //匹配含有括号、带中文输入法括号的字符  
//            array('account_name', 'match', 'pattern' => '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u'),
//            array('account_name', 'match', 'pattern' => '/[\(|（][\s|\S]+[\)|）]/'),
            array('account', 'match', 'pattern' => '/^[0-9]*[0-9]*$/'),
            array('member_id', 'length', 'max' => 11),
            array('account_name, bank_name, account', 'length', 'max' => 128),
            array('licence_image,province_id,city_id,district_id,status,cardno,auto_status', 'safe'),
            array('id, member_id, account_name,  bank_name, account,licence_image,status,cardno,auto_status', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'Member' => array(self::BELONGS_TO, 'Member', 'member_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('bankAccount', '主键'),
            'member_id' => Yii::t('bankAccount', '所属会员'),
            'account_name' => Yii::t('bankAccount', '银行开户名'),
            'province_id' => Yii::t('bankAccount', '省份'),
            'city_id' => Yii::t('bankAccount', '城市'),
            'district_id' => Yii::t('bankAccount', '区/县'),
//			'street' => Yii::t('bankAccount','详细地址'),del
            'bank_name' => Yii::t('bankAccount', '开户银行支行名称'),
            'account' => Yii::t('bankAccount', '公司银行账号'),
            'licence_image' => Yii::t('bankAccount', '开户银行许可证电子版'),
        	'status' => Yii::t('bankAccount', '状态'),
        	'cardno' => Yii::t('bankAccount', '身份证号'),
//			'sister_bank_number' => Yii::t('bankAccount','支行联行号'), delete
        );
    }

    public function search() {

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('member_id', $this->member_id, true);
        $criteria->compare('account_name', $this->account_name, true);
        $criteria->compare('province_id', $this->province_id, true);
        $criteria->compare('city_id', $this->city_id, true);
        $criteria->compare('district_id', $this->district_id, true);
        $criteria->compare('street', $this->street, true);
        $criteria->compare('bank_name', $this->bank_name, true);
        $criteria->compare('account', $this->account, true);
        $criteria->compare('status', $this->status, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20, //分页
            ),
            'sort' => array(
            //'defaultOrder'=>' DESC', //设置默认排序
            ),
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    
    /**
     * 获取银行卡信息
     * @param unknown $code
     * @return Ambigous <boolean, multitype:unknown Ambigous <> >
     */
    static function getBankCardInfo($code){
    	Yii::import('ext.BankHelper');
    	return BankHelper::cardInfo($code);
    }
    

    /**
     * 获取银行卡列表
     */
    static function getBankList(){
    	Yii::import('ext.BankHelper');
    	return BankHelper::bankList();
    }
    /**
     * 审核状态
     * @param null $k
     * @return array|null
     */
    public static function getType($k = null) {
        $arr = array(
            1 => Yii::t('site', '储蓄卡'),
            2=> Yii::t('site','信用卡'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }
    
    /**
     * 审核状态
     * @param null $k
     * @return array|null
     */
    public static function status($k = null) {
    	$arr = array(
    			self::STATUS_NO => Yii::t('site', '审核中'),
    			self::STATUS_PASS=> Yii::t('site','审核通过'),
    			self::STATUS_NOT_PASS => Yii::t('site','审核不通过'),
    	);
    	return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }
    
    /**
     * 审核状态
     * @param null $k
     * @return array|null
     */
    public static function autoStatus($k = null) {
    	$arr = array(
    			self::AUTO_STATUS_PASS=> Yii::t('site','自动认证通过'),
    			self::AUTO_STATUS_NOT_PASS => Yii::t('site','未通过自动认证'),
    	);
    	return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }
    
}
