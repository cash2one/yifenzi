<?php
/**
 * 游戏店铺用户信息模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/11/24 10:31
 * The followings are the available columns in table '{{game_store_member}':
 * @property integer $id
 * @property integer $store_id
 * @property integer $member_id
 * @property string $real_name
 * @property string $mobile
 * @property integer $status
 * @property string $member_address
 * @property string $items_info
 */
class GameStoreMember extends CActiveRecord
{
    //是否发货
    const STATUS_NOT_DELIVERY = 0; //未发货
    const STATUS_DELIVERY = 1; //已发货

    public function tableName()
    {
        return '{{game_store_member}}';
    }

    public function getDbConnection() {
        return Yii::app()->gw;
    }

    public function rules(){
        return array(
            array('real_name,mobile','required'),
            array('real_name','length','max' => 10),
            array('member_address','length','max' => 30),
            array('mobile', 'ext.validators.isMobile', 'errMsg' => Yii::t('user', '请输入正确的手机号码')),
            array('id,store_id,member_id,status,real_name,mobile,member_address,items_info', 'safe', 'on'=>'search'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'store_id' => '店铺ID',
            'member_id' => '会员ID',
            'real_name' => '用户姓名',
            'mobile' => '手机号',
            'status' => '状态',
            'member_address' => '用户地址',
            'items_info' => '商品信息',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('store_id', $this->store_id);
        $criteria->compare('member_id', $this->member_id, true);
        $criteria->compare('real_name', $this->real_name, true);
        $criteria->compare('mobile', $this->mobile, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('member_address', $this->member_address, true);
        $criteria->compare('items_info', $this->items_info, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 发货状态
     * @param $key
     * @return array|null
     */
    public static function status($key = null) {
        $arr = array(
            self::STATUS_NOT_DELIVERY => Yii::t('gameStoreMember', '未发货'),
            self::STATUS_DELIVERY => Yii::t('gameStoreMember', '已发货'),
        );
        if (is_numeric($key)) {
            return isset($arr[$key]) ? $arr[$key] : null;
        } else {
            return $arr;
        }
    }
}