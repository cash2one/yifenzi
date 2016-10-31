<?php
/**
 * 游戏花费金币模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/9/28 11:36
 */

class GameExpend extends CActiveRecord
{
    public function tableName()
    {
        return '{{expend}}';
    }

    public function getDbConnection() {
        return Yii::app()->game;
    }


    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 插入花费记录
     * @param $data
     */
    public static function insertExpend($data){
        $connection = Yii::app()->gw;;
        $keys = array_keys($data);
        $table = GAME . '.' . "`gw_game_expend`";
        $sql = "INSERT INTO $table (" . implode(",", $keys) . ") VALUES ('" . implode("','", $data) . "');";
        $connection->createCommand($sql)->execute();
        $id = $connection->lastInsertId;
        return $id;
    }
}