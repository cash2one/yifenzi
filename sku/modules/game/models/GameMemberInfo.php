<?php
/**
 * 游戏信息模型
 * @author: xiaoyan.luo
 * @mail: xiaoyan.luo@g-emall.com
 * Date: 2015/9/23 14:36
 */

class GameMemberInfo extends CActiveRecord
{
    //游戏类型
    const GAME_TYPE_SANGUORUN = 21;     //三国跑跑
    const GAME_TYPE_PAIPAIMENG = 22;    //啪啪萌僵尸
    const GAME_TYPE_GOLDENMINER = 23;   //盖付通黄金矿工
    const GAME_TYPE_SHENTOULILI = 24;   //神偷莉莉
    const GAME_TYPE_PANZHIHUA = 25;     //攀枝花抢水果
    const GAME_TYPE_DAFEIJI = 26;       //打飞机
    const GAME_TYPE_TANTIAOGONGZHU = 27;  //弹跳公主
    const GAME_TYPE_MENDJINGCHUANSHUO = 28;  //梦境传说

    //是否第一次游戏
    const NOT_FIRST_GAME = 0;   //非第一次游戏
    const IS_FIRST_GAME = 1;   //第一次游戏

    const DEFAULT_MAX_GAME_POWER = 5;  //游戏默认的最大体力值
    const DEFAULT_MIN_GAME_POWER = 0;  //游戏默认的最小体力值
    const DEFAULT_LIMIT_POWER_PLAY = 1; //默认玩游戏至少需要一个体力值
    const DEFAULT_RUN_OUT_POWER = 0 ; //游戏体力用完

    //黄金矿工游戏规则列表
    const FIRST_LOGIN_BOOM_GOLDENMINER = 0 ;   //黄金矿工首次登录炸弹数为零

    //购买的物品类别(黄金矿工用)
    const ITEM_TYPE_BOOM = 1; //炸药
    const ITEM_TYPE_SHENGLISHUI = 2; //生力水
    const ITEM_TYPE_LUCKYGRASS = 3; //幸运草
    const ITEM_TYPE_STONEBOOK = 4; //石头收藏书
    const ITEM_TYPE_GOODSTONE = 5; //优质矿石
    const ITEM_TYPE_FULLPOWER = 6; //补充满体力
    const ITEM_TYPE_TENSECOND = 7; //加时10秒
    const ITEM_TYPE_TWENTYSECOND = 8; //加时20秒
    const ITEM_TYPE_THRITYSECOND = 9; //加时30秒

    const CONTINUE_DAFEIJI_GOLDNUM = 1;    //打飞机游戏续关花费金币
    const GAIN_PANZHIHUA_GOLDNUM = 100;    //攀枝花抢水果游戏每抢一次水果花费100金币

    public function tableName()
    {
        return '{{member_info}}';
    }

    public function getDbConnection() {
        return Yii::app()->game;
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 获取游戏名称
     * @param bool $key
     * @return array
     */
    public static function getGameName($key = false) {
        $status = array(
            self::GAME_TYPE_SANGUORUN => '三国跑跑游戏',
            self::GAME_TYPE_PAIPAIMENG => '啪啪萌僵尸游戏',
            self::GAME_TYPE_GOLDENMINER => '盖付通黄金矿工游戏',
            self::GAME_TYPE_SHENTOULILI => '神偷莉莉游戏',
            self::GAME_TYPE_PANZHIHUA => '攀枝花抢水果游戏',
            self::GAME_TYPE_DAFEIJI => '盖付通打飞机游戏',
            self::GAME_TYPE_TANTIAOGONGZHU => '弹跳公主游戏',
            self::GAME_TYPE_MENDJINGCHUANSHUO => '梦境传说游戏',
        );
        if ($key === false)
            return $status;
        return $status[$key];
    }
}