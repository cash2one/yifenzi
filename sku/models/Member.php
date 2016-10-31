<?php

/**
 * This is the model class for table "{{member}}".
 *
 * The followings are the available columns in table '{{member}}':
 * @property string $id
 * @property string $logins
 * @property string $signins
 * @property string $sku_number
 * @property string $referrals_id
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property integer $sex
 * @property string $real_name
 * @property string $password2
 * @property string $password3
 * @property string $birthday
 * @property string $email
 * @property string $mobile
 * @property string $country_id
 * @property string $province_id
 * @property string $city_id
 * @property string $district_id
 * @property string $street
 * @property string $register_time
 * @property integer $register_type
 * @property string $head_portrait
 * @property integer $status
 * @property string $last_login_time
 * @property string $current_login_time
 * @property string $nickname
 * @property string $referrals_time
 * @property string $pay_limit_daily
 * @property string $ratio
 */
class Member extends CActiveRecord
{
    const STATUS_NO_ACTIVE = 0;
    const STATUS_NORMAL = 1;
    const STATUS_DELETE = 2;
    const STATUS_REMOVE = 3;
    const REGISTER_TYPE_DEFAULT = 0;
    const REGISTER_TYPE_GW_SYNC = 1;
    const REGISTER_TYPE_GAME = 2; //游戏平台注册
    public $confirmpassword; //确认密码
	public $confirmpassword3; //确认密码
    public $verifyCode; //验证码

    /**
     * 会员状态
     * @param $key
     * @return array|null
     */
    public static function status($key = null) {
        $arr = array(
            self::STATUS_NO_ACTIVE => Yii::t('member', '待激活'),
            self::STATUS_NORMAL => Yii::t('member', '正常'),
            self::STATUS_DELETE => Yii::t('member', '删除'),
            self::STATUS_REMOVE => Yii::t('member', '除名'),
        );
        if (is_numeric($key)) {
            return isset($arr[$key]) ? $arr[$key] : null;
        } else {
            return $arr;
        }
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{member}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
//			array('sku_number,  mobile, register_time, register_type, status', 'required'),
			array('sku_number,gai_number, mobile, register_time, register_type, status', 'required' ,'on'=>'sync'),
			array('sex, register_type, status,gai_member_id,verifyCode', 'numerical', 'integerOnly'=>true,'message'=>Yii::t('yii','{attribute}必须为整数')),
			array('logins, signins, referrals_id, birthday, country_id, province_id, city_id, district_id, register_time, last_login_time, current_login_time, referrals_time', 'length', 'max'=>11),
			array('sku_number', 'length', 'max'=>32),
			array('username, password, salt, real_name, password2, password3, email, mobile, street, head_portrait, nickname', 'length', 'max'=>128),
			array('pay_limit_daily', 'length', 'max'=>10),
			array('ratio', 'length', 'max'=>5),
            array('confirmpassword','compare','compareAttribute'=>'password','on'=>'yifenRegister,changepw','message'=>'密码不一致'), //验证密码
            array('mobile','unique','on'=>'yifenRegister','message'=>'此号码已注册'),
            array('verifyCode','checkVerifyCode','on'=>'yifenRegister,findpw,yifenpaypwss'),
            array('verifyCode,password,confirmpassword,mobile','required','on'=>'yifenRegister','message'=>Yii::t('yii','不可为空')),
			array('verifyCode,mobile','required','on'=>'yifenpaypwss','message'=>Yii::t('yii','不可为空')),
            array('mobile','comext.validators.isMobile','on'=>'yifenRegister,findpw,yifenVerify','errMsg'=>'手机号码不合法'),//检测手机号码的合法性
            array('verifyCode,mobile','required','on'=>'findpw','message'=>Yii::t('yii','不可为空')),
            array('password,confirmpassword','required','on'=>'changepw'),
            array('mobile','checkMemberExist','on'=>'findpw,changpw,yifenpaypwss'), // 验证用户是否存在
            array('password,confirmpassword','length','max'=>20,'on'=>'changepw,yifenRegister','tooLong'=>Yii::t('yii','{attribute}太长')),
            array('password,confirmpassword','length','min'=>6,'on'=>'changepw,yifenRegister','tooShort'=>Yii::t('yii','{attribute}太短')),

			array('password,password3,confirmpassword3','required','on'=>'paypwss,yifenzipaypwss1','message'=>Yii::t('yii','不可为空')),
			array('confirmpassword3','compare','compareAttribute'=>'password3','on'=>'paypwss,yifenzipaypwss1','message'=>'密码不一致'), //验证密码
			array('password3,confirmpassword3','length','max'=>6,'on'=>'paypwss','tooLong'=>Yii::t('yii','{attribute}太长')),
			array('password','length','max'=>20,'min'=>6,'on'=>'paypwss'),
            array('password3,confirmpassword3','length','max'=>6,'on'=>'yifenzipaypwss1','tooLong'=>Yii::t('yii','{attribute}太长')),
            array('password3,confirmpassword3','length','min'=>6,'on'=>'yifenzipaypwss1','tooShort'=>Yii::t('yii','{attribute}太短')),
            array('password3,confirmpassword3', 'numerical', 'integerOnly'=>true,'on'=>'yifenzipaypwss1','message'=>Yii::t('yii','{attribute}必须为整数')),


//                        array('verityCode','required','on'=>''),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, logins, signins, sku_number, referrals_id, username, password, salt, sex, real_name, password2, password3, birthday, email, mobile, country_id, province_id, city_id, district_id, street, register_time, register_type, head_portrait, status, last_login_time, current_login_time, nickname, gai_member_id,referrals_time, pay_limit_daily, ratio', 'safe', 'on'=>'search'),
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
			'id' => '主键id',
			'logins' => '登录次数',
			'signins' => '签到次数',
			'sku_number' => 'SKU编号',
			'referrals_id' => '推荐人',
			'username' => '用户名',
			'password' => '密码',
			'salt' => '唯一密钥',
			'sex' => '性别',
			'real_name' => '真实姓名',
			'password2' => '二级密码',
			'password3' => '支付密码',
			'birthday' => '生日',
			'email' => '邮箱',
			'mobile' => '手机号码',
			'country_id' => '国家',
			'province_id' => '省份',
			'city_id' => '城市',
			'district_id' => '区/县',
			'street' => '详细地址',
			'register_time' => '注册时间',
			'register_type' => '注册类型（0默认，1盖网同步）',
			'head_portrait' => '头像',
			'status' => '状态（0待激活，1正常，2删除，3除名）',
			'last_login_time' => '上次登录时间',
			'current_login_time' => '当前登录时间',
			'nickname' => '昵称',
			'referrals_time' => '更新推荐人时间',
			'pay_limit_daily' => '每日积分支付限额',
			'ratio' => 'Ratio',
                        'verifyCode'=>'验证码',
                        'confirmpassword'=>'密码',
						'confirmpassword3'=>'支付密码',
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
		$criteria->compare('logins',$this->logins,true);
		$criteria->compare('signins',$this->signins,true);
		$criteria->compare('sku_number',$this->sku_number,true);
		$criteria->compare('referrals_id',$this->referrals_id,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('salt',$this->salt,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('real_name',$this->real_name,true);
		$criteria->compare('password2',$this->password2,true);
		$criteria->compare('password3',$this->password3,true);
		$criteria->compare('birthday',$this->birthday,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('country_id',$this->country_id,true);
		$criteria->compare('province_id',$this->province_id,true);
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('district_id',$this->district_id,true);
		$criteria->compare('street',$this->street,true);
		$criteria->compare('register_time',$this->register_time,true);
		$criteria->compare('register_type',$this->register_type);
		$criteria->compare('head_portrait',$this->head_portrait,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('last_login_time',$this->last_login_time,true);
		$criteria->compare('current_login_time',$this->current_login_time,true);
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('referrals_time',$this->referrals_time,true);
		$criteria->compare('pay_limit_daily',$this->pay_limit_daily,true);
		$criteria->compare('ratio',$this->ratio,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Member the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * 生成唯一的会员编号 GW+8位数字
     * @return string
     */
    public function generateNumber() {
        $profix = 'SK';
        $number = str_pad(mt_rand('1', '99999999'), 8, mt_rand(99999, 999999));
        if ($this->exists('sku_number="' . $profix . $number . '"')) {
            return $this->generateNumber();
        }
        return $profix . $number;
    }

    /**
     * 检测输入的密码是否正确
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password) {
        return CPasswordHelper::verifyPassword($password . $this->salt, $this->password);
    }
    /**
     * 检测输入的二级密码是否正确
     * @param string $password
     * @return boolean
     */
    public function validatePassword2($password) {
        return CPasswordHelper::verifyPassword($password . $this->salt, $this->password2);
    }

    /**
     * 检测输入的三级密码是否正确
     * @param string $password
     * @return boolean
     */
    public function validatePassword3($password) {
        return CPasswordHelper::verifyPassword($password . $this->salt, $this->password3);
    }

    /**
     * 生成的密码哈希.
     * @param string $password
     * @return string $hash
     */
    public function hashPassword($password) {
        return CPasswordHelper::hashPassword($password . $this->salt);
    }

    /**
     * 通过gw号获取会员信息
     * @param $gaiNumber
	 * @param string $select
     * @return bool|CActiveRecord
     */
    public static function getByGwNumber($gaiNumber,$select="*"){
        $member = Member::model()->find(array(
            'select' => $select,
            'condition'=>"gai_number=:gai_number",
            'params'=>array(":gai_number"=>$gaiNumber)
        ));
        return !empty($member) ? $member : false;
    }

    /**
     * 检查每日消费限额
     * @param $memberId
     * @param $consumeMoney
     * @param null $member
     * @throws Exception
     * @return bool
     */
    public static function checkAccountLimit($consumeMoney,$machine_id,$member=null){
//         return true;
//        if(empty($member)){
//            $member = Member::model()->find(array(
//                'select' => 'pay_limit_daily',
//                'condition'=>"id=:id",
//                'params'=>array(":id"=>$memberId)
//            ));
//        }
        $today_time = strtotime(date('Y-m-d'));
        if(isset($machine_id['store_id'])){
            $sql = 'member_id='.$member.' AND pay_status='.Order::PAY_STATUS_YES.' AND status!='.Order::STATUS_CANCEL.' AND store_id='.$machine_id['store_id'].' AND pay_time>='.$today_time.' AND pay_time<='.($today_time+3600*24);
        }else{
            $sql = 'member_id='.$member.' AND pay_status='.Order::PAY_STATUS_YES.' AND status!='.Order::STATUS_CANCEL.' AND machine_id='.$machine_id['machine_id'].' AND pay_time>='.$today_time.' AND pay_time<='.($today_time+3600*24);
        }
         $memberTotalPayPreStoreLimit = Tool::getConfig('amountlimit','memberTotalPayPreStoreLimit'); // 查询限额
         //限额为0则不限制消费限额
         if(empty($memberTotalPayPreStoreLimit)){
            return true; 
         }
        $sum_rs = Yii::app()->db->createCommand()
        ->select('sum(total_price) as p')
        ->from(Order::model()->tableName())
        ->where($sql)
        ->queryRow();
        $pay_rs = $sum_rs['p'];
          $pay_rs = bcmul($pay_rs,100,0);
          $memberTotalPayPreStoreLimit = bcmul($memberTotalPayPreStoreLimit,100,0);
          $rs = ($memberTotalPayPreStoreLimit-$pay_rs)/100;
         $error =array();
        if(empty($member)) {$code = '用户不存在'; $error['code']=$code; return $error;}
        if($rs == 0) {$code = '抱歉,当天消费额度已用完'; $error['code']=$code; return $error;}
  
        
        if($rs < $consumeMoney && $rs >0) {$code = '抱歉,当天消费额度不足已支付此笔消费,当前剩余额度为:'.$rs.'元'; $error['code']=$code; return $error;}

        return true;
    }

    /**
     * 同步盖网会员账号
     * @param $memberInfo
     * @return bool
     */
    public static function syncFromGw($memberInfo) {
        if (isset($memberInfo[0])) {
             $trans = Yii::app()->db->beginTransaction();
            try{
            foreach ($memberInfo as $v) {
                $skuMemberId = Yii::app()->db->createCommand()
                        ->select('id,mobile')
                        ->from(Member::model()->tableName())->where('gai_number=:gai_number', array(':gai_number' => $v['gai_number']))
                        ->queryRow();
                if (!$skuMemberId) {
                    $memberInfo['gai_member_id'] = $v['id'];
                    unset($memberInfo['id']);
                    $newMember = new Member('sync');
                    $newMember->attributes = $v;
                    $newMember->mobile = $v['mobile'];
                    $newMember->sku_number = 'SK00' . substr($v['gai_number'], 2);
                    $newMember->register_type = Member::REGISTER_TYPE_GW_SYNC;
                    $newMember->register_time = time();
                    $newMember->save();
                }else{
                    if(empty($skuMemberId['mobile'])){
                        Yii::app()->db->createCommand()->update(Member::model()->tableName(), array('mobile'=>$memberInfo['mobile']), 'id=' . $skuMemberId['id']);
                    }
                }
            }
            $trans->commit();
             } catch (Exception $e) {
            $trans->rollback();           
            Yii::log('sku账号同步失败' . var_export($e->getMessage(), true));
             return false;
        }
        } else {
            $skuMemberId = Yii::app()->db->createCommand()
                    ->select('id,mobile')
                    ->from(Member::model()->tableName())->where('gai_number=:gai_number', array(':gai_number' => $memberInfo['gai_number']))
                    ->queryRow();
            if (!$skuMemberId) {
                $memberInfo['gai_member_id'] = $memberInfo['id'];
                unset($memberInfo['id']);
                $newMember = new Member('sync');
                $newMember->attributes = $memberInfo;
                $newMember->mobile = $memberInfo['mobile'];
                $newMember->sku_number = 'SK00' . substr($memberInfo['gai_number'], 2);
                $newMember->register_type = Member::REGISTER_TYPE_GW_SYNC;
                $newMember->register_time = time();
                if ($newMember->save()) {
                    return true;
                }
                Yii::log('sku账号同步失败' . var_export($newMember->getErrors(), true));
                return false;
            } else {
                if(empty($skuMemberId['mobile'])){
                    Yii::app()->db->createCommand()->update(Member::model()->tableName(), array('mobile'=>$memberInfo['mobile']), 'id=' . $skuMemberId['id']);
                }
                return true;
            }
        }
    }


    public static function updateFromGw($memberInfo){
    	$skuMember = Member::model()->find('gai_number=:gai_number',array(':gai_number'=>$memberInfo['gai_number']));
    	if(!empty($skuMember)){
    		unset($memberInfo['id']);

    		$skuMember->attributes = $memberInfo;

    		if($skuMember->save()){
    			return true;
    		}
    		Yii::log('sku账号同步失败'.var_export($skuMember->getErrors(),true));
    		return false;
    	}else{
    		return false;
    	}
    }

	/**
	 * 生成唯一的会员编号 GW+8位数字
	 * @return string
	 */
	public function generateGaiNumber() {
		define('GAI_NUMBER_LENGTH', 8);
		$number = str_pad(mt_rand('1', '99999999'), GAI_NUMBER_LENGTH, mt_rand(99999, 999999));
		$exists = Yii::app()->gw->createCommand()->select('id')->from('{{member}}')
			->where('gai_number = :gai_number',array(':gai_number' => "GW" . $number))->queryRow();
		if ($exists) {
			return $this->generateGaiNumber();
		}
		return 'GW' . $number;
	}

	/**
	 * 获取会员信息
	 */
	static function getMemberInfoByGaiNumber($gai_number){
		$skumemberInfo = Member::model()->find('gai_number=:gai_number',array(':gai_number'=>$gai_number));

		if (empty($skumemberInfo)) {
			$aMember = new ApiMember();
			$member_info = $aMember->getInfo($gai_number);
			$skumemberInfo = Member::model()->find('gai_number=:gai_number',array(':gai_number'=>$gai_number));
		}

		return $skumemberInfo;
	}

	/**
	 * 获取会员信息
	 */
	static function getMemberInfoByGaiId($gai_id){
			$skumemberInfo = Member::model()->find('gai_member_id=:gai_member_id',array(':gai_member_id'=>$gai_id));
			if (empty($skumemberInfo)) {
				$aMember = new ApiMember();
				$member_info = $aMember->getInfo($gai_id);
				$skumemberInfo = Member::model()->find('gai_member_id=:gai_member_id',array(':gai_member_id'=>$gai_id));
			}
			return $skumemberInfo;
	}

    /**
     * 同步盖象支付密码到sku
     */
    static function syncPassword($gaiNumber){
        $rs = Yii::app()->gw->createCommand()
            ->select('password,password2,password3,salt')
            ->from("{{member}}")
            ->where('gai_number = :gai_number', array(':gai_number' => $gaiNumber))
            ->queryRow();

        if($rs){
            Yii::app()->db->createCommand()->update('{{member}}',
                array(
                    'password'=>$rs['password'],
                    'password2'=>$rs['password2'],
                    'password3'=>$rs['password3'],
                    'salt'=>$rs['salt']
                ),
                'gai_number = :name',
                array(':name'=>$gaiNumber)
            );
        }
        return true;
    }


    /**
     * 手机验证码验证
     * @param type $attribute
     * @param type $param
     */
    public function checkVerifyCode($attribute,$param)
    {
        $state = Yii::app()->user->getState($this->mobile);
        $verifyCode = Tool::authcode($state, 'DECODE','');
        $verifyCode = unserialize($verifyCode);
        if($verifyCode['code'] != $this->verifyCode){
            $times =  Yii::app()->user->getState('verifyCode_times'); //验证次数 记录错误次数
            if($times > 5) {
                Yii::app()->user->setState($this->mobile,''); //验证不正确，销毁验证码，要求重新获取
               // Yii::app()->user->setState('verifyCode_times', 0);
                $this->addError($attribute, '请重新获取验证码');
            } else {
                Yii::app()->user->setState('verifyCode_times', ++$times);
                //Yii::app()->session->remove($this->mobile); //验证不正确，销毁验证码，要求重新获取
                $this->addError($attribute, '验证码不正确');
            }
        }
    }
    /**
     * 用户验证，验证用户是否存在
     * @param type $attribute
     * @param type $param
     */
    public function checkMemberExist($attribute,$param)
    {
        $this->autoMobile($this->mobile);
        $count = $this->count('mobile=:mobile',array('mobile'=>$this->mobile));
        if(!$count){
            $this->addError($attribute, '用户不存在');
        }
    }
    /**
     * 同步用户到sku
     */
//    public function syncMember($gai_member_id)
//    {
//         //同步数据
//        $connection = Yii::app()->gw; //得到链接
//        $sql = "SELECT m.id,m.referrals_id,m.username,m.password,m.salt,m.sex,m.real_name,m.password2,m.password3,m.gai_number,
//                        m.birthday,m.mobile,m.country_id,m.province_id,m.district_id,m.street,m.register_time,m.register_type,mt.ratio,
//                        m.head_portrait,m.status,m.nickname,m.referrals_time FROM {{member}} as m left join {{member_type}} as mt on m.type_id = mt.id WHERE m.id={$gai_member_id}";
//        $user = $connection->createCommand($sql)->queryRow();
//        if ($user) {
//            $user['sku_number'] = Member::model()->generateNumber();
//            $user['gai_member_id'] = $user['id'];
//            unset($user['id']); //清除id
//            Yii::app()->db->createCommand()->insert('{{member}}', $user); //加入
//            $sql = "select id from {{member}} where gai_member_id={$user['gai_member_id']}";
//            $result = Yii::app()->db->createCommand($sql)->queryRow();
//            if($result){
//                Yii::log($user['username'] . '同步成功！ID为' . $result['id']);
//                Yii::app()->user->login($result['id'], $user); //cookie有问题的
//                YfzCart::syncData(); //登陆成功 同步购物车
//            }
//        } else {
//            Yii::log($user['username'] . '同步失败！');
//            return false;
//        }
//        return true;
//    }
    /**
     * 根据用户id获取 用户信息
     * @param type $id  用户id
     */
    public static function getMemberInfo($id)
    {
        if(is_numeric($id)){
            $sql = "SELECT m.id,m.username,m.gai_number,m.mobile,p.name as province_name,m.head_portrait,r.name as city_name from {{member}} as m
                left join {{region}} as r on r.id=m.city_id
                left join {{region}} as p on p.id=m.province_id
                where m.id={$id}";
            $member = Yii::app()->db->createCommand($sql)->queryRow();
            if(!$member) return false;
            return $member;
        }
        return false;
    }
	
	/**
     * 根据用户id获取 最新用户登录IP
     * @param type $id  用户id
     */
    public static function getMemberIp($id)
    {
        if(is_numeric($id)){
			$connection = Yii::app()->gw;
            $sql = "SELECT ip from {{member_login_log}} 
                where member_id={$id} AND login_time =(SELECT max(login_time) from {{member_login_log}} where member_id={$id})";
            $memberIp = $connection->createCommand($sql)->queryRow();
            if(!$memberIp) return false;
            return $memberIp;
        }
        return false;
    }
	
	public static function getMemberAddressNew($id)
    {
       if(is_numeric($id)){
			$connection = Yii::app()->db;
            $sql = "SELECT member_id,province_id,real_name,city_id,district_id,street from {{address}}
                where member_id={$id} AND id =(SELECT max(id) from {{address}} where member_id={$id})";
            $member = $connection->createCommand($sql)->queryRow();
            if(!$member) return false;
            return $member;
        }
        return false;
    }

	public static function getMemberInfos($id)
    {
       return self::model()->findByPk($id)->username;
    }
	
    /**
     * 后台使用，根据ID获取用户手机号码
     * @param type $id
     */
    public static function getMemberById($id,$select = array())
    {
        if(empty($select)){
        return self::model()->findByPk($id)->mobile;
        }else{
            return self::model()->find(array('select' =>$select,'condition' => 'id='.$id));
        }
    }
    /**
     * 重置用户密码，盖象API
     */
    public function resetPassword()
    {
        $apiMember = new ApiMember;
        $data = array('captcha'=>$this->verifyCode,'mobile'=>$this->mobile,'newPassword'=>$this->password);
        return $apiMember->resetPassword($data);
    }

	/**
	 * 一份子获取用户购买记录
	 * @param unknown $memberId 会员id
	 * @return multitype:
	 */
	public function getMemberBuyRecord($memberId, $page, $pageSize = 10)
	{
		$offer = ($page-1)*$pageSize;
// 		$sql = "select gn.member_id,o.order_id,g.goods_id,g.current_nper,g.goods_name,g.goods_price,g.goods_number,gn.sumlotterytime,gn.status,gi.goods_thumb
// 				from {{order}} o
// 				LEFT JOIN  {{order_goods}} g on g.order_id = o.order_id
// 				LEFT JOIN {{goods_image}} gi ON gi.goods_id=g.goods_id
// 				LEFT JOIN {{order_goods_nper}} gn ON g.order_id=gn.order_id AND g.goods_id=gn.goods_id AND g.current_nper=gn.current_nper
// 				where o.member_id = :id and o.order_status = :status limit $offer,$pageSize";
		
		
		$sql = "SELECT
                	o.order_id,o.member_id,ogn.member_id as ogn_member_id,og.goods_id,og.current_nper,og.goods_name,og.goods_price,og.goods_number,og.goods_image as goods_thumb,ogn.status,ogn.sumlotterytime
                FROM
                	{{order}} AS o
                LEFT JOIN {{order_goods}} AS og ON o.order_id = og.order_id
                LEFT JOIN {{order_goods_nper}} AS ogn ON og.order_id = ogn.order_id and og.current_nper=ogn.current_nper and og.goods_id=ogn.goods_id
                
                WHERE
                	o.member_id = :id
                AND o.order_status = :status 
                ORDER BY og.addtime DESC
		        limit $offer,$pageSize";
		
		$command = Yii::app()->gwpart->createCommand($sql);
		$command->bindValue(":id",$memberId);
		$status = YfzOrder::STATUS_PAY_SUCCESS;
		$command->bindValue(':status',$status);
		$recordAll = $command->queryAll();
		
// 		print_r($recordAll);exit;
		foreach($recordAll as $k=>$r){
			$recordAll[$k]['goods_thumb'] = ATTR_DOMAIN . '/' . $recordAll[$k]['goods_thumb'];
			$recordAll[$k]['sumlotterytime'] = $recordAll[$k]['sumlotterytime']>0?date('Y-m-d H:i:s', $recordAll[$k]['sumlotterytime']):'';
			
			switch ($r['status']){
			    case YfzOrderGoodsNper::STATUS_ING:
			        $recordAll[$k]['status_re'] = '开奖中';
			        break;
			    case YfzOrderGoodsNper::STATUS_FALSE:
			        $recordAll[$k]['status_re'] = '已揭晓';
			        $member = Member::getMemberInfo($r['ogn_member_id']);
			        $recordAll[$k]['username'] = $member['username'];
                    $recordAll[$k]['gai_number'] = !empty($member['gai_number'])?substr_replace($member['gai_number'],'****',4,4):'';
                    $recordAll[$k]['mobile'] = !empty($member['mobile'])?substr_replace($member['mobile'],'****',3,4):'';
			        break;
			    default:
			        //如果用户购买某期商品，但是购买成功之后人数没有满期。那么在展示时应该拿这订单中的商品ID和商品期数在中奖中查询看是否有记录
			        //如果有记录那么要进行核对
			        $sql = "select * from gw_yifenzi_order_goods_nper where goods_id={$r['goods_id']} and current_nper = {$r['current_nper']}";
			        $nperData = Yii::app()->gwpart->createCommand($sql)->queryRow();
			        
			        if ($nperData){
			            $sumlotterytime = $nperData['sumlotterytime']>0?date('Y-m-d H:i:s', $nperData['sumlotterytime']):'';
			            if ($nperData['status'] == YfzOrderGoodsNper::STATUS_ING){
			                $recordAll[$k]['status_re'] = '开奖中';
			                $recordAll[$k]['status'] = YfzOrderGoodsNper::STATUS_ING;
			                $recordAll[$k]['sumlotterytime'] = $sumlotterytime;
			            }elseif($nperData['status'] == YfzOrderGoodsNper::STATUS_FALSE){
			                $recordAll[$k]['status_re'] = '已揭晓';
			                $recordAll[$k]['status'] = YfzOrderGoodsNper::STATUS_FALSE;
			                $member = Member::getMemberInfo($nperData['member_id']);
			                $recordAll[$k]['username'] = $member['username'];
                            $recordAll[$k]['gai_number'] = !empty($member['gai_number'])?substr_replace($member['gai_number'],'****',4,4):'';
                            $recordAll[$k]['mobile'] = !empty($member['mobile'])?substr_replace($member['mobile'],'****',3,4):'';
			                $recordAll[$k]['sumlotterytime'] = $sumlotterytime;
			            }
			        }else{
			            $sql = "select * from gw_yifenzi_yfzgoods where goods_id=".$r['goods_id']." and current_nper=".$r['current_nper'];
			            $goodsData = Yii::app()->gwpart->createCommand($sql)->queryRow();
			            if ($goodsData){
			                $recordAll[$k]['status_re'] = '进行中';
			                $recordAll[$k]['status'] = 0;
			                $recordAll[$k]['inventory'] = $goodsData['goods_number']; //库存
			                $recordAll[$k]['count_nper'] = $goodsData['shop_price'] / $goodsData['single_price'];
			                //$recordAll[$k]['newinventory'] = $recordAll[$k]['count_nper'] - $goodsData['goods_number'];
			                $recordAll[$k]['percentage'] = (($recordAll[$k]['count_nper'] - $goodsData['goods_number'])/$recordAll[$k]['count_nper']) * 100;
			            }
			        }
			        
			        break;
			}
// 			if($r['status'] == YfzOrderGoodsNper::STATUS_FALSE)
// 			{
// 				$member = Member::getMemberInfo($r['member_id']);
// 				$recordAll[$k]['username'] = $member['username'];
// 			}
// 			else
// 			{
// 				$recordAll[$k]['username'] = '';
// 			}
// 			if($r['status'] == YfzOrderGoodsNper::STATUS_TURE) {
// 				$recordAll[$k]['status'] = '未揭晓';
// 			}
// 			elseif ($r['status'] == YfzOrderGoodsNper::STATUS_ING)
// 			{
// 				$recordAll[$k]['status'] = '开奖中';
// 			}
// 			else{
// 				$recordAll[$k]['status'] = '已揭晓';
// 			}
		}
// 		print_r(date('Y-m-d H:i:s',1462254162));
// 		print_r($recordAll);exit;
		return $recordAll;

	}
	/**
	 * 一份子获取用户个人获得奖品
	 */
	public function getMemberGetProduct()
	{
		$id =  Yii::app()->user->id;
		$page = Yii::app()->request->getParam('page',1);
		$pageSize = CPagination::DEFAULT_PAGE_SIZE;
		$offer = ($page-1)*$pageSize;
		$sql = "select member_id,order_id,goods_name,winning_code,sumlotterytime,current_nper,goods_id
                from gw_yifenzi_order_goods_nper
				where member_id = :id limit $offer,$pageSize";
		$command = Yii::app()->gwpart->createCommand($sql);
		$command->bindValue(":id",$id);
		$record = $command->queryAll();

		$recordAll = array();
		foreach($record as $k=>$v ){
			$recordAll[$k]['goods_id'] = $v['goods_id'];
			$recordAll[$k]['goods_name'] = $v['goods_name'];
			$recordAll[$k]['current_nper'] = $v['current_nper'];
			$recordAll[$k]['winning_code'] = $v['winning_code'];
			$recordAll[$k]['sumlotterytime'] = date("Y-m-d H:i:s",$v['sumlotterytime']);
			$goods = YfzGoods::model()->findByPk($v['goods_id']);
			$recordAll[$k]['shop_price'] = $goods->shop_price;
//			$goods_image = Goods_image::model()->findByPk($v['goods_id']);
			$goods_image = Goods_image::model()->find(array("condition"=>"goods_id={$v['goods_id']}"));
			$recordAll[$k]['goods_thumb'] = ATTR_DOMAIN . '/' . $goods_image->goods_thumb;

		}
		return $recordAll;

	}

    /**
     * 盖象APP 登陆
     * @param type $accessToken
     * @return boolean
     */
    public function appLogin($accessToken)
    {
        $token = array('accessToken'=>$accessToken); //组装数据
		$aMember = new ApiMember();
        $memberInfo = $aMember->appLogin($token); //返回接口数据  数组类型
        if($memberInfo['status'] == 200 && $memberInfo['msg'] == 'success'){
            $member = $memberInfo['data'];
//print_r($memberInfo);exit;
			//验证是否有此用户
			$sql = "select * from {{member}} where gai_member_id={$member['memberId']}  and gai_number='{$member['gaiNumber']}'";
			$skumember = Yii::app()->db->createCommand($sql)->queryRow();
			if ($skumember){
				$tmpData['id'] = $skumember['id'];
				$tmpData['real_name'] = $skumember['real_name'];
				$tmpData['sku_number'] = $skumember['sku_number'];
				$tmpData['gai_number'] = $skumember['gai_number'];
				$tmpData['username'] = $skumember['username'];
				Yii::app()->user->login($skumember['id'],$tmpData);
				YfzCart::syncData(); //同步 购物车
				return true;
			}

        }
        return false;
    }
    
    /**
     * 一份子 注册后自动登录
     * @param type $member  apiMember返回的数组
     */
    public function autoLogin()
    {
        $apiMember = new ApiMember;
        $data = array_merge($this->attributes, array('captcha' => $this->verifyCode));
        $member = $apiMember->register($data);
        if($member['success']){
            $memberId = $member['memberId']; //盖象用户表id
            $memberInfo = $this->find(array(
                        'select' => 'id,username,mobile,gai_number,sku_number',
                        'condition' => 'gai_member_id=:mid',
                        'params' => array(':mid'=>$memberId)
                    ));
            if($memberInfo){
                /*
                 * 设置用户ip
                 *
                 * */
                YfzMemberIp::setMemberIp($memberInfo->id);
                //代码有点重复了 
                Yii::app()->user->login($memberInfo->id,$memberInfo->attributes);
                $openid = Yii::app()->user->getState(WeixinMember::MEMBER_OPENID);
				Member::syncFromGw($memberInfo);
                WeixinMember::processMember($openid); //同步微信用户登陆
                YfzCart::syncData(); //同步 购物车
                return true;
            }
        }
        return false; //数据同步失败 用户手动登录
    }


    /**
     * 根据手机号进行数据同步
     */
    public function autoMobile($mobile){
        $apiMember = new ApiMember;

        $member = $apiMember->getInfo($mobile);

        if(is_array($member)){
            $memberInfo = array(
                'gai_member_id'    =>  $member['id'],
                'gai_number'    =>  $member['gai_number'],
                'mobile'    => $member['mobile']
            );

            if ($memberInfo){
                Member::syncFromGw($memberInfo);
                return true;
            }
        }else{
            return false;
        }
    }
}
