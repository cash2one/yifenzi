<?php
/**
 * 游戏商铺商品模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/11/19 11:29
 * The followings are the available columns in table '{{game_store_items}}':
 * @property integer $id
 * @property integer $store_id
 * @property string $item_name
 * @property integer $item_number
 * @property integer $item_status
 * @property string $item_description
 * @property string $store_description
 * @property  string $start_date
 * @property  string $end_date
 * @property  string $start_time
 * @property  string $end_time
 * @property  integer $limit_per_time
 * @property  integer $bees_number
 * @property  integer $create_time
 * @property  integer $update_time
 * @property integer $flag
 * @property integer $probability
 */
class GameStoreItems extends CActiveRecord
{
    //商品状态
    const STATUS_ONLINE = 1;   //上架
    const STATUS_OFFLINE = 2;   //下架

    //商品标识
    const ORDINARY_ITEM_FLAG = 0; //普通商品
    const SPECIAL_ITEM_FLAG = 1;  //特殊商品

    public function tableName()
    {
        return '{{game_store_items}}';
    }

    public function getDbConnection() {
        return Yii::app()->gw;
    }

    /*
     * 特殊商品
     * @param int $key
     */
    public static function flagItems($key = null){
        $arr = array(
            self::ORDINARY_ITEM_FLAG => Yii::t('gameStoreItems', '否'),
            self::SPECIAL_ITEM_FLAG => Yii::t('gameStoreItems', '是'),
        );
        if(is_numeric($key)){
            return isset($arr[$key]) ? $arr[$key] : null;
        }else{
            return $arr;
        }
    }

    public function rules(){
        return array(
            array('item_name,item_number,item_status,limit_per_time,start_date,end_date,start_time,end_time,bees_number,probability','required'),
            array('item_number,bees_number,limit_per_time,probability','numerical', 'integerOnly'=>true),
            array('item_number,limit_per_time','compare', 'compareValue'=>'0', 'operator'=>'>', 'message'=>Yii::t('gameStoreItems', '必须大于0')),
            array('bees_number,probability','compare', 'compareValue'=>'0', 'operator'=>'>=', 'message'=>Yii::t('gameStoreItems', '不能小于0')),
            array('bees_number','compare', 'compareValue'=>'9', 'operator'=>'<=', 'message'=>Yii::t('gameStoreItems', '不能大于9')),
            array('probability','compare','compareValue'=>'9999', 'operator'=>'<=', 'message'=>Yii::t('gameStoreItems', '不能大于9999')),
            array('store_description','length','max' => 10),
            array('item_description','length','max' => 20),
            array('item_name','length','max'=>20),
            array('item_number','length','max'=>9),
            array('item_name','checkItemName','on' => 'Create,Createflag,Update'),
            array('id,store_id, item_name,item_number, item_status,item_description, store_description,start_date,end_date,start_time,end_time,
            limit_per_time,bees_number,create_time, update_time,flag,probability', 'safe'),
        );
    }

    public function checkItemName() {
        $exists = $this->exists('store_id = :sid AND item_name = :name', array(':sid' => $this->store_id, ':name' => $this->item_name));
        if ($exists) {
            $this->addError('item_name',  $this->getAttributeLabel('item_name') . '不可重复！');
        }
    }

    public function relations(){
        return array(

        );
    }

    /**
     * 商品状态
     * @param $key
     * @return array|null
     */
    public static function status($key = null) {
        $arr = array(
            self::STATUS_ONLINE => Yii::t('gameStoreItems', '上架'),
            self::STATUS_OFFLINE => Yii::t('gameStoreItems', '下架'),
        );
        if (is_numeric($key)) {
            return isset($arr[$key]) ? $arr[$key] : null;
        } else {
            return $arr;
        }
    }

    public function attributeLabels(){
        return array(
            'id' => 'ID',
            'store_id' => '店铺',
            'item_name' => '商品名称',
            'item_number' => '每日提供数量',
            'item_status' => '商品状态',
            'item_description' => '商品描述',
            'store_description' => '商家描述',
            'start_date' => '活动开始日期',
            'end_date' => '活动结束日期',
            'start_time' => '每日开始时间',
            'end_time' => '每日结束时间',
            'limit_per_time' => '用户单次获得数量',
            'bees_number' => '蜜蜂数量',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
            'flag' => '商品标识',
            'probability' => '获取惊喜大奖概率',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('store_id', $this->store_id, true);
        $criteria->compare('item_name', $this->item_name, true);
        $criteria->compare('item_number', $this->item_number, true);
        $criteria->compare('item_status', $this->item_status, true);
        $criteria->compare('item_description', $this->item_description, true);
        $criteria->compare('store_description', $this->store_description, true);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('start_time', $this->start_time, true);
        $criteria->compare('end_time', $this->end_time, true);
        $criteria->compare('limit_per_time', $this->limit_per_time, true);
        $criteria->compare('bees_number', $this->bees_number, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_time', $this->update_time, true);
        $criteria->compare('flag', $this->flag, true);
        $criteria->compare('probability', $this->probability, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 获取商品标识
     * @param null $key
     * @return array|null
     */
    public static function getFlagStatus($key = null){
        $arr = array(
            self::ORDINARY_ITEM_FLAG => Yii::t('gameStoreItems', '普通商品'),
            self::SPECIAL_ITEM_FLAG=> Yii::t('gameStoreItems', '特殊商品'),
        );
        if (is_numeric($key)) {
            return isset($arr[$key]) ? $arr[$key] : null;
        } else {
            return $arr;
        }
    }
}