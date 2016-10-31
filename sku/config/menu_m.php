<?php

return array(
    'userInfo' => array(// 用户信息
        Yii::t('main', '用户信息') => array(
            'url' => '/sub/user',
            'sub' => array(
                Yii::t('main', '修改密码') => '/user/modifyPassword'
            )
        ),
    ),
     'webConfig' => array(// 网站配置
         Yii::t('main', '网站配置管理') => array(
             'url' => '/sub/config',
             'sub' => array(
                 Yii::t('main', '网站配置') => '/home/siteConfig',
                 Yii::t('main', '分配配置') => '/home/assignConfig',
             	 Yii::t('main', '消费限额配置') => '/home/amountLimitConfig',
                 Yii::t('main', '订单时间配置') => '/home/orderExpireTimeConfig',
				 //Yii::t('main', '免密金额设置') => '/home/FreeAmountConfig',
             )
         ),

     ),
    'administrators' => array(// 管理员管理
        Yii::t('main', '管理员管理') => array(
            'url' => '/sub/admin',
            'sub' => array(
                Yii::t('main', '管理员列表') => '/user/admin',
                Yii::t('main', '管理员角色') => 'authItem/admin',
                Yii::t('main', '管理员操作日志') => '/user/log',
            ),
        )
    ),
    
		
	'partners' => array(// 商户管理
			Yii::t('main', '商户管理') => array(
					'url' => '/partner/admin',
					'sub' => array(
							Yii::t('main', '商户列表') => '/partners/admin',
							Yii::t('main', '门店管理') => '/supermarkets/admin',
							Yii::t('main', '售货机管理') => '/vendingMachine/admin',
                            Yii::t('main', '生鲜机机管理') => '/freshMachine/admin',
                                                                                                                           Yii::t('main', '个人认证') => '/memberPersonalAuthentication/admin',
							Yii::t('main', '运营方绑定') => '/operatorBinding/admin',
                                                                                                                           Yii::t('main', '配送员管理') => '/memberPersonalAuthentication/renAdmin',
					),
			),
			Yii::t('main', '订单管理') => array(
			'url' => '/sub/score',
			'sub' => array(
			Yii::t('main', '订单列表') => '/order/admin',
			Yii::t('main', '盖鲜生订单') => '/order/freshAdmin',
			)
			),                                     
	),
		
		
	'goods' => array(// 商品管理
			Yii::t('main', '商品管理') => array(
					'url' => '/goods/admin',
					'sub' => array(
							Yii::t('main', '商品列表') => '/goods/admin',
							Yii::t('main', '商家商品导入') => '/goods/excelImport',
							Yii::t('main', '条码库管理') => '/barcodeGoods/admin',
							Yii::t('main', '条码库商品导入') => '/barcodeGoods/excelImport',
					),
			),
			Yii::t('main', '分类管理') => array(
					'url' => '/sub/score',
					'sub' => array(
							Yii::t('main', '原始商品分类') => '/category/admin',
							Yii::t('main', '店铺分类') => '/storeCategory/admin',
					)
			),Yii::t('main', '录入审核') => array(
					'url' => '/sub/score',
					'sub' => array(
							Yii::t('main', '待审核列表') => '/inputGoods/admin',
							Yii::t('main', '发布管理') => '/inputGoods/release',
					)
			),
            
	),
		
		'appAdvert' => array(// 广告管理
				Yii::t('main', '广告管理') => array(
						'url' => '/goods/admin',
						'sub' => array(
								Yii::t('main', '广告列表') => '/appAdvert/admin',
						),
				),
		),
    'questResult' => array(// 广告管理
        Yii::t('main', '问卷调查管理') => array(
            'url' => '/freshQuestResult/admin',
            'sub' => array(
                Yii::t('main', '问卷列表') => '/freshQuestResult/admin',
            	Yii::t('main', 'sku商户加盟审核') => '/partnerJoinAuditing/index',
            ),
        ),
    ),
    'webData' => array(// 广告管理
        Yii::t('main', '多语言管理') => array(
            'url' => '/home/languageManage',
            'sub' => array(
                Yii::t('main', '多语言-后台') => '/home/languageBackend',
                Yii::t('main', '多语言-商户') => '/home/languagePartner',
                Yii::t('main', '多语言-API') => '/home/languageApi',
                Yii::t('main', 'SKU公共语言包') => '/home/languageSku',
            ),
        ),
    ),
	'rechargeCashManagement' => array(// 充值提现管理
		Yii::t('main', '提现管理') => array(
			'url' => '/sub/cash',
			'sub' => array(
				Yii::t('main', '提现申请单') => '/cashHistory/applyCash',
			)
		),
	),

	'gameConfig' => array( //游戏配置管理
		Yii::t('main', '游戏版本管理') => array(
			'url' => '/sub/appVersion',
			'sub' => array(
				Yii::t('game', '版本管理') => '/appVersion/admin',
			)
		),
		Yii::t('main', '游戏配置管理') => array(
			'url' => '/sub/gameConfig',
			'sub' => array(
				Yii::t('game', '三国跑跑概率表') => '/gameConfig/multipleConfig',
				Yii::t('game','三国跑跑房间表') => '/gameConfig/roomConfig',
				Yii::t('game','啪啪萌僵尸配置表') => '/gameConfig/paipaimengConfig',
				Yii::t('game','黄金矿工价格表') => '/gameConfig/minerConfig',
				Yii::t('game','黄金矿工配置表') => '/gameConfig/goldenConfig',
				Yii::t('game','神偷莉莉配置表') => '/gameConfig/shentouliliConfig',
				Yii::t('game','攀枝花抢水果配置') => '/gameStore/admin',
				Yii::t('game','弹跳公主配置表') => '/gameConfig/tantiaogongzhuConfig',
			),
		),
	),
		'guadan' => array(// 挂单管理
				Yii::t('main', '积分挂单管理') => array(
		            'url' => '/guadan/guadanAdmin',
		            'sub' => array(
		                Yii::t('main', '挂单管理') => '/guadan/guadanAdmin',
		            		Yii::t('main', '售卖管理') => '/guadanCollect/sellAdmin',
		            		Yii::t('main', '积分批发') => '/guadanpifa/admin',
		            		Yii::t('main', '绑定管理') => '/memberBind/index',
		            		Yii::t('main', '日志') => '/guadan/log',
		            ),
		        ),
		),
		
		'tradeManagement' => array(// 交易管理
				Yii::t('main', '帐户余额') => array(
						'url' => '/sub/accountBalance',
						'sub' => array(
								Yii::t('main', '余额列表') => '/accountBalance/admin',
						)
				),
				Yii::t('main', '交易流水') => array(
						'url' => '/sub/accountFlow',
						'sub' => array(
								Yii::t('main', '流水日志') => '/accountFlow/admin',
// 								Yii::t('main', '流水导出') => '/accountFlow/exportMonth',
// 								Yii::t('main', '余额导出') => '/accountFlow/exportBalance',
						)
				),
		),

		#一份子项目后台管理
        'onepartManagement' => array(
            Yii::t('main', '栏目管理') => array(
                'url' => '/onepartManagement/admin',
                'sub' => array(
                    Yii::t('main', '栏目列表') => '/onepartManagement/admin',
                  //  Yii::t('main', '添加栏目') => '/onepartManagement/adds',
                )
            ),
            Yii::t('main', '商品管理') => array(
                'url' => '/onepartGoods/admin',
                'sub' => array(
                    Yii::t('main', '商品列表') => '/onepartGoods/admin',
					Yii::t('main', '最新揭晓') => '/onepartAnnounced/admin',
                )
            ),
			Yii::t('main', '品牌管理') => array(
				'url' =>  '/onepartManagement/admin',
				'sub' => array(
					Yii::t('main', '品牌列表') => '/onepartBrand/admin',
				//	Yii::t('main', '添加品牌') => '/onepartBrand/adds',

				)
			),
			Yii::t('main', '广告管理') => array(
				'url' =>  '/onepartAdvertising/admin',
				'sub' => array(
					Yii::t('main', '广告列表') => '/onepartAdvertising/admin',
				//	Yii::t('main', '添加广告') => '/onepartAdvertising/adds',
				)
			),
			Yii::t('main', '短信通道') => array(
				'url' =>  '/onepartSms/yfzSmsModel',
				'sub' => array(
					Yii::t('main', '短信模板') => '/onepartSms/yfzSmsModel',
					Yii::t('main', '短信发送记录') => '/onepartSms/yfzSendSmsRecord',

				)
			),
             Yii::t('main','订单管理')=> array(
                 'url' => '/onepartOrder/admin',
                 'sub'=> array(
                            Yii::t('main','订单列表')=> '/onepartOrder/admin',
                            Yii::t('main','公事验证')=> '/onepartOrder/check',
                        )
             ),
            Yii::t('main','支付管理')=> array(
                'url' => '/onepartOrder/admin',
                'sub'=> array(
                    Yii::t('main','支付列表')=> '/onepartPay/admin',
                )
            )
        ),
);
