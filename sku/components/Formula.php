<?php

/**
 * Class Formula
 */
class Formula {

    const VER = 1;//版本
    const SIGN_KEY = '5e753ba7a409f4c02892';

    public static function _createDistributionConfig($order,$member,$consumerBalance,$consumerDistributionBalance,$sellerDistributionBalance){
        $func = 'v'.self::VER.'_makeConfig';
        $distribution_config = forward_static_call(array('Formula',$func),$order,$member,$consumerBalance,$consumerDistributionBalance,$sellerDistributionBalance);
        return $distribution_config;
    }

    public static function _sign($config){
        $key = $config['key'];
        unset($config['key']);
        if(!self::checkKey($config,$key)){
            return false;
        }
        $func = 'v'.$config['version'].'_readConfig';
        $result = forward_static_call(array('Formula',$func),$config);
//        var_dump($result);
//        exit;
	    return $result;
    }

    /**
     * 生产key
     * @param $ary
     * @return string
     */
    public static function makeKey($ary){
        return substr(md5(CJSON::encode($ary).self::SIGN_KEY),5,20);
    }

    /**
     * 验证key
     * @param $ary
     * @param $key
     * @return bool
     */
    public static function checkKey($ary,$key){
        return substr(md5(CJSON::encode($ary).self::SIGN_KEY),5,20) == $key;
    }


    public static function v1_makeConfig($order,$member,$consumerBalance,$consumerDistributionBalance,$sellerDistributionBalance){
        //折扣差
        $setting = Tool::getConfig('assign');
        $distributionPercentTotal = $profitTotal = 0.00;
        if($setting['isEnable']){
            // 获取折扣差
            $orderGoods = Yii::app()->db->createCommand()
                ->select('num,supply_price,price')
                ->from(OrdersGoods::model()->tableName())
                ->where('order_id=:id',array(':id'=>$order['id']))
                ->queryAll();
            foreach($orderGoods as $goods){
                // 数量*(单价-供货价)
                $profitTotal = bcadd($profitTotal,bcmul($goods['num'],bcsub($goods['price'],$goods['supply_price'],8),8),8);
            }
        }
        //比例分配
        if($consumerBalance['today_amount'] > 0){
            $distributionPercentTotal = bcmul(bcdiv($order['total_price'],$consumerBalance['today_amount'],8),$consumerDistributionBalance['today_amount'],8);
        }
        // 结构体
        $distribution_config = array(
            'version' => self::VER,
            //余额
            'balance' => array(
                'consumer' => $consumerBalance['today_amount'],
                'consumer_distribution' => $consumerDistributionBalance['today_amount'],
                'seller_distribution' => $sellerDistributionBalance['today_amount']
            ),
            'profit' => array(
                'difference' => $profitTotal,
                'percent' => $distributionPercentTotal
            ),
            'order' => array(
                'id' => $order['id'],
                'code' => $order['code'],
                'member_id' => $member['id'],
                'sku_number' => $member['sku_number']
            ),
            //折扣差分配配置
            'setting' => $setting,
            //分配明细
            'distribution' => array(
                //按折扣差分配
                'difference' => Formula::v1_distributionDifference($member,$profitTotal),
                //按比率分配
                'percent' => Formula::v1_distributionPercent($member,$distributionPercentTotal)
            )
        );
        $distribution_config['key'] = self::makeKey($distribution_config);
        return $distribution_config;
    }

    /**
     * 按折扣差计算分配
     * 分配各角色的利润后,剩余值为盖网收入
     * @param $member
     * @param float $profit 折扣差利润
     * @return array
     */
    public static function v1_distributionDifference($member,$profit=0.00){
        $memberReferrals = $memberIncome = 0.00;
        $profit_distribution = bcmul($profit,0.9,8);//分配金额
        $result = array(
            'memberIncome' => array(),
            'memberReferrals' => array(),
            'storeReferrals' => array(),
            'gaiIncome' => 0.00,
        );
        // 分配计算
        if($profit && $profit_distribution >= 0.01){
            /**
             * **************************************
             * 根据后台规则计算各个角色的分配金额
             * **************************************
             */
            $distribution_config = Tool::getConfig('assign');
            if (!empty($distribution_config) && $distribution_config['isEnable']){
                // 积分比例换算

                // 消费会员分配
                if($distribution_config['skuMemberIncome']){
                    $memberIncome = bcmul($distribution_config['skuMemberIncome'],$profit_distribution,2);
                    if($memberIncome >= 0.01){
                        $result['memberIncome']['id'] = $member['id'];
                        $result['memberIncome']['sku_number'] = $member['sku_number'];
                        $result['memberIncome']['amount'] = $memberIncome;
                    }
                }
                // 消费会员推荐者分配
                if($distribution_config['skuMemberReferrals']){
                    $memberReferrals = bcmul($distribution_config['skuMemberReferrals'],$profit_distribution,2);
                    if($memberReferrals >= 0.01){
                        //获取盖网编号
                        if(!isset($member['gai_number'])){
                            $member['gai_number'] = Yii::app()->db->createCommand()
                                ->select('gai_number')->from(Member::model()->tableName())
                                ->where('id=:id',array(':id'=>$member['id']))
                                ->queryScalar();
                        }
                        //获取盖网的推荐关系
                        if($member['gai_number']){
                            $apiMember = new ApiMember();
                            $referralsAry = $apiMember->getReferralsInfo($member['gai_number']);//同步账号
                            // 有推荐关系则分配
                            if(!empty($referralsAry)){
                                $referralsInfo = Member::getMemberInfoByGaiNumber($referralsAry['gai_number']);
                                $result['memberReferrals']['id'] = $referralsInfo['id'];
                                $result['memberReferrals']['sku_number'] = $referralsInfo['sku_number'];
                                $result['memberReferrals']['amount'] = $memberReferrals;
                            }
                        }
                    }
                }
                // 店铺推荐者分配
                if($distribution_config['skuStoreReferrals']){
                    //暂时不确定关系
                    //$ = bcmul($distribution_config['skuStoreReferrals'],$profit_distribution,8);
                }
            }
        }
        // 盖网收益
        $result['gaiIncome'] = bcsub(bcsub($profit,$memberIncome,8),$memberReferrals,2);
        return $result;
    }

    /**
     * 按比例计算分配
     * 会员有分配,盖网才有收益
     * @param $member
     * @param $distribution_money
     * @return array
     */
    public static function v1_distributionPercent($member,$distribution_money){
        $memberDistribution = 0.00;
        $result = array(
            'memberDistribution' => array(),
            'memberReferrals' => array(),
            'storeReferrals' => array(),
            'gaiIncome' => 0.00,
        );

        // 分配计算
        if ($distribution_money > 0){
            //获取gai_number
            if(!isset($member['gai_number'])){
                $member['gai_number'] = Yii::app()->db->createCommand()
                    ->select('gai_number')->from(Member::model()->tableName())
                    ->where('id=:id',array(':id'=>$member['id']))
                    ->queryScalar();
            }
            Yii::log('formula $member'.var_export($member,true));
            //获取盖网的推荐关系
            if($member['gai_number']){
                $apiMember = new ApiMember();
                $referralsAry = $apiMember->getReferralsInfo($member['gai_number']);//同步账号
                Yii::log('formula $referralsAry'.var_export($referralsAry,true));
                // 有推荐关系则分配
                if(!empty($referralsAry)){
                    $memberIncome = bcmul($distribution_money,0.9,2);
                    if($memberIncome >= 0.01){
                        // 盖网收益
                        $gaiIncome = bcsub($distribution_money,$memberIncome,2);
                        if($gaiIncome >= 0.01){
                            $result['gaiIncome'] = $gaiIncome;
                            $memberDistribution = bcadd($memberDistribution,$gaiIncome,2);
                        }
                        // 会员收益
                        $referralsInfo = Member::getMemberInfoByGaiNumber($referralsAry['gai_number']);
                        $result['memberReferrals']['id'] = $referralsInfo['id'];
                        $result['memberReferrals']['sku_number'] = $referralsInfo['sku_number'];
                        $result['memberReferrals']['amount'] = $memberIncome;
                        // 待分配扣钱
                        $memberDistribution = bcadd($memberDistribution,$memberIncome,2);
                        $result['memberDistribution'] = array(
                            'id' => $member['id'],
                            'sku_number' => $member['sku_number'],
                            'amount' => $memberDistribution
                        );
                    }
                }
            }
        }

        return $result;
    }


    public static function v1_readConfig($config){
        $flow_table_name = AccountFlow::monthTable();
        $time = time();
        $gaiIncome = 0.00;
//        var_dump($config['balance']);
//        var_dump($config['setting']);
//        var_dump($config['distribution']);

        $difference = $config['distribution']['difference'];
        $percent = $config['distribution']['percent'];
        // 折扣差分配
        if(!empty($difference)){
            foreach($difference as $key => $val){
                if(!empty($val) && $val != false){
                    if($key == 'gaiIncome'){
                        $gaiIncome = bcadd($gaiIncome,$val,8);
                        continue;
                    }

                    $account = AccountBalance::findRecord(array(
                        'account_id' => $val['id'],
                        'type' => AccountBalance::TYPE_CONSUME,
                        'sku_number' => $val['sku_number'],
                    ));
                    // 分配
                    AccountBalance::calculate(array('today_amount' => $val['amount']), $account['id']);
                    // 流水
                    $flow = array();
                    $flow['account_id'] = $val['id'];
                    $flow['sku_number'] = $val['sku_number'];
                    $flow['credit_amount'] = $val['amount'];
                    $flow['order_id'] = $config['order']['id'];
                    $flow['order_code'] = $config['order']['code'];
                    $flow['type'] = AccountFlow::TYPE_CONSUME;
                    $flow['operate_type'] = AccountFlow::OPERATE_TYPE_SKU_SIGN;
                    $flow['transaction_type'] = AccountFlow::TRANSACTION_TYPE_DISTRIBUTION;
                    $flow['remark'] = '订单签收';
                    $flow['current_balance'] = $account['today_amount'];

                    if($key == 'memberIncome'){
                        $flow['node'] = AccountFlow::BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_MEMBER;
                    }elseif($key == 'memberReferrals'){
                        $flow['node'] = AccountFlow::BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_OTHER;
                    }elseif($key == 'storeReferrals'){
                        $flow['node'] = AccountFlow::BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_OTHER;
                    }
                    Yii::app()->db->createCommand()->insert($flow_table_name, AccountFlow::mergeField($flow,$time));
                }
            }
        }

        // 比例分配
        if(!empty($percent)){
            foreach($percent as $key => $val){
                if(!empty($val) && $val != false){
                    if($key == 'gaiIncome'){
                        $gaiIncome = bcadd($gaiIncome,$val,8);
                        continue;
                    }

                    if($key == 'memberDistribution'){
                        //待分配扣钱
                        $account = array();
                        $account = AccountBalance::findRecord(array(
                            'account_id' => $config['order']['member_id'],
                            'type' => AccountBalance::TYPE_GUADAN_DAIFENPEI_XIAOFEI,
                            'sku_number' => $config['order']['sku_number'],
                        ));
                        AccountBalance::calculate(array('today_amount' => -$val['amount']), $account['id']);
                        // 流水
                        $flow = array();
                        $flow['account_id'] = $config['order']['member_id'];
                        $flow['sku_number'] = $config['order']['sku_number'];
                        $flow['debit_amount'] = $val['amount'];
                        $flow['order_id'] = $config['order']['id'];
                        $flow['order_code'] = $config['order']['code'];
                        $flow['type'] = AccountFlow::TYPE_CONSUME;
                        $flow['operate_type'] = AccountFlow::OPERATE_TYPE_SKU_SIGN;
                        $flow['transaction_type'] = AccountFlow::TRANSACTION_TYPE_DISTRIBUTION;
                        $flow['remark'] = '订单签收';
                        $flow['current_balance'] = $account['today_amount'];
                        $flow['node'] = AccountFlow::BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_MEMBER_REFERENCE_UNDISTRIBUTED;//3606
                        Yii::app()->db->createCommand()->insert($flow_table_name, AccountFlow::mergeField($flow,$time));

                    }elseif($key == 'memberReferrals'){
                        //推荐人加钱
                        $account = array();
                        $account = AccountBalance::findRecord(array(
                            'account_id' => $val['id'],
                            'type' => AccountBalance::TYPE_CONSUME,
                            'sku_number' => $val['sku_number'],
                        ));
                        AccountBalance::calculate(array('today_amount' => $val['amount']), $account['id']);
                        // 流水
                        $flow = array();
                        $flow['account_id'] = $val['id'];
                        $flow['sku_number'] = $val['sku_number'];
                        $flow['credit_amount'] = $val['amount'];
                        $flow['order_id'] = $config['order']['id'];
                        $flow['order_code'] = $config['order']['code'];
                        $flow['type'] = AccountFlow::TYPE_CONSUME;
                        $flow['operate_type'] = AccountFlow::OPERATE_TYPE_SKU_SIGN;
                        $flow['transaction_type'] = AccountFlow::TRANSACTION_TYPE_DISTRIBUTION;
                        $flow['remark'] = '订单签收';
                        $flow['current_balance'] = $account['today_amount'];
                        $flow['node'] = AccountFlow::BUSINESS_NODE_SKU_SIGN_DISTRIBUTION_MEMBER_REFERENCE;//3616
                        Yii::app()->db->createCommand()->insert($flow_table_name, AccountFlow::mergeField($flow,$time));

                    }elseif($key == 'storeReferrals'){

                    }
                }
            }
        }

        if($gaiIncome){
            $balanceCommon = CommonAccount::getAccount(CommonAccount::TYPE_GAI_INCOME, AccountBalance::TYPE_COMMON);
            AccountBalance::calculate(array('today_amount' => $gaiIncome), $balanceCommon['id']); //收益加钱

            $flow = array();
            $flow['account_id'] = $balanceCommon['account_id'];
            $flow['sku_number'] = $balanceCommon['sku_number'];
            $flow['credit_amount'] = $gaiIncome;
            $flow['order_id'] = $config['order']['id'];
            $flow['order_code'] = $config['order']['code'];
            $flow['type'] = AccountFlow::TYPE_COMMON;
            $flow['operate_type'] = AccountFlow::OPERATE_TYPE_SKU_SIGN;
            $flow['transaction_type'] = AccountFlow::TRANSACTION_TYPE_DISTRIBUTION;
            $flow['remark'] = '订单签收';
            $flow['current_balance'] = $balanceCommon['today_amount'];
            $flow['node'] = AccountFlow::BUSINESS_NODE_SKU_SIGN_PROFIT;//3612
            Yii::app()->db->createCommand()->insert($flow_table_name, AccountFlow::mergeField($flow,$time));
        }
        return true;
    }

}
