<?php
/**
 * 水果庫存模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2016/1/7 14:56
 */
class GameItemStock extends CActiveRecord
{
    public function tableName()
    {
        return '{{item_stock}}';
    }

    public function getDbConnection()
    {
        return Yii::app()->game;
    }


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    //减去水果的库存
    public static function delStock($stockId, $number){
        $connection = Yii::app()->db;
        $sql = "UPDATE " . GAME . ".gw_game_item_stock SET stock_number=stock_number-{$number} WHERE (stock_number-{$number}) >=0
                AND id={$stockId};";
        $connection->createCommand($sql)->execute();
    }
}