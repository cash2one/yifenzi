<?php 
/**
   * Cart数据表对象换
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

class Cart extends CActiveRecord{
    
    #数据表名
    public function tableName()
    {
        return '{{cart}}';
    }
    
    #数据库连接实例
    public function getDbConnection()
    {
        return Yii::app()->gwpart;
    }
    
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function delonegoods($goods_id){
        return Yii::app()->gwpart->createCommand()->delete($this->tableName(),
//             'goods_id=:goods_id and member_id=:member_id',
                "goods_id=:goods_id and member_id=:member_id",
            array(
                'goods_id' => $goods_id,
                'member_id' => Yii::app()->user->id,
            )
            );
    }
    
    /**
     * 对一个商品进行操作统计
     * @param Number $goods_id
     * @return bool
     */
    public function cartLog( $goods_id ){
        $cartLog = Goods_statistics::model()->find(array("condition"=>"goods_id={$goods_id}"));
        $cartLog  = json_decode(CJSON::encode($cartLog), true);
        
        if ( $cartLog ){
            $num = ($cartLog['carts'] + 1);
            return Yii::app()->gwpart->createCommand()->update("gw_yifenzi_goods_statistics", array(
                "carts" => $num
            ), "goods_id=:goods_id", array(
                "goods_id" => $goods_id
            ));
        }else{
            return Yii::app()->gwpart->createCommand()->insert("gw_yifenzi_goods_statistics",array(
                "goods_id"=>$goods_id,
                "carts"=>1,
            ));
        }
    }
}
