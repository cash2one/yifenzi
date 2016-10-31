<?php
/**
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/9/25 14:06
 */

class GameReward extends CActiveRecord
{
    public function tableName()
    {
        return '{{reward}}';
    }

    public function getDbConnection() {
        return Yii::app()->game;
    }


    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
}