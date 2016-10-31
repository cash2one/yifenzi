<?php

/**
 * 公共用方法类
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class Fun {

    /**
     * 团购客户端输出错误信息
     * @param string|array $data
     * return json
     */
    public static function errorPrint($data=NULL){
        $ary = array('Status' => 0);
        if($data != false){
            if(is_string($data)){
                $ary['Warning'] = $data;
            }else{
                $ary['Response'] = $data;
            }
        }
        header("Content-type:text/html;charset=utf-8");
        echo CJSON::encode($ary);
        Yii::app()->end();
    }

    /**
     * 团购客户端输出成功信息
     * @param string|array $data
     * return json
     */
    public static function successPrint($data=NULL){
        $ary = array('Status' => 1);
        if($data != false){
            if(is_string($data)){
                $ary['Warning'] = $data;
            }else{
                $ary['Response'] = $data;
            }
        }
        header("Content-type:text/html;charset=utf-8");
        echo CJSON::encode($ary);
        Yii::app()->end();
    }

    /**
     * 生成指定长度的随机字符串或md5后的唯一id
     * @param string $length
     * @return string
     */
    public static function generateSalt($length = '') {
        $string = md5(uniqid());
        return $length ? substr($string, -$length) : $string;
    }
    /**
     * 产生随机字符串
     * @param    int        $length  输出长度
     * @param    string     $chars   可选的 ，默认为 0123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ
     * @return   string     字符串
     */
    public static function random($length, $chars = '0123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ') {
        $hash = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    /**
     * 检查验证码是否正确
     * @param $code string 验证码
     * @return bool true|false
     * @throws Exception 抛出异常
     */
    public static function checkerifyCode($code){
        if($code == Yii::app()->session['verifyCode']){
            return true;
        }else{
            throw new Exception('验证码不正确');
        }
    }
    
    /**
     * 访问订单接口，并接收返回数据
     * @route string 路由（例如：cart/create）
     * @param array $data
     */
    public static function orderApiPost($route, $data = array(), $api = ORDER_API_URL)
    {
        if(!empty($data))
        {
//             unset($data['YII_CSRF_TOKEN']);
            $project = $data['p_key'];
            $data = json_encode($data);
            
            $sign = md5($data.ORDER_API_SIGN_KEY);
            $sign = substr($sign,5,20);
            $posts = array(
                'data' => $data,
                'sign' => $sign,
                'project'   =>GAIFUTONG_PROJECT_ID,
                "encryptCode"   =>  md5($data.$project),
            );
            
            $url = $api . '/'.$route;
            $httpClient = new HttpClient($api);
            $response = $httpClient->quickPost($url, $posts);
            $response = json_decode($response,true);
            print_r($response);exit;
//             if($response['status'] == 200)
//             {
//                 return $response['data'];
//             }
//             else
//             {
//                 throw new Exception($response['msg'].':'.$response['data'], $response['status']);
//             }
        }
        else
        {
            throw new Exception('data is empty!', '404');
        }
    }

    /**
     * 返回经arrayAddslashes处理过的字符串或数组(转义字符串)
     * @param string|array $data 需要处理的字符串或数组
     * @return mixed
     */
    public static function arrayAddslashes($data) {
        if (!is_array($data))
            return addslashes($data);
        foreach ($data as $key => $val)
            $data[$key] = self::arrayAddslashes($val);
        return $data;
    }

    /**
     * 计算某个经纬度的周围某段距离的正方形的四个点
     * @param $lng float 经度
     * @param $lat float 纬度
     * @param int $distance float 该点所在圆的半径，该圆与此正方形内切，默认值为1千米
     * @return array 正方形的四个点的经纬度坐标
     */
    public static function coordinateDistance($lng, $lat,$distance = 1){
        $earthRadius = 6378.137; //地球半径
        $dlng =  2 * asin(sin($distance / (2 * $earthRadius)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);

        $dlat = $distance/$earthRadius;
        $dlat = rad2deg($dlat); //转换弧度

        return array(
            'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
            'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
            'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
            'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
        );
    }

    /**
     * 弧角度转换
     * @param $d float 弧度
     * @return float 角度
     */
    public static function rad($d)
    {
        return $d * M_PI / 180.0;
    }

    /**
     * 获取两个坐标点之间的距离，单位km，小数点后2位
     */
    public static function GetDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6378.137;
        $radLat1 = self::rad($lat1);
        $radLat2 = self::rad($lat2);
        $a = $radLat1 - $radLat2;
        $b = self::rad($lng1) - self::rad($lng2);
        $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
        $s = $s * $earthRadius;
        $s = round($s * 100) / 100;
        return $s;
    }

    /**
     * ip转整形
     * @author LC
     * @param string $ip 要转换的ip
     * @return int
     */
    public static function ip2int($ip) {
        return sprintf("%u", ip2long($ip));
    }

    /**
     * ip转字符串
     * @author LC
     * @param int $ip 要转换的ip
     * @return string
     */
    public static function int2ip($ip) {
        return long2ip($ip);
    }


    /**
     * 生成唯一订单号
     * @param int $length 订单号长度(不包含前缀)，最小19位
     * @param string $prefix
     * @return string
     */
    public static function buildOrderNo($length = 20, $prefix = null) {
        $main = date('YmdHis') . substr(microtime(), 2, 3) . sprintf('%02d', mt_rand(0, 99));
        return $prefix . str_pad($main, $length, mt_rand(0, 99999));
    }
    
    /**
     * 获取配置文件
     * @param string $name
     * @param string|null $key
     * @return string
     */
    public static function getConfig($name, $key = null) {
    	$val = self::cache($name . 'config')->get($name);
    	if($val){
    		$array = unserialize($val);
    	}else{
    		$value = WebConfig::model()->findByAttributes(array('name' => $name));
    		if ($value) {
                self::cache($name . 'config')->add($name, $value->value);
    			$array = unserialize($value->value);
    		} else {
    			$file = Yii::getPathOfAlias('webConfig') . DS . $name . '.config.inc';
    			if (!file_exists($file)) return array();
    			$content = file_get_contents($file);
    			$array = unserialize(base64_decode($content));
    		}
    	}
    	return $key ? (isset($array[$key]) ? $array[$key] : '') : $array;
    }
    
    /**
     * 设定缓存路径
     * @param string $dir
     * @return CFileCache or CMemCache
     */
    public static function cache($directory) {
    	$cache = Yii::app()->fileCache;
    	if (get_class($cache) == 'CMemCache') {
    		//memcache
    		$cache->keyPrefix = $directory;
    		return $cache;
    	} else {
    		//文件缓存
    		$path = Yii::getPathOfAlias('cache') . DS . $directory;
    		if (!is_dir($path))
    			self::createDir($directory, Yii::getPathOfAlias('cache'));
    		$cache->cachePath = Yii::getPathOfAlias('cache') . DS . $directory;
    		return $cache;
    	}
    }
    
    /**
     * 设置配置
     * @param string $name 配置文件名
     * @param mixed $key 配置文件值
     * @return boolean
     */
    public static function setConfig($name,$key){
    	$value = WebConfig::model()->findByAttributes(array('name' => $name));
    	if($value){
    		$webConfig = WebConfig::model();
    		$webConfig->id = $value->id;
    	}
    	else{
    		$webConfig = new WebConfig();
    	}
    	$val = serialize($key);

    	$webConfig->name = $name;
    	$webConfig->value = $val;
    	if ($webConfig->save()) { //保存到数据库
    		if (self::cache($name . 'config')->get($name)) {
    			self::cache($name . 'config')->set($name, $val);
    		} else {
    			self::cache($name . 'config')->add($name, $val);
    		}
    		return true;
   	 	}
   	 	else return false;
    }
    
    /**
     * 生成基于URL的图片处理 的网址
     * @param $url 图片地址
     * @param string $params 以逗号分隔的参数
     * @return string
     */
    public static function showImg($url, $params = '') {
    	$info = pathinfo($url);
    	if (!isset($info['extension'])) {
    		return $url;
    	}
    	$urlBase = $info['dirname'] . '/' . $info['filename'] . ',' . $params . '.' . strtolower($info['extension']);
    	//添加thumb_cache字段
    	if (stripos($urlBase, ATTR_DOMAIN) !== false || stripos($urlBase, IMG_DOMAIN) !== false) {
    		$urlReplace = str_replace(ATTR_DOMAIN, ATTR_DOMAIN . '/thumb_cache', $urlBase);
    		return str_replace(IMG_DOMAIN, IMG_DOMAIN . '/thumb_cache', $urlReplace);
    	} else {
    		return 'thumb_cache/' . $urlBase;
    	}
    }

    /**
     * curl 并发处理
     * @param array|\网址数组 $urls 网址数组
     * @param int|\超时时间 $timeout 超时时间，秒
     * @return array/boolean
     */
    public static function concurrentCurl(Array $urls, $timeout = 5){
        if(empty($urls)) return false;
        $queue = curl_multi_init();
        $map = array();
        foreach($urls as $url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //不直接输出到浏览器
            curl_setopt($ch, CURLOPT_HEADER, 0);
            //            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    //是否抓取跳转后的页面
            curl_setopt($ch, CURLOPT_NOSIGNAL, true); //启用时忽略所有的curl传递给php进行的信号。在SAPI多线程传输时此项被默认启用
            curl_multi_add_handle($queue, $ch);
            $map[(string) $ch] = $url;
        }

        $responses = array();
        do{
            while(($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;

            if($code != CURLM_OK) { break; }

            // 找出一个刚好完成的请求
            while($done = curl_multi_info_read($queue)) {

                // 获取请求返回的信息
                //用url做键名
                $responses[$map[(string) $done['handle']]] = curl_multi_getcontent($done['handle']);

                // 移除已经完成的curl句柄
                curl_multi_remove_handle($queue, $done['handle']);
                curl_close($done['handle']);
            }

            // curl_multi_exec 数据输出
            if ($active > 0) {
                curl_multi_select($queue, 0.5);
            }

        } while($active);

        curl_multi_close($queue);
        //获取的$responses是乱序的
        //按照$urls 里的网址重新排序，把结果给$urls
        foreach($urls as $key=>$value){
            $urls[$key] = $responses[$value];
        }
        return $urls;
    }

    /**
     * 获取客户端IP地址,比$_SERVER['REMOTE_ADDR']要准确获得客户端的IP
     */
    public static function getClientIP() {
        static $ip = NULL;
        if ($ip !== NULL)
            return $ip;
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $ip = ( false !== ip2long($ip) ) ? $ip : '0.0.0.0';
        return $ip;
    }

    /**
     * 获取客户端真实ip
     * @author hhb
     */
    public static function getIP() {
        // 定义一个函数getIP()
        $ip = self::getClientIP();
        return self::ip2int($ip);
    }

    /**
     * discuz 的可逆加密解密函数
     * @param string $string 明文 或 密文
     * @param string $operation DECODE 表示解密,其它表示加密
     * @param string $key 密匙
     * @param int|number $expiry 密文有效期
     * @return string
     */
    public static function authcode($string, $operation = 'ENCODE', $key = '', $expiry = 0) {

        $ckey_length = 4; // 随机密钥长度 取值 0-32;
        // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
        // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
        // 当此值为 0 时，则不产生随机密钥

        $key = md5($key ? $key : 'GATE23450dfsdfasfsdf*(&^&%^%^%$345345324523sdfsf');
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }
    
    
       /**
     * 转换单个积分为金额
     * 不做四舍五入
     * @param float $score 积分
     * @param int $typeId 会员类型（正式或消费）
     * @return $float 金额
    * gaiwang copy
     */
    public static function reverseSingle($score, $typeId = null) {
        $type = MemberType::fileCache();
        if ($typeId)
//            return sprintf("%.2f", $score * $type[$typeId]); 
            return bcmul($score, $type[$typeId], 2);
        $typeId = Yii::app()->user->getState('typeId');
//        return sprintf("%.2f", $score * $type[$typeId]);
        return bcmul($score, $type[$typeId], 2);
    }

    /**
     * 检验验证码是否正确
     * @param $phone
     * @param string $checkcode
     * @internal param string $code 验证码
     * @internal param string $content 内容
     * @return bool 是否正确
     */
    public static function checkVerifyCode($phone,$checkcode='gw') {
        $res = Yii::app()->db->createCommand()->from('{{checkcode}}')->where("phone='{$phone}'")->queryRow();
        if(empty($res)) return '验证码不存在';
        if($res['overtime'] < time()){
            Yii::app()->db->createCommand()->delete('{{checkcode}}', "phone='{$phone}'");
            return '验证码超时';
        }else{
            if($checkcode == $res['checkcode']){
                Yii::app()->db->createCommand()->delete('{{checkcode}}', "phone='{$phone}'");
                return true;
            }
            return '验证码错误';
        }
    }

    /**
     * 生成手机短信验证码
     * @param $phone string 手机号
     * @param string $expire 过期时间
     * @return string 短信验证码
     */
    public static function createPhoneValifyCode($phone,$expire='300') {
        $code = Fun::random('6','0123456789');
        $sql = "replace into {{checkcode}} (phone,checkcode,overtime) values('{$phone}','{$code}',".(time() + $expire).")";
        if(Yii::app()->db->createCommand($sql)->execute()){
            return $code;
        }
    }

    /**
     * 创建目录
     * 可以递归创建，默认是以当前网站根目录下创建
     * 第二个参数指定，就以第二参数目录下创建
     * @param string $path      要创建的目录
     * @param string $webroot   要创建目录的根目录
     * @return boolean
     */
    public static function createDir($path, $webroot = null) {
        $path = preg_replace('/\/+|\\+/', DS, $path);
        $dirs = explode(DS, $path);
        if (!is_dir($webroot))
            $webroot = Yii::getPathOfAlias('webroot');
        foreach ($dirs as $element) {
            $webroot .= DS . $element;
            if (!is_dir($webroot)) {
                if (!mkdir($webroot, 0777))
                    return false;
                else
                    chmod($webroot, 0777);
            }
        }
        return true;
    }

    /**
     * 创建rsa公钥和密钥
     * @param $string $machineCode	盖机编码
     * return array 包含公钥和密钥的数组
     */
    public static function createRsaKey($code)
    {
        //参数配置，不过感觉没用
        $config = array(
                'digest_alg' => $code,
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
        );
    
        //创建公钥密钥中间变量
        $tmp = openssl_pkey_new($config);
    
        //如果中间变量生成成功
        if ($tmp) {
            //根据中间变量生成私钥
            openssl_pkey_export($tmp, $privateKey);
    
            //根据中间变量生成公钥
            $publicKey = openssl_pkey_get_details($tmp);
            $publicKey = $publicKey['key'];
        } else {
            Yii::app()->end();
        }
    
        //去掉开头和结尾,根据实际情况截取
        $publicKeyApk = substr($publicKey, 27, -26);		//-----BEGIN PUBLIC KEY-----  和-----END PUBLIC KEY-----
        return array('privateKey'=>$privateKey, 'publicKey'=>$publicKey, 'publicKeyApk' => $publicKeyApk);
    }
    
    /**
     * 关键词过滤  替换
     * @param unknown $string
     * @param unknown $fileName
     * @return boolean
     */
    public static function banwordReplace($string, $replace = '*') {
        $words = self::getConfig('filterworld');
        $words = $words['filterWorld'];
        $words = str_replace(',', '|', $words);
        //     	$string = strtolower($string);
    
        $matched = preg_match('/' . $words . '/i', $string, $result);
        if ($matched && isset($result[0]) && strlen($result[0]) > 0) {
            if (strlen($result[0]) == 2) {
                $matched = preg_match('/' . $words . '/iu', $string, $result);
            }
            if ($matched && isset($result[0]) && strlen($result[0]) > 0) {
                return preg_replace('/' . $words . '/iu', str_pad($replace, mb_strlen($result[0], Yii::app()->charset), $replace), $string);
            }
        }
    
        return $string;
    }
    /**
     * 验证手机号是否正确
     * @param unknown_type $mobile
     */
    public static function validateMobile($mobile)
    {
        $_pattern = "/^13[0-9]{1}[0-9]{8}$|^15[0-9]{1}[0-9]{8}$|^18[0-9]{1}[0-9]{8}$|^14[0-9]{1}[0-9]{8}$|^(852){0,1}[0-9]{8}$/";
        if (!preg_match($_pattern, $mobile))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    
    /**
     * 一份子中奖计算方法
     * 当期商品的人数满足时要运行此方法
     * @param string $order_id 
     * @param string $goods_id
     * @param string $current_nper 中奖码
     * @throws Exception
     */
    public static function calculateWinning($order_id=false, $goods_id=false, $current_nper=false){
        $retArr = array();
        $sql = "select * from ".YIFENZI.'.gw_yifenzi_yfzgoods where goods_id='.$goods_id;
        $GoodsData = Yii::app()->db->createCommand($sql)->queryRow();
        
        //用这个商品的市场价格 “/”每人次得出来库存，注意二者取余不能大于0
        $_goods_number = ($GoodsData['shop_price'] * 1) / ($GoodsData['single_price'] * 1);

        if ($GoodsData['current_nper'] != $current_nper)
            throw new Exception("订单提交失败");
        
        //取该商品最后购买时间前网站 所有商品的最后100条购买记录；
        $sql = "select order_id,addtime,user_name,member_id from ".YIFENZI.'.gw_yifenzi_order where order_status > 0 order by addtime desc limit 100';
        $Data = Yii::app()->db->createCommand($sql)->queryAll();
        if (!$Data)
            throw new Exception("订单提交失败");
        
        $retArr['yffdata'] = array();
        $retArr['hisdata'] = array();
        $retArr['sumhisdata'] = array();
        $retArr['formuladata'] = array();
        $retArr['allusername'] = array();
        
        foreach ($Data as $k => $v) {
            list($date,$sec) = explode(".", $v['addtime']);
            $retArr['yffdata'][$v['order_id']] = date('Y-m-d',$date);
            $retArr['hisdata'][$v['order_id']] = date("H:i:s",$date).'.'.$sec;
            $member = Member::getMemberInfo($v['member_id']);
            $retArr['allusername'][$v['order_id']] = $member['gai_number'];
            list($h,$i,$s) = explode(":", date('H:i:s',$date));
            $retArr['sumhisdata'][$v['order_id']] = $h.$i.$s.$sec;
        }        

        //H:i:s.sec时间总和
        $retArr['formuladata']['h_i_s_sum'] = array_sum($retArr['sumhisdata']);
        $retArr['formuladata']['nperall'] =   $_goods_number;
//        print_r($retArr['formuladata']['nperall']);
        
        //时间总和'/'商品总需人数.取该商品最后购买时间前风站所有商品的最后100条购买计录
        $sumceil = floor($retArr['formuladata']['h_i_s_sum'] / $retArr['formuladata']['nperall']);
//        $sumceil = intval($retArr['formuladata']['h_i_s_sum'] / 4);
        $oldsumceil = $retArr['formuladata']['nperall'] * $sumceil;
//        $oldsumceil = 4 * $sumceil;
        $winning_code = abs($retArr['formuladata']['h_i_s_sum'] - $oldsumceil);

        if ( $winning_code == 0 || $winning_code == false ) $winning_code = 0;

        if ( $retArr['formuladata']['nperall'] == 1 ) $winning_code = 0;

        if ($retArr['formuladata']['nperall'] < $winning_code)
            throw new Exception('订单提交失败');
        $retArr['formuladata']['winning_code'] = $winning_code;
        $retArr['formuladata']['lucky_code']  =   10000001;
        
        //得出中奖码，算出这个中奖码所属哪个订单
//         $sql = "select * from ".YIFENZI.'.gw_yifenzi_order_goods where goods_id='.$goods_id." and current_nper=".$current_nper;
        
        $sql = "SELECT
        og.*,o.order_id as oorder_id
        FROM
        ".YIFENZI.".gw_yifenzi_order_goods AS og
        LEFT JOIN ".YIFENZI.".gw_yifenzi_order AS o ON og.order_id = o.order_id
        WHERE
        og.goods_id ={$goods_id}
        AND og.current_nper ={$current_nper} and o.order_status=1";
        
        $OrderGoodsData = Yii::app()->db->createCommand( $sql )->queryAll();
        
        //循环所有订单商品,再循环这个商品购买的商品幸运号是否与中奖匹配
        unset($k);
        unset($v);
        $winning_order_id = false; //中奖订单号
        $winning_goods_id = false; //中奖商品ID
        $winning_goods_name = false; //中奖商品名称
        
        $new_winning_code = ($retArr['formuladata']['winning_code'] * 1) + ($retArr['formuladata']['lucky_code'] * 1);
        foreach ($OrderGoodsData as $k=>$v){
//             file_put_contents('F:\wamp\www\sku\www/test.txt', $v);
            $bool = false;
            $winning_code_txt = json_decode($v['winning_code']);
            foreach ($winning_code_txt as $key=>$val){
                if ($val == $new_winning_code){
                    $bool = true;
                    $winning_order_id = $v['order_id'];
                    $winning_goods_id = $v['goods_id'];
                    $winning_goods_name = $v['goods_name'];
                    break;
                }
            }
            //退出循环
            if ($bool) continue;
        }
//         file_put_contents('F:\wamp\www\sku\www/test.txt', $sql);
        if (!$winning_order_id || !$winning_goods_id || !$winning_goods_name)
            throw new Exception('订单提交失败');
        
        //程序 跑到这里已经把当期商品的中奖订单ID得出
        $sql = "select * from ".YIFENZI.'.gw_yifenzi_order where order_id = '.$winning_order_id;
        $winningOrder = Yii::app()->db->createCommand($sql)->queryRow();
        
		//获取中奖人手机号码,盖网号
		$sqlMobile = "select mobile,gai_number from {{member}} where id = {$winningOrder['member_id']}";
		$winningMobile = Yii::app()->db->createCommand($sqlMobile)->queryRow();
        //准备insert到此期商品的中奖数据表中
        $data = array(
            'order_id'  =>  $winningOrder['order_id'],
            'goods_id'  =>  $winning_goods_id,
            'member_id'  =>  $winningOrder['member_id'],
            'goods_name'  => $winning_goods_name ,
            'winning_code'  =>  ($retArr['formuladata']['winning_code'] + $retArr['formuladata']['lucky_code']),
            'current_nper'  =>  $current_nper,
            'lotterytime'  =>  $GoodsData['announced_time'],
            'sumlotterytime'  =>  (time()+$GoodsData['announced_time']),
            'status'    =>1,//默认开奖中
            'order_id_log'  =>  json_encode($retArr),
			'gai_number' => $winningMobile['gai_number'],
			'mobile' => $winningMobile['mobile'],
        );
        
        $sql = "select * from ".YIFENZI.'.gw_yifenzi_order_goods_nper where goods_id='.$goods_id.' and order_id ='.$order_id.' and current_nper='.$current_nper;
        if (Yii::app()->db->createCommand($sql)->queryRow())
            throw new Exception('订单提交失败');
        
        if (!Yii::app()->db->createCommand()->insert(YIFENZI.'.gw_yifenzi_order_goods_nper', $data))
            throw new Exception('订单提交失败');
        
    }
    
    /**
     * 一份子毫秒获取
     * @return number
     */
    public static function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return sprintf("%.3f",((float)$usec + (float)$sec));
    }

    /**
     * 封装Excel方法
     * @param $str  页表信息
     * @param $headerArr 表头信息
     * @param $params 信息数组
     * @return boolean
     * @author yuanmei.chen
     */
    public static function cleanExcel($str = "列表数据",$headerArr,$params){
        ini_set("memory_limit","1024M");
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        Yii::import('comext.PHPExcel.*');


        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        $ExcelHeader = $objPHPExcel->setActiveSheetIndex(0);
        $char = range('A','Z');
        $excelCols = 2;
        //输出表头
        if(is_array($headerArr) && !empty($headerArr)){
            foreach(array_values($headerArr)  as $key => $val){
               $ExcelHeader->setCellValue($char[$key].'1',$val);
            }
        }else{
            return false;
        }

        if(is_array($params)){
            foreach($params as $key => $val){
                foreach(array_values($val) as $k=>$v){
                    $ExcelHeader->setCellValue($char[$k].$excelCols,' '.$v);
                }
                $excelCols++;
            }
        }else{
            return false;
        }
        $objPHPExcel->getActiveSheet()->setTitle($str);
        $name = date('YmdHis' . rand(0, 99999));
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
         return true;
    }

    
}
