<?php
/**
 * 游戏店铺模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/11/17 18:12
 * The followings are the available columns in table '{{game_store}}':
 * @property integer $id
 * @property string $gai_number
 * @property string $store_name
 * @property string $store_phone
 * @property string $store_address
 * @property integer $store_status
 * @property integer $limit_time_hour
 * @property integer $limit_time_minute
 * @property integer $create_time
 * @property integer $update_time
 */
class GameStore extends CActiveRecord
{
    //店铺状态
    const STATUS_ONLINE = 1;    //启用
    const STATUS_CLOSE = 2;     //关闭

    //特殊商铺
    const FRANCHISE_STORES_NO = 0; //否
    const FRANCHISE_STORES_IS = 1; //是

    public function tableName()
    {
        return '{{game_store}}';
    }

    public function getDbConnection() {
        return Yii::app()->gw;
    }

    /**
     * 店铺状态
     * @param $key
     * @return array|null
     */
    public static function status($key = null) {
        $arr = array(
            self::STATUS_ONLINE => Yii::t('gameStore', '启用'),
            self::STATUS_CLOSE => Yii::t('gameStore', '关闭'),
        );
        if (is_numeric($key)) {
            return isset($arr[$key]) ? $arr[$key] : null;
        } else {
            return $arr;
        }
    }

    /*
     * 特殊商品店铺
     * @param int $key
     */
    public static function franchiseStores($key = null){
        $arr = array(
            self::FRANCHISE_STORES_NO => Yii::t('gameStore', '否'),
            self::FRANCHISE_STORES_IS => Yii::t('gameStore', '是'),
        );
        if(is_numeric($key)){
            return isset($arr[$key]) ? $arr[$key] : null;
        }else{
            return $arr;
        }
    }

    /**
     * 获取店铺名称
     * @param $store_id
     * @return mixed|null
     */
    public static function name($store_id){
        if(empty($store_id))return null;
        $model = self::model()->findByPk($store_id);
        return $model ? $model->store_name : null;
    }

    public function rules() {
        return array(
            array('store_name,gai_number', 'unique'),
            array('store_name,gai_number', 'required'),
            array('limit_time_hour,limit_time_minute', 'numerical', 'integerOnly'=>true),
            array('limit_time_hour', 'compare', 'compareValue'=>'0', 'operator'=>'>=', 'message'=>Yii::t('gameStore', '必须大于等于0')),
            array('limit_time_hour', 'compare', 'compareValue'=>'24', 'operator'=>'<=', 'message'=>Yii::t('gameStore', '必须小于等于24')),
            array('limit_time_minute', 'compare', 'compareValue'=>'0', 'operator'=>'>=', 'message'=>Yii::t('gameStore', '必须大于等于0')),
            array('limit_time_minute', 'compare', 'compareValue'=>'60', 'operator'=>'<=', 'message'=>Yii::t('gameStore', '必须小于等于60')),
            array('gai_number', 'comext.validators.isGaiNumber', 'message' => Yii::t('gameStore', '请输入正确的盖网号')),
            array('gai_number','checkGaiNumber'),
            array('store_address','length','max'=>30),
            array('store_phone', 'match', 'pattern' => '/(^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$)|(^((\(\d{3}\))|(\d{3}\-))?(1[34578]\d{9})$)/', 'message' => Yii::t('partner', '请填写正确的手机号码或电话号码')),
            array('id, gai_number,store_name, store_phone,store_address, store_status,limit_time_hour,limit_time_minute,create_time, update_time, franchise_stores', 'safe'),
        );
    }

    /**
     * 验证gw号
     * @param $attribute
     */
    public function checkGaiNumber($attribute){
        if($this->$attribute) {
            $id = self::getEnterpriseGaiNumber($this->$attribute);
            if (!$id) {
                $this->addError($attribute, Yii::t('GameStore', '商家盖网号不存在'));
            }
        }
    }

    /**
     * 获取商家member_id
     * @param $str
     * @return array
     */
    public static function  getEnterpriseGaiNumber($str){
        $str = Yii::app()->getController()->magicQuotes($str);
        if(preg_match('/^GW[0-9]{8}$/',$str)){
            $id= Yii::app()->db->createCommand()->select('id')->from('{{member}}')
                ->where('(status=:status_no or status=:status_yes ) and gai_number=:gai_number',array(':status_no'=>Member::STATUS_NO_ACTIVE,':status_yes'=>Member::STATUS_NORMAL,':gai_number'=>$str))
                ->queryScalar();
            return $id ? $id : '';
        }else {
            return '';
        }
    }

    public function relations() {
        return array(
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'gai_number' => '商家盖网号',
            'store_name' => '店铺名称',
            'store_phone' => '店铺电话',
            'store_address' => '店铺地址',
            'store_status' => '店铺状态',
            'limit_time_hour' => '已抢限制(小时)',
            'limit_time_minute' => '已抢限制(分钟)',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
            'franchise_stores' => '特殊商品店铺'
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('gai_number', $this->gai_number, true);
        $criteria->compare('store_name', $this->store_name, true);
        $criteria->compare('store_phone', $this->store_phone, true);
        $criteria->compare('store_address', $this->store_address, true);
        $criteria->compare('store_status', $this->store_status, true);
        $criteria->compare('limit_time_hour', $this->limit_time_hour, true);
        $criteria->compare('limit_time_minute', $this->limit_time_minute, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_time', $this->update_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
}