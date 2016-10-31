<?php

/**
 * This is the model class for table "{{operator_relation}}".
 *
 * The followings are the available columns in table '{{operator_relation}}':
 * @property integer $id
 * @property integer $member_id
 * @property integer $partner_id
 * @property integer $operator_member_id
 * @property integer $operator_partner_id
 * @property integer $status
 * @property integer $create_time
 */
class OperatorRelation extends CActiveRecord
{
	public $gai_number;
    public $m_gai_number;
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;
    public $p_gai_number;
    public $create_times;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{operator_relation}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('id, member_id, partner_id, operator_member_id, operator_partner_id, status, create_time', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, partner_id, operator_member_id, operator_partner_id, status, create_time, m_gai_number, p_gai_number', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
           // 'partner'=>array(self::BELONGS_TO,'Partners','partner_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'member_id' => '商家会员id',
			'partner_id' => '商家id',
			'operator_member_id' => '运营方会员id',
			'operator_partner_id' => '运营方商家id',
			'status' => '状态，1为有效  0为失效',
            'status_name' => '状态',
			'create_time' => '创建时间',
            'create_times' => '绑定时间',
            'm_gai_number'=>'商家GW号',
            'p_gai_number'=>'运营商GW号',
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

		$criteria=new CDbCriteria;
        $criteria->select = 't.id,t.status,t.create_time as create_times,p.gai_number as p_gai_number,m.gai_number as m_gai_number';
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.member_id',$this->member_id);
		$criteria->compare('t.partner_id',$this->partner_id);
		$criteria->compare('t.operator_member_id',$this->operator_member_id);
		$criteria->compare('t.operator_partner_id',$this->operator_partner_id);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.create_time',$this->create_time);
        $criteria->compare('m.gai_number',$this->m_gai_number);
        $criteria->compare('p.gai_number',$this->p_gai_number);
        $criteria->join = "LEFT JOIN {{partners}} as m ON  m.id=t.partner_id LEFT JOIN {{partners}} as p ON t.operator_partner_id=p.id";
        $criteria->order = 't.id DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OperatorRelation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     *
     */
    public static function getOneRestule($id){

        $result = Yii::app()->db->createCommand()->select("t.id,t.create_time,t.status,pr.gai_number as pr_gai_number,ps.gai_number as ps_gai_number")
            ->from("{{operator_relation}} as t")
            ->leftJoin("{{partners}} as pr",'pr.id=t.partner_id')
            ->leftJoin("{{partners}} as ps",'ps.id=t.operator_partner_id')
            ->where('t.id=:tid',array(':tid'=>$id))
            ->queryRow();
            return $result;
    }

    static public function getStaus($status){
        $string = '';
        switch($status){
            case 1:
                $string = '有效';
                break;
            case 0:
                $string = '无效';
                break;
            default:

        }
        return $string;
    }
    
    /**
     *获取状态 
     */
    public static function getStatusName($status = null){
        $arr = array(
            self::STATUS_DISABLE => Yii::t('operatorRelation', '无效'),
            self::STATUS_ENABLE => Yii::t('operatorRelation', '有效'),
        );
        if (is_numeric($status)) {
            return isset($arr[$status]) ? $arr[$status] : null;
        } else {
            return $arr;
        }
    }


    /**检测商家
     * @param $partnerId
     * @param null $id
     * @return bool
     */
    static public function checkPartnerId($partnerId,$id=0){
            if($id == 0){
                $result = Yii::app()->db->createCommand()->select('id')->from("{{operator_relation}}")->where('partner_id=:partner_id',array(':partner_id'=>$partnerId))->queryRow();
            }else{
                $result = Yii::app()->db->createCommand()->select('id')->from("{{operator_relation}}")->where('partner_id=:partner_id and id!=:id',array(':partner_id'=>$partnerId,':id'=>$id))->queryRow();
            }

        return $result ? 0 : 1;
    }

}
