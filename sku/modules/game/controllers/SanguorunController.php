<?php
/**
 * @author: chen.luo
 * Date: 2015/8/14 17:34
 * 三国跑跑游戏专用接口
 */

class SanguorunController extends GameBaseController
{
    public function actionIndex()
    {
        $type = $this->getQuery('type');
        $msgData = $this->getPost('msgData');
        $msgData = str_replace('%2B', '+', $msgData);
        $msgData = $this->decrypt($msgData);

        if ($msgData == null) {
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => 'post数据为空'));
        }

        switch ($type) {
            case 'gameLogin':
                $this->getLoginData($msgData);
                break;
            case 'getGameData':
                $this->getGameData($msgData);
                break;
            case 'getMatchResult':
                $this->getResultData($msgData);
                break;
            case 'exchangeGold':
                $this->getExchangeData($msgData);
                break;
            case "newPlayerReward":
                $this->updateReward($msgData);
                break;
            default:
                $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '地址栏参数type为空'));
        }
    }

    /**
     * 账号登录、获取信息
     * @param $msgData
     * @author xiaoyan.luo
     */
    private function getLoginData($msgData)
    {
        if (!Validator::isMobile($msgData['account']) && !Validator::isGaiNumber($msgData['account'])){
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '账号输入格式不正确'
            ));
        }

        $exists = Member::model()->exists('gai_number = :params or mobile = :params',array(':params' => $msgData['account']));
        if (!$exists){
            $memberInfo = Yii::app()->gw->createCommand()
                ->select('m.id,m.gai_number,m.referrals_id,m.username,m.password,m.salt,m.sex,m.real_name,m.password2,m.password3,
                        m.birthday,m.mobile,m.country_id,m.province_id,m.district_id,m.street,m.register_time,m.register_type,
                        m.head_portrait,m.status,m.nickname,m.referrals_time,mt.ratio')
                ->from('gw_member m')
                ->join('gw_member_type mt','m.type_id = mt.id')
                ->where('gai_number = :params or mobile = :params', array(':params' => $msgData['account']))
                ->queryAll();
            if($memberInfo == false){
                $this->returnResult(GameModule::RESULT_CODE_0, array(
                    'gameMsgId' => $msgData['gameMsgId'],
                    'errorMsg' => '用户不存在'
                ));
            }
            //同步盖网用户到sku
            $model = new Member();
            $memberInfo = $memberInfo[0];
            $skuMemberData = $memberInfo;
            $skuMemberData['gai_member_id'] = $memberInfo['id'];
            $skuMemberData['sku_number'] = $model->generateNumber();
            unset($skuMemberData['id']);
            Yii::app()->db->createCommand()->insert('{{member}}', $skuMemberData);
        }

        $member = Member::model()->findAll(array(
            'select' => array('id,gai_number,salt,password,status,ratio,nickname,gai_member_id'),
            'condition' => 'gai_number = :params or mobile = :params',
            'params' => array(':params' => $msgData['account'])
        ));

        if (count($member) > 1) {
            $gaiNumberList = array();
            foreach ($member as $key => $value) {
                $gaiNumberList[] = $value['gai_number'];
            }
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'gaiNumber' => $gaiNumberList,
                'errorMsg' => "绑定多个盖网号"
            ));
        }

        $member = $member[0];
        if ($member['status'] != Member::STATUS_NO_ACTIVE && $member['status'] != Member::STATUS_NORMAL){
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '此用户已经删除或者除名，不能登陆'
            ));
        }

        $countLoginError = 0;//初始错误登录次数为0
        $redis = Yii::app()->redis;
        if ($redis->exists("userLoginCount:{$member['gai_member_id']}")) {
            $countLoginError = $redis->get("userLoginCount:{$member['id']}");
        }
        if (!CPasswordHelper::verifyPassword($msgData['password'] . $member['salt'], $member['password'])) {
            $countLoginError++;
            $redis->set("userLoginCount:{$member['gai_member_id']}", $countLoginError, 86400);
            if ($redis->get("userLoginCount:{$member['gai_member_id']}") > 3) {
                $captchas = rand(1000, 9999); //生成4位数字验证码
                $redis->set("userLoginCaptcha:{$member['gai_member_id']}", $captchas, 86400);
                $this->returnResult(GameModule::RESULT_CODE_5, array(
                    'gameMsgId' => $msgData['gameMsgId'],
                    'errorMsg' => '多次账号密码错误',
                    'captchas' => $captchas
                ));
            } else {
                $this->returnResult(GameModule::RESULT_CODE_4, array(
                    'gameMsgId' => $msgData['gameMsgId'],
                    'errorMsg' => '登录密码错误'
                ));
            }
        }

        if (isset($msgData['captchas']) && !empty($msgData['captchas'])) {
            $captchas = $redis->get("userLoginCaptcha:{$member['gai_member_id']}");
            if ($msgData['captchas'] != $captchas) {
                $captchas = rand(1000, 9999); //生成4位数字验证码
                $redis->set("userLoginCaptcha:{$member['gai_member_id']}", $captchas, 86400);
                $redis->set("userLoginCount:{$member['gai_member_id']}", $countLoginError + 1, 86400);
                $this->returnResult(GameModule::RESULT_CODE_5, array(
                    'gameMsgId' => $msgData['gameMsgId'],
                    'errorMsg' => '多次账号密码错误',
                    'captchas' => $captchas
                ));
            }
        }

        if ($redis->exists("userLoginCaptcha:{$member['gai_member_id']}")) $redis->remove("userLoginCaptcha:{$member['id']}");
        if ($redis->exists("userLoginCount:{$member['gai_member_id']}")) $redis->remove("userLoginCount:{$member['id']}");
        $token = GameMember::createToken($member['gai_member_id'],GameMemberInfo::GAME_TYPE_SANGUORUN);//生成token

        $memberData = array(
            'member_id' => $member['gai_member_id'],
            'gai_number' => $member['gai_number'],
            'gold_num' => 0,
            'login_time' => time(),
            'token' => $token,
        );
        $gameData = GameMember::model()->find(array(
            'condition' => 'member_id = :member_id',
            'select' => array('id,gold_num,token'),
            'params' => array(':member_id' => $member['gai_member_id'])
        ));
        if ($gameData == false) {
            Yii::app()->db->createCommand()->insert('game.gw_game_member',$memberData);
        } else {
            if ($gameData['token'] != $token) {
                GameMember::updateToken($gameData['id'], $token, $member['gai_member_id']);//更新token和登录时间
            }
        }

        //获取会员的游戏信息
        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id,is_first'),
            'params' => array(':member_id' => $member['gai_member_id'], ':game_type' => GameMemberInfo::GAME_TYPE_SANGUORUN)
        ));
        if ($gameInfo == false) {
            $info['member_id'] = $member['gai_member_id'];
            $info['game_type'] = GameMemberInfo::GAME_TYPE_SANGUORUN;
            $info['is_first'] = GameMemberInfo::IS_FIRST_GAME;
            $info['power'] = $info['max_score'] = $info['max_combo'] = 0;
            Yii::app()->db->createCommand()->insert('game.gw_game_member_info',$info);
        }

        AccountBalance::findRecord(array('account_id' => $member['gai_member_id'], 'type' => AccountBalance::TYPE_CONSUME, 'sku_number' => $member['sku_number']));
        $totalBalance = AccountBalance::getAccountAllBalance($member['sku_number'],AccountInfo::TYPE_CONSUME);//会员消费账户余额
        $totalIntegral = bcdiv($totalBalance, $member['ratio'],2);//会员积分数量

        $types = MemberType::getMemberType();//获取会员类型数据
        $oneIntegralGoldNum = bcmul($member['ratio'] / $types['official'], GameExchange::EXCHANGE_GOLD_NUM, GameResult::SCALE);//会员1积分可兑换金币数

        //查询是否要领取新手奖励
        $gameReward = GameReward::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id'),
            'params' => array(':member_id' => $member['id'],':game_type' => GameMemberInfo::GAME_TYPE_SANGUORUN)
        ));
        $isNewPlayerReward = $gameReward == false ? true : false;

        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'goldNum' => isset($gameData['gold_num']) ? $gameData['gold_num'] : $memberData['gold_num'],
            'scoreNum' => $totalIntegral,
            'nickName' => $member['nickname'],
            'isFirst' => (isset($info['is_first']) || !empty($gameInfo['is_first'])) ? true : false,
            'oneIntegralGoldNum' => $oneIntegralGoldNum,
            'isNewPlayerReward' => $isNewPlayerReward,
            'token' => $token
        );
        if (isset($captchas) && !empty($captchas)) $returnData['captchas'] = $captchas;
        $this->returnResult(GameModule::RESULT_CODE_1, $result);
    }

    /**
     * 获取盖网通账号游戏的基本信息（游戏登陆）
     * @param $msgData
     * @author xiaoyan.luo
     */
    private function getGameData($msgData)
    {
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '登录过期，请重新登录'));

        $gaiNumber = Yii::app()->gw->createCommand()->select('gai_number')->from('{{member}}')->where('id = :id', array(':id' => $memberId))->queryscalar();
        if (empty($gaiNumber))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '会员信息不存在'));

        if (!Member::model()->exists('gai_number="' . $gaiNumber . '"')) {
            //同步盖网信息到sku
            $member = Yii::app()->gw->createCommand()
                ->select('m.id,m.gai_number,m.referrals_id,m.username,m.password,m.salt,m.sex,m.real_name,m.password2,m.password3,
                        m.birthday,m.mobile,m.country_id,m.province_id,m.district_id,m.street,m.register_time,m.register_type,
                        m.head_portrait,m.status,m.nickname,m.referrals_time,mt.ratio')
                ->from('gw_member m')
                ->join('gw_member_type mt', 'm.type_id = mt.id')
                ->where('gai_number = :gai_number', array(':gai_number' => $gaiNumber))
                ->queryRow();
            $model = new Member();
            $skuMemberData = $member;
            $skuMemberData['gai_member_id'] = $member['id'];
            $skuMemberData['sku_number'] = $model->generateNumber();
            unset($skuMemberData['id']);
            Yii::app()->db->createCommand()->insert('{{member}}', $skuMemberData);
        }

        $member = Member::model()->find(array(
            'select' => array('id,sku_number,gai_number,ratio,nickname'),
            'condition' => 'gai_number = :gai_number',
            'params' => array(':gai_number' => $gaiNumber)
        ));
        if ($member == false)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '会员信息不存在'));

        $memberData = array(
            'member_id' => $memberId,
            'gai_number' => $member['gai_number'],
            'gold_num' => 0,
            'login_time' => time(),
            'token' => $msgData['token']
        );
        $gameData = GameMember::model()->find(array(
            'condition' => 'member_id = :member_id',
            'select' => array('id,gold_num,token'),
            'params' => array('member_id' => $memberId)
        ));
        if ($gameData == false){
            Yii::app()->db->createCommand()->insert('game.gw_game_member',$memberData);
        }else{
            if($gameData['token'] != $msgData['token']){
                GameMember::updateToken($gameData['id'], $msgData['token'],$memberId);
            }
        }

        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id,is_first'),
            'params' => array(':member_id' => $memberId, ':game_type' => GameMemberInfo::GAME_TYPE_SANGUORUN)
        ));
        if ($gameInfo == false) {
            $info['member_id'] = $memberId;
            $info['game_type'] = GameMemberInfo::GAME_TYPE_SANGUORUN;
            $info['is_first'] = GameMemberInfo::IS_FIRST_GAME;
            $info['power'] = $info['max_score'] = $info['max_combo'] = 0;
            Yii::app()->db->createCommand()->insert('game.gw_game_member_info',$info);
        }

        AccountBalance::findRecord(array('account_id' => $member['id'], 'type' => AccountBalance::TYPE_CONSUME, 'sku_number' => $member['sku_number']));
        $totalBalance = AccountBalance::getAccountAllBalance( $member['sku_number'],AccountInfo::TYPE_CONSUME);//会员消费账户余额
        $totalIntegral = bcdiv($totalBalance, $member['ratio'],2);//会员积分数量
        $types = MemberType::getMemberType();//获取会员类型数据
        $oneIntegralGoldNum = bcmul($member['ratio'] / $types['official'], GameExchange::EXCHANGE_GOLD_NUM, GameResult::SCALE);//会员1积分可兑换金币数

        //查询是否要领取新手奖励
        $gameReward = GameReward::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id'),
            'params' => array(':member_id' => $memberId,':game_type' => GameMemberInfo::GAME_TYPE_SANGUORUN)
        ));
        $isNewPlayerReward = $gameReward == false ? true : false;

        $returnData = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'goldNum' => isset($gameData['gold_num']) ? $gameData['gold_num'] : $memberData['gold_num'],
            'scoreNum' => $totalIntegral,
            'nickName' => $member['nickname'],
            'isFirst' => (isset($info['is_first']) || !empty($gameInfo['is_first'])) ? true : false,
            'oneIntegralGoldNum' => $oneIntegralGoldNum,
            'isNewPlayerReward' => $isNewPlayerReward
        );
        $this->returnResult(GameModule::RESULT_CODE_1, $returnData);
    }

    /**
     * 获取游戏比赛结果
     * @param $msgData
     */
    private function getResultData($msgData)
    {
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '登录过期，请重新登录'));

        //查询该玩家是否第一次玩游戏
        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('is_first'),
            'params' => array(':member_id' => $memberId, ':game_type' => GameMemberInfo::GAME_TYPE_SANGUORUN)
        ));

        $gameData = GameMember::model()->find(array(
            'condition' => 'member_id = :member_id',
            'select' => array('gold_num'),
            'params' => array(':member_id' => $memberId)
        ));

        $betInfoList = $msgData['betInfoList'];//玩家下注信息
        $data = Tool::getConfigData('multiple');//获取概率表数据
        if (empty($data))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '无法获取到概率表数据'));

        $roomData = $arr = $array = array();
        foreach ($data as $key => $value) {
            if ($value['room_id'] != $msgData['roomId']) continue;
            $roomData[$msgData['roomId']][$key] = $value;
        }

        foreach ($roomData[$msgData['roomId']] as $key => $value) {
            $arr[$value['id']] = $value['probability'];//概率
            $array[$value['id']] = $value['multiple'];//倍数
        }

        //判断概率表的概率和倍数是否符合规则
        $res1 = array_map(create_function('$item', 'if($item < 0 || $item > 100) return "invalid";'), $arr);
        $res2 = array_map(create_function('$item', 'if(!preg_match("/^[1-9]\d*$/",$item)) return "invalid";'), $array);
        $search = array_search('invalid', $res1);
        $search2 = array_search('invalid', $res2);
        if ($search)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '概率不能小于0或大于100'));
        if ($search2)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '倍数必须为正整数'));
        if (strval(array_sum($arr)) !== '100')
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '同一房间的概率总和必须为100'));

        $betGoldNum = array();
        if (!empty($betInfoList) && is_array($betInfoList)) {
            foreach ($betInfoList as $key => $value) {
                $betGoldNum[$value['runnerId']] = $value['addBetNum'];
            }
        }

        if ($gameInfo['is_first'] != GameMemberInfo::IS_FIRST_GAME || empty($betInfoList)) {
            //根据概率获取本局winner
            $winnerId = $this->getRand($arr);
        } else {
            //新手引导阶段的下注必然胜利
            $winnerId = array_rand($betGoldNum);
        }

        $expenditure = array_sum($betGoldNum);//花费的总金币数
        $income = 0; //收入的金币数初始为0

        if (in_array($winnerId, array_keys($betGoldNum))) { //下注含本局winner的情况
            $roomListData =Tool::getConfigData('room');
            if (empty($data))
                $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '无法获取到房间表数据'));
            foreach ($roomListData as $key => $value) {
                if ($value['room_id'] == $msgData['roomId']) $tax = $value['tax'];
            }
            //判断房间表的税率是否符合规则
            if (isset($tax) && $tax < 0)
                $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '税率不能小于0'));

            $probitGoldNum = bcsub($betGoldNum[$winnerId], bcmul($betGoldNum[$winnerId], $tax, GameExchange::SCALE), GameExchange::SCALE);
            $income = bcmul($probitGoldNum, $array[$winnerId], GameResult::SCALE);//收入金币数 = [下注金币- (下注金币 * 抽税比率)] * 倍率
        }

        $totalGoldNum = $expenditure - $income;//本局花费金币数与收入金币数的差额
        $remainGoldNum = $gameData['gold_num'] - $totalGoldNum; //本局游戏结束玩家剩余的金币数

        //组装要返回给客户端的数据
        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'winnerId' => $winnerId,
            'winGold' => $income,
            'goldNum' => ($remainGoldNum < 0) ? $gameData['gold_num'] : $remainGoldNum,//拥有的金币数量
        );
        $resultData = array(
            'code' => Tool::buildOrderNo(19, 'GAME'),
            'token' => $msgData['token'],
            'game_type' => GameMemberInfo::GAME_TYPE_SANGUORUN,
            'member_id' => $memberId,
            'msg_id' => $msgData['gameMsgId'],//游戏协议ID
            'expenditure' => $expenditure,
            'income_gold' => $income,
            'request' => json_encode($msgData),
            'result' => json_encode($result),
            'result_code' => GameModule::RESULT_CODE_0,
            'create_time' => time(),
            'update_time' => time()
        );
        $id = GameResult::insertResult($resultData);//比赛结果存入数据库

        $flag = false;
        $connection = Yii::app()->db->beginTransaction();
        try {
            if ($remainGoldNum < 0) throw new Exception('玩家金币不足');
            //更新玩家金币数
            Yii::app()->db->createCommand()->update('game.gw_game_member',array('gold_num' => $remainGoldNum),'member_id = :member_id',array(':member_id' => $memberId));
            if ($gameInfo['is_first'] == GameMemberInfo::IS_FIRST_GAME) {
                //更新新手玩家为已玩过游戏的状态
                Yii::app()->db->createCommand()->update('game.gw_game_member_info',array(
                    'is_first' => GameMemberInfo::NOT_FIRST_GAME),
                    'member_id = :member_id',array(':member_id' => $memberId)
                    );
            }
            $connection->commit();
            $flag = true;
        } catch (Exception $e) {
            $connection->rollBack();
            $result['errorMsg'] = $e->getMessage();
            $this->returnResult(GameModule::RESULT_CODE_0, $result);
        }
        if ($flag)
            Yii::app()->db->createCommand()->update('game.gw_game_result',array('result_code' => GameModule::RESULT_CODE_1),"id = '{$id}'");
        $this->returnResult(GameModule::RESULT_CODE_1, $result);//返回结果给客户端
    }

    /**
     * 兑换金币
     * @param $msgData
     */
    private function getExchangeData($msgData)
    {
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '登录过期，请重新登录'));

        $gaiNumber = Yii::app()->gw->createCommand()->select('gai_number')->from('{{member}}')->where('id = :id', array(':id' => $memberId))->queryscalar();
        if (empty($gaiNumber))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '会员信息不存在'));

        $member = Member::model()->find(array(
            'condition' => 'gai_number = :gai_number',
            'select' => array('id,sku_number,gai_number,ratio'),
            'params' => array(':gai_number' => $gaiNumber)
        ));

        $types = MemberType::getMemberType();
        //当前会员兑换所需金币的积分数
        $expenditure = bcdiv(bcdiv($msgData['needGold'], GameExchange::EXCHANGE_GOLD_NUM, GameExchange::SCALE), $member['ratio'] / $types['official'], GameExchange::SCALE);
        $totalBalance = AccountBalance::getAccountAllBalance($member['sku_number'], AccountInfo::TYPE_CONSUME);//用户余额
        $totalIntegral = bcdiv($totalBalance, $member['ratio'], 2);//用户可用积分
        $gameData = GameMember::model()->find(array(
            'condition' => 'member_id = :member_id',
            'select' => array('gold_num'),
            'params' => array(':member_id' => $memberId)
        ));

        //组装要返回给客户端的数据
        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'needGold' => $msgData['needGold'],
            'goldNum' => $gameData['gold_num'], //拥有的金币数量
            'scoreNum' => $totalIntegral //拥有的积分数
        );

        $code = Tool::buildOrderNo(19, 'GAME');
        //组装金币兑换数据
        $exchangeData = array(
            'code' => $code,
            'token' => $msgData['token'],
            'member_id' => $memberId,
            'game_type' => GameMemberInfo::GAME_TYPE_SANGUORUN,
            'msg_id' => $msgData['gameMsgId'],//游戏协议ID
            'expenditure' => $expenditure,
            'income_gold' => $msgData['needGold'],
            'request' => json_encode($msgData),
            'result' => json_encode($result),
            'result_code' => ($expenditure > $totalIntegral) ? GameModule::RESULT_CODE_3 : GameModule::RESULT_CODE_0,
            'create_time' => time(),
            'update_time' => time()
        );
        $id = GameExchange::insertExchange($exchangeData);
        if ($expenditure > $totalIntegral) {
            $result['errorMsg'] = '积分不足';
            $this->returnResult(GameModule::RESULT_CODE_3, $result);
        }

        $connection = Yii::app()->db->beginTransaction();
        $flag = false;
        try {
            $money = bcmul($expenditure, $member['ratio'], 2);//要扣除的金额
            $apiLogData['money'] = $money;
            $apiLogData['order_code'] = $code;
            $apiLogData['order_id'] = $id;
            $apiLogData['account_id'] = $member['id'];
            $apiLogData['gai_number'] = $member['gai_number'];
            $apiLogData['sku_number'] = $member['sku_number'];
            $apiLogData['operate_type'] = AccountFlow::OPERATE_TYPE_GAME_EXCHANGE;
            $apiLogData['transaction_type'] = AccountFlow::TRANSACTION_TYPE_CONSUME;
            $apiLogData['callback'] = isset($msgData['callback']) ? $msgData['callback'] : '';
            $apiLogData['is_callback'] = empty($apiLogData['callback']) ? 0 : 1;
            $apiLogData['remark'] = GameMemberInfo::getGameName(GameMemberInfo::GAME_TYPE_SANGUORUN) . '金币兑换';
            $apiLogData['data'] = json_encode($msgData);

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

            $res = Yii::app()->db->createCommand()->insert($apiLogTable, $apiLogData);
            if ($res == false) throw new Exception('操作不成功');

            $memberArray = array(
                'account_id' => $member['id'],
                'type' => AccountBalance::TYPE_CONSUME,
                'sku_number' => $member['sku_number'],
                'ratio' => $member['ratio']
            );
            AccountBalance::exchange($memberArray,$apiLogData);
            $remainGoldNum = $gameData['gold_num'] + $msgData['needGold'];
            Yii::app()->db->createCommand()->update('game.gw_game_member', array('gold_num' => $remainGoldNum), 'member_id = :member_id', array(':member_id' => $memberId));
            $connection->commit();
            $flag = true;
        } catch (Exception $e) {
            $connection->rollBack();
            Yii::log($e->getMessage());
            $result['errorMsg'] = $e->getMessage();
            $this->returnResult(GameModule::RESULT_CODE_0, $result);
        }

        $result['goldNum'] = $gameData['gold_num'] + $msgData['needGold'];//兑换金币成功后玩家的金币数
        $result['scoreNum'] = $totalIntegral - $expenditure; //兑换金币成功后玩家的积分数
        if ($flag) GameExchange::updateResultCode($id, $result);//更新状态码
        $this->returnResult(GameModule::RESULT_CODE_1, $result);
    }

    private function getRand($proArr)
    {
        $result = '';
        $proSum = array_sum($proArr);//概率数组的总概率精度
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset($proArr);
        return $result;
    }

    /**
     * 领取新手奖励
     * @param $msgData
     */
    private function updateReward($msgData)
    {
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '登录过期，请重新登录'));

        $gameData = GameMember::model()->find(array(
            'condition' => 'member_id = :member_id',
            'select' => array('gold_num'),
            'params' => array(':member_id' => $memberId)
        ));
        if ($gameData == false)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '会员游戏数据不存在'));

        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id'),
            'params' => array(':member_id' => $memberId, ':game_type' => GameMemberInfo::GAME_TYPE_SANGUORUN)
        ));
        if ($gameInfo == false)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '会员尚未登录过游戏'));

        $gameReward = GameReward::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id'),
            'params' => array(':member_id' => $memberId, ':game_type' => GameMemberInfo::GAME_TYPE_SANGUORUN)
        ));
        if (!empty($gameReward))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'gold_num' => $gameData['gold_num'], 'errorMsg' => '已经领取过新手奖励'));

        //组装要返回给客户端的数据
        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'goldNum' => bcadd($gameData['gold_num'], GameMember::REWARD_GOLD_NUM_SANGUORUN, GameResult::SCALE), //拥有的金币数量
        );

        $connection = Yii::app()->db->beginTransaction();

        try {
            $remainGoldNum = bcadd($gameData['gold_num'], GameMember::REWARD_GOLD_NUM_SANGUORUN, GameResult::SCALE);
            Yii::app()->db->createCommand()->update('game.gw_game_member',array('gold_num' => $remainGoldNum),'member_id = :member_id',array(':member_id' => $memberId));

            $data = array(
                'member_id' => $memberId,
                'game_type' => GameMemberInfo::GAME_TYPE_SANGUORUN,
                'gold_num' => GameMember::REWARD_GOLD_NUM_SANGUORUN,
                'reward_time' => time()
            );
            Yii::app()->db->createCommand()->insert('game.gw_game_reward',$data);
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            $result['gold_num'] = $gameData['gold_num'];
            $result['errorMsg'] = $e->getMessage();
            $this->returnResult(GameModule::RESULT_CODE_0, $result);
        }
        $this->returnResult(GameModule::RESULT_CODE_1, $result);
    }
}