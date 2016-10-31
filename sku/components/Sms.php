<?php

/**
 * 短信发送类
 *
 * @author zhenjun_xu<412530435@qq.com>
 */
class Sms
{
    /**
     * 发送短信
     * @param int $phoneNumber 手机号码单个
     * @param string $content 短信内容
     * @param array $datas 发送的数据
     * @param int $tmpId 容联云通讯需要用到的模板ID
     * @api  int  发送时定义的接口
     * @throws Exception
     * @return array()
     */
    public static function send($phoneNumber, $content, $datas, $tmpId, $api = null)
    {
        $errorReturn = array(
            'create_time' => time(),
            'send_count' => 0,
            'result' => 0,
            'send_status' => SmsLog::STATUS_FAILD,
            'send_api' => 0,
            'phone' => $phoneNumber > 0 ? $phoneNumber : 0,
            'send_time' => time(),
            'msg' => $content,
            'ip' => Tool::getIP() ,
        );
        if (!is_numeric($phoneNumber) || empty($content)) {
            return $errorReturn;
        }
        $smsConfig = Tool::getConfig('smsapi', ''); //短信接口配置
        if($api !== null)
        {
            $smsConfig['currentAPI'] = $api;
        }
        if (strlen($phoneNumber) == 8) {
            $result = self::_sendYt('852'.$phoneNumber, $content, $smsConfig);
        }else if(substr($phoneNumber,0,3)=='852'){
            $result = self::_sendYt($phoneNumber, $content, $smsConfig);
        } else {
            $result = self::_sendMain($phoneNumber, $content, $datas, $tmpId, $smsConfig);
        }

        if (empty($result)) return $errorReturn;
        return $result;
    }

    //当前大陆的api接口
    private static $apisMain = array(
        SmsLog::INTERFACE_YX => 1,
        SmsLog::INTERFACE_JXT => 1,
        SmsLog::INTERFACE_DXT => 1,
        SmsLog::INTERFACE_RLY => 1,
    );
    /**
     * 大陆的发短信方法
     * @param unknown $phoneNumber
     * @param unknown $content
     * @param array $datas
     * @param int $tmpId //容联云通讯模板ID
     * @param unknown $smsConfig
     */
    private static function _sendMain($phoneNumber, $content, $datas, $tmpId, $smsConfig, $useApi = null)
    {
        if($useApi === null)
        {
            $useApi = self::$apisMain;
        }
        if($smsConfig['currentAPI'] == SmsLog::INTERFACE_YX)
        {
            $result = self::_sendYx($phoneNumber, $content, $smsConfig);
        }
        elseif ($smsConfig['currentAPI'] == SmsLog::INTERFACE_JXT)
        {
            $result = self::_sendJxt($phoneNumber, $content, $smsConfig);
        }
        elseif ($smsConfig['currentAPI'] == SmsLog::INTERFACE_DXT)
        {
            $result = self::_sendDxt($phoneNumber, $content, $smsConfig);
        }
        elseif ($smsConfig['currentAPI'] == SmsLog::INTERFACE_RLY)
        {
            $result = self::_sendRly($datas,$content,$phoneNumber,$tmpId);
        }
        else
        {
            $result = self::_sendJxtAdvert($phoneNumber, $content, $smsConfig);
            return $result;
        }
        unset($useApi[$smsConfig['currentAPI']]);
        $smsConfig['currentAPI'] = array_rand($useApi);
        if(empty($result) || $result['send_status'] != SmsLog::STATUS_SUCCESS)
        {
            if(count($useApi)>0)
            {
                $result = self::_sendMain($phoneNumber, $content,$datas, $tmpId, $smsConfig, $useApi);
            }
        }
        return $result;
    }


    /**
     * 易信接口，单个手机号码的短信发送
     * @param int $phoneNumber
     * @param string $content
     * @param array $config 接口配置
     * @return array()
     */
    private static  function _sendYx($phoneNumber, $content, $config)
    {

        $data = array(
            'CorpID' => $config['yxCorpID'],
            'LoginName' => $config['yxLoginName'],
            'send_no' => $phoneNumber,
            'msg' => mb_convert_encoding($content, 'GBK', 'UTF-8'),
            'passwd' => $config['yxPassword'],
            'LongSms' => 1,
        );
        $sendUrl = $config['yxSendUrl'] . '?' . http_build_query($data);
//        var_dump($sendUrl);exit;
        $create_time = time();
        $tmpResult = mb_convert_encoding(self::_curlGet($sendUrl), 'UTF-8', 'GBK');
        $tmpResult = explode(',', $tmpResult);
        if (count($tmpResult) < 2) return array();
        return array(
            'create_time' => $create_time,
            'send_count' => $tmpResult[0],
            'result' => $tmpResult[1],
            'send_status' => $tmpResult[0] > 0 ? SmsLog::STATUS_SUCCESS : SmsLog::STATUS_FAILD,
            'send_api' => SmsLog::INTERFACE_YX,
            'phone' => $phoneNumber,
            'send_time' => time(),
            'msg' => $content,
            'ip' => Tool::getIP(),
        );
    }

    /**
     * 短信通接口，单个手机号码的短信发送
     * @param int $phoneNumber
     * @param string $content
     * @param array $config 接口配置
     * @return array()
     */
    private static  function _sendDxt($phoneNumber, $content, $config)
    {
        $data = array(
            'zh' => $config['dxtZh'],
            'mm' => $config['dxtMm'],
            'hm' => $phoneNumber,
            'nr' => $content,
            'dxlbid' => $config['dxtDxlbid'],
            'extno' => $config['dxtExtno'],
        );
        $sendUrl = $config['dxtSendUrl'] . '?' . http_build_query($data);
        $create_time = time();
        @$tmpResult = simplexml_load_string(self::_curlGet($sendUrl));
        if ($tmpResult === false) {
            return array();
        }
        $tmpResult = (string)$tmpResult;
        $tmpResult = explode(',', $tmpResult);
        $tmpResult = explode(':', $tmpResult[0]);
        $tmpResult = $tmpResult[1];
        return array(
            'create_time' => $create_time,
            'send_count' => $tmpResult == 0 ? 1 : 0,
            'result' => $tmpResult,
            'send_status' => $tmpResult == 0 ? SmsLog::STATUS_SUCCESS : SmsLog::STATUS_FAILD,
            'send_api' => SmsLog::INTERFACE_DXT,
            'phone' => $phoneNumber,
            'send_time' => time(),
            'msg' => $content,
            'ip' => Tool::getIP(),
        );
    }

    /**
     * 香港 易通讯 短信接口
     * @param $phoneNumber
     * @param $content
     * @param $config
     * @return array
     */
    private static function _sendYt($phoneNumber, $content, $config)
    {
        $data = array(
            'msg' => ZhTranslate::convert($content,'zh-hk'),// //翻译
            'phone' => $phoneNumber,
            'pwd' => $config['ytPwd'],
            'accountno' => $config['ytAccountNo'],
        );
        $sendUrl = $config['ytSendUrl'] . '?' . http_build_query($data);
        $create_time = time();
        $tmpResult = self::_curlGet($sendUrl);
        return array(
            'create_time' => $create_time,
            'send_count' => (int)$tmpResult > 0 ? 1 : 0,
            'result' => $tmpResult,
            'send_status' => (int)$tmpResult > 0 ? SmsLog::STATUS_SUCCESS : SmsLog::STATUS_FAILD,
            'send_api' => SmsLog::INTERFACE_YTX,
            'phone' => $phoneNumber,
            'send_time' => time(),
            'msg' => $content,
            'ip' => Tool::getIP(),
        );
    }

    /**
     * 吉信通短信接口
     * @param unknown $phoneNumber
     * @param unknown $content
     * @param unknown $config
     */
    public static $jxtReturned = array(
        '000' => '成送成功！',
        '-01' => '当前账号余额不足！',
        '-02' => '当前用户ID错误！',
        '-03' => '当前密码错误！',
        '-04' => '参数不够或参数内容的类型错误！',
        '-05' => '手机号码格式不对！',
        '-06' => '短信内容编码不对！',
        '-07' => '短信内容含有敏感字符！',
        '-8'  => '无接收数据',
        '-09' => '系统维护中..',
        '-10' => '手机号码数量超长！',
        '-12' => '其它错误！',
    );
    public static $jxtPingbi = array(
        '交易' => '交.易',
        '获得' => '获.得',
        '投资' => '投.资',
    ); //需要屏蔽的字段
    private static function _sendJxt($phoneNumber, $content, $config)
    {
        $content = self::converContent($content, self::$jxtPingbi);
        $data = array(
            'id' => $config['jxtLoginName'],
            'to' => $phoneNumber,
            'content' => mb_convert_encoding($content, 'GBK', 'UTF-8'),
            'pwd' => $config['jxtPassword'],
        );
        $sendUrl = $config['jxtSendUrl'] . '?' . http_build_query($data);
        $create_time = time();
        $tmpResult = self::_curlGet($sendUrl);
        $tmpResult = explode('/', $tmpResult);
        if (count($tmpResult) < 2) return array();
        return array(
            'create_time' => $create_time,
            'send_count' => 1,
            'result' => $tmpResult[0].':'.self::$jxtReturned[$tmpResult[0]],
            'send_status' => $tmpResult[0] == '000' ? SmsLog::STATUS_SUCCESS : SmsLog::STATUS_FAILD,
            'send_api' => SmsLog::INTERFACE_JXT,
            'phone' => $phoneNumber,
            'send_time' => time(),
            'msg' => $content,
            'ip' => Tool::getIP(),
        );
    }
    /**
     * 吉信通广告接口
     * @param unknown $phoneNumber
     * @param unknown $content
     * @param unknown $config
     * @return multitype:|multitype:number string unknown Ambigous <unknown, mixed>
     */
    private static function _sendJxtAdvert($phoneNumber, $content, $config)
    {
        $data = array(
            'id' => $config['jxtadvertLoginName'],
            'to' => $phoneNumber,
            'content' => mb_convert_encoding($content, 'GBK', 'UTF-8'),
            'pwd' => $config['jxtadvertPassword'],
        );
        $sendUrl = $config['jxtadvertSendUrl'] . '?' . http_build_query($data);
        $create_time = time();
        $tmpResult = self::_curlGet($sendUrl);
        $tmpResult = explode('/', $tmpResult);
        if (count($tmpResult) < 2) return array();
        return array(
            'create_time' => $create_time,
            'send_count' => 1,
            'result' => $tmpResult[0].':'.self::$jxtReturned[$tmpResult[0]],
            'send_status' => $tmpResult[0] == '000' ? SmsLog::STATUS_SUCCESS : SmsLog::STATUS_FAILD,
            'send_api' => SmsLog::INTERFACE_JXT_ADVERT,
            'phone' => $phoneNumber,
            'send_time' => time(),
            'msg' => $content,
            'ip' => Tool::getIP(),
        );
    }
    /**
     * 将短信内容进行转化，防止运营商拦截
     * @param unknown $content   短信内容
     * @param unknown $pingbiArr 需要屏蔽的内容，采用数组的形式,如何设置参考$jxt_pingbi
     */
    public static function converContent($content, $pingbiArr = null)
    {
        if($pingbiArr != null)
        {
            foreach ($pingbiArr as $key => $val)
            {
                $content = str_replace($key, $val, $content);
            }
        }
        return $content;
    }

    /**
     * curl get 方式 获取
     * @param string $url
     * @return string
     */
    private static function _curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //设置超时
        curl_setopt($ch, CURLOPT_HEADER, 0); //这里不要header，加块效率
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //是否抓取跳转后的页面
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出
        $contents = curl_exec($ch);
        curl_close($ch);
        return $contents;
    }

    /**
     * 获取短信接口类型
     * @param string $mobile
     * return string
     */
    public static function getSmsApi($mobile){
        //如果是香港手机号码，则更换短信接口
        if (strlen($mobile) == 8 || substr($mobile,0,3)=='852') {
            $apiType = Smslog::INTERFACE_YTX;
        }
        else
        {
            $apiType = Tool::getConfig('smsapi', 'currentAPI'); //短信接口配置
        }
        return $apiType;
    }

    /**
     * 发起POST 请求
     * @param string $url
     * @param string $data
     * @param string $header
     * @param int $post
     * @return mixed|string
     */
    private static function _curlPost($url,$data,$header,$post=1)
    {
        //初始化curl
        $ch = curl_init();
        //参数设置
        $res= curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, $post);
        if($post)
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $result = curl_exec ($ch);
        //连接失败
        if($result == FALSE){
            $result = "{\"statusCode\":\"172001\",\"statusMsg\":\"网络错误\"}";
        }
        curl_close($ch);
        return $result;
    }

    /**
     * 发送语音验证码
     * @param string $verifyCode 验证码内容，为数字和英文字母，不区分大小写，长度4-8位
     * @param int $playTimes 播放次数，1－3次
     * @param int $to 接收号码
     * @param string $displayNum 显示的主叫号码
     * @return  mixed
     */
    public static function voiceVerify($verifyCode,$to,$playTimes=3,$displayNum='')
    {
        $smsConfig = Tool::getConfig('smsapi', ''); //短信接口配置
        $batch = date("YmdHis");
        // 拼接请求包体
        $bodyArray = array(
            'appId'=>$smsConfig['ytxAppId'],
            'verifyCode'=>$verifyCode,
            'playTimes'=>$playTimes,
            'to'=>$to,
            'displayNum'=>$displayNum,
        );
        $body= json_encode($bodyArray);
        // 大写的sig参数
        $sig =  strtoupper(md5($smsConfig['ytxSid'] . $smsConfig['ytxToken'] .$batch ));
        // 生成请求URL
        $url=$smsConfig['ytxUrl']."/2013-12-26/Accounts/{$smsConfig['ytxSid']}/Calls/VoiceVerify?sig=$sig";
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($smsConfig['ytxSid'] . ":" . $batch);
        // 生成包头
        $header = array("Accept:application/json","Content-Type:application/json;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = self::_curlPost($url,$body,$header);
        return json_decode($result);
    }

    /**
     * 容联云短信接口
     * @param array $datas 要发送的数据
     * @param int $to  手机号
     * @return int $tmpId 模板ID
     *
     */
    public static function _sendRly($datas,$content,$to,$tmpId)
    {
        $smsConfig = Tool::getConfig('smsapi', ''); //短信接口配置
        $batch = date("YmdHis");

        // 拼接请求包体
        $bodyArray = array(
            'to'=>$to,
            'appId'=>$smsConfig['ytxAppId'],
            'templateId'=>$tmpId, //模板ID
            'datas'=>$datas
        );
        $body= json_encode($bodyArray);
        // 大写的sig参数
        $sig =  strtoupper(md5($smsConfig['ytxSid'] . $smsConfig['ytxToken'] .$batch ));
        // 生成请求URL
        $url=$smsConfig['ytxUrl']."/2013-12-26/Accounts/{$smsConfig['ytxSid']}/SMS/TemplateSMS?sig=$sig";
//        var_dump($url);die;
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($smsConfig['ytxSid'] . ":" . $batch);
        // 生成包头
        $header = array("Accept:application/json","Content-Type:application/json;charset=utf-8","Authorization:$authen");
        // 发送请求
        $result = self::_curlPost($url,$body,$header);
        $createtime =  time();
        $tmpResult =  json_decode($result);
        $tmpResult = (array)$tmpResult;
        if($tmpResult['statusCode'] != '000000'){return array();}
        return array(
            'create_time' =>  $createtime,
            'send_count' =>  1 ,
            'result' => $tmpResult['statusCode']=='000000' ? '发送成功':$tmpResult['statusCode'],
            'send_status' => $tmpResult['statusCode']=='000000'? SmsLog::STATUS_SUCCESS:SmsLog::STATUS_FAILD,
            'send_api' => SmsLog::INTERFACE_RLY,
            'phone' => $to,
            'send_time' => time(),
            'msg' => $content,
            'ip' => Tool::getIP(),
        );
    }
}
