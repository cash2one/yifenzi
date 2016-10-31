<?php

/**
 * 会员级别模型
 * @author wencong.lin <183482670@qq.com>
 * 
 * The followings are the available columns in table '{{member_grade}}':
 * @property integer $id
 * @property string $name
 * @property string $description
 */
class WebConfig extends CActiveRecord {


    public function tableName() {
        return '{{web_config}}';
    }

    public function rules() {
        return array(
            array('name,value', 'required'),
//            array('name', 'unique'),
            array('name', 'length', 'max' => 128),
    
        );
    }

    public function relations() {
        return array(
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => '网站配置信息名称',
            'value' => '网站配置信息内容',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('value', $this->description, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
