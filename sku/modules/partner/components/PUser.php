<?php

/**
 * 商家用户验证类
 * @author leo8705
 */

class PUser extends CWebUser
{

    private $_id;
    private $_userName;
    private $_member;

    public $_loginKey = 'partnerLoginKey';
    public $_publicKey = 'partnerPublicKey';

    public $id;
    public $userName;
    public $member;
    public $isGuest;

    public $loginPath = '/partner/home/login/';
    public $homePath = '/partner/home/index/';

    public function init(){
        $this->id = isset($_SESSION[$this->_loginKey]['id'])?$_SESSION[$this->_loginKey]['id']:'';

        $this->checkLogin();
    }

    public function Login($id,$info=array()){
        $this->id = $id;
       $_SESSION[$this->_loginKey]['id'] = $this->id;
        $_SESSION[$this->_loginKey]['info'] = $info;
    }


    public function checkLogin(){
        if(isset($this->id)){
            $isGuest = false;
            return true;
        }else{
            $isGuest = ture;
            return false;
        }
    }


}
