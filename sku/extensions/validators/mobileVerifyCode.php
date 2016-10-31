<?php

/**
 * 手机验证码验证类
 *
 * $this->setSession($_POST['mobile'], array('time'=>time(),'verifyCode'=>$verifyCode));
 *
 * @author zhenjun_xu<412530435@qq.com>
 */
class mobileVerifyCode extends CValidator
{
    protected function validateAttribute($object, $attribute)
    {
        if (empty($object->$attribute)) return false; //当验证码还没有输入的时候，不要验证，因为验证失败会删除cookie/session
        $cookie = Yii::app()->request->cookies[$object->mobile];
//        Tool::pr($object->$attribute);
        $verifyCode = '';
        if ($cookie && $cookie->value) {
            $verifyCode = $cookie->value;
            $verifyCode = unserialize(Tool::authcode($verifyCode, 'DECODE'));
        } else {
            $code = Yii::app()->user->getState($object->mobile);
            if ($code) $verifyCode = unserialize(Tool::authcode($code, 'DECODE'));
        }

        if (!$verifyCode) {
//			return false;   //这导致 未发送验证码，随便输入都能通过了
            $this->addError($object, $attribute, Yii::t('home', '验证码未发送或已失效，请点击获取'));
        } else {
            if (time() - $verifyCode['time'] > 60 * 5) {
                $this->addError($object, $attribute, Yii::t('home', '验证码已经过期，请重新点击获取'));
            }
            if ($verifyCode['verifyCode'] != $object->$attribute) {

                //如果手机验证码输入错误超过3次,就显示图形验证码
                $showCaptcha = Yii::app()->user->getState('showCaptcha');
                if($showCaptcha){
                    $showCaptcha +=1;
                }else{
                    $showCaptcha =1;
                }
                Yii::app()->user->setState('showCaptcha', $showCaptcha);
                if ($showCaptcha == 3) {
                    $cookie->value = null;
                    Yii::app()->request->cookies[$object->mobile] = $cookie;
                    Yii::app()->user->setState($object->mobile, null);
                    $this->addError($object, $attribute, 'error');
                }else{
                    $this->addError($object, $attribute, Yii::t('home', '验证码不正确，请重新点击获取'));
                }
            }
        }
        return true;
    }
}
