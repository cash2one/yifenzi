<?php 
/**
   * Good_statistics数据表对象换
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

class GoodsStatistics extends CActiveRecord{
    
    #数据表名
    public function tableName()
    {
        return '{{goods_statistics}}';
    }
    
    #数据库连接实例
    public function getDbConnection()
    {
        return Yii::app()->gwpart;
    }
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    /**
     * 添加产品点击量
     * @param type $goods_id
     */
    public static function addViews($goods_id)
    {
        $model = self::model()->findByPk($goods_id);
        if($model){
            $sql = "update {{goods_statistics}} set views=views+1 where goods_id=:id";
            $command = Yii::app()->gwpart->createCommand($sql);
            $command->bindValue(':id',$goods_id);
            $result = $command->execute();
        } else {
            $command = Yii::app()->gwpart->createCommand();
            $command->insert('{{goods_statistics}}', array('goods_id'=>$goods_id,'views'=>1));
        }
        return true;
    }

    /**
     * @param $id 商品id
     * @param string $fieldName 字段名称
     * @return null
     */
    public static function getField($id, $fieldName=''){
        $model = self::model()->find("goods_id=:id",array(':id'=>$id));

        if ($fieldName){
            return $model->$fieldName ? $model->$fieldName : null;
        }
        return $model;
    }
}