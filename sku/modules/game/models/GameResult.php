<?php
/**
 * 游戏结果模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/8/17 16:38
 */

class GameResult extends CActiveRecord
{
    const SCALE = 0; //金币不设保留小数

    public function tableName()
    {
        return '{{result}}';
    }

    public function getDbConnection() {
        return Yii::app()->game;
    }


    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 记录比赛结果
     * @param $data
     * @return int 比赛结果记录的主键id
     */
    public static function insertResult($data){
        $connection = Yii::app()->gw;
        $keys = array_keys($data);
        $table = GAME . '.' . "`gw_game_result`";
        $sql = "INSERT INTO $table (" . implode(",", $keys) . ") VALUES ('" . implode("','", $data) . "');";
        $connection->createCommand($sql)->execute();
        $id = $connection->lastInsertId;
        return $id;
    }
}