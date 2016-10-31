<?php

/**
 * This is the model class for table "{{partner_token}}".
 *
 * The followings are the available columns in table '{{partner_token}}':
 * @property string $id
 * @property integer $member_id
 * @property integer $partner_id
 * @property string $token
 * @property integer $create_time
 * @property integer $expir_time
 *
 * The followings are the available model relations:
 * @property GwMember $member
 */
class XiaoerClientToken extends CActiveRecord
{
	const CACHE_PATH = 'XiaoerClientTokenCache';
	
	const TOKEN_KEY_PREFIX = 'XR';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{xiaoer_client_token}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id,partner_id', 'required'),
			array('create_time, expir_time,partner_id,member_id', 'numerical', 'integerOnly'=>true),
			array('member_id,partner_id', 'length', 'max'=>11),
			array('token', 'length', 'max'=>34),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, token, create_time, expir_time,lang,partner_id', 'safe', 'on'=>'search'),
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
// 			'member' => array(self::BELONGS_TO, 'GwMember', 'member_id'),
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
			'token' => 'Token',
			'create_time' => '创建时间',
			'expir_time' => '超时时间',
            'lang'=>'语言'
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
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('partner_id',$this->partner_id);
		$criteria->compare('token',$this->token,true);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('expir_time',$this->expir_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ClientToken the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * 根据token获取用户模型对象
	 * @return CActiveDataProvider
	 */
	public static  function getMemberByToken($token)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		//查询缓存
		$memberId = Tool::cache(self::CACHE_PATH)->get($token);
		if (!$memberId) {
			$criteria=new CDbCriteria;
			$criteria->compare('token',$token);
			$rs = self::model()->find($criteria);
			if ($rs) {
				$memberId = $rs->member_id;
				Tool::cache(self::CACHE_PATH)->set($token, $memberId);
			}
		}
		
		return $memberId;
	}
	
	/**
	 * 根据token获取token信息
	 * @return CActiveDataProvider
	 */
	public static  function getInfoByToken($token)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		//查询缓存
		$cache_key = $token.'info';
		$info = Tool::cache(self::CACHE_PATH)->get($cache_key);
		if (!$info) {
			$criteria=new CDbCriteria;
			$criteria->compare('token',$token);
			$info = self::model()->find($criteria);
			if ($info) {
				$info = $info->attributes;
				Tool::cache(self::CACHE_PATH)->set($cache_key, $info);
			}
		}
	
		return $info;
	}
	
	/**
	 * 清除token 
	 * @return CActiveDataProvider
	 */
	public static  function destoryToken($member_id)
	{
		$tokens = self::model()->findAll('member_id=:member_id',array(':member_id'=>$member_id));
		if (!empty($tokens)) {
			foreach ($tokens as $t){
				$t->delete();
				Tool::cache(self::CACHE_PATH)->set($t->token,null);
				Tool::cache(self::CACHE_PATH)->set($t->token.'info',null);
			}
		}

		return true;
	}
	
	public static  function createTokenCode($member_id){
		return self::TOKEN_KEY_PREFIX.md5($member_id.time());
	}

    /**
     * @param array $where 条件语句
     * @param string $fields 选择字段
     * @return mixed
     */
    public static function getxiaoerClientInfo($where,$fields = "*"){
        $whereTmp = $whereVal = array();
        $whereStr = '';
        foreach($where as $key =>$value){
            $whereTmp[] = $key.'=:'.$key;
            $whereVal[':'.$key] = $value;
        }
        if(count($whereTmp) > 1) {
            $whereStr = implode(' and ',$whereTmp);
        }else{
            $whereStr = $whereTmp[0];
        }
        $result = Yii::app()->db->createCommand()->select($fields)->from("{{xiaoer_client_token}}")->where($whereStr,$whereVal)->queryAll();
        return $result;
    }
}
