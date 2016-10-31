<?php

/**
 * 公共验证方法类
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class Validator {

    /**
     * 是否整形
     * @param type $value
     * @return boolean
     */
    public static function isInt($value) {
        return true;
    }

    /**
     * @author lhao
     * 验证盖网号，并且返回盖网member;
     * @param str $gaiNumber
     * @return boolean|object|array
     */
    public static function isGwNumber($gaiNumber,$is_array = true)
    {
        if(self::isGaiNumber($gaiNumber))
        {
//             $member = Member::model()->find(array(
//                     'select' => 'id,username,head_portrait,gai_number,mobile,status,type_id,password,password3,salt,referrals_id',
//                     'condition'=>"gai_number=:gai_number",
//                     'params'=>array(":gai_number"=>$gaiNumber)
//                     ));
            $member = Member::getMemberInfoByGaiNumber($gaiNumber);
            if(empty($member))
                return false;
            else
            {
                if($is_array)$member = $member->attributes;
                return $member;
            }
        }else
            return false;
    }
    
    /**
     * @author lhao
     * 验证手机号
     * @param str $mobile
     * @return boolean
     */
    public static function isMobile($mobile)
    {
        $pattern = "/(^\d{11}$)|(^852\d{8}$)/";
        if(preg_match($pattern, $mobile))
            return true;
        else
            return false;
    }

    /**
     * 验证密码
     * @param $value
     * @return bool
     */
    public static function isPassword($value)
    {
        if(preg_match("/^[0-9a-zA-Z]{6,}$/", $value))
            return true;
        else
            return false;
    }

    /**
     * 验证盖网编号gw
     * @param $value
     * @return bool
     */
    public static function isGaiNumber($value)
    {
        if(preg_match("/^(GW|gw|Gw|gW)[0-9]{7,20}$/", $value))
            return true;
        else
            return false;
    }

    /**
     * 验证id
     * @param $value
     * @return bool
     */
    public static function isId($value)
    {
        if(preg_match("/^[0-9]{1,11}$/", $value))
            return true;
        else
            return false;
    }

    /**
     * 验证token
     * @param $value
     * @param string $len
     * @return bool
     */
    public static function isToken($value,$len='20')
    {
        if(preg_match("/^[0-9a-zA-Z]{".$len."}$/", $value))
            return true;
        else
            return false;
    }
    
    /**
     * 过滤密码
     * @param unknown_type $pwd
     */
    public static function checkPassword($pwd)
    {
        if(preg_match("/^[0-9a-zA-Z]{6,128}$/", $pwd))
            return true;
        else
            return false;
    }
    
    public static function checkUmOrder($orderId)
    {
        if(preg_match("/^UM[0-9]+$/", $orderId))
            return true;
        else
            return false;
    }
}
