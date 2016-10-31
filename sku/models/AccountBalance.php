<?php

/**
 * 余额表模型类
 * @author wanyun.liu <wanyun_liu@163.com>
 *
 * @property string $id
 * @property string $account_id
 * @property string $sku_number
 * @property string $debit_today_amount
 * @property string $debit_yesterday_amount
 * @property string $today_amount
 * @property string $credit_yesterday_amount
 * @property integer $type
 * @property string $last_update_time
 * @property string $remark
 * @property string create_time
 */
class AccountBalance extends CActiveRecord {

    const TYPE_MERCHANT = 1; // 商家
    const TYPE_AGENT = 2; // 代理
    const TYPE_CONSUME = 3; // 消费
    const TYPE_RETURN = 4; // 待返还
    const TYPE_FREEZE = 5; //  冻结
    const TYPE_COMMON = 6; // 公共
    const TYPE_TOTAL = 9; // 总账户，充值、中转
    const TYPE_CASH = 8; //普通会员提现账户
    const TYPE_GUADAN_XIAOFEI = 10;  //待挂单积分钱包
    const TYPE_GUADAN_SHANGJIA = 11; //商家购买的挂单积分钱包
    const TYPE_GUADAN_DAIFENPEI_XIAOFEI = 12; //消费者待分配钱包
    const TYPE_GUADAN_DAIFENPEI_SHANGJIA = 13; //商家推荐人待分配钱包
//     const YFZ_ORDER_STATUS_SUCCE = 1; //支付完成
//     const YFZ_ORDER_STATUS_FAILURE = 2; //支付失败

    public function tableName() {
        return '{{account_balance}}';
    }

    public function rules() {
        return array(
            array('sku_number, yesterday_amount, today_amount, type', 'safe', 'on' => 'search'),
        );
    }

    public function attributeLabels() {
        return array(
            'id' => '主键',
            'account_id' => '所属账号',
            'sku_number' => 'SKU号',
            'yesterday_amount' => '昨天余额',
            'today_amount' => '今天余额',
            'type' => '类型', //（1商家、2代理、3消费、4待返还、5冻结、6、盖网公共、11总账户）
            'last_update_time' => '最后更新时间',
            'remark' => '备注',
            'create_time' => '创建时间'
        );
    }

    public function search() {
        $criteria = new CDbCriteria;
        $criteria->compare('sku_number', $this->sku_number, true);
        $criteria->compare('today_amount', $this->today_amount, true);
        $criteria->compare('yesterday_amount', $this->yesterday_amount, true);
        $criteria->compare('type', $this->type);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => 'id DESC',
            ),
        ));
    }

    public function getDbConnection() {
        return Yii::app()->ac;
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public static function getType() {
        return array(
            self::TYPE_MERCHANT => '商家',
            self::TYPE_AGENT => '代理',
            self::TYPE_CONSUME => '消费',
            self::TYPE_RETURN => '待返还',
            self::TYPE_FREEZE => '冻结',
            self::TYPE_COMMON => '公共',
//             self::TYPE_SIGN => '签到积分',
//             self::TYPE_GAME => '游戏币',
            self::TYPE_TOTAL => '总账户',
//             self::TYPE_RED => '红包账户',
            self::TYPE_CASH => '普通会员提现账户',
        	self::TYPE_GUADAN_XIAOFEI => '待挂单积分钱包',
        	self::TYPE_GUADAN_SHANGJIA => '商家购买的挂单积分钱包',
        	self::TYPE_GUADAN_DAIFENPEI_XIAOFEI => '消费者待分配钱包',
        	self::TYPE_GUADAN_DAIFENPEI_SHANGJIA => '商家推荐人待分配钱包',
        );
    }

    public static function showType($key=null) {
        $typies = self::getType();
        return isset($typies[$key])?$typies[$key]:' ';
    }

    /**
     * 获取用户余额的数组构建，配合 @see findRecord 使用
     * @param $member 会员对象，或会员记录属性
     * @return array
     * @author jianlin.lin
     */
    public static function getAccountBalanceArrayBuild($member) {
        return array('account_id' => $member['id'], 'sku_number' => $member['sku_number'], 'type' => AccountBalance::TYPE_CONSUME);
    }

    /**
     * 创建及获取余额表账户信息 + 创建账户信息（account_info）
     * @param  array
     * $arr = array(
     *              'account_id'=>
     *              'type'=>
     *              'sku_number'=>
     *          );
     * @param bool
     * @param bool
     * @return array
     * @throws Exception
     */
    public static function findRecord($array, $doTransaction = false,$useLock=true) {
        if (isset($array['account_id']) && isset($array['type']) && isset($array['sku_number'])) {
            $balanceTable = self::model()->tableName();
            $forUpdate = in_array($array['type'], array(self::TYPE_COMMON, self::TYPE_TOTAL)) ? "" : ($useLock == true ? " FOR UPDATE" : ""); //只行锁非盖网帐号
            $condition = '`account_id`=' . $array['account_id'] . ' and `type`=' . $array['type'] . ' and `sku_number`="' . $array['sku_number'] . '"' . $forUpdate;
            $accountBalance = Yii::app()->db->createCommand()->select()->from(ACCOUNT . '.' . $balanceTable)->where($condition)->queryRow();
            if ($accountBalance) {
                return $accountBalance;
            } else {
                unset($array['id']);
                //重新赋值，过滤掉 $array 多余的数据
                $array = array(
                    'account_id' => $array['account_id'],
                    'type' => $array['type'],
                    'sku_number' => $array['sku_number'],
                );
                $balance = $info = $array;
                $balance['create_time'] = $info['create_time'] = time();
                $balance['yesterday_amount'] = '0.00';
                $balance['today_amount'] = '0.00';
                $balance['amount_salt'] = md5(uniqid());
                $balance['last_update_time'] = 0;
                $balance['amount_hash'] = sha1($balance['sku_number'] . $balance['account_id'] . $balance['today_amount'] . $balance['amount_salt'] . AMOUNT_SIGN_KEY);
                if ($doTransaction == true) {
                    $transaction = Yii::app()->db->beginTransaction();
                    try {
                        // 余额表信息创建
                        Yii::app()->db->createCommand()->insert(ACCOUNT . '.' . $balanceTable, $balance);
                        $balance['id'] = Yii::app()->db->lastInsertID;
                        $transaction->commit();
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        return false;
                    }
                } else {
                    // 余额表信息创建
                    Yii::app()->db->createCommand()->insert(ACCOUNT . '.' . $balanceTable, $balance);
                }
                return self::findRecord($array);
            }
        } else
            return false;
    }

    /**
     * 余额表更新操作
     * @param array $records 更新的数组 array('money' => 300, 'value' => -500);
     * @param int $param 主键
     * @return boolean
     */
    public static function calculate($records, $param) {
        if (!empty($records) && $param) {
            $condition = '';
            foreach ($records as $key => $value)
                $condition .= '`' . $key . '` = `' . $key . '` ' . ($value < 0 ? $value : ('+ ' . $value)) . ',';
            $condition = rtrim($condition, ',');
            $account = Yii::app()->db->createCommand('select * from ' . ACCOUNT . '.{{account_balance}} where id=' . $param . ' for update')->queryRow();
            if (empty($account['amount_salt'])) {
                //self::addHashLog('金额密钥不能为空', $account);
                throw new Exception("金额密钥不能为空");
            } else if ($account['type'] != self::TYPE_COMMON && $account['type'] != self::TYPE_TOTAL) { //公共账户、总账户不做检查
                //校验金额
                $hash = sha1($account['sku_number'] . $account['account_id'] . $account['today_amount'] . $account['amount_salt'] . AMOUNT_SIGN_KEY);
                if ($account['amount_hash'] != $hash) {
                    //self::addHashLog('更新余额时金额校验失败 ' . $hash, $account);
                    throw new Exception("更新余额时金额校验失败 -" . $account['amount_hash'] . '-' . $hash);
                }
            }
            //新的hash
            $data = array($account['sku_number'], $account['account_id'], sprintf('%0.2f', $account['today_amount'] + $records['today_amount']), $account['amount_salt'], AMOUNT_SIGN_KEY);
            $newHash = sha1(implode('', $data));
            $sql = 'UPDATE ' . ACCOUNT . '.' . "{{account_balance}}" . ' SET ' . $condition . ', last_update_time=' . time() . ',amount_hash="' . $newHash . '"  WHERE id = ' . $param;
            //self::addHashLog('更新余额 ' . $sql, $account);
            return Yii::app()->db->createCommand($sql)->execute();
        } else
            return false;
    }
    
    /**
     * 取账户余额
     */
    public static function getTodayAmountByGaiNumber($gnumber, $type = self::TYPE_CONSUME) {
        if (empty($gnumber))
            return false;
        $rs = self::model()->find(" sku_number = '{$gnumber}' AND type=" . $type . " ORDER BY id DESC ");
        if (empty($rs))
            return 0;
        return $rs->today_amount * 1;
    }
    
    
    /**
     * 取账户余额
     */
    public static function getMemberTodayAmount($sku_number, $type = self::TYPE_CONSUME) {
    	if (empty($sku_number))
    		return false;
    	$rs = self::model()->find(" sku_number = '{$sku_number}' AND type=" . $type . " ORDER BY id DESC ");
    	if (empty($rs))
    		return 0;
    	return $rs->today_amount * 1;
    }

    /**
     * 获取账号余额,返回新余额加上旧余额
     * @param string $sku_number 会员
     * @param int $type 账户类型
     * @return float
     */
    public static function getAccountAllBalance($sku_number, $type) {
        $accountNew = $accountOld = 0;
        $accountNew = self::getTodayAmountByGaiNumber($sku_number, $type);
        $total = $accountNew ? $accountNew : 0;
        $total +=!empty($accountOld) && $accountOld['today_amount'] ? $accountOld['today_amount'] : 0;
        return $total * 1;
    }

    /**
     * 获取可提现账户的余额，商家+代理
     * @param $memberId
     * @return mixed
     */
    public static function getWithdrawBalance($memberId) {
        $sql = 'select sum(today_amount) from ' . ACCOUNT . '.{{account_balance}}
               where account_id=:mid and type in(:t1,:t2)';
        return Yii::app()->db->createCommand($sql)
                        ->bindValues(array(':mid' => $memberId, ':t1' => self::TYPE_MERCHANT, ':t2' => self::TYPE_AGENT))
                        ->queryScalar();
    }

    /**
     * 获取可提现账户的余额，普通会员
     * @param $memberId
     * @return mixed
     */
    public static function getMemberBalance($memberId) {
        $sql = 'select sum(today_amount) from ' . ACCOUNT . '.{{account_balance}}
               where account_id=:mid and type=:t1';
        return Yii::app()->db->createCommand($sql)
                        ->bindValues(array(':mid' => $memberId, ':t1' => self::TYPE_CASH))
                        ->queryScalar();
    }
    
    /**
     * 获取可提现账户的余额，商家
     * @param $memberId
     * @return mixed
     */
    public static function getShangJiaCashBalance($memberId) {
    	$sql = 'select sum(today_amount) from ' . ACCOUNT . '.{{account_balance}}
               where account_id=:mid and type=:t1';
    	return Yii::app()->db->createCommand($sql)
    	->bindValues(array(':mid' => $memberId, ':t1' => self::TYPE_MERCHANT))
    	->queryScalar();
    }

    /**
     * 获取可消费余额，普通会员
     * @param $memberId
     * @return mixed
     */
    public static function getMemberXiaofeiAmount($memberId) {
        $sql = 'select sum(today_amount) from ' . ACCOUNT . '.{{account_balance}}
               where account_id=:mid and type=:t1';
        return Yii::app()->db->createCommand($sql)
                        ->bindValues(array(':mid' => $memberId, ':t1' => self::TYPE_CONSUME))
                        ->queryScalar();
    }
    
    /**
     * 获取可消费余额，普通会员
     * @param $memberId
     * @return mixed
     */
    public static function getMemberGuadanReturnAmount($memberId) {
    	$sql = 'select sum(today_amount) from ' . ACCOUNT . '.{{account_balance}}
               where account_id=:mid and type=:t1';
    	return Yii::app()->db->createCommand($sql)
    	->bindValues(array(':mid' => $memberId, ':t1' => self::TYPE_RETURN))
    	->queryScalar();
    }

    /**
     * 获取挂单积分余额
     * @param $memberId
     * @return mixed
     */
    public static function getGuadanCommonAmount($type = CommonAccount::TYPE_GUADAN_SALE_UNBUNDLING) {
        $balance = CommonAccount::getAccount($type, AccountBalance::TYPE_TOTAL);

        return $balance['today_amount'];
    }

    public static function getAmountCompare() {
        return array('' => '不限', '< 0' => '小于零', '> 0' => '大于零', '= 0' => '等于零');
    }

    /**
     * 处理事物
     * @param $apiLogData
     * @return bool
     * @throws Exception
     */
    public static function changeBalance($apiLogData, $trans = true) {
        $apiLogTable = ACCOUNT . '.' . 'gw_api_log';
        //检查同一操作是否重复
        $apiLog = Yii::app()->db->createCommand()
                ->select('id')
                ->from($apiLogTable)
                ->where('order_code = :code and operate_type = :operate_type and transaction_type = :transaction_type', array(':code' => $apiLogData['order_code'], ':operate_type' => $apiLogData['operate_type'], ':transaction_type' => $apiLogData['transaction_type']))
                ->queryScalar();

        if (!empty($apiLog)) {
            throw new Exception('请勿重复操作');
        }

        $memberArray = array(
            'account_id' => $apiLogData['account_id'],
            'type' => AccountBalance::TYPE_CONSUME,
            'sku_number' => $apiLogData['sku_number'],
        );

        $extendData = json_decode($apiLogData['data'], true); //原始POST数据
        //事物开始
        if ($trans)  $transaction = Yii::app()->db->beginTransaction();
        try {
            //插入操作
            $result = Yii::app()->db->createCommand()->insert($apiLogTable, $apiLogData);
            if ($result == false)
                throw new Exception('操作不成功');
            $apiLogData['id'] = Yii::app()->db->getLastInsertID();
            switch ($apiLogData['operate_type']) {
                //一分子支付
                case AccountFlow::OPERATE_TYPE_SKU_YFZ_PAY:
                    self::YfzOrderCheckAndPayAndChangeorder($memberArray, $apiLogData);
                    break;
                //支付
                case AccountFlow::OPERATE_TYPE_SKU_PAY :
                    self::pay($memberArray, $apiLogData);
                    break;
                //取消
                case AccountFlow::OPERATE_TYPE_SKU_CANCEL :
                    self::cancelOrder($memberArray, $apiLogData);
                    break;
                //签收
                case AccountFlow::OPERATE_TYPE_SKU_SIGN:
                    self::sign($apiLogData, $extendData['costPrice'], $extendData['merchantMemberId'], $extendData['merchantSkuNumber']);
                    break;
                //充值
                case AccountFlow::OPERATE_TYPE_EBANK_RECHARGE:
                    self::recharge($apiLogData);
                    break;
                //返利
                case AccountFlow::OPERATE_TYPE_SKU_FIRST_CONSUMPTION:
                    self::FirstConsumeRebate($memberArray,$apiLogData);
                    break;
                default:
                    throw new Exception('操作类型错误');
            }
            if ($trans){
                $transaction->commit();
            }
        } catch (Exception $e) {
            if ($trans) $transaction->rollBack();
            throw new Exception($e->getMessage());
        }
//        Yii::app()->db->createCommand()->delete($apiLogTable, 'id='.$apiLogData['id']);

        return true;
    }

    /**
     * @param $title
     * @param string $data
     */
    public static function addHashLog($title, $data = '') {
        if(strpos($_SERVER['SERVER_NAME'],'orderapi') !== false){
            return;
        }
        $trace = debug_backtrace();
        $action = '';
        if (Yii::app()->controller) {
            $action .= Yii::app()->controller->id . '/' . Yii::app()->controller->getAction()->getId();
            $action .= PHP_EOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . PHP_EOL;
        }
        $function = PHP_EOL . $action . PHP_EOL . $trace[1]['class'] . $trace[1]['type'] . $trace[1]['function'] . PHP_EOL;
        if (isset($trace[2]))
            $function .= $trace[2]['class'] . $trace[2]['type'] . $trace[2]['function'] . PHP_EOL;
        if (isset($trace[3]))
            $function .= $trace[3]['class'] . $trace[3]['type'] . $trace[3]['function'] . PHP_EOL;
        if (isset($trace[4]))
            $function .= $trace[4]['class'] . $trace[4]['type'] . $trace[4]['function'] . PHP_EOL;
        
        Yii::log($title . $function . var_export($data, true), CLogger::LEVEL_ERROR, 'hash');
        
    }

    /**
     * 一份子订单支付到处理过程
     * @param array $memberArray   用户信息
     * @param array $orderArray    订单信息
     * @throws Exception
     */
    public static function YfzOrderCheckAndPayAndChangeorder( array $memberArray, array $orderArray){
        //第一步，获取订单中的商品数据
        $sql = 'select * from '.YIFENZI.'.gw_yifenzi_order_goods where order_id='.$orderArray['order_id'];
        
        $OrderGoodsData = Yii::app()->db->createCommand( $sql )->queryAll();
        
        if ( !$OrderGoodsData )
            throw new Exception('订单异常，订单与商品不匹配');
        
        //第二步，根据订单的商品进行当期商品库存检验,因为有多个商品需要进行foreach,如果订单中的一个商品库存不》于购买量则整个订单throw
        
        $GoodsBuyNums = array();//key=>商品ID、value=>购物数量|goods and buy nums;
        $_GoodsData = array();
        foreach ( $OrderGoodsData as $k=>$v ){
            $sql = "select * from ".YIFENZI.'.gw_yifenzi_yfzgoods where goods_id='.$v['goods_id'].' and current_nper='.$v['current_nper'].' for update';

            $GoodsData = Yii::app()->db->createCommand( $sql )->queryRow();

            if ( !$GoodsData )
                throw new Exception('无此商品或检查商品期数是否已更新');
            
            //验证用户购买商品数量是否大于库存
            if ( ($v['goods_number'] * 1) > ($GoodsData['goods_number']*1) ){
                throw new Exception('购买人次不足，需再次确定购买.请查看账户余额');
                return false;
            }

            
            $GoodsBuyNums[$v['goods_id']] =$v['goods_number'] * 1;
            $_GoodsData[$v['goods_id']] = $GoodsData;
        }

        if (count($OrderGoodsData) != count($_GoodsData))
            throw new Exception('订单数据有误');
        
        //判断用户是怎么哪一种支付方式，1、直接积分支付；2、先微信充值再来积分支付
        //第三步为订单支付流程，所有流程中只要有一步操作不通过数据都会回滚。此订单没有完成之前其它用户的操作不能进行库存修改
        self::pay($memberArray, $orderArray, array('debit'=>AccountFlow::BUSINESS_NODE_SKU_YFZ_PAY_PAY,"credit"=>AccountFlow::BUSINESS_NODE_SKUYFZ__PAY_FREEZE));

        //流水
        self::yfzcalculate($memberArray, $orderArray);
        //积分支付完成时间
        $pay_time = time();

        //第四步订单处理，例如：订单的状态，购物删除已购买的记录，库存的减少，订单记录
        $sql = "select * from ".YIFENZI.'.gw_yifenzi_order where order_id='.$orderArray['order_id'];
        $OrderData = Yii::app()->db->createCommand( $sql )->queryRow();

        //判断订单状态是否已经支付或者为作废订单
        if ( $OrderData['order_status'] >= 1 )
            throw new Exception('订单已经支付或者已完成');

        //状态检验完成之后修改订单状态为支付成功
//                     $condition = "pay_time=".$pay_time.',order_status='.self::YFZ_ORDER_STATUS_SUCCE.' ';
        $condition = "pay_time=".$pay_time.',order_status='.YfzOrder::STATUS_PAY_SUCCESS.' ';

        $sql = "update ".YIFENZI.'.gw_yifenzi_order set '.$condition.' where order_id='.$orderArray['order_id'];
        if (!Yii::app()->db->createCommand($sql)->execute())
            throw new Exception('订单提交失败，请联系管理人员进行处理');

        //处理当期商品的库存
        unset($k);
        unset($v);
        foreach ( $OrderGoodsData as $k=>$v ){
            if ( $v['current_nper'] != $_GoodsData[$v['goods_id']]['current_nper'] ){
                throw new Exception('当前购买次数不足，请购买下一期商品');
                return false;
            }
            //用这个商品的市场价格 “/”每人次得出来库存，注意二者取余不能大于0
            $_goods_number = (($_GoodsData[$v['goods_id']]['shop_price'] * 1) / ($_GoodsData[$v['goods_id']]['single_price'] * 1)) * 1;
            if ( !is_float($_goods_number) )
                throw new Exception('订单提交失败，请联系管理人员进行处理');

            //给每一个商品分配一个幸运号，以json的格式存入订单商品表中
            $luckyArr = array();
            for( $i=0;$i<$v['goods_number'];$i++){
                $luckyArr[] = (10000001 + ($_goods_number - $_GoodsData[$v['goods_id']]['goods_number']) + $i);
            }

            //把中奖幸运号存入订单商品表中
            $sql = "update ".YIFENZI.'.gw_yifenzi_order_goods set winning_code="'.json_encode($luckyArr).'" where order_id='.$orderArray['order_id'].' and goods_id='.$v['goods_id'];
            if (!Yii::app()->db->createCommand($sql)->execute())
                throw new Exception('订单提交失败，请联系管理人员进行处理');

            //判断当期商品库存是否相减等于0，如果乖于0的话就要更新商品期数和商品库存
            if ( ($_GoodsData[$v['goods_id']]['goods_number'] - $v['goods_number']) == 0 ){
                //算出得奖者并保存数据
                Fun::calculateWinning($orderArray['order_id'],$v['goods_id'],$v['current_nper']);

                $sql = "update ".YIFENZI.'.gw_yifenzi_yfzgoods set goods_number = '.$_goods_number .',current_nper=current_nper+1 where goods_id='.$v['goods_id'];
                if (!Yii::app()->db->createCommand($sql)->execute())
                    throw new Exception('订单提交失败，请联系管理人员进行处理');

            }else{
                //商品库存更新
                $sql = "update ".YIFENZI.'.gw_yifenzi_yfzgoods set goods_number=goods_number-'.$v['goods_number'].' where current_nper= '.$v['current_nper'].' and goods_id='.$v['goods_id'];
                if (!Yii::app()->db->createCommand($sql)->execute())
                    throw new Exception('订单提交失败，请联系管理人员进行处理');
            }

            //删除购物车中数据
            $condition = "goods_id={$v['goods_id']} and member_id = ".$memberArray['account_id'];
            if (!Yii::app()->db->createCommand()->delete(YIFENZI.'.gw_yifenzi_cart',$condition))
                throw new Exception('订单提交失败，请联系管理人员进行处理');
        }
        
    }

    /**
     * 从账号A积分转入到积分B
     * @param $apiLogData   订单数据
     * @param $money 转入积分
     */
    public static function AccountOutIn($apiLogData, $money=1){
        $flow_table_name = AccountFlow::monthTable();

        //转账给谁的账号
        $data = json_decode($apiLogData['data'], true);
        $tMemberArray = array(
            'account_id' => $data['t']['id'],
            'type' => AccountBalance::TYPE_CONSUME,
            'sku_number' => $data['t']['sku_number'],
        );

        //消费账户
//        $accountBalance = AccountBalance::findRecord($memberArray);
        $accountBalance = CommonAccount::getAccount(CommonAccount::TYPE_GUADAN_COST_PAY,AccountBalance::TYPE_COMMON);

        //转账给谁的账号
        $TaccountBalance = AccountBalance::findRecord($tMemberArray);
        //消费余额
        AccountBalance::calculate(array('today_amount' => -$money*1), $accountBalance['id']);
        //借方(会员)
        $debit = array(
            'account_id' => $accountBalance['account_id'],
            'sku_number' => $accountBalance['sku_number'],
            'type' => $accountBalance['type'],
            'current_balance' => $accountBalance['today_amount'],
            'debit_amount' => $money,
            'operate_type' => AccountFlow::OPERATE_TYPE_SIGN_TIAOZHENG,
            'order_id' => $apiLogData['order_id'],
            'order_code' => $apiLogData['order_code'],
            'remark' => $apiLogData['remark'],
            'node' => AccountFlow::TIAOZHENG_NODE_OUT,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_TIAOZHENG,
            'flag' => 0,
            'date' => date('Y-m-d', time()),
            'create_time' => time(),
            'week' => date('W', time()),
            'week_day' => date('N', time()),
            'hour' => date('G', time()),
            'ratio' => '0',
        );
        Yii::app()->db->createCommand()->insert($flow_table_name, $debit);


        //暂收账户加钱
        AccountBalance::calculate(array('today_amount' => $money*1), $TaccountBalance['id']);
        //贷方（暂收账户）
        $credit = array(
            'account_id' => $TaccountBalance['account_id'],
            'sku_number' => $TaccountBalance['sku_number'],
            'type' => $TaccountBalance['type'],
            'current_balance' => $TaccountBalance['today_amount'],
            'credit_amount' => $money,
            'operate_type' => AccountFlow::OPERATE_TYPE_SIGN_TIAOZHENG,
            'order_id' => $apiLogData['order_id'],
            'order_code' => $apiLogData['order_code'],
            'remark' => $apiLogData['remark'],
            'node' => AccountFlow::TIAOZHENG_NODE_IN,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_TIAOZHENG,
            'flag' => 0,
            'date' => date('Y-m-d', time()),
            'create_time' => time(),
            'week' => date('W', time()),
            'week_day' => date('N', time()),
            'hour' => date('G', time()),
        );
        Yii::app()->db->createCommand()->insert($flow_table_name, $credit);

    }

    /**
     * 支付的方法
     * @param array $memberArray
     * @param array $orderArray
     * @throws Exception
     */
    public static function pay($memberArray, $orderArray ,$node=false) {

        $flow_table_name = AccountFlow::monthTable();
        $money = $orderArray['money'];

        //公共账户
//         print_r($memberArray);
        $balanceCommon = CommonAccount::getAccount(CommonAccount::TYPE_TOTAL, AccountBalance::TYPE_TOTAL);
//         print_r($balanceCommon);


        //消费账户
        $accountBalance = AccountBalance::findRecord($memberArray);
        if (($accountBalance['today_amount'] - $money) < 0) {
            throw new Exception('余额不够支付');
        }

        //消费余额
        AccountBalance::calculate(array('today_amount' => -$money*1), $accountBalance['id']);
        //借方(会员)
        $debit = array(
            'account_id' => $accountBalance['account_id'],
            'sku_number' => $accountBalance['sku_number'],
            'type' => $accountBalance['type'],
            'current_balance' => $accountBalance['today_amount'],
            'debit_amount' => $money,
            'operate_type' => $orderArray['operate_type'],
            'order_id' => $orderArray['order_id'],
            'order_code' => $orderArray['order_code'],
            'remark' => $orderArray['remark'],
            'node' => AccountFlow::BUSINESS_NODE_SKU_PAY_PAY,
            'transaction_type' => $orderArray['transaction_type'],
            'flag' => 0,
            'date' => date('Y-m-d', time()),
            'create_time' => time(),
            'week' => date('W', time()),
            'week_day' => date('N', time()),
            'hour' => date('G', time()),
            'ratio' => '0',
        );

        //兼容一份子流水节点
        if (is_array($node)) $debit['node'] = $node['debit'];
        Yii::app()->db->createCommand()->insert($flow_table_name, $debit);

        //暂收账户加钱
        AccountBalance::calculate(array('today_amount' => $money*1), $balanceCommon['id']);
        //贷方（暂收账户）
        $credit = array(
            'account_id' => $balanceCommon['account_id'],
            'sku_number' => $balanceCommon['sku_number'],
            'type' => $balanceCommon['type'],
            'current_balance' => $balanceCommon['today_amount'],
            'credit_amount' => $money,
            'operate_type' => $orderArray['operate_type'],
            'order_id' => $orderArray['order_id'],
            'order_code' => $orderArray['order_code'],
            'remark' => $orderArray['remark'],
            'node' => AccountFlow::BUSINESS_NODE_SKU_PAY_FREEZE,
            'transaction_type' => $orderArray['transaction_type'],
            'flag' => 0,
            'date' => date('Y-m-d', time()),
            'create_time' => time(),
            'week' => date('W', time()),
            'week_day' => date('N', time()),
            'hour' => date('G', time()),
        );

        //兼容一份子流水节点
        if (is_array($node)) $credit['node'] = $node['credit'];
        
        Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
    }

    /**
     * 首次支付返还金额
     * @param Array $memberArray
     * @param Array $orderArray
     * @throws Exception
     */
    public static function FirstConsumeRebate($memberArray,$orderArray){
        $flow_table_name = AccountFlow::monthTable();
        $money = $orderArray['money'];

        //兼容返利金额
        $memberArray['type'] = AccountBalance::TYPE_CONSUME;

        //公共账户
        $balanceCommon = CommonAccount::getAccount(CommonAccount::TYPE_GUADAN_COST_PAY, AccountBalance::TYPE_TOTAL);

        //消费余额
        AccountBalance::calculate(array('today_amount' => -$money*1), $balanceCommon['id']);
        //借方(公共账户)
        $debit = array(
            'account_id' => $balanceCommon['account_id'],
            'sku_number' => $balanceCommon['sku_number'],
            'type' => $balanceCommon['type'],
            'current_balance' => $balanceCommon['today_amount'],
            'debit_amount' => $money,
            'operate_type' => $orderArray['operate_type'],
            'order_id' => $orderArray['order_id'],
            'order_code' => $orderArray['order_code'],
            'remark' => $orderArray['remark'],
            'node' => AccountFlow::BUSINESS_NODE_SKU_COST_PAY,
            'transaction_type' => $orderArray['transaction_type'],
            'flag' => 0,
            'date' => date('Y-m-d', time()),
            'create_time' => time(),
            'week' => date('W', time()),
            'week_day' => date('N', time()),
            'hour' => date('G', time()),
            'ratio' => '0',
        );
        Yii::app()->db->createCommand()->insert($flow_table_name, $debit);

        //暂收账户加钱
        $accountBalance = AccountBalance::findRecord($memberArray);
        AccountBalance::calculate(array('today_amount' => $money*1), $accountBalance['id']);
        //贷方（暂收账户）
        $credit = array(
            'account_id' => $accountBalance['account_id'],
            'sku_number' => $accountBalance['sku_number'],
            'type' => $accountBalance['type'],
            'current_balance' => $accountBalance['today_amount'],
            'credit_amount' => $money,
            'operate_type' => $orderArray['operate_type'],
            'order_id' => $orderArray['order_id'],
            'order_code' => $orderArray['order_code'],
            'remark' => $orderArray['remark'],
            'node' => AccountFlow::BUSINESS_NODE_SKU_MEMBER_INCOME,
            'transaction_type' => $orderArray['transaction_type'],
            'flag' => 0,
            'date' => date('Y-m-d', time()),
            'create_time' => time(),
            'week' => date('W', time()),
            'week_day' => date('N', time()),
            'hour' => date('G', time()),
        );

        Yii::app()->db->createCommand()->insert($flow_table_name, $credit);

    }

    /**
     * 一份子确认消费
     *        利润流水
     * @param Array $memberArray
     * @param Array $orderArray
     * @throws NULL
     */
    public static function yfzcalculate($memberArray, $orderArray){
        $flow_table_name = AccountFlow::monthTable();
        $money = $orderArray['money'];
        
//         $balanceCommon = CommonAccount::getAccount(CommonAccount::TYPE_TOTAL, AccountBalance::TYPE_TOTAL);
        $balanceCommon = CommonAccount::getAccount(CommonAccount::TYPE_GAI_INCOME, AccountBalance::TYPE_COMMON);
        
        $accountBalance = CommonAccount::getAccount(CommonAccount::TYPE_TOTAL, AccountBalance::TYPE_TOTAL);
        
        //暂收账户减钱
        AccountBalance::calculate(array('today_amount' => -$money*1), $accountBalance['id']);
        
        //借方（暂收账户）
        $debit = array(
            'account_id' => $accountBalance['account_id'],
            'sku_number' => $accountBalance['sku_number'],
            'type' => $accountBalance['type'],
            'current_balance' => $accountBalance['today_amount'],
            'debit_amount' => $money,
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_YFZ_SIGN,
            'order_id' => $orderArray['order_id'],
            'order_code' => $orderArray['order_code'],
            'remark' => $orderArray['remark'],
            'node' => AccountFlow::BUSINESS_NODE_SKU_YFZ_SIGN_CONFIRM,
            'transaction_type' => $orderArray['transaction_type'],
            'flag' => 0,
            'date' => date('Y-m-d', time()),
            'create_time' => time(),
            'week' => date('W', time()),
            'week_day' => date('N', time()),
            'hour' => date('G', time()),
        );
        
        Yii::app()->db->createCommand()->insert($flow_table_name, $debit);
        
        //利润
        AccountBalance::calculate(array('today_amount' => $money*1), $balanceCommon['id']);
        //贷方（利润）
        $credit = array(
            'account_id' => $balanceCommon['account_id'],
            'sku_number' => $balanceCommon['sku_number'],
            'type' => $balanceCommon['type'],
            'current_balance' => $balanceCommon['today_amount'],
            'credit_amount' => $money,
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_YFZ_SIGN,
            'order_id' => $orderArray['order_id'],
            'order_code' => $orderArray['order_code'],
            'remark' => $orderArray['remark'],
            'node' => AccountFlow::BUSINESS_NODE_SKU_YFZ_SIGN_PAYMENT,
            'transaction_type' => $orderArray['transaction_type'],
            'flag' => 0,
            'date' => date('Y-m-d', time()),
            'create_time' => time(),
            'week' => date('W', time()),
            'week_day' => date('N', time()),
            'hour' => date('G', time()),
        );
        Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
    }
    
    /**
     * 取消订单，返还扣款给用户
     * @param array $memberArray
     * $memberArray = array(
     *              'account_id'=>
     *              'type'=>
     *              'sku_number'=>
     *          );
     * @param array $apiLogData
     */
    public static function cancelOrder($memberArray, $apiLogData) {
        $flow_table_name = AccountFlow::monthTable();

        $money = $apiLogData['money'];
        //公共账户
        $balanceCommon = CommonAccount::getAccount(CommonAccount::TYPE_TOTAL, AccountBalance::TYPE_TOTAL);
        //消费账户
        $accountBalance = AccountBalance::findRecord($memberArray);

        AccountBalance::calculate(array('today_amount' => $money*1), $accountBalance['id']); //取消订单，返还用户扣款
        AccountBalance::calculate(array('today_amount' => -$money*1), $balanceCommon['id']); //暂收账 扣钱

        $ip = Tool::ip2int(Yii::app()->request->userHostAddress);
        $timeStamp = time();
        //借方(会员)
        $data = array(
            'account_id' => $accountBalance['account_id'],
            'sku_number' => $accountBalance['sku_number'],
            'type' => $accountBalance['type'],
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_CANCEL,
            'order_id' => $apiLogData['order_id'],
            'order_code' => $apiLogData['order_code'],
            'remark' => $apiLogData['remark'],
            'transaction_type' => $apiLogData['transaction_type'],
            'current_balance' => $accountBalance['today_amount'],
            'debit_amount' => -$money,
            'node' => AccountFlow::BUSINESS_NODE_SKU_CANCEL_REFUND,
            'flag' => 0,
            'ratio' => 0,
            'date' => date('Y-m-d', $timeStamp),
            'create_time' => $timeStamp,
            'week' => date('W', $timeStamp),
            'week_day' => date('N', $timeStamp),
            'hour' => date('G', $timeStamp),
        );
        Yii::log('取消订单会员流水' . var_export($data, true));
        Yii::app()->db->createCommand()->insert($flow_table_name, $data);

        //贷方（线下暂收账户）
        $data = array(
            'account_id' => $balanceCommon['account_id'],
            'sku_number' => $balanceCommon['sku_number'],
            'type' => $balanceCommon['type'],
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_CANCEL,
            'order_id' => $apiLogData['order_id'],
            'order_code' => $apiLogData['order_code'],
            'remark' => $apiLogData['remark'],
            'transaction_type' => $apiLogData['transaction_type'],
            'current_balance' => $balanceCommon['today_amount'],
            'credit_amount' => -$money,
            'node' => AccountFlow::BUSINESS_NODE_SKU_CANCEL_RETURN,
            'flag' => 0,
            'ratio' => 0,
            'date' => date('Y-m-d', $timeStamp),
            'create_time' => $timeStamp,
            'week' => date('W', $timeStamp),
            'week_day' => date('N', $timeStamp),
            'hour' => date('G', $timeStamp),
        );
        Yii::log('取消订单暂收流水' . var_export($data, true));
        Yii::app()->db->createCommand()->insert($flow_table_name, $data);
    }

    /**
     * 签收的方法
     */
    public static function sign($apiLogData, $costPrice, $merchantMemberId, $merchantSkuNumber) {
        $flow_table_name = AccountFlow::monthTable();
        $timeStamp = time();
        $money = $apiLogData['money'];

        //1、暂收账户扣钱
        $balanceCommon = CommonAccount::getAccount(CommonAccount::TYPE_TOTAL, AccountBalance::TYPE_TOTAL);
        AccountBalance::calculate(array('today_amount' => -$money*1), $balanceCommon['id']); //暂收账加钱
        $debit = array(
            'account_id' => $balanceCommon['account_id'],
            'sku_number' => $balanceCommon['sku_number'],
            'type' => $balanceCommon['type'],
            'current_balance' => $balanceCommon['today_amount'],
            'debit_amount' => $money,
            'operate_type' => $apiLogData['operate_type'],
            'order_id' => $apiLogData['order_id'],
            'order_code' => $apiLogData['order_code'],
            'remark' => $apiLogData['remark'],
            'node' => AccountFlow::BUSINESS_NODE_SKU_SIGN_CONFIRM,
            'transaction_type' => $apiLogData['transaction_type'],
            'flag' => 0,
            'date' => date('Y-m-d', $timeStamp),
            'create_time' => $timeStamp,
            'week' => date('W', $timeStamp),
            'week_day' => date('N', $timeStamp),
            'hour' => date('G', $timeStamp),
        );
        Yii::app()->db->createCommand()->insert($flow_table_name, $debit);


        //2、商家得到供货款
        $merchantMemberArray = array(
            'account_id' => $merchantMemberId,
            'type' => AccountBalance::TYPE_MERCHANT,
            'sku_number' => $merchantSkuNumber,
        );
        $merchantBalance = AccountBalance::findRecord($merchantMemberArray);

        // 商家收益=商品供货价+运费
        $gongHuoJia = bcadd($costPrice, $apiLogData['freight'], 2);
        Yii::log('222' . $merchantMemberId . var_export($merchantBalance, true));
        //商家增加供货款
        AccountBalance::calculate(array('today_amount' => $gongHuoJia), $merchantBalance['id']);
        $credit = array(
            'account_id' => $merchantBalance['account_id'],
            'sku_number' => $merchantBalance['sku_number'],
            'type' => $merchantBalance['type'],
            'current_balance' => $merchantBalance['today_amount'],
            'credit_amount' => $gongHuoJia,
            'operate_type' => $apiLogData['operate_type'],
            'order_id' => $apiLogData['order_id'],
            'order_code' => $apiLogData['order_code'],
            'remark' => '商家供货款￥' . $gongHuoJia,
            'node' => AccountFlow::BUSINESS_NODE_SKU_SIGN_PAYMENT,
            'transaction_type' => $apiLogData['transaction_type'],
            'flag' => 0,
            'date' => date('Y-m-d', $timeStamp),
            'create_time' => $timeStamp,
            'week' => date('W', $timeStamp),
            'week_day' => date('N', $timeStamp),
            'hour' => date('G', $timeStamp),
        );
        Yii::app()->db->createCommand()->insert($flow_table_name, $credit);


        $orderData = Yii::app()->db->createCommand()
            ->select('code,status,pay_status,distribute_config')
            ->from('{{orders}}')
            ->where('code=:code',array(':code'=>$apiLogData['order_code']))
            ->queryRow();
        if(!empty($orderData)){
            if($orderData['pay_status'] == Order::PAY_STATUS_YES && ($orderData['status'] == Order::STATUS_PAY || $orderData['status'] == Order::STATUS_SEND)){
                $distribute_config = CJSON::decode($orderData['distribute_config'],true);
                if($distribute_config){
                    Formula::_sign($distribute_config);
                }
            }else{
                throw new Exception('订单状态异常,不能签收');
            }
        }

    }

    /**
     * 第三方支付的时候充值
     * @param $apiLogData
     */
    public static function recharge($apiLogData) {
        $arr = array(
            'account_id' => $apiLogData['account_id'],
            'type' => AccountBalance::TYPE_CONSUME,
            'sku_number' => $apiLogData['sku_number']
        );


        //月表
        // 会员余额表记录创建
        $monthTable = AccountFlow::monthTable();

        $memberAccountBalance = AccountBalance::findRecord($arr);

        // 会员充值流水 贷 +
        $MemberCredit = array(
            'account_id' => $memberAccountBalance['account_id'],
            'sku_number' => $memberAccountBalance['sku_number'],
            'type' => AccountFlow::TYPE_CONSUME,
            'current_balance' => $memberAccountBalance['today_amount'],
            'credit_amount' => $apiLogData['money'],
            'operate_type' => AccountFlow::OPERATE_TYPE_EBANK_RECHARGE,
            'order_id' => $apiLogData['order_id'],
            'order_code' => $apiLogData['order_code'],
            'remark' => $apiLogData['gai_number'] . '充值消费' . $apiLogData['money'],
            'node' => $apiLogData['node'],
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
            'recharge_type' => AccountFlow::RECHARGE_TYPE_BANK,
            'flag' => 0
        );

        // 会员账户余额表更新
        AccountBalance::calculate(array('today_amount' => $apiLogData['money']*1), $memberAccountBalance['id']);
        // 借贷流水1.按月

        Yii::app()->db->createCommand()->insert($monthTable, AccountFlow::mergeField($MemberCredit));
    }

    /**
     * 获取商家账户的挂单积分余额
     * @param $memberId
     * @return mixed
     */
    public static function getPartnerGuadanBalance($memberId) {
        $sql = 'select sum(today_amount) from ' . ACCOUNT . '.{{account_balance}}
               where account_id=:mid and type=:t1';
        return Yii::app()->db->createCommand($sql)
                        ->bindValues(array(':mid' => $memberId, ':t1' => self::TYPE_GUADAN_DAIFENPEI_SHANGJIA))
                        ->queryScalar();
    }

    /**
     * 获取商家账户的挂单积分余额
     * @param $memberId
     * @return mixed
     */
    public static function getPartnerGuadanScorePoolBalance($memberId) {
        $sql = 'select sum(today_amount) from ' . ACCOUNT . '.{{account_balance}}
               where account_id=:mid and type=:t1';
        return Yii::app()->db->createCommand($sql)
                        ->bindValues(array(':mid' => $memberId, ':t1' => self::TYPE_GUADAN_SHANGJIA))
                        ->queryScalar();
    }

    /**
     * 商家批发挂单积分 支付逻辑
     * @param $apiLogData
     * 
     * public static function sign($apiLogData, $costPrice, $merchantMemberId, $merchantSkuNumber)
     * 
     */
    public static function guadanPifaPay($money, $price, $member_id, $order_info, $guadanAccountType = CommonAccount::TYPE_GUADAN_SALE_UNBUNDLING, $remark = '商家批发挂单积分') {

        error_reporting(0);
        $flow_table_name = AccountFlow::monthTable();
        $timeStamp = time();


        $memberInfo = Member::model()->findByPk($member_id);
        if (empty($memberInfo))
            return false;

        //消费支付
        $MemberArray = array(
            'account_id' => $member_id,
            'type' => AccountBalance::TYPE_CONSUME,
            'sku_number' => $memberInfo['sku_number'],
        );
        $memberBalance = AccountBalance::findRecord($MemberArray);

        AccountBalance::calculate(array('today_amount' => -$price), $memberBalance['id']);
        $debit = array(
            'account_id' => $memberBalance['account_id'],
            'sku_number' => $memberBalance['sku_number'],
            'type' => $memberBalance['type'],
            'current_balance' => $memberBalance['today_amount'],
            'debit_amount' => $price,
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_PIFA,
            'order_id' => $order_info['id'],
            'order_code' => $order_info['code'],
            'remark' => $remark,
            'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_PIFA_PAY,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_CONSUME,
            'flag' => 0,
            'date' => date('Y-m-d', $timeStamp),
            'create_time' => $timeStamp,
            'week' => date('W', $timeStamp),
            'week_day' => date('N', $timeStamp),
            'hour' => date('G', $timeStamp),
        );
        Yii::app()->db->createCommand()->insert($flow_table_name, $debit);

        //利润   利润就是原金额
        $profitMoney = $price;
        if ($profitMoney > 0) {
            $gaiBalance = CommonAccount::getAccount(CommonAccount::TYPE_GAI_INCOME, AccountBalance::TYPE_COMMON);
            AccountBalance::calculate(array('today_amount' => $profitMoney), $gaiBalance['id']);
            $credit = array(
                'account_id' => $gaiBalance['account_id'],
                'sku_number' => $gaiBalance['sku_number'],
                'type' => $gaiBalance['type'],
                'current_balance' => $gaiBalance['today_amount'],
                'credit_amount' => $profitMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_PIFA,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_PIFA_PROFIT,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_CONSUME,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
        }



        //售卖挂单积分池转出
        $poolOutMoney = $price;
        if ($poolOutMoney > 0) {
            $poolBalance = CommonAccount::getAccount($guadanAccountType, AccountBalance::TYPE_TOTAL);
            AccountBalance::calculate(array('today_amount' => -$poolOutMoney), $poolBalance['id']);
            $debit = array(
                'account_id' => $poolBalance['account_id'],
                'sku_number' => $poolBalance['sku_number'],
                'type' => $poolBalance['type'],
                'current_balance' => $poolBalance['today_amount'],
                'debit_amount' => $poolOutMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_PIFA,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_PIFA_GAI_AMOUNT_OUT,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_CONSUME,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $debit);
        }


        //商家购买的挂单积分转入
        $partnerInMoney = $money;
        if ($partnerInMoney > 0) {

            $partnerInArray = array(
                'account_id' => $member_id,
                'type' => AccountBalance::TYPE_GUADAN_SHANGJIA,
                'sku_number' => $memberInfo['sku_number'],
            );
            $partnerInBalance = AccountBalance::findRecord($partnerInArray);

            AccountBalance::calculate(array('today_amount' => $partnerInMoney), $partnerInBalance['id']);
            $credit = array(
                'account_id' => $partnerInBalance['account_id'],
                'sku_number' => $partnerInBalance['sku_number'],
                'type' => $partnerInBalance['type'],
                'current_balance' => $partnerInBalance['today_amount'],
                'credit_amount' => $partnerInMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_PIFA,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_PIFA_PARTNER_AMOUNT_IN,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_CONSUME,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
        }

         //SK成本支出 （折扣部分）
         $costOutMoney =$money-$price;
         if ($costOutMoney > 0) {
         	$costOutBalance = CommonAccount::getAccount(CommonAccount::TYPE_GUADAN_COST_PAY, AccountBalance::TYPE_TOTAL);
         	AccountBalance::calculate(array('today_amount' => -$costOutMoney), $costOutBalance['id']);
         	$credit = array(
         			'account_id' => $costOutBalance['account_id'],
         			'sku_number' => $costOutBalance['sku_number'],
         			'type' => $costOutBalance['type'],
         			'current_balance' => $costOutBalance['today_amount'],
         			'debit_amount' => $costOutMoney,
         			'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_PIFA,
         			'order_id' => $order_info['id'],
         			'order_code' => $order_info['code'],
         			'remark' => $remark ,
         			'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_PIFA_COST,
         			'transaction_type' => AccountFlow::TRANSACTION_TYPE_CONSUME,
         			'flag' => 0,
         			'date' => date('Y-m-d', $timeStamp),
         			'create_time' => $timeStamp,
         			'week' => date('W', $timeStamp),
         			'week_day' => date('N', $timeStamp),
         			'hour' => date('G', $timeStamp),
         	);
         	Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
         }
         //商家待分配
//         $rule = Yii::app()->db->createCommand()
//         ->select('distribution_ratio')
//         ->from('{{guadan_partner_config}}')
//         ->where('status='.GuadanPartnerConfig::STATUS_ENABLE)
//         ->queryRow();
//         $toDistRadio = !empty($rule)?$rule['distribution_ratio']/100:0;
//         $toDistMoney =$money*$toDistRadio;
//         if ($toDistMoney > 0) {
//         	$toDistArray = array(
//         			'account_id' => $member_id,
//         			'type' => AccountBalance::TYPE_GUADAN_DAIFENPEI_SHANGJIA,
//         			'sku_number' => $memberInfo['sku_number'],
//         	);
//         	$toDistBalance = AccountBalance::findRecord($toDistArray);
//         	AccountBalance::calculate(array('today_amount' => $toDistMoney), $toDistBalance['id']);
//         	$credit = array(
//         			'account_id' => $toDistBalance['account_id'],
//         			'sku_number' => $toDistBalance['sku_number'],
//         			'type' => $toDistBalance['type'],
//         			'current_balance' => $toDistBalance['today_amount'],
//         			'credit_amount' => $toDistMoney,
//         			'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_PIFA,
//         			'order_id' => $order_info['id'],
//         			'order_code' => $order_info['code'],
//         			'remark' => $remark ,
//         			'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_PIFA_PARTNER_TO_DISTRIBUTION,
//         			'transaction_type' => AccountFlow::TRANSACTION_TYPE_CONSUME,
//         			'flag' => 0,
//         			'date' => date('Y-m-d', $timeStamp),
//         			'create_time' => $timeStamp,
//         			'week' => date('W', $timeStamp),
//         			'week_day' => date('N', $timeStamp),
//         			'hour' => date('G', $timeStamp),
//         	);
//         	Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
//         }

        return true;
    }

    /**
     * 积分兑换金币
     * @param array $memberArray
     * @param array $apiLogData
     * @throws Exception
     */
    public static function exchange($memberArray, $apiLogData) {
        $flow_table_name = AccountFlow::monthTable();
        $balanceCommon = CommonAccount::getGameAccount(); //游戏收益账户
        $accountBalance = AccountBalance::findRecord($memberArray); //消费账户

        if ($accountBalance['today_amount'] - $apiLogData['money'] < 0) {
            throw new Exception('余额不够支付');
        }
        //消费余额
        AccountBalance::calculate(array('today_amount' => -$apiLogData['money']), $accountBalance['id']);
        //借方(会员)
        $debit = array(
            'account_id' => $accountBalance['account_id'],
            'sku_number' => $accountBalance['sku_number'],
            'type' => $accountBalance['type'],
            'current_balance' => $accountBalance['today_amount'],
            'debit_amount' => $apiLogData['money'],
            'operate_type' => $apiLogData['operate_type'],
            'order_id' => $apiLogData['order_id'],
            'order_code' => $apiLogData['order_code'],
            'remark' => $apiLogData['remark'],
            'node' => AccountFlow::BUSINESS_NODE_GAME_EXCHANGE,
            'transaction_type' => $apiLogData['transaction_type'],
            'flag' => 0,
            'date' => date('Y-m-d', time()),
            'create_time' => time(),
            'week' => date('W', time()),
            'week_day' => date('N', time()),
            'hour' => date('G', time()),
            'ratio' => '0',
        );

        Yii::app()->db->createCommand()->insert($flow_table_name, $debit);

        //暂收账户加钱
        AccountBalance::calculate(array('today_amount' => $apiLogData['money']), $balanceCommon['id']);
        //贷方（暂收账户）
        $credit = array(
            'account_id' => $balanceCommon['account_id'],
            'sku_number' => $balanceCommon['sku_number'],
            'type' => $balanceCommon['type'],
            'current_balance' => $balanceCommon['today_amount'],
            'credit_amount' => $apiLogData['money'],
            'operate_type' => $apiLogData['operate_type'],
            'order_id' => $apiLogData['order_id'],
            'order_code' => $apiLogData['order_code'],
            'remark' => $apiLogData['remark'],
            'node' => AccountFlow::BUSINESS_NODE_GAME_INCOME,
            'transaction_type' => $apiLogData['transaction_type'],
            'flag' => 0,
            'date' => date('Y-m-d', time()),
            'create_time' => time(),
            'week' => date('W', time()),
            'week_day' => date('N', time()),
            'hour' => date('G', time()),
        );
        Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
    }

    /**
     * 用户购买积分 支付逻辑
     * 
     * 比如他首次冲了五张100的，第一张按新会员，后面的按老会员
     * type
     */
    public static function guadanBuyPointPay($order_info, $rule, $remark = '用户购买第三方积分') {

        error_reporting(0);

        $money = $order_info['buy_amount'];
        $price = $order_info['total_price'];
        $member_id = $order_info['member_id'];
        $partner_member_id = $order_info['partner_member_id'];

        $flow_table_name = AccountFlow::monthTable();
        $timeStamp = time();
        //获取商家推荐者分配比例
        $ratio = Yii::app()->db->createCommand()
                ->select("distribution_ratio")
                ->from("{{guadan_partner_config}}")
                ->where("status =" . GuadanPartnerConfig::STATUS_ENABLE)
                ->queryRow();

        if (empty($ratio))
            return false;

        $ratio = bcdiv($ratio, 100, 2);



        //用户信息
        $memberInfo = Member::model()->findByPk($member_id);


        if (empty($memberInfo))
            return false;

        //商户信息
        $partnerMemberInfo = Member::model()->findByPk($partner_member_id);

        $remark .= '，售价：'.$price;

        if (empty($partnerMemberInfo))
            return false;
        //如果订单类型为空中充值商家积分商品充值的话，直接积分转赠，不写消费支付和商家收益流水
        if ($order_info['type'] != GuadanJifenOrder::TYPE_AIR_RECHARGE_PARTNER) {

            //消费支付
            $MemberArray = array(
                'account_id' => $member_id,
                'type' => AccountBalance::TYPE_CONSUME,
                'sku_number' => $memberInfo['sku_number'],
            );

            $memberBalance = AccountBalance::findRecord($MemberArray);

            AccountBalance::calculate(array('today_amount' => -$price), $memberBalance['id']);
            $debit = array(
                'account_id' => $memberBalance['account_id'],
                'sku_number' => $memberBalance['sku_number'],
                'type' => $memberBalance['type'],
                'current_balance' => $memberBalance['today_amount'],
                'debit_amount' => $price,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_PAY,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $debit);

            //商家得到收益
            $profitMoney = $price;     //计算金额

            if ($profitMoney > 0) {
                $partnerMemberArray = array(
                    'account_id' => $partnerMemberInfo['id'],
                    'type' => AccountBalance::TYPE_MERCHANT,
                    'sku_number' => $partnerMemberInfo['sku_number'],
                );
                $partnerMemberBalance = AccountBalance::findRecord($partnerMemberArray);

                AccountBalance::calculate(array('today_amount' => $profitMoney), $partnerMemberBalance['id']);
                $credit = array(
                    'account_id' => $partnerMemberBalance['account_id'],
                    'sku_number' => $partnerMemberBalance['sku_number'],
                    'type' => $partnerMemberBalance['type'],
                    'current_balance' => $partnerMemberBalance['today_amount'],
                    'credit_amount' => $profitMoney,
                    'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN,
                    'order_id' => $order_info['id'],
                    'order_code' => $order_info['code'],
                    'remark' => $remark,
                    'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_PARTNER_PROFIT,
                    'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                    'flag' => 0,
                    'date' => date('Y-m-d', $timeStamp),
                    'create_time' => $timeStamp,
                    'week' => date('W', $timeStamp),
                    'week_day' => date('N', $timeStamp),
                    'hour' => date('G', $timeStamp),
                );
                Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
            }
        }




        //商家购买的挂单积分转出
        $partnerOutMoney = $rule['amount'] * $order_info['quantity']*1;

        if ($partnerOutMoney > 0) {
            $partnerMemberArray = array(
                'account_id' => $partnerMemberInfo['id'],
                'type' => AccountBalance::TYPE_GUADAN_SHANGJIA,
                'sku_number' => $partnerMemberInfo['sku_number'],
            );
            $partnerMemberBalance = AccountBalance::findRecord($partnerMemberArray);

            AccountBalance::calculate(array('today_amount' => -$partnerOutMoney), $partnerMemberBalance['id']);

            $debit = array(
                'account_id' => $partnerMemberBalance['account_id'],
                'sku_number' => $partnerMemberBalance['sku_number'],
                'type' => $partnerMemberBalance['type'],
                'current_balance' => $partnerMemberBalance['today_amount'],
                'debit_amount' => $partnerOutMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_PARTNER_AMOUNT_OUT,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $debit);
        }


        //SK成本支出  会员赠送积分
        $costOutMoney = $rule['amount_give'] * $order_info['quantity'];
        if ($costOutMoney > 0) {
            $costOutBalance = CommonAccount::getAccount(CommonAccount::TYPE_GUADAN_COST_PAY, AccountBalance::TYPE_TOTAL);
            AccountBalance::calculate(array('today_amount' => -$costOutMoney), $costOutBalance['id']);
            $debit = array(
                'account_id' => $costOutBalance['account_id'],
                'sku_number' => $costOutBalance['sku_number'],
                'type' => $costOutBalance['type'],
                'current_balance' => $costOutBalance['today_amount'],
                'debit_amount' => $costOutMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_GIVE_COST,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $debit);
        }


        //消费者积分转入   分期执行  先转入第一期本机+赠送金额，其他入库 待脚本执行
        $memberInMoney = 0;   //会员首期获得金额
        if (!empty($rule)) {
//             $memberInMoney = bcadd(bcmul(bcdiv($rule['amount'],$rule['amount_installment'],5),$order_info['quantity'],5),bcmul(bcdiv($rule['amount_give'],$rule['give_installment'],5),$order_info['quantity'],5),2)*1;
           $memberInMoney = floor((($rule['amount'] / $rule['amount_installment']) + ($rule['amount_give'] / $rule['give_installment'])) * $order_info['quantity'] * 100) / 100;
        }
        if ($memberInMoney > 0) {

            $memberInArray = array(
                'account_id' => $member_id,
                'type' => AccountBalance::TYPE_CONSUME,
                'sku_number' => $memberInfo['sku_number'],
            );
            $memberInBalance = AccountBalance::findRecord($memberInArray);

            AccountBalance::calculate(array('today_amount' => $memberInMoney), $memberInBalance['id']);
            $credit = array(
                'account_id' => $memberInBalance['account_id'],
                'sku_number' => $memberInBalance['sku_number'],
                'type' => $memberInBalance['type'],
                'current_balance' => $memberInBalance['today_amount'],
                'credit_amount' => $memberInMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_MEMBER_AMOUNT_IN,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
        }


        //消费者待返还积分转入			 获取赠送积分   剩下期数的金额进入待返还
//         $memberToReturnMoney = bcsub(bcmul(bcadd($rule['amount'],$rule['amount_give'],5),$order_info['quantity'],5),$memberInMoney,2)*1;
//        $memberToReturnMoney = floor((($rule['amount'] + $rule['amount_give']) * $order_info['quantity'] - $memberInMoney) * 100) / 100;
       $memberToReturnMoney = floor((($rule['amount'] / $rule['amount_installment'])*($rule['amount_installment']-1) + ($rule['amount_give'] / $rule['give_installment'])*($rule['give_installment']-1)) * $order_info['quantity'] * 100) / 100;
        
        if ($memberToReturnMoney > 0) {
            $memberToReturnArray = array(
                'account_id' => $member_id,
                'type' => AccountBalance::TYPE_RETURN,
                'sku_number' => $memberInfo['sku_number'],
            );
            $memberToReturnBalance = AccountBalance::findRecord($memberToReturnArray);

            AccountBalance::calculate(array('today_amount' => $memberToReturnMoney), $memberToReturnBalance['id']);
            $credit = array(
                'account_id' => $memberToReturnBalance['account_id'],
                'sku_number' => $memberToReturnBalance['sku_number'],
                'type' => $memberToReturnBalance['type'],
                'current_balance' => $memberToReturnBalance['today_amount'],
                'credit_amount' => $memberToReturnMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_MEMBER_TO_RETURN_IN,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
        }



        //会员待分配    推荐者收益
        $guadan_config = Yii::app()->db->createCommand()
                ->select('t.distribution_ratio')
                ->from(GuadanCollect::model()->tableName() . ' as t')
                ->leftJoin(Guadan::model()->tableName() . ' as g', 't.code=g.code')
                ->where('t.status=' . GuadanCollect::STATUS_ENABLE . ' AND g.member_id=:member_id', array(':member_id' => $partner_member_id))
                ->queryRow();

        $toDistRadio = !empty($guadan_config) ? $guadan_config['distribution_ratio'] / 100 : 0;
//        $toDistMoney = floor(($price * $toDistRadio) * 100) / 100;
        $toDistMoney = bcmul($price,$toDistRadio,2)*1;

        if ($toDistMoney > 0) {

            $toDistArray = array(
                'account_id' => $member_id,
                'type' => AccountBalance::TYPE_GUADAN_DAIFENPEI_XIAOFEI,
                'sku_number' => $memberInfo['sku_number'],
            );
            $toDistBalance = AccountBalance::findRecord($toDistArray);

            AccountBalance::calculate(array('today_amount' => $toDistMoney), $toDistBalance['id']);
            $credit = array(
                'account_id' => $toDistBalance['account_id'],
                'sku_number' => $toDistBalance['sku_number'],
                'type' => $toDistBalance['type'],
                'current_balance' => $toDistBalance['today_amount'],
                'credit_amount' => $toDistMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_MEMBER_TO_DISTRIBUTION,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );

            Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
        }


        //SK成本支出			//会员推荐人
        $costOutMoney = $toDistMoney;
        if ($costOutMoney > 0) {
            $costOutBalance = CommonAccount::getAccount(CommonAccount::TYPE_GUADAN_COST_PAY, AccountBalance::TYPE_TOTAL);
            AccountBalance::calculate(array('today_amount' => -$costOutMoney), $costOutBalance['id']);
            $debit = array(
                'account_id' => $costOutBalance['account_id'],
                'sku_number' => $costOutBalance['sku_number'],
                'type' => $costOutBalance['type'],
                'current_balance' => $costOutBalance['today_amount'],
                'debit_amount' => $costOutMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_XIAOFEI_DAIFENPEI_COST,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $debit);
        }




        //商家待分配
        $partner_config = Yii::app()->db->createCommand()
                ->select('distribution_ratio')
                ->from('{{guadan_partner_config}}')
                ->where('status=' . GuadanPartnerConfig::STATUS_ENABLE)
                ->queryRow();

        $toDistRadio = !empty($partner_config) ? $partner_config['distribution_ratio'] / 100 : 0;
        $toDistMoney = $price * $toDistRadio;
        if ($toDistMoney > 0) {

            $toDistArray = array(
                'account_id' => $member_id,
                'type' => AccountBalance::TYPE_GUADAN_DAIFENPEI_SHANGJIA,
                'sku_number' => $memberInfo['sku_number'],
            );
            $toDistBalance = AccountBalance::findRecord($toDistArray);

            AccountBalance::calculate(array('today_amount' => $toDistMoney), $toDistBalance['id']);
            $credit = array(
                'account_id' => $toDistBalance['account_id'],
                'sku_number' => $toDistBalance['sku_number'],
                'type' => $toDistBalance['type'],
                'current_balance' => $toDistBalance['today_amount'],
                'credit_amount' => $toDistMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_PIFA_PARTNER_TO_DISTRIBUTION,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
        }



        //SK成本支出  商家推荐者收益
        $costOutMoney = $toDistMoney;
        if ($costOutMoney > 0) {
            $costOutBalance = CommonAccount::getAccount(CommonAccount::TYPE_GUADAN_COST_PAY, AccountBalance::TYPE_TOTAL);
            AccountBalance::calculate(array('today_amount' => -$costOutMoney), $costOutBalance['id']);
            $debit = array(
                'account_id' => $costOutBalance['account_id'],
                'sku_number' => $costOutBalance['sku_number'],
                'type' => $costOutBalance['type'],
                'current_balance' => $costOutBalance['today_amount'],
                'debit_amount' => $costOutMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_SHANGJIA_DAIFEIPEI_COST,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $debit);
        }



        return true;
    }

    /**
     * 用户购买官方积分 支付逻辑
     *
     *
     */
    public static function guadanBuyOfficalPointPay($order_info, $rule,$type = CommonAccount::TYPE_GUADAN_SALE_UNBUNDLING, $remark = '用户购买官方积分') {
        error_reporting(0);

        $money = $order_info['buy_amount'];
        $price = $order_info['total_price'];
        $member_id = $order_info['member_id'];
        $partner_member_id = $order_info['partner_member_id'];

        $flow_table_name = AccountFlow::monthTable();
        $timeStamp = time();
        //获取商家推荐者分配比例
        $ratio = Yii::app()->db->createCommand()
                ->select("distribution_ratio")
                ->from("{{guadan_partner_config}}")
                ->where("status =" . GuadanPartnerConfig::STATUS_ENABLE)
                ->queryRow();
        if (empty($ratio))
            return false;
        $ratio = $ratio["distribution_ratio"];
        $ratio = $ratio / 100;
        //用户信息
        $memberInfo = Member::model()->findByPk($member_id);
        $remark .= '，售价：'.$price; 

        if (empty($memberInfo['id']))
            return false;

        //消费支付
        $MemberArray = array(
            'account_id' => $member_id,
            'type' => AccountBalance::TYPE_CONSUME,
            'sku_number' => $memberInfo['sku_number'],
        );

        $memberBalance = AccountBalance::findRecord($MemberArray);


        AccountBalance::calculate(array('today_amount' => -$price), $memberBalance['id']);

        $debit = array(
            'account_id' => $memberBalance['account_id'],
            'sku_number' => $memberBalance['sku_number'],
            'type' => $memberBalance['type'],
            'current_balance' => $memberBalance['today_amount'],
            'debit_amount' => $price,
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_OFFICAL_JIFEN,
            'order_id' => $order_info['id'],
            'order_code' => $order_info['code'],
            'remark' => $remark,
            'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_PAY,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
            'flag' => 0,
            'date' => date('Y-m-d', $timeStamp),
            'create_time' => $timeStamp,
            'week' => date('W', $timeStamp),
            'week_day' => date('N', $timeStamp),
            'hour' => date('G', $timeStamp),
        );
        Yii::app()->db->createCommand()->insert($flow_table_name, $debit);


        //盖网得到收益
        //利润   利润就是原金额
        $profitMoney = $price;
        if ($profitMoney > 0) {
            $gaiBalance = CommonAccount::getAccount(CommonAccount::TYPE_GAI_INCOME, AccountBalance::TYPE_COMMON);
            AccountBalance::calculate(array('today_amount' => $profitMoney), $gaiBalance['id']);
            $credit = array(
                'account_id' => $gaiBalance['account_id'],
                'sku_number' => $gaiBalance['sku_number'],
                'type' => $gaiBalance['type'],
                'current_balance' => $gaiBalance['today_amount'],
                'credit_amount' => $profitMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_OFFICAL_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_GAI_PROFIT,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
       
        }



        //售卖挂单积分池转出
        $poolOutMoney = $order_info['quantity']*$rule['amount'];
        if ($poolOutMoney > 0) {
            $poolBalance = CommonAccount::getAccount($type, AccountBalance::TYPE_TOTAL);
            AccountBalance::calculate(array('today_amount' => -$poolOutMoney), $poolBalance['id']);
            $debit = array(
                'account_id' => $poolBalance['account_id'],
                'sku_number' => $poolBalance['sku_number'],
                'type' => $poolBalance['type'],
                'current_balance' => $poolBalance['today_amount'],
                'debit_amount' => $poolOutMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_OFFICAL_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_GAI_AMOUNT_OUT,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $debit);
        }


        //消费者积分转入   分期执行  先转入第一期本机+赠送金额，其他入库 待脚本执行
        $memberInMoney = 0;   //会员首期获得金额
        if (!empty($rule)) {
//             $memberInMoney = bcadd(bcmul(bcdiv($rule['amount'],$rule['amount_installment'],5),$order_info['quantity'],5),bcmul(bcdiv($rule['amount_give'],$rule['give_installment'],5),$order_info['quantity'],5),2)*1;
           $memberInMoney = floor((($rule['amount'] / $rule['amount_installment']) + ($rule['amount_give'] / $rule['give_installment'])) * $order_info['quantity'] * 100) / 100;
        }

        if ($memberInMoney > 0) {

            $memberInArray = array(
                'account_id' => $member_id,
                'type' => AccountBalance::TYPE_CONSUME,
                'sku_number' => $memberInfo['sku_number'],
            );
            $memberInBalance = AccountBalance::findRecord($memberInArray);

            AccountBalance::calculate(array('today_amount' => $memberInMoney), $memberInBalance['id']);
            $credit = array(
                'account_id' => $memberInBalance['account_id'],
                'sku_number' => $memberInBalance['sku_number'],
                'type' => $memberInBalance['type'],
                'current_balance' => $memberInBalance['today_amount'],
                'credit_amount' => $memberInMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_OFFICAL_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_MEMBER_AMOUNT_IN,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
        }


        //消费者待返还积分转入			 获取赠送积分   剩下期数的金额进入待返还
//        $memberToReturnMoney = floor((($rule['amount'] + $rule['amount_give']) * $order_info['quantity'] - $memberInMoney) * 100) / 100;
//         $memberToReturnMoney = bcsub(bcmul(bcadd($rule['amount'],$rule['amount_give'],5),$order_info['quantity'],5),$memberInMoney,2)*1;

       $memberToReturnMoney = floor((($rule['amount'] / $rule['amount_installment'])*($rule['amount_installment']-1) + ($rule['amount_give'] / $rule['give_installment'])*($rule['give_installment']-1)) * $order_info['quantity'] * 100) / 100;
        

        if ($memberToReturnMoney > 0) {
            $memberToReturnArray = array(
                'account_id' => $member_id,
                'type' => AccountBalance::TYPE_RETURN,
                'sku_number' => $memberInfo['sku_number'],
            );
            $memberToReturnBalance = AccountBalance::findRecord($memberToReturnArray);

            AccountBalance::calculate(array('today_amount' => $memberToReturnMoney), $memberToReturnBalance['id']);
            $credit = array(
                'account_id' => $memberToReturnBalance['account_id'],
                'sku_number' => $memberToReturnBalance['sku_number'],
                'type' => $memberToReturnBalance['type'],
                'current_balance' => $memberToReturnBalance['today_amount'],
                'credit_amount' => $memberToReturnMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_OFFICAL_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_MEMBER_TO_RETURN_IN,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
        }


        //SK成本支出
        $costOutMoney = $rule['amount_give'] * $order_info['quantity'];
        if ($costOutMoney > 0) {
            $costOutBalance = CommonAccount::getAccount(CommonAccount::TYPE_GUADAN_COST_PAY, AccountBalance::TYPE_TOTAL);
            AccountBalance::calculate(array('today_amount' => -$costOutMoney), $costOutBalance['id']);
            $debit = array(
                'account_id' => $costOutBalance['account_id'],
                'sku_number' => $costOutBalance['sku_number'],
                'type' => $costOutBalance['type'],
                'current_balance' => $costOutBalance['today_amount'],
                'debit_amount' => $costOutMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_OFFICAL_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_POINT_COST,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $debit);
        }

        //会员待分配    会员推荐者收益
        $guadan_config = Yii::app()->db->createCommand()
                ->select('t.distribution_ratio')
                ->from(GuadanCollect::model()->tableName() . ' as t')
                ->leftJoin(Guadan::model()->tableName() . ' as g', 't.code=g.code')
                ->where('t.status=' . GuadanCollect::STATUS_ENABLE . ' AND g.member_id=:member_id', array(':member_id' => $partner_member_id))
                ->queryRow();

        $toDistRadio = !empty($guadan_config) ? $guadan_config['distribution_ratio'] / 100 : 0;
//        $toDistMoney = floor(($price * $toDistRadio) * 100) / 100;
        $toDistMoney = bcmul($price,$toDistRadio,2)*1;
        if ($toDistMoney > 0) {
            $toDistArray = array(
                'account_id' => $member_id,
                'type' => AccountBalance::TYPE_GUADAN_DAIFENPEI_XIAOFEI,
                'sku_number' => $memberInfo['sku_number'],
            );
            $toDistBalance = AccountBalance::findRecord($toDistArray);

            AccountBalance::calculate(array('today_amount' => $toDistMoney), $toDistBalance['id']);
            $credit = array(
                'account_id' => $toDistBalance['account_id'],
                'sku_number' => $toDistBalance['sku_number'],
                'type' => $toDistBalance['type'],
                'current_balance' => $toDistBalance['today_amount'],
                'credit_amount' => $toDistMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_OFFICAL_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_POINT_MEMBER_TO_DISTRIBUTION,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
        }



        //SK成本支出			会员待分配 支出
        $costOutMoney = $toDistMoney;
        if ($costOutMoney > 0) {
            $costOutBalance = CommonAccount::getAccount(CommonAccount::TYPE_GUADAN_COST_PAY, AccountBalance::TYPE_TOTAL);
            AccountBalance::calculate(array('today_amount' => -$costOutMoney), $costOutBalance['id']);
            $debit = array(
                'account_id' => $costOutBalance['account_id'],
                'sku_number' => $costOutBalance['sku_number'],
                'type' => $costOutBalance['type'],
                'current_balance' => $costOutBalance['today_amount'],
                'debit_amount' => $costOutMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_MEMBER_BUY_OFFICAL_JIFEN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_BUY_OFFICAL_XIAOFEI_DAIFENPEI_POINT_COST,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $debit);
        }


        return true;
    }

    /**
     * 挂单导入流水处理
     * @param array $guadan_info 导入数据
     * @param array $types 类型总汇  包括类型有 账号类型(type),交易类型(operate_type),交易节点(node),事物类型(transaction_type)
     * @param string $table 流水表
     * @param Boolean  $is_debit 是否是转出
     * @param Boolean $is_common 是否是公共账户
     * @return Boolean 
     */
    public static function ImportGuaDan($guadan_info, $types, $account_type, $table, $is_debit = false, $is_common = false) {
        if (is_array($guadan_info) && is_array($types) && is_string($table)) {
            if ($is_common) {
                $balance = CommonAccount::getAccount($account_type, AccountInfo::TYPE_COMMON, '挂单成本支出账户');  // 取得公共账户 
                if (!$balance)
                    throw new CException('获取或者创建公共账户失败');
            } else {
                //此处获取用户账号
                $member = Member::getByGwNumber($guadan_info['gai_number']);
               
                if (!$member)
                    return false; //用户不存在返回false
                $balance = AccountBalance::findRecord(array('account_id' => $member->id, 'type' => AccountBalance::TYPE_GUADAN_XIAOFEI, 'sku_number' => $member->sku_number));
            }
            //账户余额
            $amount = $balance['today_amount'] * 1;
            //创建流水
            $current_balance = $is_debit ? bcsub($amount, $guadan_info['amount'], 2) : bcadd($amount, $guadan_info['amount'], 2);
            $flow = array(
                $balance['account_id'], //account_id
                $balance['sku_number'], // sku_number
                date('Y-m-d'), // date
                time(), // time
                $types['type'], //type
                $guadan_info['amount'], // debit_amount or credit_amount
                $types['operate_type'], //交易类型operate_type
                $guadan_info['order_id'], //order_id
                $guadan_info['order_code'], //order_code
                '批量挂单导入', // remark
                date('W'), //week
                date('w'), //week_day
                date('h'), //hour
                $types['node'], // node
                $types['transaction_type'], // transaction_type
                $current_balance, //current_balance 
            );
            $values = '"' . implode('","', $flow) . '"';
            if ($is_debit) {
                $sql = "INSERT INTO {$table} (`account_id`,`sku_number`,`date`,`create_time`,`type`,`debit_amount`,`operate_type`,`order_id`,`order_code`,`remark`,`week`,`week_day`,`hour`,`node`,`transaction_type`,`current_balance`) VALUES ({$values})";
                $result = self::calculate(array('today_amount' => -($guadan_info['amount'])), $balance['id']);
            } else {
                $sql = "INSERT INTO {$table} (`account_id`,`sku_number`,`date`,`create_time`,`type`,`credit_amount`,`operate_type`,`order_id`,`order_code`,`remark`,`week`,`week_day`,`hour`,`node`,`transaction_type`,`current_balance`) VALUES ({$values})";
                $result = self::calculate(array('today_amount' => $guadan_info['amount']), $balance['id']);
            }
            $result_flow = Yii::app()->db->createCommand($sql)->execute();
            return $result && $result_flow;
        }
        return false;
    }

    /**
     * 用户获得返还积分逻辑
     *
     */
    public static function guadanReturnInstallmentAmount($member_id, $amount, $order_info, $remark = '用户购买积分定期返还') {
        error_reporting(0);

        $flow_table_name = AccountFlow::monthTable();
        $timeStamp = time();

        //用户信息
        $memberInfo = Member::model()->findByPk($member_id);


        if (empty($memberInfo['id']))
            return false;

        //消费者待返还积分转出
        $MemberArray = array(
            'account_id' => $member_id,
            'type' => AccountBalance::TYPE_RETURN,
            'sku_number' => $memberInfo['sku_number'],
        );

        $memberBalance = AccountBalance::findRecord($MemberArray);
		

        AccountBalance::calculate(array('today_amount' => -$amount), $memberBalance['id']);
        
        $debit = array(
            'account_id' => $memberBalance['account_id'],
            'sku_number' => $memberBalance['sku_number'],
            'type' => $memberBalance['type'],
            'current_balance' => $memberBalance['today_amount'],
            'debit_amount' => $amount,
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN__MEMBER_RETURN,
            'order_id' => $order_info['id'],
            'order_code' => $order_info['code'],
            'remark' => $remark,
            'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_RETURN_OUT,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
            'flag' => 0,
            'date' => date('Y-m-d', $timeStamp),
            'create_time' => $timeStamp,
            'week' => date('W', $timeStamp),
            'week_day' => date('N', $timeStamp),
            'hour' => date('G', $timeStamp),
        );
        Yii::app()->db->createCommand()->insert($flow_table_name, $debit);


        //消费者积分转入 
        $memberInMoney = $amount;   //会员首期获得金额
        if ($memberInMoney > 0) {

            $memberInArray = array(
                'account_id' => $member_id,
                'type' => AccountBalance::TYPE_CONSUME,
                'sku_number' => $memberInfo['sku_number'],
            );
            $memberInBalance = AccountBalance::findRecord($memberInArray);

            AccountBalance::calculate(array('today_amount' => $memberInMoney), $memberInBalance['id']);
            $credit = array(
                'account_id' => $memberInBalance['account_id'],
                'sku_number' => $memberInBalance['sku_number'],
                'type' => $memberInBalance['type'],
                'current_balance' => $memberInBalance['today_amount'],
                'credit_amount' => $memberInMoney,
                'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN__MEMBER_RETURN,
                'order_id' => $order_info['id'],
                'order_code' => $order_info['code'],
                'remark' => $remark,
                'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_MEMBER_RETURN_IN,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                'flag' => 0,
                'date' => date('Y-m-d', $timeStamp),
                'create_time' => $timeStamp,
                'week' => date('W', $timeStamp),
                'week_day' => date('N', $timeStamp),
                'hour' => date('G', $timeStamp),
            );
            Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
        }

        return true;
    }

    /*
     * 积分奖励
     */

    public static function JiFenBalance($memberId, $addmoney, $order) {
//        $transaction=Yii::app()->db->beginTransaction();
//        try{
        $account_flow_table = AccountFlow::monthTable();
        $PublicAcc = CommonAccount::getAccount(CommonAccount::TYPE_GUADAN_COST_PAY,  AccountBalance::TYPE_COMMON);
        $timeStamp = time();
        $sku_member = Member::model()->find('gai_member_id=:gid', array(':gid' => $memberId));
        //账户扣钱
        $result = self::calculate(array('today_amount' => -$addmoney), $PublicAcc['id']);
        if (!$result) {
            throw new Exception("扣除总账户金额失败");
        }
        $debit = array(
            'account_id' => $PublicAcc['account_id'],
            'sku_number' => $PublicAcc['sku_number'],
            'type' => $PublicAcc['type'],
            'current_balance' => $PublicAcc['today_amount'],
            'debit_amount' => $addmoney,
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_INPUT_GOODS_JIFEN,
            'order_id' => '',
            'order_code' => $order,
            'remark' => '商品录入奖励',
            'node' => AccountFlow::BUSINESS_NODE_SKU_INPUT_GOODS_TOTAL_JINFEN_IN,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
            'flag' => 0,
            'date' => date('Y-m-d', $timeStamp),
            'create_time' => $timeStamp,
            'week' => date('W', $timeStamp),
            'week_day' => date('N', $timeStamp),
            'hour' => date('G', $timeStamp),
        );
        Yii::app()->db->createCommand()->insert($account_flow_table, $debit);

        //获取会员账户信息
        $memberBalance = self::findRecord(array(
                    'account_id' => $sku_member->id,
                    'type' => AccountBalance::TYPE_CONSUME,
                    'sku_number' => $sku_member->sku_number
        ));
        $member_result = self::calculate(array('today_amount' => $addmoney), $memberBalance['id']);
        if (!$member_result) {
            throw new Exception("增会员奖励金额失败");
        }
        $credit = array(
            'account_id' => $memberBalance['account_id'],
            'sku_number' => $memberBalance['sku_number'],
            'type' => $memberBalance['type'],
            'current_balance' => $memberBalance['today_amount'],
            'credit_amount' => $addmoney,
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_INPUT_GOODS_JIFEN,
            'order_id' => '',
            'order_code' => $order,
            'remark' => '商品录入奖励',
            'node' => AccountFlow::BUSINESS_NODE_SKU_INPUT_GOODS_JINFEN_IN,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
            'flag' => 0,
            'date' => date('Y-m-d', $timeStamp),
            'create_time' => $timeStamp,
            'week' => date('W', $timeStamp),
            'week_day' => date('N', $timeStamp),
            'hour' => date('G', $timeStamp),
        );
        Yii::app()->db->createCommand()->insert($account_flow_table, $credit);
//          $transaction->commit();
//          return true;
//        }  catch (Exception $e){
//             $transaction->rollBack();
//              throw new Exception($e.'(积分奖励失败)');
//              return false;
//        }
    }
    
    
    /**
     * 商家申请提现
     * @param $apiLogData
     *
     * public static function sign($apiLogData, $costPrice, $merchantMemberId, $merchantSkuNumber)
     *
     */
    public static function applyCash($money, $member_id,$record_id=0,$remark = '申请提现') {
    
    	error_reporting(0);
    	$flow_table_name = AccountFlow::monthTable();
    	$timeStamp = time();
    	$money = abs($money)*1;
    
    	$memberInfo = Member::model()->findByPk($member_id);
    	if (empty($memberInfo))
    		return false;
    
    	//现金账户扣除
    	$MemberArray = array(
    			'account_id' => $member_id,
    			'type' => AccountBalance::TYPE_MERCHANT,
    			'sku_number' => $memberInfo['sku_number'],
    	);
    	$memberBalance = AccountBalance::findRecord($MemberArray);
    
    	AccountBalance::calculate(array('today_amount' => -$money), $memberBalance['id']);
    	$debit = array(
    			'account_id' => $memberBalance['account_id'],
    			'sku_number' => $memberBalance['sku_number'],
    			'type' => $memberBalance['type'],
    			'current_balance' => $memberBalance['today_amount'],
    			'debit_amount' => $money,
    			'operate_type' => AccountFlow::OPERATE_TYPE_CASH_APPLY,
    			'order_id' => $record_id,
    			'order_code' => $record_id,
    			'remark' => $remark,
    			'node' => AccountFlow::BUSINESS_NODE_CASH_APPLY,
    			'transaction_type' => AccountFlow::TRANSACTION_TYPE_CASH,
    			'flag' => 0,
    			'date' => date('Y-m-d', $timeStamp),
    			'create_time' => $timeStamp,
    			'week' => date('W', $timeStamp),
    			'week_day' => date('N', $timeStamp),
    			'hour' => date('G', $timeStamp),
    	);
    	Yii::app()->db->createCommand()->insert($flow_table_name, $debit);

    
    	//商家购买的挂单积分转入
    		$frozenArray = array(
    				'account_id' => $member_id,
    				'type' => AccountBalance::TYPE_FREEZE,
    				'sku_number' => $memberInfo['sku_number'],
    		);
    		$fronzenBalance = AccountBalance::findRecord($frozenArray);
    
    		AccountBalance::calculate(array('today_amount' => $money), $fronzenBalance['id']);
    		$credit = array(
    				'account_id' => $fronzenBalance['account_id'],
    				'sku_number' => $fronzenBalance['sku_number'],
    				'type' => $fronzenBalance['type'],
    				'current_balance' => $fronzenBalance['today_amount'],
    				'credit_amount' => $money,
    				'operate_type' => AccountFlow::OPERATE_TYPE_CASH_APPLY,
    				'order_id' => $record_id,
    				'order_code' => $record_id,
    				'remark' => $remark,
    				'node' => AccountFlow::BUSINESS_NODE_CASH_APPLY,
    				'transaction_type' => AccountFlow::TRANSACTION_TYPE_CASH,
    				'flag' => 0,
    				'date' => date('Y-m-d', $timeStamp),
    				'create_time' => $timeStamp,
    				'week' => date('W', $timeStamp),
    				'week_day' => date('N', $timeStamp),
    				'hour' => date('G', $timeStamp),
    		);
    		Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
    	

    	return true;
    }
    
    
    /**
     * 商家申请提现  通过
     * @param $apiLogData
     *
     * public static function sign($apiLogData, $costPrice, $merchantMemberId, $merchantSkuNumber)
     *
     */
    public static function applyCashPass($money, $member_id,$record_id=0,$remark = '商家申请提现通过') {
    
    	error_reporting(0);
    	$flow_table_name = AccountFlow::monthTable();
    	$timeStamp = time();
    	$money = abs($money)*1;
    
    	$memberInfo = Member::model()->findByPk($member_id);
    	if (empty($memberInfo))
    		return false;
    
    	//冻结账户扣除
    	$MemberArray = array(
    			'account_id' => $member_id,
    			'type' => AccountBalance::TYPE_FREEZE,
    			'sku_number' => $memberInfo['sku_number'],
    	);
    	$memberBalance = AccountBalance::findRecord($MemberArray);
    
    	AccountBalance::calculate(array('today_amount' => -$money), $memberBalance['id']);
    	$debit = array(
    			'account_id' => $memberBalance['account_id'],
    			'sku_number' => $memberBalance['sku_number'],
    			'type' => $memberBalance['type'],
    			'current_balance' => $memberBalance['today_amount'],
    			'debit_amount' => $money,
    			'operate_type' => AccountFlow::OPERATE_TYPE_CASH_SUCCESS,
    			'order_id' => $record_id,
    			'order_code' => $record_id,
    			'remark' => $remark,
    			'node' => AccountFlow::BUSINESS_NODE_CASH_CONFIRM,
    			'transaction_type' => AccountFlow::TRANSACTION_TYPE_CASH,
    			'flag' => 0,
    			'date' => date('Y-m-d', $timeStamp),
    			'create_time' => $timeStamp,
    			'week' => date('W', $timeStamp),
    			'week_day' => date('N', $timeStamp),
    			'hour' => date('G', $timeStamp),
    	);
    	Yii::app()->db->createCommand()->insert($flow_table_name, $debit);
    
    	return true;
    }
    
    /**
     * 商家申请提现  不通过  返还现金账户  相当于取消提现
     * @param $apiLogData
     *
     * public static function sign($apiLogData, $costPrice, $merchantMemberId, $merchantSkuNumber)
     *
     */
    public static function applyCashUnPass($money, $member_id,$record_id=0,$remark = '商家申请提现通过') {
    
    	error_reporting(0);
    	$flow_table_name = AccountFlow::monthTable();
    	$timeStamp = time();
    	$money = abs($money)*1;
    
    	$memberInfo = Member::model()->findByPk($member_id);
    	if (empty($memberInfo))
    		return false;
    
    	//冻结账户扣除
    	$MemberArray = array(
    			'account_id' => $member_id,
    			'type' => AccountBalance::TYPE_MERCHANT,
    			'sku_number' => $memberInfo['sku_number'],
    	);
    	$memberBalance = AccountBalance::findRecord($MemberArray);
    
    		AccountBalance::calculate(array('today_amount' => $money), $memberBalance['id']);
    		$credit = array(
    				'account_id' => $memberBalance['account_id'],
    				'sku_number' => $memberBalance['sku_number'],
    				'type' => $memberBalance['type'],
    				'current_balance' => $memberBalance['today_amount'],
    				'credit_amount' => $money,
    				'operate_type' => AccountFlow::OPERATE_TYPE_CASH_CANCEL,
    				'order_id' => $record_id,
    				'order_code' => $record_id,
    				'remark' => $remark,
    				'node' => AccountFlow::BUSINESS_NODE_CASH_CANCEL,
    				'transaction_type' => AccountFlow::TRANSACTION_TYPE_CASH_CANCEL,
    				'flag' => 0,
    				'date' => date('Y-m-d', $timeStamp),
    				'create_time' => $timeStamp,
    				'week' => date('W', $timeStamp),
    				'week_day' => date('N', $timeStamp),
    				'hour' => date('G', $timeStamp),
    		);
    		Yii::app()->db->createCommand()->insert($flow_table_name, $credit);
    
    	return true;
    }
    
}
