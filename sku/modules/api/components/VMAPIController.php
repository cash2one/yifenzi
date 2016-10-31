<?php

/**
 * 售货机api模块控制器父类
 * 
 * 参数需要使用一机一密解密
 * 
 * shopId用公钥解密
 * 
 */
class VMAPIController extends APIController {

	protected $code;
    protected $vending;
    protected $type;					//售货机类型
    protected $className;
    protected $goodsClassName;
    protected $stockApiProjectId;
    protected $privateKey = '';
    protected $primaryKey = 'shopId';
    protected $params;
    const CK_MEMBER_INFO = 'MACHINE_CACHE_MemberInfo';
    
    public function beforeAction($action)
    {

//         if(!$this->isPost())$this->_error('提交方式错误');
        if (isset($_REQUEST['shopId']))
        {
            $rsa = new RSA();
            if ($this->getParam('onlyTest')==1) {
            	$this->code = $this->getParam('shopId');	
            	$this->type = $this->getParam('type');
            }else{
            	$this->code = $rsa->decrypt($this->getParam('shopId'));		//解密使用之前的公钥加密得到的machine_code
            	$this->type = $rsa->decrypt($this->getParam('type'));
            }
//             var_dump($this->code,$this->type);
            
            $this->type = empty($this->type)?Stores::MACHINE:$this->type;
            if ($this->type==Stores::MACHINE) {
            	$this->className = 'VendingMachine';
            	$this->goodsClassName = 'VendingMachineGoods';
            	$this->stockApiProjectId = API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID;
            }
            
            
            if ($this->type==Stores::FRESH_MACHINE) {
            	$this->className = 'FreshMachine';
            	$this->goodsClassName = 'FreshMachineGoods';
            	$this->stockApiProjectId = API_PARTNER_FRESH_MACHINE_MODULES_PROJECT_ID;
            }
            
            unset($_REQUEST[$this->primaryKey]);
          
            //查询币种，设定语言类型
            $className = $this->className;

            $this->vending = Yii::app()->db->createCommand()
                            ->select('*')
                            ->from($className::model()->tableName())
                            ->where('code = :code ', array(':code' => $this->code))
                            ->queryRow();

            if (empty($this->vending)){
                echo $this->_error(Yii::t('MachineForGt','机器编码没有对应的机器'));
                Yii::app()->end();
            }
            
            
            
             
            $actionName = $this->_getActionName();
            if ($actionName != 'machine/activation' && !empty($this->vending['private_key'])){				//如果不是盖机初次安装接口那么就设定盖机私钥，用于解密。
                $this->privateKey = $this->vending['private_key'];
            }
            //设定语言，优先按照传递过来的语言设定
            $language = isset($_REQUEST['Language']) ? $this->getPost('Language') : $this->vending['symbol'];
            switch ($language){		//按照盖机的币种设定语言
                case VendingMachine::HONG_KONG_DOLLAR:
                    Yii::app()->language = "zh_tw";
                    break;
                case VendingMachine::EN_DOLLAR:
                    Yii::app()->language = "en";
                    break;
                case 'ft':
                    Yii::app()->language = "zh_tw";
                    break;
                case 'en':
                    Yii::app()->language = "en";
                    break;
                default:
                    Yii::app()->language = "zh_cn";
                    break;
            }
        }
        
        $action->run();
    }

    /**
     * 检查会员消费金额是否超过店铺每日限额
     */
    protected function _getMemberToStoreAmount($sid=0,$member,$add_amount=0,$type=null){
        
        $amount = Order::getMemberTodayAmount($member,$sid,$type);
        $limit_config = Tool::getConfig('amountlimit');

        $store = null;
        if ($type==Order::TYPE_SUPERMARK && !empty($sid)) {
            $store = Yii::app()->db->createCommand()->select('max_amount_preday')->from(Supermarkets::model()->tableName())->where('id=:id',array(':id'=>$sid))->queryRow();
        }

        if ($type==Order::TYPE_MACHINE && !empty($sid)) {
            $store = Yii::app()->db->createCommand()->select('max_amount_preday')->from(VendingMachine::model()->tableName())->where('id=:id',array(':id'=>$sid))->queryRow();
        }

        if ($type==Order::TYPE_FRESH_MACHINE && !empty($sid)) {
            $store = Yii::app()->db->createCommand()->select('max_amount_preday')->from(FreshMachine::model()->tableName())->where('id=:id',array(':id'=>$sid))->queryRow();
        }

//        $max_amount = isset($store['max_amount_preday'])&&$store['max_amount_preday']>0?$store['max_amount_preday']:$limit_config['memberTotalPayPreStoreLimit'];
        $max_amount = $limit_config['memberTotalPayPreStoreLimit'];  //获取后台限额
        
        //加上准备消费的金额 看是否超过
        if (!empty($add_amount)) {
            $amount +=  $add_amount;
        }

        $isOver=false;
        if ($amount>=$max_amount&&$max_amount>0) {
            $isOver = true;
        }

        $isPointOver = false;
        $point_amount = Order::getMemberTodayPointPayAmount($member,$sid,$type);
        if ($point_amount>$limit_config['memberPointPayPreStoreLimit']) {
            $isPointOver = true;
        }

        return  array('amount'=>$amount,'max_amount'=>$max_amount,'isOver'=>$isOver,'point_amount'=>$point_amount,'max_point_amount'=>$limit_config['memberPointPayPreStoreLimit'],'isPointOver'=>$isPointOver);
    }
    
    /**
     * 运行成功返回json
     * @param type $data
     */
    protected function _success($data='')
    {
        $array = array();
        if($data != false && is_array($data)){
            $array['reason'] = "";
            $array['data'] = $data;
        }elseif(is_string($data)||is_numeric($data)){
            $array['reason'] = $data;
        }else{
            $array['reason'] = "";
        }
        $array['isSuccess'] = "1";
        header("Content-type:text/html;charset=utf-8");
        echo json_encode($array);
        Yii::app()->end();
    }


    /**
     * 运行错误返回json
     * @param type $error
     */
    protected function _error($data='',$resultCode=0,$resultDesc='')
    {
        $array = array('isSuccess'=>(string)$resultCode);
        if($data != false){
            if(is_string($data)){
                $array['reason'] = $data;
            }else{
                $array['device'] = $data;
                if($resultDesc!='')$array['reason']=$resultDesc;
            }
        }
        header("Content-type:text/html;charset=utf-8");
        echo json_encode($array);
        Yii::app()->end();
    }


    /**
     * 加密
     */
    protected function encrypt($data)
    {
        $rsa = new RSA();
        return $rsa->encrypt($data);
    }
    
    /**
     * 公用解密方法
     * @param array|string $post
     * @param unknown_type $is_public
     * @return Ambigous <string, multitype:, string|array>|boolean
     */
    public function decrypt($request, $requiredFields = array(), $decryptFields = array(),$one=false)
    {
        if(is_array($request))
        {
            $result = array();
            $rsa = new RSA;
            if(!empty($this->privateKey) && $one)$rsa->privateKey = $this->privateKey;
            if (empty($this->params)) {
            	return false;
            }
            foreach ($this->params as $field)
            {
                if($field==$this->primaryKey)
                {
                    if(empty($this->code))throw new Exception($this->primaryKey.'提交数据不能为空');
                    $result[$this->primaryKey] = $this->code;//解密完毕之后，将shopId重新赋值为之前已经解密好的值
                    continue;
                }

                if (isset($request[$field]))
                {                	 
                    // 验证必填字段
                    if ($requiredFields && in_array($field, $requiredFields)) {
                        if (!trim($request[$field]) && (string)$request[$field]!=='0')
                            throw new Exception($field.'提交数据不能为空');
                    }
                    // 解密字段值
                    if ($decryptFields && in_array($field, $decryptFields))
                    {             
                        $result[$field] = $rsa->decrypt($request[$field]);
                        if ($result[$field]===false)throw new Exception('数据解密失败');
                    }else
                        $result[$field] = $request[$field];
              
                    //一机一密的处理
                    if($field == $this->primaryKey && !empty($this->code))
                        $result[$this->primaryKey] = $this->code;
                    
                }elseif(in_array($field, $requiredFields)){
                	throw new Exception($field.'是必填字段！');
                }else
                    continue;
            }
            return $this->magicQuotes($result);
        }else{
            return false;
        }
    }
    
    /**
     * 获取当前方法名
     */
    public function _getActionName(){
        return strtolower($this->getId().'/'.$this->action->getId());
        // return $this->action.'/'.$this->id;
    }

    public function requestSku($params,$url,$project = '105',$api = DOMAIN_API)
    {
        $json = json_encode($params);
        $private_key = $this->_getApiKeys('gw_project',$project);
        $code = md5($json.$private_key);//校验
        $url = $api.'/'.$url;
        $data = array(
            'project'=>$project,
            'data'=>$json,
            'encryptCode'=>$code
        );
        $ch = curl_init() ;
        curl_setopt($ch, CURLOPT_URL,$url) ;
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data); // 在HTTP中的“POST”操作。如果要传送一个文件，需要一个@开头的文件名
        ob_start();
        curl_exec($ch);
        $response = ob_get_contents() ;
        ob_end_clean();
        curl_close($ch) ;
        $res = json_decode($response,true);
        Yii::log('sorder/pay   -   '.$response,CLogger::LEVEL_TRACE);
//         if($res == null)
//             throw new Exception($response);
		if (isset($res['resultCode']) && $res['resultCode']==1) {
			$res['success'] = true;
			return $res;
		}else {
			$res['success'] = false;
			return $res;
		}

    }
    
 
    protected function _output($array)
    {
        header("Content-type:text/html;charset=utf-8");
        echo CJSON::encode($array);
        Yii::app()->end();
    }
    
    	  /**
	 * 检验验证码是否正确
	 * @param string $phone 手机号
	 * @param string $code 验证码
	 * @return bool 是否正确
	 */
	public function checkVerifyCode($phone, $code)
	{
		$res = Yii::app()->gw->createCommand()->select()->from('{{checkcode}}')->where("phone=:phone", array(':phone' =>$phone))->queryRow();

		if (empty($res) || $code != $res['checkcode']) {
			$this->_error('验证码错误');
		} elseif ($res['overtime']+60 < time()) {
			$this->_error(Yii::t('member', '验证码超时'));
		} else {
			Yii::app()->gw->createCommand()->delete('{{checkcode}}', "phone='{$phone}'");
			return true;
		}
	}
        
	
}