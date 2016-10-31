<?php
/**
 * 获取ip地址位置信息
 * 根据二分法从ip纯真库查找
 *
 * phpwind 的代码修改而来
 * @author zhenjun_xu <412530435@qq.com>
 *
 * @param $fp
 * @param $firstip
 * @param $lastip
 * @return $totalip
 */
class IpTable
{

    private $_fp;
    private $_firstip;
    private $_lastip;
    private $_totalip;

    public function __construct($fileName = null)
    {
        $this->_fp = 0;
        $fileName = $fileName ? $fileName : dirname(__FILE__) . '/qqwry.dat';
        if (($this->_fp = fopen($fileName, 'rb')) !== false) {
            $this->_firstip = $this->getlong();
            $this->_lastip = $this->getlong();
            $this->_totalip = ($this->_lastip - $this->_firstip) / 7;
        }
    }

    /**
     * 根据所给 IP 地址或域名返回所在地区信息
     *
     * @param string $ip
     * @return string
     */
    public function getIpFromWry($ip)
    {
        $unknowIp = "Unknown";
        if (!$this->_fp) return $unknowIp;
        $ip = $this->packip($ip);
        // 二分法搜索索引区间
        $l = 0;
        $u = $this->_totalip;
        $findip = $this->_lastip;
        while ($l <= $u) {
            $i = floor(($l + $u) / 2);
            fseek($this->_fp, $this->_firstip + $i * 7);
            $beginip = strrev(fread($this->_fp, 4));
            if ($ip < $beginip) {
                $u = $i - 1;
            } else {
                fseek($this->_fp, $this->getlong3());
                $endip = strrev(fread($this->_fp, 4));
                if ($ip > $endip) {
                    $l = $i + 1;
                } else {
                    $findip = $this->_firstip + $i * 7;
                    break;
                }
            }
        }

        //获取查找到的IP地址
        fseek($this->_fp, $findip);
        $location['beginip'] = long2ip($this->getlong());
        $offset = $this->getlong3();
        fseek($this->_fp, $offset);
        $location['endip'] = long2ip($this->getlong());
        $byte = fread($this->_fp, 1);
        switch (ord($byte)) {
            case 1:
                $countryOffset = $this->getlong3();
                fseek($this->_fp, $countryOffset);
                $byte = fread($this->_fp, 1);
                switch (ord($byte)) {
                    case 2:
                        fseek($this->_fp, $this->getlong3());
                        $country = $this->getstring();
                        fseek($this->_fp, $countryOffset + 4);
                        $area = $this->getarea();
                        break;
                    default:
                        $country = $this->getstring($byte);
                        $area = $this->getarea();
                        break;
                }
                break;
            case 2:
                fseek($this->_fp, $this->getlong3());
                $country = $this->getstring();
                fseek($this->_fp, $offset + 8);
                $area = $this->getarea();
                break;
            default:
                $country = $this->getstring($byte);
                $area = $this->getarea();
                break;
        }
        if ($country == " CZ88.NET") {
            $country = $unknowIp;
        }
        if ($area == " CZ88.NET") {
            $area = "";
        }
        return mb_convert_encoding($country . $area, 'UTF-8', 'GBK');
    }


    /**
     * 返回读取的长整型数
     *
     * @return int
     */
    private function getlong()
    {
        $result = unpack('Vlong', fread($this->_fp, 4));
        return $result['long'];
    }

    /**
     * 返回读取的3个字节的长整型数
     *
     * @return int
     */
    private function getlong3()
    {
        $result = unpack('Vlong', fread($this->_fp, 3) . chr(0));
        return $result['long'];
    }

    /**
     * 返回压缩后可进行比较的IP地址
     *
     * @param string $ip
     * @return string
     */
    private function packip($ip)
    {
        return pack('N', intval(ip2long($ip)));
    }

    /**
     * 返回读取的字符串
     *
     * @param string $data
     * @return string
     */
    private function getstring($data = '')
    {
        $char = fread($this->_fp, 1);
        while (ord($char) > 0) {
            $data .= $char;
            $char = fread($this->_fp, 1);
        }
        return $data;
    }

    /**
     * 返回地区信息
     *
     * @return string
     */
    private function getarea()
    {
        $byte = fread($this->_fp, 1);
        switch (ord($byte)) {
            case 0:
                $area = '';
                break;
            case 1:
                fseek($this->_fp, $this->getlong3());
                $area = $this->getstring();
                break;
            case 2:
                fseek($this->_fp, $this->getlong3());
                $area = $this->getstring();
                break;
            default:
                $area = $this->getstring($byte);
                break;
        }
        return $area;
    }

    /**
     * 获取中国ip所在的位置信息
     * @param $ip
     * @return array
     */
    public function getPosition($ip)
    {
        //默认位置
        $position = array(
            'province_id'=>'22',
            'province_name'=>'广东',
            'city_id'=>'237',
            'city_name'=>'广州市',
        );
        $placeInfo = $this->getIpFromWry($ip);
        //城市
        foreach (Region::getCityShort() as $k=>$v) {
            if (stripos($placeInfo, $v) !== false) {
                $position = array(
                    'province_id'=>'',
                    'province_name'=>'',
                    'city_id'=>$k,
                    'city_name'=>$v,
                );
            }
        }
        //省份
        foreach(Region::getProvinceShort() as $k=>$v){
            if (stripos($placeInfo, $v) !== false) {
                $position['province_id'] = $k;
                $position['province_name'] = $v;
            }
        }
        return $position;
    }

}

