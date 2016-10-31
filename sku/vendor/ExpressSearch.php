<?php

/**
 * 快递查询类
 * @author: leo8705
 * 
 * 已作缓存处理
 * 
 * 用法：  
 * 
 * $exp = new ExpressSearch(Yii::app()->params['ExpressApiKey'],Yii::app()->params['ExpressApiHost']);
  $rs = $exp->search($kd_company_code,$kd_code);
 * 
 * 
 * 参数及返回请参考    http://www.kuaidi100.com/openapi/api_2_02.shtml
 * 
 */
class ExpressSearch {

    const CACHEDIRNAME = 'express';        //缓存目录名

    public $cacheTime = 7200;         //结果缓存时间
    public $AppKey;            //身份授权key
    public $referer;
    public $com;            //要查询的快递公司代码，
    public $nu;             //要查询的快递单号
    public $show = 0;           //返回类型： 0：返回json字符串， 1：返回xml对象， 2：返回html对象， 3：返回text文本。
    public $muti = 1;           //返回信息数量： 1:返回多行完整的信息， 0:只返回一行信息。
    public $order = 'asc';          //排序： desc：按时间由新到旧排列， asc：按时间由旧到新排列。 
    public $AppBaseUrl = 'http://api.kuaidi100.com/api';
    public $companys = array(
        'aae' => 'aae全球专递',
        'anjie' => '安捷快递',
        'anxindakuaixi' => '安信达快递',
        'biaojikuaidi' => '彪记快递',
        'bht' => 'bht',
        'baifudongfang' => '百福东方国际物流',
        'coe' => '中国东方（COE）',
        'changyuwuliu' => '长宇物流',
        'datianwuliu' => '大田物流',
        'debangwuliu' => '德邦物流',
        'dhl' => 'dhl',
        'dpex' => 'dpex',
        'dsukuaidi' => 'd速快递',
        'disifang' => '递四方',
        'ems' => 'ems快递',
        'fedex' => 'fedex（国外）',
        'feikangda' => '飞康达物流',
        'fenghuangkuaidi' => '凤凰快递',
        'feikuaida' => '飞快达',
        'guotongkuaidi' => '上海国通快递',
        'ganzhongnengda' => '港中能达',
        'guangdongyouzhengwuliu' => '广东邮政物流',
        'gongsuda' => '共速达',
        'huitongkuaidi' => '汇通快运',
        'hengluwuliu' => '恒路物流',
        'huaxialongwuliu' => '华夏龙物流',
        'haihongwangsong' => '海红',
        'haiwaihuanqiu' => '海外环球',
        'jiayiwuliu' => '佳怡物流',
        'jinguangsudikuaijian' => '京广速递',
        'jixianda' => '急先达',
        'jjwl' => '佳吉物流',
        'jymwl' => '加运美物流',
        'jindawuliu' => '金大物流',
        'jialidatong' => '嘉里大通',
        'jykd' => '晋越快递',
        'kuaijiesudi' => '快捷快递',
        'lianb' => '联邦快递（国内）',
        'lianhaowuliu' => '联昊通物流',
        'longbanwuliu' => '龙邦物流',
        'lijisong' => '立即送',
        'lejiedi' => '乐捷递',
        'minghangkuaidi' => '民航快递',
        'meiguokuaidi' => '美国快递',
        'menduimen' => '门对门',
        'ocs' => 'OCS',
        'peisihuoyunkuaidi' => '配思货运',
        'quanchenkuaidi' => '全晨快递',
        'quanfengkuaidi' => '全峰快递',
        'quanjitong' => '全际通物流',
        'quanritongkuaidi' => '全日通快递',
        'quanyikuaidi' => '全一快递',
        'rufengda' => '如风达',
        'santaisudi' => '三态速递',
        'shenghuiwuliu' => '盛辉物流',
        'shentong' => '申通',
    	'shunfeng' => '顺丰速递',
        'suer' => '速尔快递',
        'shengfeng' => '盛丰物流',
        'saiaodi' => '赛澳递',
        'tiandihuayu' => '天地华宇',
        'tiantian' => '天天快递',
        'tnt' => 'tnt',
        'ups' => 'ups',
        'wanjiawuliu' => '万家物流',
        'wenjiesudi' => '文捷航空速递',
        'wuyuan' => '伍圆',
        'wxwl' => '万象物流',
        'xinbangwuliu' => '新邦物流',
        'xinfengwuliu' => '信丰物流',
        'yafengsudi' => '亚风速递',
        'yibangwuliu' => '一邦速递',
        'youshuwuliu' => '优速快递',
        'youzhengguonei' => '邮政包裹挂号信',
        'youzhengguoji' => '邮政国际包裹挂号信',
        'yuanchengwuliu' => '远成物流',
        'yuantong' => '圆通速递',
        'yuanweifeng' => '源伟丰快递',
        'yuanzhijiecheng' => '元智捷诚快递',
        'yunda' => '韵达快运',
        'yuntongkuaidi' => '运通快递',
        'yuefengwuliu' => '越丰物流',
        'yad' => '源安达',
        'yinjiesudi' => '银捷速递',
        'zhaijisong' => '宅急送',
        'zhongtiekuaiyun' => '中铁快运',
        'zhongtong' => '中通速递',
        'zhongyouwuliu' => '中邮物流',
        'zhongxinda' => '忠信达',
        'zhimakaimen' => '芝麻开门',
        'longbanwuliu'=>'上海龙邦快递',
        'huiqiangkuaidi'=>'汇强快递',
        'jiayunmeiwuliu'=>'加运美速递',
        'yafengsudi'=>'亚风快递',
    );

    const StatusNoResult = 0;
    const StatusSuccess = 1;
    const StatusError = 2;

    function __construct($AppKey = '', $referer = '') {
        $this->AppKey = $AppKey;
        $this->referer = $referer;
    }

    /**
     * 取接口返回状态
     * Enter description here ...
     */
    public static function getStatus($status = '') {
        $status_arr = array(
            self::StatusNoResult => '物流单暂无结果',
            self::StatusSuccess => '查询成功',
            self::StatusError => '接口出现异常',
        );
        if (!empty($status) && !empty($status_arr[$status]))
            return $status_arr[$status];
        else
            return $status_arr;
    }

    /**
     * 取快递状态
     * Enter description here ...
     * @param unknown_type $com
     * @param unknown_type $nu
     * 
     * 参数及返回请参考    http://www.kuaidi100.com/openapi/api_2_02.shtml
     * 
     * 
     */
    public function search($com = '', $nu = '',$code='') {
        if (empty($com))
            $com = $this->com;
        if (empty($nu))
            $nu = $this->nu;
            
            
//       if (!in_array($com, $this->companys)){
//       		echo json_encode(array('status'=>self::StatusError,'message'=>'快递公司不存在！'));
//       		exit();
//       }
            
        //缓存处理
        $cache_key = "{$com}_{$nu}";

        $get_content = Tool::cache(self::CACHEDIRNAME)->get($cache_key);

        if (!$get_content) {
//            $url = $this->AppBaseUrl . '?id=' . $this->AppKey . '&com=' . $com . '&nu=' . $nu . '&show=' . $this->show . '&muti=' . $this->muti . '&order=' . $this->order;
            /**
             * 不使用api
             */
            $tempCode = self::getCompanyCodeByName($com);
            if(!empty($tempCode)) $code = $tempCode;
            $url = 'http://www.kuaidi100.com/query?type='.$code.'&postid='.$nu.'&id=1&valicode=&temp='.mt_rand();
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            $ip = $_SERVER['REMOTE_ADDR'];
            curl_setopt($curl, CURLOPT_REFERER, "http://www.kuaidi100.com/");   //构造来路
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.$ip, 'CLIENT-IP:'.$ip));  //构造IP

            $get_content = curl_exec($curl);
            curl_close($curl);
            $rs = json_decode($get_content);
//            Tool::pr($rs);
            if (isset($rs->status) && $rs->status==self::StatusError) $this->cacheTime = 300;			//如果结果返回异常，则缓存60秒
            
            Tool::cache(self::CACHEDIRNAME)->set($cache_key, $get_content, $this->cacheTime);
        }

        return $get_content;
    }

    /**
     * 取快递公司
     * Enter description here ...
     */
    public static function getCompanys($company_code = '') {
        $es = new ExpressSearch('');
        $companys = $es->companys;
        if (!empty($company_code) && !empty($companys[$company_code]))
            return $companys[$company_code];
        else
            return $companys;
    }

    /**
     * 根据快递公司中文名取快递公司代码
     * Enter description here ...
     */
    public static function getCompanyCodeByName($company_name = '') {
        $es = new ExpressSearch('');
        $companys = array_flip($es->companys);
        if (!empty($company_name) && !empty($companys[$company_name]))
            return $companys[$company_name];
        else
            return '';
    }

}