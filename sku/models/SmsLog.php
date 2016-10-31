<?php

/**
 * This is the model class for table "{{sms_log}}".
 *
 * The followings are the available columns in table '{{sms_log}}':
 * @property string $id
 * @property string $mobile
 * @property string $content
 * @property string $create_time
 * @property integer $status
 * @property integer $count
 * @property string $target_id
 * @property integer $type
 * @property string $send_time
 * @property integer $interface
 * @property integer $source
 */
class SmsLog extends CActiveRecord {

    const TYPE_ONLINE_ORDER = 1; // 线上订单
    const TYPE_OFFLINE_ORDER = 2; // 线下订单
    const TYPE_CARD_RECHARGE = 3; // 卡充值
    const TYPE_HOTEL_ORDER = 4; // 酒店订单
    const TYPE_CAPTCHA = 5; // 验证码
    const TYPE_OTHER = 6;   //其他
    const TYPE_POS_RECHARGE = 7;	//盖网通POS充值
    const TYPE_VENDING_COMPLEMENT = 8; //售货机补货
    const TYPE_VENDING_RETURN = 9; //售货机退货
    const TYPE_TEST = 10;	// 测试短信
    const TYPE_ORDER_REMIND = 11; //提醒发货
    const TYPE_TRANSFER_ORDER = 12;//转账
    /* 短信通道(大陆) */
    const INTERFACE_DXT = 1; // 短信通
    const INTERFACE_YX = 2; // 易信
    const INTERFACE_JXT = 3;	//吉信通
    const INTERFACE_JXT_ADVERT = 4;	//吉信通(广告)

    const INTERFACE_YTX = 101; // 香港易通信
    const INTERFACE_RLY = 5 ; //容联云

    /* 状态 */
    const STATUS_SUCCESS = 1; // 发送成功
    const STATUS_FAILD = 2; // 发送失败

    //来源
    const GW_SEND_SMS = 0;          //商城
    const GT_SEND_SMS = 1;          //盖网通
    const HOTEL_SEND_SMS = 2;       //酒店
    const SKU_SEND_SMS = 3;         //sku项目
    const DRUGSTORE_SEND_SMS = 4;   //盖象大药房
    const WUYE_SEND_SMS = 5;        //物业管理项目
    const YOUXI_SEND_SMS = 6;       //游戏项目

    public  $create_end_time,$send_end_time;

   
/**
     * 状态
     * @return array
     */
    public static function  getStatus(){
        return  array(
            self::STATUS_SUCCESS=>'发送成功',
            self::STATUS_FAILD=>'发送失败',
        );
    }
    /**
     * 显示状态
     * @param int $key
     * @return string
     */
    public static function showStatus($key) {
        $status = self::getStatus();
        return isset($status[$key]) ? $status[$key] : null;
    }
    /**
     * 类型
     * @return array
     */
    public static function  getType(){
        return  array(
            self::TYPE_ONLINE_ORDER =>'线上订单',
            self::TYPE_OFFLINE_ORDER =>'线下订单',
            self::TYPE_CARD_RECHARGE =>'卡充值',
            self::TYPE_HOTEL_ORDER =>'酒店订单',
            self::TYPE_CAPTCHA =>'验证码',
            self::TYPE_OTHER => '其他',
            self::TYPE_POS_RECHARGE => '盖网通POS充值',
            self::TYPE_VENDING_COMPLEMENT => '售货机补货',
            self::TYPE_VENDING_RETURN => '售货机退货',
            self::TYPE_TEST => '测试短信',
            self::TYPE_ORDER_REMIND => '提醒发货',
        );
    }
    /**
     * 显示类型
     * @param int $key
     * @return string
     */
    public static function showType($key) {
        $type = self::getType();
        return isset($type[$key]) ? $type[$key] : null;
    }

    /**
     * 短信通道
     * @return array
     */
    public static  function  getInterface(){
        return  array(
            self::INTERFACE_DXT =>'短信通',
            self::INTERFACE_YX =>'易信',
            self::INTERFACE_JXT => '吉信通',
            self::INTERFACE_YTX =>'香港易通信',
            self::INTERFACE_JXT_ADVERT => '吉信通(广告)',
            self::INTERFACE_RLY=>'容联云通讯'
        );
    }
    /**
     * 显示短信通道
     * @param int $key
     * @return string
     */
    public static function showInterface($key) {
        $interface = self::getInterface();
        return $interface[$key];
    }

    public function tableName() {
        return '{{sms_log}}';
    }

    public function getDbConnection() {
        return Yii::app()->gw;
    }

    public function rules() {
        return array(
            array('mobile, content, create_time, target_id, type', 'required'),
            array('status, count, send_time,create_end_time,send_end_time, interface', 'safe'),
            array('status, count, type, interface', 'numerical', 'integerOnly' => true),
            array('mobile', 'length', 'max' => 64),
            array('create_time, target_id, send_time', 'length', 'max' => 11),
            array('id, mobile, content, create_time, status, count, target_id, type, send_time, interface', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
        );
    }

    public function attributeLabels() {
        return array(
            'id' => '主键',
            'mobile' => '手机号码',
            'content' => '内容',
            'create_time' => '创建时间',
            'status' => '状态（1发送成功、2发送失败）',
            'count' => '发送次数',
            'target_id' => '对象',
            'type' => '类型（1线上订单、2线下订单、3卡充值、4酒店订单、5验证码）',
            'send_time' => '发送时间',
            'interface' => '短信接口（1短信通、2易信、3香港易通讯）',
        );
    }


    public function search() {
        $criteria = new CDbCriteria;
		$criteria->addCondition('t.type = :type');    
        $criteria->params[':type'] = SmsLog::TYPE_ONLINE_ORDER; 
		$criteria->addCondition('t.source = :source');    
        $criteria->params[':source'] = SmsLog::SKU_SEND_SMS; 
        $criteria->compare('id', $this->id, true);
        $criteria->compare('mobile', $this->mobile);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('count', $this->count);
        $criteria->compare('target_id', $this->target_id, true);
        //$criteria->compare('type', $this->type);
        $criteria->compare('interface', $this->interface);

        if ($this->create_time) {
            $criteria->compare('t.create_time', ' >=' . strtotime($this->create_time));
        }
        if ($this->create_end_time) {
            $criteria->compare('t.create_time', ' <' . (strtotime($this->create_end_time)));
        }

        if ($this->send_time) {
            $criteria->compare('t.send_time', ' >=' . strtotime($this->send_time));
        }
        if ($this->send_end_time) {
            $criteria->compare('t.send_time', ' <' . (strtotime($this->send_end_time)));
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder'=>'id DESC', //设置默认排序
            ),
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

	 /**
     * 获取发送状态
     * @return array 返回状态数组
     */
    public static function getSendStatus($key = null)
    {
        $arr = array(
            self::STATUS_SUCCESS => '发送成功',
            self::STATUS_FAILD => '发送失败',
        );
        return $key !== null ? (isset($arr[$key]) ? $arr[$key] : '未知状态') : $arr;
    }
	
	/**
     * 获取发送类型
     * @return array 返回状态数组
     */
    public static function getSendType($key = null)
    {
        $arr = array(
            self::TYPE_ONLINE_ORDER =>'线上订单',
            self::TYPE_OFFLINE_ORDER =>'线下订单',
            self::TYPE_CARD_RECHARGE =>'卡充值',
            self::TYPE_HOTEL_ORDER =>'酒店订单',
            self::TYPE_CAPTCHA =>'验证码',
            self::TYPE_OTHER => '其他',
            self::TYPE_POS_RECHARGE => '盖网通POS充值',
            self::TYPE_VENDING_COMPLEMENT => '售货机补货',
            self::TYPE_VENDING_RETURN => '售货机退货',
            self::TYPE_TEST => '测试短信',
            self::TYPE_ORDER_REMIND => '提醒发货',
        );
        return $key !== null ? (isset($arr[$key]) ? $arr[$key] : '未知类型') : $arr;
    }
	
	
	/**
     * 获取发送接口
     * @return array 返回状态数组
     */
    public static function getSendInterfaceType($key = null)
    {
        $arr = array(
            self::INTERFACE_DXT =>'短信通',
            self::INTERFACE_YX =>'易信',
            self::INTERFACE_JXT => '吉信通',
            self::INTERFACE_YTX =>'香港易通信',
            self::INTERFACE_JXT_ADVERT => '吉信通(广告)',
            self::INTERFACE_RLY=>'容联云通讯'
        );
        return $key !== null ? (isset($arr[$key]) ? $arr[$key] : '未知接口') : $arr;
    }
	
    /**
     * 短信发送
     * @param string $mobile 手机号码
     * @param string $content 短信内容
     * @param int $target 对象
     * @param int $type 短信类型(1线上订单、2线下订单、3卡充值、4酒店订单、5验证码)
     * @param int $apiType
     * @param bool $is_push 是否推送信息
     * @param array $datas 发送的数据
     * @param int $tmpId 容联云通的模板ID
     * @param integer $source 短信来源
     * @return bool
     */
    public static function addSmsLog($mobile, $content, $target=0, $type=self::TYPE_CAPTCHA, $apiType = null,$is_push = true,$datas=null, $tmpId=null, $source = SmsLog::GW_SEND_SMS) {
        $smsLog = new SmsLog();
        $smsLog->mobile = $mobile;
        $smsLog->content = $content;
        $smsLog->create_time = time();
        $smsLog->target_id = $target;
        $smsLog->type = $type;
        $smsLog->source = $source;
        $result = false;
        if ($smsLog->save(false)) {
            if($apiType === null)
            {
                $apiType = Sms::getSmsApi($smsLog->mobile);			//默认使用短信配置
            }
            $arr = array('id' => $smsLog->id, 'mobile' => $smsLog->mobile, 'content' => $smsLog->content, 'datas'=>$datas,'tmpId'=>$tmpId, 'api' => $apiType, 'type' => $type);
            if($type == self::TYPE_CAPTCHA)
            {
                $result = GWRedisList::sendSmsGTCode($arr);
            }
            else
            {
                $result = GWRedisList::sendSmsGT($arr);
            }
        }
        //调用极光推送接口发给盖付通
        if($is_push)
            @JPushTool::tokenPush($mobile, $content);

        return $result;
    }

    /**
     * 根据短信内容进行相关的屏蔽
     * @param int $interface
     * @param string $content
     * @author LC
     */
    public static function showContent($type,$content)
    {
        //如果是充值卡，则屏蔽充值卡密码
        if($type == SmsLog::TYPE_CARD_RECHARGE)
        {
            $start = mb_strpos($content,'密码',0 , 'utf-8') + 3;
            $find = mb_substr($content, $start, 8, 'utf-8');
            $content = str_replace($find, '******', $content);
        }
        return $content;
    }

    /**
     * 将盖网通对账的短信，未发送成功的进行发送
     */
    public static function againSmsLog()
    {
        $sql = "SELECT id,create_time,mobile,content,interface FROM {{sms_log}} WHERE `status`=0 AND type!=".self::TYPE_CAPTCHA;
        $result = Yii::app()->db->createCommand($sql)->queryAll();
        $time = time();
        $time2 = $time-120; //创建时间小于当前时间两分钟且未发送成功的进行补发
        foreach ($result as $row)
        {
            if($row['create_time'] > $time2) continue;
            if($row['mobile'] != '' && $row['content']){
                $rs = Sms::send($row['mobile'], $row['content'], $row['interface']);
                $updateArr = array(
                    'status' => $rs['send_status'],
                    'send_time' => $time,
                    'interface' => $rs['send_api']
                );
                Yii::app()->db->createCommand()->update('{{sms_log}}', $updateArr, 'id=' . $row['id']);
            }
        }
    }
}
