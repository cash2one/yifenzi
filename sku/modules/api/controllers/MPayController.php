<?php

/**
 * 售货机
 * @author hao.liang
 * 注意：父辈VController 已经对应shopId在保护变量vending获取了售货机信息 不用再获取一次
 */
class MPayController extends VMAPIController {

    protected $encryptField;

//     protected $prefix = 'VD';//检查验证码前缀
//     protected $cache = 'fileCache';//使用什么缓存

    /**
     * 获取预消费订单
     * shopId 售货机编码 加密必填
     * cityID 选填
     * districtID 选填
     * provinceID 必填
     * orderID 订单号，加密必填
     */
    public function actionGetPrepayOrder() {
        try {
            if ($this->getParam('onlyTest') == 1) {
                $post = $this->getParams();
            } else {
                $this->params = array('shopId', 'cityID', 'districtID', 'provinceID', 'orderID');
                $requiredFields = array('shopId', 'provinceID', 'orderID');
                $decryptFields = array('shopId', 'orderID');
                $post = $this->decrypt($_POST, $requiredFields, $decryptFields);
            }


            if ($this->vending['status'] != VendingMachine::STATUS_ENABLE || $this->vending['is_activate'] != VendingMachine::IS_ACTIVATE_YES)
                $this->_error(Yii::t('apiModule.pay', '该售货机不是正常运行状态'));

            $orderId = 'VM' . $post['orderID']; //前缀VM 作为搜索条件
            $preOrderTable = FranchiseeConsumptionPreRecord::tableName();
            $db = Yii::app()->db;
            $preOrder = $db->createCommand()
                    ->select("id,member_id,amount,machine_id,is_processed,status,is_pay")
                    ->from($preOrderTable)
                    ->where("machine_serial_number = '{$orderId}'")
                    ->queryRow();
            //判断有没有该订单
            if (empty($preOrder) || $preOrder['is_pay'] == FranchiseeConsumptionPreRecord::IS_PAY_NO)
                $this->_error(Yii::t('apiModule.pay', '没有该订单'), -1);
            if ($preOrder['is_processed'] == FranchiseeConsumptionPreRecord::IS_PROCESSED_YES)
                $this->_success();
            //是否是同一机器销售
            if ($this->vending['id'] != $preOrder['machine_id'])
                $this->_error(Yii::t('apiModule.pay', '不是同一售货机购买'));
            //检查会员
            $member = $db->createCommand()
                    ->select("id,gai_number,status")
                    ->from(Member::tableName())
                    ->where("id = '{$preOrder['member_id']}'")
                    ->queryRow();
            if ($member['status'] > Member::STATUS_NORMAL)
                $this->_error(Yii::t('apiModule.pay', '消费者会员状态有问题,禁止出货'));
            $freezingBalanceRes = AccountBalance::findRecord(array('account_id' => $member['id'], 'type' => AccountBalance::TYPE_FREEZE, 'gai_number' => $member['gai_number']));

            if ($freezingBalanceRes['today_amount'] < $preOrder['amount'])
                $this->_error(Yii::t('apiModule.pay', '预消费订单有出错，请联系相关人员处理'));

            $sql = 'update ' . $preOrderTable . ' set is_processed = ' . FranchiseeConsumptionPreRecord::IS_PROCESSED_YES . ' , processed_time = ' . time() . ' where id = ' . $preOrder['id'];
            $db->createCommand($sql)->execute();

            $this->_success();
        } catch (Exception $e) {
            $this->_error($e->getMessage());
        }
    }

    /**
     * 获取预消费订单
     * shopId 售货机编码 加密必填
     * cityID 选填
     * districtID 选填
     * provinceID 必填
     * orderID 订单号，加密必填
     * isSuccess 
     * momey 出货成功部分的金额
     * sysbol
     */
    public function actionConfirm() {
        try {
            if ($this->getParam('onlyTest') == 1) {
                $post = $this->getParams();
            } else {
                $this->params = array('shopId', 'cityID', 'districtID', 'provinceID', 'money', 'orderID', 'symbol', 'goodsNum');
                $requiredFields = array('shopId', 'provinceID', 'money', 'orderID', 'symbol', 'goodsNum');
                $decryptFields = array('shopId', 'orderID', 'money', 'goodsNum');

                $post = $this->decrypt($_POST, $requiredFields, $decryptFields, true);
            }

            $this->addLog('vending_pay_confirm.log', '', $post);
            if ($this->vending['status'] != VendingMachine::STATUS_ENABLE || $this->vending['is_activate'] != VendingMachine::IS_ACTIVATE_YES)
                $this->_error(Yii::t('apiModule.pay', '该售货机不是正常运行状态'));

            $orderId = 'VM' . $post['orderID']; //前缀VM 作为搜索条件
            if ($post['money'] == 0 && $post['goodsNum'] == 0)
                $this->_success();
            if (!is_numeric($post['goodsNum']) && $post['goodsNum'] < 0)
                $this->_error(Yii::t('apiModule.pay', 'goodsNum是要大于等于0的数字'));
            if (!is_numeric($post['money']) && $post['money'] < 0)
                $this->_error(Yii::t('apiModule.pay', 'money是要大于等于0的数字'));

            if (IntegralOfflineNew::preConsumeConfirm($orderId, $post['money'], $post['symbol'], $this->vending, FranchiseeConsumptionRecord::RECORD_TYPE_VENDING, $post['goodsNum']))
                $this->_success();
            else
                $this->_error(Yii::t('apiModule.pay', '预消费支付失败'));
        } catch (Exception $e) {
            $this->_error($e->getMessage());
        }
    }

    /**
     * 拒绝销售的订单
     * shopId 售货机编码 加密必填
     * cityID 选填
     * districtID 选填
     * provinceID 必填
     * orderID 订单号，加密必填
     */
    public function actionForbidpay() {
        try {
            if ($this->getParam('onlyTest') == 1) {
                $post = $this->getParams();
            } else {
                $this->params = array('shopId', 'cityID', 'districtID', 'provinceID', 'orderID');
                $requiredFields = array('shopId', 'provinceID', 'orderID');
                $decryptFields = array('shopId', 'orderID');
                $post = $this->decrypt($_POST, $requiredFields, $decryptFields);
            }

            if ($this->vending['status'] != VendingMachine::STATUS_ENABLE || $this->vending['is_activate'] != VendingMachine::IS_ACTIVATE_YES)
                $this->_error(Yii::t('apiModule.pay', '该售货机不是正常运行状态'));
            $orderId = 'VM' . $post['orderID']; //前缀VM 作为搜索条件
            $cache = Yii::app()->redis;
            $key = FranchiseeConsumptionPreRecord::ForbidCacheName . '_' . $orderId;
            if ($cache->set($key, 1, 3600))
                $this->_success();
            else
                $this->_error();
        } catch (Exception $e) {
            $this->_error($e->getMessage());
        }
    }

    /**
     * 补货短信发送
     */
    public function actionSendmsg() {
        try {
            if ($this->getParam('onlyTest') == 1) {
                $post = $this->getParams();
            } else {
                $this->params = array('shopId', 'cityID', 'districtID', 'provinceID', 'goodsName', 'code', 'phoneNos');
                $requiredFields = array('shopId', 'provinceID', 'goodsName', 'code');
                $decryptFields = array('shopId', 'goodsName', 'code', 'phoneNos');
                $post = $this->decrypt($_POST, $requiredFields, $decryptFields);
            }

            if ($this->vending['status'] != VendingMachine::STATUS_ENABLE || $this->vending['is_activate'] != VendingMachine::IS_ACTIVATE_YES)
                $this->_error(Yii::t('apiModule.pay', '该售货机不是正常运行状态'));
            if (Fun::cache('vending')->get($post['code']))
                $this->_error(Yii::t('apiModule.pay', '该短信已发送'));
            if (!isset($post['phoneNos']))
                $this->_success();
            $nosArr = explode(",", $post['phoneNos']);
            foreach ($nosArr as $value) {
                if (Validator::isMobile($value)) {
                    $content = "您好，" . $this->vending['name'] . " 自动售货机里面的 “" . $post['goodsName'] . "” 已售罄，请注意补货";
                    $smsRes = SmsLog::addSmsLog($value, $content, 0, SmsLog::TYPE_VENDING_COMPLEMENT);
                }
            }
            Fun::cache('vending')->set($post['code'], 1, 43200);
            $this->_success();
        } catch (Exception $e) {
            $this->_error($e->getMessage());
        }
    }

    /**
     * 售货机 pos机刷卡支付接口
     */
    public function actionConsumebypos() {
        set_time_limit(300);

        $sql_first = "set interactive_timeout=300";
        Yii::app()->db->createCommand($sql_first)->execute();

        if ($this->getParam('onlyTest') == 1) {
            $post = $this->getParams();
        } else {
            //由于太长 所以分拆几行
            $this->params = array(
                'shopId', 'Name',
                'UserPhone', 'CardNum', 'Amount',
                'TransactionSerialNum', 'BusinessNum', 'DeviceNum',
                'ShopName', 'Operator', 'DocNum',
                'BatchNum', 'CardValidDate', 'TransactionDate', 'TransactionTime',
                'TransactionType', 'SendCardBank', 'ReceiveBank', 'ClearAccountDate', 'codeId', 'code', 'token');
            $requiredFields = array('shopId', 'CardNum', 'Amount', 'TransactionDate', 'TransactionTime', 'TransactionSerialNum',
                'BusinessNum', 'DeviceNum', 'ShopName', 'DocNum', 'BatchNum', 'CardValidDate', 'TransactionType', 'codeId', 'code');
            $decryptFields = array(
                'shopId', 'Name',
                'UserPhone', 'CardNum', 'Amount',
                'TransactionSerialNum', 'BusinessNum', 'DeviceNum',
                'ShopName', 'Operator', 'DocNum',
                'BatchNum', 'CardValidDate', 'TransactionDate', 'TransactionTime',
                'TransactionType', 'SendCardBank', 'ReceiveBank', 'ClearAccountDate', 'codeId', 'code');
            $post = $this->decrypt($_POST, $requiredFields, $decryptFields, true);
        }


//         var_dump($post);

        Yii::log('mPay/consumebypos   -   ' . serialize($post), CLogger::LEVEL_TRACE);

        if (!is_numeric($post['CardNum']))
            $this->_error('不是合法的卡号');
        $token = $this->getParam('token');
        if (!empty($token)) {
            $memberInfo = Tool::cache(self::CK_MEMBER_INFO)->get($token); //登录消费 用户信息
            if (!empty($memberInfo)) {
                $member = $memberInfo['id'];
            } else {
                $this->_error('非法参数');
            }
        } else {
            $member = $this->vending['member_id']; //无登录消费 商家信息

            $memberInfo = Member::model()->findByPk($member);
        }


        if (!is_numeric($post['Amount']) || $post['Amount'] <= 0)
            $this->_error('支付金额数要大于0');
        if (!is_numeric($post['codeId']) || $post['codeId'] <= 0)
            $this->_error('codeId要大于0');

        $status = Tool::getConfig('amountlimit', 'isEnable');
        if ($status == AmountLimitConfigForm::STATUS_ENABLE) {
            // 检查消费限额
//                    $rs = Member::checkAccountLimit($memberInfo['id'],$post['Amount'],$this->vending['machine_id'],$member );
            $machine = array();

            $machine['machine_id'] = $this->vending['id'];
            $rs = Member::checkAccountLimit($post['Amount'], $machine, $member);
            if (is_array($rs)) {
                $this->_error($rs['code']);
            }
        }
        $pos_info = Yii::app()->db->createCommand()
                ->select("id")
                ->from("{{pos_information}}")
                ->where("doc_num =" . $post['DocNum'] . " AND serial_num =" . $post['TransactionSerialNum'] . " AND batch_num = " . $post['BatchNum'] . " AND device_num =" . $post['DeviceNum'])
                ->queryRow();

        if (!empty($pos_info))
            $this->_error('已经提交过此订单，请重新下单');



        //开启事务
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $time = time();
            $pay_time = strtotime($post['TransactionDate'] . $post['TransactionTime']);
            $insertData = array(
                'member_id' => $memberInfo['id'], //会员id
                'phone' => $memberInfo['mobile'], //会员手机号码
                'card_num' => $post['CardNum'], //卡号
                'amount' => $post['Amount'], //金额
                'serial_num' => $post['TransactionSerialNum'], //流水号
                'transaction_time' => $pay_time, //交易时间
                'business_num' => $post['BusinessNum'], //商铺号
                'device_num' => $post['DeviceNum'], //终端号
                'shopname' => $post['ShopName'], //商户名称
                'operator' => isset($post['Operator']) ? $post['Operator'] : '', //操作员
                'doc_num' => $post['DocNum'], //凭证号
                'batch_num' => $post['BatchNum'], //批次号
                'card_valid_date' => strtotime($post['CardValidDate']), //卡片有限期
                'transaction_type' => $post['TransactionType'], //交易类型
                'send_card_bank' => isset($post['SendCardBank']) ? $post['SendCardBank'] : '', //发卡行
                'receive_bank' => isset($post['ReceiveBank']) ? $post['ReceiveBank'] : '', //收单行
                'clear_account_date' => isset($post['ClearAccountDate']) ? strtotime($post['ClearAccountDate']) : '', //清算日期
            );

            if (!Yii::app()->db->createCommand()->insert("{{pos_information}}", $insertData)) {
                $transaction->rollback();
                $this->_error('保存数据失败！');
            };


            //充值开始
            $apiLogData['order_id'] = $post['codeId'];
            $apiLogData['order_code'] = $post['code'];
            $apiLogData['operate_type'] = AccountFlow::OPERATE_TYPE_EBANK_RECHARGE;
            $apiLogData['transaction_type'] = AccountFlow::TRANSACTION_TYPE_RECHARGE;
            $apiLogData['remark'] = isset($post['remark']) ? $post['remark'] : 'pos机刷卡充值';
            $apiLogData['money'] = $post['Amount'];
            $apiLogData['account_id'] = $memberInfo['id'];
            $apiLogData['sku_number'] = isset($memberInfo['sku_number']) ? $memberInfo['sku_number'] : '';
            $apiLogData['gai_number'] = $memberInfo['gai_number'];
            $apiLogData['data'] = json_encode($post);
            $apiLogData['create_time'] = time();
            $apiLogData['node'] = AccountFlow::BUSINESS_NODE_EBANK_POS;
            //处理金额
            $res = AccountBalance::changeBalance($apiLogData, false);
            if ($res == false) {
                $transaction->rollback();
                $this->_error('保存数据失败！');
            }


            //充值结束
            //组装数据
            $data['order_id'] = $post['codeId'];
            $data['order_code'] = $post['code'];
            $data['operate_type'] = AccountFlow::OPERATE_TYPE_SKU_PAY;
            $data['transaction_type'] = AccountFlow::TRANSACTION_TYPE_CONSUME;
            $data['remark'] = isset($post['remark']) ? $post['remark'] : 'pos机刷卡订单支付';
            $data['money'] = $post['Amount'];
            $data['account_id'] = $memberInfo['id'];
            $data['sku_number'] = isset($memberInfo['sku_number']) ? $memberInfo['sku_number'] : '';
            $data['gai_number'] = $memberInfo['gai_number'];
            $data['data'] = json_encode($post);
            $data['create_time'] = time();
            //处理金额
            @$ab = AccountBalance::changeBalance($data, false);
            if ($ab) {
                //提交
                $transaction->commit();

                //处理订单
                $params = array('code' => $data['order_code'], 'pay_type' => 2);
                $postrs = $this->requestSku($params, 'sOrder/pay/', 105);

                if ($postrs['success']) {
                    $this->_success('订单支付成功！');
                } else {
                    Yii::log('mpay/$postrs ' . serialize($postrs), CLogger::LEVEL_ERROR);
                    $this->_error('执行充值逻辑失败' . (isset($postrs['resultDesc']) ? $postrs['resultDesc'] : '') . ' 请等待退款或联系客服 ');
                }
            } else {
                $transaction->rollback();
                $this->_error('事务失败');
            }

            $transaction->commit();
            return;
        } catch (Exception $e) {
            $transaction->rollback();
            $this->_error($e->getMessage());
        }

        $transaction->commit();
        return;
    }

    /**
     * 新机制pos消费
     * 直接生成一个完成订单，记录流水
     */
    public function actionNewConsumebypos() {
        set_time_limit(300);

        $sql_first = "set interactive_timeout=300";
        Yii::app()->db->createCommand($sql_first)->execute();

        $this->params = array(
            'shopId', 'Name', 'type', 'machineTakeType', 'remark', 'goods',
            'UserPhone', 'CardNum', 'Amount',
            'TransactionSerialNum', 'BusinessNum', 'DeviceNum',
            'ShopName', 'Operator', 'DocNum',
            'BatchNum', 'CardValidDate', 'TransactionDate', 'TransactionTime',
            'TransactionType', 'SendCardBank', 'ReceiveBank', 'ClearAccountDate', 'codeId', 'code', 'token');
        $requiredFields = array('shopId', 'type', 'goods', 'CardNum', 'Amount', 'TransactionDate', 'TransactionTime', 'TransactionSerialNum',
            'BusinessNum', 'DeviceNum', 'ShopName', 'DocNum', 'BatchNum', 'CardValidDate', 'TransactionType');
        $decryptFields = array(
            'shopId', 'Name', 'type', 'goods', 'remark', 'goods',
            'UserPhone', 'CardNum', 'Amount',
            'TransactionSerialNum', 'BusinessNum', 'DeviceNum',
            'ShopName', 'Operator', 'DocNum',
            'BatchNum', 'CardValidDate', 'TransactionDate', 'TransactionTime',
            'TransactionType', 'SendCardBank', 'ReceiveBank', 'ClearAccountDate', 'codeId', 'code');
        $post = $this->decrypt($_POST, $requiredFields, $decryptFields, true);

        Yii::log('mPay/NewConsumebypos   -   ' . serialize($post), CLogger::LEVEL_TRACE);

        if (!is_numeric($post['CardNum']))
            $this->_error('不是合法的卡号');



        $machineTakeType = isset($post['machineTakeType']) ? $post['machineTakeType'] : 1;
        $remark = isset($post['remark']) ? $post['remark'] : "";
        $goods_arr = CJSON::decode(str_replace('\"', '"', $post['goods']));   //商品列表
        $sid = $this->vending['id'];
        $type = $this->type;
        $token = isset($post['token']) ? $post['token'] : "";
        if (!empty($token)) {
            $memberInfo = Tool::cache(self::CK_MEMBER_INFO)->get($token);  //消费者登录信息
            if (!empty($memberInfo)) {
                $member = $memberInfo['id'];
                if ($member == $this->vending['member_id']) {
                    $this->_error('店家不能购买自己的商品');
                }
            } else {
                $this->_error('用户无效');
            }
        } else {
            $member = $this->vending['member_id'];
            $memberInfo = Member::model()->findByPk($member);
        }


        //         if ($type==Order::TYPE_SUPERMARK) {
        $limit_config = Tool::getConfig('amountlimit');
        if ($limit_config['isEnable']) {
            $amount_rs = $this->_getMemberToStoreAmount($sid, $member, null, $type);

            if ($amount_rs['isOver'] == true) {
                $this->_error(Yii::t('apiModule.order', '您的当日消费金额已超过每日最大限额，请明天再消费。'), ErrorCode::ORDER_OVER_MAX_AMOUNT_PREDAY_ERROR, $tag_name);
            }
        }
        $pay_time = strtotime($post['TransactionDate'] . $post['TransactionTime']);  //支付时间
        //查询订单是否已存在

        $pos_info = Yii::app()->db->createCommand()
                ->select("id,order_id")
                ->from("{{pos_information}}")
                ->where("doc_num =" . $post['DocNum'] . " AND serial_num =" . $post['TransactionSerialNum'] . " AND batch_num = " . $post['BatchNum'] . " AND device_num =" . $post['DeviceNum'] . " AND transaction_time =" . $pay_time . " AND business_num =" . $post['BusinessNum'])
                ->queryRow();
        if (!empty($pos_info) && !empty($pos_info['order_id'])) {
            $has_order = Order::model()->findByPk($pos_info['order_id']);
        } else {
            $has_order = Order::model()->find(array(
                'select' => 'id, code',
                'condition' => 'partner_id=:pid and machine_id=:machine_id and pay_time=:ptime',
                'params' => array(
                    ':pid' => $this->vending['partner_id'],
                    ':machine_id' => $this->vending['id'],
                    ':ptime' => $pay_time,
                )
            ));
        }
        //已存在返回订单编号
        if (!empty($has_order) && !empty($pos_info)) {
            $has_rs = array();
            $has_rs['code'] = $has_order['code'];
            $has_rs['son_code'] = '';
            $this->_success($has_rs);
        }

        //开启事务
        $transaction = Yii::app()->db->beginTransaction();
        try {
            //下订单
            $order_rs = $this->actionCreate($type, $sid, $member, $goods_arr, 0, 0, 0, $machineTakeType, $remark, true, true, null, null, $transaction);
            if ($order_rs['success'] == true) {
                $insertData = array(
                    'member_id' => $memberInfo['id'], //会员id
                    'phone' => $memberInfo['mobile'], //会员手机号码
                    'card_num' => $post['CardNum'], //卡号
                    'amount' => $post['Amount'], //金额
                    'serial_num' => $post['TransactionSerialNum'], //流水号
                    'transaction_time' => $pay_time, //交易时间
                    'business_num' => $post['BusinessNum'], //商铺号
                    'device_num' => $post['DeviceNum'], //终端号
                    'shopname' => $post['ShopName'], //商户名称
                    'operator' => isset($post['Operator']) ? $post['Operator'] : '', //操作员
                    'doc_num' => $post['DocNum'], //凭证号
                    'batch_num' => $post['BatchNum'], //批次号
                    'card_valid_date' => strtotime($post['CardValidDate']), //卡片有限期
                    'transaction_type' => $post['TransactionType'], //交易类型
                    'send_card_bank' => isset($post['SendCardBank']) ? $post['SendCardBank'] : '', //发卡行
                    'receive_bank' => isset($post['ReceiveBank']) ? $post['ReceiveBank'] : '', //收单行
                    'clear_account_date' => isset($post['ClearAccountDate']) ? strtotime($post['ClearAccountDate']) : '', //清算日期
                    'order_id' => $order_rs['data']['order_id']
                );
                if (!Yii::app()->db->createCommand()->insert("{{pos_information}}", $insertData)) {
                    $transaction->rollback();
                    $stock_rs = ApiStock::stockFrozenRestoreList($order_rs['outlets'], $order_rs['order_line_ids'], $order_rs['order_goods_nums'], API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
                    $this->_error('保存数据失败！');
                };


                //充值开始
                $apiLogData['order_id'] = $order_rs['data']['order_id'];
                $apiLogData['order_code'] = $order_rs['data']['code'];
                $apiLogData['operate_type'] = AccountFlow::OPERATE_TYPE_EBANK_RECHARGE;
                $apiLogData['transaction_type'] = AccountFlow::TRANSACTION_TYPE_RECHARGE;
                $apiLogData['remark'] = isset($post['remark']) ? $post['remark'] : 'pos机刷卡充值';
                $apiLogData['money'] = $post['Amount'];
                $apiLogData['account_id'] = $memberInfo['id'];
                $apiLogData['sku_number'] = isset($memberInfo['sku_number']) ? $memberInfo['sku_number'] : '';
                $apiLogData['gai_number'] = $memberInfo['gai_number'];
                $apiLogData['data'] = json_encode($post);
                $apiLogData['create_time'] = time();
                $apiLogData['node'] = AccountFlow::BUSINESS_NODE_EBANK_POS;
                //处理金额
                $res = AccountBalance::changeBalance($apiLogData, false);
                if ($res == false) {
                    $transaction->rollback();
                    $stock_rs = ApiStock::stockFrozenRestoreList($order_rs['outlets'], $order_rs['order_line_ids'], $order_rs['order_goods_nums'], API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
                    $this->_error('保存数据失败！');
                }


                //充值结束
                //组装数据
                $data['order_id'] = $order_rs['data']['order_id'];
                $data['order_code'] = $order_rs['data']['code'];
                $data['operate_type'] = AccountFlow::OPERATE_TYPE_SKU_PAY;
                $data['transaction_type'] = AccountFlow::TRANSACTION_TYPE_CONSUME;
                $data['remark'] = isset($post['remark']) ? $post['remark'] : 'pos机刷卡订单支付';
                $data['money'] = $post['Amount'];
                $data['account_id'] = $memberInfo['id'];
                $data['sku_number'] = isset($memberInfo['sku_number']) ? $memberInfo['sku_number'] : '';
                $data['gai_number'] = $memberInfo['gai_number'];
                $data['data'] = json_encode($post);
                $data['create_time'] = time();
                //处理金额
                @$ab = AccountBalance::changeBalance($data, false);
                if ($ab) {

                    //提交
                    $transaction->commit();
                    //处理订单
                    $params = array('code' => $data['order_code'], 'pay_type' => 4, 'pay_status' => 'pos','pay_time'=>$pay_time);
                    $postrs = $this->requestSku($params, 'sOrder/pay/', 105);

                    $sign = Order::orderSign($data['order_code']);
//                    $orders = Order::model()->find('code=:code', array(':code' => $data['order_code']));
//                    $orders->pay_time = $pay_time;
//                    $orders->save();

                    if ($sign['success'] && $postrs['success']) {

                        //首次充值 返回10%金额
                        if(!empty($token)){  //有登录消费才返现
                            $modelOrder = new Order();
                            $modelOrder->giveBackAmountFirstConsume( $data['order_code'],$memberInfo['id']);
                        }
                        $rs = array();
                        $rs['code'] = $order_rs['data']['code'];
                        $rs['son_code'] = isset($order_rs['data']['son_code']) ? $order_rs['data']['son_code'] : '';
                        $this->_success($rs);
                    } else {
                               $sql = 'update '.Order::model()->tableName().' set machine_status='.Order::MACHINE_STATUS_NO. ' where code='.$data['order_code'];
                                Yii::app()->db->createCommand($sql)->execute();              
                        Yii::log('mpay/$postrs ' . serialize($postrs), CLogger::LEVEL_ERROR);
                        $stock_rs = ApiStock::stockFrozenRestoreList($order_rs['outlets'], $order_rs['order_line_ids'], $order_rs['order_goods_nums'], API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
//                    var_dump($stock_rs);
                        $this->_error('执行充值逻辑失败' . (isset($postrs['resultDesc']) ? $postrs['resultDesc'] : '') . ' 请等待退款或联系客服 ');
                    }
                } else {
                    $transaction->rollback();
                    $stock_rs = ApiStock::stockFrozenRestoreList($order_rs['outlets'], $order_rs['order_line_ids'], $order_rs['order_goods_nums'], API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
                    $this->_error('事务失败');
                }

                $transaction->commit();
                return;
            }
        } catch (Exception $e) {
//            $stock_rs = ApiStock::stockFrozenRestoreList($order_rs['outlets'],  $order_rs['order_line_ids'], $order_rs['order_goods_nums'], API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID); 
            $transaction->rollback();
            $this->_error($e->getMessage());
        }

        $transaction->commit();
        return;
    }

    public function actionCreate($type = Order::TYPE_FRESH_MACHINE, $sid, $member, $goods_arr, $addressId = 0, $shippingType = 0, $shipping_time = '', $machineTakeType = 0, $remark = '', $stock = true, $trans = true, $fatherId = null, $partnerId = null, $transaction) {
        $member = $member * 1;
        $sid = $sid * 1;
        $fatherId = $fatherId * 1;
        $partnerId = $partnerId * 1;
        $rs = array();
        $rs['success'] = false;
        $rs['data'] = array();
        $rs['code'] = ErrorCode::COMMOM_SYS_ERROR;
        $member_info = Member::model()->getMemberById($member); // 直接获取了mobile
        $mobile = $member_info;

//        // 执行事务
//        if ($trans == true){
//                $transaction = Yii::app()->db->beginTransaction();       
//        }
        try {
            //俊鹏机
            $ids = array();
            foreach ($goods_arr as $g) {
                $ids[] = $g['id'];
            }


            if ($type == Order::TYPE_SUPERMARK) {
                $store = Yii::app()->db->createCommand(' SELECT id,name,member_id,is_delivery,delivery_mini_amount,delivery_fee,max_amount_preday,delivery_start_amount FROM ' . Supermarkets::model()->tableName() . ' WHERE id= ' . $sid)->limit(1)->queryRow();
            } elseif ($type == Order::TYPE_MACHINE || $type == Order::TYPE_MACHINE_CELL_STORE) {
                $store = Yii::app()->db->createCommand(' SELECT id,name,member_id FROM ' . VendingMachine::model()->tableName() . ' WHERE id= ' . $sid)->limit(1)->queryRow();
            } elseif ($type == Order::TYPE_FRESH_MACHINE) {
                $store = Yii::app()->db->createCommand(' SELECT id,name,member_id,type FROM ' . FreshMachine::model()->tableName() . ' WHERE id= ' . $sid)->limit(1)->queryRow();
            }


            if (empty($store)) {
                $this->_error(ErrorCode::getErrorStr(ErrorCode::STORE_NO_EXIST));
            }
            $partner = Yii::app()->db->createCommand(' SELECT member_id,id,name FROM ' . Partners::model()->tableName() . ' WHERE member_id = ' . $store['member_id'])->limit(1)->queryRow();


            $gcri = new CDbCriteria();
            $gcri->addInCondition('id', $ids);
            if ($type == Order::TYPE_FRESH_MACHINE) {
                $projectId = API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID;
                $storeGoods = Yii::app()->db->createCommand('SELECT l.code,b.* FROM ' . FreshMachineLine::model()->tableName() . ' as l inner join (SELECT g.*,t.id as t_id,t.goods_id,t.weight,t.line_id FROM ' . FreshMachineGoods::model()->tableName() . ' as t LEFT JOIN ' . Goods::model()->tableName() . ' as g ON t.goods_id=g.id WHERE  t.machine_id=' . $sid . ' AND t.status=' . FreshMachineGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' AND t.id IN ( ' . implode(',', $ids) . ')) as b on b.line_id = l.id')->queryAll();
            }
//            elseif ($type == self::TYPE_FRESH_MACHINE && $store['type'] == FreshMachine::FRESH_MACHINE_SMALL) {
//                //俊鹏机处理 
//                $projectId = API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID;
//                $lids = array();
//                $nums1 = array();
//                foreach($goods as $v){
//                    $nums1[$v['id']] =$v['num']; 
//                }
//                $storeGoods2 = Yii::app()->db->createCommand('SELECT l.code,b.* FROM ' . FreshMachineLine::model()->tableName() . ' as l inner join (SELECT g.*,t.id as t_id,t.goods_id,t.weight as t_weight,t.line_id,t.create_time as t_time FROM ' . FreshMachineGoods::model()->tableName() . ' as t LEFT JOIN ' . Goods::model()->tableName() . ' as g ON t.goods_id=g.id WHERE  t.machine_id=' . $sid . ' AND t.status=' . FreshMachineGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' AND t.goods_id IN ( ' . implode(',', $ids) . ')) as b on b.line_id = l.id  order by t_time asc')->queryAll();
//                foreach ($storeGoods2 as $v) {
//                    foreach ($ids as $k1 => $v1) {
//                        if ($v['goods_id'] == $v1) {
//                            $s = array('t_id' => $v['t_id'], 'line_id' => $v['line_id']);
//                            $lids[$v1][] = $s;
//                        }
//                    }
//                    $lids1[] = $v['line_id'];
//                }  
//                
//                $stocks1 = ApiStock::goodsStockList($sid,  array_values($lids1), $projectId);
//                if (empty($stocks1)) {
//                    $rs['code'] = ErrorCode::GOOD_STOCK_NOT_EXIST;
//                    return $rs;
//                }
//               
//                $goods1 =array();
//                foreach ($lids as $k => $v) {
//                    if (count($v) > 1) {                         
//                        foreach ($goods as $k1 => $v1) {
//                            $num_rs = $v1['num']; 
//                            if($v1['id'] == $k){                                
//                                foreach ($v as $v2) {
//                                    if ($num_rs >= $stocks1[$v2['line_id']]['stock'] && $stocks1[$v2['line_id']]['stock']>0) {
//                                        $goods1[] = array('id'=>$v2['t_id'],'num'=>$stocks1[$v2['line_id']]['stock']);
//                                        $num_rs = $num_rs - $stocks1[$v2['line_id']]['stock'];               
//                                       
//                                    }elseif($num_rs<$stocks1[$v2['line_id']]['stock'] && $stocks1[$v2['line_id']]['stock']>0 &&$num_rs>0){
//                                        $goods1[] = array('id'=>$v2['t_id'],'num'=>$num_rs);
//                                        $num_rs = 0;
//                                    }
//                                }
//                            }
//                        }
//                    }else{
//                        $goods1[] = array('id'=>$v[0]['t_id'],'num'=>$nums1[$k]);
//                    }
//                }
//                $goods = $goods1;
//
//                $ids_new = array();
//                foreach ($goods as $g) {
//                    $ids_new[] = $g['id'];
//                }
//                $storeGoods = Yii::app()->db->createCommand('SELECT l.code,b.* FROM ' . FreshMachineLine::model()->tableName() . ' as l inner join (SELECT g.*,t.id as t_id,t.goods_id,t.weight as t_weight,t.line_id FROM ' . FreshMachineGoods::model()->tableName() . ' as t LEFT JOIN ' . Goods::model()->tableName() . ' as g ON t.goods_id=g.id WHERE  t.machine_id=' . $sid . ' AND t.status=' . FreshMachineGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' AND t.id IN ( ' . implode(',', $ids_new) . ')) as b on b.line_id = l.id')->queryAll();
//            } 
            else {
                Yii::log(var_export('1=='.$goods_arr));
                if ($trans == true)
                    $transaction->rollback();
                $this->_error(ErrorCode::getErrorStr(ErrorCode::ORDER_GOODS_LESS));
            }

            if (count($storeGoods) != count($goods_arr)) {
                 Yii::log(var_export($storeGoods));
                if ($trans == true)
                    $transaction->rollback();
                $this->_error(ErrorCode::getErrorStr(ErrorCode::ORDER_GOODS_LESS));
            }


            $goods_nums = array();
            $line_ids = array();
            $outlets = $store['id'];
     
                foreach ($storeGoods as $v) {
                    $line_ids[$v['id']] = $v['line_id'];
                }
                foreach ($goods_arr as $g) {
                    $goods_nums[$g['id']] = $g['num'];
                }

            ksort($goods_nums);
            ksort($line_ids);
            $lids = array();
//            $stock_rs = ApiStock::stockFrozenRestoreList($outlets, $line_ids, $goods_nums, $project_id); //返回冻结库存
            if ($stock === true) {
                //冻结库存
            
                    foreach ($storeGoods as $g) {
                        //先查询库存
                        $stock_rs = ApiStock::goodsStockOne($store['id'], $g['line_id'], $projectId);
                        if ($stock_rs['result']['stock'] * 1 > 0 && $stock_rs['result']['stock'] * 1 >= $goods_nums[$g['t_id']] * 1) {
                            $out_rs = ApiStock::stockFrozen($store['id'], $g['line_id'], $goods_nums[$g['t_id']], $projectId);
// 	        	var_dump($frozen_lines,$frozen_nums,$frozen_rs);exit();
                            if (!isset($out_rs['result']) || $out_rs['result'] != true) {
                                if ($trans == true)
                                    $transaction->rollback();
                                $this->_error(ErrorCode::getErrorStr(ErrorCode::GOOD_STOCK_UPDATE_ERROR));
                            }
                        }elseif ($stock_rs['result']['stock'] * 1 > 0 && $stock_rs['result']['stock'] * 1 < $goods_nums[$g['t_id']] * 1) {
                            $out_rs = ApiStock::stockFrozen($store['id'], $g['line_id'], $stock_rs['result']['stock'], $projectId);
// 	        	var_dump($frozen_lines,$frozen_nums,$frozen_rs);exit();
                            if (!isset($out_rs['result']) || $out_rs['result'] != true) {
                                if ($trans == true)
                                    $transaction->rollback();
                                $this->_error(ErrorCode::getErrorStr(ErrorCode::GOOD_STOCK_UPDATE_ERROR));
                            }
                        }
                    }
                }
            

            $order = new Order();
            $order->father_id = !empty($fatherId) ? $fatherId * 1 : 0;
            $order->code = Order::_createCode($type, $member);
            $order->type = $type;
            $order->goods_code = Order::getGoodsCode();
            $order->member_id = $member;
            $order->status = Order::STATUS_NEW;
            $order->address_id = $addressId;
            $order->mobile = $mobile;
            $order->shipping_type = $shippingType;
            $order->shipping_time = $shipping_time;
            $order->machine_take_type = Order::MACHINE_TAKE_TYPE_AFTER_PAY;
            $order->machine_status = Order::MACHINE_STATUS_YES;
            $order->pay_status = Order::PAY_STATUS_NO;
            $order->create_time = time();
            if (!empty($remark))
                $order->remark = $remark;
            $order->save();
            $order_id = Yii::app()->db->getLastInsertID();

            if ($type == Order::TYPE_SUPERMARK) {
                $order->store_id = $sid;
            } elseif ($type == Order::TYPE_MACHINE || $type == Order::TYPE_FRESH_MACHINE || $type == Order::TYPE_MACHINE_CELL_STORE) {
                $order->machine_id = $sid;
            }


            $order->partner_id = $partner['id'];

            $total_price = 0;

                foreach ($storeGoods as $g) {
                    $orderGoods = new OrdersGoods();
                    $orderGoods->sgid = $g['t_id'];
                    $orderGoods->gid = $g['goods_id'];
                    $orderGoods->num = $goods_nums[$g['t_id']];
                    $orderGoods->sg_outlets = $g['code'];
                    if ($orderGoods->num <= 0) {
                        continue;
                    }


                    $orderGoods->order_id = $order_id;
                    $orderGoods->supply_price = $g['supply_price'];
                    $orderGoods->price = $g['price'];
                    $orderGoods->total_price = $orderGoods->price * $orderGoods->num;
                    $orderGoods->status = Order::STATUS_COMPLETE;
                    $orderGoods->line_id = isset($g['line_id']) ? $g['line_id'] : 0;
                    $orderGoods->create_time = $order['create_time'];
                    if (isset($g['weight']))
                        $orderGoods->weight = $g['weight'];
                    if (isset($g['name']))
                        $orderGoods->name = $g['name'];
                    $orderGoods->save();

                    $total_price += $orderGoods->total_price;
                }
            




            //保存订单
            $order->total_price = $total_price;
            $order->save();

            $father_order_id = $order->id;

            //生鲜机下单，需要查询货道所属商家					分别生成子订单
            if ($type == Order::TYPE_FRESH_MACHINE && $fatherId == null) {

                $son_goods = Yii::app()->db->createCommand()
                        ->select('t.id,t.goods_id,t.machine_id,t.line_id,l.rent_partner_id,l.rent_member_id')
                        ->from(FreshMachineGoods::model()->tableName() . ' as t')
                        ->leftJoin(FreshMachineLine::model()->tableName() . ' as l', 't.line_id=l.id')
                        ->leftJoin(Goods::model()->tableName() . ' as g', 't.goods_id=g.id')
                        ->where('t.id IN (' . implode(',', $ids) . ') AND t.machine_id=' . $sid . ' AND t.status= ' . FreshMachineGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' ')
                        ->queryAll();

                $son_partner_goods = array();


                    foreach ($son_goods as $g) {
                        $temp_arr = array();
                        $temp_arr['id'] = $g['id'];
                        foreach ($goods_arr as $gg) {
                            if ($gg['id'] == $g['id']) {
                                $temp_arr['num'] = $gg['num'];
                            }
                        }
                        $son_partner_goods[$g['rent_partner_id']][] = $temp_arr;
                    }        	      
                
                //查询是否多个商家  如果有多个商家则生成多个订单
                if (count($son_partner_goods) > 1) {
                    $son_code = array();
                    foreach ($son_partner_goods as $key => $val) {
                        $son_order = $this->actionCreate($type, $sid, $member, $val, 0, 0, 0, $machineTakeType, $order['remark'] . '|此订单为总订单[' . $order['code'] . ']的子订单', false, false, $father_order_id, $key);
                        $son_code[] = $son_order['data']['code'];
                    }
                } elseif (count($son_partner_goods) == 1) {
                    //只有一个商家  即当前商家  更新订单的所属商家
                    $partner_keys = array_keys($son_partner_goods);
                    $update_rs = Yii::app()->db->createCommand()->update(Order::model()->tableName(), array('partner_id' => $partner_keys[0]), 'id=' . $order['id']);
                }
            }
        } catch (Exception $e) {
            $ids = array();
            foreach ($goods_arr as $g) {
                $ids[] = $g['id'];
            }
            $store = Yii::app()->db->createCommand(' SELECT id,name,member_id FROM ' . FreshMachine::model()->tableName() . ' WHERE id= ' . $sid)->limit(1)->queryRow();
            $projectId = API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID;
            $storeGoods = Yii::app()->db->createCommand('SELECT l.code,b.* FROM ' . FreshMachineLine::model()->tableName() . ' as l inner join (SELECT g.*,t.id as t_id,t.goods_id,t.weight,t.line_id FROM ' . FreshMachineGoods::model()->tableName() . ' as t LEFT JOIN ' . Goods::model()->tableName() . ' as g ON t.goods_id=g.id WHERE  t.machine_id=' . $sid . ' AND t.status=' . FreshMachineGoods::STATUS_ENABLE . ' AND g.status=' . Goods::STATUS_PASS . ' AND t.id IN ( ' . implode(',', $ids) . ')) as b on b.line_id = l.id')->queryAll();
            $goods_nums = array();
            $line_ids = array();
            $outlets = $store['id'];
            if ($store['type'] == FreshMachine::FRESH_MACHINE_SMALL) {
                foreach ($goods_arr as $v) {
                    foreach ($v['hds'] as $k1 => $v1) {
                        $goods_nums[] = $v1['count'];

                        $lis = FreshMachineLine::model()->find('machine_id=:mid and code=:code', array(':mid' => $store['id'], ':code' => $v1['hd_id']));
                        $line_ids[] = $lis['id'];
                    }
                }
            } else {
                foreach ($goods_arr as $g) {
                    $goods_nums[$g['id']] = $g['num'];
                }
                foreach ($storeGoods as $v) {
                    $line_ids[$v['id']] = $g['line_id'];
                }
            }
            ksort($goods_nums);
            ksort($line_ids);


            if ($trans == true)
                $transaction->rollBack();
            $stock_rs = ApiStock::stockFrozenRestoreList($outlets, $line_ids, $goods_nums, API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID);
            throw new Exception($e->getMessage());
        }

//        if ($trans == true)
//            $transaction->commit();
        $rs['success'] = true;
        $rs['data'] = $order->getAttributes();
        $rs['data']['order_id'] = $order_id;
        $rs['data']['store_name'] = $store['name'];
        $rs['order_goods_nums'] = $goods_nums;
        $rs['order_line_ids'] = $line_ids;
        $rs['outlets'] = $outlets;
        if (!empty($son_code)) {
            $rs['son_code'] = $son_code;
        }
        return $rs;
    }

    public function actionTest() {
  
$post = array(
//    'appType'=>'6',
//    'ver'=>1,
//    'version'=>'Android'
);
//        $post = array(
//            'machineTakeType' => '6294a59a0fd73f8c620326c27ebaae94b60718847dd2c87f652d52efee1a1a2c8a80c75c69b186daa61aa672b1812aa1f32984287223658f6fcda3ede6258bb3ffce9483c3cd9423f9e3c78c2ac7fac38fd99355f8d8a096c10941d84f2d4b0101f93aca06728a768660dc203fb2247f4813bc070ece8d3f545bd829bc37ed24caa4285af6845faadb848914195b571f751c83bcc7845e8ea7c6288ebde922d33dca03d208773cb6a0d82c0800934463943d4072024065904f59435cad94e3f4a4d4f80836c95e40e9ab31a5062e8e9fa99277fc178848d04abe5f30ae892a57328c1f46b014ff349201b1ad10b7bde3583eb00f8f75e8e49748cbc6f4fe4322', 'ClearAccountDate' => '5a7f72bbc80ecbd8c0fea7bde14beedec067ad345a88a03a18d54d73bbdf108b0a5cc33c32b02fb248e01c92765dcd0cfa9d1dc27c3cd68dd4cc29c41ccc77206f67db1d44a9664af68744c417ea2b66d6dca95a711a2805eb85e3e9974eea4b2952202b7e697b7f2949d14a758f1e0d7ddd81b11020c761603f1caf667accdd49848f2b64862ade64d78d81b7934668b9feae104c6c52b25a9fb788f7baccf1f6ac17103fc5eca1fa22283580d3c3476e405e06b167744ff9c527fc08a1a33b23abe2c6cf4cf38c94522024d5ac6d2c2f1824a35c482765ca9ad02f3f6df3af0420f11d9d6e69c05240b92f1894bdb7c6d8cadcc1b3c0a762a44b0b0c837ecf', 'Amount' => 'b837e34961f6b3f8af5833de14fd197c2845979c88dca70feb92d264a9f24b15b7efbe9480385fc1946457bdff59e05fae03ba3e178eca8c495cd3a0afec3be7d8982487eec451ec541b1be65cacf22fe5f6b641ab6e4d36b61a416358475746483eec59fe5839b9a548e4b632b53aaa8e93dc4dab81c686ed2972f0e1e58468346ac2c2b6336948c5a2bc5078200aa0c82e65b48e95438e94eafec5565f96271fb3d39d800ce23eb78fc45b2d1a27e659bcd274a3ffb69ad4c7b7ac5b7e89614adfc2a7b25142fb0feb40ff7ea9b2764030f0ca6893d82f8e3e44215c485a4043c5dbd048a0ccb732722827089488aabec9bc5e67672a2d542d7c7807db7fa5', 'DeviceNum' => '755be3493fb80d79007e509a0b8bbfcb657f55f001eea11fb9749cdf334e04c9b56738033ad4e1ba1f5d83b2f2979503cc5300ffc43fc4cad2fe770221c63af9dabb6794fe58325d78631c3266500a53fcfc188c496ab6625e8d4ee9fe15d1362cd0a7dd2a8d54cfc2bf846c9969c3bafd27907a807d6e308a20bd11a505f0ad57d4a7cf87011108fe678963b0820744267d18466f0255e4c8dde72dcb8978a609f7d40829666c6ee76b365fe1f7b38ab8acd1c6fc2335999b479189bdde0e921a5c8aa08710c3003c10ec0352783c17e589f7ba16e1cf4cb8d30f038b3423a8bab0f7f11b55b7660f712bd2e395358338ddbce74439cdcac98fc95e4651ef0d', 'TransactionDate' => '46b870cd532b26de5026af1d888c7bc03b6ac7343a0c7ee9372e5caeee049df1fe093fb1cda46d8b1496b1fae37cc7713cb8ab7122083377702d45473ee20ce068f56f70daeeb8b0843c91c166d7327928c9f651eeca0ab9ae6c2e57534752fca0080a99cf7ad9f2718d63e18141cd1cb8161d0e42326098ebc5d67551f85665488e2d2d211cf63eabe934a5ee8302c0c0f78f72e4e0d5b38501187c2888dd143501d1a592a12b03ece4c0a0a510858e35f26a0da0837093d38b9a6dc17fedca36c65589758c81d26e79f37664ec354c1b7ae3f2638b3c7e9280b2d22cef8236bd4cab7171c3510503a811319c7bfd30022e71394014db390a459515db253306', 'Operator' => '9604803af06106cb2f55ff7f49aee1fd1ebe2899383f260b887e76cc7b83fea6d85cad55c64d62a1fc33a2921bcb11c5d308c458fc8a5ccfa40a5469084eb5014dc8cdcc5c864dc646f2690b4292b049b26620b0978ddf2d8e1d83d6744b06a5c1c1916fa28253a20e87dd1f80a6dd034da1ba98b02248e2415b4f5a7c816ba29d013a3a8402e0e3795c912d33ca5e0ca32243951d10edccb6139f80d1e7031d0a5570135d3cc8bb3725f50c9bf2dff355ad9368eb7bc8c2090d4079a17b8ddc110832940eb72a7c559023c112907210d7e651a3318b72507c8d8a5ec5ccb610bb9cf155ff03265810d2adc36c0650e1ce915231ab3d1e3a9f7b37825156c529', 'goods' => 'a9168652991f26bd33ed89ba937e5c5c174a862996f1267f56ba04aa3904b2f0b174ebcf3acdb9f3460290287756f3c148160a28b2acab711a4b015e187d00738a5d8f9608427e9674945bff6ae1d54835042179666966bdf9be628c393ac5bd97206e4c2e130fe3c93bcbcd8e5ab56b4fad4c52301a25c3fa46543e455acb1e96b1a0dfe1ec3788052c6c0194de9b16b223c3eb4de28149b7ff11a47bf7eaa40512c1e95feb45185b18ef0228afdbf2685502af844d8e1f44d4ec6c41385de320585c3b322e5334af2bb7741cdc44bfa5aefb858fc35144faf0376ebbf91457b2838531b129f7b4cafce29dff5b271b801d87ab2c2fc644e804752f11d6df15', 'BusinessNum' => '0c3e6cfae8baa1c62f83c423e15c53e1e3cd6ab7458710b12baaed555fe27de4ede52960fd827a7074238d8e2dd72471ddb0c6ee24e5efc404d0c9ad7aaeae24a91821d88c22c244bddbbc9c9132d58d2187ee698d5333d34b06478f80291d387298f071e7082865e5e67ad38a2271fb2c8099ff52f44b552be8537229a9f94290923d81a9ed9154bede0c104a90692e8d39f6530fb9774d1b4a35610948d6da15067fca5547701d5abf72ee9b7ec863ab63874bd239aec7f0f734d3fa99e4cb28020b2d35760e92cac7c0b2a8f504f39244030dce0b37bddd564ed5b16a09936fd14e95247aa5efcfd4a61c87f01bc64cb4a928e753ba6c33fa1af3fdc55e42', 'ShopName' => '69c0207c47fc22b5caf98bfc8191276fbc18d4b2a16030c89e4bbd69522d52613b7a0cfa28588fb370d89c5e891a7af3d00a573a2880499e3dcebef48e1ac38cfc9c132f20abe92955ff16eebea7ad1f24a7c8ce7d3c288be354db678d21223376e239a5f6ab2b1f04da7e2586e407128b2dc7daa2135c13572703f175713be4a06890ba40df278d202c7398e534cbdc67c34f973cc47740a822bc17aeae24437e8c8f0bb8307b5293aa22d18b087a126cfd4ab6e425a28b7232e563d8e6e43feae3d150a977cb4e7470be3a5c3a16110fa7137716b4c31c14fd50bc39e7f4fde2d66905d3c8e207fa218e84b5dff55ee97b6815b84772335fec2ce805f9fb22', 'TransactionType' => '096f93b3827b893a870b0c8c36334b86eed1d20dd4450c34ec58e94e22c6296a107ff1cfb5511a15c49c2cc67a459fd43b1a89c9f84352b303c3026e3e537a8d3538e8b834817214283488e35724efc79ea68e68ae842c7894f15b922344cb9ba3bfbd0881615770c8f2a9d6fe52933852dd4e9705db0f6b199c7f15060cb208a8ab1a6d9dda1bc8fae80294358385107760810facb6bd1219569982ab52b51efd1c903a9dd4c7cfb29f85df8545ca70876158b58a59d18a4d330df6121e51e42b5ec060e0ce848e8862854a5cd0e22d5f1995f6c4c7a249a6cd663ebac39a4e184cc775804e1b2191c982a0950047843ce7f535ff076cc01f1fb4825aefa912', 'CardValidDate' => '6f8764f1c272376274e7029b00a2be7458e755acee89826b5b5c0b1ff642610e8f753858a3f1ee8e3b7580ae6d1cca29e48033cb4ad009be5affb541964696f13501f9cd4f917ea030b24bef100b0802190a28f826cd9937cb8b01d74df05c281913058076eab49889dc7b1904114c0d2879230f1494066824e3b828c87f84078713a9961396165826a22e34d3d27cef824011da735fb8ec0484aeea6ba2676f09c45a4ba37b8772277cd135a32dffe967221b6c7e4540e2c49f509ce451204b574844988f70ce47e6d4cd509c4f67313baafe89e6066917d87b4ef4a78139d1ce6da8845736e5f80bfe0e9ba5fc22d7d8d1915b91dce25a123375c35076f65b', 'type' => '33bf3f777fb01121f92630c1f78af2b1b0b71f3d243fcae42958bfc919c0001e06ea8e4ac67fa760b25bbdafeac50fb0aa7f6fe86479ec32b09c2af0c728c8f74074246acfa13e520f239f33da931e826e436e35379e8e3c5a044bd7f60db608de390064242f876841aa03f3f76c17760e820d9c31f92ce1e478f281d510cc9c', 'BatchNum' => '543c6280562b8dc9879ccecc5ca07041a81d42949d29d4b5be71b07443879b2f981b9c6f30a15a0bc057a7aa95582ec702e7b6eddcfee7726b61d2b4b3ab9ca82303410ea4627f8f1bd8c2127a17ffd3e91701fe87dc1b0877dada69dc609c10c88901a4407a26325c3ea4ebbe1a8f0c76dc3fd4545c3288b1b51c105264a52c140091968395773952d7a74eaf776ef11cef5e50be21f592439433931bf6a1fbb654e1cd11e288b9074864139c53ece0b3f0a3dbc7f039c79028616d928ba629b259cb480b39e17b24c5a7d0655a9178bc34a94676f5ce87a5d3424d927ae9c32d3fcee5d1ce4e2c0a7c76a56a4aab505078d29ff6ad25c78b3a5512d96a1154', 'DocNum' => '2a186dcab1454a08287c28a8575a037432c752f049f1b9eb55b9d783757bf085c4783bbcd31df1d10df8175cde7a10ec6933491b962427effda3f718a50239696f6a7532498ed4c0314f7a9b3746e3f21f4c637c241ba485d5bcb70d61aac45475afe713f9fafadcb5a2083b92947e18e6e73cd100ff79fcdf12b18f5500f0f289a90e7591daabf5d34a35284504a055c49b9994f797d9a46332a3a8f90e91d6140279f78c1d3cc56d72efc33c02cf1dcece0d61ec16bcfd635ea107d9f049d57986353fd6e5b76dc3a98dcaf877f6a77b3843099a2847c122354e5e0e5db17df8c636b5ec8c0d19171f4c367e5fb167d8d43d3af20a5cc1fa9c170f40ed699f', 'SendCardBank' => 'c44151e422b0d3c6954e90d3605300afd9db164d0d62fa2ca17dd495527531048ad53772c31734d213a66c7709bc61e03ab4f9c4be190f2c7f81ee719065eb93862ea82bc36af3ca93f4174abd272b2f690348fb65116963113fb4684ac34e6f3322098c1ba52256365de401c3a112e4709660d2c102b68951505c6f72dd9c1d4983eedc3842b789490483998a77aba7c0f94f37f1707c0f91afd3321a93dff9e54a9edd7bfd31041dac1705e6cda2e1c8c24cbb0f8198b13f15c9b1b6eba30df0103403095e36f62a31bf8524e0439835054f6a814e9b276c9308845b2bcf4f729a2777b1b892d684a467b2f5dcde6dce30fc541b70a48a492a3109989bc60b', 'shopId' => '294703eef2e6620431a76619b5ea545f4dbec1a35ee15239398f83bd62ef3d053322616b2b9ac4445f469cef934b762408602e8b77ab210ec03d1563d2545901f081f915b638a306aa67db5c2fe8197efd3f191f994719d07353606c309e4fe562d4632d299d6063e3057ca6684baf5a8b19b17c69fbe130f5810ad6a6bd1714', 'TransactionSerialNum' => '8fb4cc370561eecb3546b38132216363a3ddf4ea75858203664fb4cb59e6a9db3999ead697d5c21ec9a5de9621884254bff62f8cf0f5fff1461daf3ebba841cc6ba5ef0a66b7ecfdd56420171fd800fe5a488280cb4d90f04da759c42965aacd7cf31a5b373ce15ca03c8e3b930c634db971f128d934fea4817f24b45189c6c2aa0d75211f6d564a3dbe4e0741b01aec329433aa987493d49302af3f4b4f69d1ae47695385f390d4e0bc0c571a02560d4ad4ea0ae6b878e592f927f6a0bae24907404b2732141660231e48d552f28f85ec11ec00ed15a158c915d733780b908081d67eb812462caff00c759c86b34a65ee907ad3e898b2243cfd5906a7be390f', 'ReceiveBank' => 'b0d0c179baf44214669c021e66a234ff75c33f6956f6ef42f7e009fe93bafcab98f0f55eabd035c9eca0e32d132aa7e6c3d05a7d9019bee25037dee1e556d0626f707336a573f1c2daee76177199d60858dc5628e5d1958bb3d0498666fcca052db31d17a5e3756ee7900ad66195c2530426fadb35c51f74d856043ccaecab5d4ee34af16fd92e5e1fa7392805c700c2705d442cc46eefabf27ab2f852cbdb7b429133c09335d24b366ddbd7b499fc529115125f73b90b9fb4458675bc18ebd139e85d3a540f4634ab3ea9768bbff390446e6862cd88eb77054d322ff01c7e93bb2b179b8c8604f45d47af2372eb037a52fff6e9af45438b85eef76cbe9a676d', 'TransactionTime' => '3683c6df5d91e2189fbc0bfdf936372120a16af153319ca7a1022f6f60702e67077d711404ed15558fbeea87f9e4efd48daeba808ebf64be141039f4824d7263b605b49f932375ae97cb8c0c43621dce42db20250a84278195a5e0988cd327cafd28ff15ce297f7bcc06292b2c5b4859dc99b20868d724ec3b3f8ed5af1f032db660da242e815b188842c587660f1bdc1507f9e381deb65d3b97ffc031fcda0eb087f6d9b4e0f5d08cf998696f7a32c747066e9a372a88a18e410928498ac190ce06565c2e74b30c312be29ca514e2ea07e11ec7d3895b8012698adca7db91f3b71639f76d06586a94f44b1e6b152ecc8e88804b1e928e7bed9beddc6474553b', 'CardNum' => '51abec9ca01c9913b2dc065d6f8cee12f0d7af0d8117e1dfc5abb4159da4e59f358e672e60e2c8f6153996f3435043728f19ecad4e25039e8043cf1da3d1de981d4b9321efbed607d4ef43943111ad655e85563db75237fda07346e76f747c36fc3cf6d20b2f1222d091f75d765299fcdd8f591e4095cd0c6c0616613e05fb19cbdee379482414fd4e7b9ac1fa3f46bd289fbcdf20c69390589c13e666e8aeac2bb2106941e8b5ec39305971ed2f6aa8c8fe0d8859bdc26b7524b849ad3f29f4405baff578b98d45a3e7131a6435a5c108b8b295b3ba0c53f8e49692ad86b37d8bdd9c6666aca23380c26d93fd3d28d6bb1ea78272f7237f345cf74524d849ec'
//        );http://token.gnet-mall.net//token/login
        $url = 'http://api.gaiwangsku.com/mMember/Test';
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post,
        );
//
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        var_export($result);
    }

}
