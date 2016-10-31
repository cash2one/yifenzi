<?php
/**
 * 贩卖机用户接口控制器
 * 
 * @author leo8705
 *
 */
class MMemberController extends VMAPIController {

	public $memberInfo;
	public $token;
	public $member;
	const CACHE_DIR = 'MACHINE_CACHE';
	const CK_MEMBER_INFO = 'MACHINE_CACHE_MemberInfo';

	public function beforeAction($action)
	{
		$action->run();
	}

	protected function _checkToken(){
		if ($this->getParam('onlyTest')==1) {
			$this->token = $this->getParam('token');
            $post = $this->getParams();
		}else{
			$requiredFields = array('token');
			$decryptFields = array('token');
			$post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
			$this->token = $post['token'];
		}

		if (!empty($this->token)) {

			$this->token = ClientToken::getInfoByToken($this->token);
			if (empty($this->token)) {
				$this->_error('token不正确');
			}

			$this->memberInfo = Tool::cache(self::CK_MEMBER_INFO)->get($this->token);
			$this->member = $this->memberInfo['memberId'];
		}else{
			$this->_error('token不正确');
		}
	}



    /**
     * 登录接口
     * @param array stock
     * @param array $goods_id
     */
    public function actionLogin() {
       if ($this->getParam('onlyTest')==1) {
       	    $gai_number = $this->getParam('userName');
       	    $password = $this->getParam('password');
           $post = $this->getParams();
       }else{
	       	$this->params = array('userName','password','version');
	       	$requiredFields = array('userName','password');
	       	$decryptFields = array('userName','password','shopId','type');
	       	$post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
                 $rsa = new RSA();
                 $code = $rsa->decrypt($this->getParam('shopId'));

	       	$gai_number = $post['userName'];    //登录名
	       	$password = $post['password'];    //密码
            $version = !empty($post['version']) ? $post['version'] : '3.1.2';
       }

       $machine = FreshMachine::model()->find('code=:code',array(':code'=>$code));

        if (!empty($gai_number) && !empty($password)) {
            $model = new ApiMember();
            $memberRs = $model->login($gai_number, $password);
            if ($memberRs['success']==true) {

            	$memberLoginInfo = $memberRs['memberInfo'];
            	
            	$memberInfo = Member::getMemberInfoByGaiNumber($memberLoginInfo['gai_number']);

                if($machine['member_id'] == $memberInfo['id']){
                    $this->_error(Yii::t('apiModule.member','不可在自己的机器上登录！'));
                }
//             	$memberInfo = Member::model()->find('gai_number=:gai_number',array(':gai_number'=>$memberLoginInfo['gai_number']));
//             	 if (empty($memberInfo)) {
//             	 	$memberApiInfo = $model->getInfo($memberRs['memberId']);
//             	 	$memberInfo = Member::model()->find('gai_number=:gai_number',array(':gai_number'=>$memberLoginInfo['gai_number']));
//             	 }
            	
            	//删除token
            	ClientToken::destoryToken($memberInfo['id']);
                    if (empty($memberInfo)) {
                    	$this->_error(Yii::t('apiModule.member','获取用户数据失败，请稍后重试'));
                    }
                    $lang = isset($_POST['Language'])?$this->getParam('Language'):HtmlHelper::LANG_ZH_CN;
                    switch($lang){
                        case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
                        case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
                        case HtmlHelper::LANG_EN : $lang= 'en';break;
                    }
                    $token = md5($memberInfo['id'] + time() + mt_rand(0, 9));
                    $c_token = new ClientToken();
                    $c_token->member_id = $memberInfo['id'];
                    $c_token->gai_number = $memberInfo['gai_number'];
                    $c_token->token = $token;
                    $c_token->create_time = time();
                    $c_token->expir_time = strtotime("1 month");
                    $c_token->lang = $lang;
                    $c_token->version = $version;
                    if($c_token->save()){
                    	Tool::cache(self::CK_MEMBER_INFO)->set($c_token->token,$memberInfo,600);
                    	$this->_success(array('token'=>$token,'gaiNumber'=>$memberInfo['gai_number']),'GetToken');
                    }
           
            } else {
                if(is_array($memberRs['msg'])){
//                     $this->_error(isset($memberRs['msg']['tips'])?$memberRs['msg']['tips'].',请使用GW号进行登录操作':Yii::t('apiModule.member','请输入正确的GW号'));
                      $this->_success($memberRs['msg']['data']);

                }else{
                $this->_error(isset($memberRs['msg'])?$memberRs['msg']:Yii::t('apiModule.member','用户名或密码错误'));
                }
                 $this->_error(isset($memberRs['msg'])?$memberRs['msg']:Yii::t('apiModule.member','用户名或密码错误'));
            }
        }else{
            $this->_error(Yii::t('apiModule.member','参数错误'));
        }
    }

    /**
     * 注销
     * 清除token 和 缓存
     */
    public function actionLogout(){
    	$this->_checkToken();
           $delete = PartnerToken::destoryToken($this->member);
           if(!empty($delete)){
               $this->_success(Yii::t('apiModule.member','注销成功'));
           }else{
              $this->_error(Yii::t('apiModule.member','注销失败'));
           }
    }

    /**
     * 登录设置语言
     */
    public function actionSetLanguage(){
    	$this->_checkToken();
        if($_POST['Language']){
            $this->_success(Yii::t('apiModule.member','设置语言成功'));
        }
    }


    /**
     * 注册接口
     * @param array stock
     * @param array $goods_id
     */
    public function actionRegister() {
    	if ($this->getParam('onlyTest')==1) {
    		$mobile = $this->getParam('mobile');
    		$password = $this->getParam('password');
    		$captcha = $this->getParam('captcha');
            $post = $this->getParams();
    	}else{

    		$this->params = array('mobile','password','captcha');
    		$requiredFields = array('mobile','password','captcha');
    		$decryptFields = array('mobile','password','captcha');
    		$post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);

    		$mobile = $post['mobile'];
    		$password = $post['password'];
    		$captcha = $post['captcha'];
    	}


    	if (!empty($mobile) && !empty($password) && !empty($captcha)) {
    		$model = new ApiMember();
    		$r_data = array();
    		$r_data['mobile'] = $mobile;
    		$r_data['password'] = $password;
    		$r_data['captcha'] = $captcha;
    		$memberRs = $model->register($r_data);
    		if ($memberRs['success']==true) {

    			$memberInfo =  $model->getInfo($memberRs['memberId']);
    			if (empty($memberInfo)) {
    				$this->_error(Yii::t('apiModule.member','获取用户数据失败，请稍后重试'));
    			}
    			
    			$memberInfo = Member::model()->find('gai_number=:gai_number',array(':gai_number'=>$memberInfo['gai_number']));
    			
    			$lang = isset($_POST['Language'])?$this->getParam('Language'):HtmlHelper::LANG_ZH_CN;
    			switch($lang){
    				case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
    				case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
    				case HtmlHelper::LANG_EN : $lang= 'en';break;
    			}
    			$token = md5($memberInfo['id'] + time() + mt_rand(0, 9));
    			$c_token = new ClientToken();
    			$c_token->member_id = $memberInfo['id'];
    			$c_token->gai_number = $memberInfo['gai_number'];
    			$c_token->token = $token;
    			$c_token->create_time = time();
    			$c_token->expir_time = strtotime("1 month");
    			$c_token->lang = $lang;
    			if($c_token->save()){
    				Tool::cache(self::CK_MEMBER_INFO)->set($c_token->token,$memberInfo,600);
    				//发送短信      
                                                                      $msg = "您的账号申请成功！账号为{$memberInfo['gai_number']}，请牢记。";
    				$model->sendSms($mobile,$msg, ApiMember::SMS_TYPE_ONLINE_ORDER, 0, ApiMember::SKU_SEND_SMS,array($memberInfo['gai_number']),  ApiMember::APPLY_SUCCESS);
    				$this->_success(array('token'=>$token,'gaiNumber'=>$memberInfo['gai_number']),'GetToken');
    			}
    		} else {
    			$this->_error(Yii::t('apiModule.member',$memberRs['msg']));
    		}
    	}else{
    		$this->_error(Yii::t('apiModule.member','参数错误'));
    	}
    }



    /**
     * 发送短信验证码
     */
    public function actionCaptchaCode() {
    	if ($this->getParam('onlyTest')==1) {
    		$mobile = $this->getParam('mobile');
            $post = $this->getParams();
    	}else{

    		$this->params = array('mobile','tag');
    		$requiredFields = array('mobile');
    		$decryptFields = array('mobile','tag');
    		$post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
                
    		$mobile = $post['mobile'];
                $tag = isset($post['tag'])?$post['tag']:'';
                if(!empty($tag)&& $tag==1){
                    // 1表示修改密码验证mobile 是否存在
                    $member = Member::model()->find('mobile=:mobile',array(':mobile'=>$mobile));
                    if(empty($member)){
                        $this->_error('未注册的手机号码！');
                    }
                }
    	}

    	if (!empty($mobile) ) {
    		$model = new ApiMember();
                if(!empty($tag)&& $tag==2){
                    $rs = $model->getInfo($mobile);
                    if($rs!=FALSE){
                        $this->_error('手机号码已被注册！');
                    }
                }
    		$sendRs = $model->captcha($mobile);
    		if ($sendRs['success']==true) {
    			$this->_success('发送成功');
    		}else {
    			$this->_error(isset($sendRs['msg'])?$sendRs['msg']:'发送失败');
    		}
    	}

    }

    /**
     * 找回密码
     */
    public function actionFindPassWord()
    {
        try {
            if ($this->getParam('onlyTest')==1) {
                $post = $this->getParams();
            }else{
                $this->params = array('mobile', 'captcha', 'name');
                $requiredFields = array('mobile', 'captcha');
                $decryptFields = array('mobile', 'captcha');
                $post = $this->decrypt($_REQUEST, $requiredFields, $decryptFields);
            }

            $mobile = $post['mobile'];
            $captcha = $post['captcha'];
            $password = mt_rand(100000, 999999);
            $oldPassword = $password;
            $name = isset($post['name'])?$post['name']:'';
            $this->checkVerifyCode($mobile, $captcha);
            $members = Member::model()->findAll('mobile=:params', array(
                ':params' => $mobile,
            ));
            if (count($members) > 1) {
                $users = array();
                foreach ($members as $key => $value) {
                    $users[] = array('user' =>$value->gai_number);
                }
                $this->_error(array('data'=>$users),2);
            } elseif (!$members) {
               //查询盖象商城里是否已注册，如果已注册则将盖象数据同步到sku
                $list = Yii::app()->gw->createCommand()
                    ->select('gai_number')
                    ->from("{{member}}")
                    ->where('mobile = :mobile', array(':mobile' => $mobile))
                    ->queryRow();
                if(!empty($list)){
                    Member::getMemberInfoByGaiNumber($list['gai_number']);
                }else{
                    $this->_error('该手机号未被注册');
                }

            }
            if (!empty($name)) {
                $model = Member::model()->findByAttributes( array(
                    'mobile' => $mobile, 'gai_number' => $name
                ));
               $password = CPasswordHelper::hashPassword($password . $model->salt);
                Yii::app()->db->createCommand()->update('gaiwang.gw_member',
                    array(
                        'password'=>$password,
                        'salt'=>$model->salt
                    ),
                    'mobile=:mobile AND gai_number = :name',
                    array(':mobile'=>$mobile,':name'=>$name)
                );
                Yii::app()->db->createCommand()->update('{{member}}',
                    array(
                        'password'=>$password,
                        'salt'=>$model->salt
                    ),
                    'mobile=:mobile AND gai_number = :name',
                    array(':mobile'=>$mobile,':name'=>$name)
                );
            } else {
                $model = Member::model()->findByAttributes( array(
                    'mobile' => $mobile,
                ));
                $password = CPasswordHelper::hashPassword($password . $model->salt);
                Yii::app()->db->createCommand()->update('gaiwang.gw_member',
                    array(
                        'password'=>$password,
                        'salt'=>$model->salt
                    ),
                    'mobile=:mobile',
                    array(':mobile'=>$mobile)
                );
                Yii::app()->db->createCommand()->update('{{member}}',
                    array(
                        'password'=>$password,
                        'salt'=>$model->salt
                    ),
                    'mobile=:mobile',
                    array(':mobile'=>$mobile)
                );
            }
            $content = "欢迎使用盖网，尊敬的".$model->gai_number."，您的新密码是：".$oldPassword."，请尽快登录盖网修改密码，切忌转发。";
            $Member = new ApiMember();
            $Member->sendSms($mobile, $content, ApiMember::SMS_TYPE_ONLINE_ORDER, 0, $type = ApiMember::SKU_SEND_SMS, array($model->gai_number,$oldPassword),  ApiMember::SET_PASS );
            $this->_success(array());
        } catch (Exception $e) {
            $this->_error($e->getMessage());
        }

    }
/**
 * 测试
 */
   public function actionTest(){
//	$data = array(
//			'DeviceID'=> '9effafe3dc04042ae5be686d306217fbd58f421419d635f124d8665eff57693dcc6797c0edffc76da01e020e470e46f7b0fda6f15819617f2dbb8fb3a3d6ce73c51f30b46e89e95fa8c87c93013e20d4c94874b901d41b69f04ab6755b1fd52b2e95ac928c4d11ce5b3cb2aa488ef17ac484a2c8467ab8aa42c0b9a26a181ac6',
//			'AppName' => '03432abaf7a76294331bb82b21758953f8ce9125415808dd9d319b973b88e30c1ddc04abb7a5761b0e4ce79aeec09435f9b3432ae5ed5a09e910e3203e93b92b4e75228751931a589dd13a459f5d1c412ebb5a452dde9159ec1210b8dc71563f7857432b919382e834db58dedc8cbd8dbb60d0173edb7760e23bf45bfa287e3c',
//			'AppVersion' => 7.33,
//			'Language'   => 'jt',
//			'ShopID'     => '3dbd3e4421062d11a0af4a8a87bb14e66bec1c90df399d26d7f352742de8f4b758a74a22bdb6db82aba6c3acd12a246130cf4050a21661c76a70fb9c98e7a38767b34f22ff6566706cf8ea37aa015abc427befa4e4b6b2968c2ce8a931bd335604caf31ec36292f9c3b30647812c8d55ba082558ee3765676ec6e86dec49f5f7',
//			'ProvinceID' => 22,
//			'CityID' => 254,
//			'DistrictID' => 3529,
//			'SN' => '7de2eac8cff35b2d3019a19857135a10e73183712e07f388bff7fdaeba98b7c741d8422775713ad0dad59354581837797784927ee2c719b5f7b99ec6142448a44da4178e3776d293436c174197f1c81216fa3dfeb910082a4f984fdddc36254194c34d29865d842d8d061ffc093fa3c5c985359e7a88ec421950b4fbe84b7c323e37e4aaff9aa400ea8332a03dc213aeb1d267004c23b5d07ac7d3e28bdeedcd0baab8317f522d7354fea66b6e964e6021919bc05c27c8d0fe205d657f3c6d360ef35cf4bee62cadd176051ccc1d417f06ddd72ec111b69999b61eeac1c215c242227c7d3c1cca1daf312cbcc569af7e974bff0a282e755614e10765b15aacb3',
//			'Amount' => '66172a843c238307cf45e28a2be3441e9c2dbcf42dee92827a9cd2c66f124e7db0d0da70ec8379ad8450590b55a72ebdee66226fbd6a3b1a55f2e1ae15ae2e427b1c37029f3e5ca5ca1790e592ff54b525a2e79964f8abbc91dd47013d2a425df13cf3e7c7bf8ae1aa0acb30bb44a97de7872297abd4f8b18d0e67aa1cbcbc92dbe4a0baf25800f2709354334ccb64c678cb1e76c0df1aef333a19dbc3d81924b789c835b91767fa578e0d98f56db15e443aeb98a5e39cabfe572fbf4e046c4391e869f81dbb69016f39065a9b48d4fb36bb8aa69bd25c91e0e7487744e2365b6cd1ce006ab10718a79475b5d466c1a99796d7b203634412c3d4544f5b01e089',
//			'GW' => '98c9bd88e674c6425a77fe8abfc4dd516e9b64770d5fe8155301f938601d07fc158f0d39f33e73e4f7e5adb61fd951ea8a6d04500a31e4c90017a88565b5d03ef5967293bba66f566e0c1815515c02d81d601c84e5a523888790f67cc8156548064d61f5223940b484900ecf9924e0fa56f2a5e0c6cff6e26be112ef46f33ed47da72803aacd601aa85035ef285386b30e9ca92f09c5f549e9a420d9a8c7e621bc863af72ca45a5be39371e5e7f56f03cd10b18411f7710d63eb127e37afa97b3068411aec3870b862e0adf2a692444ebf4f0b4f6ab1edd12b15f26008fb3d4781487181a71254c83472ba4e065344e81194bab97dd996330aede5ac5d58ba2c',
//			'Symbol' => '34f10f6946c894001321117e697f99b2a59e882d8cade93047612506eebcce04aad18147a9084a2596301179af678c95e519c83711f0ebbd53727c932173832e1f6140e61495ffea0c8c2a63a55d7815b121fa1a5e6ae33710e3170d8ffe8f1089492da96bb58d4352a9c71098adf6c639aaca06401c6b771944140fc46cd6d6da38bdcf190fe690fb3b024144e7e3d0115a9dd6cd6c3ff9d4b74096b7096e28181b6a3da3abc4371d0c701c55ecc0129972198891d04095fbcbf1d022b77ed6c548455d9d363c50d0462ee2390ba43376cd0be6c9353252c34f6ef6ef1533313927771dcd1a3fb5498895e07283a160c3cf40654a24cd793250512086689d54',
//			'Token' => '514bb847b8a68eb76a111954c799d052375b1da930dc7b6919c85bc1b837dea61caff5a73fb4445fb86451d3ab9259a64e0f7b68cc570f3a6e35fc1d2bba06aab61ccc09565c73be77ab1a60706c94e4706172fa4f03b8832dd9695046cd1ea01a36d4f6fe5d35328bc1789346b44449d61ad4ea2fb90194ff203e07ad89b05c529daa19cf0bf08e39b0b4489f1f868a6fdeebfb846136947def31202fcd8f66392210818ce7fd5904635c2e17aa58ccff4eb47f8c30772b207488b777677aad7555f9b227c4dc8767050450c35104fe5a85dc483113cfc34b29cbfb5e489cf172a503470ebaea9411c14573e582f6bb5bedf810fff96f2d11ab94c341002392',
//			'RecordType' => '44247255bb2575b9ab9153597cf68a688d0b9e0add7b9d9323014d44859c66c70c12784e968f14550665d0e3c75dbbe5986b7ae1370bb2d9cc07613524eb186ca7d0302e2d0f0de67f593ab4cb6caede10441d0ddfa8e781f9a51e185977db2cec073ab519f984667e51c388c44d3f6bc617dde56f77aab08baaca18fbf18daf4877ef4c653ea7d8e8c5021d6c10cebafa7a5928dcf63bf25d42ab2147f59372b3a3151a83cc761cf7f3f56d738008db7638fa02ae644b8575a04bb225245351b585eff918872b7244f4a9b0f96cff807dc2108a5772a79936bfa81e30714cd55c4afc801c4fcf0447ef5b59510758e81fe0dcced0899f0f058568e8576307e1',
//		);
       
//       $api = new ApiMember();
//       $api->sendSms('13760671419','123456789',ApiMember::SMS_TYPE_ONLINE_ORDER,0 , ApiMember::SKU_SEND_SMS);die;
//       preg_match_all ("/\(?  (\d{3})?  \)?  (?(1)  [\-\s] ) \d{3}-\d{4}/x",
//                "Call 555-1212 or 1-800-555-1212", $phones);
//       preg_match_all('/^[a-zA-Z]+(\d+)$/i','GW34676953230150117245640579869',$TransactionID);
//       var_dump($TransactionID);die;
       $data = array(
'addressId'=>'',	
'goods'=>'[{"id":"5","num":"1"}]',
'Language'=>'1',
'machineTakeType'=>'0',
'memberId'=>'GW60000002',
'shippingTime'=>'10/11 , 13:30',
'shippingType'=>'1',
'sid'=>'46',
'token'=>'Uj2eSnGARD1VDnx23jwR',
'type'=>'3'
);
        $url = 'http://api.gaiwangsku.com/cOrder/Create';
         $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        var_export($result);

       }
                             
    
}

