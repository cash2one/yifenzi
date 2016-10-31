<?php
/**
 * 水果背包模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2016/1/7 14:56
 */
class GameItemGain extends CActiveRecord
{
    public function tableName()
    {
        return '{{item_gain}}';
    }

    public function getDbConnection()
    {
        return Yii::app()->game;
    }


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}