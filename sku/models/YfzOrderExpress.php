<?php

/**
 *  快递100 推送结果 模型
 *
 * The followings are the available columns in table '{{order_express}}':
 * @property int $id
 * @property int $times
 * @property int $send_time
 * @property int $created
 * @property int $update_time
 * @property integer $state
 * @property integer $status
 * @property string $order_code
 * @property string $shipping_code
 * @property string $message
 * @property string $data
 */
class YfzOrderExpress extends CActiveRecord
{
    //1.快递单还在监控过程中;2.已签收;3.监控中止,异常运单
    const  STATUS_POLLING = 1;
    const  STATUS_SHUTDOWN = 2;
    const  STATUS_ABORT = 3;
    /**
     * 状态转换
     * @param string $data
     * @return int|string
     */
    public static function toStatus($data)
    {
        switch ($data) {
            case 'polling':
                $status = YfzOrderExpress::STATUS_POLLING;
                break;
            case 'shutdown':
                $status = YfzOrderExpress::STATUS_SHUTDOWN;
                break;
            case 'abort':
                $status = YfzOrderExpress::STATUS_ABORT;
                break;
            default:
                $status = '';
        }
        return $status;
    }

    public function tableName()
    {
        return '{{order_express}}';
    }

    #数据库连接

    public function getDbConnection()
    {
        return Yii::app()->gw;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('send_time, created, state, order_code, shipping_code, message, data', 'required'),
            array('state, status', 'numerical', 'integerOnly' => true),
            array('times, send_time, created', 'length', 'max' => 10),
            array('order_code', 'length', 'max' => 50),
            array('shipping_code', 'length', 'max' => 100),
            array('message', 'length', 'max' => 200),
            array('id, times, send_time, created, state, status, order_code, shipping_code, message, data', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('orderExpress', 'id'),
            'times' => Yii::t('orderExpress', '推送次数'),
            'send_time' => Yii::t('orderExpress', '推送时间'),
            'created' => Yii::t('orderExpress', '创建时间'),
            'state' => Yii::t('orderExpress', '快递单当前签收状态;包括0在途中、1已揽收、2疑难、3已签收、4退签、5同城派送中、6退回、7转单等7个状态，其中4-7需要另外开通才有效'),
            'status' => Yii::t('orderExpress', '状态（1.快递单还在监控过程中;2.已签收;3.监控中止,异常运单）'),
            'order_code' => Yii::t('orderExpress', '订单编号'),
            'shipping_code' => Yii::t('orderExpress', '快递单号'),
            'message' => Yii::t('orderExpress', '监控状态相关消息，如:3天查询无记录，60天无变化'),
            'data' => Yii::t('orderExpress', '回调消息结果'),
        );
    }

    public function search()
    {

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('times', $this->times, true);
        $criteria->compare('send_time', $this->send_time, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('state', $this->state);
        $criteria->compare('status', $this->status);
        $criteria->compare('order_code', $this->order_code, true);
        $criteria->compare('shipping_code', $this->shipping_code, true);
        $criteria->compare('message', $this->message, true);
        $criteria->compare('data', $this->data, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20, //分页
            ),
            'sort' => array(//'defaultOrder'=>' DESC', //设置默认排序
            ),
        ));
    }


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /**
     * 添加快递100订阅记录
     * @param array $order
     * @return bool|string
     * @throws Exception
     */
    public static function add($order)
    {
        try {
            self::checkCode($order['invoice_no']);
            $model = new YfzOrderExpress();
            $model->created = time();
            $model->send_time = time();
            $model->order_code = $order['order_sn'];
            $model->shipping_code = $order['invoice_no'];
            $model->times = 1;
            $result = self::send($order);
            $model->message = $result;
            $model->save(false);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return true;
    }


    /**
     * 更新快递100订阅
     * @param $order
     * @return bool
     * @throws Exception
     */
    public static function updated($order)
    {
        try {
            self::checkCode($order['invoice_no']);
            /** @var OrderExpress $model */
            $model = self::model()->findByAttributes(array('order_code' => $order['order_sn']));
            if (!$model) {
                $model = new YfzOrderExpress();
                $model->created = time();
            }
            $model->send_time = time();
            $model->order_code = $order['order_sn'];
            $model->shipping_code = $order['invoice_no'];
            $model->times = $model->isNewRecord ? 1 : $model->times + 1;
            $result = self::send($order);
            $model->message = $result;
            $model->save(false);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

    /**
     * 发送快递100订阅
     * @param array $data
     * @return bool|string
     * @throws Exception
     */
    public static function send($data)
    {
        //查询快递公司编码
        $company = Yii::app()->gw->createCommand('SELECT code FROM {{express}} WHERE name=:name')
            ->bindValue(':name', $data['invoice_company'])->queryScalar();
        if (empty($company)) {
            throw new Exception($data['invoice_company'] . '快递公司数据有误');
        }
        //组装参数
        $sqlMember = "SELECT member_id FROM {{order}} WHERE order_id = {$data['order_id']}";
        $memberResult = Yii::app()->gwpart->createCommand($sqlMember)->queryRow();
        $memberId = $memberResult['member_id'];//中奖用户member_id


        $sqlMobile = "SELECT mobile FROM {{member}} WHERE id = {$memberId}";
        $mobileReslut = Yii::app()->db->createCommand($sqlMobile)->queryRow();
        $mobile = $mobileReslut['mobile'];//中奖人手机号码

        $sqlAddress = "SELECT * FROM {{address}} a  WHERE member_id = {$memberId} AND a.default = 1";
        $addressResult = Yii::app()->db->createCommand($sqlAddress)->queryRow();
        $addressDefault = $addressResult['default'];//中奖人的默认收货地址
        if(empty($addressDefault)) {//没有默认收货地址则插入最新的收货地址
            $sqlDefault1 = "SELECT member_id,province_id,city_id,district_id,street from {{address}}
                where member_id={$memberId} AND id =(SELECT max(id) from {{address}} where member_id={$memberId})";
            $addressResult1 = Yii::app()->db->createCommand($sqlDefault1)->queryRow();
            $address = Region::getName($addressResult1["province_id"], $addressResult1["city_id"], $addressResult1["district_id"]) . ' ' . $addressResult1["street"];
        }else{//有默认收货地址
            $address = Region::getName($addressResult["province_id"], $addressResult["city_id"], $addressResult["district_id"]) . ' ' . $addressResult["street"];
        }
        $params = array(
            'company' => strtolower($company),
            'number' => $data['invoice_no'],
            'to' => $address,
            'key' => 'MnojKcKb1187',
            'parameters' => array(
                'callbackurl' => Yii::app()->createAbsoluteUrl('/yifenzi/kuaidi100/index', array('code' => $data['order_sn'])),
                'mobiletelephone' => $mobile,
                'seller' => '盖象商城',
            )
        );
        $http = new HttpClient('');
        return $http->quickPost('http://www.kuaidi100.com/poll', array('schema' => 'json', 'param' => json_encode($params)));
    }

    /**
     * 过滤假运单号
     * @param $code
     * @throws Exception
     */
    public static function checkCode($code)
    {
        $msg = '运单号可能有误，请核对';
        //连续5个相同数字
        if (preg_match('/([\d])\1{4,}/', $code)) {
            throw new Exception ($msg);
        }
        //连续5个顺增|递减
        if (preg_match('/(?:(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){4}|(?:9(?=8)|8(?=7)|7(?=6)|6(?=5)|5(?=4)|4(?=3)|3(?=2)|2(?=1)|1(?=0)){4})\d/', $code)) {
            throw new Exception ($msg);
        }
    }
}
