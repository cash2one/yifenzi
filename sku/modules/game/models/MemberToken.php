<?php
/**
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/8/17 16:12
 */

class MemberToken extends CActiveRecord
{
    const TYPE_CUSTOMER = 1; //消费者登录
    const TYPE_ADMIN = 2; //管理员登录
    const TYPE_STAFF = 3; //服务员登录

    public function tableName()
    {
        return '{{member_token}}';
    }

    public function getDbConnection() {
        return Yii::app()->gw;
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 插入token
     * @param $data
     */
    public static function insertToken($data){
        $connection = Yii::app()->gw;
        $keys = array_keys($data);
        $table = "`gw_member_token`";
        $sql = "INSERT INTO $table (" . implode(",", $keys) . ") VALUES ('" . implode("','", $data) . "');";
        $connection->createCommand($sql)->execute();
        $id = $connection->getLastInsertID();
        return $id;
    }

    /**
     * 更新token
     * @param $memberId
     * @param $app_type
     * @param $data
     */
    public static function updateToken($memberId,$app_type,$data){
        $connection = Yii::app()->gw;
        $tableName = '`gw_member_token`';
        $keys = array_keys($data);
        $array = array();
        foreach($keys as $key){
            $array[] = $key . " = :" . $key;
        }
        $params = implode(',',$array);
        $sql = "UPDATE $tableName SET $params WHERE target_id = '$memberId' AND app_type = '$app_type'";
        $command = $connection->createCommand($sql);
        foreach($data as $key => $value){
            $command->bindValue(":$key",$value,PDO::PARAM_STR);
        }
        $command->execute();
    }
}