<?php
/**
 * 游戏金币兑换模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/8/17 16:38
 */

class GameExchange extends CActiveRecord
{
    const SCALE = 2; //保留两位小数
    const EXCHANGE_GOLD_NUM = 10; //正式会员1积分可兑换金币数

    //游戏类型
    const GAME_TYPE_SANGUORUN = 1;    //三国跑跑
    const GAME_TYPE_PAIPAIMENG = 2;   //啪啪萌僵尸

    public function tableName()
    {
        return '{{exchange}}';
    }

    public function getDbConnection() {
        return Yii::app()->game;
    }


    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 记录积分兑换金币
     * @param $data
     */
    public static function insertExchange($data){
        $connection = Yii::app()->gw;
        $keys = array_keys($data);
        $tableName = GAME . '.' . 'gw_game_exchange';
        $sql = "INSERT INTO $tableName (" . implode(",", $keys) . ") VALUES ('" . implode("','", $data) . "');";
        $connection->createCommand($sql)->execute();
        $id = $connection->lastInsertId;
        return $id;
    }

    /**
     * 更新状态码和返回数据
     * @param $id
     * @param $result
     */
    public static function updateResultCode($id, $result){
        $connection = Yii::app()->gw;
        $result = json_encode($result);
        $sql = "UPDATE " . GAME . "." ."`gw_game_exchange` SET `result_code` = " . GameModule::RESULT_CODE_1 .", `result` = '{$result}',
        `update_time` = " . time() . " WHERE `id` = '{$id}'";
        $connection->createCommand($sql)->execute();
    }
}