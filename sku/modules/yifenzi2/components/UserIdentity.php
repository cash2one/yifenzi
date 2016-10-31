<?php

/**
 * 商家用户验证类
 * @author leo8705
 */
class UserIdentity extends CUserIdentity
{

    private $_id; //member_id
    private $_member; // member model
    const  ERROR_USERNAME_FAIL = 3; //账号异常

    public function authenticate()
    {
        $selectMember = 'id,username,password,gai_number,mobile,status,salt,last_login_time,current_login_time';

        /** @var  $user  Member*/
        $user = Member::model()->findAll(array(
            'select'=>$selectMember,
            'condition'=>'username=:params or gai_number=:params or mobile=:params limit 1',
            'params'=>array(':params'=>$this->username),
        ));
        if($user) $user = $user[0];
        if (!$user){
            return $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else {
            if(!$user->validatePassword($this->password)){
               return $this->errorCode = self::ERROR_PASSWORD_INVALID; 
            }
            if($user->status != Member::STATUS_NO_ACTIVE && $user->status != Member::STATUS_NORMAL){
                return $this->errorCode = self::ERROR_USERNAME_FAIL;
            }
        }
        
        //密码错误
        $this->errorCode = self::ERROR_NONE;
        $this->_id = $user->id;
        $states = array(
            'gw'=>$user->gai_number,
            'selectLanguage'=>isset($_POST['select_language']) ? $_POST['select_language'] : HtmlHelper::LANG_ZH_CN,
        );
        $this->setPersistentStates($states);
        $this->_member = $user;
        return $this->errorCode == self::ERROR_NONE;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getMember(){
        return $this->_member;
    }
}
