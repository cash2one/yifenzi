<?php

/**
 * 积分挂单规则、政策
 *
 * The followings are the available columns in table '{{guadan_rule}}':
 * @property integer $id
 * @property string $collect_id
 * @property integer $type
 * @property string $title
 * @property string $amount_give
 * @property string $amount
 * @property string $amount_pay
 * @property string $amount_limit
 * @property integer $amount_installment
 * @property integer $give_installment
 * @property integer $installment_time
 * @property string $remark
 */
class GuadanRule extends CActiveRecord
{
    const NEW_MEMBER = 1;//类型为新用户
    const OLD_MEMBER = 2;//类型为老用户
    /**
     * @var int 最大的积分面额，不能大于积分挂单提取,用户ar验证
     */
    public $amount_bind = 0;
    public $amount_unbind = 0;

    const STATUS_NEW= 0;
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;
    const STAUS_FINISHED = 3; //已完结
    


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{guadan_rule}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, amount_give, amount, amount_pay, amount_limit, give_installment,amount_installment,installment_time', 'required'),
            array('type, amount_installment, give_installment,installment_time', 'numerical', 'integerOnly' => true, 'max' => 100, 'min' => 1),
            array('collect_id, amount_give, amount, amount_pay', 'length', 'max' => 10),
            array('amount_give,amount,amount_pay', 'numerical', 'on' => 'create,update',
                'numberPattern' => '/^[0-9]+(.[0-9]{1,2})?$/'), //正整数、最多两位小数
            array('title','length','max'=>50,'min'=>2,'on'=>'create,update'),
            array('amount_limit', 'length', 'max' => 18),
            array('amount_limit','compare','compareAttribute'=>'amount','operator'=>'>='),
            array('remark', 'length', 'max' => 350),
            array('amount,amount_pay', 'compare', 'on' => 'create', 'compareAttribute' => 'amount_bind', 'operator' => '<='),
            array('amount_give', 'compare', 'on' => 'create', 'compareAttribute' => 'amount_unbind', 'operator' => '<='),
            array('amount_bind,amount_unbind', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, collect_id, type, amount_give, amount, amount_pay, amount_limit, amount_installment, give_installment, remark', 'safe', 'on' => 'search'),
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
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'collect_id' => '挂单提取id',
            'type' => '规则类型：1.新用户 2.老用户',
            'amount_give' => '赠送金额',
            'title' => '商品名',
            'amount' => '积分面值',
            'amount_pay' => '售价',
            'amount_limit' => '积分包限额',
            'amount_installment' => '本金返回分期',
            'give_installment' => '赠送返回分期',
            'installment_time' => '分期间隔',
            'remark' => '说明',
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
        $criteria->compare('collect_id', $this->collect_id, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('amount_give', $this->amount_give, true);
        $criteria->compare('amount', $this->amount, true);
        $criteria->compare('amount_pay', $this->amount_pay, true);
        $criteria->compare('amount_limit', $this->amount_limit, true);
        $criteria->compare('amount_installment', $this->amount_installment);
        $criteria->compare('give_installment', $this->give_installment);
        $criteria->compare('remark', $this->remark, true);
        $criteria->order = 'id DESC';
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return GuadanRule the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 获取提取规则、政策
     * @param int $collect_id
     * @param int $type
     * @return array
     */
    public static function getRule($collect_id, $type)
    {
        return Yii::app()->db->createCommand()
            ->select("*")
            ->from("{{guadan_rule}}")
            ->where(array('and', 'collect_id=' . $collect_id, 'type=' . $type))
            ->queryAll();
    }
}
