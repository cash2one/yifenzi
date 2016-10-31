<?php

/**
 * 商家登录模型
 * @author wanyun.liu <wanyun.liu@163.com>
 */
class LoginForm extends CFormModel
{

    public $username;
    public $password;
    public $verifyCode;
    private $_identity;
    public $loginTimes;//缓存登陆错误次数

    public function rules()
    {
        return array(
            array('username, password,verifyCode', 'required','message'=>'不能为空'),
            array('password', 'authenticate'),
//            array('verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements()),
            array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
            array('username, password', 'safe'),
//            array('username', 'match', 'pattern' => '/^GW\d+$/', 'message' => Yii::t('YifenziModule.member', '请输入正确的盖网号')),
            array('username', 'match', 'pattern' => '/[(^GW\d+)|(^1\d{10})]$/', 'message' => Yii::t('YifenziModule.member', '请输入正确的盖网号或手机号')),
        );
    }

    public function attributeLabels()
    {
        return array(
            'username' => Yii::t('YifenziModule.member', '用户名'),
            'password' => Yii::t('YifenziModule.member', '密码'),
            'verifyCode' => Yii::t('YifenziModule.member', '验证码')
        );
    }

    public function authenticate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            if (!$this->_identity->authenticate()) {
                if (empty($this->_identity->errorMessage)) {
                    $this->addError('password', Yii::t('YifenziModule.home', '用户名或密码错误'));
                } else {
                    $this->addError('username', $this->_identity->errorMessage);
                }
            }
        }
    }

    /**
     * 
     * @param type $username
     * @return int 返回验证状态
     */
    public function login($username)
    {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($username);
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = 3600 * 24 * 1; // 1 days
            Yii::app()->user->login($this->_identity->getMember()->id, $this->_identity->getMember());
            $this->afterLoginUpdate($this->_identity->getMember());
            return true;
        } else {
            return false;
        }
    }

    /**
     * 登录后更新相关数据
     * @param $member Member
     */
    public function afterLoginUpdate($member)
    {
        $member->logins = $member->logins + 1;
        $member->last_login_time = $member->current_login_time;
        $member->current_login_time = time();
        $member->save(false);
    }

    /**
     * sku 一份子登陆
     */
    public function processLogin()
    {
        $apiMember = new ApiMember;
        $login = $apiMember->login($this->username, $this->password);

		//print_r($login);exit;
		/*if(!empty($login['msg']['gaiNumber'])){
	        $counts = count($login['msg']['gaiNumber']);
		    if($counts >= 2){
			    $this->addError('password', '手机号已绑定多个盖网号，请用GW号登录');
		    }
		}*/
		if(!empty($login['msg']['data'])){
	        $counts = count($login['msg']['data']);
		    if($counts >= 2){
			    $this->addError('password', '手机号已绑定多个盖网号，请用GW号登录');
                return false;
		    }
		}

        //账号是否存在
        if ( empty($login['success']) ){
            $this->addError('password', $login['msg']);
            return false;
        }
        //判断有没有绑定手机号
        if(!empty($login['memberInfo'])){
            if(empty($login['memberInfo']['mobile']) || $login['memberInfo']['mobile'] == '') {
                $this->addError('username', '请绑定手机号码再登录！');
                return false;
            }
        }
        if (is_array($login) && isset($login['success']) && $login['success']) {
            try{
                $sql = "SELECT id,real_name,sku_number,gai_number,username FROM {{member}} WHERE gai_member_id={$login['memberId']}";
                $member = Yii::app()->db->createCommand($sql)->queryRow();
                //先屏蔽
//                Yii::app()->db->createCommand()->update(
//                            '{{member}}',
//                            array('last_login_time'=>'current_login_time','current_login_time'=>time()),
//                            'gai_member_id=:id',
//                            array(':id'=>$login['memberId'])
//                        );
                $openid = Yii::app()->user->getState(WeixinMember::MEMBER_OPENID);
                Yii::app()->user->login($member['id'],$member);
                $result = WeixinMember::processMember($openid); //同步微信用户登陆
                YfzCart::syncData(); //同步 购物车
                /*
          * 设置用户ip
          * */
                YfzMemberIp::setMemberIp($member['id']);
            } catch(CException $e){
                Yii::log($e->getMessage(),CLogger::LEVEL_ERROR);
                return false;
            }
        } else {
            $this->loginTimes=(int)Tool::cache($this->username.'_login_times')->get($this->username.'_login_times');//记录登录错误
            $cacheKey=$this->username.'_login_times';
            $this->loginTimes++;
            Tool::cache($cacheKey)->set($cacheKey,$this->loginTimes);
            $loginCount = Tool::cache($cacheKey)->get($cacheKey);
            if($loginCount >= 3) {
                Yii::app()->user->setState('captchaRequired', 3);
            }
            $this->addError('password', $login['msg']);
            return false;
        }
        return true;
    }

    /**
     * 检查是否需要输入验证码，当用户登录失败一次，则开启验证码
     * @return type
     */
    public static function captchaRequirement() {
        // return Yii::app()->user->getState('captchaRequired') && CCaptcha::checkRequirements();
        return Yii::app()->user->getState('captchaRequired');
    }
    /*
     * 微信自动登录
     * */
    public function weiXinLogin(){
            $openid = Yii::app()->user->getState(WeixinMember::MEMBER_OPENID);
            if (empty($openid)) return false;
            $member_id = Yii::app()->gwpart->createCommand()
                ->select('member_id')
                ->from(WeixinMember::model()->tableName())
                ->where('openid=:oid', array(':oid' => $openid))
                ->queryScalar();
            if ($member_id) {
                try {
                    $sql = "SELECT id,real_name,sku_number,gai_number,username FROM {{member}} WHERE id={$member_id}";
                    $member = Yii::app()->db->createCommand($sql)->queryRow();
                    Yii::app()->user->login($member['id'], $member);
                    $result = WeixinMember::processMember($openid); //同步微信用户登陆
                    YfzCart::syncData(); //同步 购物车
                    /*
              * 设置用户ip
              * */
                    YfzMemberIp::setMemberIp($member['id']);

                } catch (CException $e) {
                    Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
                    return false;
                }
            }
    }
}
