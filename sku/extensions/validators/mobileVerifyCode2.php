<?php

/**
 * 手机验证码验证类,在一个页面需要验证两个手机号的情况，另外一个是mobile2
 * 
 * $this->setSession($_POST['mobile2'], array('time'=>time(),'verifyCode'=>$verifyCode));
 *
 * @author zhenjun_xu<412530435@qq.com>
 */
class mobileVerifyCode2 extends CValidator {

    protected function validateAttribute($object, $attribute) {
        if(empty($object->$attribute)) return false; //当验证码还没有输入的时候，不要验证，因为验证失败会删除cookie/session
        $cookie = Yii::app()->request->cookies[$object->mobile2];
        $verifyCode = '';
        if($cookie && $cookie->value) {
            $verifyCode = $cookie->value;
            $verifyCode = unserialize(Tool::authcode($verifyCode,'DECODE'));
        }else{
            $code =Yii::app()->user->getState($object->mobile2);
            if($code) $verifyCode = unserialize( Tool::authcode($code,'DECODE'));
        }

        if (!$verifyCode) {
//			return false;   //这导致 未发送验证码，随便输入都能通过了
            $this->addError($object, $attribute, Yii::t('home', '验证码未发送，请点击获取'));
        }else{
            if(time()-$verifyCode['time']>60*5){
                $this->addError($object, $attribute, Yii::t('home', '验证码已经过期，请重新获取获取'));
            }
            if($verifyCode['verifyCode']!=$object->$attribute){
                $this->addError($object, $attribute, Yii::t('home', '验证码不正确，请重新获取获取'));
                $cookie->value = null;
                Yii::app()->request->cookies[$object->mobile2] = $cookie;
                Yii::app()->user->setState($object->mobile2,null);
            }
        }

    }


}
