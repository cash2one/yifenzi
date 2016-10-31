<?php

/**
 * Created by PhpStorm.
 * User: derek
 * Date: 2016/8/17
 * Time: 15:00
 */
class YfzQuickPlay
{
    //网关url地址
    protected $gateUrl;

    //密钥
    protected $key;

    protected $parameters;

    public function __construct($Arr=array())
    {
        $Conf = require('QuickConfig.php') ;
        if ( count($Arr) > 0 ){
            $Conf['return_url'] = $Arr['return_url'];
            $Conf['notify_url'] = $Arr['notify_url'];
        }

        $this->_init($Conf);
    }

    public function _init( array $data ){
//        $this->gateUrl = 'https://www.epaylinks.cn/paycenter/v2.0/getoi.do';
        $this->setKey( $data['key'] );
        $this->setGateURL( $data['url'] );
        $this->setParameter('busi_code',$data['busi_code']);
        $this->setParameter('merchant_no',$data['merchant_no']);
        $this->setParameter('terminal_no',$data['terminal_no']);
        $this->setParameter('order_no','');
        $this->setParameter('amount','0.00');
        $this->setParameter('return_url',$data['return_url']);
        $this->setParameter('notify_url',$data['notify_url']);
        $this->setParameter('product_name','');
        $this->setParameter('currency_type',$data['currency_type']);
        $this->setParameter('sett_currency_type',$data['currency_type']);
        $this->setParameter('client_ip',$data['client_ip']);
        $this->setParameter('sign_type',$data['sign_type']);
        $this->setParameter('bank_code',$data['bank_code']);
        $this->setParameter('base64_memo',$data['base64_memo']);
    }

    /**
     *获取入口地址,不包含参数值
     */
    public  function getGateURL() {
        return $this->gateUrl;
    }

    /**
     *设置入口地址,不包含参数值
     */
    public  function setGateURL($gateUrl) {
        $this->gateUrl = $gateUrl;
    }

    /**
     *获取密钥
     */
    public  function getKey() {
        return $this->key;
    }

    /**
     *设置密钥
     */
    public function setKey($key) {
        $this->key = $key;
    }

    /**
     *获取参数值
     */
    public function getParameter($parameter) {
        return $this->parameters[$parameter];
    }

    /**
     *设置参数值
     */
    public function setParameter($parameter, $parameterValue) {
        $this->parameters[$parameter] = $parameterValue;
    }

    /**
     *获取所有请求的参数
     *@return array
     */
    public function getAllParameters() {
        return $this->parameters;
    }

    /**
     *获取带参数的请求URL
     */
    public function getRequestURL() {

        $this->buildRequestSign();

        $reqPar = "";
        ksort($this->parameters);
        foreach($this->parameters as $k => $v) {
            $reqPar .= $k . "=" . urlencode($v) . "&";
        }

        //去掉最后一个&
        $reqPar = substr($reqPar, 0, strlen($reqPar)-1);

        $requestURL = $this->getGateURL() . "?" . $reqPar;

        return $requestURL;

    }

    /**
     *获取调试信息
     */
    public function getDebugMsg() {
        return $this->debugMsg;
    }

    /**
     *重定向到支付
     */
    public function doSend() {
        header("Location:" . $this->getRequestURL());
        exit;
    }

    /**
     *生成SHA256摘要,规则是:按ASCII码顺序排序,遇到空值的参数不参加签名。
     */
    public function buildRequestSign() {
        $signOrigStr = "";
        ksort($this->parameters);
        foreach($this->parameters as $k => $v) {
            if("" != $v && "sign" != $k) {
                $signOrigStr .= $k . "=" . $v . "&";
            }
        }
        $signOrigStr .= "key=" . $this->getKey();
        $sign = strtolower(hash("sha256",$signOrigStr));
        $this->setParameter("sign", $sign);

        //调试信息
        //$this->_setDebugMsg($signOrigStr . " => sign:" . $sign);

    }

    /**
     *设置调试信息
     */
    public function _setDebugMsg($debugMsg) {
        $this->debugMsg = $debugMsg;
    }
}