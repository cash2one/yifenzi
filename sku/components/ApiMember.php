<?php

/**
 * 用户中心api使用类
 * 
 * @author leo8705
 */
class ApiMember {
    
	
	const CODE_SUCCESS = 200;
	
	public $signkey = '';
	public $apiUrl = '';
    public $smsUrl='';
	const  GW_SEND_SMS = 0; //商城
    const   GT_SEND_SMS = 1; //盖网通
    const   HOTEL_SEND_SMS = 2; //酒店
    const   SKU_SEND_SMS = 3; //sku
	const DRUGSTORE_SEND_SMS = 4;   //盖象大药房
	const WUYE_SEND_SMS = 5;    //物业管理
	const YOUXI_SEND_SMS = 6;  //游戏
	const SMS_TYPE_ONLINE_ORDER = 1; // 线上订单
	const SMS_TYPE_OFFLINE_ORDER = 2; // 线下订单
	const SMS_TYPE_CARD_RECHARGE = 3; // 卡充值
	const SMS_TYPE_HOTEL_ORDER = 4; // 酒店订单
	const SMS_TYPE_CAPTCHA = 5; // 验证码
	const SMS_TYPE_OTHER = 6;   //其他
	const SMS_TYPE_POS_RECHARGE = 7;	//盖网通POS充值
	const SMS_TYPE_VENDING_COMPLEMENT = 8; //售货机补货
	const SMS_TYPE_VENDING_RETURN = 9; //售货机退货
	const SMS_TYPE_TEST = 10;	// 测试短信
	/* 短信通道(大陆) */
	const SMS_INTERFACE_DXT = 1; // 短信通
	const SMS_INTERFACE_YX = 2; // 易信
	const SMS_INTERFACE_JXT = 3;	//吉信通
	const SMS_INTERFACE_JXT_ADVERT = 4;	//吉信通(广告)
	
	const SMS_INTERFACE_YTX = 101; // 香港易通信
	
	/* 状态 */
	const SMS_STATUS_SUCCESS = 1; // 发送成功
	const SMS_STATUS_FAILD = 2; // 发送失败
	
	
	const MEMBER_OFFICAL=2;//正式会员
    const MEMBER_EXPENSE=1;//消费会员
	
	public $source = ApiMember::SKU_SEND_SMS;				//分配的来源
	
         //容联云通讯模板id
    const CAPTCHA = 52755; //验证码
const CANCLE_ORDER_SUCCESS = 66075; //取消微小企订单成功
const APPLY_SUCCESS = 66078;//账号申请成功
const SET_PASS = 66079;//微小企重置密码
const MACHINE_FAIL = 66080; //贩卖机出货失败
const MACHINE_RETURN_MONEY =66081; //售货机库存不足退款
const SEND_SUCCESS = 66082; //微小企订单发货
const CANCLE_ORDER_POINT_SUCCESS = 66083; //微小企取消订单（积分）
const CANCLE_ORDER_POINT_FAIL = 66084; //微小企取消订单失败（积分）
const CANCLE_EXCESS_ORDER_SUCCESS = 66086; //微小企取消订单超额
const CANCLE_EXCESS_ORDER_FAIL = 66087;//微小企取消订单失败
const CANCLE_ABNORMAL_GOODS_ORDER = 66088; //订单商品异常取消订单
const CANCLE_ABNORMAL_GOODS_ORDER_FAIL = 66089; //订单商品异常取消订单失败
const PAY_ORDER_SUCCESS = 66090; //支付订单成功短信
const STOCK_UP_CANCLE_ORDER_FAIL = 66091;//备货失败取消订单失败
const STOCK_UP_CANCLE_ORDER = 66092;//备货失败取消订单成功
const OVER_TIME_CANCLE_ORDER_FAIL = 66093;//超时取消订单失败
const OVER_TIME_CANCLE_ORDER = 66094 ;//超时取消订单成
const JMS_APPLY = 76653; //加盟商审核通过
const JMS_APPLY_CHU = 76654; //加盟商审核通过初始密码
const JMS_APPLY_NO = 76655; //加盟商审核不通过
	
	public function __construct(){
		$this->apiUrl = MEMBER_API_URL;
		$this->signkey = MEMBER_API_SIGN_KEY;
        $this->smsUrl = ORDER_ORDER_API_URL;
	}
	
	/**
	 * 生成加密串
	 *
	 * 检验规则是data参数值连成json字符串，加上密文私钥，生成md5
	 *
	 */
	private  function _createEncryption($json_data){
		return substr(md5($json_data.$this->signkey),5,20);
	}
	
	
	/**
	 * 登录
	 */
	public function login($username,$password,$sync=true){
		$api_path = $this->apiUrl.'/index/login';
		
		$tmpData = array('user'=>$username,'password'=>$password);
		$tmpData['source'] = $this->source;
		$data = CJSON::encode($tmpData);
		$sign = $this->_createEncryption($data);
		$postData = array('data'=>$data , 'sign' => $sign);

		$rs = Tool::post($api_path,$postData);
		$rsArray = CJSON::decode($rs);
		if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
			Yii::log($api_path.' : $rs  ->'.$rs);
			var_dump($rsArray,$rs);
		}
		
		$rs = array();
		$rs['success']=false;
		if ($rsArray['status']=='200') {
            //登录后同步账号
            if(isset($rsArray['data']['memberInfo']) && $rsArray['data']['memberInfo'] && $sync){
                Member::syncFromGw($rsArray['data']['memberInfo']);
            }
			$rs = $rsArray['data'];
			$rs['success']=true;
		}else{
			$rs['success']=false;
			$rs['msg'] = $rsArray['msg'];
		}
		
		return $rs;
		
	}

    /**
     * 注册
     * @param $data
     * @return array|mixed|string
     */
    public function register($data,$sync=true){
        $api_path = $this->apiUrl.'/index/register';
        $tmpData = $data;
        $tmpData['source'] = $this->source;
        $data = CJSON::encode($tmpData);
        $sign = $this->_createEncryption($data);
        $postData = array('data'=>$data , 'sign' => $sign);
        $rs = Tool::post($api_path,$postData);
        $rsArray = CJSON::decode($rs);
        $rs = array();
        $rs['success']=false;
        if ($rsArray['status']=='200') {
            //登录后同步账号
            if(isset($rsArray['data']['memberInfo']) && $rsArray['data']['memberInfo'] && $sync){
                Member::syncFromGw($rsArray['data']['memberInfo']);
            }
            $rs = $rsArray['data'];
            $rs['success']=true;
        }else{
            $rs['success']=false;
            $rs['msg'] = $rsArray['msg'];
        }

        return $rs;
    }
    
    public function resetPassword($data){
        $api_path = $this->apiUrl.'/index/reset';
        $tmpData = $data;
        $tmpData['source'] = $this->source;
        $data = CJSON::encode($tmpData);
        $sign = $this->_createEncryption($data);
        $postData = array('data'=>$data , 'sign' => $sign);
        $rs = Tool::post($api_path,$postData);
        $rsArray = CJSON::decode($rs);
        if($rsArray['msg'] == 'success' && $rsArray['status'] == 200){
            return true;
        }
        return false;
    }
    
    
    public function captcha($mobile,$source=0){
        $api_path = $this->apiUrl.'/index/captcha';
        $tmpData = array('mobile'=>$mobile,'source'=>$source,'tmpId'=>  self::CAPTCHA);
        $data = CJSON::encode($tmpData);
        $sign = $this->_createEncryption($data);
        $postData = array('data'=>$data , 'sign' => $sign);
        $rs = Tool::post($api_path,$postData);
        $rsArray = CJSON::decode($rs);
        $rs = array();
        $rs['success']=false;
        if ($rsArray['status']=='200') {
            $rs = $rsArray['data'];
            $rs['success']=true;
        }else{
            $rs['success']=false;
            $rs['msg'] = $rsArray['msg'];
        }

        return $rs;
    }
	

	/**
	 * 获取用户信息
	 * 
	 * @param $memberId  ID 或者盖网编号
	 * 
	 */
	public function getInfo($memberId){
		$api_path = $this->apiUrl.'/index/info';
	
		$tmpData = array('user'=>$memberId);
// 		$tmpData['source'] = $this->source;
		$data = CJSON::encode($tmpData);
		$sign = $this->_createEncryption($data);
		$postData = array('data'=>$data , 'sign' => $sign);
		$rs = Tool::post($api_path,$postData);
		$rsArray = CJSON::decode($rs);
		
		if ($rsArray['status']=='200') {
			Member::syncFromGw($rsArray['data']);
			return $rsArray['data'];
		}else{
			return false;
		}
		
	}
	
	/**
	 * 更新用户信息
	 *
	 * @param $memberId  ID 或者盖网编号
	 *
	 */
	public function updateInfo($username){
		$api_path = $this->apiUrl.'/index/info';
	
		$tmpData = array('user'=>$username);
		$data = CJSON::encode($tmpData);
		$sign = $this->_createEncryption($data);
		$postData = array('data'=>$data , 'sign' => $sign);
		$rs = Tool::post($api_path,$postData);
		$rsArray = CJSON::decode($rs);
	
		if ($rsArray['status']=='200') {
			return Member::updateFromGw($rsArray['data']);
		}else{
			return false;
		}
	
	}
	

    /**
     * 获取推荐人信息
     * @param $gaiNumber
     * @return bool
     */
    public function getReferralsInfo($gaiNumber){
		$api_path = $this->apiUrl.'/index/getReferralsData';

		$data = CJSON::encode(array('gwNumber'=>$gaiNumber));
		$sign = $this->_createEncryption($data);
		$postData = array('data'=>$data , 'sign' => $sign);
		$rs = Tool::post($api_path,$postData);
		$rsArray = CJSON::decode($rs);
		if ($rsArray['status']=='200') {
			Member::syncFromGw($rsArray['data']);
			return $rsArray['data'];
		}else{
			return false;
		}

	}
	
	

	/**
	 * 发送短信
	 *
	 *@param  int $mobile 手机号
                    *@param  string $msg 内容
                    *@param  int $type 类型
                    *@param  int $target_id 对象 
	  *@param  int $source 来源
                    * @param array $rong 需要发送的数据
                    *@param int $tmpId 对应的模板id   
	 */
	public function sendSms($mobile,$msg,$type=ApiMember::SMS_TYPE_ONLINE_ORDER,$target_id=0,$source=  ApiMember::SKU_SEND_SMS,$rong=null,$tmpId=null){
		$api_path = $this->smsUrl.'/sms/send';
		$tmpData = array();
		$tmpData['mobile'] = $mobile;
		$tmpData['msg'] = $msg;
		$tmpData['target_id'] = $target_id;
		$tmpData['type'] = $type;
		$tmpData['source'] = $this->source;
                                   $tmpData['datas'] = $rong;             
                                   $tmpData['tmpId'] = $tmpId;

		$data = CJSON::encode($tmpData);
		$sign = $this->_createEncryption($data);
		$postData = array('data'=>$data , 'sign' => $sign);
		$rs = Tool::post($api_path,$postData);
		$rsArray = CJSON::decode($rs);
		$arr = array();
		if ($rsArray['status']=='200') {
			$arr['status'] = true;
			$arr['msg'] = $rsArray['data'];
		}else{
			$arr['status'] = false;
// 			Yii::log($rs);
			$arr['msg'] = isset($rsArray['data'])?$rsArray['data']:'系统错误';
		}
		return $arr;
	
	}
	
	
	/**
	 * 		网签请求
	 *
	 *		$signData 包含字段：
	 *
	 		memberId	int	会员ID
			accountName	string	账户名
			stree	string	银行所在地
			bankName	string	开户银行
			account	string	账号
			identityCard	string	身份证号码
			identityImage	string	身份证正面照片
			identityImage2	string	身份证反面照片
	 * 
	 *
	 */
	public function netSign($signData){
		$api_path = $this->apiUrl.'/index/sign';
		$tmpData = $signData;
		$tmpData['source'] = $this->source;
		$data = CJSON::encode($tmpData);
		$sign = $this->_createEncryption($data);
		$postData = array('data'=>$data , 'sign' => $sign);
		$rs = Tool::post($api_path,$postData);
		$rsArray = CJSON::decode($rs);
	
		$rs = array();
		$rs['success']=false;
		if ($rsArray['status']=='200') {
			$rs = $rsArray;
			$rs['success']=true;
		}else{
			$rs['success']=false;
			$rs['msg'] = $rsArray['msg'];
		}
		
		return $rs;
	}
	
    /**
     * 模拟登陆 //
     * @param type $data
     * @return boolean
     */
    public function appLogin($data){
        $api_path = $this->apiUrl.'/access-token/getUserInfos';
        $tmpData = $data;
        $tmpData['source'] = $this->source;
        $data = CJSON::encode($tmpData);
        $sign = $this->_createEncryption($data);
        $postData = array('data'=>$data , 'sign' => $sign);
        $rs = Tool::post($api_path,$postData);
        $rsArray = CJSON::decode($rs);
        return $rsArray;
    }
	
    /**
     * 获取盖象会员的密码并同步
     * @param unknown $memberId
     */
    public function getGWPassword($memberId)
    {
        $api_path = $this->apiUrl.'/index/getPassword';
        
        $tmpData = array('memberId'=>$memberId);
        $data = CJSON::encode($tmpData);
        $sign = $this->_createEncryption($data);
        $postData = array('data'=>$data , 'sign' => $sign);
        $rs = Tool::post($api_path,$postData);
        $rsArray = CJSON::decode($rs);
        
        if ($rsArray['status']=='200') {
            Member::updateFromGw($rsArray['data']);
            return $rsArray['data'];
        }else{
            return false;
        }
    }
}
