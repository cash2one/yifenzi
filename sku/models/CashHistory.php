<?php

/**
 *  提现，兑现表 模型
 *  @author zhenjun_xu <412530435@qq.com>
 * The followings are the available columns in table '{{cash_history}}':
 * @property string $id
 * @property string $code
 * @property string $applyer
 * @property string $account_name
 * @property string $bank_name
 * @property string $bank_address
 * @property string $account
 * @property string $score
 * @property string $ratio
 * @property string $money
 * @property string $apply_time
 * @property string $ip
 * @property integer $status
 * @property string $reason
 * @property integer $type
 * @property string $member_id
 * @property string $factorage
 * @property string $symbol
 * @property string $base_price
 */
class CashHistory extends CActiveRecord {

	
	const MONEY_POINT_RADIO=1.0;
	
    //（1兑现，2提现 , 3普通会员提现）
    const TYPE_CASH = 1;
    const TYPE_COMPANY_CASH = 2;
    const TYPE_MEMBER_CASH = 3;
    /**
     * 提现类型
     * @param null $k
     * @return array|null
     */
    public static function getType($k=null)
    {
        $a = array(
            //self::TYPE_CASH =>'代理商',
            self::TYPE_COMPANY_CASH =>'商户',
//            self::TYPE_MEMBER_CASH =>'普通会员',
        );
        return is_numeric($k) ? (isset($a[$k]) ? $a[$k] : null) : $a;
    }
    //0申请中、1已审核、2转账中、3已转账，4失败
    const STATUS_APPLYING = 0;
    const STATUS_CHECKED = 1;
    const STATUS_TRANSFERING = 2;
    const STATUS_TRANSFERED = 3;
    const STATUS_FAIL = 4;

    public $end_score; //搜索用的积分区间
    public $end_time;  //搜索用的时间区间
    public $mobile;  //手机号，用于后台联表搜索
    public $order;  //默认的排序，用户后台搜索

    const ORDER_TIME_ASC = 0;
    const ORDER_TIME_DESC = 1;
    const ORDER_MONEY_ASC = 2;
    const ORDER_MONEY_DESC = 3;
    
    public $exportLimit = 5000; //导出excel的每页数
    public $isExport; // 是否导出excel
    //前台提现使用
    /** @var  float 线下收益 */
    public $offlineMoney;
    /** @var  float 代理收益 */
    public $agentMoney;

    public static function orderShow() {
        return array(
            self::ORDER_TIME_ASC => Yii::t('cashHistory', '申请时间由先到后'),
            self::ORDER_TIME_DESC => Yii::t('cashHistory', '申请时间由后到先'),
            self::ORDER_MONEY_ASC => Yii::t('cashHistory', '申请金额由小到大'),
            self::ORDER_MONEY_DESC => Yii::t('cashHistory', '申请金额由大到小'),
        );
    }

    public static function orderValue($k = null) {
        $arr = array(
            self::ORDER_TIME_ASC => 't.apply_time asc',
            self::ORDER_TIME_DESC => 't.apply_time desc',
            self::ORDER_MONEY_ASC => 't.money asc',
            self::ORDER_MONEY_DESC => 't.money desc',
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    //地区id,用于提现表单验证
    public $province_id;
    public $city_id;
    public $district_id;

    /**
     * 申请状态
     * @param null $k
     * @return array|null
     */
    public static function status($k = null) {
        $arr = array(
            self::STATUS_APPLYING => Yii::t('cashHistory', '申请中'),
//             self::STATUS_CHECKED => Yii::t('cashHistory', '已审核'),
            self::STATUS_TRANSFERING => Yii::t('cashHistory', '转账中'),
            self::STATUS_TRANSFERED => Yii::t('cashHistory', '已转账'),
            self::STATUS_FAIL => Yii::t('cashHistory', '失败'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    /**
     *  审核（0未审核、1已审核）',
     */
    const CHECK_NO = 0;
    const CHECK_YES = 1;

    /**
     * 获取审核状态
     * @param null $k
     * @return array|null
     */
    public static function is_check($k=null)
    {
        $arr = array(
            self::CHECK_NO => Yii::t('cashHistory','未审核'),
            self::CHECK_YES => Yii::t('cashHistory','已审核'),
        );
        if(is_numeric($k)){
            return isset($arr[$k]) ? $arr[$k] : null;
        }else{
            return $arr;
        }
    }

    //是否已经审阅
    const REVIEW_NO = 0;
    const REVIEW_YES = 1;

    /**
     * 是否已经审阅
     * @param null $k
     * @return array|null
     */
    public static function reviewStatus($k = null)
    {
        $arr = array(
            self::REVIEW_NO => Yii::t('cashHistory', '否'),
            self::REVIEW_YES => Yii::t('cashHistory', '是')
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    public function tableName() {
        return '{{cash_history}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('province_id,city_id,district_id', 'required', 'on' => 'enterpriseCash'), //企业提现必选的
            array('applyer, account_name, bank_name,account, score, money,
			 apply_time, ip, status,type, member_id, factorage', 'required'),
            array('money', 'numerical', 'min' => 0, 'tooSmall' => '{attribute} 数值太小'),
            array('account', 'match', 'pattern' => '/^[0-9]*[0-9]*$/'),
            array('status, type', 'numerical', 'integerOnly' => true),
            array('applyer, account_name, bank_name, bank_address, account', 'length', 'max' => 128),
            array('score, money, factorage', 'length', 'max' => 15),
            array('apply_time, ip, member_id', 'length', 'max' => 11),
            array('id, applyer, account_name, bank_name, bank_address, account, score, money,
			 apply_time, ip, status, reason, type, member_id, factorage,
			  end_score,end_time,province_id,city_id,district_id,mobile,order,offlineMoney,agentMoney', 'safe', 'on' => 'search'),
            array('reason,offlineMoney,agentMoney,sku_number,is_review,is_check,code','safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'member' => array(self::BELONGS_TO, 'Member', 'member_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('cashHistory', '主键'),
            'applyer' => Yii::t('cashHistory', '申请人'),
            'account_name' => Yii::t('cashHistory', '账户名'),
            'bank_name' => Yii::t('cashHistory', '银行名称'),
            'bank_address' => Yii::t('cashHistory', '银行地址'),
            'account' => Yii::t('cashHistory', '银行帐号'),
            'score' => Yii::t('cashHistory', '实际扣除积分'),
            'money' => Yii::t('cashHistory', '兑现金额'),
            'apply_time' => Yii::t('cashHistory', '申请时间'),
            'ip' => Yii::t('cashHistory', 'IP'),
            'status' => Yii::t('cashHistory', '状态'), //（1申请中，2转账中，3已转账，4失败）
            'reason' => Yii::t('cashHistory', '失败原因'),
            'type' => Yii::t('cashHistory', '类型'), //（1兑现，2提现）
            'member_id' => Yii::t('cashHistory', '所属商家或会员'),
            'factorage' => Yii::t('cashHistory', '手续费率'),
            'province_id' => Yii::t('cashHistory', '省份'),
            'city_id' => Yii::t('cashHistory', '城市'),
            'district_id' => Yii::t('cashHistory', '地区'),
            'is_review' => Yii::t('cashHistory', '审阅'),
        );
    }

    const MEMBER_ONLINE = 0;
    const MEMBER_OFFLINE = 1;
    public $sku_number;
    /**
     * 提现会员的类型
     * @param null $k
     * @return array|null
     */
    public static function memberCashType($k = null)
    {
        $arr = array(
            ''=>Yii::t('cashHistory', '全部'),
            self::MEMBER_ONLINE => Yii::t('cashHistory', '商城'),
            self::MEMBER_OFFLINE => Yii::t('cashHistory', '盖网通'),
        );
        if(is_numeric($k)){
            return isset($arr[$k]) ? $arr[$k] : null;
        }else{
            return $arr;
        }
    }

    /**
     * 后台兑现、提现搜索
     * @param bool $export 是否导出excel
     * @return CActiveDataProvider|CDbCriteria
     */
    public function search($export=false) {

        $criteria = new CDbCriteria;
        $criteria->compare('t.id',$this->id);
        $criteria->compare('account_name', $this->account_name, true);
        $criteria->compare('bank_name', $this->bank_name, true);
        $criteria->compare('bank_address', $this->bank_address, true);
        $criteria->compare('account', $this->account, true);

        $criteria->compare('t.status', $this->status);
        $criteria->compare('t.is_check', $this->is_check);
        $criteria->compare('type', $this->type );

        if (!empty($this->order)) {
            $criteria->order = self::orderValue($this->order);
        }
        $criteria->compare('account_name', $this->account_name, true);
        $criteria->compare('m.mobile', $this->mobile);

        $dateTime = Tool::searchDateFormat($this->apply_time, $this->end_time);
//        var_dump($dateTime);exit;
        $criteria->compare('apply_time', '>=' . $dateTime['start']);
        $criteria->compare('apply_time', '<' . $dateTime['end']);

        $criteria->compare('m.sku_number', $this->sku_number,true);

        $criteria->select = 't.*,sku_number as member_id,m.mobile';
        $criteria->join = 'left join {{member}} as m on m.id=t.member_id';



        if(!$export){
            return $criteria;
        }else{
            // 导出excel
            $pagination = array();
            if (!empty($this->isExport)) {
                $pagination['pageVar'] = 'page';
                $pagination['pageSize'] = $this->exportLimit;
            }
            return new CActiveDataProvider($this, array(
                'criteria' => $criteria,
                'pagination' => $pagination,
            ));
        }

    }
    
    
	public function searchList($type) {

        $criteria = new CDbCriteria;

        $criteria->compare('account_name', $this->account_name, true);
        $criteria->compare('bank_name', $this->bank_name, true);
        $criteria->compare('bank_address', $this->bank_address, true);
        $criteria->compare('account', $this->account, true);

        $criteria->compare('t.status', $this->status);

        $criteria->compare('type', $type);
        if (!empty($this->order)) {
            $criteria->order = self::orderValue($this->order);
        }
        $criteria->compare('account_name', $this->account_name, true);
        $criteria->compare('m.mobile', $this->mobile);

        $dateTime = Tool::searchDateFormat($this->apply_time, $this->end_time);
        $criteria->compare('apply_time', '>=' . $dateTime['start']);
        $criteria->compare('apply_time', '<' . $dateTime['end']);

        if($type==self::TYPE_CASH){
            $criteria->select = 't.*,sku_number as member_id,m.mobile';
            $criteria->join = 'left join {{member}} as m on m.id=t.member_id';
        }else{
            $criteria->select = 't.*,mi.mobile,m.sku_number as member_id ';
            $criteria->join = ' left join {{enterprise}} as mi on mi.id=t.member_id ';
            $criteria->join .= ' left join {{member}} as m on m.id=mi.member_id ';
        }

        // 导出excel
        $pagination = array();
        if (!empty($this->isExport)) {
            $pagination['pageVar'] = 'page';
            $pagination['pageSize'] = $this->exportLimit;
        }
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => $pagination,
        ));
    }
    
    
    /**
     * 只选取加盟商
     */
	public function searchListFranchisee($type) {

        $criteria = new CDbCriteria;

        $criteria->compare('account_name', $this->account_name, true);
        $criteria->compare('bank_name', $this->bank_name, true);
        $criteria->compare('bank_address', $this->bank_address, true);
        $criteria->compare('account', $this->account, true);

        $criteria->compare('t.status', $this->status);

        $criteria->compare('type', $type);
        if (!empty($this->order)) {
            $criteria->order = self::orderValue($this->order);
        }
        $criteria->compare('account_name', $this->account_name, true);
        $criteria->compare('m.mobile', $this->mobile);

        $dateTime = Tool::searchDateFormat($this->apply_time, $this->end_time);
        $criteria->compare('apply_time', '>=' . $dateTime['start']);
        $criteria->compare('apply_time', '<' . $dateTime['end']);
        

        if($type==self::TYPE_CASH){
            $criteria->select = 't.*,sku_number as member_id,m.mobile';
            $criteria->join = 'left join {{member}} as m on m.id=t.member_id';
        }else{
            $criteria->select = 't.*,m.mobile';
            $criteria->join = ' left join {{enterprise}} as m on m.id=t.member_id ';
        }
        
        
        $criteria->join .= ' left join {{franchisee}} as f on f.member_id=m.member_id ';
        
        $criteria->addCondition("f.member_id>0");
        

        // 导出excel
        $pagination = array();
        if (!empty($this->isExport)) {
            $pagination['pageVar'] = 'page';
            $pagination['pageSize'] = $this->exportLimit;
        }
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => $pagination,
        ));
    }
    

    /**
     * 前台 兑现搜索
     * @param $member_id
     * @return CDbCriteria
     */
    public function searchLog($member_id) {
        $criteria = new CDbCriteria;
        $criteria->addCondition('t.member_id=:member_id and t.type=' . self::TYPE_CASH);
        $criteria->params = array(':member_id' => $member_id);
        $criteria->order = 't.apply_time DESC';
        $criteria->compare('account_name', $this->account_name, true);
        if ($this->score < $this->end_score) {
            $criteria->compare('score', '>=' . $this->score);
            $criteria->compare('score', '<=' . $this->end_score);
        }
        $dateTime = Tool::searchDateFormat($this->apply_time, $this->end_time);
        if ($dateTime['end'] > $dateTime['start']) {
            $criteria->compare('apply_time', '>=' . $dateTime['start']);
            $criteria->compare('apply_time', '<=' . $dateTime['end']);
        }
        $criteria->compare('status', $this->status);
        return $criteria;
    }

    /**
     * 前台 提现 搜索
     * @param $memberId
     * @return CDbCriteria
     */
    public function searchEnterpriseCash($memberId) {
        $criteria = new CDbCriteria;
        $criteria->addCondition('t.member_id='.$memberId);
        $criteria->order = 't.apply_time DESC';
        $criteria->compare('account_name', $this->account_name, true);
//        $criteria->compare('type', self::TYPE_MEMBER_CASH);
        $criteria->addInCondition('type', array(self::TYPE_COMPANY_CASH,  self::TYPE_CASH));
        $dateTime = Tool::searchDateFormat($this->apply_time, $this->end_time);
        if ($dateTime['end'] > $dateTime['start']) {
            $criteria->compare('apply_time', '>=' . $dateTime['start']);
            $criteria->compare('apply_time', '<=' . $dateTime['end']);
        }
        $criteria->compare('status', $this->status);
        return $criteria;
    }
    
      /**
     * 前台 提现 搜索
     * @param $memberId
     * @return CDbCriteria
     */
    public function searchMemberCash($memberId) {
        $criteria = new CDbCriteria;
        $criteria->addCondition('t.member_id='.$memberId);
        $criteria->order = 't.apply_time DESC';
        $criteria->compare('account_name', $this->account_name, true);
        $criteria->compare('type', self::TYPE_MEMBER_CASH);
        $dateTime = Tool::searchDateFormat($this->apply_time, $this->end_time);     
            $criteria->compare('apply_time', '>=' . $dateTime['start']);
            $criteria->compare('apply_time', '<=' . $dateTime['end']);      
        $criteria->compare('status', $this->status);
        return $criteria;
    }

    /**
     * 企业会员上一次提现的记录
     * @param $enterpriseId
     * @return CDbDataReader
     * @deprecated
     *
     */
    public static function lastEnterpriseCash($enterpriseId) {
        return Yii::app()->db->createCommand()
                        ->from('{{cash_history}}')
                        ->order('apply_time DESC')
                        ->where('member_id=' . $enterpriseId)
                        ->queryRow();
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 显示
     * @param $id
     * @param $status
     * @param $action
     * @return string
     */
    public static function showReviewStatus($id, $status,$action='setReview')
    {
        $image = $status ? '/manage/images/tick_circle.png' : '/manage/images/cross_circle.png';
        $string = CHtml::ajaxLink(CHtml::image($image), array('/cashHistory/'.$action), array(
                'type' => 'POST',
                'dataType' => 'json',
                'data' => array(
                    'id' => $id,
                    'status' => $status,
//                    'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken
                ),
                'success' => 'function(res){if(res.status){location.reload();}}'),
            array(
                'style' => 'display:block;width:20px;margin:0 auto;'
            ));
        //只能修改一次
        if($status){
            $string = CHtml::image($image);
        }
        return $string;
    }
    
    /**
     * 获取编码
     */
    public static function getCode(){
    	return date('YmdGis').rand(1000000, 9999999);
    }
    
    /*
     * 根据金额获取积分
     * 
     * 取整数
     * 
     */
    static function getPoint($money=0){
    	$money = $money*1;
    	return floor($money/self::MONEY_POINT_RADIO*100)/100;
    }
    
    /*
     * 根据金额获取积分
    *
    * 取整数
    *
    */
    static function getMoney($point=0){
    	$point = $point*1;
    	return floor($point*self::MONEY_POINT_RADIO*100)/100;
    }
    
}
