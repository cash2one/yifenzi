<?php
$parmas_arr = array(
		//未成为合作商家也能访问
		'noPartner' => array(
				'partner/apply',
				'partner/view',
				'partner/sellerSign',
				'partner/update',
		),


    'noLogin' => array(
        'home/index',
        'home/login',
    ),
		
		'stock' => array(
				'maxStock'=>1000000,
		),
		
	'order'=>array(
    	'orderExpireTime'=>600,			//订单未支付超时时间
		'orderUnsendRefundTime'=>3600,			//订单支付后未发货可以手动申请退款的时间
		'orderUnsendAutoRefundTime'=>7200,			//订单支付后未发货超时退款的时间
		'machineAutoCancelTime'=>2000,			//售货机订单自动取消
		'machineUnTakeAutoCancelTime'=>172800,			//售货机订单用户不取货自动取消时间  48小时
// 		'machineUnTakeAutoCancelTime'=>1800,			//测试改成1小时
    ),
		
	'storeDefaultMaxAmountPreday'=>2000,				//门店每日每人最大限额默认值
		
);
return $parmas_arr;
