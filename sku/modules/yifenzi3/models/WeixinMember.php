<?php

/**
 * This is the model class for table "gw_yifenzi_weixin_member".
 *
 * The followings are the available columns in table 'gw_yifenzi_weixin_member':
 * @property string $id
 * @property string $member_id
 * @property string $openid
 * @property string $login_time
 * @property string $last_login_time
 * @author qiuye.xu<qiuye.xu@g-mall.com>
 * @since 2016-04-28
 */
class WeixinMember extends CActiveRecord
{
    const MEMBER_OPENID = 'openid'; //首次或者登陆未注册的微信用户 记录到session中
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{weixin_member}}';
	}

    public function getDbConnection()
    {
        return Yii::app()->gwpart;
    }
    
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, openid, login_time, last_login_time', 'required'),
			array('member_id, openid, login_time, last_login_time', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, openid, login_time, last_login_time', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'member_id' => 'Member',
			'openid' => 'Openid',
			'login_time' => 'Login Time',
			'last_login_time' => 'Last Login Time',
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
		$criteria->compare('openid',$this->openid,true);
		$criteria->compare('login_time',$this->login_time,true);
		$criteria->compare('last_login_time',$this->last_login_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WeixinMember the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    /**
     * 对于微信端登陆或者注册的用户处理
     */
    public static function processMember($openid)
    {
        $user_id = Yii::app()->user->id;
        if(empty($openid)) return false; //非微信端登陆 退出不做操作
        if(empty($user_id)) return false; //微信自动登录必须登录一次
        $login_time = Yii::app()->gwpart->createCommand()
            ->select('login_time')
            ->from(WeixinMember::model()->tableName())
            ->where('openid=:oid', array(':oid' => $openid))
            ->queryScalar();
        if(!empty($login_time)){
            //存在该用户，更新用户登陆时间
            Yii::app()->gwpart->createCommand()->update("gw_yifenzi_weixin_member", array(
                "last_login_time" => $login_time,"login_time" =>time(),"member_id"=>$user_id
            ), "openid=:oid", array(':oid' => $openid));
        } else {
            $newWeixinMember = new self;
            $newWeixinMember->member_id = $user_id;
            $newWeixinMember->openid = $openid;
            $newWeixinMember->last_login_time = time();
            $newWeixinMember->login_time = time();
            $newWeixinMember->save(false);
        }
        return true;
    }
    /**
     * 对于微信端登陆或者注册的用户处理
     */
    public static function DeleteProcessMember()
    {
        $user_id = Yii::app()->user->id;
        if($user_id)
            Yii::app()->gwpart->createCommand()->delete(self::model()->tableName(),"member_id = $user_id");
    }
}
