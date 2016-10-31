<?php

class BalanceController extends OrderapiBaseController {

    public function actionIndex() {
        $memberId = '1';
        $member = Yii::app()->db->createCommand()
                        ->select('id,gai_number,mobile,email,country_id,province_id,city_id,district_id,street')
                        ->from('gaiwang.gw_member')->where('id=:id', array(':id' => $memberId))->queryRow();
        echo Member::syncFromGw($member);
        echo '<br/>success<br/>';
//        $url = 'http://orderapi.gaiwangsku.com/OBalance/Consume';
//        $postData = array('memberId'=>0);
//        $res = Tool::post($url,$postData);
//        printf($res);
//        $res = ApiOrder::orderPay('241720151214111205496368');
//        printf($res);
    }

    public function actionAb() {
//         $arr = array(
//         	"name" => $_GET['name'],
//         );
        print_r($this->_getApiKeys());
        exit;
//         echo $_GET['jsoncallback'];
    }

    /**
     * 获取余额
     */
    public function actionAmount() {
        try {
            if ($this->isPost()) {
                $post = $this->data;
                // 检验会员信息
                if (!$member = Member::getByGwNumber($post['GWnumber']))
                    throw new Exception('账号错误或不存在');
                if (!$member->status > Member::STATUS_NORMAL)
                    throw new Exception('禁止登陆');

                //消费账户
                $accountBalance = AccountBalance::findRecord(array(
                            'account_id' => $member['id'],
                            'type' => AccountBalance::TYPE_CONSUME,
                            'sku_number' => $member['sku_number'],
                ));
                $this->_success(array(
                    'amount' => $accountBalance['today_amount']
                ));
            } else {
                throw new Exception('错误的访问方式');
            }
        } catch (Exception $e) {
            $this->_error($e->getMessage());
        }
    }

    /**
     *
     * GWnumber
     * codeId
     * code
     * node
     * amount
     * freight
     * remark
     */
    public function actionRecharge() {
        try {
            if ($this->isPost()) {
                $post = $this->data;
                // 检验会员信息
                if (!$member = Member::getByGwNumber($post['GWnumber']))
                    throw new Exception('账号错误或不存在');
                if (!$member->status > Member::STATUS_NORMAL)
                    throw new Exception('禁止登陆');
                if (!is_numeric($post['amount']) || $post['amount'] <= 0)
                    throw new Exception('支付金额数要大于0');
                if (!is_numeric($post['codeId']) || $post['codeId'] <= 0)
                    throw new Exception('codeId要大于0');
                if(!isset($post['type'])||$post['type']!='guadan'){
                    $sid = array();
                    $status = Tool::getConfig('amountlimit', 'isEnable');
                    if ($status == AmountLimitConfigForm::STATUS_ENABLE) {
                        $order = Order::model()->find('code=:cd', array(':cd' => $post['code']));
    //                    $sid = empty($order['store_id']) ? (empty($order['machine_id']) ? '' : $order['machine_id']) : $order['store_id'];
                        if($order['type'] == Order::TYPE_SUPERMARK){
                            $sid['store_id'] = $order['store_id'];
                        }else{
                            $sid['machine_id'] = $order['machine_id'];
                        }
                        if (!empty($sid)) {
                            // 检查消费限额
                            $rs = Member::checkAccountLimit($post['amount'], $sid, $member['id']);
                            if (is_array($rs)) {
                                $this->_error($rs['code']);
                            }
                        }
                     }          
                 }
                $apiLogData['order_id'] = $post['codeId'];
                $apiLogData['order_code'] = $post['code'];
                $apiLogData['operate_type'] = AccountFlow::OPERATE_TYPE_EBANK_RECHARGE;
                $apiLogData['transaction_type'] = AccountFlow::TRANSACTION_TYPE_RECHARGE;
                $apiLogData['remark'] = isset($post['remark']) ? $post['remark'] : '网银支付';
                $apiLogData['node'] = isset($post['node']) ? $post['node'] : '';
                $apiLogData['money'] = $post['amount'];
                $apiLogData['account_id'] = $member['id'];
                $apiLogData['sku_number'] = $member['sku_number'];
                $apiLogData['gai_number'] = $member['gai_number'];
                $apiLogData['data'] = json_encode($post);
                $apiLogData['create_time'] = time();
                //处理金额
                if (AccountBalance::changeBalance($apiLogData)) {
                    $this->_success('充值成功');
                } else {
                    throw new Exception('事务失败');
                }
            } else {
                throw new Exception('错误的访问方式');
            }
        } catch (Exception $e) {
            $this->_error($e->getMessage());
        }
    }

    public function actionYfzConsume() {
        try {
            $post = $this->data;

//             $this->_output(Member::getByGwNumber($post['gwNumber']));exit;
            // 检验会员信息
            if (!$member = Member::getByGwNumber($post['gwNumber']))
                throw new Exception('账号错误或不存在');

            if (!$member->status > Member::STATUS_NORMAL)
                throw new Exception('禁止登陆');
            if (!is_numeric($post['money']) || $post['money'] <= 0)
                throw new Exception('支付金额数要大于0');
            if (!is_numeric($post['codeId']) || $post['codeId'] <= 0)
                throw new Exception('codeId要大于0');

            $status = Tool::getConfig('amountlimit', 'isEnable');
            if ($status == AmountLimitConfigForm::STATUS_ENABLE) {
                $order = Order::model()->find('code=:cd', array(':cd' => $post['code']));
//                $sid = empty($order['store_id']) ? (empty($order['machine_id']) ? '' : $order['machine_id']) : $order['store_id'];
                  if($order['type'] = Order::TYPE_SUPERMARK){
                        $sid['store_id'] = $order['store_id'];
                    }else{
                        $sid['machine_id'] = $order['machine_id'];
                    }
                if (!empty($sid)) {
                    // 检查消费限额
                    
                    $rs = Member::checkAccountLimit($post['money'], $sid, $member['id']);
                    if (is_array($rs)) {
                        $this->_error($rs['code']);
                    }
                }
            }
            //检查余额
            $member_amount = AccountBalance::getMemberTodayAmount($member['sku_number']);

            if ($member_amount < $post['money']) {
                throw new Exception('用户余额不足');
            }
        } catch (Exception $e) {
            $this->_error($e->getMessage());
        }
    }

    /**
     * 来自盖付通后台访问
     * 订单扣钱的接口
     *
     * GWnumber
     * codeId
     * code
     * amount
     * freight
     * remark
     */
    public function actionConsume() {
        try {
            if ($this->isPost()) {
                $post = $this->data;
                // 检验会员信息
                if (!$member = Member::getByGwNumber($post['GWnumber']))
                    throw new Exception('账号错误或不存在');
                if (!$member->status > Member::STATUS_NORMAL)
                    throw new Exception('禁止登陆');
                if (!is_numeric($post['amount']) || $post['amount'] <= 0)
                    throw new Exception('支付金额数要大于0');
                if (!is_numeric($post['codeId']) || $post['codeId'] <= 0)
                    throw new Exception('codeId要大于0');
                if (!is_numeric($post['freight']))
                    throw new Exception('运费必须是数字');
                $order = Order::model()->find('code=:cd', array(':cd' => $post['code']));
                if(!empty($order['machine_id'])){
                    $machine = FreshMachine::model()->findByPk($order['machine_id']);
                    if($machine){
                        if($machine['member_id']==$member['id']){
                            $this->_error('商家不可购买自己的商品！');
                        }
                    }
                }
                $status = $order['status'];
                if($status == Order::STATUS_CANCEL){
                     $this->_error('订单已失效，请重新下单！');
                }
                $status = Tool::getConfig('amountlimit', 'isEnable');         
                if ($status == AmountLimitConfigForm::STATUS_ENABLE) {
//                    $order = Order::model()->find('code=:cd', array(':cd' => $post['code']));
//                    $sid = empty($order['store_id']) ? (empty($order['machine_id']) ? '' : $order['machine_id']) : $order['store_id'];
                      if($order['type'] == Order::TYPE_SUPERMARK){
                        $sid['store_id'] = $order['store_id'];
                    }else{
                        $sid['machine_id'] = $order['machine_id'];
                    }
                    if (!empty($sid)) {
                        // 检查消费限额
                        $rs = Member::checkAccountLimit($post['amount'], $sid, $member['id']);
                        if (is_array($rs)) {
                            $this->_error($rs['code']);
                        }
                    }
                }
                //检查余额
                $member_amount = AccountBalance::getMemberTodayAmount($member['sku_number']);

                if ($member_amount < $post['amount']) {
                    throw new Exception('用户余额不足');
                }

                //组装数据
                $apiLogData['order_id'] = $post['codeId'];
                $apiLogData['order_code'] = $post['code'];
                $apiLogData['operate_type'] = AccountFlow::OPERATE_TYPE_SKU_PAY;
                $apiLogData['transaction_type'] = AccountFlow::TRANSACTION_TYPE_CONSUME;
                $apiLogData['remark'] = isset($post['remark']) ? $post['remark'] : '订单支付';
                $apiLogData['money'] = $post['amount'];
                $apiLogData['account_id'] = $member['id'];
                $apiLogData['sku_number'] = $member['sku_number'];
                $apiLogData['gai_number'] = $member['gai_number'];
                $apiLogData['freight'] = $post['freight'];
                $apiLogData['data'] = json_encode($post);
                $apiLogData['create_time'] = time();
                //处理金额
                if (AccountBalance::changeBalance($apiLogData)) {
  
                    //处理订单
                    $params = array('code' => $apiLogData['order_code'], 'pay_type' => $post['pay_type']);
                    $this->_output($this->requestSku($params, 'sOrder/pay/', 105));
                    
                } else {
                    throw new Exception('事务失败');
                }
            } else {
                throw new Exception('错误的访问方式');
            }
        } catch (Exception $e) {
            $this->_error($e->getMessage());
        }
    }

    /**
     * 来自sku后台的请求
     */
    public function actionSign() {
        try {
            if ($this->isPost()) {
                $requireParams = array('memberId', 'gwNumber', 'code', 'operateType', 'transactionType', 'money', 'freight',
                    'merchantMemberId', 'merchantGwNumber', 'costPrice',
                );
                $data = $this->getValidateData($requireParams);

                if (!is_numeric($data['money'])) {
                    throw new Exception('参数有误');
                }
                if ($data['money'] <= 0) {
                    throw new Exception('金额必须大于0');
                }
                if (!is_numeric($data['costPrice'])) {
                    throw new Exception('参数有误');
                }
                if ($data['costPrice'] < 0 || $data['costPrice'] > $data['money']) {
                    throw new Exception('供货价有误');
                }

                $member = Yii::app()->db->createCommand()->select(array('id', 'sku_number', 'gai_number'))->from(Member::model()->tableName())->where('id=:id', array(':id' => $data['memberId']))->queryRow();
                if (empty($member)) {
                    Yii::log('用户不存在' . var_export($_POST, true) . var_export($data, true));
                    throw new Exception('用户不存在');
                }
//                if ($member['gai_number'] != $data['gwNumber']) {
//                    throw new Exception('账号异常');
//                }
                //
                $merchantMember = Yii::app()->db->createCommand()->select(array('id', 'sku_number', 'gai_number'))->from(Member::model()->tableName())->where('id=:id', array(':id' => $data['merchantMemberId']))->queryRow();
                if (empty($merchantMember)) {
                    throw new Exception('商家账号不存在');
                }
//                if ($merchantMember['gai_number'] != $data['merchantGwNumber']) {
//                    throw new Exception('商家账号错误');
//                }
                $data['merchantSkuNumber'] = $merchantMember['sku_number'];

                $apiLogData['order_id'] = $data['codeId'];
                $apiLogData['order_code'] = $data['code'];
                $apiLogData['operate_type'] = $data['operateType'];
                $apiLogData['transaction_type'] = $data['transactionType'];
                $apiLogData['account_id'] = $member['id'];
                $apiLogData['gai_number'] = $member['gai_number'];
                $apiLogData['sku_number'] = $member['sku_number'];
                $apiLogData['money'] = $data['money'];
                $apiLogData['freight'] = $data['freight'];
                $apiLogData['remark'] = isset($data['remark']) ? $data['remark'] : '订单签收';
                $apiLogData['callback'] = isset($data['callback']) ? $data['callback'] : '';
                $apiLogData['is_callback'] = empty($apiLogData['callback']) ? 0 : 1;
                $apiLogData['data'] = json_encode($data);
                if (AccountBalance::changeBalance($apiLogData)) {
                    $return = isset($data['other']) ? array('other' => $data['other']) : '';
                    Tool::success($return);
                } else {
                    throw new Exception('事务失败');
                }
            } else {
                throw new Exception('错误的访问方式');
            }
        } catch (Exception $e) {
            Tool::error(404, $e->getMessage());
        }
    }

    /**
     * 来自sku后台的请求
     * 取消订单，返还扣钱金额并写相关流水日志
     */
    public function actionCancelOrder() {
        try {
            if (!$this->isPost())
                throw new Exception('错误的访问方式');

            $requireParams = array('codeId', 'code', 'money', 'freight', 'memberId', 'gwNumber', 'operateType', 'transactionType');
            $data = $this->getValidateData($requireParams);
            $memberInfo = Yii::app()->db->createCommand()->select(array('id', 'sku_number', 'gai_number'))->from(Member::model()->tableName())->where('id=:id', array(':id' => $data['memberId']))->queryRow();

            if (empty($memberInfo))
                throw new Exception('用户不存在');
//            if ($memberInfo['gai_number'] != $data['gwNumber']) throw new Exception('账号错误');


            $apiLogData['money'] = $data['money'];
            $apiLogData['freight'] = $data['freight'];
            $apiLogData['order_code'] = $data['code'];
            $apiLogData['order_id'] = $data['codeId'];
            $apiLogData['account_id'] = $memberInfo['id'];
            $apiLogData['gai_number'] = $memberInfo['gai_number'];
            $apiLogData['sku_number'] = $memberInfo['sku_number'];
            $apiLogData['operate_type'] = $data['operateType'];
            $apiLogData['transaction_type'] = $data['transactionType'];

            $apiLogData['callback'] = isset($data['callback']) ? $data['callback'] : '';
            $apiLogData['is_callback'] = empty($apiLogData['callback']) ? 0 : 1;
            $apiLogData['remark'] = isset($data['remark']) ? $data['remark'] : '订单取消';
            $apiLogData['data'] = json_encode($data);
            if (AccountBalance::changeBalance($apiLogData)) {
                $return = isset($data['other']) ? array('other' => $data['other']) : '';
                Tool::success($return);
            } else {
                throw new Exception('处理失败');
            }
        } catch (Exception $e) {
            Yii::log($e->getMessage());
            Tool::error(404, $e->getMessage());
        }
    }

//    public function actionTest(){
//        $status = Tool::getConfig('amountlimit', 'isEnable');
//        $s = Member::checkAccountLimit(20, 1,3);
//        var_dump($s);die;
//    }
}
