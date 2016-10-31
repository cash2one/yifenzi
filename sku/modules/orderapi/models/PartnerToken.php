<?php

/**
 * This is the model class for table "{{partner_token}}".
 *
 * The followings are the available columns in table '{{partner_token}}':
 * @property string $id
 * @property string $member_id
 * @property string $token
 * @property integer $create_time
 * @property integer $expir_time
 *
 * The followings are the available model relations:
 * @property GwMember $member
 */
class PartnerToken extends CActiveRecord
{
	const CACHE_PATH = 'skuPartnerTokenCache';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{partner_token}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id', 'required'),
			array('create_time, expir_time', 'numerical', 'integerOnly'=>true),
			array('member_id', 'length', 'max'=>11),
			array('token', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, token, create_time, expir_time,lang', 'safe', 'on'=>'search'),
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
		$criteria->compare('member_id',$this->member_id,true);
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
			else 
			{
				$url = GAIFUTONG_API_URL . '/sku/getUserInfo?token='.$token;
				$rs = Tool::curl_file_get_contents($url);
				$rsArray = CJSON::decode($rs);
				if($rsArray['Response']['resultCode'] == 1)
				{
					$memberData = $rsArray['Response']['resultData'];
					$memberId = $memberData['id'];
					$client_token = new ClientToken();
					$client_token->member_id = $memberId;
					$client_token->gai_number = $memberData['gaiNumber'];
					$client_token->token = $token;
					$client_token->create_time = time();
					$client_token->expir_time = 0;
					$client_token->save();
					Tool::cache(self::CACHE_PATH)->set($token, $memberId);
				}
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
		foreach ($tokens as $t){
			Tool::cache(self::CACHE_PATH)->set($t->token,null);
			Tool::cache(self::CACHE_PATH)->set($t->token.'info',null);
		}
		
		return Yii::app()->db->createCommand(' DELETE  FROM  '.  PartnerToken::model()->tableName().' WHERE member_id='.$member_id)->execute();
	}

}
