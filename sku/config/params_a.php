<?php

return array(
    'noToken' => array(
        'cMember/login',
    ),
		//盖掌柜
		'noTokenPartner' => array(
			'pMember/login',
                     'pMember/register',
			'pXiaoer/login',
                    'pXiaoer/logout'
		),
		
		
		//盖掌柜  店小二有权限的操作
		'xiaoerPartnerRights' => array(
				'pOrder/list',
				'pOrder/detail',
				'pOrder/send',
				'pOrder/complete',
				'pOrder/cancel',
				'pOrder/cancelPart',
				
				'pStore/list',
				'pStore/cateList',
				'pStore/view',
				
				'pGoods/list',
				'pGoods/storeGoodsCateList',
				'pGoods/storeGoodsList',
				'pGoods/updateStocks',
				'pGoods/enable',
				'pGoods/barcodeGoods',
				'pGoods/disable',
				'pGoods/changePrice',
				'pGoods/productList',
				'pGoods/productInfo',
				'pGoods/goodsCateList',
				'pGoods/sysCates',
				'pGoods/barcodeGoodsInfo',
				'pGoods/multEnableStoreGoods',
		),
		
		'orderUnpayInvalidTime'=>600,		//订单未支付失效时间
		
		
);
