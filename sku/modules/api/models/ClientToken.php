<?php

/**
 * This is the model class for table "{{client_token}}".
 *
 * The followings are the available columns in table '{{client_token}}':
 * @property string $id
 * @property string $member_id
 * @property string $token
 * @property integer $create_time
 * @property integer $expir_time
 *
 * The followings are the available model relations:
 * @property GwMember $member
 */
class ClientToken extends CActiveRecord
{
	const CACHE_PATH = 'skuClientTokenCache';
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{client_token}}';
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
	 * 根据token获取用户id
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
				Tool::cache(self::CACHE_PATH)->set($token, $memberId,600);
			}
			else 
			{
				$url = GAIFUTONG_API_URL . '/sku/getUserInfo?token='.$token;
				$rs = Tool::curl_file_get_contents($url);
				$rsArray = CJSON::decode($rs);
				if($rsArray['Response']['resultCode'] == 1 && isset($rsArray['Response']['resultData']['id']))
				{
					$memberData = $rsArray['Response']['resultData'];
					$skumemberInfo = Member::getMemberInfoByGaiId($memberData['id']);
// 					$skumemberInfo = Member::model()->find('gai_member_id=:gai_member_id',array(':gai_member_id'=>$memberData['id']));
					
// 					if (empty($skumemberInfo)) {
// 						$aMember = new ApiMember();
// 						$member_info = $aMember->getInfo($memberData['id']);
// 						$skumemberInfo = Member::model()->find('gai_member_id=:gai_member_id',array(':gai_member_id'=>$memberData['id']));
// 					}
					
					$memberId = $skumemberInfo['id'];
					ClientToken::destoryToken($memberId);
					$client_token = new ClientToken();
					$client_token->member_id = $memberId;
					$client_token->gai_number = $memberData['gaiNumber'];
					$client_token->token = $token;
					$client_token->create_time = time();
                                        $client_token->version = '3.1.3';
					$client_token->expir_time = 0;
					$client_token->save();
					Tool::cache(self::CACHE_PATH)->set($token, $memberId,600);
				}else{
					return false;
				}

				return false;
				
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
				Tool::cache(self::CACHE_PATH)->set($cache_key, $info,600);
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
		$member_id = $member_id*1;
		$tokens = self::model()->findAll('member_id=:member_id',array(':member_id'=>$member_id));
		if (!empty($tokens)) {
			foreach ($tokens as $t){
				Tool::cache(self::CACHE_PATH)->set($t->token,null);
				Tool::cache(self::CACHE_PATH)->set($t->token.'info',null);
				$t->delete();
			}
		}
		
		return true;
	}
	
	/**
	 * 清除token
	 * @return CActiveDataProvider
	 */
	public static  function clearTokenCache($member_id)
	{
		$member_id = $member_id*1;
		$tokens = self::model()->findAll('member_id=:member_id',array(':member_id'=>$member_id));
		if (!empty($tokens)) {
			foreach ($tokens as $t){
				Tool::cache(self::CACHE_PATH)->set($t->token,null);
				Tool::cache(self::CACHE_PATH)->set($t->token.'info',null);
			}
		}
	
		return true;
	}
	
	
}
