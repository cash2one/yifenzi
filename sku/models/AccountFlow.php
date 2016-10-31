<?php

/**
 * 流水表模型类
 * @author wanyun.liu <wanyun_liu@163.com>
 *
 * @property string $id
 * @property string $account_id
 * @property string $sku_number
 * @property string $date
 * @property string $create_time
 * @property integer $type
 * @property string $debit_amount
 * @property string $credit_amount
 * @property integer $operate_type
 * @property string $trade_spec
 * @property integer $trade_terminal_id
 * @property integer $trade_terminal_type
 * @property string $order_id
 * @property string $order_code
 * @property string $remark
 * @property string $province_id
 * @property string $city_id
 * @property string $district_id
 * @property integer $week
 * @property integer $week_day
 * @property integer $hour
 * @property integer $moved
 * @property string $node
 * @property integer $transaction_type
 * @property string $current_balance
 * @property integer $flag
 */
class AccountFlow extends CActiveRecord {

    private static $_baseTableName = '{{account_flow}}';

    const TYPE_MERCHANT = 1; // 商家
    const TYPE_AGENT = 2; // 代理
    const TYPE_CONSUME = 3; // 消费
    const TYPE_RETURN = 4; // 待返还
    const TYPE_FREEZE = 5; //  冻结
    const TYPE_COMMON = 6; // 公共
    const TYPE_CASH = 8; //普通会员提现账户
    const TYPE_TOTAL = 9; // 总账户
    // 新操作类型
    const OPERATE_TYPE_EBANK_RECHARGE = 13; // 13、网银充值

    //提现
    const OPERATE_TYPE_CASH_APPLY = 15; // 15、申请提现
    const OPERATE_TYPE_CASH_CANCEL = 16; // 16、撤消提现
    const OPERATE_TYPE_CASH_SUCCESS = 20; // 提现成功
    //SKU订单
    const OPERATE_TYPE_SKU_PAY = 35;			//SKU订单支付
    const OPERATE_TYPE_SKU_SIGN = 36;			//SKU订单签收
    const OPERATE_TYPE_SKU_CANCEL = 37;			//SKU订单取消
    //游戏金币兑换
    const OPERATE_TYPE_GAME_EXCHANGE = 38;			//游戏兑换
    const OPERATE_TYPE_SIGN_TIAOZHENG = 34;         //余额调整

    const OPERATE_TYPE_SKU_GUADAN_IMPORT = 50;		//导入挂单积分
    const OPERATE_TYPE_SKU_GUADAN_IMPORT_DEL = 51;	//删除挂单积分
    const OPERATE_TYPE_SKU_GUADAN_SALE = 52;		//出售挂单积分
    const OPERATE_TYPE_SKU_GUADAN_SALE_STOP = 53;		//出售挂单积分-终止

    const OPERATE_TYPE_SKU_GUADAN_PIFA = 54;			//sku挂单批发
    const OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN = 55;			//SK会员购买充值
    const OPERATE_TYPE_SKU_GUADAN__MEMBER_RETURN = 56;				//SK会员积分到账
    const OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_OFFICAL_JIFEN = 58;				//SK会员购买充值-官方
    const OPERATE_TYPE_SKU_INPUT_GOODS_JIFEN = 59;

    const OPERATE_TYPE_SKU_YFZ_PAY = 61;			//SKU中一份子订单积分支付
    const OPERATE_TYPE_SKU_YFZ_SIGN = 62;			//SKU一份子订单签收
    
    const OPERATE_TYPE_SKU_RRPS_DEPOSIT = 65 ;                      //SKU扣除押金
    const OPERATE_TYPE_SKU_FIRST_CONSUMPTION = 66 ;                 //SKU首次消费返还积分

    //业务节点
    /*     * 直充* */
    const BUSINESS_NODE_EBANK_HUANXUN = '1311'; //环讯支付
    const BUSINESS_NODE_EBANK_GUANGZHOUYINLIAN = '1312'; //广州银联
    const BUSINESS_NODE_EBANK_YI = '1313';  //翼支付
    const BUSINESS_NODE_EBANK_HI = '1314';  //汇卡支付
    const BUSINESS_NODE_EBANK_POS = '1315';  //POS机刷卡
    const BUSINESS_NODE_EBANK_UM = '1316';  //联动优势
    const BUSINESS_NODE_EBANK_TL = '1317';  //通联支付
    const BUSINESS_NODE_EBANK_GHT = '1318';  //高汇通支付
    const BUSINESS_NODE_EBANK_WEIXIN = '1319'; //微信支付
    const BUSINESS_NODE_EBANK_EBC = '1331';	   //EBC钱包支付
    //SKU订单支付
    const BUSINESS_NODE_SKU_PAY_PAY = '3501';  //SKU订单支付-消费支付
    const BUSINESS_NODE_SKU_PAY_FREEZE = '3511';  //SKU订单支付-消费冻结
    const TIAOZHENG_NODE_OUT = '3401'; //调整转出
    const TIAOZHENG_NODE_IN = '3411'; //调整转入
    //SKU订单签收
    const BUSINESS_NODE_SKU_SIGN_CONFIRM = '3601'; //SKU订单签收-确认消费
    const BUSINESS_NODE_SKU_SIGN_PAYMENT = '3611'; //SKU订单签收-支付货款
    const BUSINESS_NODE_SKU_SIGN_PROFIT = '3612';  // SKU订单签收-利润
    const BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_MEMBER = '3613';  // SKU订单签收-会员消费奖励
    const BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_OTHER = '3614';  // SKU订单签收-收益分配 -其它角色
    const BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_SELLER_REFERENCE_UNDISTRIBUTED = '3605';  // SKU订单签收-收益分配 -商家推荐人待分配
    const BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_SELLER_REFERENCE = '3615';  // SKU订单签收-收益分配 -商家推荐人
    const BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_MEMBER_REFERENCE_UNDISTRIBUTED = '3606';  // SKU订单签收-收益分配 -会员推荐人待分配
    const BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_MEMBER_REFERENCE = '3616';  // SKU订单签收-收益分配 -会员推荐人

    //SKU订单取消
    const BUSINESS_NODE_SKU_CANCEL_REFUND = '3701';   //SKU订单取消-收回退款
    const BUSINESS_NODE_SKU_CANCEL_RETURN = '3711';		  //SKU订单取消-退还订单金额
    const BUSINESS_NODE_SKU_CANCEL_PAY_CHARGE = '3702';		  //SKU订单取消-支付手续费
    const BUSINESS_NODE_SKU_CANCEL_GET_CHARGE = '3712';		  //SKU订单取消-收取手续费

    const BUSINESS_NODE_SKU_GUADAN_IMPORT_OUT = '5001';		//导入挂单积分 挂单池转出
    const BUSINESS_NODE_SKU_GUADAN_IMPORT_IN = '5011';		//导入挂单积分 挂单账户转入
    const BUSINESS_NODE_SKU_GUADAN_IMPORT_DEL_OUT = '5101';		//删除挂单积分 挂单池转出-退回
    const BUSINESS_NODE_SKU_GUADAN_IMPORT_DEL_IN = '5111';		//删除挂单积分 挂单账户转入-退回
    const BUSINESS_NODE_SKU_GUADAN_SALE_OUT = '5201';			//出售挂单积分 挂单账户转出
    const BUSINESS_NODE_SKU_GUADAN_SALE_IN = '5211';			//出售挂单积分 售卖挂单积分池转入
    const BUSINESS_NODE_SKU__GUADAN_SALE_STOP_OUT = '5301';		//出售挂单积分-终止 挂单账户转入
    const BUSINESS_NODE_SKU__GUADAN_SALE_STOP_IN = '5311';		//出售挂单积分-终止 售卖挂单积分池退回

	const BUSINESS_NODE_SKU_GUADAN_PIFA_PAY = '5401';		  						//SK商户购买挂单积分  消费支付
	const BUSINESS_NODE_SKU_GUADAN_PIFA_PROFIT = '5411';		  					//SK商户购买挂单积分  利润
	const BUSINESS_NODE_SKU_GUADAN_PIFA_GAI_AMOUNT_OUT = '5402';		  			//SK商户购买挂单积分  售卖挂单积分池转出
	const BUSINESS_NODE_SKU_GUADAN_PIFA_PARTNER_AMOUNT_IN = '5412';		  			//SK商户购买挂单积分  商家购买的挂单积分转入
	const BUSINESS_NODE_SKU_GUADAN_PIFA_COST = '5403';		  						//SK商户购买挂单积分  SK成本支出
	const BUSINESS_NODE_SKU_GUADAN_PIFA_PARTNER_TO_DISTRIBUTION = '5413';		  	//SK商户购买挂单积分  商家待分配



	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_PAY = '5501';		  						//SK会员购买充值  消费支付
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_PARTNER_PROFIT = '5511';		  					//SK会员购买充值  商家得到收益
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_PARTNER_AMOUNT_OUT = '5502';		  			//SK会员购买充值  商家购买的挂单积分转出
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_GIVE_COST = '5503';		  									//SK会员购买充值  SK成本支出  赠送金额
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_MEMBER_AMOUNT_IN = '5512';		  			//SK会员购买充值  消费者积分转入
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_MEMBER_TO_RETURN_IN= '5513';		  	//SK会员购买充值  消费者待返还积分转入
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_XIAOFEI_DAIFENPEI_COST = '5504';		  									//SK会员购买充值  SK成本支出
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_MEMBER_TO_DISTRIBUTION = '5514';		  	//SK会员购买充值  商家待分配
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_SHANGJIA_DAIFEIPEI_COST = '5505';		  						//SK商户购买挂单积分  商家SK成本支出
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_PIFA_PARTNER_TO_DISTRIBUTION = '5515';		  	//SK商户购买挂单积分  商家待分配

	//挂单积分到账
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_RETURN_OUT = '5601';			//SK会员积分到账 消费者待返还积分转出
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_RETURN_IN = '5611';			//SK会员积分到账 消费者积分转入

	//SK会员购买充值-官方
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_PAY = '5801';		  						//SK会员购买充值  消费支付
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_GAI_PROFIT = '5811';		  					//SK会员购买充值  盖网得到收益
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_GAI_AMOUNT_OUT = '5802';		  			//SK会员购买充值  挂单积分转出
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_MEMBER_AMOUNT_IN = '5812';		  			//SK会员购买充值  消费者积分转入
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_MEMBER_TO_RETURN_IN= '5813';		  	//SK会员购买充值  消费者待返还积分转入
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_COST = '5804';		  									//SK会员购买充值  SK成本支出
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_XIAOFEI_DAIFENPEI_POINT_COST = '5805';		  									//SK会员购买充值  SK成本支出  会员待分配
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_MEMBER_TO_DISTRIBUTION = '5814';		  	//SK会员购买充值  会员待分配
	const BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_SHANGJIA_DAIFENPEI_POINT_COST = '5815';		  	//SK会员购买充值  SK成本支出  商家待分配

    const BUSINESS_NODE_SKU_INPUT_GOODS_JINFEN_IN = '5901'  ;     //SK会员商品录入积分奖励
    const BUSINESS_NODE_SKU_INPUT_GOODS_TOTAL_JINFEN_IN = '5902'  ;     //SK会员商品录入积分奖励 SK成本支出

    const BUSINESS_NODE_SKU_YFZ_PAY_PAY = '6101';  //SKU一份子订单支付-消费支付
    const BUSINESS_NODE_SKUYFZ__PAY_FREEZE = '6111';  //SKU一份子订单支付-消费冻结
    const BUSINESS_NODE_SKU_YFZ_SIGN_CONFIRM = '6201'; //一份子订单签收-确认消费
    const BUSINESS_NODE_SKU_YFZ_SIGN_PAYMENT = '6212'; //一份子订单签收-支付货款
    
    const BUSINESS_NODE_SKU_RRPS_SIGN_CONFIRM = '6501' ; //SKU人人配送确认消费
    const BUSINESS_NODE_SKU_RRPS_DEPOSIT = '6511'; //SKU人人配送押金扣除
    
    const BUSINESS_NODE_SKU_COST_PAY      = '6601' ;     //SKU成本支出
    const BUSINESS_NODE_SKU_MEMBER_INCOME = '6611'; //SKU会员转入

    //游戏兑换
    const BUSINESS_NODE_GAME_EXCHANGE = '3801';		  //游戏兑换
    const BUSINESS_NODE_GAME_INCOME = '3811';		  //游戏收益
    /*  提现 */
    const BUSINESS_NODE_CASH_APPLY = '1501';  //申请提现
    const BUSINESS_NODE_CASH_CHECK = '1511';  //核对提现
    /* 提现打回* */
    const BUSINESS_NODE_CASH_BACK = '1601';   //打回提现申请
    const BUSINESS_NODE_CASH_CANCEL = '1611';  //取消提现
    /*  提现成功* */
    const BUSINESS_NODE_CASH_CONFIRM = '2001';  //确认提现

    //事务类型
    const TRANSACTION_TYPE_CONSUME = 1;  //消费
    const TRANSACTION_TYPE_DISTRIBUTION = 2; //分配
    const TRANSACTION_TYPE_REFUND = 3; //退款
    const TRANSACTION_TYPE_RETURN = 4; //退货
    const TRANSACTION_TYPE_ORDER_CANCEL = 5; //取消订单
    const TRANSACTION_TYPE_COMMENT = 6; //评论
    const TRANSACTION_TYPE_RIGHTS = 7; //维权
    const TRANSACTION_TYPE_ORDER_CONFIRM = 8; //订单确认
    const TRANSACTION_TYPE_RECHARGE = 9;  //充值
    const TRANSACTION_TYPE_CASH = 10; //提现
    const TRANSACTION_TYPE_CASH_CANCEL = 11; //取消提现
    const TRANSACTION_TYPE_CASH_HONGBAO_APPLY = 12; //红包申请
    const TRANSACTION_TYPE_ASSIGN = 13;  //调拨
    const TRANSACTION_TYPE_CASH_HONGBAO_RECHARGE = 14;//红包充值
    const TRANSACTION_TYPE_OTHER_REFUND = 15;//其它退款
    const TRANSACTION_TYPE_TRANSFER = 16;//旧余额转账
    const TRANSACTION_TYPE_TIAOZHENG = 17;//调整
    const TRANSACTION_TYPE_ENVELOPE = 18;  //盖讯通红包
    const TRANSACTION_TYPE_FREEZE = 19;  //冻结
    const TRANSACTION_TYPE_GUADAN = 20;//挂单

    /**
     *   `recharge_type` '充值类型',
     */
    const RECHARGE_TYPE_BANK = 1; //直充
    const RECHARGE_TYPE_CARD = 2; //卡充
    const FLAG_SPECIAL = 1; //特殊的流水，不显示

    //RabbitMQ插入流水用
    const FLOW_ENAME = 'save-month-flow-table-exchange-public';
    const FLOW_K_ROUTE = 'save-month-flow-table-route-public';
    const FLOW_Q_NAME = 'save-month-flow-table-queue-public';


    /**
     * @var string 搜索用
     */

    public $endTime;
    public $month;
    public $isExport; //是否导出excel
    /**
     * 当前月份表
     * @return string
     */
    public function tableName() {
        $month = Yii::app()->user->getState('accountFlowMonth');
        if (empty($month))
            return self::monthTable();
        $this->month = $month;
        return self::$_baseTableName . '_' . str_replace('-', '', $month);
    }

    /**
     * 搜索用月份列表
     * @return array
     */
    public static function getMonth() {
        $date = '2014-07-01';
        $unixTime = strtotime($date);
        list($startDate['y'], $startDate['m']) = explode("-", $date);
        list($endDate['y'], $endDate['m']) = explode("-", date('Y-m', time()));
        $num = abs($startDate['y'] - $endDate['y']) * 12 + $endDate['m'] - $startDate['m'];
        $month = array();
        for ($i = 0; $i <= $num; $i++) {
            $monthTime = date('Y-m', $unixTime + (32 * $i * 86400));
            $month[$monthTime] = $monthTime;
        }
        return $month;
    }

    public function rules() {
        return array(
        	array('create_time', 'length', 'max' => 20),
        	array('remark', 'length', 'max' => 255),
        	array('type,operate_type','numerical', 'integerOnly' => true),
            array('sku_number,create_time,remark, type, month, order_code, operate_type', 'safe', 'on' => 'search'),
        );
    }

    public function attributeLabels() {
        return array(
            'month' => '月份',
            'id' => '主键',
            'account_id' => '所属账号',
            'sku_number' => 'SKU号',
            'date' => '日期',
            'create_time' => '创建时间',
            'type' => '类型', //（1商家、2代理、3消费、4待返还、5冻结、6、盖网公共、9总账户）
            'debit_amount' => '借方发生额',
            'credit_amount' => '贷方发生额',
            'operate_type' => '交易类型', //（1、后续定）
            'trade_spec' => '地点',
            'trade_terminal_id' => '所属终端',
            'trade_terminal_type' => '所属终端类型',
            'order_id' => '订单ID',
            'order_code' => '订单编号',
            'remark' => '备注',
            'province_id' => '省份',
            'city_id' => '城市',
            'district_id' => '区/县',
            'week' => '第几周',
            'week_day' => '星期几',
            'hour' => '小时',
            'moved' => '是否搬送', //（0否、1是）
            'node' => '业务节点',
            'transaction_type' => '事务类型',
            'current_balance' => '当前余额',
            'flag' => '标识', //（0无、1特殊）1特殊是代扣的流水，不在前台显示给会员看
        );
    }

    public function search() {
        $criteria = new CDbCriteria;
        $criteria->compare('operate_type', $this->operate_type);
        $criteria->compare('remark', $this->remark, true);
        $searchDate = Tool::searchDateFormat($this->create_time, $this->endTime);
        $criteria->compare('create_time', ">=" . $searchDate['start']);
        $criteria->compare('create_time', "<" . $searchDate['end']);

        $fields = '`id`,`type`,`remark`, `create_time`, `debit_amount`, `credit_amount`, `by_sku_number`,`order_code`,`operate_type`';
        $sql = 'SELECT ' . $fields . ' FROM ' . self::monthTable() . ' WHERE account_id = :mid
            AND flag = 0 AND create_time>=:create_time AND (type IN (:t1) or (type = :t5 and node in( :node1 , :node2,:node3))) :condition
            UNION ALL SELECT ' . $fields . ' FROM ' . self::hashTable(Yii::app()->user->gw) . '
            WHERE account_id = :mid AND flag = 0 AND (type IN (:t1) or (type = :t5 and node in( :node1 , :node2,:node3))) :condition';
        $sqlCount = 'SELECT COUNT(*) FROM ( ' . $sql . ' ) AS a';
        $params = array(
                ':mid' => Yii::app()->user->id,
                ':create_time' => strtotime(date('Y-m-d')),
                ':t1' => self::TYPE_CONSUME,
                ':t5'=>self::TYPE_FREEZE,
        );
        //搜索
        $params = array_merge($params, $criteria->params);
        $sql = str_replace(':condition', empty($criteria->condition) ? '' : ' and ' . $criteria->condition, $sql);
        $sqlCount = str_replace(':condition', empty($criteria->condition) ? '' : ' and ' . $criteria->condition, $sqlCount);

        $count = Yii::app()->db->createCommand($sqlCount)->bindValues($params)->queryScalar();
        $command = Yii::app()->db->createCommand($sql);
        return new CSqlDataProvider($command, array(
                'params' => $params,
                'totalItemCount' => $count,
                'sort' => array(
                        'defaultOrder' => 'create_time DESC',
                ),
        ));

    }

    /**
     * 账户明细搜索
     * @return CSqlDataProvider
     */
    public function searchForStore() {
        $criteria = new CDbCriteria;
        $criteria->compare('operate_type', $this->operate_type);
        $criteria->compare('remark', $this->remark, true);
        $searchDate = Tool::searchDateFormat($this->create_time, $this->endTime);
        $criteria->compare('create_time', ">=" . $searchDate['start']);
        $criteria->compare('create_time', "<" . $searchDate['end']);

        return self::searchFlow($criteria, array(self::TYPE_MERCHANT, self::TYPE_AGENT));
    }

    /**
     * 后台列表
     * @return CActiveDataProvider
     */
    public function backendSearch() {
        $criteria = new CDbCriteria;
        $criteria->compare('sku_number', $this->sku_number, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('operate_type', $this->operate_type);
        $criteria->compare('order_code', $this->order_code, true);
        $criteria->compare('node', $this->node, true);
        $criteria->compare('transaction_type', $this->transaction_type, true);
        $pagination = array();
        if (!empty($this->isExport)) {
            $pagination['pageSize'] = 5000;
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
     * 查询流水公共方法
     * @param CDbCriteria $criteria				//查询类
     * @param int/array $type				//角色类型
     * @return CSqlDataProvider
     */
    public static function searchFlow($criteria, $type) {
        $uid = Yii::app()->user->id;       //登录会员id
        $now = strtotime(date('Y-m-d'));      //当前时间
        $monthTable = self::monthTable();      //当月表
        $memberTable = self::hashTable(Yii::app()->user->gw); //会员流水表

        $sqType = is_numeric($type) ? 'type = ' . $type : 'type in (' . implode(",", $type) . ')';

        $sqlTime = ' AND create_time >= '.$now;
        $sql = 'SELECT
                *
            FROM
                ' . $monthTable . '
            WHERE
                account_id = ' . $uid .$sqlTime . '
            AND ' . $sqType . ' :condition
            UNION ALL
                SELECT
                    *
                FROM
                    ' . $memberTable . '
                WHERE
                    account_id = ' . $uid  . '
                AND ' . $sqType . ' :condition';

        $sqlCount = '
                select count(*) from (
                SELECT
                        *
                    FROM
                        ' . $monthTable . '
                    WHERE
                        account_id = ' . $uid . $sqlTime . '
                    AND ' . $sqType . ' :condition
                    UNION ALL
                        SELECT
                            *
                        FROM
                            ' . $memberTable . '
                        WHERE
                            account_id = ' . $uid  . '
                        AND ' . $sqType . ' :condition
                ) as a
                        ';
        //搜索
        $sql = str_replace(':condition', empty($criteria->condition) ? '' : ' and ' . $criteria->condition, $sql);
        $sqlCount = str_replace(':condition', empty($criteria->condition) ? '' : ' and ' . $criteria->condition, $sqlCount);

        $count = Yii::app()->db->createCommand($sqlCount)->bindValues($criteria->params)->queryScalar();
        $command = Yii::app()->db->createCommand($sql);
        return new CSqlDataProvider($command, array(
            'params' => $criteria->params,
            'totalItemCount' => $count,
            'sort' => array(
                'defaultOrder' => 'create_time DESC',
            ),
        ));
    }

    //代理后台查询代理进账明细
    public function searchAgent() {
        $criteria = new CDbCriteria;
        $searchDate = Tool::searchDateFormat($this->create_time, $this->endTime);
        $criteria->compare('create_time', ">=" . $searchDate['start']);
        $criteria->compare('create_time', "<" . $searchDate['end']);

        return self::searchFlow($criteria, array(self::TYPE_AGENT));
    }

    //商家后台查询后台查询代理进账明细
    public function searchSeller() {
        $criteria = new CDbCriteria;
        $searchDate = Tool::searchDateFormat($this->create_time, $this->endTime);
        $criteria->compare('create_time', ">=" . $searchDate['start']);
        $criteria->compare('create_time', "<" . $searchDate['end']);
        return self::searchFlow($criteria, array(self::TYPE_MERCHANT));
    }

    public function getDbConnection() {
        return Yii::app()->ac;
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function getType() {
        return AccountBalance::getType();
    }

    public static function showType($key) {
        $typies = self::getType();
        return isset($typies[$key])?$typies[$key]:'';
    }

    public static function getOperateType($key=null) {
        $arr =  array(
        	self::OPERATE_TYPE_SKU_PAY=>'SKU订单支付',
        	self::OPERATE_TYPE_SKU_SIGN=>'SKU订单签收',
        	self::OPERATE_TYPE_SKU_CANCEL=>'SKU订单取消',
        	self::OPERATE_TYPE_GAME_EXCHANGE=>'游戏兑换',
            self::OPERATE_TYPE_CASH_APPLY => '申请提现',
            self::OPERATE_TYPE_CASH_CANCEL =>'撤消提现',
            self::OPERATE_TYPE_CASH_SUCCESS =>'提现成功',
        	self::OPERATE_TYPE_SKU_GUADAN_IMPORT => '导入挂单积分',
        	self::OPERATE_TYPE_SKU_GUADAN_IMPORT_DEL => '删除挂单积分',
        	self::OPERATE_TYPE_SKU_GUADAN_SALE => '出售挂单积分',
        	self::OPERATE_TYPE_SKU_GUADAN_SALE_STOP => '出售挂单积分-终止',
        	self::OPERATE_TYPE_SKU_GUADAN_PIFA => 'sku挂单批发',
        	self::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN => 'SKU会员购买充值',
        	self::OPERATE_TYPE_SKU_GUADAN__MEMBER_RETURN => 'SKU会员积分到账',
        	self::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_OFFICAL_JIFEN => 'SKU会员购买充值-官方',
        	self::OPERATE_TYPE_SKU_INPUT_GOODS_JIFEN => '商品录入积分奖励',
        	self::OPERATE_TYPE_EBANK_RECHARGE => '网银充值',
                self::OPERATE_TYPE_SKU_FIRST_CONSUMPTION =>'SKU首次消费返还积分',
        );
        return $key!==null?(isset($arr[$key])?$arr[$key]:' '):$arr;
    }


    public static function showOperateType($key) {
        $operateTypes = self::getOperateType();
        return isset($operateTypes[$key])?$operateTypes[$key]:' ';
    }

    /**
     * 事务类型
     * @param type $key
     * @return string
     */
    public static function showTransactinnType($key) {
        $transTypes = array(
            self::TRANSACTION_TYPE_CONSUME => '消费',
            self::TRANSACTION_TYPE_DISTRIBUTION => '分配',
            self::TRANSACTION_TYPE_REFUND => '退款',
            self::TRANSACTION_TYPE_RETURN => '退货',
            self::TRANSACTION_TYPE_ORDER_CANCEL => '取消订单',
            self::TRANSACTION_TYPE_COMMENT => '评论',
            self::TRANSACTION_TYPE_RIGHTS => '维权',
            self::TRANSACTION_TYPE_ORDER_CONFIRM => '订单确认',
            self::TRANSACTION_TYPE_RECHARGE => '充值',
            self::TRANSACTION_TYPE_CASH => '提现',
            self::TRANSACTION_TYPE_CASH_CANCEL => '取消提现',
            self::TRANSACTION_TYPE_ASSIGN => '调拨',
            self::TRANSACTION_TYPE_CASH_HONGBAO_APPLY =>'红包申请',
            self::TRANSACTION_TYPE_CASH_HONGBAO_RECHARGE=>'红包充值',
            self::TRANSACTION_TYPE_OTHER_REFUND=>'其它退款',
            self::TRANSACTION_TYPE_TRANSFER=>'旧余额转账',
            self::TRANSACTION_TYPE_TIAOZHENG=>'调整',
        	self::TRANSACTION_TYPE_ENVELOPE=>'盖讯通红包',
        	self::TRANSACTION_TYPE_FREEZE=>'冻结',
        	self::TRANSACTION_TYPE_GUADAN=>'挂单',
        );
        return isset($transTypes[$key])?$transTypes[$key]:' ';
    }

    /**
     * 获取业务节点内容
     * @author LC
     */
    public static function getBusinessNode()
    {
        return array(
        	self::BUSINESS_NODE_SKU_PAY_PAY=>'SKU订单支付-消费支付',
        	self::BUSINESS_NODE_SKU_PAY_FREEZE=>'SKU订单支付-消费冻结',
        	self::BUSINESS_NODE_SKU_SIGN_CONFIRM=>'SKU订单签收-确认消费',
        	self::BUSINESS_NODE_SKU_SIGN_PAYMENT=>'SKU订单签收-支付货款',
        	self::BUSINESS_NODE_SKU_SIGN_PROFIT=>'SKU订单签收-利润',
        	self::BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_MEMBER=>'SKU订单签收-会员消费奖励',
        	self::BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_OTHER=>'SKU订单签收-收益分配 -其它角色',
        	self::BUSINESS_NODE_SKU_CANCEL_REFUND=>'SKU订单取消-收回退款',
        	self::BUSINESS_NODE_SKU_CANCEL_RETURN=>'SKU订单取消-退还订单金额',
        	self::BUSINESS_NODE_SKU_CANCEL_PAY_CHARGE=>'SKU订单取消-支付手续费',
        	self::BUSINESS_NODE_SKU_CANCEL_GET_CHARGE=>'SKU订单取消-收取手续费',
        	self::BUSINESS_NODE_GAME_EXCHANGE=>'游戏金币兑换',
        	self::BUSINESS_NODE_GAME_INCOME=>'游戏收益',
            self::BUSINESS_NODE_CASH_APPLY =>'申请提现',
            self::BUSINESS_NODE_CASH_CHECK =>'核对提现',
            self::BUSINESS_NODE_CASH_BACK =>'打回提现申请',
            self::BUSINESS_NODE_CASH_CANCEL =>'取消提现',
            self::BUSINESS_NODE_CASH_CONFIRM =>'确认提现',
        	self::BUSINESS_NODE_SKU_YFZ_PAY_PAY => 'SKU-一份子订单支付-消费支付',
        	self::BUSINESS_NODE_SKUYFZ__PAY_FREEZE => 'SKU-一份子订单支付-消费冻结',
        	self::BUSINESS_NODE_SKU_YFZ_SIGN_CONFIRM => 'SKU-一份子订单签收-确认消费',
        	self::BUSINESS_NODE_SKU_YFZ_SIGN_PAYMENT => 'SKU-一份子订单签收-支付货款',
        );
    }

    /**
     * 获取单个业务节点内容
     * @author LC
     */
    public static function showBusinessNode($key) {
        $businessNodes = self::getBusinessNode();
        return $businessNodes[$key];
    }

    /**
     * 按月创建表
     * @return string
     */
    public static function monthTable($time = null) {
        $time = $time === null ? date('Ym', time()) : date('Ym', $time);
        $table = self::$_baseTableName . '_' . $time;
        $baseTable = self::$_baseTableName;
        $rs = Yii::app()->ac->createCommand("SHOW TABLES LIKE '%{$table}%'")->queryScalar();
        if($rs == false){//如果不检查表会导致创建表时锁表
            $sql = "CREATE TABLE IF NOT EXISTS $table LIKE $baseTable;";
            Yii::app()->ac->createCommand($sql)->execute();
        }

        return ACCOUNT . '.' . $table;
    }

    /**
     * 散列创建表
     * @param type $string
     * @return string
     */
    public static function hashTable($string) {
        $suffix = self::getHash($string);
        $table = self::$_baseTableName . '_' . $suffix;
        $baseTable = self::$_baseTableName;
        $sql = "CREATE TABLE IF NOT EXISTS $table LIKE $baseTable;";
        Yii::app()->ac->createCommand($sql)->execute();
        // 同步结构
        self::tableSyn($baseTable,$table);
        return ACCOUNT . '.' . $table;
    }

    /**
     * 生成散列表字串
     * @param type $string
     * @return string
     */
    public static function getHash($string) {
        $string = md5($string);
        return substr($string, 0, 2);
    }

    /**
     * 合并流水数据
     * @param array $order 订单属性
     * @param array $balance 余额表属性
     * @param array $flow 需要替换的流水属性
     * @return array 流水表数据
     */
    public static function mergeFlowData($order = null, $balance, $flow)
    {
        return array_merge(array(
            'account_id' => $balance['account_id'],
            'sku_number' => $balance['sku_number'],
            'type' => $balance['type'],
            'order_id' => isset($order) ? $order['id'] : 0,
            'order_code' => isset($order) ? $order['code'] : '',
            'date' => date('Y-m-d', time()),
            'create_time' => time(),
            'week' => date('W', time()),
            'week_day' => date('N', time()),
            'hour' => date('G', time()),
        ), $flow);
    }

    /**
     * 翻译+货币转换
     * @param $content
     * @return string
     */
    public static function formatContent($content) {
        $content = str_replace('￥', '¥', $content); //统一货币符号
        $reStr = preg_replace_callback('/(¥\d+?.\d+?元)|(\d+?.\d+?元)|(¥\d+?.\d+?)/U', function ($matches) {
            if (preg_match('/\d+?.\d+?/U', $matches[0], $priceArr)) {
                return HtmlHelper::formatPrice($priceArr[0]);
            }
            return $matches[0];
        }, $content);
        return Yii::t('accountFlow', $reStr ? $reStr : $content);
    }

    /**
     * 账户明细中的金额显示
     * @param float $credit 贷方发生额
     * @param float $debit 借方发生额
     * @return string
     */
    public static function showPrice($credit, $debit) {
        if ($debit == 0) {
            return HtmlHelper::formatPrice($credit);
        } else {
            return HtmlHelper::formatPrice(-$debit, 'span', array('style' => 'color:red'));
        }
    }

    /**
     * 将月流水表转移到散列表中
     * Enter description here ...
     * 昨天的数据转移，每天凌晨执行
     * @author LC
     */
    public static function moveHashTable() {
        $yesterday = strtotime(date('Y-m-d 00:00:00')) - 1;
        $time = date('Ym', $yesterday);
        $baseTableName = '{{account_flow}}';
        $flowTable = $baseTableName . '_' . $time;
        $sql = "SELECT * FROM $flowTable WHERE create_time<=$yesterday AND moved=0 ORDER BY sku_number LIMIT 5000";
        $dataQuery = Yii::app()->ac->createCommand($sql)->queryAll();

        //将数据按照会员名称进行排列
        $data = array();
        foreach ($dataQuery as $row) {
        	$row['remark'] = Tool::magicQuotes($row['remark']);
            $data[$row['sku_number']][] = $row;
        }
        foreach ($data as $key => $row) {
            //创建hash表
            $hashTable = AccountFlow::hashTable($key);
            $insertSql = "INSERT INTO $hashTable VALUES";
            $updateSql = "UPDATE $flowTable SET `moved`=1 WHERE id IN (";
            foreach ($row as $item) {
                $updateSql .= $item['id'] . ',';
                $item['id'] = '';
                $item['moved'] = 1;
                $insertSql .= "('" . implode("','", $item) . "'),";
            }
            $insertSql = substr($insertSql, 0, -1);
            $updateSql = substr($updateSql, 0, -1) . ")";
            $transaction = Yii::app()->ac->beginTransaction();
            try {
                Yii::app()->ac->createCommand($insertSql)->execute();
                Yii::app()->ac->createCommand($updateSql)->execute();
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }
    }

    /**
     * 检测之前未转移的流水记录，将之转移到hash表中
     * @author LC
     */
    public static function checkHashTable()
    {
    	$yesterday = strtotime(date('Y-m-d 00:00:00')) - 1;
    	$baseTableName = '{{account_flow}}';

    	$beginY = 2015;
    	$beginM = 1;
    	$y = date('Y', $yesterday);
    	$m = date('n', $yesterday);
    	$m = ($y-$beginY)*12+$m;
    	while ($beginM<=$m && $beginY<=$y)
    	{
    		$curMonth = $beginM%12;
    		$curMonth = ($curMonth==0) ? 12 : $curMonth.'-';

    		$curTableTime = $beginY.sprintf("%02d", $curMonth);
    		$flowTable = $baseTableName . '_' . $curTableTime;
    		$sql = "SELECT * FROM $flowTable WHERE create_time<=$yesterday AND moved=0 ORDER BY sku_number";

    		$dataQuery = Yii::app()->ac->createCommand($sql)->queryAll();
    		//将数据按照会员名称进行排列
    		$data = array();
    		foreach ($dataQuery as $row) {
    			$row['remark'] = Tool::magicQuotes($row['remark']);
    			$data[$row['sku_number']][] = $row;
    		}
    		foreach ($data as $key => $row) {
    			//创建hash表
    			$hashTable = AccountFlow::hashTable($key);
    			$insertSql = "INSERT INTO $hashTable VALUES";
    			$updateSql = "UPDATE $flowTable SET `moved`=1 WHERE id IN (";
    			foreach ($row as $item) {
    				$updateSql .= $item['id'] . ',';
    				$item['id'] = '';
    				$item['moved'] = 1;
    				$insertSql .= "('" . implode("','", $item) . "'),";
    			}
    			$insertSql = substr($insertSql, 0, -1);
    			$updateSql = substr($updateSql, 0, -1) . ")";
    			$transaction = Yii::app()->ac->beginTransaction();
    			try {
    				Yii::app()->ac->createCommand($insertSql)->execute();
    				Yii::app()->ac->createCommand($updateSql)->execute();
    				$transaction->commit();
    			} catch (Exception $e) {
    				$transaction->rollBack();
    			}
    		}

    		if($curMonth==12)
    		{
    			$beginY++;
    		}
    		$beginM++;
    	}
    }

    /**
     * 同步表结构
     * @param $source_name
     * @param $target_name
     */
    public static function tableSyn($source_name,$target_name){
        $table_source = Yii::app()->ac->createCommand("SHOW columns FROM ".$source_name)->queryAll();
        $table_target = Yii::app()->ac->createCommand("SHOW columns FROM ".$target_name)->queryAll();

        foreach($table_source as $key => $val){
            $columns_source[$key] = $val['Field'];
        }
        foreach($table_target as $key => $val){
            $columns_target[$key] = $val['Field'];
        }
        $diff_field = array_diff($columns_source,$columns_target);
        if(!empty($diff_field))foreach($diff_field as $key => $val){
            $sql = "ALTER table ".$target_name;
            $sql .= " ADD COLUMN `".$val."` ".$table_source[$key]['Type']." ".($table_source[$key]['Null'] == 'YES' ? 'NULL' : 'NOT NULL');
            if($table_source[$key]['Default'] == null){
                $sql .= " DEFAULT NULL ";
            }else{
                $sql .= " DEFAULT ".$table_source[$key]['Default'];
            }
            $sql .= " AFTER `".$table_source[$key-1]['Field']."`";
            if($table_source[$key]['Key'] == 'MUL'){
                $sql .= ", ADD INDEX (`".$val."`) ";
            }
            $sql .= ";";
            Yii::app()->ac->createCommand($sql)->execute();
        }
    }

    /**
     * 与流水固定数据合并
     * @param array $field
     * @param null $time
     * @return array
     */
    public static function mergeField(Array $field,$time = null) {
        if($time === null)
            $time = time();
        $publicArr = array(
            'date' => date('Y-m-d', $time),
            'create_time' => $time,
            'week' => date('W', $time),
            'week_day' => date('N', $time),
            'hour' => date('G', $time),
        );
        return CMap::mergeArray($publicArr, $field);
    }
}
