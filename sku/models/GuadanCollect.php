<?php

/**
 *  积分挂单提取
 *
 * The followings are the available columns in table '{{guadan_collect}}':
 * @property integer $id
 * @property string $code
 * @property string $amount_bind
 * @property string $amount_unbind
 * @property integer $distribution_ratio
 * @property string $bind_size
 * @property string $time_start
 * @property string $time_end
 * @property string $create_time
 * @property int $status
 *
 */
class GuadanCollect extends CActiveRecord
{

    const STATUS_NEW = 0;
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2; //已中止
    const STAUS_FINISHED = 3; //已完结

    /**
     * @var int $maxBind 最大绑定金额
     */
    public $maxBind = 0;
    /**
     * @var int $maxUnbind 最大非绑定金额，用户校验、显示
     */
    public $maxUnbind = 0;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{guadan_collect}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('amount_bind, amount_unbind, distribution_ratio, bind_size, time_start, time_end', 'required'),
            array('distribution_ratio', 'numerical', 'integerOnly' => true, 'max' => 100, 'min' => 0),
            array('code', 'length', 'max' => 30),
            array('amount_bind, amount_unbind', 'length', 'max' => 18),
            array('bind_size', 'length', 'max' => 10),
            array('bind_size', 'checkValue'),
            array('time_start','checkTimeStart','on'=>'create'),
            array('time_end', 'comext.validators.compareDateTime', 'compareAttribute' => 'time_start', 'allowEmpty' => true,
                'operator' => '>', 'on' => 'create', 'message' => Yii::t('member', '计划截止日 必须大于 计划起始日')),
            array('amount_bind,amount_unbind,bind_size', 'numerical', 'on' => 'create',
                'numberPattern' => '/^[0-9]+(.[0-9]{1,2})?$/'), //正整数、最多两位小数
            array('amount_bind', 'compare', 'compareAttribute' => 'maxBind', 'operator' => '<='),
            array('amount_unbind', 'compare', 'compareAttribute' => 'maxUnbind', 'operator' => '<='),
            array('amount_bind','checkAmount'),
            array('amount_unbind','checkAmount'),
            array('maxBind,maxUnbind', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, code, amount_bind, amount_unbind, distribution_ratio, bind_size, time_start, time_end, create_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * 检查起始日期是否大于当前时间
     * @param $attribute
     * @param $params
     */
    public function checkTimeStart($attribute, $params){
        if(isset($this->$attribute)){
            $startTime = strtotime($this->$attribute);
            if($startTime < time()){
                $this->addError($attribute, '计划起始时间应不能早于当前时间');
            }
        }
    }
    public function checkAmount($attribute, $params){
        if(isset($this->$attribute)){
            if($this->$attribute < 10){
                $this->addError($attribute, '积分额度最少为10积分');
            }
        }
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'code' => '编号',
            'amount_bind' => '待绑定积分额度',
            'amount_unbind' => '非绑定积分额度',
            'distribution_ratio' => '推荐者分配比例',
            'bind_size' => '新用户绑定粒度',
            'time_start' => '计划起始日',
            'time_end' => '计划截止日',
            'create_time' => '创建时间',
            'status' => '状态', //（0待启用 1启用 2终止）
            'new_member_count' => '已获取新用户'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('amount_bind', $this->amount_bind, true);
        $criteria->compare('amount_unbind', $this->amount_unbind, true);
        $criteria->compare('distribution_ratio', $this->distribution_ratio);
        $criteria->compare('bind_size', $this->bind_size, true);
        $criteria->compare('time_start', $this->time_start, true);
        $criteria->compare('time_end', $this->time_end, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->order = 'id DESC';
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return GuadanCollect the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_time = time();
                $this->code = Tool::buildOrderNo(20, 'C');
            }
            if (!is_numeric($this->time_start)) {
                $this->time_start = strtotime($this->time_start);
                $this->time_end = strtotime($this->time_end);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 添加挂单提取关系
     * @param $ids
     * @throws CDbException
     * @throws Exception
     */
    public function addRelation($ids)
    {
        $trans = Yii::app()->db->beginTransaction();
        try {
            $data = Guadan::getGuadanByIds($ids);
            if (empty($data)) throw new Exception('查找挂单失败');
            //本次提取的积分
            $amount_bind = $this->amount_bind;
            $amount_unbind = $this->amount_unbind;
            
            $insertBind = array(); //绑定积分关系数据
            $insertUnbind = array(); //非绑定积分关系数据
            foreach ($data as $v) {
                if($v['amount_remain'] <= 0) continue; //剩余积分小于等于0跳过
                //绑定积分
                if ($v['type'] == Guadan::TYPE_TO_BIND) {
                    if ($amount_bind > 0) {
                        //如果提取小于 挂单
                        if ($v['amount_remain'] >= $amount_bind) {
                            $money = $amount_bind;
                            $amount_bind = 0;
                        } else {
                            //如果提取 大于 挂单
                            $money = $v['amount_remain'];
                            $amount_bind = bcsub($amount_bind, $v['amount_remain'], 2);
                        }
                        $insertBind[] = array(
                            'collect_id' => $this->id,
                            'guadan_id' => $v['id'],
                            'amount' => $money,
                            'amount_remain' => $money,
                            'type' => Guadan::TYPE_TO_BIND,
                            'gai_number' => $v['gai_number']
                        );
                    }
                } else {
                    //非绑定积分
                    if ($amount_unbind > 0) {
                        //如果提取小于 挂单
                        if ($v['amount_remain'] >= $amount_unbind) {
                            $money = $amount_unbind;
                            $amount_unbind = 0;
                        } else {
                            //如果提取 大于 挂单
                            $money = $v['amount_remain'];
                            $amount_unbind = bcsub($amount_unbind, $v['amount_remain'], 2);
                        }
                        $insertUnbind[] = array(
                            'collect_id' => $this->id,
                            'guadan_id' => $v['id'],
                            'amount' => $money,
                            'amount_remain' => $money,
                            'type' => Guadan::TYPE_NO_BIND,
                            'gai_number' => $v['gai_number']
                        );
                    }
                }
            } //endforeach
            //         $db = Yii::app()->db;
            //插入绑定积分关系数据，修改余额
            $insert = array_merge($insertBind, $insertUnbind);
            if (!empty($insert)) {
                foreach ($insert as $ib) {
                    $gai_number = $ib['gai_number'];
                    unset($ib['gai_number']);
                    Yii::app()->db->createCommand()->insert('{{guadan_relation}}', $ib);
                    //更新积分挂单余额
                    $sql = 'UPDATE {{guadan}} SET amount_remain=amount_remain-' . $ib['amount'] . ' WHERE id=' . $ib['guadan_id'] . ';';
                    Yii::app()->db->createCommand($sql)->execute();
                    /**
                     * 添加流水
                     */
                    $this->addFlow($gai_number, $ib['amount'], $ib['type']);
                }
                
            
            
            }
            $trans->commit();
             return true;
        }catch (Exception $e) {
             $trans->rollback();
             return false;
        }
        
    }


    /**
     * 挂单提取添加流水
     * @param string $gai_number gw
     * @param float $amount 金额
     * @param int $type 挂单类型（绑定、非绑定）
     * @throws Exception
     */
    public function addFlow($gai_number, $amount, $type)
    {
        if($amount=='0') throw new Exception($gai_number."挂单金额不能为0");
        $flow_table_name = AccountFlow::monthTable();
        $gdType = $type == Guadan::TYPE_NO_BIND ? CommonAccount::TYPE_GUADAN_SALE_UNBUNDLING : CommonAccount::TYPE_GUADAN_SALE_BINDING;
        //会员的流水
        $member = Member::getByGwNumber($gai_number, 'id,sku_number');
        if (!$member) throw new Exception("找不到" . $gai_number);
        $balance = AccountBalance::findRecord(array('account_id' => $member->id, 'type' => AccountBalance::TYPE_GUADAN_XIAOFEI, 'sku_number' => $member->sku_number));
        $remark = '挂单账户'.($amount>0?'转出':'转入').'：￥' . $amount .Guadan::getType($type).'积分';
        $flow = AccountFlow::mergeFlowData(array('id' => $this->id, 'code' => $this->code), $balance, array(
            'debit_amount' => $amount,
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_SALE,
            'remark' => $remark,
            'ratio' => 0,
            'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_SALE_OUT,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_GUADAN,
        ));
        Yii::app()->db->createCommand()->insert($flow_table_name, $flow);
        AccountBalance::calculate(array('today_amount'=>-$amount),$balance['id']);
        //售卖挂单积分池流水
        $balance = CommonAccount::getAccount($gdType, AccountInfo::TYPE_TOTAL, '售卖挂单积分池');  // 取得公共账户
        $remark = '售卖挂单积分池：'.($amount > 0 ? '转入' :'退回').'￥' . $amount .Guadan::getType($type).'积分';
        $flow = AccountFlow::mergeFlowData(array('id' => $this->id, 'code' => $this->code), $balance, array(
            'credit_amount' => $amount,
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_SALE,
            'remark' => $remark,
            'ratio' => 0,
            'node' => AccountFlow::BUSINESS_NODE_SKU_GUADAN_SALE_IN,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_GUADAN,
        ));
        Yii::app()->db->createCommand()->insert($flow_table_name, $flow);
        AccountBalance::calculate(array('today_amount'=>$amount),$balance['id']);
    }

    /**
     * 出售挂单积分-终止
     * @param string $gai_number gw
     * @param float $amount 金额
     * @param int $type 挂单类型（绑定、非绑定）
     * @throws Exception
     */
    public function stopFlow($gai_number, $amount, $type)
    {
        if($amount=='0') throw new Exception($gai_number."挂单金额不能为0");
        $flow_table_name = AccountFlow::monthTable();
        $gdType = $type == Guadan::TYPE_NO_BIND ? CommonAccount::TYPE_GUADAN_SALE_UNBUNDLING : CommonAccount::TYPE_GUADAN_SALE_BINDING;
        //会员的流水
        $member = Member::getByGwNumber($gai_number, 'id,sku_number');
        if (!$member) throw new Exception("找不到" . $gai_number);
        $balance = AccountBalance::findRecord(array('account_id' => $member->id, 'type' => AccountBalance::TYPE_GUADAN_XIAOFEI, 'sku_number' => $member->sku_number));
        $remark = '挂单账户转入：￥' . $amount .Guadan::getType($type).'积分';
        $flow = AccountFlow::mergeFlowData(array('id' => $this->id, 'code' => $this->code), $balance, array(
            'debit_amount' => -$amount,
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_SALE_STOP,
            'remark' => $remark,
            'ratio' => 0,
            'node' => AccountFlow::BUSINESS_NODE_SKU__GUADAN_SALE_STOP_OUT,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_GUADAN,
        ));
        Yii::app()->db->createCommand()->insert($flow_table_name, $flow);
        AccountBalance::calculate(array('today_amount'=>$amount),$balance['id']);
        //售卖挂单积分池流水
        $balance = CommonAccount::getAccount($gdType, AccountInfo::TYPE_TOTAL, '售卖挂单积分池');  // 取得公共账户
        $remark = '售卖挂单积分池：退回 ￥' . $amount .Guadan::getType($type).'积分';
        $flow = AccountFlow::mergeFlowData(array('id' => $this->id, 'code' => $this->code), $balance, array(
            'credit_amount' => -$amount,
            'operate_type' => AccountFlow::OPERATE_TYPE_SKU_GUADAN_SALE_STOP,
            'remark' => $remark,
            'ratio' => 0,
            'node' => AccountFlow::BUSINESS_NODE_SKU__GUADAN_SALE_STOP_IN,
            'transaction_type' => AccountFlow::TRANSACTION_TYPE_GUADAN,
        ));
        Yii::app()->db->createCommand()->insert($flow_table_name, $flow);
        AccountBalance::calculate(array('today_amount'=>-$amount),$balance['id']);
    }

    /**
     * 查找没有挂单规则的积分挂单
     * @param int $limit
     * @return array
     */
    public static function getNotRelation($limit = 10)
    {
        return Yii::app()->db->createCommand()->select("*")->from("{{guadan_collect}}")
            ->where('status = ' . self::STATUS_NEW)
            ->limit($limit)
            ->order('id DESC')
            ->queryAll();
    }
    
    public function checkValue($attribute,$param)
    {
//        if(!$this->hasErrors()){
        if((float)bcadd($this->amount_bind,$this->amount_unbind) < (float)$this->bind_size){
            $this->addError('bind_size',  Yii::t('GuadanCollect','绑定粒度不能大于总积分'));
        }
//        }
    }
}
