<?php
$config = array(
    '版本管理' => array(
        '列表' => 'Manage.AppVersion.Admin',
        '添加' => 'Manage.AppVersion.Create',
        '编辑' => 'Manage.AppVersion.Update',
        '删除' => 'Manage.AppVersion.Delete',
    ),
    '三国跑跑' => array(
        '编辑概率表' => 'Manage.GameConfig.MultipleConfig',
        '编辑房间表' => 'Manage.GameConfig.RoomConfig',
    ),
    '啪啪萌僵尸' => array(
        '编辑配置表' => 'Manage.GameConfig.PaipaimengConfig',
    ),
    '黄金矿工' => array(
        '编辑配置表' => 'Manage.GameConfig.GoldenConfig',
        '编辑价格表' => 'Manage.GameConfig.MinerConfig',
    ),
    '神偷莉莉' => array(
        '编辑配置表' => 'Manage.GameConfig.ShentouliliConfig',
    ),
    '攀枝花抢水果' => array(
        '游戏店铺列表' => 'Manage.GameStore.Admin',
        '添加游戏店铺' => 'Manage.GameStore.Create',
        '编辑游戏店铺' => 'Manage.GameStore.Update',
        '游戏商品列表' => 'Manage.GameStoreItems.Admin',
        '添加游戏商品' => 'Manage.GameStoreItems.Create',
        '编辑游戏商品' => 'Manage.GameStoreItems.Update',
    ),
    '弹跳公主' => array(
        '编辑配置表' => 'Manage.GameConfig.TantiaogongzhuConfig',
    ),
);
$this->renderPartial('_input', array('config' => $config, 'rights' => $rights));
?>