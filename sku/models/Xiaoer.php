<?php

/**
 * This is the model class for table "{{xiaoer}}".
 *
 * The followings are the available columns in table '{{xiaoer}}':
 * @property string $id
 * @property integer $member_id
 * @property integer $partner_id
 * @property integer $xiaoer_member_id
 * @property integer $status
 * @property integer $create_time
 */
class Xiaoer extends CActiveRecord
{
    public $gai_number;
    public $partner_gai_number;
    const STATUS_N = 0;
    const STAYUS_Y = 1;
    /**
     * 获取状态
     */
    public static function getStatus($key =null){
        $data = array(
        self::STATUS_N=>Yii::t('xiaoer', '禁用'),
        self::STAYUS_Y=>Yii::t('xiaoer','启用'),
        );
        return $key === null ? $data : $data[$key];
    }
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{xiaoer}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, partner_id, xiaoer_member_id, status, create_time', 'numerical', 'integerOnly'=>true),
                                                     array('gai_number','required','message' =>Yii::t('xiaoRe', '盖网号必填')),
                                                     array('gai_number','checkGw','on'=>'create,update,barcode_add,barcode_update'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, partner_id, xiaoer_member_id, status, create_time', 'safe', 'on'=>'search'),
		);
	}
        
         /**
     * 检查盖网号是否存在
     * @param type $attribute
     * @param type $params
     */
    public function checkGw($attribute, $params) {
//        var_dump(111);
    	 $xiao = Member::getByGwNumber($this->$attribute);
    	if ($this->scenario=='create') {
                                     if($this->$attribute == $this->partner_gai_number){
                                        $this->addError($attribute, Yii::t('xiaoer', '商家不能成为自己的店小二'));
                                    }
    		$count = Xiaoer::model()->count('member_id=:member_id AND xiaoer_member_id=:x_id',array(':member_id'=>$this->member_id,':x_id'=>$xiao['id']));
//                    var_dump($xiao);
    		if ($count>0) {
    			$this->addError($attribute, Yii::t('xiaoer', '此盖网号已成为店小二'));
    		}
                                
    	}
    	
    	if ($this->scenario=='update') {
                                    if($this->$attribute == $this->partner_gai_number){
                                        $this->addError($attribute, Yii::t('xiaoer', '商家不能成为自己的店小二'));
                                    }
    		$rs =	Yii::app()->db->createCommand()
    			->select('id')
		    	->from('{{xiaoer}}')
		    	->where('member_id=:member_id AND xiaoer_member_id=:x_id', array(':member_id'=>$this->member_id,':x_id'=>$xiao['id']))
		    	->queryRow();
    		if (!empty($rs) && $rs['id']!=$this->id) {
    			$this->addError($attribute, Yii::t('xiaoer', '此盖网号已成为店小二'));
    		}
    	}

    	return true;
    	
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'member'=>array(self::BELONGS_TO,'Member','xiaoer_member_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'member_id' => 'Member',
			'partner_id' => 'Partner',
			'xiaoer_member_id' => 'Xiaoer Member',
			'status' => '状态',
			'create_time' => 'Create Time',
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
//
//		$criteria->compare('id',$this->id,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('partner_id',$this->partner_id);
//		$criteria->compare('xiaoer_member_id',$this->xiaoer_member_id);
//		$criteria->compare('status',$this->status,true);
//		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
	public function partnerSearch()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
	
		$criteria=new CDbCriteria;
		//
		//		$criteria->compare('id',$this->id,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('partner_id',$this->partner_id);
		//		$criteria->compare('xiaoer_member_id',$this->xiaoer_member_id);
		//		$criteria->compare('status',$this->status,true);
		//		$criteria->compare('create_time',$this->create_time);
		$criteria->with = 'member';
	
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Xiaoer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
