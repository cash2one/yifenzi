<?php
/**
 * 游戏店铺发货模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/11/24 10:31
 * The followings are the available columns in table '{{game_store_delivery}':
 * @property integer $id
 * @property integer $order_id
 * @property integer $delivery_store_id
 * @property string $delivery_items
 * @property integer $receive_member_id
 * @property string $delivery_time
 */
class GameStoreDelivery extends CActiveRecord
{
    public function tableName()
    {
        return '{{game_store_delivery}}';
    }

    public function getDbConnection() {
        return Yii::app()->gw;
    }

    public function rules(){
        return array(
            array('delivery_items,delivery_time','required'),
            array('delivery_items','length','max'=>30),
            array('delivery_time','length','max'=>20),
            array('id,delivery_store_id,order_id,delivery_items,receive_member_id,delivery_time', 'safe'),
        );
    }

    public function relations(){
        return array(
            'info' => array(self::BELONGS_TO, 'GameStoreMember', array('order_id' => 'id')),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'order_id' => '订单ID',
            'delivery_store_id' => '店铺ID',
            'delivery_items' => '发货商品',
            'receive_member_id' => '收货人ID',
            'delivery_time' => '发货时间',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('delivery_store_id', $this->delivery_store_id, true);
        $criteria->compare('delivery_items', $this->delivery_items, true);
        $criteria->compare('receive_member_id', $this->receive_member_id, true);
        $criteria->compare('delivery_time', $this->delivery_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}