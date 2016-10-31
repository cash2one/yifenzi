<?php
/**
 * Created by PhpStorm.
 * User: Gemall
 * Date: 2016/8/23
 * Time: 9:52
 */
class PayController extends YfzController
{
    public function actionIndex()
    {

        if (!IS_DEVELOPMENT && !YII_DEBUG) {
            exit('no test');
        }

        header("Content-type:text/html;charset=utf-8");
        try {
            if (!isset($_GET['gw']) || $_GET['gw'] == false) {
                print_r('请输入gw号');
                exit;
            }
            $gaiNumbers = $this->getParam('gw');
            $money = $this->getParam('money', 100000);
            $gaiNumbers = explode(',', $gaiNumbers);
            foreach ($gaiNumbers as $gaiNumber) {
                $member = Member::getByGwNumber($gaiNumber);
                if (empty($member)) {
                    $memberInfo = Yii::app()->db->createCommand('select * from gaiwang.gw_member where gai_number=:gw')->bindValues(array(':gw' => $gaiNumber))->queryRow();
                    if (empty($memberInfo)) {
                        echo '<br/>' . $gaiNumber . ' <span style="color:red;">盖网账号不存在</span>';
                        continue;
                    }
                    if (Member::syncFromGw($memberInfo)) {
                        echo '<br/>' . $gaiNumber . ' 账号同步成功';
                        $member = Member::getByGwNumber($gaiNumber);
                    } else {
                        echo '<br/>' . $gaiNumber . ' <span style="color:red;">账号同步失败,盖网账号不存在</span>';
                        continue;
                    }
                }
                $array = array(
                    'account_id' => $member->id,
                    'type' => AccountBalance::TYPE_CONSUME,
                    'sku_number' => $member->sku_number
                );
                $accountBalance = AccountBalance::findRecord($array);//消费账户

                //消费余额
                AccountBalance::calculate(array('today_amount' => $money), $accountBalance['id']);
                echo '<br/>' . $gaiNumber . ' added ' . number_format($money, 2);

                // 会员充值流水 贷 +
                $MemberCredit = array(
                    'account_id' => $accountBalance['account_id'],
                    'sku_number' => $accountBalance['sku_number'],
                    'type' => AccountFlow::TYPE_CONSUME,
                    'current_balance' => $accountBalance['today_amount'],
                    'credit_amount' => $money,
                    'operate_type' => AccountFlow::OPERATE_TYPE_EBANK_RECHARGE,
                    'order_id' => 0,
                    'order_code' => 0,
                    'remark' => $gaiNumber . ' 人工充值 ' . $money,
                    'transaction_type' => AccountFlow::TRANSACTION_TYPE_RECHARGE,
                    'flag' => 0
                );

                $monthTable = AccountFlow::monthTable();
                Yii::app()->db->createCommand()->insert($monthTable, AccountFlow::mergeField($MemberCredit));
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
        echo '<br/>end';
    }
}