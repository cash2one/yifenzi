<?php

/**
 * 商家用户验证类
 * @author leo8705
 */
class UserIdentity extends CUserIdentity
{

    private $_id;
    private $_enterpriseId;
    /** @var  int 店小二 id */
    private $_assistantId;
    /** @var  $_member Member */
    private $_member;

    public function authenticate()
    {
        $selectMember = 'id,username,password,gai_number,mobile,status,head_portrait,type_id,enterprise_id,salt,logins,last_login_time,current_login_time';

        /** @var  $user  Member*/
        /** @var $assistant Assistant */


            $user = Member::model()->findAll(array(
                'select'=>$selectMember,
                'condition'=>'username=:params or gai_number=:params or mobile=:params order by is_master_account DESC limit 1',
                'params'=>array(':params'=>$this->username),
            ));
            if($user) $user = $user[0];

       
        if (!$user){
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }
        
        //密码错误
            //被删除，禁止登录
        //var_dump($user);die;
                $this->errorCode = self::ERROR_NONE;
                $this->_id = $user->id;
                /** 如果是店小二登录，username是店小二的username */
                $states = array(
                    'avatar'=>$user->head_portrait,
                    'typeId'=>$user->type_id,
                    'gw'=>$user->gai_number,
                    'selectLanguage'=>isset($_POST['select_language']) ? $_POST['select_language'] : HtmlHelper::LANG_ZH_CN,
                );

                if(empty($this->_assistantId)){
                    $this->username = empty($user->username)? $user->gai_number : $user->username;
                    $this->setPersistentStates($states);
                }else{
                    $this->username = $assistant->username;
                    $states['avatar'] = $assistant->avatar;
                    $states['assistantId'] = $this->_assistantId;
                    $this->setPersistentStates($states);
                }
                $this->_member = $user;


        return $this->errorCode == self::ERROR_NONE;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getEnterpriseId(){
        return $this->_enterpriseId;
    }

    public function getAssistantId(){
        return $this->_assistantId;
    }

    public function getMember(){
        return $this->_member;
    }
}
