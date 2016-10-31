<?php

/**
 * 一份子基础模型
 */
class YifenBase extends CActiveRecord
{
    public function getDbConnection()
    {
        return Yii::app()->gwpart;
    }
}