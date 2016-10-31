<?php

/**
 * This is the model class for table "{{order}}".
 *
 * The followings are the available columns in table '{{order}}':
 * @property string $order_id
 * @property string $order_sn
 * @property integer $member_id
 * @property string $user_name
 * @property integer $order_status
 * @property integer $pay_status
 * @property string $order_amount
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $payment_code
 * @property string $out_trade_sn
 * @property integer $pay_time
 * @property string $shipping_time
 * @property string $invoice_no
 * @property string $finished_time
 * @property integer $evaluation_status
 * @property string $evaluation_time
 * @property string $addtime
 * @property string $winning_number
 * @property integer $current_nper
 * @property integer $is_address
 *  @property integer $is_delivery
 */
class YfzOrder extends CActiveRecord
{

    const STATUS_PAY_SUCCESS = 1; //支付成功
    const STATUS_PAY_FAIL = 2; //支付失败
    const STATUS_PAY_NO = 0; //未支付
    
    const IS_SHIPPING_TRUE = 1 ; //已发货
    const IS_SHIPPING_FALSE = 0 ; //未发货
    const IS_DELIVERY_NO = 0; //未收货
    const IS_DELIVERY_YES = 1;//买家已收货
    
    
    const PAY_JF = 1;
    const PAY_WX = 2;
    
    /**
     * @return string the associated database table name
     */

    public function tableName()
    {
        return '{{order}}';
    }
    
    #数据库连接
    public function getDbConnection()
    {
        return Yii::app()->gwpart;
    }
    /**
     * 返回订单专题
     * @param type $order_status
     */
    public static function getOrderStatus($order_status=null)
    {
        $status = array(
            self::STATUS_PAY_NO => '未支付',
            self::STATUS_PAY_SUCCESS => '支付成功',
            self::STATUS_PAY_FAIL => '支付失败'
        );
        return is_null($order_status) ? $status : $status[$order_status] ;
    }
	
	 public static function getDeliveryStatus($key = null)
    {
        $type = array(
            self::IS_DELIVERY_NO => '未收货',
			self::IS_DELIVERY_YES => '已收货',
        );
        return $key !== null ? (isset($type[$key]) ? $type[$key] : '其它') : $type;
    }
	
    /**
     * 运输状态
     * @param type $ship
     */
    public static function getShipping($ship)
    {
        return $ship ? "已发货" : "未发货";
    }
    /**
     * 订单支付状态
     * @param type $status
     */
    public static function getPayStatus($status)
    {
        $pay = array(
            self::PAY_JF => '积分',
            self::PAY_WX => '微信'
        );
        return isset($pay[$status]) ? $pay[$status] : '未知的支付方式';
    }
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pay_time', 'required'),
            array('member_id, order_status, payment_id, pay_time, evaluation_status, is_address, is_delivery', 'numerical', 'integerOnly' => true),
            array('order_sn', 'length', 'max' => 20),
            array('user_name', 'length', 'max' => 30),
            array('order_amount, shipping_time, finished_time, evaluation_time,', 'length', 'max' => 10),
            array('payment_name, payment_code, out_trade_sn', 'length', 'max' => 100),
            array('invoice_no,invoice_company', 'length', 'max' => 255),
            array('invoice_company,invoice_no,is_delivery','required','on'=>'shippingUpdate'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('order_id, order_sn, member_id, user_name, order_status, order_amount, payment_id, payment_name, payment_code, out_trade_sn, pay_time, shipping_time, invoice_no, finished_time, evaluation_status, evaluation_time, addtime, is_address', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'order_id' => 'Order',
            'order_sn' => 'Order Sn',
            'member_id' => 'Member',
            'user_name' => 'User Name',
            'order_status' => 'Order Status',
            'pay_status' => 'Pay Status',
            'order_amount' => 'Order Amount',
            'payment_id' => 'Payment',
            'payment_name' => 'Payment Name',
            'payment_code' => 'Payment Code',
            'out_trade_sn' => 'Out Trade Sn',
            'pay_time' => 'Pay Time',
            'shipping_time' => 'Shipping Time',
            'invoice_no' => '快递单号',
            'invoice_company'=>'物流公司',
            'finished_time' => 'Finished Time',
            'evaluation_status' => 'Evaluation Status',
            'evaluation_time' => 'Evaluation Time',
            'addtime' => 'Addtime',
            'winning_number' => 'Winning Number',
            'current_nper' => 'Current Nper',
            'is_address' => 'Is Address',
            'is_delivery'=> 'Is delivery',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

//        $criteria->compare('order_id', $this->order_id, true);
//        $criteria->compare('order_sn', $this->order_sn, true);
//        $criteria->compare('member_id', $this->member_id);
//        $criteria->compare('user_name', $this->user_name, true);
//        $criteria->compare('order_status', $this->order_status);
//        $criteria->compare('order_amount', $this->order_amount, true);
//        $criteria->compare('payment_id', $this->payment_id);
//        $criteria->compare('payment_name', $this->payment_name, true);
//        $criteria->compare('payment_code', $this->payment_code, true);
//        $criteria->compare('out_trade_sn', $this->out_trade_sn, true);
//        $criteria->compare('pay_time', $this->pay_time);
//        $criteria->compare('shipping_time', $this->shipping_time, true);
//        $criteria->compare('invoice_no', $this->invoice_no, true);
//        $criteria->compare('finished_time', $this->finished_time, true);
//        $criteria->compare('evaluation_status', $this->evaluation_status);
//        $criteria->compare('evaluation_time', $this->evaluation_time, true);
//        $criteria->compare('addtime', $this->addtime, true);
//        $criteria->compare('is_address', $this->is_address);
        $criteria->join = 'left join {{order_goods_nper}} o on o.order_id=t.order_id';
        $dataProvider = new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
        return $dataProvider;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return YfzOrder the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    /**
     * 
     * @param type $order_id
     */
    public static function getOrderSnById($order_id)
    {
        $model = new self;
        $order = self::model()->findByPk($order_id);
        return $order ? $order : $model;
    }

    /**
     * 读取 一条订单
     * @param $order_sn 订单括号
     * @return array
     */
    public static function getOrderOne($order_sn){
        $model = new self;
        $data =  $model->find(array("condition"=>"order_sn=$order_sn"));
        if ($data){
            return $data;
        }
        return false;
    }

}
