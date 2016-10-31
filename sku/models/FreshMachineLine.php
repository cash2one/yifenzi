<?php

/**
 * This is the model class for table "{{fresh_machine_line}}".
 *
 * The followings are the available columns in table '{{fresh_machine_line}}':
 * @property string $id
 * @property integer $machine_id
 * @property integer $rent_partner_id
 * @property integer $rent_member_id
 * @property string $code
 * @property integer status
 * @property integer $create_time
 */
class FreshMachineLine extends CActiveRecord
{
    public $gai_number;
    
 
    const STATUS_ENABLE = 1;  //可用
    const STATUS_DISABLE =2;    //禁用
    const STATUS_EMPLOY =3;     //占用


    /**
     * 货道状态
     */
    public static function getStatus($status = null) {
		$arr = array(
				self::STATUS_ENABLE => Yii::t('freshMachine', '可用'),
				self::STATUS_DISABLE => Yii::t('freshMachine', '禁用'),
				self::STATUS_EMPLOY => Yii::t('freshMachine', '占用'),
          
		);
		if (is_numeric($status)) {
			return isset($arr[$status]) ? $arr[$status] : Yii::t('freshMachine', '未知状态');
		} else {
			return $arr;
		}
	}
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{fresh_machine_line}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,code', 'required'),
            array('machine_id, rent_partner_id, status, rent_member_id, create_time', 'numerical', 'integerOnly'=>true),
            array('gai_number,expir_time', 'length', 'max'=>128),
           array('name','length','max'=>25),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('name,code','match','pattern'=>'/^[L|R]([1-9]{2})$/','message'=>Yii::t('freshMachine', '请输入格式为字母L/R加两位数字（数字不为0），格式：LXX或者RXX')),
        	array('code', 'safe'),
            array('code','length','max'=>10),
            array('code','checkCode'),
            array('gai_number','checkGai'),
            array('name','checkName','on'=>'create,update'),
            array('id, machine_id, rent_partner_id, rent_member_id, code, create_time, status, name, gai_number,expir_time', 'safe', 'on'=>'search'),
            //array('id, machine_id, code, create_time, status, name', 'safe', 'on'=>'lineSearch'),
        );
    }

    /**
     * 货道授权验证 验证GW号是否为合作商家
     */
     public function checkGai($attribute, $params) {
         if(isset($this->$attribute)){
        $gai = Partners::model()->find('gai_number=:gw', array(':gw' => $this->$attribute));
        if (empty($gai)) {
            $this->addError($attribute, Yii::t('freshMachine', '请输入正确的合作商盖网号'));
        }
         }
    }
    public function checkCode($attribute,$params){
        if(isset($this->$attribute)){
            if($this->scenario=='create'){
                $code =	Yii::app()->db->createCommand()
                    ->select('code')
                    ->from('{{fresh_machine_line}}')
                    ->where('machine_id=:machine_id AND code=:code', array(':machine_id'=>$this->machine_id,':code'=>$this->$attribute))
                    ->queryRow();
            }
            if($this->scenario=='update'){
                $code =	Yii::app()->db->createCommand()
                    ->select('id')
                    ->from('{{fresh_machine_line}}')
                    ->where('machine_id=:machine_id AND code=:code AND id !=:id', array(':machine_id'=>$this->machine_id,':code'=>$this->$attribute,':id'=>$this->id))
                    ->queryRow();
            }
            if(!empty($code)){
                $this->addError($attribute, Yii::t('freshMachine', '此货道编号已被取用'));
            }
        }

    }
    /**
     * 同一商家货道唯一
     */
    public function checkName($attribute, $params){
            	
    	if ($this->scenario=='create') {
    		$count = FreshMachineLine::model()->count('machine_id=:machine_id AND name=:name',array(':machine_id'=> $this->machine_id,':name'=>$this->$attribute));
    		if ($count>0) {
    			$this->addError($attribute, Yii::t('freshMachine', '货道已存在'));
    		}
    	}
    	
    	if ($this->scenario=='update') {
    		$rs =	Yii::app()->db->createCommand()
    			->select('id')
		    	->from('{{fresh_machine_line}}')
		    	->where('machine_id=:machine_id AND name=:name', array(':machine_id'=>$this->machine_id,':name'=>$this->$attribute))
		    	->queryRow();
    		if (!empty($rs) && $rs['id']!=$this->id) {
    			$this->addError($attribute, Yii::t('freshMachine', '货道已存在'));
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
            'partner' => array(self::BELONGS_TO, 'Partners', 'rent_partner_id'),
            'goodsStock' => array(self::HAS_MANY, 'GoodsStock', 'target'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('freshMachine', 'ID'),
            'machine_id' => Yii::t('freshMachine', '生鲜机id'),
            'gai_number'=>Yii::t('freshMachine', '租用货道商家盖网号'),
            'rent_partner_id' => Yii::t('freshMachine', '租用货道的合作商家id'),
            'rent_member_id' => Yii::t('freshMachine', '租用者id'),
            'code' => Yii::t('freshMachine', '货道编码'),
            'name'=>Yii::t('freshMachine', '货道名称'),
            'status'=>Yii::t('freshMachine', '状态'),
            'expir_time'=>Yii::t('freshMachine', '有效期'),
            'create_time' => Yii::t('freshMachine', '创建时间'),
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('machine_id',$this->machine_id);
        $criteria->compare('rent_partner_id',$this->rent_partner_id);
        $criteria->compare('rent_member_id',$this->rent_member_id);
        $criteria->compare('code',$this->code,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('status',$this->status,true);
        $criteria->compare('create_time',$this->create_time);
        $criteria->compare('expir_time',$this->expir_time);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
      public function lineSearch()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('machine_id',$this->machine_id);
        $criteria->compare('code',$this->code,true);
        $criteria->compare('name',$this->name,true);
        //$criteria->compare('status',$this->status);
        $criteria->compare('create_time',$this->create_time);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->db;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return FreshMachineLine the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    
    public function afterSave(){
    	//清空货道缓存
    	FreshMachine::clearLineInfo($this->rent_partner_id);
    	parent::afterSave();
    	return true;
    }
       
}