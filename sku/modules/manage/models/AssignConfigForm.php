<?php

/**
 * sku分配设置模型类
 * @author zehui.hong
 */
class AssignConfigForm extends CFormModel{
    public $skuGaiIncome;       //盖网收益的百分比
    public $skuMemberIncome;        //会员的百分比  
    public $skuMemberReferrals;     //会员推荐者的百分比
    public $skuStoreReferrals;          //店铺推荐者的百分比
    public $skuAgentIncome;         //代理的百分比
    
    public $skuMachineOwenerIncome;         
    public $skuMachineSellerIncome;        
    public $isEnable;
    
    
    //服务费配置
    public $machineDefaultFee = 8;				//售货机默认服务费
    public $freshMachineDefaultFee = 8;		//生鲜机默认服务费
    public $storeDefaultFee = 8;						//门店默认服务费
    
    
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
            array('isEnable,skuGaiIncome, skuMemberIncome, skuMemberReferrals, skuStoreReferrals, skuAgentIncome,skuMachineOwenerIncome,skuMachineSellerIncome,machineDefaultFee,freshMachineDefaultFee,storeDefaultFee','required'),
            array('isEnable,skuGaiIncome, skuMemberIncome, skuMemberReferrals, skuStoreReferrals, skuAgentIncome,skuMachineOwenerIncome,skuMachineSellerIncome,machineDefaultFee,freshMachineDefaultFee,storeDefaultFee', 'numerical'),
			array('skuGaiIncome, skuMemberIncome, skuMemberReferrals, skuStoreReferrals, skuAgentIncome,skuMachineOwenerIncome,skuMachineSellerIncome', 'compare', 'compareValue' => '1', 'operator' => '<'), //零售价必须大于供货价
            array('isEnable,skuGaiIncome, skuMemberIncome, skuMemberReferrals, skuStoreReferrals, skuAgentIncome,skuMachineOwenerIncome,skuMachineSellerIncome', 'match', 'pattern' => '/^\d+(\.\d{1,2}){0,1}$/u',
                'message' => '只能保留两位小数！'),
        		
        	array('machineDefaultFee,freshMachineDefaultFee,storeDefaultFee', 'length', 'max' => 2),
            
        );
    }
    
     public function attributeLabels() {
        return array(
        	'isEnable' => Yii::t('home','是否启用分配'),
            'skuGaiIncome' => Yii::t('home','盖网收益分配比率'),
            'skuMemberIncome' => Yii::t('home','会员分配比率'),
            'skuMemberReferrals' => Yii::t('home','会员推荐者分配比率'),
            'skuStoreReferrals' => Yii::t('home','店铺推荐者分配比率'),
            'skuAgentIncome' => Yii::t('home','代理分配比率'),
        	'skuMachineOwenerIncome' => Yii::t('home','售货机拥有者分配比率'),
        	'skuMachineSellerIncome' => Yii::t('home','售货机销售者分配比率'),
        		
        	'machineDefaultFee' => Yii::t('home','售货机默认服务费百分比'),
        	'freshMachineDefaultFee' => Yii::t('home','生鲜机默认服务费百分比'),
        	'storeDefaultFee' => Yii::t('home','门店默认服务费百分比'),
        );
    }
}
