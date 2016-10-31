<?php

/**
 * 公有账号模型
 * @author wanyun.liu <wanyun_liu@163.com>
 * 
 * @property string $id
 * @property string $name
 * @property integer $type
 * @property string $city_id
 * @property string $sku_number
 */
class CommonAccount extends CActiveRecord {

    public $maxMoney;
    public $level;
    public $yesterday_amount;
    public $today_amount;

    public function tableName() {
        return '{{common_account}}';
    }

    public function rules() {
        return array(
            array('name, type, sku_number', 'required'),
            array('type', 'numerical', 'integerOnly' => true),
            array('name,sku_number', 'length', 'max' => 128),
            array('city_id', 'length', 'max' => 11),
            array('cash', 'length', 'max' => 18),
            array('name, type, cash, maxMoney,sku_number', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'dis' => array(self::BELONGS_TO, 'Region', 'city_id')
        );
    }

    const TYPE_TOTAL = 1; // 公共总帐户    账户类型9
    const TYPE_GAI_INCOME = 3; // 盖网收益帐户  账户类型6
    
    //SKU公共账户
    const TYPE_GUADAN_BINDING = 10;  //挂单积分池-绑定 账户类型6
    const TYPE_GUADAN_UNBUNDLING = 11;  //挂单积分池-非绑定 账户类型6
    const TYPE_GUADAN_SALE_BINDING = 12;	//出售挂单积分池-绑定 账户类型9
    const TYPE_GUADAN_SALE_UNBUNDLING = 13;	//出售挂单积分池-非绑定 账户类型9
    const TYPE_GUADAN_COST_PAY = 14;		//挂单成本支出账户 账户类型6
    
    const TYPE_GAME_INCOME = 5;	//游戏收益账户 账户类型6

    public static function getType() {
        return array(
            self::TYPE_TOTAL => '公共总帐户',
            self::TYPE_GAI_INCOME => '收益总帐户',
            self::TYPE_GAME_INCOME => '游戏收益账户',
            self::TYPE_GUADAN_BINDING => '挂单积分池-绑定',
            self::TYPE_GUADAN_UNBUNDLING => '挂单积分池-非绑定',
            self::TYPE_GUADAN_SALE_BINDING => '出售挂单积分池-绑定',
            self::TYPE_GUADAN_SALE_UNBUNDLING => '出售挂单积分池-非绑定',
            self::TYPE_GUADAN_COST_PAY => '挂单成本支出账户',
        	
        );
    }

    public static function showType($key) {
        $type = self::getType();
        return isset($type[$key]) ? $type[$key] : '类型不存在';
    }

    public function attributeLabels() {
        return array(
            'id' => '主键',
            'name' => '名称',
            'type' => '类型',
            'city_id' => '地区（省/市/区）',
            'cash' => '金额',
            'sku_number' => '会员编号',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('sku_number', $this->sku_number);



        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => 'id DESC',
            ),
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }



    /**
     * 获取某类型的账户，没有则创建
     * @param int $type 账户类型
     * @param int $accountType  AccountInfo 中的账户类型，是 6 or 9
     * @param string $name
     * @param int $targetId
     * @param bool $isTrans 是否使用事务
     * @return array
     */
    public static function getAccount($type, $accountType, $name = '', $targetId = 0, $isTrans = false) {
        $array = array('type' => $accountType);
        $commonAccount = Yii::app()->db->createCommand()->select()->from('{{common_account}}')
                        ->where('type=:type AND city_id=:cid', array(':type' => $type, ':cid' => $targetId))->queryRow();
        $accountName = $name ? $name . (self::showType($type)) : (self::showType($type));
        if (empty($commonAccount)) {
            $skuNumber = self::generateNumber();
            Yii::app()->db->createCommand()->insert('{{common_account}}', array(
                'type' => $type, 'city_id' => $targetId, 'name' => $accountName, 'sku_number' => $skuNumber));
            $array['account_id'] = Yii::app()->db->lastInsertID;
            $array['sku_number'] = $skuNumber;
        } else {
            $array['account_id'] = $commonAccount['id'];
            $array['sku_number'] = $commonAccount['sku_number'];
        }
        $balanceInfo = AccountBalance::findRecord($array, $isTrans);
        return $balanceInfo;
    }

    /**
     * 生成gw号
     * @param int|\type $length
     * @return type
     */
    public static function generateNumber($length = 7) {
        $chars = '0123456789';
        $number = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++)
            $number .= $chars[mt_rand(0, $max)];
        $res = Yii::app()->db->createCommand()->select('id')->from('{{common_account}}')
                        ->where('sku_number=:sk', array(':sk' => 'SK' . $number))->queryScalar();
        if ($res)
            return self::generateNumber($length);
        return 'SK' . $number;
    }

    /**
     * 获取总账户
     */
    public static function getTotalAccount() {
        return self::getAccount(CommonAccount::TYPE_TOTAL, AccountInfo::TYPE_TOTAL);
    }

    /**
     * 获取游戏收益账户
     */
    public static function getGameAccount(){
        return self::getAccount(CommonAccount::TYPE_GAME_INCOME, AccountInfo::TYPE_COMMON);
    }
}
