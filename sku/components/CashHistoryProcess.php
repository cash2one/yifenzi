<?php
/**
 * 提醒、兑现 处理 
 * @author zhenjun_xu <412530435@qq.com>
 * Date: 14-4-19
 * Time: 下午3:58
 */
class CashHistoryProcess {

    /**
     * 商家提现申请
     * @param array $data CashHistory data
     * @param array $member Member data
     * @return bool
     */
    public static function enterpriseCash(Array $data, Array $member) {
        $time = $data['apply_time'];
        $money = $data['money'] + $data['money'] * $data['factorage'] / 100; // 算出要提现的金额，加上手续费

        $transaction = Yii::app()->db->beginTransaction();
        try {
            // 会员商家账户
            $array = array(
                'account_id' => $member['id'],
                'type' => AccountBalance::TYPE_MERCHANT,
                'sku_number' => $member['sku_number'],
            );
            $enterpriseAccount = AccountBalance::findRecord($array);

            if($enterpriseAccount['today_amount']<$money){
                throw new Exception("金额校验失败");
            }
            // 会员冻结账户
            $array = array(
                'account_id' => $member['id'],
                'type' => AccountBalance::TYPE_FREEZE,
                'sku_number' => $member['sku_number'],
            );
            $freezeAccount = AccountBalance::findRecord($array);

            // 当月的流水表
            $monthTable = AccountFlow::monthTable();
            //获取可提现账户的余额，商家+代理
            $currentBalance = AccountBalance::getWithdrawBalance($member['id']);
            //'current_balance'=>$currentBalance,
            //商家流水
            $credit = array(
                'account_id' => $enterpriseAccount['account_id'],
                'sku_number' => $enterpriseAccount['sku_number'],
                'create_time' => $time,
                'type' => $enterpriseAccount['type'],
                'debit_amount' => $money,
                'operate_type' => AccountFlow::OPERATE_TYPE_CASH_APPLY,
                'order_id' => $data['id'],
                'order_code' => $data['code'],
                'remark' => '商家提现',
                'node' => AccountFlow::BUSINESS_NODE_CASH_APPLY,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_CASH,
                'current_balance'=>$currentBalance,
            );

            // 冻结账户流水
            $debit = array(
                'account_id' => $freezeAccount['account_id'],
                'sku_number' => $freezeAccount['sku_number'],
                'type' => $freezeAccount['type'],
                'credit_amount' => $money,
                'operate_type' => AccountFlow::OPERATE_TYPE_CASH_APPLY,
                'order_id' => $data['id'],
                'order_code' => $data['code'],
                'remark' => '商家提现冻结',
                'node' => AccountFlow::BUSINESS_NODE_CASH_CHECK,
                'transaction_type' => AccountFlow::TRANSACTION_TYPE_CASH,
                'current_balance'=>$currentBalance,
            );
            // 冻结账余额更新
            AccountBalance::calculate(array('today_amount' => $money), $freezeAccount['id']);
            // 企业会员余额更新
            AccountBalance::calculate(array('today_amount' => -$money), $enterpriseAccount['id']);
            //插入兑现数据
            if (!isset($data['id'])) {
                Yii::app()->db->createCommand()->insert('{{cash_history}}', $data);
                $cashHistoryId = Yii::app()->db->lastInsertID;
                $debit['order_id'] = $cashHistoryId;
                $credit['order_id'] = $cashHistoryId;
            }

            // 记录月流水表
            Yii::app()->db->createCommand()->insert($monthTable, AccountFlow::mergeField($credit));
            Yii::app()->db->createCommand()->insert($monthTable, AccountFlow::mergeField($debit));

            $transaction->commit();
            $flag = true;
        } catch (Exception $e) {
            $transaction->rollBack();
            $flag = false;
        }
        return $flag;
    }

    /**
     * 提现成功处理
     * @param array $data CashHistory
     * @param array $member Member
     * @return boolean
     * @throws Exception
     */
    public static function enterpriseCashEnd($data, $member) {
        $transaction = Yii::app()->db->beginTransaction();
        try {
        // 当月的流水表
        $monthTable = AccountFlow::monthTable();
        $money = $data['money'] + $data['money'] * $data['factorage'] / 100; // 算出要兑现的金额，加上手续费
        // 会员冻结账户
        $array = array(
            'account_id' => $member['id'],
            'type' => AccountBalance::TYPE_FREEZE,
            'sku_number' => $member['sku_number'],
        );
        $freezeAccount = AccountBalance::findRecord($array);
        //获取可提现账户的余额，商家+代理
        $currentBalance = AccountBalance::getWithdrawBalance($member['id']);
        // 冻结账户流水
        $debit = array(
            'account_id' => $freezeAccount['account_id'],
            'sku_number' => $freezeAccount['sku_number'],
            'type' => $freezeAccount['type'],
            'debit_amount' => $money,
            'operate_type' => AccountFlow::OPERATE_TYPE_CASH_SUCCESS,
            'order_id' => $data['id'],
            'order_code' => $data['code'],
            'remark' => '商家提现成功',
            'node' => AccountFlow::BUSINESS_NODE_CASH_CONFIRM,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_CASH,
            'current_balance'=>$currentBalance!==null?$currentBalance:0,
        );

            //加锁
            self::_checkStatus($data);
            // 冻结账户
            AccountBalance::calculate(array('today_amount' => -$money), $freezeAccount['id']);
            // 记录月流水表
            Yii::app()->db->createCommand()->insert($monthTable, AccountFlow::mergeField($debit));
            //修改状态
            Yii::app()->db->createCommand()->
                update('{{cash_history}}', array('update_time'=>time(),'reason' => $data['reason'], 'status' => $data['status']), "id='{$data['id']}'");
            //检测借贷平衡

            $transaction->commit();
            $flag = true;
        } catch (Exception $e) {
            $transaction->rollBack();
            Tool::pr($e->getMessage());exit;
            $flag = false;
        }
        return $flag;
    }


    /**
     * 商家提现失败
     * @param array $data CashHistory data
     * @param array $member Member data
     * @return bool
     */
    public static function enterpriseCashFailed($data, $member) {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            
        $money = $data['money'] + $data['money'] * $data['factorage'] / 100; // 算出要提现的金额，加上手续费
        $time = $data['apply_time'];

        // 会员商家账户
        $array = array(
            'account_id' => $member['id'],
            'type' => AccountBalance::TYPE_MERCHANT,
            'sku_number' => $member['sku_number'],
        );
        $enterpriseAccount = AccountBalance::findRecord($array);

        // 会员冻结账户
        $array = array(
            'account_id' => $member['id'],
            'type' => AccountBalance::TYPE_FREEZE,
            'sku_number' => $member['sku_number'],
        );
        $freezeAccount = AccountBalance::findRecord($array);

        // 当月的流水表
        $monthTable = AccountFlow::monthTable();
        //获取可提现账户的余额，商家+代理
        $currentBalance = AccountBalance::getWithdrawBalance($member['id']);
        //商家流水
        $credit = array(
            'account_id' => $enterpriseAccount['account_id'],
            'sku_number' => $enterpriseAccount['sku_number'],
            'type' => $enterpriseAccount['type'],
            'debit_amount' => -$money,
            'operate_type' => AccountFlow::OPERATE_TYPE_CASH_CANCEL,
            'order_id' => $data['id'],
            'order_code' => $data['code'],
            'remark' => '商家提现失败',
            'node' => AccountFlow::BUSINESS_NODE_CASH_BACK,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_CASH_CANCEL,
            'current_balance'=>$currentBalance!==null ? $currentBalance : 0,
        );

        // 冻结账户流水
        $debit = array(
            'account_id' => $freezeAccount['account_id'],
            'sku_number' => $freezeAccount['sku_number'],
            'type' => $freezeAccount['type'],
            'credit_amount' => -$money,
            'operate_type' => AccountFlow::OPERATE_TYPE_CASH_CANCEL,
            'order_id' => $data['id'],
            'order_code' => $data['code'],
            'remark' => '商家提现冻结',
            'node' => AccountFlow::BUSINESS_NODE_CASH_CANCEL,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_CASH_CANCEL,
            'current_balance'=>$currentBalance!==null ? $currentBalance : 0,
        );
        
            //加锁
            self::_checkStatus($data);
            // 冻结账余额更新
            AccountBalance::calculate(array('today_amount' => -$money), $freezeAccount['id']);
            // 企业会员余额更新
            AccountBalance::calculate(array('today_amount' => $money), $enterpriseAccount['id']);
            // 记录月流水表
            Yii::app()->db->createCommand()->insert($monthTable, AccountFlow::mergeField($credit));
            Yii::app()->db->createCommand()->insert($monthTable, AccountFlow::mergeField($debit));
            
            //修改状态
            Yii::app()->db->createCommand()->
                    update('{{cash_history}}', array('update_time'=>time(),'reason' => $data['reason'], 'status' => $data['status']), "id='{$data['id']}'");

            // 检测借贷平衡

            $transaction->commit();
            $flag = true;
        } catch (Exception $e) {
            $transaction->rollBack();
            Tool::pr($e->getMessage());exit;
            $flag = false;
        }
        return $flag;
    }


    /**
     * 检查重复提交
     * @param $data
     * @throws Exception
     */
    private static function  _checkStatus($data){
        $checkStatus = Yii::app()->db->createCommand('select status from {{cash_history}} where id='.$data['id'].' for update')->queryRow();
        if(!$checkStatus){
            throw new Exception('找不到数据');
        }
        if($checkStatus['status']==$data['status']){
            throw new Exception('重复提交了');
        }
        if($checkStatus['status']>$data['status']){
            throw new Exception("不能做回滚操作");
        }
    }

}
