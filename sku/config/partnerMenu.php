<?php

/**
 * 右侧菜单
 */
return array(

    'partnerManage' => array(
        'name' => Yii::t('home', '商户管理'),
        'class' => 'bartenderMana',
        'children' => array(
            Yii::t('home', '查看资料') => '/partner/partner/view',
            Yii::t('home', '修改资料') => '/partner/partner/update',
        		Yii::t('home', '切换当前操作商家') => '/partner/partner/operChange',
            		Yii::t('home', '店小二') => '/partner/partner/xiaoEr',
        ),
    ),
		
// 	'operManage' => array(
// 			'name' => Yii::t('home', '商家管理'),
// 			'class' => 'productMana',
// 			'children' => array(
// 					Yii::t('home', '切换当前操作商家') => '/partner/partner/operChange',
// 			),
// 	),

    'sellManage' => array(
        'name' => Yii::t('home', '商品管理'),
        'class' => 'productMana',
        'children' => array(
            Yii::t('home', '添加商品') => array(
                'value' => '/partner/goods/create',
                'actions' => array(
                    'goods/create' => Yii::t('home', '添加商品'),
                ),
            ),

            Yii::t('home', '商品列表') => array(
                'value' => '/partner/goods/index',
                'actions' => array(
                    'goods/update' => Yii::t('home', '修改信息'),
                    'goods/delete' => Yii::t('home', '删除商品'),
                )
            ),
            Yii::t('home', '分类管理') => array(
                'value' => '/partner/goodsCategory/index',
                'actions' => array(
                    'goodsCategory/add' => Yii::t('home', '添加分类'),
                    'goodsCategory/update' => Yii::t('home', '更新分类'),
                    'goodsCategory/delete' => Yii::t('home', '删除分类'),
                )
            ),
        ),
    ),


    'cardManage' => array(
        'name' => Yii::t('home', '销售管理'),
        'class' => 'rechargeMana',
        'children' => array(
            Yii::t('home', '订单管理') => array(
                'value' => '/partner/order/index',
                'actions' => array('/partner/order/view' => Yii::t('home', '查看详情')),
            ),
        ),
    ),


    'storeManage' => array(
        'name' => Yii::t('home', '超市门店管理'),
        'class' => 'transMana',
        'children' => array(
            Yii::t('home', '切换当前超市门店') => '/partner/store/change',
            Yii::t('home', '申请超市门店') => '/partner/store/add',
            Yii::t('home', '查看门店信息') => '/partner/store/view',
//						Yii::t('home', '更新门店信息') => '/partner/store/update',
            Yii::t('home', '门店商品管理') => '/partner/storeGoods/index',
            //Yii::t('home', '门店员工管理') => '/partner/storeStaffs/index',
        ),
    ),


    'vmManage' => array(
        'name' => Yii::t('home', '售货机管理'),
        'class' => 'transMana',
        'children' => array(
            Yii::t('home', '申请售货机') => '/partner/machine/create',
            Yii::t('home', '所有售货机') => '/partner/machine/list',
        ),
    ),

    'fmManage' => array(
        'name' => Yii::t('home', '生鲜机管理'),
        'class' => 'transMana',
        'children' => array(
            Yii::t('home', '生鲜机列表') => '/partner/freshMachine/list',
        ),
    ),


//     'assistantManage' => array(
//         'name' => Yii::t('home', '店小二管理'),
//         'class' => 'bartenderMana',
//         'children' => array(
//             Yii::t('home', '店小二列表') => array(
//                 'value' => '/partner/assistant/admin',
//                 'actions' => array(
//                     'assistant/create' => Yii::t('home', '添加'),
//                     'assistant/update' => Yii::t('home', '修改'),
//                     'assistant/delete' => Yii::t('home', '删除'),
//                 ),
//             ),
//             Yii::t('home', '操作日志') => '/partner/partnerLog/index',
//         ),
//     ),


    'gameManage' => array(
        'name' => Yii::t('home', '游戏店铺管理'),
        'class' => 'transMana',
        'children' => array(
            Yii::t('home', '查看游戏店铺') => '/partner/gameStore/view',
        ),
    ),
    
     'gameManage' => array(
        'name' => Yii::t('home', '操作日志'),
        'class' => 'transMana',
        'children' => array(
            Yii::t('home', '操作记录') => '/partner/partner/logView',
        ),
    ),

);


