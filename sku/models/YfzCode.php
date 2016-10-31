<?php
/**
 * Code数据表对象换
 * ==============================================
 * 编码时间:2016年3月25日
 * ------------------------------------------------------------------------------------
 * 公司源码文件，未经授权不许任何使用和传播。
 * ==============================================
 * @date: 2016年3月25日
 * @author: Derek
 * @version: G-emall child One Parts 1.0.0
 * @return: Object
 **/

class YfzCode extends CActiveRecord
{
    #数据表名
    public function tableName()
    {
        return '{{code}}';
    }

    ##数据库连接
    public function getDbConnection()
    {
        return Yii::app()->gwpart;
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
