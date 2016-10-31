<?php
/**
 * 游戏脚本
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2016/1/11 18:01
 */
class GameCommand extends CConsoleCommand {
    /**
     * 啪啪萌游戏加体力
     * 条件：体力值不足5，每720秒(12分钟)为玩家加一个体力
     */
    public function actionMain()
    {
        set_time_limit(0);
        Yii::import('application.modules.game.models.GameMemberInfo');
        //获取游戏配置参数
        $config = Tool::getConfigData('paipaimeng', GameMemberInfo::GAME_TYPE_PAIPAIMENG);
        $config = $config[0];
        $interval = 1; //每隔1秒运行
        while (true) {
            try {
                //获取需要更新体力的玩家
                $gameInfo = GameMemberInfo::model()->findAll(array(
                    'condition' => 'game_type = :game_type and power < :power and update_power_time < :update_power_time',
                    'select' => 'id,power',
                    'params' => array(
                        ':game_type' => GameMemberInfo::GAME_TYPE_PAIPAIMENG,
                        ':power' => GameMemberInfo::DEFAULT_MAX_GAME_POWER,
                        ':update_power_time' => time() - $config['refection']
                    ),
                ));
                if (!empty($gameInfo)) {
                    foreach ($gameInfo as $value) {
                        $res = Yii::app()->game->createCommand()->update('{{member_info}}', array(
                            'power' => $value['power'] + 1, 'update_power_time' => time()),"id = '{$value['id']}'");
                        if ($res) echo "success" . "\n";
                    }
                }
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
                Yii::log(GameMemberInfo::getGameName(GameMemberInfo::GAME_TYPE_PAIPAIMENG). ":". $e->getMessage());
            }
            sleep($interval);
        }
    }

    /**
     * 黄金矿工游戏加体力
     * 条件：体力值不足5，每1500秒(25分钟)为玩家加一个体力
     */
    public function actionMiner()
    {
        set_time_limit(0);
        Yii::import('application.modules.game.models.GameMemberInfo');
        //获取游戏配置参数
        $config = Tool::getConfigData('golden', GameMemberInfo::GAME_TYPE_GOLDENMINER);
        $config = $config[0];
        $interval = 1; //每隔1秒运行
        while (true) {
            try {
                //获取需要更新体力的玩家
                $gameInfo = GameMemberInfo::model()->findAll(array(
                    'condition' => 'game_type = :game_type and power < :power and update_power_time < :update_power_time',
                    'select' => 'id,power',
                    'params' => array(
                        ':game_type' => GameMemberInfo::GAME_TYPE_GOLDENMINER,
                        ':power' => GameMemberInfo::DEFAULT_MAX_GAME_POWER,
                        ':update_power_time' => time() - $config['heart_update_time_second']
                    ),
                ));
                if (!empty($gameInfo)) {
                    foreach ($gameInfo as $value) {
                        $res = Yii::app()->game->createCommand()->update('{{member_info}}', array(
                            'power' => $value['power'] + 1, 'update_power_time' => time()),"id = '{$value['id']}'");
                        if ($res) echo "success" . "\n";
                    }
                }
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
                Yii::log(GameMemberInfo::getGameName(GameMemberInfo::GAME_TYPE_GOLDENMINER) . ":". $e->getMessage());
            }
            sleep($interval);
        }
    }

    /**
     * 神偷莉莉游戏加体力
     * 条件：体力值不足5，每720秒(12分钟)为玩家加一个体力
     */
    public function actionSteal()
    {
        set_time_limit(0);
        Yii::import('application.modules.game.models.GameMemberInfo');
        //获取游戏配置参数
        $config = Tool::getConfigData('shentoulili', GameMemberInfo::GAME_TYPE_SHENTOULILI);
        $config = $config[0];
        $interval = 1; //每隔1秒运行
        while (true) {
            try {
                //获取需要更新体力的玩家
                $gameInfo = GameMemberInfo::model()->findAll(array(
                    'condition' => 'game_type = :game_type and power < :power and update_power_time < :update_power_time',
                    'select' => 'id,power',
                    'params' => array(
                        ':game_type' => GameMemberInfo::GAME_TYPE_SHENTOULILI,
                        ':power' => GameMemberInfo::DEFAULT_MAX_GAME_POWER,
                        ':update_power_time' => time() - $config['refection']
                    ),
                ));
                if (!empty($gameInfo)) {
                    foreach ($gameInfo as $value) {
                        $res = Yii::app()->game->createCommand()->update('{{member_info}}', array(
                            'power' => $value['power'] + 1, 'update_power_time' => time()),"id = '{$value['id']}'");
                        if ($res) echo "success" . "\n";
                    }
                }
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
                Yii::log(GameMemberInfo::getGameName(GameMemberInfo::GAME_TYPE_SHENTOULILI) . ":". $e->getMessage());
            }
            sleep($interval);
        }
    }

    /**
     * 弹跳公主游戏增加体力
     * 条件：体力值不足5，每720秒(12分钟)为玩家加一个体力
     */
    public function actionJump()
    {
        set_time_limit(0);
        //获取游戏配置参数
        $config = Tool::getConfigData('tantiaogongzhu', GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU);
        $config = $config[0];
        $interval = 1; //每隔1秒运行
        while (true) {
            try {
                //获取需要更新体力的玩家
                $gameInfo = GameMemberInfo::model()->findAll(array(
                    'condition' => 'game_type = :game_type and power < :power and update_power_time < :update_power_time',
                    'select' => 'id,power',
                    'params' => array(
                        ':game_type' => GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU,
                        ':power' => GameMemberInfo::DEFAULT_MAX_GAME_POWER,
                        ':update_power_time' => time() - $config['refection']
                    ),
                ));
                if (!empty($gameInfo)) {
                    foreach ($gameInfo as $value) {
                        $res = Yii::app()->game->createCommand()->update('{{member_info}}', array(
                            'power' => $value['power'] + 1, 'update_power_time' => time()),"id = '{$value['id']}'");
                        if ($res) echo "success" . "\n";
                    }
                }
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
                Yii::log(GameMemberInfo::getGameName(GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU) . ":". $e->getMessage());
            }
            sleep($interval);
        }
    }

    /**
     * 攀枝花抢水果游戏
     * 条件：活动期间自动更新水果库存
     */
    public function actionFruit(){
        set_time_limit(0);
        Yii::import('application.modules.manage.models.GameStoreItems');
        Yii::import('application.modules.manage.models.GameStore');
        Yii::import('application.modules.game.models.GameItemStock');
        $interval = 1; //每隔1秒运行
        while (true) {
            try{
                $sql = '';
                //商品下架或关闭店铺状态，清空相关商品库存
                $stores = GameStore::model()->findAll(array(
                    'select' => 'id',
                    'condition' => 'store_status = :store_status',
                    'params'=> array(':store_status' => GameStore::STATUS_CLOSE)
                ));
                if(!empty($stores)){
                    foreach($stores as $key => $store){
                        $sql .= "DELETE FROM `gw_game_item_stock` WHERE store_id = '{$store['id']}';";
                    }
                }

                //查询下架商品
                $offlineItems = GameStoreItems::model()->findAll(array(
                    'select' => 'id',
                    'condition' => 'item_status = :item_status',
                    'params' => array(':item_status' => GameStoreItems::STATUS_OFFLINE)
                ))->toArray();
                if(!empty($offlineItems)){
                    foreach($offlineItems as $key => $offlineItem){
                        $sql .= "DELETE FROM `gw_game_item_stock` WHERE item_id = '{$offlineItem['id']}';";
                    }
                }

                $items = GameStoreItems::model()->findAll(array(
                    'condition' => 'item_status = :item_status',
                    'params'=> array(':item_status' => GameStoreItems::STATUS_ONLINE)
                ));
                if(!empty($items)){
                    foreach($items as $key => $item){
                        $beginTime = strtotime($item['start_date'] . $item['start_time']);
                        $endTime = strtotime($item['end_date'] . $item['end_time']);
                        $dateTime = time(); //当前时间
                        if($dateTime >= $beginTime && $dateTime < $endTime){
                            //查询该商家水果的库存记录
                            $stock = GameItemStock::model()->find(array(
                                'select' => 'id,update_stock_time',
                                'condition' => 'store_id = :store_id and item_id = :item_id',
                                'params' => array(':store_id' => $item['store_id'], ':item_id' => $item['id'])
                            ));
                            //活动开始那天更新水果库存
                            if($dateTime == strtotime(date('Y-m-d' . $item['start_time']))){
                                //更新水果库存
                                if($stock){
                                    $stockData = array(
                                        'stock_number' => $item['item_number'],
                                        'update_stock_time' => time()
                                    );
                                    $result = Yii::app()->game->createCommand()->update('{{item_stock}}',$stockData,"id = '{$stock['id']}'");
                                    if($result)echo "success" . "\n";
                                }else{
                                    $data = array(
                                        'store_id' => $item['store_id'],
                                        'item_id' => $item['id'],
                                        'stock_number' => $item['item_number'],
                                        'update_stock_time' => time()
                                    );
                                    $result = Yii::app()->game->createCommand()->insert('{{item_stock}}',$data);
                                    if($result)echo "success" . "\n";
                                }
                            }

                            //如果那个时间点未更新的，补水果库存
                            if($dateTime > strtotime(date('Y-m-d' . $item['start_time'])) && $dateTime <= strtotime(date('Y-m-d' . $item['end_time']))){
                                //没有库存记录的补上
                                if($stock === false){
                                    $data = array(
                                        'store_id' => $item['store_id'],
                                        'item_id' => $item['id'],
                                        'stock_number' => $item['item_number'],
                                        'update_stock_time' => time()
                                    );
                                    $result = Yii::app()->game->createCommand()->insert('{{item_stock}}',$data);
                                    if($result)echo "success" . "\n";
                                }else{
                                    //查询数据库当天是否已更新了库存，没有的话更新上
                                    if(date('Y-m-d',$stock['update_stock_time']) != date('Y-m-d')){
                                        $stockData = array(
                                            'stock_number' => $item['item_number'],
                                            'update_stock_time' => time()
                                        );
                                        $result = Yii::app()->game->createCommand()->update('{{item_stock}}',$stockData,"id = '{$stock['id']}'");
                                        if($result)echo "success" . "\n";
                                    }
                                }
                            }

                            //如果当前时间大于当天的抢水果结束时间
                            if($dateTime > strtotime(date('Y-m-d' . $item['end_time']))){
                                $stockInfo = GameItemStock::model()->find(array(
                                    'select' => 'id,stock_number',
                                    'condition' => 'item_id = :item_id',
                                    'params' => array(':item_id' => $item['id'])
                                ));
                                if($stockInfo['stock_number'] != 0){
                                    Yii::app()->game->createCommand()->update('{{item_stock}}',array('stock_number' => 0),"id = '{$stockInfo['id']}'");
                                }
                            }
                        }

                        //如果水果活动时间已过期，清除该水果的库存
                        if($dateTime > $endTime){
                            $sql .= "DELETE FROM `gw_game_item_stock` WHERE item_id = '{$item['id']}';";
                        }
                    }
                }
                if(!empty($sql))Yii::app()->game->createCommand()->execute($sql);
            }catch(Exception $e){
                echo $e->getMessage() . "\n";
                Yii::log(GameMemberInfo::getGameName(GameMemberInfo::GAME_TYPE_PANZHIHUA) . ":". $e->getMessage());
            }
            unset($items);
            sleep($interval);
        }
    }


}
