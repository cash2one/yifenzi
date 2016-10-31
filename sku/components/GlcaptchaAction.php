<?php
class GlcaptchaAction extends CCaptchaAction
{
    public function validate($input,$caseSensitive)
    {
        $code = $this->getVerifyCode();
        $valid = $caseSensitive ? ($input === $code) : !strcasecmp($input,$code);
        $session = Yii::app()->session;
        $session->open();
        $name = $this->getSessionKey() . 'count';
        if(!Yii::app()->request->isAjaxRequest){
            $session[$name] = $session[$name] + 1;
        }
        if($session[$name] > $this->testLimit && $this->testLimit > 0){
            $this->getVerifyCode(true);
        }
        return $valid;
    }
}
 
?>