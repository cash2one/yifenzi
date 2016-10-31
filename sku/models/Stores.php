<?php

/**
 * This is the model class for table "{{stores}}".
 *
 * The followings are the available columns in table '{{stores}}':
 * @property integer $id
 * @property integer $stype
 * @property integer $target_id
 * @property integer $create_time
 */
class Stores extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{stores}}';
    }
    const SUPERMARKETS = 1; //超市门店
    const MACHINE = 2;      //售货机
    const FRESH_MACHINE = 3;      //生鲜机
    //格仔铺
    const FRESH_MACHINE_SMALL = 5 ; //俊鹏生鲜机
    
    //状态
    const STATUS_APPLY = 0;   //申请
    const STATUS_ENABLE = 1;  //启用
    const STATUS_DISABLE = 2; //禁用
    
    public static function getStatus($key = null) {
    	$data = array(
    			self::STATUS_APPLY => Yii::t('stores', '申请'),
    			self::STATUS_ENABLE => Yii::t('stores', '启用'),
    			self::STATUS_DISABLE => Yii::t('stores', '禁用'),
    	);
    	return $key === null ? $data : $data[$key];
    }
    
    /**
     * 获取类型
     * @param type $key
     * @return type
     */
    public static function getTpye($key = null) {
        $data = array(
            self::SUPERMARKETS => Yii::t('stores', '超市门店'),
            self::MACHINE => Yii::t('stores', '售货机'),        
            self::FRESH_MACHINE => Yii::t('stores', '生鲜机'),
            self::FRESH_MACHINE_SMALL => Yii::t('stores', '俊鹏生鲜机'),
        );
        return $key === null ? $data : $data[$key];
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('stype, target_id, create_time', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, stype, target_id, create_time', 'safe', 'on'=>'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('stores', 'ID'),
            'stype' =>Yii::t('stores', '类型  售货机、门店等'),
            'target_id' => Yii::t('stores', '目标店铺id'),
            'create_time' => Yii::t('stores', '创建时间'),
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

        $criteria->compare('id',$this->id);
        $criteria->compare('stype',$this->stype);
        $criteria->compare('target_id',$this->target_id);
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
     * @return Stores the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
