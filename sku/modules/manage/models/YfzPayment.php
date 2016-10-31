<?php

/**
 * 一份子图片模型
 */
class YfzPayment extends YifenBase
{
    public $JFPALY;
    public $jfpaly_online;
    public $jfpaly_enabled;
    public $GHTPALY;
    public $ghtpaly_online;
    public $ghtpaly_enabled;
    public $WXPAY;
    public $wxpay_online;
    public $wxpay_enabled;
    public $online;
    public $enabled;
    /**
     * 数据表
     * @return string
     */
    public function tableName()
    {
        return '{{payment}}';
    }

    public function rules()
    {
        return array(
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('payment_id, payment_code, payment_name, payment_desc, enabled, sort_order, config, is_online', 'safe', 'on' => 'search'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'payment_id' => '支付方式ID',
            'payment_code' => '支付代码',
            'payment_name' => '支付名称',
            'payment_desc' => '支付配置',
            'enabled' => '是否启用支付',
            'is_online' => '是否为在线支付',
            'JFPALY' => '积分支付',
            'GHTPALY' => '高汇通支付',
            'WXPAY' => '微信支付',
            'jfpaly_online' =>'是否为在线支付',
            'jfpaly_enabled' =>'是否启用支付',
            'ghtpaly_online' =>'是否为在线支付',
            'ghtpaly_enabled' =>'是否启用支付',
            'wxpay_online' =>'是否为在线支付',
            'wxpay_enabled' =>'是否启用支付',
        );
    }
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function search() {
        $criteria = new CDbCriteria;

        return $criteria;
    }

    const USE_YES = 1;
    const USE_NO = 0;
    /**
     * 获取启用状态
     * @return array
     */
    public static function getEnabledStatus($key = null) {
        $data = array(
            self::USE_YES => Yii::t('onepartPay', '启用'),
            self::USE_NO => Yii::t('onepartPay', '禁用')
        );
        if($key===null) return $data;
        return isset($data[$key]) ? $data[$key] : '未知';
    }
    const ONLINE_YES = 1;
    const ONLINE_NO = 0;
    /**
     * 获取启用状态
     * @return array
     */
    public static function getOnlineStatus($key = null) {
        $data = array(
            self::ONLINE_YES => Yii::t('onepartPay', '是'),
            self::ONLINE_NO => Yii::t('onepartPay', '否')
        );
        if($key===null) return $data;
        return isset($data[$key]) ? $data[$key] : '未知';
    }
    /**
     * afterFind
     * public $JFPALY;
    public $jfpaly_online;
    public $jfpaly_enabled;
    public $GHTPALY;
    public $ghtpaly_online;
    public $ghtpaly_enabled;
    public $WXPAY;
    public $wxpay_online;
    public $wxpay_enabled;
     */
    protected function afterFind()
    {
        parent::afterFind();
        if($this->payment_code == 'JFPALY'){
            $this->jfpaly_enabled = !empty($this->enabled)?$this->enabled:0;
            $this->jfpaly_online = !empty($this->is_online)?$this->is_online:0;
        }elseif($this->payment_code == 'GHTPALY'){
            $this->ghtpaly_enabled = !empty($this->enabled)?$this->enabled:0;
            $this->ghtpaly_online = !empty($this->is_online)?$this->is_online:0;
        }else{
            $this->wxpay_enabled = !empty($this->enabled)?$this->enabled:0;
            $this->wxpay_online = !empty($this->is_online)?$this->is_online:0;
        }
        return true;
    }
}
