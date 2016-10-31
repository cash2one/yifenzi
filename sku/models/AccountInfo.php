<?php

/**
 * 
 * @property string $id
 * @property string $gai_number
 * @property string $account_id
 * @property string $card_no
 * @property integer $type
 * @property string $create_time
 * @property string $remark
 */
class AccountInfo extends CActiveRecord {

    const TYPE_MERCHANT = 1; // 商家
    const TYPE_AGENT = 2; // 代理
    const TYPE_CONSUME = 3; // 消费
    const TYPE_RETURN = 4; // 待返还
    const TYPE_FREEZE = 5; //  冻结
    const TYPE_COMMON = 6; // 公共
    const TYPE_SIGN = 101;	//签到积分(不记录流水)
    const TYPE_GAME = 102;	//游戏币(不记录流水)
    const TYPE_TOTAL = 9; // 总账户，充值、中转
    const TYPE_RED = 7; //红包账户

    public function tableName() {
        return '{{account_info}}';
    }

    public function rules() {
        return array(
            array('gai_number, account_id, card_no, type, create_time', 'required'),
            array('type', 'numerical', 'integerOnly' => true),
            array('gai_number, card_no', 'length', 'max' => 32),
            array('account_id, create_time', 'length', 'max' => 11),
            array('remark', 'safe'),
            array('id, gai_number, account_id, card_no, type, create_time, remark', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
        );
    }

    public function attributeLabels() {
        return array(
            'id' => '主键',
            'gai_number' => 'GW号',
            'account_id' => '所属账号',
            'card_no' => '卡号',
            'type' => '类型（1商家、2代理、3消费、4待返还、5冻结、6、盖网公共、11总账户）',
            'create_time' => '创建时间',
            'remark' => '备注',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('gai_number', $this->gai_number, true);
        $criteria->compare('account_id', $this->account_id, true);
        $criteria->compare('card_no', $this->card_no, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('remark', $this->remark, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getDbConnection() {
        return Yii::app()->ac;
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 创建及获取账户信息
     * @param array
     *         $arr = array(
     *              'account_id'=>
     *              'type'=>
     *              'gai_number'=>
     *          );
     * @return array
     */
    public static function findRecord($array) {
        $table = self::model()->tableName();
        $account = Yii::app()->db->createCommand()->select()->from('account.' . $table)
                        ->where('`account_id`=' . $array['account_id'] . ' and `type`=' . $array['type'] . ' and `gai_number`="' . $array['gai_number'] . '"')->queryRow();
        if ($account) {
            return $account;
        } else {
            $array['create_time'] = time();
            $array['card_no'] = self::createCardNo($array['type']);
            Yii::app()->db->createCommand()->insert('account.' . $table, $array);
            $array['id'] = Yii::app()->db->lastInsertID;
            return $array;
        }
    }

    /**
     * 生成唯一卡号
     * @param int $type
     * @return string
     */
    public static function createCardNo($type) {
        switch ($type) {
            case self::TYPE_MERCHANT:
                $front = self::TYPE_MERCHANT;
                break;
            case self::TYPE_AGENT:
                $front = self::TYPE_AGENT;
                break;
            case self::TYPE_CONSUME:
                $front = self::TYPE_CONSUME;
                break;
            case self::TYPE_RETURN:
                $front = self::TYPE_RETURN;
                break;
            case self::TYPE_FREEZE:
                $front = self::TYPE_FREEZE;
                break;
            case self::TYPE_COMMON:
                $front = self::TYPE_COMMON;
                break;
            case self::TYPE_TOTAL:
                $front = self::TYPE_TOTAL;
                break;
            default :
                $front = 0;
                break;
        }
        return $front . time() . rand(10000, 99999);
    }

}
