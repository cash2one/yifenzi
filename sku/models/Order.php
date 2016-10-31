<?php

/**
 * This is the model class for table "{{orders}}".
 *
 * The followings are the available columns in table '{{orders}}':
 * @property integer $id
 * @property integer $code
 * @property string $member_id
 * @property integer $store_id
 * @property integer $type
 * @property integer $node
 * @property string $total_price
 * @property string $pay_price
 * @property integer $address_id
 * @property integer $shipping_type
 * @property integer $status
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property GwMember $member
 */
class Order extends CActiveRecord {

    public $goods_count;
    public $gai_number;
    public $name;
    public $num;
    public $price;
    public $goods_name;
    public $end_time;
    public $network;  //网点
    public $complete;
    public $day;
    public $machine;
    public $pageSize = 5000;

//月份
    //提示类型
    const NOW = 1;
    const ZHIYOUYIBAI = 2; //首次消费返回积分 至优壹佰广告
    //订单状态
    const STATUS_NEW = 0;                //新订单
    const STATUS_PAY = 2;                //已支付
    const STATUS_SEND = 3;               //已发货
    const STATUS_AUTO_ORDER_TAKENRS = 4; //商户已接单
    const STATUS_REFUNDED = 5;           //已退款
    const STATUS_COMPLETE = 6;           //交易完成
    const STATUS_CANCEL = 7;             //交易关闭
    const STATUS_INVALID = 8;            //订单失效
    const STATUS_FROZEN = 9;             //订单冻结
    const STATUS_SENDING = 10;           //配送中
    const REFUND_STATUS_NONE = 0;                //订单退款状态 无
    const REFUND_STATUS_PENDING = 1;            //订单退款状态 申请中
    const REFUND_STATUS_FAILURE = 2;            //订单退款状态 失败
    const REFUND_STATUS_SUCCESS = 3;            //订单退款状态 成功
    //订单类型
    const TYPE_SUPERMARK = 1; //超市
    const TYPE_MACHINE = 2; //售货机
    const TYPE_FRESH_MACHINE = 3;   //生鲜机
    const TYPE_MACHINE_CELL_STORE = 4;   //售货机格仔铺
    const TYPE_FRESH_MACHINE_SMALL = 5; //俊鹏生鲜机
    //送货方式
    const SHIPPING_TYPE_TAKE = 1;   //到店自取
    const SHIPPING_TYPE_SEND = 2;   //送货上门
    //售货机提货方式  machine_take_type
    const MACHINE_TAKE_TYPE_WITH_CODE = 0;            //手机下单 提货码取货
    const MACHINE_TAKE_TYPE_AFTER_PAY = 1;                //当面下单支付 取货
    const IS_AUTO_CANCEL_NO = 0;
    const IS_AUTO_CANCEL_YES = 1;
    //支付方式
    const PAY_TYPE_POINT = 1;
    const PAY_TYPE_CASH = 2;
    const PAY_TYPE_WECHAT = 3;
    const PAY_TYPE_BANK = 4;

    /**
     * @var string 搜索用
     */
    public $endTime;
    public $month;
    public $isExport; //是否导出excel

    /**
     * 售货机取货方式
     * @param null $k
     * @return  array | null
     */

    public static function machineTakeType($k = null) {
        $arr = array(
            self::MACHINE_TAKE_TYPE_WITH_CODE => Yii::t('order', '提货码取货'),
            self::MACHINE_TAKE_TYPE_AFTER_PAY => Yii::t('order', '当面下单取货'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    /**
     * 订单支付方式
     * @param null $k
     * @return  array | null
     */
    public static function getPayType($k = null) {
        $arr = array(
            self::PAY_TYPE_POINT => Yii::t('order', '积分支付'),
            self::PAY_TYPE_CASH => Yii::t('order', '银联支付'),
            self::PAY_TYPE_WECHAT => Yii::t('order','微信支付'),
            self::PAY_TYPE_BANK => Yii::t('order','pos机银联'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    /**
     * 订单状态
     * @param null $k
     * @return array|null
     */
    public static function status($k = null) {
        $arr = array(
            self::STATUS_NEW => Yii::t('order', '新订单'),
            self::STATUS_PAY => Yii::t('order', '已支付'),
            self::STATUS_SEND => Yii::t('order', '已发货'),
//            self::STATUS_REFUNDING=>Yii::t('order','申请退款'),
            self::STATUS_REFUNDED => Yii::t('order', '已退款'),
            self::STATUS_COMPLETE => Yii::t('order', '交易完成'),
            self::STATUS_CANCEL => Yii::t('order', '交易关闭'),
            self::STATUS_SENDING => Yii::t('order', '配送中'),
//        	self::STATUS_INVALID=>Yii::t('order','已失效'),
            self::STATUS_FROZEN => Yii::t('order', '已冻结'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    /**
     * 订单类型
     * @param null $k
     * @return  array | null
     */
    public static function type($k = null) {
        $arr = array(
            self::TYPE_SUPERMARK => Yii::t('order', '门店'),
            self::TYPE_MACHINE => Yii::t('order', '售货机'),
            self::TYPE_FRESH_MACHINE => Yii::t('order', '生鲜机'),
//        	self::TYPE_MACHINE_CELL_STORE => Yii::t('order','格仔铺'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    /**
     * 订单支付方式
     * @param null $k
     * @return  array | null
     */
    public static function pay_type($k = null) {
        $arr = array(
            1 => Yii::t('order', '积分支付'),
            2 => Yii::t('order', '现金支付'),
            3 => Yii::t('order', '其他'),
//        	self::TYPE_MACHINE_CELL_STORE => Yii::t('order','格仔铺'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    /**
     * 送货方式
     * @param null $k
     * @return  array | null
     */
    public static function shippingType($k = null) {
        $arr = array(
            self::SHIPPING_TYPE_TAKE => Yii::t('order', '到店自取'),
            self::SHIPPING_TYPE_SEND => Yii::t('order', '送货上门')
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    const PAY_STATUS_NO = 0;
    const PAY_STATUS_YES = 1;

    /**
     * 支付状态
     * （1未支付，2已支付）
     * @param null $k
     * @return array|null
     */
    public static function payStatus($k = null) {
        $arr = array(
            self::PAY_STATUS_NO => Yii::t('order', '未支付'),
            self::PAY_STATUS_YES => Yii::t('order', '已支付'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    //1未发货，2等待发货，3已出货，4签收

    const DELIVERY_STATUS_NOT = 1;
    const DELIVERY_STATUS_WAIT = 2;
    const DELIVERY_STATUS_SEND = 3;
    const DELIVERY_STATUS_RECEIVE = 4;

    /**
     * 配送状态
     * @param null $k
     * @return array|null
     */
    public static function deliveryStatus($k = null) {
        $arr = array(
            self::DELIVERY_STATUS_NOT => Yii::t('order', '未发货'),
            self::DELIVERY_STATUS_WAIT => Yii::t('order', '等待发货'),
            self::DELIVERY_STATUS_SEND => Yii::t('order', '已出货'),
            self::DELIVERY_STATUS_RECEIVE => Yii::t('order', '已签收'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    /**
     * 退款状态
     * 0无，1申请中，2失败，3成功
     * @param null $k
     * @return array|null
     */
    public static function refundStatus($k = null) {
        $arr = array(
            self::REFUND_STATUS_NONE => Yii::t('order', '无'),
            self::REFUND_STATUS_PENDING => Yii::t('order', '申请中'),
            self::REFUND_STATUS_FAILURE => Yii::t('order', '退款失败'),
            self::REFUND_STATUS_SUCCESS => Yii::t('order', '退款成功'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    //0无，1协商中，2失败，3同意，4，成功

    const RETURN_STATUS_NONE = 0;
    const RETURN_STATUS_PENDING = 1;
    const RETURN_STATUS_FAILURE = 2;
    const RETURN_STATUS_AGREE = 3;
    const RETURN_STATUS_SUCCESS = 4;
    const RETURN_STATUS_CANCEL = 5;

    /**
     * 售货机相关
     * @var unknown
     */
    const MACHINE_STATUS_NO = 0;            //未备货
    const MACHINE_STATUS_YES = 1;        //已备货、

    /**
     * 支付状态
     * @param null $k
     * @return array|null
     */

    public static function machineStatus($k = null) {
        $arr = array(
            self::MACHINE_STATUS_NO => Yii::t('order', '未备货'),
            self::MACHINE_STATUS_YES => Yii::t('order', '已备货'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    const GOODS_STATUS_NO = 0;  //出货失败
    const GOODS_STATUS_YES = 1; //出货成功
    const GOODS_STATUS_FAIL_LOCK =-1 ; //电子锁没锁好
    const GOODS_STATUS_FAIL = -2 ; //出货指令没收到

    /*
     * 与机器进行时间验证之后的出货状态
     */

    public static function GoodsStatus($k = null) {
        $arr = array(
            self::GOODS_STATUS_NO => Yii::t('order', '出货失败'),
            self::GOODS_STATUS_YES => Yii::t('order', '出货成功'),
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }

    /**
     * 商家后台已卖出商品搜索列表
     * @param $partner_id 商家id
     * @return CDbCriteria
     */
    public function searchSold($partner_id) {
        $criteria = new CDbCriteria;
        $criteria->addCondition('t.partner_id=:partner');
        $criteria->params = array(':partner' => $partner_id);

        if ($this->code !== null)
            $criteria->compare('t.code', $this->code);
        if ($this->status !== null)
            $criteria->compare('t.status', $this->status);
        if ($this->type !== null)
            $criteria->compare('t.type', $this->type);
        if ($this->refund_status !== null) {
            $criteria->compare('t.refund_status', $this->refund_status);
        }
        //时间区间搜索
        $searchDate = Tool::searchDateFormat($this->create_time, $this->end_time);
        $criteria->compare('t.create_time', ">=" . $searchDate['start']);
        $criteria->compare('t.create_time', "<" . $searchDate['end']);


        //商品搜索
        if (!empty($this->goods_name)) {
            $criteria->join = 'LEFT JOIN {{orders_goods}} as o ON o.order_id=t.id';
            $criteria->compare('o.name', trim($this->goods_name), true);
        }

        if (!empty($this->network)) {
            $criteria->with = array('store', 'freshMachine', 'machine');
            $criteria->addCondition(' store.name like :s1 or machine.name like :s1 or freshMachine.name like :s1');

            $extendParam = array(':s1' => '%' . $this->network . '%');
            $criteria->params = array_merge($criteria->params, $extendParam);
//            $criteria->compare("store.name",  $this->network,true);
//            $criteria->orWhere("freshMachine.name",  $this->network,true);
//            $criteria->compare('machine.name',  $this->network,true,'or');
        }

        $criteria->order = 't.create_time DESC';
        return $criteria;
    }

    /**
     * 商家后台已卖出商品搜索列表
     * @param $partner_id 商家id
     * @return CDbCriteria
     */
    public function backendSearch() {
        $criteria = new CDbCriteria;
//        $criteria->with=array('ordersGoods');
        $criteria->select = 't.*,ordersGoods.name,ordersGoods.num,ordersGoods.supply_price,ordersGoods.price,ordersGoods.total_price';
        $criteria->compare('partner_id', $this->partner_id, true);
        $criteria->compare('t.status', $this->status, false);
        $pagination = array();
        $criteria->join = "LEFT JOIN {{orders_goods}} ordersGoods on t.id = ordersGoods.order_id";
        // 关联

        if (!empty($this->isExport)) {
            $pagination['pageSize'] = 5000;
        } else {
            $pagination = array();
        }
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => $pagination,
            'sort' => array(
                'defaultOrder' => 'id DESC',
            ),
        ));
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{orders}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('create_time', 'required'),
            array('is_auto_cancel,code, goods_code,store_id, type, node, address_id, shipping_type, status, create_time,pay_status,machine_id,machine_status,goods_count,machine_take_type,refund_status,pay_type,machine_status,end_time', 'numerical', 'integerOnly' => true),
            array('pay_time,send_time,sign_time,cancel_time,member_id, total_price, pay_price', 'length', 'max' => 11),
            array('shipping_time', 'length', 'max' => 32),
            array('remark,seller_remark', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('pay_time,send_time,sign_time,cancel_time,is_auto_cancel,id, code, member_id, store_id, type, node, total_price, pay_price, address_id, shipping_type, status, create_time,pay_status,goods_code,machine_id,machine_status,goods_count,shipping_time,machine_take_type,remark,seller_remark,refund_stauts,pay_type,machine_status,gai_number,end_time,goods_name,network,distribute_config', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'address' => array(self::BELONGS_TO, 'OrderAddress', 'address_id'),
            'ordersGoods' => array(self::HAS_MANY, 'OrdersGoods', 'order_id'),
            'machine' => array(self::BELONGS_TO, 'VendingMachine', 'machine_id'),
            'store' => array(self::BELONGS_TO, 'Supermarkets', 'store_id'),
            'freshMachine' => array(self::BELONGS_TO, 'FreshMachine', 'machine_id'),
            'distribution_order' => array(self::HAS_ONE, 'DistributionOrder', 'order_id'),
            'partner' => array(self::BELONGS_TO, 'Partners', 'partner_id'),
            'member' => array(self::BELONGS_TO, 'Member', 'member_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('order', 'ID'),
            'code' => Yii::t('order', '订单编号'),
            'goods_code' => Yii::t('order', '提货码'),
            'partner_id' => Yii::t('order', '商家id'),
            'member_id' => Yii::t('order', '会员id'),
            'store_id' => Yii::t('order', '门店id'),
            'machine_id' => Yii::t('order', '机器id'),
            'type' => Yii::t('order', '订单类型'),
            'node' => Yii::t('order', '业务节点'),
            'total_price' => Yii::t('order', '总价'),
            'pay_price' => Yii::t('order', '支付价格'),
            'address_id' => Yii::t('order', '送货地址'),
            'shipping_type' => Yii::t('order', '送货方式'),
            'status' => Yii::t('order', '订单状态'),
            'refund_status' => Yii::t('order', '退款状态'),
            'create_time' => Yii::t('order', '创建时间'),
            'pay_status' => Yii::t('order', '支付状态'),
            'remark' => Yii::t('order', '备注'),
            'seller_remark' => Yii::t('order', '商家备注'),
            'gai_number' => Yii::t('order', '商家盖网号'),
            'pay_time' => Yii::t('order', '支付时间'),
            'send_time' => Yii::t('order', '发货时间'),
            'sign_time' => Yii::t('order', '签收、完成时间'),
            'cancel_time' => Yii::t('order', '取消时间'),
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;
//        $criteria->select = 't.*,ordersGoods.*';
        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.code', $this->code, true);
        $criteria->compare('t.member_id', $this->member_id, true);
        $criteria->compare('t.store_id', $this->store_id);
        $criteria->compare('t.machine_id', $this->machine_id);
        if (!empty($this->type)) {
            $criteria->compare('t.type', $this->type);
        }
        $criteria->compare('t.node', $this->node);
        $criteria->compare('t.total_price', $this->total_price, true);
        $criteria->compare('t.pay_price', $this->pay_price, true);
        $criteria->compare('t.address_id', $this->address_id);
        if (!empty($this->shipping_type)) {
            $criteria->compare('t.shipping_type', $this->shipping_type);
        }
        if ($this->status !== null) {
            $criteria->compare('t.status', $this->status);
        }
        if ($this->refund_status !== null) {
            $criteria->compare('t.refund_status', $this->refund_status);
        }
        $criteria->compare('t.create_time', $this->create_time);
        $criteria->with = array('member', 'partner', 'store', 'freshMachine', 'machine', 'ordersGoods');
        $criteria->compare('member.gai_number', $this->gai_number, true);

        if (!empty($this->network)) {
//            $criteria->with = array('store', 'freshMachine', 'machine');
//            if(empty($this->type)&& $this->type ==seLf::TYPE_FRESH_MACHINE){
//                  $criteria->compare('freshMachine.name', $this->network, true);
//            }else{
//            $criteria->compare('store.name', $this->network, true);
//            $criteria->compare('machine.name', $this->network, true, 'or');
//            }
            $criteria->condition = '(t.type=' . self::TYPE_SUPERMARK . ' && store.name like "%' . $this->network . '%") OR (t.type=' . self::TYPE_MACHINE . ' && machine.name like "%' . $this->network . '%") OR (t.type=' . self::TYPE_FRESH_MACHINE . ' && freshMachine.name like "' . $this->network . '")';
        }
        $criteria->order = 't.id desc';

        if (!empty($this->isExport)) {
            $pagination['pageSize'] = $this->pageSize;
            $pagination['pageVar']  = 'page';
        } else {
            $pagination = array();
            $pagination['pageVar']  = '';
        }

        return new CActiveDataProvider($this, array(
            'pagination' => $pagination,
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Orders the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 生成订单编码
     */
    public static function _createCode($type = self::TYPE_SUPERMARK, $memberId = 0, $len = 24) {
        return substr($type . rand(100, 999) . date('YmdGis') . rand(100, 999) . substr($memberId + rand(1000, 9000), 0, 4), 0, $len);
    }

    /**
     * 生成取货码
     */
    public static function _createGoodsCode() {
        return rand(10000000, 99999999);
    }

    /**
     * 生成生鲜机订单
     *
     * 多个货道拆分多张订单
     *
     */
    public function createFreshMachineOrder($storeId, $memberId, $goods, $remark = '', $stock = true, $trans = true) {
        $memberId = $memberId * 1;
        $storeId = $storeId * 1;
        $rs = array();
        $rs['success'] = false;
        $rs['data'] = array();
        $rs['code'] = ErrorCode::COMMOM_SYS_ERROR;

        // 执行事务
        if ($trans == true)
            $transaction = Yii::app()->db->beginTransaction();
        try {
            
        } catch (Exception $e) {
            if ($trans == true)
                $transaction->rollBack();
            throw new Exception($e->getMessage());
        }

        if ($trans == true)
            $transaction->commit();
    }

    /**
     * 创建无商品订单 一般用于退款
     */
    public static function createNoGoodsOrder($type = Order::TYPE_FRESH_MACHINE, $storeId, $partnerId, $memberId, $total_price, $remark = '', $isPay = false) {
        $order = new Order();
        $order->code = Order::_createCode($type, $memberId);
        $order->type = $type;
        $order->goods_code = Order::_createGoodsCode();
        $order->member_id = $memberId;
        $order->partner_id = $partnerId;
        $order->total_price = $total_price;
        $order->status = Order::STATUS_NEW;

        if ($isPay === true) {
            $order->status = Order::STATUS_PAY;
            $order->pay_status = Order::PAY_STATUS_YES;
            $order->pay_price = $order->total_price;
        }

        if ($type == self::TYPE_SUPERMARK) {
            $order->store_id = $storeId;
        } elseif ($type == self::TYPE_MACHINE || $type == self::TYPE_FRESH_MACHINE || $type == self::TYPE_MACHINE_CELL_STORE ||$type == self::TYPE_FRESH_MACHINE_SMALL) {
            $order->machine_id = $storeId;
            $order->machine_status = Order::MACHINE_STATUS_YES;
        }
        $order->seller_remark = $remark;
        $order->create_time = time();

        $order->save();

        return $order['code'];
    }

    /**
     * 生成订单
     *
     * $goods  goods的数组   商品唯一标示 以及  数量
     *
     * @param $stock 是否需要处理库存
     * @param $machineTakeType  售货机提货方式
     * @param $fatherId  父订单id
     * @param $trans  是否启用事务
     * @param $partnerId  是否指定商家id
     *
     *
     */
    public function createOrder($type = Order::TYPE_SUPERMARK, $storeId, $memberId, $goods, $addressId = 0, $shippingType = 0, $shipping_time = '', $machineTakeType = 0, $remark = '', $stock = true, $trans = true, $fatherId = null, $partnerId = null) {
        set_time_limit(600);
        $memberId = $memberId * 1;
        $storeId = $storeId * 1;
        $fatherId = $fatherId * 1;
        $partnerId = $partnerId * 1;
        $rs = array();
        $rs['success'] = false;
        $rs['data'] = array();
        $rs['code'] = ErrorCode::COMMOM_SYS_ERROR;
        $member_info = Member::model()->getMemberById($memberId); // 直接获取了mobile
        $mobile = $member_info;

        // 执行事务
        if ($trans == true)
            $transaction = Yii::app()->db->beginTransaction();
        try {
            $ids = array();
            foreach ($goods as $g) {
                $ids[] = $g['id'];
            }
            
            if ($type == self::TYPE_SUPERMARK) {
                $store = Yii::app()->db->createCommand(' SELECT id,name,member_id,is_delivery,delivery_mini_amount,delivery_fee,max_amount_preday,delivery_start_amount FROM ' . Supermarkets::model()->tableName() . ' WHERE id= ' . $storeId)->limit(1)->queryRow();
            } elseif ($type == self::TYPE_MACHINE || $type == self::TYPE_MACHINE_CELL_STORE) {
                $store = Yii::app()->db->createCommand(' SELECT id,name,member_id FROM ' . VendingMachine::model()->tableName() . ' WHERE id= ' . $storeId)->limit(1)->queryRow();
            } elseif ($type == self::TYPE_FRESH_MACHINE) {
                $store = Yii::app()->db->createCommand(' SELECT id,name,member_id,type FROM ' . FreshMachine::model()->tableName() . ' WHERE id= ' . $storeId)->limit(1)->queryRow();
            }

            if (empty($store)) {
                $rs['code'] = ErrorCode::STORE_NO_EXIST;
                return $rs;
            }

            if ($partnerId != null) {
                $partnerId = $partnerId * 1;
                $partner = Yii::app()->db->createCommand(' SELECT member_id,id,name FROM ' . Partners::model()->tableName() . ' WHERE id = ' . $partnerId)->queryRow();
            } else {
                $partner = Yii::app()->db->createCommand(' SELECT member_id,id,name FROM ' . Partners::model()->tableName() . ' WHERE member_id = ' . $store['member_id'])->limit(1)->queryRow();
            }

            if (empty($partner)) {
                $rs['code'] = ErrorCode::PARTNER_NO_ACCOUNT;
                return $rs;
            }

            //查询用户是否该商家,对售货机和生鲜机不做判断 ，因为售货机和生鲜机可以支持无登录支付，需使用商家账号来进行流水分配
            if ($type == self::TYPE_SUPERMARK) {
                if ($partner['member_id'] == $memberId) {
                    $rs['code'] = ErrorCode::ORDER_MEMBER_ERROR;
                    return $rs;
                }
            }


            //判断送货方式 是否可用
            if ($shippingType == Order::SHIPPING_TYPE_SEND && $type == Order::TYPE_SUPERMARK && $store['is_delivery'] != Supermarkets::DELIVERY_YES) {
                $rs['code'] = ErrorCode::ORDER_SHIPPING_TYPE_ERROR;
                return $rs;
            }

            //查找地址  如果为空的话  默认取最新地址
            if ($type == Order::TYPE_SUPERMARK && $shippingType == Order::SHIPPING_TYPE_SEND && !$addressId) {
                $address = Address::model()->find('member_id=:member_id ORDER BY id DESC', array(':member_id' => $memberId));
                if ($address) {
                    $addressId = $address->id;
                } else {
                    $rs['code'] = ErrorCode::ORDER_SHIPPING_ADDRESS_ERROR;
                    return $rs;
                }
            } elseif ($type == Order::TYPE_SUPERMARK && $shippingType == Order::SHIPPING_TYPE_SEND && $addressId) {
                $address = Address::model()->findByPk($addressId);

                if (empty($address) || $address->member_id != $memberId) {
                    $rs['code'] = ErrorCode::ORDER_SHIPPING_ADDRESS_ERROR;
                    return $rs;
                }
            }

            $address_id = 0;
            if ($type == Order::TYPE_SUPERMARK && $shippingType == Order::SHIPPING_TYPE_SEND && $address) {
                $oa = new OrderAddress();
                $oa->member_id = $address['member_id'];
                $oa->real_name = $address['real_name'];
                $oa->mobile = $address['mobile'];
                $oa->province_id = $address['province_id'];
                $oa->city_id = $address['city_id'];
                $oa->district_id = $address['district_id'];
                $oa->street = $address['street'];
                $oa->zip_code = $address['zip_code'];
                $oa->save();

                $address_id = Yii::app()->db->getLastInsertID();
            }

           

            $gcri = new CDbCriteria();
            $gcri->addInCondition('id', $ids);
            if ($type == self::TYPE_SUPERMARK) {
                $projectId = API_PARTNER_SUPER_MODULES_PROJECT_ID;
                $storeGoods = Yii::app()->db->createCommand(' SELECT g.*,t.id ,t.goods_id FROM ' . SuperGoods::model()->tableName() . ' as t LEFT JOIN ' . Goods::model()->tableName() . ' as g ON t.goods_id=g.id WHERE t.super_id=' . $storeId . ' AND t.status=' . SuperGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' AND t.id IN ( ' . implode(',', $ids) . ')')->queryAll();
            } elseif ($type == self::TYPE_MACHINE) {
                $projectId = API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID;
                $storeGoods = Yii::app()->db->createCommand(' SELECT g.*,t.id,t.goods_id FROM ' . VendingMachineGoods::model()->tableName() . ' as t LEFT JOIN ' . Goods::model()->tableName() . ' as g ON t.goods_id=g.id WHERE  t.machine_id=' . $storeId . ' AND t.status=' . VendingMachineGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' AND t.id IN ( ' . implode(',', $ids) . ')')->queryAll();
            } elseif ($type == self::TYPE_FRESH_MACHINE && $store['type'] != FreshMachine::FRESH_MACHINE_SMALL) {
                $projectId = API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID;
                $storeGoods = Yii::app()->db->createCommand('SELECT l.code,b.* FROM ' . FreshMachineLine::model()->tableName() . ' as l inner join (SELECT g.*,t.id as t_id,t.goods_id,t.weight as t_weight,t.line_id FROM ' . FreshMachineGoods::model()->tableName() . ' as t LEFT JOIN ' . Goods::model()->tableName() . ' as g ON t.goods_id=g.id WHERE  t.machine_id=' . $storeId . ' AND t.status=' . FreshMachineGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' AND t.id IN ( ' . implode(',', $ids) . ')) as b on b.line_id = l.id')->queryAll();
            } elseif ($type == self::TYPE_MACHINE_CELL_STORE) {
                $projectId = API_MACHINE_CELL_STORE_PROJECT_ID;
                $storeGoods = Yii::app()->db->createCommand(' SELECT g.*,t.id,t.goods_id,t.code FROM ' . VendingMachineCellStore::model()->tableName() . ' as t LEFT JOIN ' . Goods::model()->tableName() . ' as g ON t.goods_id=g.id WHERE  t.machine_id=' . $storeId . ' AND t.status=' . VendingMachineCellStore::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' AND t.id IN ( ' . implode(',', $ids) . ')')->queryAll();
            } elseif ($type == self::TYPE_FRESH_MACHINE && $store['type'] == FreshMachine::FRESH_MACHINE_SMALL) {
                $projectId = API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID;
//                $storeGoods1 = Yii::app()->db->createCommand('SELECT l.code,b.* FROM ' . FreshMachineLine::model()->tableName() . ' as l inner join (SELECT g.*,t.id as t_id,t.goods_id,t.weight,t.line_id FROM ' . FreshMachineGoods::model()->tableName() . ' as t LEFT JOIN ' . Goods::model()->tableName() . ' as g ON t.goods_id=g.id WHERE  t.machine_id=' . $storeId . ' AND t.status=' . FreshMachineGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' AND t.id IN ( ' . implode(',', $ids) . ')) as b on b.line_id = l.id')->queryAll();
//                $gids1 = array();
                $lids = array();
//                foreach ($storeGoods1 as $v) {
//                    $gids1[] = $v['goods_id'];
//                }
                $nums1 = array();
                foreach($goods as $v){
                    $nums1[$v['id']] =$v['num']; 
                }
                $storeGoods2 = Yii::app()->db->createCommand('SELECT l.code,b.* FROM ' . FreshMachineLine::model()->tableName() . ' as l inner join (SELECT g.*,t.id as t_id,t.goods_id,t.weight as t_weight,t.line_id,t.create_time as t_time FROM ' . FreshMachineGoods::model()->tableName() . ' as t LEFT JOIN ' . Goods::model()->tableName() . ' as g ON t.goods_id=g.id WHERE  t.machine_id=' . $storeId . ' AND t.status=' . FreshMachineGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' AND t.goods_id IN ( ' . implode(',', $ids) . ')) as b on b.line_id = l.id  order by t_time asc')->queryAll();
                foreach ($storeGoods2 as $v) {
                    foreach ($ids as $k1 => $v1) {
                        if ($v['goods_id'] == $v1) {
                            $s = array('t_id' => $v['t_id'], 'line_id' => $v['line_id']);
                            $lids[$v1][] = $s;
                        }
                    }
                    $lids1[] = $v['line_id'];
                }  
                
                $stocks1 = ApiStock::goodsStockList($storeId,  array_values($lids1), $projectId);
                if (empty($stocks1)) {
                    $rs['code'] = ErrorCode::GOOD_STOCK_NOT_EXIST;
                    return $rs;
                }
               
                $goods1 =array();
                foreach ($lids as $k => $v) {
                    if (count($v) > 1) {                         
                        foreach ($goods as $k1 => $v1) {
                            $num_rs = $v1['num']; 
                            if($v1['id'] == $k){                                
                                foreach ($v as $v2) {
                                    if ($num_rs >= $stocks1[$v2['line_id']]['stock'] && $stocks1[$v2['line_id']]['stock']>0) {
                                        $goods1[] = array('id'=>$v2['t_id'],'num'=>$stocks1[$v2['line_id']]['stock']);
                                        $num_rs = $num_rs - $stocks1[$v2['line_id']]['stock'];               
                                       
                                    }elseif($num_rs<$stocks1[$v2['line_id']]['stock'] && $stocks1[$v2['line_id']]['stock']>0 &&$num_rs>0){
                                        $goods1[] = array('id'=>$v2['t_id'],'num'=>$num_rs);
                                        $num_rs = 0;
                                    }
                                }
                            }
                        }
                    }else{
                        $goods1[] = array('id'=>$v[0]['t_id'],'num'=>$nums1[$k]);
                    }
                }
                $goods = $goods1;

                $ids_new = array();
                foreach ($goods as $g) {
                    $ids_new[] = $g['id'];
                }
                $storeGoods = Yii::app()->db->createCommand('SELECT l.code,b.* FROM ' . FreshMachineLine::model()->tableName() . ' as l inner join (SELECT g.*,t.id as t_id,t.goods_id,t.weight as t_weight,t.line_id FROM ' . FreshMachineGoods::model()->tableName() . ' as t LEFT JOIN ' . Goods::model()->tableName() . ' as g ON t.goods_id=g.id WHERE  t.machine_id=' . $storeId . ' AND t.status=' . FreshMachineGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' AND t.id IN ( ' . implode(',', $ids_new) . ')) as b on b.line_id = l.id')->queryAll();
            } else {
                $rs['code'] = ErrorCode::ORDER_GOODS_LESS;
                if ($trans == true)
                    $transaction->rollback();
                return $rs;
            }
                                                
            if (count($storeGoods) != count($goods)) {
                $rs['code'] = ErrorCode::ORDER_GOODS_LESS;
                if ($trans == true)
                    $transaction->rollback();
                return $rs;
            }

            $sgs = array();
            $gids = array();
            $goods_nums = array();


            foreach ($goods as $g) {
                $goods_nums[$g['id']] = $g['num'];
            }

            $frozen_nums = array();
            $frozen_ids = array();
            $frozen_lines = array();
            $line_ids = array();
            $line_goodsid = array();
            foreach ($storeGoods as $k => $sg) {
                $sgid = isset($sg['t_id']) ? $sg['t_id'] : $sg['id'];
                $gids[] = $sg['goods_id'];            
                $frozen_ids[] = $sg['goods_id'];
                $frozen_nums[$sgid] = $goods_nums[$sgid];
                if (isset($sg['line_id'])) {
                    $sgs[$sgid] = array('id'=>$sg['goods_id'],'num'=>$goods_nums[$sgid]);
                    $line_ids[] = $sg['line_id'];
                    $line_goods[$sg['line_id']] = $sgid;
                    $frozen_lines[] = $sg['line_id'];
                }else{
                    $sgs[$sg['goods_id']]['id'] = $sg['goods_id'];
                    $sgs[$sg['goods_id']]['num'] = $goods_nums[$sgid];
                }
            }
 

            sort($frozen_ids);
            ksort($frozen_lines);
            ksort($frozen_nums);
            $frozen_lines = array_values($frozen_lines);
            $frozen_nums = array_values($frozen_nums);

            if ($stock === true) {
                //查询库存
                $stocks = ApiStock::goodsStockList($storeId, $projectId == API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID ? $line_ids : $gids, $projectId);
                if (empty($stocks)) {
                    $rs['code'] = ErrorCode::GOOD_STOCK_NOT_EXIST;
                    return $rs;
                }
                foreach ($stocks as $key => $val) {
                    if ($type == self::TYPE_FRESH_MACHINE) {
                        if ($val < $sgs[$line_goods[$key]]['num']) {
                            $rs['code'] = ErrorCode::GOOD_STOCK_NOT_ENOUGH;
                            if ($trans == true)
                                $transaction->rollback();
                            return $rs;
                        }
                    } elseif ($val < $sgs[$key]['num']) {
                        $rs['code'] = ErrorCode::GOOD_STOCK_NOT_ENOUGH;
                        if ($trans == true)
                            $transaction->rollback();
                        return $rs;
                    }
                }
            }
            $gai_number = Member::getMemberById($memberId, array('gai_number'));
            $order = new Order();
            $order->father_id = !empty($fatherId) ? $fatherId * 1 : 0;
            $order->code = Order::_createCode($type, $memberId);
            $order->type = $type;
            $order->goods_code = Order::_createGoodsCode();
            $order->member_id = $memberId;
            $order->status = Order::STATUS_NEW;
            $order->address_id = $address_id;
            $order->mobile = !empty($address->mobile) ? $address->mobile : $mobile;
            $order->shipping_type = $shippingType;
            $order->shipping_time = $shipping_time;
            $order->machine_take_type = $machineTakeType * 1;
            $order->create_time = time();
            $order->goods_status = Order::GOODS_STATUS_NO;
            $order->gai_number = $gai_number['gai_number'];
            if (!empty($remark))
                $order->remark = $remark;
            $order->save();
            $order_id = Yii::app()->db->getLastInsertID();

            if ($type == self::TYPE_SUPERMARK) {
                $order->store_id = $storeId;
            } elseif ($type == self::TYPE_MACHINE || $type == self::TYPE_FRESH_MACHINE || $type == self::TYPE_MACHINE_CELL_STORE) {
                $order->machine_id = $storeId;
            }


            $order->partner_id = $partner['id'];

            $total_price = 0;

            foreach ($storeGoods as $g) {
                $orderGoods = new OrdersGoods();
                $orderGoods->sgid = isset($g['t_id']) ? $g['t_id'] : $g['id'];
                $orderGoods->gid = $g['goods_id'];
                $orderGoods->num = isset($g['t_id'])?$sgs[$g['t_id']]['num']:$sgs[$g['goods_id']]['num'];
                if ($orderGoods->num <= 0) {
                    continue;
                }

                if ($type == self::TYPE_MACHINE_CELL_STORE) {
                    $orderGoods->sg_outlets = $g['code'];
                }

                if ($type == self::TYPE_FRESH_MACHINE ) {
                    $orderGoods->sg_outlets = $g['code'];
                }

                $orderGoods->order_id = $order_id;
                $orderGoods->supply_price = $g['supply_price'];
                $orderGoods->price = $g['price'];
                $orderGoods->total_price = $orderGoods->price * $orderGoods->num;
                $orderGoods->status = Order::STATUS_NEW;
                $orderGoods->line_id = isset($g['line_id']) ? $g['line_id'] : 0;
                $orderGoods->create_time = $order['create_time'];
                if (isset($g['t_weight']))
                    $orderGoods->weight = $g['t_weight'];
                if (isset($g['name']))
                    $orderGoods->name = $g['name'];
                 $orderGoods->save();
                $total_price += $orderGoods->total_price;
            }
            //判断运费
            if ($type == Order::TYPE_SUPERMARK) {
                if ($shippingType == Order::SHIPPING_TYPE_TAKE) {
                    $order->shipping_fee = 0;
                } elseif ($shippingType == Order::SHIPPING_TYPE_SEND) {
                    if ($total_price >= $store['delivery_mini_amount']) {
                        $order->shipping_fee = 0;
                    } else {
                        //判断是否达到起送金额
                        if ($total_price < $store['delivery_start_amount']) {
                            $rs['code'] = ErrorCode::ORDER_AMOUNT_LESS_THEN_DELIVERY_START_AMOUNT;
                            if ($trans == true)
                                $transaction->rollback();
                            return $rs;
                        }

                        $order->shipping_fee = $store['delivery_fee'];
                        $total_price += $order->shipping_fee;
                    }
                }
            }


            //判断每日消费限额
            $limit_config = Tool::getConfig('amountlimit');
            if ($trans && $limit_config['isEnable']) {
                $total_amount = Order::getMemberTodayAmount($memberId, $storeId, $type);
                $max_amount = $limit_config['memberTotalPayPreStoreLimit'];
                if (($total_amount + $total_price) > $max_amount && $max_amount > 0) {
                    $rs['code'] = ErrorCode::ORDER_OVER_MAX_AMOUNT_PREDAY_ERROR;
                    $transaction->rollback();
                    return $rs;
                }
            }


            //保存订单
            $orderGoods->create_time = time();
            $order->total_price = $total_price;
            $order->save();

            $father_order_id = $order->id;

            //生鲜机下单，需要查询货道所属商家					分别生成子订单
            if ($type == self::TYPE_FRESH_MACHINE && $fatherId == null) {

                $son_goods = Yii::app()->db->createCommand()
                        ->select('t.id,t.goods_id,t.machine_id,t.line_id,l.rent_partner_id,l.rent_member_id')
                        ->from(FreshMachineGoods::model()->tableName() . ' as t')
                        ->leftJoin(FreshMachineLine::model()->tableName() . ' as l', 't.line_id=l.id')
                        ->leftJoin(Goods::model()->tableName() . ' as g', 't.goods_id=g.id')
                        ->where('t.id IN (' . implode(',', $ids) . ') AND t.machine_id=' . $storeId . ' AND t.status= ' . FreshMachineGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' ')
                        ->queryAll();


                $son_partner_goods = array();
                foreach ($son_goods as $g) {
                    $temp_arr = array();
                    $temp_arr['id'] = $g['id'];
                    foreach ($goods as $gg) {
                        if ($gg['id'] == $g['id']) {
                            $temp_arr['num'] = $gg['num'];
                        }
                    }
                    $son_partner_goods[$g['rent_partner_id']][] = $temp_arr;
                }
//         		var_dump($father_order_id,$ids,$son_goods);exit();
                //查询是否多个商家  如果有多个商家则生成多个订单
                if (count($son_partner_goods) > 1) {
                    foreach ($son_partner_goods as $key => $val) {
                        $this->createOrder($type, $storeId, $memberId, $val, $addressId, $shippingType, $shipping_time, $machineTakeType, $order['remark'] . '|此订单为总订单[' . $order['code'] . ']的子订单', false, false, $father_order_id, $key);
                    }
                } elseif (count($son_partner_goods) == 1) {
                    //只有一个商家  即当前商家  更新订单的所属商家
                    $partner_keys = array_keys($son_partner_goods);
                    $update_rs = Yii::app()->db->createCommand()->update(Order::model()->tableName(), array('partner_id' => $partner_keys[0]), 'id=' . $order['id']);
                }
            }
          
            if ($stock === true) {
                //冻结库存

                $frozen_rs = ApiStock::stockFrozenList($store['id'], $projectId == API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID ? $frozen_lines : $frozen_ids, $frozen_nums, $projectId);
// 	        	var_dump($frozen_lines,$frozen_nums,$frozen_rs);exit();
                if (!isset($frozen_rs['result']) || $frozen_rs['result'] != true) {
                    $rs['code'] = ErrorCode::GOOD_STOCK_UPDATE_ERROR;
                    if ($trans == true)
                        $transaction->rollback();
                    return $rs;
                }
            }
        } catch (Exception $e) {
            if ($trans == true)
                $transaction->rollBack();
            throw new Exception($e->getMessage());
        }
//        var_dump($trans);die;
        if ($trans == true)
            $transaction->commit();
        $rs['success'] = true;
        $rs['data'] = $order->getAttributes();
        $rs['data']['store_name'] = $store['name'];
        return $rs;
    }

    static function getDetailByCode($code) {
        if (empty($code)) {
            return false;
        }

        $cri = new CDbCriteria();
        $cri->with = array('ordersGoods', 'store', 'machine', 'freshMachine');
        $cri->compare('t.code', $code);
//         $cri->compare('member_id',$member);

        return Order::model()->find($cri);
    }

    function getByCode($code) {
        if (empty($code)) {
            return false;
        }

        $cri = new CDbCriteria();
        $cri->with = array('ordersGoods');
        $cri->compare('t.code', $code);

        return Order::model()->find($cri);
    }

    /**
     * 售货机用
     * @param unknown $code
     * @param unknown $mid
     * @param unknown $type
     * @param unknow $orderId
     * @return boolean
     */
    function getByGoodsCode($code, $mid, $type = self::TYPE_MACHINE) {
        if (empty($code) || empty($mid)) {
            return false;
        }
        $time = time() - 72 * 3600;
        $cri = new CDbCriteria();
        $cri->select = 't.code,t.status,t.pay_status';
        $cri->with = array('ordersGoods');
        $cri->compare('goods_code', $code);
        $cri->compare('machine_id', $mid);
        $cri->compare('type', $type);
        $cri->addCondition(' t.create_time>' . $time);
        $cri->addCondition(' t.create_time<' . time());
        $cri->order = 't.create_time DESC';

        return Order::model()->find($cri);
    }

    /**
     * 订单支付成功
     *
     * $goods  goods的数组
     *
     */
    public function orderPaySuccess($code, $payPrice = null, $trans = true) {
        $order = Order::model()->getByCode($code);
        if (empty($order)) {
            return false;
        }

        // 执行事务
        if ($trans == true)
            $transaction = Yii::app()->db->beginTransaction();
        try {
            $partner_info = Partners::model()->findByPk($order['partner_id']);

            //排除生鲜机不登录购买
            $not_same_member = $partner_info['member_id'] != $order['member_id'] ? true : false;
            $member_info = Member::model()->findByPk($order['member_id']);
            $order->distribute_config = Order::updateOrderDistributionConfig($order, $member_info, false);

            //支付逻辑
            $order->status = Order::STATUS_PAY;
            $order->pay_status = Order::PAY_STATUS_YES;
            $order->pay_price = $payPrice ? $payPrice * 1 : $order->total_price;
            if (empty($order->pay_time)) {
                $order->pay_time = time();
            }
            $rs = $order->save();

            if ($order['type'] == Order::TYPE_SUPERMARK) {
                $store_info = Supermarkets::model()->findByPk($order['store_id']);
                //发送通知短信
                $apiMember = new ApiMember();
                if (isset($store_info['mobile']))
                    $apiMember->sendSms($store_info['mobile'], '您好，用户已支付SKU-微小企店铺[' . $store_info['name'] . ']的订单[' . $order['code'] . ']，请及时送货或备货，谢谢！');
            }


            //同步更新子订单
            //查询是否有子订单
            $son_order = Yii::app()->db->createCommand()
                    ->select('id,code')
                    ->from(Order::model()->tableName())
                    ->where('father_id=:father_id', array(':father_id' => $order['id']))
                    ->queryAll();

            if (!empty($son_order)) {
                foreach ($son_order as $o) {
                    $this->orderPaySuccess($o['code'], null, false);
                }
            }
        } catch (Exception $e) {
            if ($trans == true)
                $transaction->rollBack();
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            return false;
            //throw new Exception($e->getMessage());
        }

        if ($trans == true)
            $transaction->commit();
        return true;
    }

    /**
     * 订单确认发货
     *
     * $goods  goods的数组
     *
     */
    public function orderConfirm($code) {
        $order = Order::getByCode($code);

        if (empty($order)) {
            return false;
        }
        // 执行事务
        $transaction = Yii::app()->db->beginTransaction();
        try {
            //支付逻辑
            $order->status = Order::STATUS_SEND;
            $order->save();
        } catch (Exception $e) {
            $transaction->rollBack();
            $flag = false;
            throw new Exception($e->getMessage());
        }

        $transaction->commit();
        return $order->code;
    }

    /**
     * 订单完成
     *
     * $goods  goods的数组
     *
     */
    public static function orderSign($code, $need_flow = true, $remark = '', $trans = true) {

        $rs = array('success' => false, 'code' => ErrorCode::COMMOM_ERROR, 'error_msg' => '');

        $order = self::getDetailByCode($code);

        if (empty($order)) {
            $rs['code'] = ErrorCode::ORDER_UNEXCIT;
            return $rs;
        }

        if ($order->pay_status != self::PAY_STATUS_YES) {
            $rs['code'] = ErrorCode::ORDER_STATUS_FAIL;
            return $rs;
        }

        if ($order->status == self::STATUS_COMPLETE) {
            $rs['code'] = ErrorCode::ORDER_STATUS_FAIL;
            $rs['error_msg'] = '订单已签收，不能重复签收';
            return $rs;
        }

        // 执行事务
        if ($trans == true)
            $transaction = Yii::app()->db->beginTransaction();
        try {

            //同步更新子订单
            //查询是否有子订单
            $son_order = Yii::app()->db->createCommand()
                    ->select('id,code')
                    ->from(Order::model()->tableName())
                    ->where('father_id=:father_id', array(':father_id' => $order['id']))
                    ->queryAll();

            if (!empty($son_order)) {
                foreach ($son_order as $o) {
                    self::orderSign($o['code'], true, '父订单[' . $order['code'] . '] | ' . $remark, false);
                }
                //如果有子订单,主订单不记流水
                $need_flow = false;
            }


            if ($need_flow == true) {
                //记录流水
                $apiOrder = new ApiOrder();

                $flow_rs = $apiOrder->orderSign($code, $order);

                if (!isset($flow_rs['success']) || $flow_rs['success'] != true) {
                    $rs['code'] = ErrorCode::ORDER_SIGN_FAIL;
                    $rs['error_msg'] = $flow_rs['msg'];
                    if ($trans == true)
                        $transaction->rollBack();
                    return $rs;
                }
            }

            $update_rs = Yii::app()->db->createCommand()->update(self::model()->tableName(), array('seller_remark' => !empty($remark) ? $order['seller_remark'] . ' [订单签收]' . $remark : $order['seller_remark'], 'status' => self::STATUS_COMPLETE, 'sign_time' => time()), 'id=' . $order['id']);

            if ($trans == true)
                $transaction->commit();
        } catch (Exception $e) {
            if ($trans == true)
                $transaction->rollBack();
            $flag = false;
            throw new Exception($e->getMessage());
        }

// 		if ($trans==true) $transaction->commit();

        $rs['success'] = true;
        $rs['code'] = 1;
        return $rs;
    }

    /**
     * 订单完成  取消
     *
     * $goods  goods的数组
     *
     */
    public static function orderCancel($code, $need_flow = true, $remark = '', $trans = true, $machine_cancle = true, $stock = true) {

        $rs = array('success' => false, 'code' => ErrorCode::COMMOM_ERROR);

        // 执行事务
        if ($trans == true)
            $transaction = Yii::app()->db->beginTransaction();
        try {
            $order = self::model()->getDetailByCode($code);

            if (empty($order)) {
                $rs['code'] = ErrorCode::ORDER_UNEXCIT;
                if ($trans == true)
                    $transaction->rollback();
                return $rs;
            }

            $type_name = self::type($order['type']);

            if ($order->status == Order::STATUS_COMPLETE || $order->status == Order::STATUS_SEND || $order->status == Order::STATUS_CANCEL) {
                $rs['code'] = ErrorCode::ORDER_STATUS_FAIL;
                if ($trans == true)
                    $transaction->rollback();
                return $rs;
            }

            //已支付订单才要流水  退钱
            if ($need_flow == true && ($order->pay_status == self::PAY_STATUS_YES || $order->status == self::STATUS_PAY)) {
                //记录流水
                $apiOrder = new ApiOrder();

                $flow_rs = $apiOrder->orderCancel($code);

                if (!$flow_rs) {
                    $rs['code'] = ErrorCode::ORDER_CANCEL_FAIL;
                    if ($trans == true)
                        $transaction->rollback();
                    return $rs;
                }

                //发送短信
                $m = new ApiMember();
                $member_info = Member::model()->findByPk($order['member_id']);
                if (!empty($member_info)) {

                    $msg = '取消微小企' . $type_name . '订单[' . $order['code'] . '] 成功，订单货款[' . ((string) $order['total_price']) . '元]已退还到您的账户';
                    $m->sendSms($member_info['mobile'], $msg, ApiMember::SMS_TYPE_ONLINE_ORDER, 0, ApiMember::SKU_SEND_SMS, array($type_name, $order['code'], ((string) $order['total_price'])), ApiMember::CANCLE_ORDER_SUCCESS);
                }


                if ($order['type'] == Order::TYPE_SUPERMARK) {
                    $store_info = Supermarkets::model()->findByPk($order['store_id']);
                    //发送通知短信
                    $apiMember = new ApiMember();
                    if (isset($store_info['mobile']))
                        $apiMember->sendSms($store_info['mobile'], '您好，用户已取消SKU-微小企店铺[' . $store_info['name'] . ']的订单[' . $order['code'] . ']，请知悉，谢谢！');
                }

                //标记退款成功
                $order->refund_status = Order::REFUND_STATUS_SUCCESS;
            }

            //归还库存
            if ($stock == true) {
                $ids = array();
                $nums = array();
                $gids = array();
                $project_id = '';
                $outlets = 0;
                if ($order->type == Order::TYPE_SUPERMARK) {
                    $project_id = API_PARTNER_SUPER_MODULES_PROJECT_ID;
                    $outlets = $order->store_id;
                } elseif ($order->type == Order::TYPE_MACHINE) {
                    $project_id = API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID;
                    $outlets = $order->machine_id;
                } elseif ($order->type == Order::TYPE_FRESH_MACHINE) {
                    $project_id = API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID;
                    $outlets = $order->machine_id;
                } elseif ($order->type == Order::TYPE_MACHINE_CELL_STORE) {
                    $project_id = API_MACHINE_CELL_STORE_PROJECT_ID;
                    $outlets = $order->machine_id;
                }
                foreach ($order->ordersGoods as $g) {
                    $ids[] = $g->gid;
                    $gids[] = $g->sgid;
                    $nums[$g->sgid] = $g->num;
                }

                if ($order->type == Order::TYPE_FRESH_MACHINE && !empty($gids)) {

                    $lines = Yii::app()->db->createCommand()
                            ->select('t.line_id,t.id,t.goods_id')
                            ->from(FreshMachineGoods::model()->tableName() . ' as t')
                            ->where('id IN (' . implode(',', $gids) . ')')
                            ->queryAll();

                    $line_ids = array();
                    foreach ($lines as $l) {
                        $line_ids[$l['id']] = $l['line_id'];
                    }

                    ksort($nums);
                    ksort($line_ids);
                    $line_ids = array_values($line_ids);
                } else {
                    $line_ids = array();
                }

                $nums = array_values($nums);
                if ($order->status == Order::STATUS_NEW) {
                    //如果是新订单则归还冻结库存
                    $stock_rs = ApiStock::stockFrozenRestoreList($outlets, $project_id == API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID ? $line_ids : $ids, $nums, $project_id);
                } elseif ($order->type == Order::TYPE_FRESH_MACHINE && $order->machine_take_type == self::MACHINE_TAKE_TYPE_WITH_CODE && $order->status != Order::STATUS_NEW) {
                    //远程购买已支付未取货返回冻结库存
                    $stock_rs = ApiStock::stockFrozenRestoreList($outlets, $project_id == API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID ? $line_ids : $ids, $nums, $project_id);
                } else {
                    //已支付订单需要归还正式库存
                    $stock_rs = ApiStock::stockChangeList($outlets, $project_id == API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID ? $line_ids : $ids, $nums, $project_id);
                }
            }

            //售货机订单推送通知售货机退货
            if (($order['type'] == Order::TYPE_MACHINE || $order['type'] == Order::TYPE_MACHINE_CELL_STORE || $order['type'] == Order::TYPE_FRESH_MACHINE) && $order['pay_status'] == Order::PAY_STATUS_YES && $order['status'] == Order::STATUS_PAY && $order['machine_take_type'] == Order::MACHINE_TAKE_TYPE_WITH_CODE && $machine_cancle == true) {
                $order_info = array();
                $order_info['orderID'] = $order->code;
                $order_info['time'] = time() * 1000;
                $store = $order['type'] == Order::TYPE_MACHINE ? VendingMachine::model()->findByPk($order['machine_id']) : FreshMachine::model()->findByPk($order['machine_id']);
                $push_rs = JPushTool::vendingMachinePush($store->device_id, 'pushOrderCancel', $order_info);
            }

            //订单逻辑
            $order->status = self::STATUS_CANCEL;
            $order->cancel_time = time();
            if (!empty($remark))
                $order->seller_remark .= ' [订单取消]' . $remark;
            $order->save();


            //同步更新子订单
            //查询是否有子订单
            $son_order = Yii::app()->db->createCommand()
                    ->select('id,code')
                    ->from(Order::model()->tableName())
                    ->where('father_id=:father_id', array(':father_id' => $order['id']))
                    ->queryAll();

            if (!empty($son_order)) {
                foreach ($son_order as $o) {
                    self::orderCancel($o['code'], false, $remark, false, false, false);
                }
            }


            if ($trans == true)
                $transaction->commit();
        } catch (Exception $e) {
            if ($trans == true)
                $transaction->rollBack();
            $flag = false;
            throw new Exception($e->getMessage());
        }

        $rs['success'] = true;
        $rs['code'] = 1;
        return $rs;
    }

    /**
     * 获取会员当日消费
     * @param number $sid 门店id
     */
    public static function getMemberTodayAmount($menber_id, $sid = 0, $type = null) {
        $menber_id = $menber_id * 1;
        $today_time = strtotime(date('Y-m-d'));
        $where = 'father_id=0 AND member_id=:member_id  AND pay_status=:pay_status AND status!=:status1 AND status!=:stuats2 AND create_time>=:today_time';
        $where_pramas = array(':member_id' => $menber_id, ':pay_status' => Order::PAY_STATUS_YES, ':status1' => Order::STATUS_NEW, ':stuats2' => Order::STATUS_CANCEL, ':today_time' => $today_time);
        if (!empty($sid) && !empty($type)) {
            $sid = $sid * 1;
            $type = $type * 1;
            if ($type == Order::TYPE_SUPERMARK) {
                $where .= ' AND store_id=:store_id ';
            } else if ($type == Order::TYPE_MACHINE || $type == Order::TYPE_FRESH_MACHINE || $type == Order::TYPE_MACHINE_CELL_STORE || $type == Order::TYPE_FRESH_MACHINE_SMALL) {
                $where .= ' AND machine_id=:store_id ';
            }
            $where_pramas[':store_id'] = $sid;


            $where .= ' AND type=:type ';
            $where_pramas[':type'] = $type;
        }

        $data = Yii::app()->db->createCommand()
                ->select('SUM(total_price) as total_price')
                ->from(Order::model()->tableName())
                ->where($where, $where_pramas)
                ->queryRow();

        return $data['total_price'] * 1;
    }

    /**
     * 获取会员当日消费
     * @param number $sid 门店id
     */
    public static function getMemberTodayPointPayAmount($menber_id, $sid = 0, $type = null) {
        $menber_id = $menber_id * 1;
        $today_time = strtotime(date('Y-m-d'));
        $where = 'father_id=0 AND pay_type=:pay_type AND member_id=:member_id  AND pay_status=:pay_status AND status!=:status1 AND status!=:stuats2 AND create_time>=:today_time';
        $where_pramas = array(':pay_type' => self::PAY_TYPE_POINT, ':member_id' => $menber_id, ':pay_status' => self::PAY_STATUS_YES, ':status1' => self::STATUS_NEW, ':stuats2' => self::STATUS_CANCEL, ':today_time' => $today_time);
        if (!empty($sid) && !empty($type)) {
            $sid = $sid * 1;
            $type = $type * 1;
            if ($type == Order::TYPE_SUPERMARK) {
                $where .= ' AND store_id=:store_id ';
            } else if ($type == Order::TYPE_MACHINE || $type == Order::TYPE_FRESH_MACHINE || $type == Order::TYPE_MACHINE_CELL_STORE || $type == Order::TYPE_FRESH_MACHINE_SMALL) {
                $where .= ' AND machine_id=:store_id ';
            }
            $where_pramas[':store_id'] = $sid;


            $where .= ' AND type=:type ';
            $where_pramas[':type'] = $type;
        }

        $data = Yii::app()->db->createCommand()
                ->select('SUM(total_price) as total_price')
                ->from(Order::model()->tableName())
                ->where($where, $where_pramas)
                ->queryRow();

        return $data['total_price'] * 1;
    }

    /**
     * 订单取消  取消一部分商品
     *
     * 逻辑为新建一张订单  执行取消逻辑，再更新现有订单。
     *
     * $goods  goods的数组
     *
     */
    public static function orderCancelPart($code, $success_goods, $remark = '', $trans = true) {

        $rs = array('success' => false, 'code' => ErrorCode::COMMOM_ERROR);

        // 执行事务
        if ($trans == true)
            $transaction = Yii::app()->db->beginTransaction();
        try {
            $order = self::model()->getDetailByCode($code);

            if (empty($order)) {
                $rs['code'] = ErrorCode::ORDER_UNEXCIT;
                if ($trans == true)
                    $transaction->rollback();
                return $rs;
            }

            $pay_price = $order->pay_price;
            $member_id = $order->member_id;
            //$Member = new ApiMember();
            //$GaiInfo = $Member->getInfo($member_id);
            $member_info = Member::model()->findByPk($order['member_id']);
            $mobile = $member_info['mobile'];

            //取出正确的成功商品、失败商品
            $fail_goods_arr = array();
            $success_goods_arr = array();
            $fail_total_price = 0;
            foreach ($order->ordersGoods as $g) {
                $temp_fail_goods = array('id' => $g['sgid'], 'num' => $g['num'], 'price' => $g['price'], 'supply_price' => $g['supply_price']);
                $temp_success_goods = $g;
                foreach ($success_goods as $sg) {
                    if ($g['sgid'] == $sg['id']) {
                        if ($g['num'] == $sg['num']) {
                            $temp_fail_goods = array();
                        } elseif ($g['num'] > $sg['num']) {
                            $temp_success_goods['num'] = $g['num'];
                            $temp_fail_goods['num'] = $g['num'] - $sg['num'];
                            $fail_total_price += $temp_fail_goods['num'] * $temp_fail_goods['price'];
                        }
                    }

                    if (!empty($temp_fail_goods) && $temp_fail_goods['num'] > 0) {
                        $fail_goods_arr[] = $temp_fail_goods;
                    }

                    if (!empty($temp_success_goods) && $temp_success_goods['num'] > 0) {
                        $success_goods_arr[] = $temp_success_goods;
                    }
                }
            }

            //重新下单，待取消订单
            $store_id = 0;
            if ($order['type'] == Order::TYPE_SUPERMARK) {
                $store_id = $order['store_id'];
            } elseif ($order['type'] == Order::TYPE_MACHINE) {
                $store_id = $order['machine_id'];
            }

            $create_rs = $order->createOrder($order['type'], $store_id, $order['member_id'], $fail_goods_arr, null, Order::SHIPPING_TYPE_TAKE, null, null, '商品不足，退款订单', false);

            if ($create_rs['success'] != true) {
                $rs['code'] = ErrorCode::ORDER_CREATE_ERROR;
                if ($trans == true)
                    $transaction->rollback();
                return $rs;
            }


            //取消退款订单		先更新价格 再取消
            $fail_order = $create_rs['data'];
            foreach ($fail_goods_arr as $g) {
                $g_total_price = $g['num'] * $g['price'];
                @Yii::app()->db->createCommand('UPDATE ' . OrdersGoods::model()->tableName() . ' SET total_price="' . $g_total_price . '"  ,status= ' . Order::PAY_STATUS_YES . ' , price= ' . $g['price'] . ' , supply_price= ' . $g['supply_price'] . ' WHERE order_id="' . $fail_order['id'] . '" AND sgid= ' . $g['id'])->execute();
            }
            $update_rs = Yii::app()->db->createCommand('UPDATE ' . Order::model()->tableName() . ' SET total_price="' . $fail_total_price . '",status= ' . Order::STATUS_PAY . ' , pay_status= ' . Order::PAY_STATUS_YES . ' WHERE code="' . $fail_order['code'] . '" ')->execute();


            $cancel_rs = self::orderCancel($fail_order['code'], true, '退款订单', false);
            if ($cancel_rs['success'] != true) {
                $rs['code'] = ErrorCode::ORDER_CANCEL_FAIL;
                if ($trans == true)
                    $transaction->rollback();
                return $rs;
            }

            //更新现有订单
            $total_price = 0;
            foreach ($success_goods_arr as $val) {
                //计算金额
                $total_price += $val['num'] * $val['price'];
                @$g_rs = Yii::app()->db->createCommand('UPDATE ' . OrdersGoods::model()->tableName() . ' SET num= ' . $val['num'] . ' , total_price= ' . $val['num'] * $val['price'] . ' WHERE id= ' . $val['id'])->execute();
            }

            $total_price += $order['shipping_fee'];
            $order_rs = Yii::app()->db->createCommand('UPDATE ' . Order::model()->tableName() . ' SET total_price= ' . $total_price . ' WHERE id= ' . $order['id'])->execute();
            if (!$order_rs) {
                $rs['code'] = ErrorCode::ORDER_UPDATE_ERROR;
                if ($trans == true)
                    $transaction->rollback();
                return $rs;
            }
        } catch (Exception $e) {
            if ($trans == true)
                $transaction->rollBack();
            $flag = false;
            throw new Exception($e->getMessage());
        }
        if ($trans == true)
            $transaction->commit();
        $rs['success'] = true;
        $rs['code'] = 1;
        return $rs;
    }

    /**
     * @param $order 订单号或者订单数组
     * @param $member
     * @param bool $notSameMember
     * @return int
     */
    public static function updateOrderDistributionConfig($order, $member, $notSameMember = true) {
        $memberId = $member['id'];
        $skuNumber = $member['sku_number'];
        $gaiNumber = $member['gai_number'];
        //生成分配数据
        if (!is_array($order) && !is_object($order)) {
            $order = Yii::app()->db->createCommand()
                    ->from(Order::model()->tableName())
                    ->where('id=:id', array(':id' => $order))
                    ->queryRow();
        }
        //消费账户
        $accountBalance = AccountBalance::findRecord(array(
                    'account_id' => $memberId,
                    'type' => AccountBalance::TYPE_CONSUME,
                    'sku_number' => $skuNumber), false, $notSameMember
        );
        //待分配账户
        $accountBalanceC = AccountBalance::findRecord(array(
                    'account_id' => $memberId,
                    'type' => AccountBalance::TYPE_GUADAN_DAIFENPEI_XIAOFEI,
                    'sku_number' => $skuNumber), false, $notSameMember
        );
        $accountBalanceB = AccountBalance::findRecord(array(
                    'account_id' => $memberId,
                    'type' => AccountBalance::TYPE_GUADAN_DAIFENPEI_SHANGJIA,
                    'sku_number' => $skuNumber), false, $notSameMember
        );
        $distribute_config = json_encode(Formula::_createDistributionConfig($order, $member, $accountBalance, $accountBalanceC, $accountBalanceB));
        return $distribute_config;
    }

    public function searchComplete() {
        $this->day = array(
            date('Y-', time()) . '01',
            date('Y-', time()) . '02',
            date('Y-', time()) . '03',
            date('Y-', time()) . '04',
            date('Y-', time()) . '05',
            date('Y-', time()) . '06',
            date('Y-', time()) . '07',
            date('Y-', time()) . '08',
            date('Y-', time()) . '09',
            date('Y-', time()) . '10',
            date('Y-', time()) . '11',
            date('Y-', time()) . '12'
        );
        $datas = array();

        foreach ($this->day as $k => $v) {
            $sql = "select * from {{orders}} where  status =6 and type=3 and FROM_UNIXTIME( create_time, '%Y-%m' ) = '$v'";
            $datas[$k] = self::model()->findAllBySql($sql);
            $datas[$k]['day'] = $v;
        }
//             $datas = self::model()->findAll('status=:s and type=:t',array(':s'=>6,':t'=>3));
        return $datas;
    }

    /**
     * @param array $where 条件语句
     * @param string $fields 选择字段
     * @return mixed
     */
    public static function getOrderInfo($where, $fields = "*") {
        $whereTmp = $whereVal = array();
        $whereStr = '';
        foreach ($where as $key => $value) {
            $whereTmp[] = $key . '=:' . $key;
            $whereVal[':' . $key] = $value;
        }
        if (count($whereTmp) > 1) {
            $whereStr = implode(' and ', $whereTmp);
        } else {
            $whereStr = $whereTmp[0];
        }
        $result = Yii::app()->db->createCommand()->select($fields)->from("{{orders}}")->where($whereStr, $whereVal)->queryRow();
        return $result;
    }

    /*
     * 查询门店信息
     * $id 门店id
     * $select 查询字段
     */

    public static function findSuper($id, $select = array()) {
        //  $select  = empty($select)?'*':$select;
        $data = FALSE;
        if ($select) {
            $data = Supermarkets::model()->find(array('select' => $select, 'condition' => 'id=' . $id));
        } else {
            $data = Supermarkets::model()->findByPk($id);
        }
        return $data;
    }

    /*
     * 获取待抢单的订单列表
     * @param int   memberId 配送员的id
     * @param int   $lastId  当前页面的id
     * @param int pageSize 当前页面显示的数量
     */

    public function getWaitingOrderLists($memberId, $lastId, $pageSize) {
        $return = array();
        try {

            //查询当前配送员驻点
            $dcInfo = Distribution::model()->getDcInfo($memberId);
            if (empty($dcInfo)) {
                throw new Exception('查不到此配送员驻店的信息');
            }

            $where = '';
            //检查是否驻店
            if (!empty($dcInfo['bind_store'])) {
                $bind_store_id = intval($dcInfo['bind_store']);
                //获取驻点的商店的经纬度
                $supermarkets = Supermarkets::model()->findByPk($bind_store_id);
                if (empty($supermarkets))
                    throw new Exception('查询不到驻店的信息!');

                $blat = $supermarkets->lat; // 纬度
                $blng = $supermarkets->lng; //经度

                $where = 'AND t.store_id = ' . intval($dcInfo['bind_store']);
            }else {
                //检测是否驻点
                if (!empty($dcInfo['range_status'])) {

                    $select_store_id = intval($dcInfo['select_store_id']);
                    $tmp = json_decode($dcInfo['bind_store_id'], true);

                    foreach ($tmp as $key => $val) {

                        if ($val['id'] == $select_store_id) {

                            $slat = $val['position']['lat'];
                            $slng = $val['position']['lng'];
                            break;
                        }
                    }
                } else {
                    $slat = $dcInfo['lat'];
                    $slng = $dcInfo['lng'];
                }

                if (empty($slat) || empty($slng)) {
                    throw new Exception('获取不到当前驻点定位的信息!');
                }
            }

            /* $lats = isset($blat) ? $blat : $slat;
              $lngs = isset($blng) ? $blng : $slng; */
            // echo 'lat:'.$slat.' lng:'.$slng.'<br/>';die;

            $condition = '';
            if (!empty($lastId) && $lastId != -1) {
                $condition = 'AND t.id < ' . intval($lastId);
            }
            //查询所有符合条件的新订单
            $sql = 'SELECT t.id,t.`code`,t.goods_code,t.partner_id,t.member_id,t.mobile,t.total_price,t.pay_price,t.pay_status,t.pay_time,t.pay_type,t.create_time,t.store_id,m.real_name,m.username,s.`name`,s.lat as s_lat,s.lng as s_lng,s.delivery_time,s.street as pickup_goods_address,a.street as send_goods_address FROM {{orders}} as t LEFT JOIN {{supermarkets}} as s ON t.store_id = s.id LEFT JOIN {{member}} as m ON t.member_id = m.id LEFT JOIN {{address}} AS a ON t.address_id = a.id WHERE 1 = 1 ' . $condition . ' AND t.`status` = 0 AND t.is_auto_cancel = 0 AND t.store_id > 0 ' . $where . ' ORDER BY t.id DESC LIMIT ' . intval($pageSize);

            $orderList = Yii::app()->db->createCommand($sql)->queryAll();

            $data = array();
            if (!empty($orderList)) {
                //2km之内的所有订单
                foreach ($orderList as $key => $value) {

                    //配送员到取货距离
                    if (!empty($dcInfo['bind_store'])) {//驻店处理
                        $distance = 0;
                        //配送员到驻店取货距离
                        $dcToGoods = Distribution::model()->getDistance($dcInfo['lat'], $dcInfo['lng'], $blat, $blng);
                    } else {
                        $distance = Distribution::model()->getDistance($slat, $slng, $value['s_lat'], $value['s_lng']);
                        $dcToGoods = Distribution::model()->getDistance($dcInfo['lat'], $dcInfo['lng'], $value['s_lat'], $value['s_lng']);
                    }
                    if (ceil($distance / 1000) <= 2) {
                        //$value['pickup'] =
                        //todo 配送员当前位置到取货位置 和配送目的地距离
                        //当前配送员到送货距离
                        //$dcToTarget = Distribution::model()->getDistance($dcInfo['lat'], $dcInfo['lng'], $value['lat'], $value['lng']);
                        $value['dcToGoods'] = ceil($dcToGoods / 1000);
                        $data[$key] = $value;
                        continue;
                    }
                }
            } else {
                throw new Exception('还没有新订单!');
            }
            $return = $data;
        } catch (Exception $e) {
            $return['result'] = false;
            $return['msg'] = $e->getMessage();
        }

        return $return;
    }

    /**
     * 生成提货码
     */
    public static function getGoodsCode() {
        self::_createCode();
    }

    /**
     * 待取货功能接口 或者待送货订单接口(工作汇报)
     * @param int memberId 配送员的id
     * @param int type 2:待送达 3:待取货
     * @param bool is_report 是否是工作汇报
     * @param int   $lastId  当前页面的id
     * @param int pageSize 当前页面显示的数量
     */
    public function getDealWithGoodsList($member_id, $type, $pagesize = 0, $lastId = 0, $is_report = false) {
        try {

            if (empty($member_id) || empty($type)) {
                throw new Exception('model缺少参数!');
            }

            $sql = 'SELECT o.id AS order_id,o.`code`,o.goods_code,o.partner_id,o.member_id,o.mobile AS client_mobile,o.total_price,o.pay_price,o.pay_status,o.pay_time,o.pay_type,o.create_time,o.store_id,o.address_id,o. STATUS AS order_status,t.id AS d_id,t. STATUS AS d_status,s.mobile AS store_mobile,s.is_fixed,s.lat AS store_lat,s.lng AS store_lng,(CASE WHEN s.is_distribution > 0 THEN (CASE WHEN s.is_fixed > 0 THEN s.delivery_man_amount ELSE (s.delivery_fee * s.delivery_poin) END) ELSE NULL END) as funds,s.street AS pickup_goods_address,r.street AS send_goods_address FROM {{distribution_order}} AS t INNER JOIN {{orders}} AS o ON t.order_id = o.id LEFT JOIN {{address}} AS r ON r.id = o.address_id LEFT JOIN {{supermarkets}} AS s ON s.id = o.store_id WHERE t.order_code = o.`code` AND t.`status` = ' . intval($type) . ' AND o.is_auto_cancel = 0 AND t.distribution_id = (SELECT tmp.id FROM {{distribution}} as tmp WHERE tmp.member_id = ' . intval($member_id) . ')';

            if ($is_report == true) {//工作汇报接口使用
                if (empty($pagesize) || empty($lastId)) {
                    //今天完成的订单
                    $sql .= ' AND date_format(from_UNIXTIME(t.create_time),"%Y-%m-%d") = date_format(NOW(),"%Y-%m-%d")  ORDER BY o.id DESC';
                } else {
                    $condition = '';
                    if (!empty($lastId) && $lastId != -1) {
                        $condition = 'AND o.id < ' . intval($lastId);
                    }
                    //今天完成 或者是取消的订单
                    $sql .= ' AND date_format(from_UNIXTIME(t.create_time),"%Y-%m-%d") = date_format(NOW(),"%Y-%m-%d")  ' . $condition . ' ORDER BY o.id DESC LIMIT ' . intval($pagesize);
                }
                //$sql .= ' AND date_format(from_UNIXTIME(t.create_time),"%Y-%m-%d") = date_format(NOW(),"%Y-%m-%d") AND o.id < ' .intval($lastId). ' ORDER BY o.id DESC LIMIT '.intval($pagesize);
            }

            $list = Yii::app()->db->createCommand($sql)->queryAll();
            if (empty($list)) {
                throw new Exception('你还没有此类订单!');
            }

            $return = array_values($list);
        } catch (Exception $e) {
            $return['result'] = false;
            $return['msg'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * 收货码接口
     * @param int orderCode 订单编号
     * @params goods_code string 取货码
     * @param bool is_user 是否是用户收货
     * @author yuanmei.chen
     */
    public function scanTakeGoods($orderCode, $goods_code, $is_user = false, $trans = true) {
        try {

            $order_info = Order::model()->find('code=:code', array(':code' => $orderCode));
            if ($trans) {
                $transaction = Yii::app()->db->beginTransaction();
            }

            if (empty($order_info))
                throw new Exception('找不到此订单的信息!');

            $order_id = $order_info->id;
            $sql = 'SELECT * FROM {{orders}} WHERE id = :id AND code = ' . $orderCode . ' FOR UPDATE';
            $ret = self::model()->findAllBySql($sql, array(':id' => intval($order_id)));

            //配送员取货 必须商户要先接单才可以取货
            if ($is_user == false && (empty($ret) || $ret[0]['status'] != self::STATUS_AUTO_ORDER_TAKENRS)) {
                throw new Exception('此订单信息错误!');
            }

            if ($order_info['goods_code'] != $goods_code) {
                throw new Exception('验证码错误，请重新输入或与掌柜确认!');
            }

            if ($ret[0]['status'] == self::STATUS_FROZEN) {
                throw new Exception('订单已冻结!');
            }

            if ($ret[0]['status'] == self::STATUS_COMPLETE) {
                throw new Exception('订单已签收，不能重复签收!');
            }

            if ($is_user == true && (empty($ret) || $ret[0]['pay_status'] != self::PAY_STATUS_YES)) {
                throw new Exception('此订单信息错误!');
            }
            //修改订单状态
            $status = $is_user == true ? self::STATUS_COMPLETE : self::STATUS_SENDING;
            $dc_status = $is_user == true ? DistributionOrder::STATUS_OK : DistributionOrder::STATUS_WAITING_SEND;
            $order = Order::model()->updateByPk(intval($order_id), array('status' => $status));

            //修改配送员订单信息
            $distributionOrder = DistributionOrder::model()->updateAll(array('order_id' => intval($order_id), 'order_code' => $orderCode), 'status=:status', array(':status' => $dc_status));

            if ($order < 0 || $distributionOrder < 0) {
                throw new Exception('更新订单信息失败!');
            }

            $return['result'] = true;
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $return['result'] = false;
            $return['msg'] = $e->getMessage();
        }

        return $return;
    }

    /**
     * 首次消费  返还消费10%金额
     * @param int code 订单编号
     * @param int memberId 盖网id
     * @author yuanmei.chen
     */
    public function giveBackAmountFirstConsume($code, $memberId, $type = null) {

        try {
            if (empty($code) && empty($memberId)) {
                throw new Exception('返利金额缺少参数!');
            }
            //查询当前订单 支付金额
            if ($type == 'guadan') {
                $orderInfo = GuadanJifenOrder::model()->find('code=:code AND member_id = :member_id AND type =:type', array(':code' => $code, ':member_id' => $memberId, 'type' => GuadanJifenOrder::TYPE_PARTNER))->attributes;
                if (count($orderInfo) < 0) {
                    throw new Exception('找不到此订单信息!');
                }
            } else {
                $orderInfo = self::model()->find('code=:code AND member_id = :member_id', array(':code' => $code, ':member_id' => $memberId))->attributes;
                if (count($orderInfo) < 0) {
                    throw new Exception('找不到此订单信息!');
                }
            }

            //获取sku账户信息
            $memberInfo = Member::model()->find('id=:id', array(':id' => intval($memberId)))->attributes;
            if (count($memberInfo) < 0) {
                throw new Exception('找不到下单用户信息!');
            }

            //查询之前的消费记录
            if ($type == 'guadan') {
                $sql_guadan = 'SELECT id FROM {{guadan_jifen_order}} WHERE member_id = ' . intval($memberId) . ' AND type=' . GuadanJifenOrder::TYPE_PARTNER . ' AND code != ' . $code . ' AND status = ' . GuadanJifenOrder::STATUS_PAY . '  LIMIT 1';
                $ConsumeHistory_guadan = Yii::app()->db->createCommand($sql_guadan)->queryAll();

                $sql_order = 'SELECT id FROM {{orders}} WHERE member_id = ' . intval($memberId) . ' AND status = ' . self::STATUS_COMPLETE . '  LIMIT 1';
                $ConsumeHistory_order = Yii::app()->db->createCommand($sql_order)->queryAll();
            } else {
                $sql_order = 'SELECT id FROM {{orders}} WHERE member_id = ' . intval($memberId) . ' AND code != ' . $code . ' AND status = ' . self::STATUS_COMPLETE . '  LIMIT 1';
                $ConsumeHistory_order = Yii::app()->db->createCommand($sql_order)->queryAll();

                $sql_guadan = 'SELECT id FROM {{guadan_jifen_order}} WHERE member_id = ' . intval($memberId) . ' AND type=' . GuadanJifenOrder::TYPE_PARTNER . ' AND status = ' . GuadanJifenOrder::STATUS_PAY . '  LIMIT 1';
                $ConsumeHistory_guadan = Yii::app()->db->createCommand($sql_guadan)->queryAll();
            }
            if (empty($ConsumeHistory_order) && empty($ConsumeHistory_guadan)) { //返还金额的10%积分
                //组装数据
                if ($type == 'guadan') {

                    $apiLogData['order_id'] = $orderInfo['id'];
                    $apiLogData['order_code'] = $orderInfo['code'];
                    $apiLogData['operate_type'] = AccountFlow::OPERATE_TYPE_SKU_FIRST_CONSUMPTION;
                    $apiLogData['transaction_type'] = AccountFlow::TRANSACTION_TYPE_CONSUME;
                    $apiLogData['remark'] = '首次支付,返利金额';
                    $apiLogData['money'] = $orderInfo['total_price'] * 0.1;
                    $apiLogData['account_id'] = $orderInfo['member_id'];
                    $apiLogData['sku_number'] = $memberInfo['sku_number'];
                    $apiLogData['gai_number'] = $memberInfo['gai_number'];
                    $apiLogData['data'] = json_encode($orderInfo);
                    $apiLogData['create_time'] = time();
                } else {
                    $apiLogData['order_id'] = $orderInfo['id'];
                    $apiLogData['order_code'] = $orderInfo['code'];
                    $apiLogData['operate_type'] = AccountFlow::OPERATE_TYPE_SKU_FIRST_CONSUMPTION;
                    $apiLogData['transaction_type'] = AccountFlow::TRANSACTION_TYPE_CONSUME;
                    $apiLogData['remark'] = '首次支付,返利金额';
                    $apiLogData['money'] = $orderInfo['total_price'] * 0.1;
                    $apiLogData['account_id'] = $orderInfo['member_id'];
                    $apiLogData['sku_number'] = $memberInfo['sku_number'];
                    $apiLogData['gai_number'] = $orderInfo['gai_number'];
                    $apiLogData['data'] = json_encode($orderInfo);
                    $apiLogData['create_time'] = time();
                }
                //处理金额
                if (AccountBalance::changeBalance($apiLogData)) {
                    return;
                }
            } else {
                return;
            }
        } catch (Exception $e) {
            $return['msg'] = $e->getMessage();
            return $return['msg'];
        }
    }

}
