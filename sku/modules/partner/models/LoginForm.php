<?php

/**
 * 商家登录模型
 * @author wanyun.liu <wanyun.liu@163.com>
 */
class LoginForm extends CFormModel {

    public $username;
    public $password;
    public $verifyCode;
    private $_identity;

    public function rules() {
        return array(
            array('username, password,verifyCode', 'required'),
            array('password', 'authenticate'),
            array('verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements()),
            array('username, password', 'safe'),
            array('username','match', 'pattern' => '/^GW\d+$/','message'=>Yii::t('partnerModule.home','请输入正确的盖网号')),
        );
    }

    public function attributeLabels() {
        return array(
            'username' => Yii::t('partnerModule.home', '用户名'),
            'password' => Yii::t('partnerModule.home', '密码'),
            'verifyCode' => Yii::t('partnerModule.home', '验证码')
        );
    }

    public function authenticate($attribute, $params) {
        if (!$this->hasErrors()) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            if (!$this->_identity->authenticate()) {
                if (empty($this->_identity->errorMessage)) {
                    $this->addError('password', Yii::t('partnerModule.home', '用户名或密码错误'));
                } else {
                    $this->addError('username', $this->_identity->errorMessage);
                }
            }
        }
    }

    public function login($username) {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($username);
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = 3600 * 24 * 1; // 1 days
            Yii::app()->user->login($this->_identity, $duration);
            $this->afterLoginUpdate($this->_identity->getMember());
            return true;
        } else
            return false;
    }

    /**
     * 登录后更新相关数据
     * @param $member Member
     */
    public function afterLoginUpdate($member) {
        $assistantId = $this->_identity->getAssistantId();
        if($assistantId){ //店小二登录
            $assistant = Assistant::model()->findByPk($assistantId);
            $assistant->logins = $assistant->logins+1;
            $assistant->save(false);
        }else{
            $member->logins = $member->logins + 1;
            $member->last_login_time = $member->current_login_time;
            $member->current_login_time = time();
            $member->save(false);
        }

    }

}
