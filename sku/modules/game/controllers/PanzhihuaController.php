<?php
/**
 * 攀枝花抢水果游戏接口
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/11/27 17:00
 */

class PanZhiHuaController extends GameBaseController
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
            case 'exchangeGold':
                $this->getExchangeData($msgData);
                break;
            case 'getStoreData':
                $this->getStoreData($msgData);
                break;
            case 'getBagData':
                $this->getBagData($msgData);
                break;
            case 'orderUserData':
                $this->orderUserData($msgData);
                break;
            case 'uploadUserfruitData':
                $this->uploadUserfruitData($msgData);
                break;
            case 'deductGold':
                $this->deductGoldResult($msgData);
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

        if ($redis->exists("userLoginCaptcha:{$member['gai_member_id']}")) $this->redis->remove("userLoginCaptcha:{$member['id']}");
        if ($redis->exists("userLoginCount:{$member['gai_member_id']}")) $this->redis->remove("userLoginCount:{$member['id']}");
        $token = GameMember::createToken($member['gai_member_id'],GameMemberInfo::GAME_TYPE_PANZHIHUA);//生成token

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
            'select' => array('id,is_first,max_score,max_combo,power'),
            'params' => array(':member_id' => $member['gai_member_id'], ':game_type' => GameMemberInfo::GAME_TYPE_PANZHIHUA)
        ));
        if ($gameInfo == false) {
            $info['member_id'] = $member['gai_member_id'];
            $info['game_type'] = GameMemberInfo::GAME_TYPE_PANZHIHUA;
            $info['is_first'] = GameMemberInfo::IS_FIRST_GAME;
            $info['power'] = isset($msgData['power']) ? $msgData['power'] : GameMemberInfo::DEFAULT_MAX_GAME_POWER;
            $info['update_power_time'] = time();
            $info['max_score'] = $info['max_combo'] = 0;
            Yii::app()->db->createCommand()->insert('game.gw_game_member_info',$info);
        }

        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'token' => $token
        );
        if (isset($captchas) && !empty($captchas)) $returnData['captchas'] = $captchas;
        $this->returnResult(GameModule::RESULT_CODE_1, $result);
    }

    /**
     * 获取游戏基本信息
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

        $member = Member::model()->find(array(
            'condition' => 'gai_number = :gai_number',
            'select' => array('id,sku_number,gai_number,ratio'),
            'params' => array(':gai_number' => $gaiNumber)
        ));
        if ($member == false)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '会员信息不存在'));

        //查找是否有该会员盖付通游戏信息，无信息则增加游戏信息
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
        if ($gameData == false) {
            Yii::app()->db->createCommand()->insert('game.gw_game_member',$memberData);
        } else {
            if ($gameData['token'] != $msgData['token']) {
                GameMember::updateToken($gameData['id'], $msgData['token'], $memberId);
            }
        }

        //查找是否有该会员登录该游戏的信息，无信息则增加游戏信息
        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id,is_first,power,max_score,max_combo,update_power_time'),
            'params' => array(':member_id' => $memberId, ':game_type' => GameMemberInfo::GAME_TYPE_PANZHIHUA)
        ));
        if ($gameInfo == false) {
            $info['member_id'] = $memberId;
            $info['game_type'] = GameMemberInfo::GAME_TYPE_PANZHIHUA;
            $info['is_first'] = GameMemberInfo::IS_FIRST_GAME;
            $info['power'] = GameMemberInfo::DEFAULT_MIN_GAME_POWER;
            $info['update_power_time'] = time();
            $info['max_score'] = $info['max_combo'] = 0;
            Yii::app()->db->createCommand()->insert('game.gw_game_member_info',$info);
        }

        AccountBalance::findRecord(array('account_id' => $member['id'], 'type' => AccountBalance::TYPE_CONSUME, 'sku_number' => $member['sku_number']));
        $totalBalance = AccountBalance::getAccountAllBalance($member['sku_number'],AccountInfo::TYPE_CONSUME);//会员消费账户余额
        $totalIntegral = bcdiv($totalBalance, $member['ratio'],2);//会员积分数量
        $types = MemberType::getMemberType();//获取会员类型数据
        $goldNum = bcmul($member['ratio'] / $types['official'], GameExchange::EXCHANGE_GOLD_NUM, GameResult::SCALE);//会员1积分可兑换金币数

        //查询用户是否已经填写送货信息
        $storeMember = GameStoreMember::model()->find(array(
            'condition' => 'member_id = :member_id',
            'select' => array('id,real_name,mobile,member_address'),
            'params' => array(':member_id' => $member['id'])
        ));
        $isInput = $storeMember ? true : false; //是否已经输入用户信息

        $returnData = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'goldNum' => isset($gameData['gold_num']) ? $gameData['gold_num'] : $memberData['gold_num'], //拥有的金币数量
            'jifenNum' => $totalIntegral,//拥有的积分数量
            'nickName' => $member['nickname'], //玩家昵称
            'oneIntegralGoldNum' => $goldNum, //1积分可兑换金币数量
            'isFirst' => (isset($info['is_first']) || !empty($gameInfo['is_first'])) ? true : false, //是否已经选择自动购买
            'isInput' => $isInput,//是否已经输入用户信息
            'userName' => !empty($storeMember['real_name']) ? $storeMember['real_name'] : '', //用户姓名
            'userPhone' => !empty($storeMember['mobile']) ? $storeMember['mobile'] : '', //用户手机号
            'userAddress' => !empty($storeMember['member_address']) ? $storeMember['member_address'] : '', //用户地址
        );
        $this->returnResult(GameModule::RESULT_CODE_1, $returnData);
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
            'jifenNum' => $totalIntegral //拥有的积分数
        );

        $code = Tool::buildOrderNo(19, 'GAME');
        //组装金币兑换数据
        $exchangeData = array(
            'code' => $code,
            'token' => $msgData['token'],
            'member_id' => $memberId,
            'game_type' => GameMemberInfo::GAME_TYPE_PANZHIHUA,
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
            $apiLogData['remark'] = GameMemberInfo::getGameName(GameMemberInfo::GAME_TYPE_PANZHIHUA) . '金币兑换';
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
        $result['jifenNum'] = $totalIntegral - $expenditure; //兑换金币成功后玩家的积分数
        if ($flag) GameExchange::updateResultCode($id, $result);//更新状态码
        $this->returnResult(GameModule::RESULT_CODE_1, $result);
    }

    /**
     * 获取所有店铺的信息
     * @param $msgData
     */
    private function getStoreData($msgData)
    {
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '登录过期，请重新登录'));

        $connection = Yii::app()->gw;
        $sql = "SELECT gs.id AS storeIp,gs.store_name AS storeName,gs.store_phone AS storePhone,gs.store_address AS storeAdd,
          gs.limit_time_hour,gs.limit_time_minute,gt.id,gt.item_name AS fruitName,gt.start_date,gt.end_date,gt.start_time AS beginTime,
          gt.end_time AS endTime,gt.item_number AS storeProvide,gt.item_status,gk.stock_number AS storeResidue FROM gw_game_store AS gs
          LEFT JOIN `gw_game_store_items` AS gt ON gs.id = gt.store_id LEFT JOIN game.gw_game_item_stock AS gk ON gt.id = gk.item_id
          WHERE gs.store_status = " . GameStore::STATUS_ONLINE . " AND gt.item_status = " . GameStoreItems::STATUS_ONLINE . " AND gt.flag = " . GameStoreItems::ORDINARY_ITEM_FLAG;;
        $data = $connection->createCommand($sql)->queryAll();
        if (empty($data)) {
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '店铺数据为空'));
        }

        foreach ($data as $k => $value) {
            if ((time() > strtotime($value['end_date'] . $value['endTime'])) || $value['item_status'] == GameStoreItems::STATUS_OFFLINE) {
                //当前时间已超过活动期限
                $data[$k]['needTime'] = null;
            } elseif (time() < strtotime($value['start_date'] . $value['beginTime'])) {
                //还没有到活动开始日期
                $data[$k]['needTime'] = strtotime($value['start_date'] . $value['beginTime']) - time();
            } elseif ((time() > strtotime(date($value['start_date'] . $value['beginTime']))) && (time() < strtotime(date($value['end_date'] . $value['endTime'])))) {
                //当前日期在活动进行日期当中,查询当天的活动时间有没有到
                $dateBeginTime = strtotime(date('Y-m-d' . $value['beginTime']));
                $dateEndTime = strtotime(date('Y-m-d' . $value['endTime']));
                if (time() < $dateBeginTime) {
                    $data[$k]['needTime'] = $dateBeginTime - time();
                }
                if (time() > $dateBeginTime && time() < $dateEndTime) { //在当天的抢水果时间段
                    $data[$k]['needTime'] = 0;
                }
                if (time() > $dateEndTime) { //当前时间已经过了当天的抢水果结束时间
                    $data[$k]['needTime'] = ($dateBeginTime + 60 * 60 * 24) - time();
                }
            }
            //考虑是否在活动时间内,玩家是否在抢水果的冻结时间内
            $redisKey = 'id:' . $memberId . ':store:' . $value['storeIp'] . ':item:' . $value['id'] . ':time';
            $limitTime = $value['limit_time_hour'] * 60 * 60 + $value['limit_time_minute'] * 60;
            $redis = Yii::app()->redis;
            if($gainTime = $redis->get($redisKey)){
                $data[$k]['isCanBuy'] = false;
                $data[$k]['coolTime'] = $limitTime - (time() - $gainTime);
            }else{
                $gain = GameItemGain::model()->find(array(
                    'select' => array('id,gain_time'),
                    'condition' => 'store_id = :store_id and item_id = :item_id and member_id = :member_id',
                    'params' => array(':store_id' => $value['storeIp'], ':item_id' => $value['id'], ':member_id' => $memberId)
                ));
                if ($gain == false) {
                    $data[$k]['isCanBuy'] = ($data[$k]['needTime'] === 0 && $value['storeResidue']) ? true : false;
                    $data[$k]['coolTime'] = 0;
                } else {
                    if (time() > $gain['gain_time'] + $limitTime) {
                        $data[$k]['isCanBuy'] = ($data[$k]['needTime'] === 0 && $value['storeResidue']) ? true : false;
                        $data[$k]['coolTime'] = 0;
                    } else {
                        $data[$k]['isCanBuy'] = false;
                        $data[$k]['coolTime'] = $limitTime - (time() - $gain['gain_time']);
                    }
                }
            }
            $data[$k]['setTime'] = $limitTime;
            unset($data[$k]['start_date'], $data[$k]['end_date'], $data[$k]['id'], $data[$k]['limit_time_time'],$data[$k]['limit_time_minute'],$data[$k]['item_status']);
        }

        //组装店铺信息
        $storeData = $storeInfo = array();
        foreach ($data as $value) {
            $storeData[$value['storeIp']][] = $value;
        }

        if(!empty($storeData)){
            $k = 0;
            foreach($storeData as $v){
                $k++;
                $storeInfo[$k] = $v;
            }
        }
        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'storeData' => $storeInfo
        );
        $this->returnResult(GameModule::RESULT_CODE_1, $result);
    }

    /**
     * 获取背包信息
     * @param $msgData
     */
    private function getBagData($msgData)
    {
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '登录过期，请重新登录'));

        $connection = Yii::app()->gw;
        $sql = "SELECT gs.store_id AS fruitIp,gs.item_name AS fruitName,gi.item_number AS fruitNumber,
            gs.store_description AS isStoreName,gs.item_description AS fruitDescribe FROM " . GAME . ".gw_game_item_gain AS gi
            LEFT JOIN gw_game_store_items AS gs ON gi.item_id = gs.id WHERE gi.member_id = '$memberId'";
        $data = $connection->createCommand($sql)->queryAll();
        $this->returnResult(GameModule::RESULT_CODE_1, array('gameMsgId' => $msgData['gameMsgId'], 'bagData' => $data));
    }

    /**
     * 上传用户下单数据
     * @param $msgData
     */
    private function orderUserData($msgData)
    {
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '登录过期，请重新登录'));

        if (empty($msgData['fruitOrder'])) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '用户下单数据为空'
            ));
        }

        $result = array('gameMsgId' => $msgData['gameMsgId']);
        $connection = Yii::app()->gw->beginTransaction();
        try {
            $array = array();
            //循环下单的数据
            foreach ($msgData['fruitOrder'] as $key => $value) {
                $itemId = GameStoreItems::model()->find(array(
                    'select' => array('id'),
                    'condition' => 'store_id = :store_id and item_name = :item_name',
                    'params' => array(':store_id' => $value['fruitIp'],':item_name' => $value['fruitName'])
                ));
                if (empty($itemId)) {
                    $this->returnResult(GameModule::RESULT_CODE_0, array(
                        'gameMsgId' => $msgData['gameMsgId'],
                        'errorMsg' => '获取水果标识失败'
                    ));
                }
                $itemId = $itemId['id'];

                //查询用户背包里是否有下单的水果
                $gain = GameItemGain::model()->find(array(
                    'select' => array('id,item_number'),
                    'condition' => 'store_id = :store_id and item_id = :item_id and member_id = :member_id',
                    'params' => array(':store_id' => $value['fruitIp'], ':item_id' => $itemId, ':member_id' => $memberId)
                ));
                if ($gain == false) {
                    $this->returnResult(GameModule::RESULT_CODE_0, array(
                        'gameMsgId' => $msgData['gameMsgId'],
                        'errorMsg' => '背包没有' . $value['fruitName'] . '数据'
                    ));
                }

                if ($gain['item_number'] < $value['fruitNumber']) {
                    $this->returnResult(GameModule::RESULT_CODE_0, array(
                        'gameMsgId' => $msgData['gameMsgId'],
                        'errorMsg' => '下单的' . $value['fruitName'] . '数量大于背包里的数量，请核实'
                    ));
                }

                //查询用户在某商家是否有未发货的订单
                $member = GameStoreMember::model()->find(array(
                    'select' => array('id,store_id,items_info'),
                    'condition' => 'member_id = :member_id and store_id = :store_id and status = :status
                     and real_name = :real_name and mobile = :mobile and member_address = :member_address',
                    'params' => array(
                        ':member_id' => $memberId,
                        ':store_id' => $value['fruitIp'],
                        ':status' => GameStoreMember::STATUS_NOT_DELIVERY,
                        ':real_name' => $msgData['userName'],
                        ':mobile' => $msgData['userPhone'],
                        ':member_address' => $msgData['userAddress']
                    )
                ));
                if ($member == false) {
                    $memberData = array(
                        'store_id' => $value['fruitIp'], //商铺id
                        'member_id' => $memberId, //会员id
                        'real_name' => $msgData['userName'], //会员收货名称
                        'mobile' => $msgData['userPhone'], //会员手机号
                        'member_address' => $msgData['userAddress'], //会员收货地址
                        'items_info' => $value['fruitName'] . '*' . $value['fruitNumber'], //下单商品
                    );
                    Yii::app()->gw->createCommand()->insert('gaiwang.gw_game_store_member',$memberData);//写入下单信息
                } else {
                    $itemInfoArr = $fruitNameArr = array();
                    $itemInfo = explode(';',$member['items_info']);
                    foreach($itemInfo as $v){
                        $fruitNameArr[] = mb_substr($v,0,mb_strpos($v,'*'));
                    }
                    array_unique($fruitNameArr);
                    foreach ($itemInfo as $k => $v) {
                        if($value['fruitIp'] != $member['store_id'])continue;
                        array_push($itemInfoArr,$v);
                        $fruitName = mb_substr($v,0,mb_strpos($v,'*'));
                        $number = mb_substr($v,mb_strpos($v,'*')+1);

                        if ($value['fruitName'] === $fruitName && in_array($value['fruitName'],$fruitNameArr) && empty($array[$value['fruitIp']][$value['fruitName']])) {
                            $array[$value['fruitIp']][$value['fruitName']] = true;
                            $itemInfoArr[$k] = $value['fruitName'] . '*' . bcadd($number,$value['fruitNumber'],0);
                        } else {
                            $itemText = implode(';',$itemInfo);
                            if((mb_strpos($itemText,$value['fruitName']) === false) && empty($array[$value['fruitIp']][$value['fruitName']])){
                                $array[$value['fruitIp']][$value['fruitName']] = true;
                                $itemInfoArr[$k+1] = $value['fruitName'] . '*' . $value['fruitNumber'];
                            }
                        }
                    }
                    $data['items_info'] = implode(';',$itemInfoArr);
                    Yii::app()->gw->createCommand()->update('gaiwang.gw_game_store_member',$data,"id = '{$member['id']}'");
                    unset($array,$member);
                }

                //清除该水果在背包的数据
                Yii::app()->gw->createCommand()->delete('game.gw_game_item_gain',
                    'member_id = :member_id and store_id = :store_id and item_id = :item_id',
                    array(':member_id' => $memberId,':store_id' => $value['fruitIp'],':item_id' => $itemId));
            }
            //保存上传的是否自动购买信息
            if(isset($msgData['isFirst'])){
                Yii::app()->gw->createCommand()->update('game.gw_game_member_info',array('is_first' => $msgData['isFirst']),
                    'member_id = :member_id and game_type = :game_type',array(
                        ':member_id' => $memberId,':game_type' => GameMemberInfo::GAME_TYPE_PANZHIHUA
                    ));
            }
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            $result['errorMsg'] = $e->getMessage();
            $this->returnResult(GameModule::RESULT_CODE_0, $result);
        }

        //获取背包数据
        $sql2 = "SELECT gs.store_id AS fruitIp,gs.item_name AS fruitName,gi.item_number AS fruitNumber,
            gs.store_description AS isStoreName,gs.item_description AS fruitDescribe FROM " . GAME . ".gw_game_item_gain AS gi
            LEFT JOIN gw_game_store_items AS gs ON gi.item_id = gs.id WHERE gi.member_id = '$memberId'";
        $data = Yii::app()->gw->createCommand($sql2)->queryAll();
        $result['bagData'] = $data;
        $this->returnResult(GameModule::RESULT_CODE_1, $result);
    }

    /**
     * 上传用户抢水果数据
     * @param $msgData
     */
    private function uploadUserfruitData($msgData)
    {
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false) {
            $this->returnResult(GameModule::RESULT_CODE_6, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '登录过期，请重新登录'
            ));
        }

        $gameData = GameMember::model()->find(array(
            'condition' => 'member_id = :member_id',
            'select' => array('id,gold_num'),
            'params' => array(':member_id' => $memberId)
        ));
        if($gameData == false){
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '游戏用户数据不存在'
            ));
        }

        $limit = GameStore::model()->find(array(
            'select' => array('limit_time_hour,limit_time_minute,store_status'),
            'condition' => 'id = :id',
            'params' => array(':id' => $msgData['storeId'])
        ));
        if($limit == false){
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '该店铺数据不存在'
            ));
        }
        if($limit['store_status'] == GameStore::STATUS_CLOSE){
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '该店铺已关闭'
            ));
        }

        $items = GameStoreItems::model()->find(array(
            'select' => array('id,bees_number,start_date,end_date,start_time,end_time,limit_per_time,item_status'),
            'condition' => 'store_id = :store_id and item_name = :item_name',
            'params' => array(':store_id' => $msgData['storeId'], ':item_name' => $msgData['fruitName'])
        ));
        if ($items == false){
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '该商店水果数据不存在'
            ));
        }
        if($items['item_status'] == GameStoreItems::STATUS_OFFLINE){
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '该水果已经下架'
            ));
        }

        //查询是否在抢水果的活动时间内
        $beginTime = strtotime($items['start_date'] . $items['start_time']);
        $endTime = strtotime($items['end_date'] . $items['end_time']);
        //情况1：抢水果活动未开始或者已经结束
        if(time() < $beginTime || time() > $endTime){
            $this->returnResult(GameModule::RESULT_CODE_0,array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '不在抢水果活动时间内，无法抢水果'
            ));
        }

        //情况2：在活动日期范围内，但今天的活动还没有开始
        if(time() > $beginTime && time() < $endTime){
            if(time() < strtotime(date('Y-m-d' . $items['start_time']))){
                $this->returnResult(GameModule::RESULT_CODE_0,array(
                    'gameMsgId' => $msgData['gameMsgId'],
                    'errorMsg' => '今天的抢水果活动还没有开始'
                ));
            }
        }

        $number = $items['limit_per_time']; //每次抢该水果的数量限制
        $expendGoldNum = GameMemberInfo::GAIN_PANZHIHUA_GOLDNUM; //每次抢水果花费100金币
        if($gameData['gold_num'] < $expendGoldNum){
            $this->returnResult(GameModule::RESULT_CODE_0,array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '玩家金币不足'
            ));
        }

        //先将花费金币的记录存入数据库
        $resultData = array(
            'member_id' => $memberId,
            'game_type' => GameMemberInfo::GAME_TYPE_PANZHIHUA,
            'token' => $msgData['token'],
            'msg_id' => $msgData['gameMsgId'],//游戏协议ID
            'expenditure' => $expendGoldNum,
            'gold_num' => $gameData['gold_num'],//先存原本金币数目，成功花费即存已经花费后的金币数
            'request' => json_encode($msgData),
            'result_code' => GameModule::RESULT_CODE_0,
            'create_time' => time(),
            'update_time' => time()
        );
        $id = GameExpend::insertExpend($resultData);//花费金币记录存入数据库

        $connection = Yii::app()->db->beginTransaction();
        $remainGoldNum = bcsub($gameData['gold_num'], $expendGoldNum, GameResult::SCALE);
        $flag = false;
        $gainResult = 0;
        try {
            //查询是否有库存
            $sql = "SELECT id,stock_number FROM " . GAME . ".gw_game_item_stock WHERE store_id = '{$msgData['storeId']}'
                AND item_id = '{$items['id']}' FOR UPDATE";
            $stock = Yii::app()->db->createCommand($sql)->queryRow();
            if ($stock == false){
                $this->returnResult(GameModule::RESULT_CODE_0, array(
                    'gameMsgId' => $msgData['gameMsgId'],
                    'errorMsg' => '该水果库存数据不存在'
                ));
            }
            if ($stock['stock_number'] < $number){
                $this->returnResult(GameModule::RESULT_CODE_0, array(
                    'gameMsgId' => $msgData['gameMsgId'],
                    'errorMsg' => '该水果库存不足'
                ));
            }

            //组装抢水果的概率数组
            $proArr = array(
                0 => $items['bees_number'] * 10,
                1 => 100 - $items['bees_number'] * 10
            );
            $gainResult = $this->getRand($proArr); //根据概率判断玩家是否抢到水果

            //无论用户是否抢到水果，在限定时间内都不能再抢店铺的该水果
            $redisKey = 'id:' . $memberId . ':store:' . $msgData['storeId'] . ':item:' . $items['id'] . ':time';
            $redis = Yii::app()->redis;
            $limitTime = $limit['limit_time_hour'] * 60 * 60 + $limit['limit_time_minute'] * 60;
            if($value = $redis->get($redisKey)){
                $this->returnResult(GameModule::RESULT_CODE_0, array(
                    'gameMsgId' => $msgData['gameMsgId'],
                    'coolTime' => $limitTime - (time() - $value),
                    'errorMsg' => '冻结时间内不能再次抢水果'
                ));
            }

            $gainTime = time();
            if ($gainResult == 1) {
                //将用户抢到的水果放入背包
                $gain = GameItemGain::model()->find(array(
                    'select' => array('id,item_number,gain_time'),
                    'condition' => 'member_id = :member_id and store_id = :store_id and item_id = :item_id',
                    'params' => array(':member_id' => $memberId, ':store_id' => $msgData['storeId'], ':item_id' => $items['id'])
                ));
                if ($gain) {
                    //查询玩家是否在冷冻时间内
                    if (time() >= $gain['gain_time'] + $limitTime) {
                        Yii::app()->db->createCommand()->update('game.gw_game_item_gain',array(
                            'item_number' => $gain['item_number'] + $number, 'gain_time' => $gainTime),"id = '{$id}'");
                        GameItemStock::delStock($stock['id'], $number);//减去水果库存
                    } else {
                        $this->returnResult(GameModule::RESULT_CODE_0, array(
                            'gameMsgId' => $msgData['gameMsgId'],
                            'coolTime' => $limitTime - (time() - $gain['gain_time']),
                            'errorMsg' => '冻结时间内不能再次抢水果'
                        ));
                    }
                } else {
                    $data = array(
                        'member_id' => $memberId,
                        'store_id' => $msgData['storeId'],
                        'item_id' => $items['id'],
                        'item_number' => $number,
                        'gain_time' => $gainTime,
                    );
                    Yii::app()->db->createCommand()->insert('game.gw_game_item_gain',$data);
                    GameItemStock::delStock($stock['id'], $number);//减去水果库存
                }
            }else{
                /*
                //没有抢到水果即有机会获取到惊喜大奖
                $specialItems = GameStoreItems::model()->findAll(array(
                    'select' => array('id,start_date,end_date,start_time,end_time,limit_per_time,item_status,flag,probability'),
                    'condition' => 'store_id = :store_id and flag = :flag ',
                    'params' => array(':store_id' => $msgData['storeId'], ':flag' => GameStoreItems::SPECIAL_ITEM_FLAG)
                ));
                if($specialItems){
                    $specialItemsArr = array();
                    foreach($specialItems as $key => $value){
                        $beginTime = strtotime($value['start_date'] . $value['start_time']);
                        $endTime = strtotime($value['end_date'] . $value['end_time']);
                        if(time() > $beginTime && time() < $endTime){
                            $specialItemsArr[$key] = $value;
                        }
                    }
                    array_rand($specialItemsArr);//取其中一种特殊商品
                    $specialItemsArr = $specialItemsArr[0];
                    $specialPro[$specialItemsArr['id']] = $specialItemsArr['probability'];
                    $specialPro[0] = 1000000 - $specialItemsArr['probability'];
                    $specialGainId = $this->getRand($specialPro);
                    if($specialGainId){
                        //有无库存
                        $sql2 = "SELECT id,stock_number FROM " . GAME . ".gw_game_item_stock WHERE store_id = '{$msgData['storeId']}'
                            AND item_id = '{$specialGainId}' FOR UPDATE";
                        $specialStock = Yii::app()->db->createCommand($sql2)->queryRow();
                        if($specialStock && $specialStock['stock_number'] >= $specialItemsArr['limit_per_time']){
                            $specialGain = GameItemGain::model()->find(array(
                                'select' => array('id,item_number,gain_time'),
                                'condition' => 'member_id = :member_id and store_id = :store_id and item_id = :item_id',
                                'params' => array(':member_id' => $memberId, ':store_id' => $msgData['storeId'], ':item_id' => $specialGainId)
                            ));
                            if($specialGain){
                                Yii::app()->db->createCommand()->update('game.gw_game_item_gain',array(
                                    'item_number' => $specialGain['item_number']  + $number, 'gain_time' => $gainTime),"id = '{$specialGain['id']}'");
                            }else{
                                $specialData = array(
                                    'member_id' => $memberId,
                                    'store_id' => $msgData['storeId'],
                                    'item_id' => $specialGainId,
                                    'item_number' => $specialItemsArr['limit_per_time'],
                                    'gain_time' => $gainTime,
                                );
                                Yii::app()->db->createCommand()->insert('game.gw_game_item_gain',$specialData);
                            }
                            GameItemStock::delStock($specialStock['id'], $number);//减去特殊商品库存
                        }
                    }
                }
                */
            }
            $redis->set($redisKey,$gainTime,$limitTime);
            Yii::app()->db->createCommand()->update('game.gw_game_member',array('gold_num' => $remainGoldNum),'member_id = :member_id',array(':member_id' => $memberId));
            $connection->commit();
            $flag = true;
        } catch (Exception $e) {
            $connection->rollBack();
            $result['errorMsg'] = $e->getMessage();
            Yii::app()->db->createCommand()->update('game.gw_game_expend',array('result' => json_encode($result)),"id = '{$id}'");
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => $result['errorMsg']
            ));
        }

        //获取背包数据
        $sql2 = "SELECT gs.store_id AS fruitIp,gs.item_name AS fruitName,gi.item_number AS fruitNumber,
            gs.store_description AS isStoreName,gs.item_description AS fruitDescribe FROM " . GAME . ".gw_game_item_gain AS gi
            LEFT JOIN gaiwang.gw_game_store_items AS gs ON gi.item_id = gs.id WHERE gi.member_id = '$memberId'";
        $data = Yii::app()->db->createCommand($sql2)->queryAll();

        //组装返回游戏端数据
        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'storeId' => $msgData['storeId'],
            'storeResidue' => $gainResult == 0 ? $stock['stock_number'] : $stock['stock_number'] - $number,
            'fruitNumber' => $gainResult == 0 ? 0 : $number,
            //'prizeIp' => isset($specialGainId) ? $specialGainId : 0,
            'coolTime' => $limit['limit_time_hour'] * 60 * 60 + $limit['limit_time_minute'] * 60,
            'bagData' => $data
        );

        if($flag){
            //更新状态码,返回结果和现有金币数目
            Yii::app()->db->createCommand()->update('game.gw_game_expend',array(
                'result' => json_encode($result),'gold_num' => $remainGoldNum,'result_code' => GameModule::RESULT_CODE_1),
                "id = '{$id}'");
        }
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
     * 扣除金币
     * @param $msgData
     */
    private function deductGoldResult($msgData){
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '登录过期，请重新登录'));

        $gameData = GameMember::model()->find(array(
            'condition' => 'member_id = :member_id',
            'select' => array('id,gold_num'),
            'params' => array(':member_id' => $memberId)
        ));
        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => 'id',
            'params' => array(':member_id' => $memberId, ':game_type' => GameMemberInfo::GAME_TYPE_PANZHIHUA)
        ));
        if($gameInfo == false || $gameData == false)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '游戏数据不存在'));

        //组装要返回给客户端的数据
        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'isBuy' => false,
            'goldNum' => $gameData['gold_num'],//拥有的金币数量
        );

        //先将花费金币的记录存入数据库
        $resultData = array(
            'member_id' => $memberId,
            'game_type' => GameMemberInfo::GAME_TYPE_PANZHIHUA,
            'token' => $msgData['token'],
            'msg_id' => $msgData['gameMsgId'],//游戏协议ID
            'expenditure' => $msgData['deductGold'],//花费金币数
            'gold_num' => $gameData['gold_num'],//先存原本金币数目，成功花费即存已经花费后的金币数
            'request' => json_encode($msgData),
            'result' => json_encode($result),
            'result_code' => GameModule::RESULT_CODE_0,
            'create_time' => time(),
            'update_time' => time()
        );
        $id = GameExpend::insertExpend($resultData);//花费金币记录存入数据库

        $connection = Yii::app()->db->beginTransaction();
        $remainGoldNum = bcsub($gameData['gold_num'], $resultData['expenditure'], GameResult::SCALE);
        $flag = false;

        try{
            if($gameData['gold_num'] < $resultData['expenditure'])throw new Exception('玩家金币不足');
            Yii::app()->db->createCommand()->update('game.gw_game_member',array('gold_num' => $remainGoldNum),'member_id = :member_id',array(':member_id' => $memberId));
            $connection->commit();
            $flag = true;
        }catch (Exception $e){
            $connection->rollBack();
            $result['errorMsg'] = $e->getMessage();
            Yii::app()->db->createCommand()->update('game.gw_game_expend',array('result' => json_encode($result)),"id = '{$id}'");
            $this->returnResult(GameModule::RESULT_CODE_0, $result);
        }
        if($flag){
            $result = array(
                'gameMsgId' => $msgData['gameMsgId'],
                'isBuy' => true,
                'goldNum' => $remainGoldNum,//拥有的金币数量
            );
            //更新状态码,返回结果和现有金币数目
            Yii::app()->db->createCommand()->update('game.gw_game_expend',array(
                'result' => json_encode($result),'gold_num' => $remainGoldNum,'result_code' => GameModule::RESULT_CODE_1),
                "id = '{$id}'");
        }
        $this->returnResult(GameModule::RESULT_CODE_1, $result);//返回结果给客户端
    }
}