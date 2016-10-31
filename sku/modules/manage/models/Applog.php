<?php

/**
 * 程序日志模型类
 * @author wanyun.liu <wanyun_liu@163.com>
 * 
 * @property integer $id
 * @property string $level
 * @property string $category
 * @property integer $logtime
 * @property string $message
 */
class Applog extends CActiveRecord {

    public function tableName() {
        return 'applog';
    }

    public function attributeLabels() {
        return array(
            'id' => '主键',
            'level' => '级别',
            'category' => '分类',
            'logtime' => '创建时间',
            'message' => '内容',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id);
        $criteria->compare('level', $this->level, true);
        $criteria->compare('category', $this->category, true);
        $criteria->compare('logtime', $this->logtime);
        $criteria->compare('message', $this->message, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => 'id DESC',
            ),
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
