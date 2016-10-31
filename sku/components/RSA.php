<?php

/**
 * 密钥类
 * 加密，解密
 * @author qinghao.ye <qinghaoye@sina.com>
 */
class RSA {

    public $privateKey;     //私钥
    public $publicKey;     //私钥

    const PASSWORD_SALT = 'gaiwangapi';

    /**
     * 初始化私钥
     */
    function __construct() {
        $private = Yii::getPathOfAlias('keyPath') . DS . 'rsa_private_key.pem';
        //私钥
        $fp = fopen($private, "r");
        $this->privateKey = fread($fp, 8192);
        fclose($fp);
    }

    /**
     * 解密方法
     * @param string $value
     * @return string|null
     * @author wanyun.liu <wanyun_liu@163.com>
     */
    public function decrypt($value) {
        $len = strlen($value);
        $string = pack("H" . $len, $value);
        $res = openssl_get_privatekey($this->privateKey);
        openssl_private_decrypt($string, $result, $res);
        return $result;
    }

    /**
     * 密码加密-用于盖网通
     */
    public static function passwordEncrypt($string) {
        $string = base64_encode($string);
        $salt = base64_encode(self::PASSWORD_SALT);
        $string = base64_encode($string . $salt);
        return $string;
    }

    /**
     * 密码解密-用于盖网通
     */
    public static function passwordDecrypt($string) {
        $string = base64_decode($string);
        $salt = base64_encode(self::PASSWORD_SALT);
        $string = str_replace($salt, '', $string);
        $string = base64_decode($string);
        return $string;
    }

    /**
     * 加密静态方法
     * @author LC
     */
    public static function quickEncrypt($data) {
        $rsa = new RSA();
        return $rsa->encrypt($data);
    }

    /**
     * RSA加密数据
     * @param $password
     * @return string
     */
    public function encrypt($data){
        $pubKey = openssl_get_publickey($this->pubicKey);
        openssl_public_encrypt($data,$encrypted,$pubKey);
        return bin2hex($encrypted); //转换十六进制
    }


}
