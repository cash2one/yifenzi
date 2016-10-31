<?php

/**
 * 后台登录表单模型
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class LoginForm extends CFormModel
{

    public $username;
    public $password;
    public $verifyCode;
    private $_identity;

    public function rules()
    {
        return array(
            array('username, password,verifyCode', 'required'),
            array('password', 'authenticate'),
            array('verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements()),
        );
    }

    public function attributeLabels()
    {
        return array(
            'username' => '用户名',
            'password' => '密码',
            'verifyCode'=>'验证码',
        );
    }

    public function authenticate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            if (!$this->_identity->authenticate())
                $this->addError('password', '错误的用户名或密码');
        }
    }

    public function login()
    {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = 3600 * 24 * 1; // 1 days
            Yii::app()->user->login($this->_identity, $duration);
            $user = User::model()->findByPk($this->_identity->getId());
            $user->logins += 1;
            $user->save();
            $message = '用户登录成功：' . $this->_identity->name . '(' . $user->real_name . ')';
            $url = Yii::app()->createAbsoluteUrl('/main/');
            Yii::app()->request->redirect($url);
            SystemLog::record($message);
            return true;
        } else
            return false;
    }

}
