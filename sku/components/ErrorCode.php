<?php
/**
 * 获取各个api接口参数的工具类
 * 
 * @author leo8705
 */
class ErrorCode extends CComponent{

	//通用错误代码
	const COMMOM_ERROR = 9001;																		//参数有误;
	const COMMOM_ENCRYPT_CODE_ERROR = 9002;											//校验码错误
	const COMMOM_SYS_ERROR = 9003;																//系统错误
	const COMMOM_UNKNOW= 9004;																	//未知错误
	const COMMON_PARAMS_LESS= 9005;															//缺少参数
	const COMMON_NORMAL= 9006;																	//操作逻辑错误
	
	//库存相关错误代码
	const GOOD_STOCK_EXIST = 1001;																	//记录已存在
	const GOOD_STOCK_DATA_ERROR= 1002;														//数据错误
	const GOOD_STOCK_UPDATE_ERROR= 1003;													//更新失败
	const GOOD_STOCK_NOT_EXIST = 1004;															//记录不存在
	const GOOD_STOCK_NOT_ENOUGH = 1005;													//库存不足
	const GOOD_FROZEN_STOCK_NOT_ENOUGH = 1006;										//冻结库存不足
	
	
	const CLIENT_NO_TOKEN= 2001;																		//	缺少token值
	const CLIENT_NO_MEMBER= 2002;																	//	用户不存在
	const CLIENT_TOKEN_ERROR= 2003;																//	TOKEN错误
	const CLIENT_NO_ACCESS= 2004;																		//	没有权限
	
	
	const ORDER_UNEXCIT= 3001;																			//	订单不存在
	const ORDER_PAYED= 3002;																				//	订单已支付
	const ORDER_SIGN_FAIL= 3003;																		//	记录订单签收流水失败
	const ORDER_STATUS_FAIL= 3004;																	//	记录状态不对应
	const ORDER_CANCEL_FAIL= 3005;																	//	记录订单取消流水失败
	const ORDER_SHIPPING_TYPE_ERROR= 3006;													//	订单配送方式错误
	const ORDER_SHIPPING_ADDRESS_ERROR= 3007;											//	订单配送地址错误
	const ORDER_OVER_MAX_AMOUNT_PREDAY_ERROR= 3008;						//	当前消费金额超过每日最大限额
	const ORDER_CREATE_ERROR= 3009;																//	订单创建失败
	const ORDER_UPDATE_ERROR= 3010;																//	订单创建失败
	const ORDER_GOODS_LESS= 3011;																	//	订单创建失败
	const ORDER_MEMBER_ERROR= 3012;															//	订单用户错误
	const ORDER_AMOUNT_LESS_THEN_DELIVERY_START_AMOUNT= 3013;		//	订单金额小于起送金额
	
	const PARTNER_ACCOUNT_UNPASS= 4001;																//	商户未通过审核
	const PARTNER_NO_ACCOUNT= 4002;																//	商户不存在
	const STORE_NO_EXIST= 4003;																//	店铺、售货机不存在
	
	
	const PREASON_AUTH_NOT_EXCITES= 5001;												//	未提交个人认证
	const SAVE_DATA_FALSE  = 5002;                                                      //保存失败
	
	
	static function getErrorStr($code=null){
		$arr = array(
			self::COMMOM_ERROR											=>Yii::t('errorCode', '参数有误'),
			self::COMMOM_ENCRYPT_CODE_ERROR				=>Yii::t('errorCode', '校验码错误'),
			self::COMMOM_SYS_ERROR									=>Yii::t('errorCode', '系统错误'),
			self::COMMOM_UNKNOW										=>Yii::t('errorCode', '未知错误'),
			self::COMMON_PARAMS_LESS								=>Yii::t('errorCode', '缺少参数'),
			self::COMMON_NORMAL										=>Yii::t('errorCode', '操作逻辑错误'),
			self::GOOD_STOCK_EXIST										=>Yii::t('errorCode', '记录已存在'),
			self::GOOD_STOCK_DATA_ERROR							=>Yii::t('errorCode', '数据错误'),
			self::GOOD_STOCK_UPDATE_ERROR						=>Yii::t('errorCode', '库存不足或同步失败'),
			self::GOOD_STOCK_NOT_EXIST								=>Yii::t('errorCode', '商品或库存不存在'),
			self::GOOD_STOCK_NOT_ENOUGH						=>Yii::t('errorCode', '库存不足'),
			self::GOOD_FROZEN_STOCK_NOT_ENOUGH			=>Yii::t('errorCode', '冻结库存不足'),
				
			self::CLIENT_NO_TOKEN											=>Yii::t('errorCode', '缺少token值'),
			self::CLIENT_NO_ACCESS										=>Yii::t('errorCode', '非法操作，没有权限'),
				
				
			self::ORDER_UNEXCIT												=>Yii::t('errorCode', '订单不存在'),
			self::ORDER_PAYED												=>Yii::t('errorCode', '订单已支付'),
			self::ORDER_SIGN_FAIL											=>Yii::t('errorCode', '记录订单签收流水失败'),
			self::ORDER_STATUS_FAIL										=>Yii::t('errorCode', '订单状态不对应'),
			self::ORDER_CANCEL_FAIL										=>Yii::t('errorCode', '记录订单取消流水失败'),
			self::ORDER_SHIPPING_TYPE_ERROR					=>Yii::t('errorCode', '订单配送方式错误'),
			self::ORDER_SHIPPING_ADDRESS_ERROR			=>Yii::t('errorCode', '订单配送地址错误'),
			self::ORDER_OVER_MAX_AMOUNT_PREDAY_ERROR		=>Yii::t('errorCode', '当前消费金额超过每日最大限额'),
			self::ORDER_CREATE_ERROR									=>Yii::t('errorCode', '订单创建失败'),
			self::ORDER_GOODS_LESS										=>Yii::t('errorCode', '订单内部分商品已失效'),
			self::ORDER_MEMBER_ERROR								=>Yii::t('errorCode', '订单用户错误，不能购买自己店铺的商品'),
				
			self::PARTNER_ACCOUNT_UNPASS						=>Yii::t('errorCode', '商户未通过审核'),
			self::PARTNER_NO_ACCOUNT						=>Yii::t('errorCode', '商户不存在'),
			self::STORE_NO_EXIST						=>Yii::t('errorCode', '店铺、售货机不存在'),
			self::ORDER_AMOUNT_LESS_THEN_DELIVERY_START_AMOUNT						=>Yii::t('errorCode', '订单金额小于起送金额'),
				
			self::PREASON_AUTH_NOT_EXCITES						=>Yii::t('errorCode', '未提交个人认证'),
			self::SAVE_DATA_FALSE						=>Yii::t('errorCode', '保存失败'),
		);
		
		return $code===null?$arr:(isset($arr[$code])?$arr[$code]:Yii::t('errorCode', '未知代码'));
		
	}
	
	
	
	
}

?>