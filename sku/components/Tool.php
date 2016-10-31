<?php

/**
 * 工具类文件
 * 整理一些常用的函数，封装成工具类，方便调用
 * @author wanyun.liu <wanyun_liu@163.com>
 */

define('PI',3.1415926535898);
define('EARTH_RADIUS',6378.137);

class Tool {

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
     * 生成指定长度的随机字符串或md5后的唯一id
     * @param string $length
     * @param string $prefix
     * @return string
     */
    public static function generateSalt($length = '', $prefix = '') {
        $prefix = !$prefix ? mt_rand(0, 1000) : $prefix;
        $string = md5(uniqid($prefix));
        return $length ? substr($string, -$length) : $string;
    }

    /**
     * 规则的打印数组，并终止程序
     */
    public static function pr($param, $depth = 10) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<pre>';
        CVarDumper::dump($param, $depth, true);
        die;
    }

    /**
     * 规则的打印数组，不终止程序
     */
    public static function p($param, $depth = 10) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<pre>';
        CVarDumper::dump($param, $depth, true);
        echo '<hr />';
    }

    /**
     * utf8字符串截取
     * @param string $string 要截取的字符串
     * @param int $length 截取长度
     * @param string $etc 截取后多余显示字符
     * @return string 
     */
    public static function truncateUtf8String($string, $length, $etc = '...') {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen ) && ( $length > 0 )); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strlen) {
            $result .= $etc;
        }
        return $result;
    }

    /**
     * 友好显示var_dump
     * @param unknow $var 需要打印的变量
     */
    static public function dump($var, $echo = true, $label = null, $strict = true) {
        $label = ( $label === null ) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo $output;
            return null;
        }
        else
            return $output;
    }

    /**
     * 获取客户端IP地址,比$_SERVER['REMOTE_ADDR']要准确获得客户端的IP
     */
    static public function getClientIP() {
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
        $ip = Tool::getClientIP();
        return self::ip2int($ip);
    }

    /**
     * discuz 的可逆加密解密函数
     * @param string $string 明文 或 密文
     * @param string $operation DECODE 表示解密,其它表示加密
     * @param string $key 密匙
     * @param number $expiry 密文有效期
     * @return string
     */
    static public function authcode($string, $operation = 'ENCODE', $key = '', $expiry = 0) {

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
     * $_GET查询字符串生成,比较适用于分页场景中.params 参数
     * @param array $getArr $_Get
     * @param array $keys 
     * @return array 返回数组
     */
    public function buildCondition($getArr, $keys = array()) {
        if ($getArr) {
            foreach ($getArr as $k => $v) {
                if (in_array($k, $keys) && $v) {
                    $arr[$k] = $v;
                }
            }
            return $arr;
        }
    }

    /**
     * 用来生成列表选项的数据(值=>显示). 
     * @param string $models
     * @param string $valueField   
     * @param string $textField
     * @return array 返回数组
     * remark：这个方法会自动的将值和标签HTML编码
     */
    public static function getListData($models, $valueField, $textField) {
        $model = $models::model()->findAll();
        return CHtml::listData($model, $valueField, $textField);
    }

    /**
     * 发送邮件
     * @param string $address 收件人
     * @param string $subject 主题
     * @param string $message  正文
     * @param boolean $debug 是否开启调试，默认false
     * @return boolean
     */
    static public function sendEmail($address, $subject, $message, $id, $debug = FALSE) {
        $config = Tool::getConfig($name = 'email');
//        var_dump($config);exit;
//        $file = Yii::getPathOfAlias('common') . DS . 'webConfig' . DS . 'email.config.inc';
//        $config = unserialize(base64_decode(file_get_contents($file)));
        $mailer = Yii::createComponent('comext.mailer.EMailer');
        $mailer->Host = $config['host'];
        $mailer->IsSMTP();
        $mailer->Port = $config['port'];
        $mailer->From = $config['fromMail'];
        $mailer->AddReplyTo($config['fromMail']);
        $mailer->AddAddress($address);
        $mailer->FromName = $config['fromName'];
        $mailer->CharSet = 'UTF-8';
        $mailer->Subject = $subject;
        $mailer->Body = $message;
        $mailer->SMTPDebug = $debug;
        if ($config['identity']) {
            $mailer->SMTPAuth = true;
            $mailer->Username = $config['username'];
            $mailer->Password = $config['password'];
        }
        $result = array();
        echo 'id:',$id,"\n\r";
        if ($mailer->Send()) {
            $result['send_status'] = EmailLog::STATUS_SUCCESS;
            $result['create_time'] = time();
        } else {
            $result['send_status'] = EmailLog::STATUS_FAILD;
            $result['create_time'] = time();
        }
        return $result;
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
     * 格式化树数据
     * @param array $items
     * @param string $id    分类ID
     * @param string $pid   父类ID
     * @param string $son   自定义子类标识
     * @return array
     */
    public static function treeDataFormat($items, $id = 'id', $pid = 'parent_id', $son = 'children', $alternate = true) {
        $tree = array();
        $tmpMap = array();
        if ($alternate == true) {
            foreach ($items as $item)
                $tmpMap[$item[$id]] = $item;
        } else {
            $tmpMap = $items;
        }
        foreach ($items as $k => $item) {
            if (isset($tmpMap[$item[$pid]])) {
                $tmpMap[$item[$pid]][$son][] = &$tmpMap[$item[$id]];
            } else {
                $tree[] = &$tmpMap[$item[$id]];
            }
        }
        return $tree;
    }

    /**
     * 替换单词 - 目前经用在首页分类数据处理
     * @param int $id  
     * @return string
     */
    public static function shiftWork($id) {
        $works = array(1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty');
        return $works[$id];
    }

    /**
     * 分类面包屑重组
     * @param int $catid 分类Id
     * @param mixed $url CHtml::normalizeUrl()
     * @return array
     */
    public static function categoryBreadcrumb($catid, $url = array()) {
        if (!$bi = Tool::cache(Category::CACHEDIR)->get(Category::CK_CATEGORYINDEX))
            $bi = Category::categoryIndexing();
        $bradcrumb = array();

        if (isset($bi[$catid])) {
            $bData = $bi[$catid];
            switch ($bData['type']):
                case '1':
                    $bradcrumb = array(Yii::t('category', $bData['name']) => array_merge($url, array('id' => $bData['id'])));
                    break;
                case '2':
                    $bradcrumb = array(
                        Yii::t('category', $bData['parentName']) => array_merge($url, array('id' => $bData['parentId'])),
                        Yii::t('category', $bData['name']) => array_merge($url, array('id' => $bData['id']))
                    );
                    break;
                case '3':
                    $bradcrumb = array(
                        Yii::t('category', $bData['grandpaName']) => array_merge($url, array('id' => $bData['grandpaId'])),
                        Yii::t('category', $bData['parentName']) => array_merge($url, array('id' => $bData['parentId'])),
                        Yii::t('category', $bData['name']) => array_merge($url, array('id' => $bData['id']))
                    );
                    break;
            endswitch;
        }
        return $bradcrumb;
    }

    /**
     * 返回 星期几， 不传参数则是当天
     * @param null $k
     * @param string $words
     * @return string
     */
    public static function getWeek($k = null, $words = '星期') {
        $w = array('天', '一', '二', '三', '四', '五', '六');
        if (is_numeric($k)) {
            $week = isset($w[$k]) ? $w[$k] : $w[date('w')];
        } else {
            $week = $w[date('w')];
        }
        return $words . $week;
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
     * 生成基于URL的盖网通图片地址
     * @param string $url 图片相对地址	eg:/uploads/2014/08/18/f26f9c74b91402ed7b5c26f8163e26a2.jpg
     * @param string $params 其它参数
     * @return string  
     */
    public static function showGtImg($url, $height = 0, $width = 0) {
        if (UPLOAD_REMOTE_GT) {
            if ($height == 0 && $width == 0) {
                return GT_IMG_DOMAIN . str_replace("/uploads", "", $url);  //	http://gtimg.gwimg.com/2014/08/18/f26f9c74b91402ed7b5c26f8163e26a2.jpg
            }

            $info = pathinfo(GT_IMG_DOMAIN . '/thumb_cache' . str_replace("/uploads", "", $url));
            return $info['dirname'] . '/' . $info['filename'] . ',c_fill,h_' . $height . ',w_' . $width . '.' . $info['extension'];
        } else {
            if ($height == 0 && $width == 0) {
                return GT_IMG_DOMAIN . str_replace("/uploads", "", $url);
            } else {
                return GT_IMG_DOMAIN . "/" . $height . "x" . $width . str_replace("/uploads", "", $url);
            }
        }
    }

    /**
     * 后台搜索用的时间段格式化
     * @param string $startTime
     * @param string $endTime
     * @return array
     */
    public static function searchDateFormat($startTime, $endTime) {
        if ($startTime)
            $startTime = strtotime($startTime);

        if ($endTime) {
            if (count(explode(' ', $endTime)) != 2) { //如果没有时分秒，则用 23:59:59
                $endTime = substr($endTime, 0, 10) . ' 23:59:59';
            }
            $endTime = strtotime($endTime);
        }
        $date ['start'] = $startTime;
        $date ['end'] = $endTime;

        return $date;
    }

    /** 得到某个月的时间段
     * @param string $month '2013-01'
     * @return array
     */
    public static function getSearchMonth($month) {
        $month = strtotime(substr($month, 0, 7));
        $start_time = strtotime($month);
        $end_time = strtotime('1 month', $start_time);
        $date['start'] = $start_time;
        $date ['end'] = $end_time;
        return $date;
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
     * 处理已经定义好的请求参数
     * 替换请求值，如果该值没作请求保持默认值
     * @param array $params     规范请求参数数组
     * @return array
     */
    public static function requestParamsDispose($params) {
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                if (is_array($v)) {
                    $arr[$k] = 0;  // 排序默认值
                    $gp = Yii::app()->request->getParam($k, '');
                    $qv = self::magicQuotes($gp);
                    foreach ($v as $sk => $sv) {
                        $svKey = array_keys($sv);
                        if (in_array($qv, $svKey)) { // 如果请求参数合法，则取合法参数
                            $arr[$k] = $qv;
                            break;
                        }
                    }
                } else {
                    $gp = Yii::app()->request->getParam($k, $v);
                    $arr[$k] = self::magicQuotes($gp);
                }
            }
        }
        return $arr;
    }

    /**
     * 查找排序值
     * @param array $params     规范请求参数数组
     * @param integer $value    用户请求值
     * @return string
     */
    public static function findSortValue($params, $value) {
        $sort = '';
        if (is_array($params) && is_numeric($value) && isset($params['order'])) {
            $order = $params['order'];
            foreach ($order as $k => $v) {
                if (array_key_exists($value, $v))
                    $sort = $v[$value];
            }
        }
        return $sort;
    }

    /**
     * ip转换为整型
     * @param string $ip
     * @return int
     */
    public static function ip2int($ip) {
        return bindec(decbin(ip2long($ip)));
    }

    /**
     * 整型转换为ip
     * @param int $number
     * @return string
     */
    public static function int2ip($number) {
        return long2ip($number);
    }

    /**
     * ip转换为整型
     * @param string $ip
     * @return int
     */
    public static function ip2number($ip) {
        $ary = explode('.', $ip);
        $num = 0;
        for ($i = 0; $i < 4; $i++) {
            $num = $num * 256 + $ary[$i];
        }
        return $num;
    }

    /**
     * 整型转换为ip
     * @param int $num
     * @return string
     */
    public static function number2ip($num) {
        $ary = array();
        for ($i = 0; $i < 4; $i++) {
            $x = $num % 256;
            if ($x < 0)
                $x+=256;
            array_unshift($ary, $x);
            $num = intval($num / 256);
        }
        return implode('.', $ary);
    }

    /**
     * 获取盖网后台网站配置的在线QQ
     * @return array
     */
    public static function getBackendQQ($qqStr) {
        $qq = array();
        $arr = explode(',', $qqStr);
        foreach ($arr as $k => $v) {
            $qqInfo = explode(':', $v);
            if (isset($qqInfo[1])) {
                if (!is_numeric($qqInfo[1]))
                    continue;
                $qq[$k]['qq'] = $qqInfo[1];
                $qq[$k]['text'] = isset($qqInfo[0]) ? $qqInfo[0] : '';
            }
        }
        return $qq;
    }

    /**
     * 配置文件查询缓存
     * @var array
     */
    public static $config = array();

    /**
     * 获取配置文件
     * @param string $name
     * @param string|null $key
     * @return string
     */
    public static function getConfig($name, $key = null) {
        $name = strtolower($name);
        $val = Tool::cache($name . 'config')->get($name);
        if ($val) {
            $array = unserialize($val);
        } else {

            $value = WebConfig::model()->findByAttributes(array('name' => $name));
            if ($value) {
                Tool::cache($name . 'config')->add($name, $value->value);
                $array = unserialize($value->value);
            } else {
                if (isset(self::$config[$name])) {
                    $array = self::$config[$name];
                } else {
                    //获取配置文件
                    $file = Yii::getPathOfAlias('common') . DS . 'webConfig' . DS . $name . '.config.inc';
                    if (!file_exists($file)) {
                        $file = Yii::getPathOfAlias('common') . DS . 'webConfig' . DS . strtolower($name) . '.config.inc';
                    } else if (!file_exists($file)) {
                        return array();
                    }
                    $content = file_get_contents($file);
                    $array = unserialize(base64_decode($content));
                    self::$config[$name] = $array;
                }
            }
        }

        return $key ? (isset($array[$key]) ? $array[$key] : '') : $array;
    }

    /**
     * 获取盖网配置文件
     * @param string $name
     * @param string|null $key
     * @return string
     */
    public static function getConfigGW($name, $key = null) {
        $name = strtolower($name);
        $val = Tool::cache($name . 'config')->get($name);
        if ($val) {
            $array = unserialize($val);
        } else {
            $value = Yii::app()->gw->createCommand()
                ->select()
                ->from(WebConfig::model()->tableName())
                ->where('name = :name' , array(':name' => $name))
                ->queryRow();
            if ($value) {
                Tool::cache($name . 'config')->add($name, $value['value']);
                $array = unserialize($value['value']);
            } else {
                if (isset(self::$config[$name])) {
                    $array = self::$config[$name];
                } else {
                    //获取配置文件
                    $file = Yii::getPathOfAlias('common') . DS . 'webConfig' . DS . $name . '.config.inc';
                    if (!file_exists($file)) {
                        $file = Yii::getPathOfAlias('common') . DS . 'webConfig' . DS . strtolower($name) . '.config.inc';
                    } else if (!file_exists($file)) {
                        return array();
                    }
                    $content = file_get_contents($file);
                    $array = unserialize(base64_decode($content));
                    self::$config[$name] = $array;
                }
            }
        }

        return $key ? (isset($array[$key]) ? $array[$key] : '') : $array;
    }

    /**
     * 显示金额，千分位为,
     * @author LC
     */
    public static function showMoney($money) {
        return number_format($money, 2, '.', ',');
    }

    /**
     * 获取访客所在的ip地理城市信息
     * @return array|string
     * array(
      'province_id'=>'22',
      'province_name'=>'广东',
      'city_id'=>'237',
      'city_name'=>'广州市',
      );
     */
    public static function getPosition() {
        $cookie = Yii::app()->request->cookies['position'];
        if ($cookie && !empty($cookie->value)) {
            $position = $cookie->value;
        } else {
            Yii::import('application.vendor.ip.IpTable');
            $ipTable = new IpTable(); //纯真ip库
            //测试 青岛：202.102.134.68, 广州：14.23.157.170
            //$position = $ipTable->getPosition('202.102.134.68');
            $position = $ipTable->getPosition($_SERVER['REMOTE_ADDR']);
            $cookie = new CHttpCookie('position', $position);
            $cookie->expire = time() + 3600 * 24 * 360;
            Yii::app()->request->cookies['position'] = $cookie;
        }
        return $position;
    }
    /*
     * 获取用户ip所在地址
     * */
    public static function GetIpLookup($member_id){
            $ip_address = Yii::app()->gwpart->createCommand()
                ->select("ip_address")
                ->from('{{member_ip}}')
                ->where("member_id = ".$member_id)
                ->queryScalar();
            $ip = !empty($ip_address)?$ip_address:$_SERVER['REMOTE_ADDR'];
        $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
        if(empty($res)){ return false; }
        $jsonMatches = array();
        preg_match('#\{.+?\}#', $res, $jsonMatches);
        if(!isset($jsonMatches[0])){ return false; }
        $json = json_decode($jsonMatches[0], true);
        if(isset($json['ret']) && $json['ret'] == 1){
            $json['ip'] = $ip;
            unset($json['ret']);
        }else{
            return false;
        }
        return $json;
    }

    /**
     * 自定义根据键名与值 在二维数组中查找
     * @param $array
     * @param $key
     * @param $value
     * @return bool|array
     */
    public static function array_2get($array, $key, $value) {
        foreach ($array as $v) {
            if (isset($v[$key]) && $v[$key] == $value)
                return $v;
        }
        return false;
    }

    /**
     * 关键词过滤判断
     * @param unknown $string
     * @param unknown $fileName
     * @return boolean
     */
    public static function banwordCheck($string) {
        $words = Tool::getConfig('filterworld');
        $words = $words['filterWorld'];
        $words = str_replace(',', '|', $words);
//     	$string = strtolower($string);
        $matched = preg_match('/' . $words . '/i', $string, $result);
        if ($matched && isset($result[0]) && strlen($result[0]) > 0) {
            if (strlen($result[0]) == 2) {
                $matched = preg_match('/' . $words . '/iu', $string, $result);
            }
            if ($matched && isset($result[0]) && strlen($result[0]) > 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * 关键词过滤  替换
     * @param unknown $string
     * @param unknown $fileName
     * @return boolean
     */
    public static function banwordReplace($string, $replace = '*') {
        $words = Tool::getConfig('filterworld');
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
     * 转义数据
     * @param string|array $var
     * @return string|array
     */
    public static function magicQuotes(&$var) {
        if (!get_magic_quotes_gpc()) {
            if (is_array($var)) {
                foreach ($var as $k => $v)
                    $var[$k] = self::magicQuotes($v);
            }
            else
                $var = addslashes($var);
        }
        return $var;
    }

    /**
     * 过滤post产生的二维数组中的值
     * @param $postData
     * @param string $needString 逗号 分割
     * @param bool $exclude 排除模式
     * @return array
     */
    public static function filterPost(Array $postData, $needString, $exclude = false) {
        $needArr = explode(',', $needString);
        if ($exclude) {
            foreach ($postData as $k => $v) {
                if (in_array($k, $needArr)) {
                    unset($postData[$k]);
                }
            }
            return $postData;
        } else {
            $tmpArr = array();
            foreach ($postData as $k => $v) {
                if (in_array($k, $needArr)) {
                    $tmpArr[$k] = $v;
                }
            }
            return $tmpArr;
        }
    }

    /**
     * 比較兩個浮點數是否相等
     * @param type $f1
     * @param type $f2
     * @param type $precision
     * @return type
     */
    public static function floatcmp($f1, $f2, $precision = 10) {
        $e = pow(10, $precision);
        $i1 = intval($f1 * $e);
        $i2 = intval($f2 * $e);
        return ($i1 == $i2);
    }

    /**
     * 货币转换
     * @param $money 要转换货币总数
     * @param $currency 要转币种
     * @param $rate 汇率
     */
    public static function currency($money, $currency = "HKD") {
        if (!is_numeric($money))
            return $money;
        $rate = Tool::getConfig('rate', 'hkRate');
        if ($currency == "HKD") {
            $rate = 100 / $rate;
        }
        if ($currency == "RMB") {
            $rate = $rate / 100;
        }
        return sprintf("%.2f", $money * $rate);
        ;
    }

    /**
     * 安全过滤类-过滤javascript,css,iframes,object等不安全参数 过滤级别高
     *  Controller中使用方法：$this->controller->filter_script($value)
     * @param  string $value 需要过滤的值
     * @return string
     */
    public static function filter_script($value) {
        $value = preg_replace("/(javascript:)?on(click|load|key|mouse|error|abort|move|unload|change|dblclick|move|reset|resize|submit)/i", "&111n\\2", $value);
        $value = preg_replace("/<script(.*?)>(.*?)<\/script>/si", "", $value);
        $value = preg_replace("/<iframe(.*?)>(.*?)<\/iframe>/si", "", $value);
        $value = preg_replace("/<object.+<\/object>/isU", '', $value);
        return $value;
    }

    /**
     * fsockopen 执行POST提交，不等待返回结果，暂时用于处理发送短信
     * @param string $url
     * @param array  $params
     * @return bool
     */
    public static function asyncPost($url, $params) {
        $url_array = parse_url($url); //获取URL信息，以便平凑HTTP HEADER
        $port = isset($url_array['port']) ? $url_array['port'] : 80;
        $fp = fsockopen($url_array['host'], $port, $errno, $errstr, 30);
        if (!$fp)
            return false;
        $path = isset($url_array['path']) ? $url_array['path'] : '';

        $RSA = new RSA();
        $datas = "";
        foreach ($params as $key => $row) {  //对传递过来的数据进行加密
            $datas.= $key . "=" . $RSA->encrypt($row);
        }
        $datas.= $datas == "" ? "" : "create_time=" . $RSA->encrypt(time());   //多传递一个时间戳，用于验证是否重复

        $out = "POST " . $path . " HTTP/1.1\r\n";       //拼接访问方式和访问地址
        $out .= "Content-type: application/x-www-form-urlencoded\r\n"; //这个是设定POST方式
//        $out .= "User-Agent: MSIE\r\n";
        $out .= "Host: " . $url_array['host'] . "\r\n";      //拼接访问域名
        $out .= "Content-length: " . strlen($datas) . "\r\n";    //设定POST数据长度
        $out .= "Connection: Close\r\n\r\n";       //连接关闭
        $out .= $datas . "\r\n\r\n";          //拼接传递数据

        fwrite($fp, $out);
        /*
          echo $out."\r\n";
          //不等待
          while (!feof($fp)) {
          echo fgets($fp, 128);
          }
         */
        fclose($fp);
    }

    /**
     * curl 并发处理
     * @param $urls 网址数组
     * @param $timeout 超时时间，秒
     * @return array/boolean
     */
    public static function concurrentCurl(Array $urls, $timeout = 5) {
        if (empty($urls))
            return false;
        $queue = curl_multi_init();
        $map = array();
        foreach ($urls as $url) {
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
        do {
            while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM);

            if ($code != CURLM_OK) {
                break;
            }

            // 找出一个刚好完成的请求
            while ($done = curl_multi_info_read($queue)) {

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
        } while ($active);

        curl_multi_close($queue);
        //获取的$responses是乱序的
        //按照$urls 里的网址重新排序，把结果给$urls
        foreach ($urls as $key => $value) {
            $urls[$key] = $responses[$value];
        }
        return $urls;
    }

    /**
     * 获取省/市/区名称(by lxy)
     * @param int $areaId 区域ID
     * @return string 名称
     */
    public static function getAreaName($areaId) {
        $areaName = Region::model()->findByPk($areaId, array('select' => 'name'))->name;
        return $areaName;
    }

    /**
     * 低强度加假解密
     * @param $data
     * @param string $type 默认为encrypt ，为加密，其它为解密
     * @param null $key 密钥，最长为8个字符
     * @return mixed
     */
    public static function lowEncrypt($data, $type = 'encrypt', $key = null) {
        $key = $key ? $key : 'aw34d.df';
        if ($type == 'encrypt') {
            $prep_code = serialize($data);
            $block = mcrypt_get_block_size('des', 'ecb');
            if (($pad = $block - (strlen($prep_code) % $block)) < $block) {
                $prep_code .= str_repeat(chr($pad), $pad);
            }
            $encrypt = mcrypt_encrypt(MCRYPT_DES, $key, $prep_code, MCRYPT_MODE_ECB);
            return base64_encode($encrypt);
        } else {
            $str = base64_decode($data);
            $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
            $block = mcrypt_get_block_size('des', 'ecb');
            $pad = ord($str[($len = strlen($str)) - 1]);
            if ($pad && $pad < $block && preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str)) {
                $str = substr($str, 0, strlen($str) - $pad);
            }
            return @unserialize($str);
        }
    }

    /**
     * 判断是否是移动设备
     * @return bool
     */
    public static function isMobileDevice() {

        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        if (isset($_SERVER['HTTP_VIA'])) {
            //找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            //此数组有待完善
            $clientkeywords = array(
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        //协议法，因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获得图片的MIME类型
     * @param $path 图片路径
     * @return mixed
     * @author binbin.liao
     */
    public static function getImageMime($path) {
        $data = getimagesize($path);
        return $data['mime'];
    }

    /**
     * 删除www目录下的对应的文件，只能用于页面静态化,不能有其他作用
     * @param unknown $name www目录下的文件夹或者文件名 例如index.html 或者  test
     * @param unknown $name
     * @return boolean
     */
    public static function deleteWebwww($name) {
        if ($name != '') {
            $cmd = "sh /shell/rm.sh ";
            if (strpos($name, "..") === false) {
                $cmd .= $name;
                exec($cmd);
                return true;
            }
        }
        return false;
    }

    /**
     * 模拟post
     * @param unknown $url
     * @param unknown $post_data
     */
    public static function post($url,$post_data){
    	set_time_limit(300);
    	$o="";
    	foreach ($post_data as $k=>$v)
    	{
     		$o.= "$k=".urlencode($v)."&";
//    		$o.= "$k=".$v."&";
    	}
    	$post_data=substr($o,0,-1);     
    	$result = '';
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_URL,$url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	//为了支持cookie
    	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    	
    	curl_setopt($ch, CURLOPT_TIMEOUT,300);
    	
    	$result = curl_exec($ch);  
    	
	    curl_close($ch);  
	    return $result;  
         
    }
    
    /**
     * 通过curl模拟file_get_contents的方法，效率比较高--用于get提交获取参数
     * @param unknown $url
     * @return mixed
     * @author LC
     */
    public static function curl_file_get_contents($url){
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 60); //设置超时
    	curl_setopt($ch, CURLOPT_HEADER, 0); //这里不要header，加块效率
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //是否抓取跳转后的页面
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    	$contents = curl_exec($ch);
    	curl_close($ch);
    	return $contents;
    }
    
    
    /**
     * 根据坐标取范围
     * 
     * 丫的， 不知道谁写出来的方法，一点也不准。
     * 
     * @param unknown $lng
     * @param unknown $lat
     * @param real $distance  距离
     * @return multitype:multitype:number
     */
    static function vicinity($lng, $lat, $distance = 500) {
    	$distance = $distance/10000;      // 单位 10KM
    	$radius = 6370.6935;
    	 
    	$dlng = rad2deg(2*asin(sin($distance/(2*$radius))/cos($lat)));
    	$dlat = rad2deg($distance*10/$radius);
    	 
    	$lng_left = round($lng - $dlng, 6);
    	$lng_right = round($lng + $dlng, 6);
    	$lat_top = round($lat + $dlat, 6);
    	$lat_bottom = round($lat - $dlat, 6);
    	 
    	return array('lng'=> array('left'=> $lng_left, 'right'=> $lng_right), 'lat'=> array('top'=> $lat_top, 'bottom'=> $lat_bottom));
    }
    
   
    //计算范围，可以做搜索用户
    static function GetRange($lat,$lng,$raidus=500){
    	//计算纬度
    	$degree = (24901 * 1609) / 360.0;
    	$dpmLat = 1 / $degree;
    	$radiusLat = $dpmLat * $raidus;
    	$minLat = $lat - $radiusLat; //得到最小纬度
    	$maxLat = $lat + $radiusLat; //得到最大纬度
    	//计算经度
    	$mpdLng = $degree * cos($lat * (PI / 180));
    	$dpmLng = 1 / $mpdLng;
    	$radiusLng = $dpmLng * $raidus;
    	$minLng = $lng - $radiusLng;  //得到最小经度
    	$maxLng = $lng + $radiusLng;  //得到最大经度
    	//范围
    	$range = array(
    			'minLat' => sprintf("%.6f", $minLat),
    			'maxLat' =>  sprintf("%.6f", $maxLat),
    			'minLng' => sprintf("%.6f", $minLng) ,
    			'maxLng' => sprintf("%.6f", $maxLng) 
    	);
    	return $range;
    }
    //获取2点之间的距离
    public static  function GetDistance($lat1, $lng1, $lat2, $lng2){
    	$radLat1 = $lat1 * (PI / 180);
    	$radLat2 = $lat2 * (PI / 180);
    	 
    	$a = $radLat1 - $radLat2;
    	$b = ($lng1 * (PI / 180)) - ($lng2 * (PI / 180));
    	 
    	$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
    	$s = $s * EARTH_RADIUS;
    	$s = round($s * 10000) / 10000;
    	return $s*1000;
    }

    /**
     * 运行成功返回json
     * @param mixed $data
     */
    public static function success($data)
    {
        header("Content-type:text/html;charset=utf-8");
        $response = array(
            'status' => 200,
            'msg' => 'success',
            'data' => $data,
        );
        exit(json_encode($response));
    }

    /**
     * 运行错误返回json
     * @param $status int 错误代码
     * @param $msg string 错误信息
     */
    public static function error($status, $msg = null)
    {
        header("Content-type:text/html;charset=utf-8");
        $response = array(
            'status' => $status,
        );
        if($msg!==null) $response['msg'] = $msg;
        exit(json_encode($response));
    }

    /**
     * app 端图片提交需做处理才能用模型方法上传图片
     */
    public static function appUploadPic($model){
        $newFiles = array();
        $model_name = get_class($model);
        if(isset($_FILES)){
            foreach($_FILES as $k =>$v){
                $newFiles[$model_name]['name'][$k]=$v['name'];
                $newFiles[$model_name]['type'][$k]=$v['type'];
                $newFiles[$model_name]['tmp_name'][$k]=$v['tmp_name'];
                $newFiles[$model_name]['error'][$k]=$v['error'];
                $newFiles[$model_name]['size'][$k]=$v['size'];
            }
        }
        return $_FILES = $newFiles;

    }

    /**
     * 获取游戏配置信息
     * @param $name
     * @param int $type
     * @return array|mixed
     */
    public static function getConfigData($name,$type = GameMemberInfo::GAME_TYPE_SANGUORUN){
        $redis = Yii::app()->redis;
        if ($redis->exists($name . 'config')) {
            $list = $redis->get($name . 'config');
        } else {
            $gameConfig = GameConfig::model()->find(array(
                'condition' => 'config_name = :config_name and app_type = :app_type',
                'select' => array('value'),
                'params' => array(':config_name' => $name, ':app_type' => $type)
            ));
            if(!empty($gameConfig)){
                $list = htmlspecialchars_decode(strip_tags(str_replace('&nbsp;','',$gameConfig['value'])));
                $redis->set($name . 'config', $list, 86400);
            }else{
                $path = Yii::app()->getModule('game')->BasePath;
                $fileName = $path . DS . 'config' . DS . $name . '.json';
                if (!file_exists($fileName)) {
                    return array();
                }
                $list = file_get_contents($fileName);
                $array  = array(
                    'app_type' => $type,
                    'config_name' => $name,
                    'value' => $list
                );
                GameConfig::insertConfig($array);//将配置表内容写入数据库
                $redis->set($name . 'config', $list, 86400);
            }
        }
        $data = json_decode($list, true);
        return $data;
    }
    
    /**
     * 重设图片大小 压缩图片
     * @param unknown $path
     */
    public static function resize_pic($path,$width=500,$height=500){
    	Yii::import('application.extensions.image.*');
    	Yii::import('application.extensions.image.helpers.*');
    	
    	if (!file_exists($path)) {
    		return false;
    	}
    	
    	@$image = new Image($path);
    	
    	//尺寸小的不调整
    	if ($image->image['width']<$width && $image->image['height']<$height) {
    		return true;
    	}
    	
    	@$image->quality(90)->resize($width, $height);
    	return @$image->save($path);
    }
    
}
