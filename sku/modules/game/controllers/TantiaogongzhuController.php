<?php
/**
 * 弹跳公主游戏接口
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2016/2/14 16:34
 */
class TantiaogongzhuController extends GameBaseController
{
    public function actionIndex(){
        $type = $this->getQuery('type');
        $msgData = $this->getPost('msgData');
        $msgData = str_replace('%2B', '+', $msgData);
        $msgData = $this->decrypt($msgData);

        if ($msgData == null) {
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => 'post数据为空'));
        }

        switch ($type) {
            case 'getGameData':
                $this->getGameData($msgData);
                break;
            case 'startGame':
                $this->getStartData($msgData);
                break;
            case 'buyFullPower':
                $this->buyFullPower($msgData);
                break;
            case 'gameResult':
                $this->getGameResult($msgData);
                break;
            case 'buyTimeToPlayGame':
                $this->buyTimeResult($msgData);
                break;
            default:
                $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '地址栏参数type为空'));
        }
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
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '登录过期，请重新登录'));

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
        if ($gameData == false){
            Yii::app()->db->createCommand()->insert('game.gw_game_member',$memberData);
        }else{
            if($gameData['token'] != $msgData['token']){
                GameMember::updateToken($gameData['id'], $msgData['token'],$memberId);
            }
        }

        //查找是否有该会员登录该游戏的信息，无信息则增加游戏信息
        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id,is_first,power,max_score,max_combo,update_power_time'),
            'params' => array(':member_id' => $memberId,':game_type' => GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU)
        ));
        if($gameInfo == false){
            $info['member_id'] = $memberId;
            $info['game_type'] = GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU;
            $info['is_first'] = GameMemberInfo::IS_FIRST_GAME;
            $info['power'] = GameMemberInfo::DEFAULT_MAX_GAME_POWER;
            $info['update_power_time'] = time();
            $info['max_score'] = $info['max_combo'] = 0;
            Yii::app()->db->createCommand()->insert('game.gw_game_member_info',$info);
        }

        AccountBalance::findRecord(array('account_id'=>$member['id'],'type'=>AccountBalance::TYPE_CONSUME, 'gai_number'=>$member['gai_number']));
        $totalBalance = AccountBalance::getAccountAllBalance($member['sku_number'],AccountInfo::TYPE_CONSUME);//会员消费账户余额
        $totalIntegral = bcdiv($totalBalance, $member['ratio'],2);//会员积分数量
        $types = MemberType::getMemberType();//获取会员类型数据
        $goldNum = bcmul($member['ratio']/$types['official'], GameExchange::EXCHANGE_GOLD_NUM, GameResult::SCALE);//会员1积分可兑换金币数
        $config = Tool::getConfigData('tantiaogongzhu',GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU);
        $config = $config[0];
        if(empty($config))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '游戏配置数据不存在'));
        if(empty($config['refection']) || $config['refection'] < 0)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '到下个体力的时间数据有误'));

        $updateTime = isset($info['update_power_time']) ? $info['update_power_time'] : $gameInfo['update_power_time'];
        $power = isset($gameInfo['power']) ? $gameInfo['power'] : $info['power'];
        if($power < GameMemberInfo::DEFAULT_MAX_GAME_POWER){
            $nextPowerTime = bcsub($config['refection'],bcsub(time(),$updateTime,GameResult::SCALE),GameResult::SCALE);//到下次更新体力时间
        }else{
            $nextPowerTime = 0;
        }
        $returnData = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'goldNum' => isset($gameData['gold_num']) ? $gameData['gold_num'] : $memberData['gold_num'],
            'scoreNum' => $totalIntegral,
            'nickName' => $member['nickname'],
            'isFirst' => (isset($info['is_first']) || !empty($gameInfo['is_first']))  ? true : false,
            'oneIntegralGoldNum' => $goldNum,
            'maxScore' => isset($gameInfo['max_score']) ? $gameInfo['max_score'] : $info['max_score'],
            'power' => $power,
            'nextPowerTime' => $nextPowerTime,
        );
        $this->returnResult(GameModule::RESULT_CODE_1, $returnData);
    }


    /**
     * 开始游戏
     * @param $msgData
     */
    private function getStartData($msgData){
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '登录过期，请重新登录'));

        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id,power,max_score,update_power_time'),
            'params' => array(':member_id' => $memberId, ':game_type' => GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU)
        ));
        if($gameInfo == false)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '游戏数据不存在'));

        //游戏开始扣除一个点的体力值
        $remainPower = $gameInfo['power'] - GameMemberInfo::DEFAULT_LIMIT_POWER_PLAY;
        if($remainPower < 0)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '剩余体力值不能小于0'));

        if($gameInfo['power'] == GameMemberInfo::DEFAULT_MAX_GAME_POWER){
            $time = time();
            $res = Yii::app()->db->createCommand()->update('game.gw_game_member_info',array(
                'power' => $remainPower,'update_power_time' =>  $time),"id = '{$gameInfo['id']}'");
        }else{
            $res = Yii::app()->db->createCommand()->update('game.gw_game_member_info',array('power' => $remainPower),"id = '{$gameInfo['id']}'");//扣除体力
        }
        if($res){
            $power = $remainPower;//剩余体力值
        }

        //获取游戏配置参数
        $config =Tool::getConfigData('tantiaogongzhu',GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU);
        $config = $config[0];
        if(empty($config))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '游戏配置数据不存在'));
        if(empty($config['refection']) || $config['refection'] < 0)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '到下个体力的时间数据有误'));

        //组装返回游戏端的数据
        $updateTime = isset($time) ? $time : $gameInfo['update_power_time'];
        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'power' => isset($power) ? $power : $gameInfo['power'],
            'canPlay' => $res ? true : false, //是否可以玩游戏
            'maxScore' => $gameInfo['max_score'],
            'nextPowerTime' => bcsub($config['refection'],bcsub(time(),$updateTime,GameResult::SCALE),GameResult::SCALE),
        );
        $this->returnResult(GameModule::RESULT_CODE_1,$result);
    }

    /**
     * 补充满体力
     * @param $msgData
     */
    private function buyFullPower($msgData){
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '登录过期，请重新登录'));

        $gameData = GameMember::model()->find(array(
            'condition' => 'member_id = :member_id',
            'select' => array('id,gold_num'),
            'params' => array(':member_id' => $memberId)
        ));
        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id,power,update_power_time'),
            'params' => array(':member_id' => $memberId, ':game_type' => GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU)
        ));
        if($gameInfo == false || $gameData == false)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '游戏数据不存在'));

        //获取啪啪萌游戏配置数据
        $config =Tool::getConfigData('tantiaogongzhu',GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU);
        $config = $config[0];

        if(empty($config))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '游戏配置数据不存在'));
        if(empty($config['refection']) || $config['refection'] < 0)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '到下个体力的时间数据有误'));
        if(empty($config['add_power_gold']) || $config['add_power_gold'] < 0)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '补充满体力要扣减金币数据有误'));

        //判断体力是否为用完状态
        if($gameInfo['power'] != GameMemberInfo::DEFAULT_RUN_OUT_POWER)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '体力值还没为用完状态'));

        //组装要返回给客户端的数据
        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'isBuy' => false,
            'goldNum' => $gameData['gold_num'],//拥有的金币数量
            'power' => $gameInfo['power'],
            'nextPowerTime' => bcsub($config['refection'],bcsub(time(),$gameInfo['update_power_time'],GameResult::SCALE),GameResult::SCALE),
        );

        //先将花费金币的记录存入数据库
        $resultData = array(
            'member_id' => $memberId,
            'game_type' => GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU,
            'token' => $msgData['token'],
            'msg_id' => $msgData['gameMsgId'],//游戏协议ID
            'expenditure' => $config['add_power_gold'],
            'gold_num' => $gameData['gold_num'],//先存原本金币数目，成功花费即存已经花费后的金币数
            'request' => json_encode($msgData),
            'result' => json_encode($result),
            'result_code' => GameModule::RESULT_CODE_0,
            'create_time' => time(),
            'update_time' => time()
        );
        $id = GameExpend::insertExpend($resultData);//花费金币记录存入数据库

        $connection = Yii::app()->db->beginTransaction();
        $remainGoldNum = bcsub($gameData['gold_num'], $config['add_power_gold'], GameResult::SCALE);
        $flag = false;

        try{
            if($gameData['gold_num'] < $config['add_power_gold'])throw new Exception('玩家金币不足');
            Yii::app()->db->createCommand()->update('game.gw_game_member',array('gold_num' => $remainGoldNum),'member_id = :member_id',array(':member_id' => $memberId));
            $updatePowerTime = time();
            Yii::app()->db->createCommand()->update('game.gw_game_member_info',array(
                'power' => GameMemberInfo::DEFAULT_MAX_GAME_POWER,'update_power_time' => $updatePowerTime),"id = '{$gameInfo['id']}'");
            $connection->commit();
            $flag = true;
        }catch (Exception $e){
            $connection->rollBack();
            $result['errorMsg'] = $e->getMessage();
            Yii::app()->db->createCommand()->update('game.gw_game_expend',array('result' => json_encode($result)),"id = '{$id}'");
            $this->returnResult(GameModule::RESULT_CODE_0, $result);
        }
        if($flag){
            $updateTime = isset($updatePowerTime) ? $updatePowerTime : $gameInfo['update_power_time'];
            $result = array(
                'gameMsgId' => $msgData['gameMsgId'],
                'isBuy' => true,
                'goldNum' => $remainGoldNum,//拥有的金币数量
                'power' => GameMemberInfo::DEFAULT_MAX_GAME_POWER,
                'nextPowerTime' => bcsub($config['refection'],bcsub(time(),$updateTime,GameResult::SCALE),GameResult::SCALE),
            );
            //更新状态码,返回结果和现有金币数目
            Yii::app()->db->createCommand()->update('game.gw_game_expend',array(
                'result' => json_encode($result),'gold_num' => $remainGoldNum,'result_code' => GameModule::RESULT_CODE_1),
                "id = '{$id}'");
        }
        $this->returnResult(GameModule::RESULT_CODE_1, $result);//返回结果给客户端
    }

    /**
     * 获取游戏结果
     * @param $msgData
     */
    private function getGameResult($msgData){
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '登录过期，请重新登录'));

        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id,max_score,max_combo'),
            'params' => array(':member_id' => $memberId, ':game_type' => GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU)
        ));
        if($gameInfo == false)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '游戏数据不存在'));

        //更新历史最高分数
        if($msgData['score'] > $gameInfo['max_score']){
            Yii::app()->db->createCommand()->update('game.gw_game_member_info',array('max_score' => $msgData['score']),"id = '{$gameInfo['id']}'");
            $maxScore = $msgData['score'];//历史最高分数
        }

        //组装返回给游戏端的数据
        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'maxScore' => isset($maxScore) ? $maxScore : $gameInfo['max_score'],
        );

        $this->returnResult(GameModule::RESULT_CODE_1,$result);
    }

    /**
     * 花费金币加时
     * @param $msgData
     */
    private function buyTimeResult($msgData){
        $memberId = GameMember::checkToken($msgData['token']);
        if ($memberId == false)
            $this->returnResult(GameModule::RESULT_CODE_6, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '登录过期，请重新登录'));


        $gameData = GameMember::model()->find(array(
            'condition' => 'member_id = :member_id',
            'select' => array('id,gold_num'),
            'params' => array(':member_id' => $memberId)
        ));
        $gameInfo = GameMemberInfo::model()->find(array(
            'condition' => 'member_id = :member_id and game_type = :game_type',
            'select' => array('id,power'),
            'params' => array(':member_id' => $memberId, ':game_type' => GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU)
        ));
        if($gameInfo == false || $gameData == false)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '游戏数据不存在'));

        //获取啪啪萌游戏配置数据
        $config =Tool::getConfigData('tantiaogongzhu', GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU);
        $config = $config[0];

        if(empty($config))
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '游戏配置数据不存在'));
        if(empty($config['addtime']) || $config['addtime'] < 0)
            $this->returnResult(GameModule::RESULT_CODE_0, array('gameMsgId' => $msgData['gameMsgId'],'errorMsg' => '加时要扣减金币数据有误'));

        //组装要返回给客户端的数据
        $result = array(
            'gameMsgId' => $msgData['gameMsgId'],
            'isBuy' => false,//是否成功花费金币加时
            'goldNum' => $gameData['gold_num'],//拥有的金币数量
        );

        //先将花费金币的记录存入数据库
        $resultData = array(
            'member_id' => $memberId,
            'game_type' => GameMemberInfo::GAME_TYPE_TANTIAOGONGZHU,
            'token' => $msgData['token'],
            'msg_id' => $msgData['gameMsgId'],//游戏协议ID
            'expenditure' => $config['addtime'],
            'gold_num' => $gameData['gold_num'],//先存原本金币数目，成功花费即存已经花费后的金币数
            'request' => json_encode($msgData),
            'result' => json_encode($result),
            'result_code' => GameModule::RESULT_CODE_0,
            'create_time' => time(),
            'update_time' => time()
        );
        $id = GameExpend::insertExpend($resultData);//花费金币记录存入数据库

        $connection = Yii::app()->db->beginTransaction();
        $remainGoldNum = bcsub($gameData['gold_num'], $config['addtime'], GameResult::SCALE);
        $flag = false;

        try{
            if($gameData['gold_num'] < $config['addtime'])throw new Exception('玩家金币不足');
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