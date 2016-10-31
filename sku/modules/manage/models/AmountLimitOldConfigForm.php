<?php

/**
 * sku支付限额
 * @author zehui.hong
 */
class AmountLimitConfigForm extends CFormModel{
    public $memberPointPayPreStoreLimit;      
    public $memberTotalPayPreStoreLimit;       
    public $isEnable;
    
    const STATUS_ENABLE=1;
    const STATUS_DISABLE=0;
    
    static function getStatus($k =null){
    	$arr = array(
    			self::STATUS_ENABLE =>Yii::t('order','启用'),
    			self::STATUS_DISABLE => Yii::t('order','禁用'),
    	);
    	return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] :null) :$arr;
    }
    
    public function rules(){
        return array(
            array('isEnable,memberPointPayPreStoreLimit, memberTotalPayPreStoreLimit','required'),
            array('isEnable,memberPointPayPreStoreLimit, memberTotalPayPreStoreLimit', 'numerical'),
             array('isEnable,memberPointPayPreStoreLimit, memberTotalPayPreStoreLimit', 'match', 'pattern' => '/^\d+(\.\d{1,2}){0,1}$/u',
                'message' => '只能保留两位小数！'),
        );
    }
    
     public function attributeLabels() {
        return array(
        	'isEnable' => Yii::t('home','是否启用限额'),
            'memberPointPayPreStoreLimit' => Yii::t('home','会员每日每店积分支付限额'),
            'memberTotalPayPreStoreLimit' => Yii::t('home','会员每日每店默认总消费限额'),
        );
    }
}
