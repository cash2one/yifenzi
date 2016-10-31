<?php
/**
 * 游戏配置模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * The followings are the available columns in table '{{game_config}}':
 * @property integer $id
 * @property integer $app_type
 * @property string $config_name
 * @property string $description
 */
class GameConfig extends CActiveRecord {

    public function tableName() {
        return '{{game_config}}';
    }

    public function getDbConnection() {
        return Yii::app()->gw;
    }

    public function rules() {
        return array(
            array('config_name,value', 'required'),
//            array('name', 'unique'),
            array('config_name', 'length', 'max' => 60),

        );
    }

    public function relations() {
        return array(
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'app_type' => '游戏类型',
            'config_name' => '游戏配置信息名称',
            'value' => '游戏配置信息内容',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('app_type', $this->app_type, true);
        $criteria->compare('config_name', $this->config_name, true);
        $criteria->compare('value', $this->description, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @param $data
     */
    public static function insertConfig($data){
        $connection = Yii::app()->gw;
        $keys = array_keys($data);
        $sql = "INSERT INTO `gw_game_config` (" . implode(",", $keys) . ") VALUES ('" . implode("','", $data) . "');";
        $connection->createCommand($sql)->execute();
    }
}