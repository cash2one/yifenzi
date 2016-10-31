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
        //var_dump($_SESSION);
        $this->id = isset($_SESSION[$this->_loginKey]['id'])?$_SESSION[$this->_loginKey]['id']:'';

        $this->checkLogin();
    }

    public function checkUser($userName,$password){
        $data = array('user'=>$userName,'password'=>$password);
        $url = "member.orderapi.com/index/login";
        $rs = Tool::post($url,$data);
        $rsArray = CJSON::decode($rs);
        if($rsArray['status']  == 200){
            $url2 = "member.orderapi.com/index/info";
            $rs2 = Tool::post($url2,$rsArray['data']['memberId']);
            $rsArray2 = CJSON::decode($rs2);
            if($rsArray2['status'] == 200){
                foreach($rsArray2['data'] as $key =>$v){
                    $this->setState($key,$v);
                }
            }
            //$model->login($rs['memberId']);


                //查询用户信息


        }

        return true;
    }

    public function Login($id,$info=array()){
        $this->id = $id;
        $_SESSION[$this->_loginKey]['id'] = $this->id;
        $_SESSION[$this->_loginKey]['info'] = $info;
    }


    public function checkLogin(){
        if(isset($this->id) && $this->id){
            $isGuest = false;
            return true;
        }else{
            $isGuest = true;
            return false;
        }
    }


}
