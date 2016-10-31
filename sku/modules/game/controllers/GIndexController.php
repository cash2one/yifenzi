<?php

/**
 * 游戏通用接口
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/8/14 17:34
 */
class GIndexController extends GameBaseController
{
    public function actionIndex()
    {
        $data = $this->encrypt('{"name":"lili123","game":"rungame"}');
        echo '加密后的字符串为：' . $data;
        echo '<br/>';
        $data = $this->decrypt($data);
        print_r($data);
        echo '<br/>';
        echo '这是游戏模块的api接口';
    }

    /**
     * 通用游戏登录接口
     */
    public function actionLogin()
    {
        $msgData = $this->getPost('msgData');
        $msgData = str_replace('%2B', '+', $msgData);
        $msgData = $this->decrypt($msgData);

        if ($msgData == null) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => 'post数据为空'
            ));
        }

        if (!Validator::isMobile($msgData['account']) && !Validator::isGaiNumber($msgData['account'])) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '账号输入格式不正确'
            ));
        }

        $exists = Member::model()->exists('gai_number = :params or mobile = :params', array(':params' => $msgData['account']));
        if (!$exists) {
            $memberInfo = Yii::app()->gw->createCommand()
                ->select('m.id,m.gai_number,m.referrals_id,m.username,m.password,m.salt,m.sex,m.real_name,m.password2,m.password3,
                        m.birthday,m.mobile,m.country_id,m.province_id,m.district_id,m.street,m.register_time,m.register_type,
                        m.head_portrait,m.status,m.nickname,m.referrals_time,mt.ratio')
                ->from('gw_member m')
                ->join('gw_member_type mt', 'm.type_id = mt.id')
                ->where('gai_number = :params or mobile = :params', array(':params' => $msgData['account']))
                ->queryAll();
            if ($memberInfo == false) {
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
        if ($member['status'] != Member::STATUS_NO_ACTIVE && $member['status'] != Member::STATUS_NORMAL) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '此用户已经删除或者除名，不能登陆'
            ));
        }

        $countLoginError = 0;//初始错误登录次数为0
        $redis = Yii::app()->redis;
        if ($redis->exists("userLoginCount:{$member['gai_member_id']}")) {
            $countLoginError = $redis->get("userLoginCount:{$member['gai_member_id']}");
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
        $token = GameMember::createToken($member['gai_member_id'], $msgData['type']);//生成token

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
            Yii::app()->db->createCommand()->insert('game.gw_game_member', $memberData);
        } else {
            if ($gameData['token'] != $token) {
                GameMember::updateToken($gameData['id'], $token, $member['gai_member_id']);//更新token和登录时间
            }
        }

        //获取会员的游戏信息
        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id,is_first,max_score,max_combo,power'),
            'params' => array(':member_id' => $member['gai_member_id'], ':game_type' => $msgData['type'])
        ));
        if ($gameInfo == false) {
            $info['member_id'] = $member['gai_member_id'];
            $info['game_type'] = $msgData['type'];
            $info['is_first'] = GameMemberInfo::IS_FIRST_GAME;
            $info['power'] = isset($msgData['power']) ? $msgData['power'] : GameMemberInfo::DEFAULT_MAX_GAME_POWER;
            $info['update_power_time'] = time();
            $info['max_score'] = $info['max_combo'] = 0;
            Yii::app()->db->createCommand()->insert('game.gw_game_member_info', $info);
        }

        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'token' => $token
        );
        if (isset($captchas) && !empty($captchas)) $returnData['captchas'] = $captchas;
        $this->returnResult(GameModule::RESULT_CODE_1, $result);
    }

    /**
     * 注册
     */
    public function actionRegister(){
        $msgData = $this->getPost('msgData');
        $msgData = str_replace('%2B', '+', $msgData);
        $msgData = $this->decrypt($msgData);

        if ($msgData == null) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => 'post数据为空'
            ));
        }
        if (empty($msgData['mobile']))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '手机号不可为空'));

        //检查手机号是否已经被注册
        $mobile = Yii::app()->gw->createCommand()->select('gai_number')->from('{{member}}')
                ->where('mobile = :mobile',array(':mobile' => $msgData['mobile']))->queryRow();
        if ($mobile)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '该手机号已被注册'));
        if (empty($msgData['captcha']))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '验证码不可为空'));

        //检验验证码是否正确
        $overTime = time()-1800;
        $sql = "SELECT * FROM " . "`gt_checkcode` WHERE `phone` = '{$msgData['mobile']}' AND `create_time` >= '{$overTime}'";
        $res = Yii::app()->gt->createCommand($sql)->queryRow();
        if (empty($res) || $msgData['captcha'] != $res['checkcode']) {
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '验证码错误'));
        }else {
            $sql2 = "DELETE FROM" . "`gt_checkcode` WHERE `phone` = '{$msgData['mobile']}'";
            Yii::app()->gt->createCommand($sql2)->execute();
        }

        $transaction = Yii::app()->db->beginTransaction();
        $member = array();
        $flag = false;
        try{
            //生成用户表记录
            $salt =  Tool::generateSalt();    //加密原始密码串
            $hash = CPasswordHelper::hashPassword($msgData['password'] . $salt);
            $memberType = MemberType::getMemberType();
            $registerTime = time(); //注册时间
            $model = new Member();
            $gw = $model->generateGaiNumber();
            $member = array(
                'password' => $hash,
                'salt' => $salt,
                'register_time' => $registerTime,
                'gai_number' => $gw,
                'type_id' => $memberType['defaultType'],
                'mobile' => $msgData['mobile'],
                'status' => Member::STATUS_NORMAL,
                'is_master_account' => '1', //主账号
                'register_type' => Member::REGISTER_TYPE_GAME,
            );
            Yii::app()->db->createCommand()->insert('gaiwang.gw_member',$member);
            $memberId = Yii::app()->db->lastInsertId;
            $member['ratio'] = $memberType['default'];
            $member['gai_member_id'] = $memberId;
            $member['sku_number'] = $model->generateNumber();
            unset($member['type_id'],$member['is_master_account']);
            Yii::app()->db->createCommand()->insert('{{member}}',$member);
            $transaction->commit();
            $flag = true;
        }catch(Exception $e){
            $transaction->rollBack();
            Yii::log($e->getMessage());
            $result['errorMsg'] = $e->getMessage();
            $this->returnResult(GameModule::RESULT_CODE_0, $result);
        }

        if($flag == true){
            $token = GameMember::createToken($member['gai_member_id'], $msgData['type']);//生成token
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
                Yii::app()->db->createCommand()->insert('game.gw_game_member', $memberData);
            } else {
                if ($gameData['token'] != $token) {
                    GameMember::updateToken($gameData['id'], $token, $member['gai_member_id']);//更新token和登录时间
                }
            }

            //获取会员的游戏信息
            $gameInfo = GameMemberInfo::model()->find(array(
                'condition' => 'member_id = :member_id and game_type = :game_type',
                'select' => array('id,is_first,max_score,max_combo,power'),
                'params' => array(':member_id' => $member['gai_member_id'], ':game_type' => $msgData['type'])
            ));
            if ($gameInfo == false) {
                $info['member_id'] = $member['gai_member_id'];
                $info['game_type'] = $msgData['type'];
                $info['is_first'] = GameMemberInfo::IS_FIRST_GAME;
                $info['power'] = isset($msgData['power']) ? $msgData['power'] : GameMemberInfo::DEFAULT_MAX_GAME_POWER;
                $info['update_power_time'] = time();
                $info['max_score'] = $info['max_combo'] = 0;
                Yii::app()->db->createCommand()->insert('game.gw_game_member_info', $info);
            }

            $result = array(
                'gameMsgId' => $msgData['gameMsgId'],
                'token' => $token
            );
            $this->returnResult(GameModule::RESULT_CODE_1, $result);
        }
    }

    /**
     * 快捷登录
     */
    public function actionQuickLogin()
    {
        $msgData = $this->getPost('msgData');
        $msgData = str_replace('%2B', '+', $msgData);
        $msgData = $this->decrypt($msgData);

        if ($msgData == null) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => 'post数据为空'
            ));
        }
        if (empty($msgData['mobile']))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '手机号不可为空'));
        if (empty($msgData['captcha']))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '验证码不可为空'));


        $exists = Member::model()->exists('mobile = :mobile', array(':mobile' => $msgData['mobile']));
        if (!$exists) {
            $memberInfo = Yii::app()->gw->createCommand()
                ->select('m.id,m.gai_number,m.referrals_id,m.username,m.password,m.salt,m.sex,m.real_name,m.password2,m.password3,
                        m.birthday,m.mobile,m.country_id,m.province_id,m.district_id,m.street,m.register_time,m.register_type,
                        m.head_portrait,m.status,m.nickname,m.referrals_time,mt.ratio')
                ->from('gw_member m')
                ->join('gw_member_type mt', 'm.type_id = mt.id')
                ->where('mobile = :mobile', array(':mobile' => $msgData['mobile']))
                ->queryRow();
            if ($memberInfo == false) {
                $this->returnResult(GameModule::RESULT_CODE_0, array(
                    'gameMsgId' => $msgData['gameMsgId'],
                    'errorMsg' => '用户未注册'
                ));
            }
            //同步盖网用户到sku
            $model = new Member();
            $skuMemberData = $memberInfo;
            $skuMemberData['gai_member_id'] = $memberInfo['id'];
            $skuMemberData['sku_number'] = $model->generateNumber();
            unset($skuMemberData['id']);
            Yii::app()->db->createCommand()->insert('{{member}}', $skuMemberData);
        }

        $member = Member::model()->findAll(array(
            'condition' => 'mobile = :params',
            'select' => array('id,gai_number,status,nickname,gai_member_id'),
            'params' => array(':params' => $msgData['mobile'])
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

        //验证码是否正确
        $overTime = time()-1800;
        $sql = "SELECT * FROM " . "`gt_checkcode` WHERE `phone` = '{$msgData['mobile']}' AND `create_time` >= '{$overTime}'";
        $res = Yii::app()->gt->createCommand($sql)->queryRow();
        if (empty($res) || $msgData['captcha'] != $res['checkcode']) {
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '验证码错误'));
        }else {
            $sql2 = "DELETE FROM" . "`gt_checkcode` WHERE `phone` = '{$msgData['mobile']}'";
            Yii::app()->gt->createCommand($sql2)->execute();
        }

        $member = $member[0];
        if ($member['status'] != Member::STATUS_NO_ACTIVE && $member['status'] != Member::STATUS_NORMAL) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '此用户已经删除或者除名，不能登陆'
            ));
        }

        $token = GameMember::createToken($member['gai_member_id'], $msgData['type']);//生成token
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
            Yii::app()->db->createCommand()->insert('game.gw_game_member', $memberData);
        } else {
            if ($gameData['token'] != $token) {
                GameMember::updateToken($gameData['id'], $token, $member['gai_member_id']);//更新token和登录时间
            }
        }

        //获取会员的游戏信息
        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id,is_first,max_score,max_combo,power'),
            'params' => array(':member_id' => $member['gai_member_id'], ':game_type' => $msgData['type'])
        ));
        if ($gameInfo == false) {
            $info['member_id'] = $member['gai_member_id'];
            $info['game_type'] = $msgData['type'];
            $info['is_first'] = GameMemberInfo::IS_FIRST_GAME;
            $info['power'] = isset($msgData['power']) ? $msgData['power'] : GameMemberInfo::DEFAULT_MAX_GAME_POWER;
            $info['update_power_time'] = time();
            $info['max_score'] = $info['max_combo'] = 0;
            Yii::app()->db->createCommand()->insert('game.gw_game_member_info', $info);
        }

        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'token' => $token
        );
        $this->returnResult(GameModule::RESULT_CODE_1, $result);
    }

    /**
     * 获取游戏信息
     */
    public function actionInfo()
    {
        $msgData = $this->getPost('msgData');
        $msgData = str_replace('%2B', '+', $msgData);
        $msgData = $this->decrypt($msgData);

        if ($msgData == null) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => 'post数据为空'
            ));
        }

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

        //查会员信息
        $member = Member::model()->find(array(
            'select' => array('id,sku_number,gai_number,ratio,nickname'),
            'condition' => 'gai_number = :gai_number',
            'params' => array(':gai_number' => $gaiNumber)
        ));

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
            'params' => array(':member_id' => $memberId)
        ));
        if ($gameData == false) {
            Yii::app()->db->createCommand()->insert('game.gw_game_member', $memberData);
        } else {
            if ($gameData['token'] != $msgData['token']) {
                GameMember::updateToken($gameData['id'], $msgData['token'], $memberId);
            }
        }

        //查找是否有该会员登录该游戏的信息，无信息则增加游戏信息
        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id,is_first,boom,power,max_score,max_combo,update_power_time'),
            'params' => array(':member_id' => $memberId, ':game_type' => $msgData['type'])
        ));
        if ($gameInfo == false) {
            $info['member_id'] = $memberId;
            $info['game_type'] = $msgData['type'];
            $info['is_first'] = GameMemberInfo::IS_FIRST_GAME;
            $info['power'] = GameMemberInfo::DEFAULT_MAX_GAME_POWER;
            $info['update_power_time'] = time();
            $info['max_score'] = $info['max_combo'] = 0;
            Yii::app()->db->createCommand()->insert('game.gw_game_member_info', $info);
        }

        AccountBalance::findRecord(array(
            'account_id' => $member['id'],
            'type' => AccountBalance::TYPE_CONSUME,
            'sku_number' => $member['sku_number']
        ));
        $totalBalance = AccountBalance::getAccountAllBalance($member['sku_number'], AccountInfo::TYPE_CONSUME);//会员消费账户余额

        $types = MemberType::getMemberType();//获取会员类型数据
        $totalIntegral = bcdiv($totalBalance, $member['ratio'], 2);//会员积分数量
        $goldNum = bcmul($member['ratio'] / $types['official'], GameExchange::EXCHANGE_GOLD_NUM, GameResult::SCALE);//会员1积分可兑换金币数

        //查询是否要领取新手奖励
        $gameReward = GameReward::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id'),
            'params' => array(':member_id' => $memberId, ':game_type' => $msgData['type'])
        ));
        $isNewPlayerReward = $gameReward == false ? true : false;

        $returnData = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'goldNum' => isset($gameData['gold_num']) ? $gameData['gold_num'] : $memberData['gold_num'],
            'scoreNum' => $totalIntegral,
            'nickName' => $member['nickname'],
            'isFirst' => (isset($info['is_first']) || !empty($gameInfo['is_first'])) ? true : false,
            'maxCombo' => isset($gameInfo['max_combo']) ? $gameInfo['max_combo'] : $info['max_combo'],
            'maxScore' => isset($gameInfo['max_score']) ? $gameInfo['max_score'] : $info['max_score'],
            'power' => isset($gameInfo['power']) ? $gameInfo['power'] : $info['power'],
            'boom' => isset($gameInfo['boom']) ? $gameInfo['boom'] : $info['boom'],
            'updatePowerTime' => isset($gameInfo['update_power_time']) ? $gameInfo['update_power_time'] : $info['update_power_time'],
            'isNewPlayerReward' => $isNewPlayerReward,
            'oneIntegralGoldNum' => $goldNum, //该会员1积分可兑换金币数
        );
        $this->returnResult(GameModule::RESULT_CODE_1, $returnData);
    }

    /**
     * 积分兑换金币
     */
    public function actionExchange()
    {
        $msgData = $this->getPost('msgData');
        $msgData = str_replace('%2B', '+', $msgData);
        $msgData = $this->decrypt($msgData);

        if ($msgData == null) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => 'post数据为空'
            ));
        }

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
            'game_type' => $msgData['type'],
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
            $apiLogData['remark'] = GameMemberInfo::getGameName($msgData['type']) . '金币兑换';;
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

    /**
     * 发送登录短信接口
     * @author xiaoyan.luo
     */
    public function actionSend()
    {
        $msgData = $this->getPost('msgData');
        $msgData = str_replace('%2B', '+', $msgData);
        $msgData = $this->decrypt($msgData);

        if ($msgData == null) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => 'post数据为空'
            ));
        }

        if (empty($msgData['mobile']))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '手机号不可为空'));
        if (!Validator::isMobile($msgData['mobile']))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '手机号格式有误'));

        //用户信息是否存在
        $member = Member::model()->find(array(
            'condition' => 'mobile = :mobile',
            'select' => array('id,gai_number,status,nickname'),
            'params' => array(':mobile' => $msgData['mobile'])
        ));
        if ($member == false) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '用户未注册'
            ));
        }

        $connection = Yii::app()->gt;
        $verifyCode = mt_rand(10000, 99999);
        $createTime = time(); //验证码时间
        $sql = "REPLACE INTO `gt_checkcode`" . " VALUES ('{$msgData['mobile']}', '{$verifyCode}', '{$createTime}')";
        $res = $connection->createCommand($sql)->execute();
        if ($res) {
            $smsContent = "欢迎使用盖网，您的验证码是：{0}。该验证码5分钟内有效且只能使用一次，切忌转发。如非本人操作请忽略本短信。";
            $msg = str_replace("{0}", "{$verifyCode}", $smsContent);
            $apiMember = new ApiMember();
            $result = $apiMember->sendSms($msgData['mobile'], $msg,  ApiMember::SMS_TYPE_CAPTCHA,0,  ApiMember::YOUXI_SEND_SMS,array($verifyCode),ApiMember::CAPTCHA);
            if ($result == true) $this->returnResult(GameModule::RESULT_CODE_1, array('gameMsgId' => $msgData['gameMsgId'], 'code' => $verifyCode, 'createTime' => $createTime));
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '短信发送失败'));
        } else {
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '插入验证码记录失败'));
        }
    }

    /**
     * 发送注册短信
     */
    public function actionSendRegister(){
        $msgData = $this->getPost('msgData');
        $msgData = str_replace('%2B', '+', $msgData);
        $msgData = $this->decrypt($msgData);

        if ($msgData == null) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => 'post数据为空'
            ));
        }

        if (empty($msgData['mobile']))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '手机号不可为空'));
        if (!Validator::isMobile($msgData['mobile']))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '手机号格式有误'));

        //用户信息是否存在
        $member = Member::model()->find(array(
            'condition' => 'mobile = :mobile',
            'select' => array('id,gai_number,status,type_id,nickname'),
            'params' => array(':mobile' => $msgData['mobile'])
        ));
        if ($member) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => '用户已经注册，请直接登录'
            ));
        }

        $connection = Yii::app()->gt;
        $verifyCode = mt_rand(10000, 99999);
        $createTime = time(); //验证码时间
        $sql = "REPLACE INTO `gt_checkcode`" . " VALUES ('{$msgData['mobile']}', '{$verifyCode}', '{$createTime}')";
        $res = $connection->createCommand($sql)->execute();
        if ($res) {
            $smsContent = "欢迎使用盖网，您的验证码是：{0}。该验证码5分钟内有效且只能使用一次，切忌转发。如非本人操作请忽略本短信。";
            $msg = str_replace("{0}", "{$verifyCode}", $smsContent);
            $apiMember = new ApiMember();
            $result = $apiMember->sendSms($msgData['mobile'], $msg,  ApiMember::SMS_TYPE_CAPTCHA,0,  ApiMember::YOUXI_SEND_SMS,array($verifyCode),ApiMember::CAPTCHA);
            if ($result == true) $this->returnResult(GameModule::RESULT_CODE_1, array('gameMsgId' => $msgData['gameMsgId'], 'code' => $verifyCode, 'createTime' => $createTime));
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '短信发送失败'));
        } else {
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'], 'errorMsg' => '插入验证码记录失败'));
        }
    }

    /**
     * 使用金币
     */
    public function actionBuyItem(){
        $msgData = $this->getPost('msgData');
        $msgData = str_replace('%2B', '+', $msgData);
        $msgData = $this->decrypt($msgData);

        if ($msgData == null) {
            $this->returnResult(GameModule::RESULT_CODE_0, array(
                'gameMsgId' => $msgData['gameMsgId'],
                'errorMsg' => 'post数据为空'
            ));
        }

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
            'params' => array(':member_id' => $memberId, ':game_type' => $msgData['type'])
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
            'game_type' => $msgData['type'],
            'token' => $msgData['token'],
            'msg_id' => $msgData['gameMsgId'],//游戏协议ID
            'expenditure' => $msgData['price'],//花费金币数
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