<?php
/**
 * 游戏会员模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/8/17 16:36
 */

class GameMember extends CActiveRecord
{
    const CACHE_PREFIX = 'LPT'; //设置缓存名称前缀
    const TOKEN_LENGTH = '20';//token Id 长度

    //游戏类型
    const GAME_TYPE_SANGUORUN = 1;    //三国跑跑
    const GAME_TYPE_PAIPAIMENG = 2;   //啪啪萌僵尸

    //游戏赠送金币列表
    const REWARD_GOLD_NUM_SANGUORUN = 18; //三国跑跑首次登录赠送金币18个
    const REWARD_GOLD_NUM_PAIPAIMENG = 0; //啪啪萌僵尸首次登录不赠送金币

    public function tableName()
    {
        return '{{member}}';
    }

    public function getDbConnection() {
        return Yii::app()->game;
    }


    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 更新用户的token
     * @param $id
     * @param $token
     * @param $memberId
     */
    public static function updateToken($id, $token, $memberId){
        $connection = Yii::app()->gw;
        $sql = "UPDATE " . GAME . "." . "`gw_game_member` SET `token` = '{$token}', `login_time` = " . time() . " WHERE `id` = '{$id}'";
        $result = $connection->createCommand($sql)->execute();
        if($result){
            $redis = Yii::app()->redis;
            $memberIdKey = self::CACHE_PREFIX . ':tokenId:' . $token . ':memberId';
            $tokenIdKey = self::CACHE_PREFIX. ':memberId:' . $memberId . ':tokenId';
            $redis->set($tokenIdKey, $token, 86400);
            $redis->set($memberIdKey, $memberId, 86400);
        }
    }

    /**
     * 生成登录token
     * @param $memberId
     * @param $app_type
     * @param string $expire
     * @param $token_type
     * @return string
     */
    public static function createToken($memberId, $app_type,$expire='3600',$token_type = MemberToken::TYPE_CUSTOMER) {
        $tokenId = self::randomstr(self::TOKEN_LENGTH);
        $tokenIdKey = self::CACHE_PREFIX . ':memberId:' . $memberId . ':tokenId';
        $memberIdKey = self::CACHE_PREFIX . ':tokenId:' . $tokenId . ':memberId';
        // 检查token是否存在
        $oldToken = self::model()->find(array(
            'condition' => 'token = :token',
            'select' => array('id'),
            'params' => array(':token' => $tokenId)
        ));
        $redis = Yii::app()->redis;
        if($oldToken == false){
            $memberToken = MemberToken::model()->find(array(
                'condition' => 'token = :token',
                'select' => array('target_id'),
                'params' => array('token' => $tokenId)
            ));
            if($memberToken == false){
                // 清除token缓存
                if($cachedTokenId = $redis->get($tokenIdKey)){
                    $redis->delete($tokenIdKey);
                    $redis->delete(self::CACHE_PREFIX . ':tokenId:' . $cachedTokenId . ':memberId');
                }

                // 插入或更新数据库
                $data = array(
                    'token'=> $tokenId, 'start_time'=> time(), 'end_time'=> time()+86400,'type'=> MemberToken::TYPE_CUSTOMER
                );
                $existToken = MemberToken::model()->find(array(
                    'condition' => 'target_id = :target_id and app_type = :app_type and type = :type',
                    'select' => array('token'),
                    'params' => array(':target_id' => $memberId, ':app_type' => $app_type, ':type' => $token_type)
                ));
                if($existToken){
                    MemberToken::updateToken($memberId,$app_type,$data);
                }else{
                    $data['target_id'] = $memberId;
                    $data['app_type'] = $app_type;
                    MemberToken::insertToken($data);
                }

                // 设置token缓存
                $redis->set($tokenIdKey, $tokenId, $expire);
                $redis->set($memberIdKey, $memberId, $expire);
                return $tokenId;
            }
        }
        return self::createToken($memberId, $expire);
    }

    public static function randomstr($lenth = 6, $chars = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ') {
        $hash = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $lenth; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    /**
     * 检查token是否有效
     * @param $tokenId
     * @param string $expire
     * @return bool|void
     */
    public static function checkToken($tokenId, $expire = '86400')
    {
        $memberIdKey = self::CACHE_PREFIX . ':tokenId:' . $tokenId . ':memberId';
        $redis = Yii::app()->redis;
        if ($redis->exists($memberIdKey)) {
            $memberId = $redis->get($memberIdKey);
            return $memberId;
        }

        $memberToken = MemberToken::model()->find(array(
            'condition' => 'token = :token and end_time > :end_time',
            'select' => array('target_id'),
            'params' => array(':token' => $tokenId,':end_time' => time())
        ));

        // 设置token缓存
        if($memberToken['target_id']){
            $tokenIdKey = self::CACHE_PREFIX. ':memberId:' . $memberToken['target_id'] . ':tokenId';
            $redis->set($tokenIdKey, $tokenId, $expire);
            $redis->set($memberIdKey, $memberToken['target_id'], $expire);
            return $memberToken['target_id'];
        }

        $member = self::model()->find(array(
            'condition' => 'token = :token',
            'select' => array('member_id,login_time'),
            'params' => array(':token' => $tokenId)
        ));

        if(!empty($member)){
            if(bcadd($member['login_time'],$expire) < time()){
                self::deleteToken($member['member_id']);
                $redis->delete($memberIdKey);
                return false;
            }
        }
        $memberId = !empty($member['member_id']) ? $member['member_id'] : null;
        // 设置token缓存
        if ($memberId) {
            $tokenIdKey = self::CACHE_PREFIX . ':memberId:' . $memberId . ':tokenId';
            $redis->set($tokenIdKey, $tokenId, $expire);
            $redis->set($memberIdKey, $memberId, $expire);
            return $memberId;
        }
        return false;
    }

    /**
     * 按id删除用户的token记录
     * @param $id
     */
    public static function deleteToken($id){
        $connection = Yii::app()->game;
        $sql = "DELETE FROM `gw_game_member` WHERE `member_id` = '{$id}'";
        $connection->createCommand($sql)->execute();
    }
}