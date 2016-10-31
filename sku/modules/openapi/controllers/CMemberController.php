<?php
/**
 * 盖付通接口控制器
 *
 * @author leo8705
 *
 */

class CMemberController extends COpenAPIController {

    /**
     * 登录接口用于第三方获取公钥

     */
    public function actionLogin() {
        try{
            if (!empty($_REQUEST['onlyTest'])) {
            	$gai_number =$this->getParam('userName');
            	$password =$this->getParam('password');
            }else{
            	$this->params = array('userName','password','source');
	            $requiredFields = array('userName','password');
	            $decryptFields = array('userName','password','source');
	            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
	            $gai_number =$post['userName'];
	            $password =$post['password'];
            }
            
            if (!empty($gai_number) && !empty($password)) {
                $model = new ApiMember();
                $memberRs = $model->login($gai_number, $password);
//                 var_dump($memberRs);exit();
                if($memberRs['success']==true){
                    //生成盖机公钥和密钥
                    $keyArr =Fun::createRsaKey($gai_number);
                    $publicKey = $keyArr['publicKey'];
                    $privateKey = $keyArr['privateKey'];
                    $publicKeyApk = $keyArr['publicKeyApk'];
                    //删除token
                    OpenClientToken::destoryToken($memberRs['memberId']);
                    $memberInfo =  $model->getInfo($memberRs['memberId']);
                    if (empty($memberInfo)) {
                        $this->_error(Yii::t('member','获取用户数据失败，请稍后重试'));
                    }
                    $lang = isset($_POST['Language'])?$this->getParam('Language'):HtmlHelper::LANG_ZH_CN;
                    switch($lang){
                        case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
                        case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
                        case HtmlHelper::LANG_EN : $lang= 'en';break;
                    }
                    $token = md5($memberRs['memberId'] + time() + mt_rand(0, 9));
                    $client_token = new OpenClientToken();
                    $client_token->member_id = $memberRs['memberId'];
                    $client_token->gai_number = $memberInfo['gai_number'];
                    $client_token->token = $token;
                    $client_token->create_time = time();
                    $client_token->expir_time = strtotime("1 month");
                    $client_token->lang = $lang;
                    $client_token->private_key = $privateKey;
                    $client_token->public_key = $publicKey;
                    if($client_token->save()){
                        $this->_success(array('key'=>$publicKeyApk,'publicKey'=>$publicKey,'token'=>$token),'GetKey');
                    }else{
                    	$this->_error(Yii::t('member','系统错误'));
                    }
                }else{
                	$this->_error(Yii::t('member','账号或密码错误'));
                }

            }else{
                $this->_error(Yii::t('member','参数错误'));
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };
    }

    /**
     * 客户端注册接口
     */
    public function actionRegister(){
        $this->params = array('mobile','captcha','password','source','referralsGaiNumber');
        $requiredFields = array('mobile','captcha','password');
        $decryptFields = array('mobile','captcha','password');
        $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
        $mobile =$post['mobile'];
        $captcha =$post['captcha'];
        $password =$post['password'];
        $source =$post['source'];
        $referralsGaiNumber = isset($post['referralsGaiNumber'])?$post['referralsGaiNumber']:'';
        $data = array(
           'mobile'=>$mobile,
            'captcha'=>$captcha,
            'password'=>$password,
            'source'=>$source,
        );
        if(!empty($referralsGaiNumber)){
            $data['referralsGaiNumber'] = $referralsGaiNumber;
        }
        $model = new ApiMember();
        $memberRs = $model->register($data);
        if($memberRs['success'] == true){
            $this->_success(array('memberId'=>$memberRs['memberId'],'tips'=>$memberRs['tips']));
        }else{
            $this->_error($memberRs['tips']);
        }
    }

    /**
     * 客户端发送手机验证码接口
     */
    public function actionSendCode(){
        $this->params = array('mobile','source');
        $requiredFields = array('mobile');
        $decryptFields = array('mobile');
        $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
        $mobile =$post['mobile'];
        $source =$post['source'];
        $model = new ApiMember();
        $memberRs = $model->captcha($mobile,$source);
        if($memberRs['success'] == true){
            $this->_success(array('code'=>$memberRs['code'],'overtime'=>$memberRs['overtime']));
        }else{
           $this->_error('获取验证码失败');
        }

    }


    /*
 * 地址查询接口
 * memberId 会员id
 */
    public function actionAddressList() {
        try{
            $this->params = array('token');
            $requiredFields = array('token');
            $decryptFields = array('token');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $cri = new CDbCriteria();
            $cri->select = 't.*';
            $cri->compare('member_id',$this->member);
            $list = Address::model()->findAll($cri);
            $list_arr = array();
            if(!empty($list)){
                foreach($list as $key => $v){
                    $list_arr[$key] = $v->attributes;
                    $list_arr[$key]['province_name'] = Region::getName($v['province_id']);
                    $list_arr[$key]['city_name'] = Region::getName($v['city_id']);
                    $list_arr[$key]['district_name'] = Region::getName($v['district_id']);
                }
                $this->_success($list_arr);
            }else{
                $this->_error(Yii::t('member','你目前没有保存地址'));
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };
    }

    /*
    * 用户地址添加接口
    * data为加密过一维数组，元素如下
    * real_name  收货人姓名
    * mobile  手机号码
    * province_id  省份id
    * city_id  城市id
    * district_id  区/县id
    * street  详细地址
    * zip_code  邮编
    * default  默认地址（0否，1是)
    */
    public function actionAddressAdd() {
        try{
            $tag=$this->action->id;
            $this->params = array('token','data');
            $requiredFields = array('token','data');
            $decryptFields = array('token','data');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $data =$post['data'];
            $add = str_replace("\\\"", "\"",$data);
            $data = CJSON::decode($add);
            if(empty($data['real_name'])){
                $this->_error(Yii::t('member','收货人姓名不能为空'),null,$tag);
            }
            if(empty($data['mobile'])){
                $this->_error(Yii::t('member','手机号码不能为空'),null,$tag);
            }
            if(empty($data['province_id'])){
                $this->_error(Yii::t('member','省份不能为空'),null,$tag);
            }
            if(empty($data['city_id'])){
                $this->_error(Yii::t('member','城市不能为空'),null,$tag);
            }
            if(empty($data['district_id'])){
                $this->_error(Yii::t('member','区/县不能为空'),null,$tag);
            }
            if(empty($data['street'])){
                $this->_error(Yii::t('member','地址不能为空'),null,$tag);
            }
            $data['member_id'] = $this->member;
            $model = new Address;
            $model->attributes = $data;

            if($model->save()){
                if($model->default == Address::DEFAULT_IS){
                    Address::model()->updateAll(array('default'=>Address::DEFAULT_NO),"member_id = :member_id AND id <> :id",array(':member_id'=>$this->member,':id'=>$model->id));
                }
                $this->_success(Yii::t('member','保存成功'),$tag);
            }else{
                $this->_error(Yii::t('member','保存失败'),null,$tag);
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }

    /*
       * 用户地址编辑接口
       * data为加密过一维数组，元素如下
       * id 主键，地址id
       * real_name  收货人姓名
       * mobile  手机号码
       * province_id  省份id
       * city_id  城市id
       * district_id  区/县id
       * street  详细地址
       * zip_code  邮编
       * default  默认地址（0否，1是)
       */
    public function actionAddressUpdate() {
        try{
            $tag=$this->action->id;
            $this->params = array('token','data');
            $requiredFields = array('token','data');
            $decryptFields = array('token','data');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $data =$post['data'];
            $add = str_replace("\\\"", "\"",$data);
            $data = CJSON::decode($add);
            if(empty($data))
                $this->_error(Yii::t('member','地址不能为空'));

            $model = Address::model()->findByPk($data['id'],"member_id = :member_id",array(':member_id'=>$this->member));
            if(empty($model)){
                $this->_error(Yii::t('member','不可修改会员id'));
            }
//        $this->_chenck($model->member_id);

            if(!empty($model)){
                $model->attributes = $data;
                $model->member_id = $this->member;
                if($model->save()){
                    if($model->default == Address::DEFAULT_IS){
                        Address::model()->updateAll(array('default'=>Address::DEFAULT_NO),"member_id = :member_id AND id <> :id",array(':member_id'=>$this->member,':id'=>$model->id));
                    }
                    $this->_success(Yii::t('member','保存成功'),$tag);
                }else{
                    $this->_error(Yii::t('member','保存失败'),null,$tag);
                }
            }else{
                $this->_error(Yii::t('member','地址信息不存在'),null,$tag);
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        };



    }

    /*
   * 用户地址删除接口
   * id 主键，地址id
   */
    public function actionAddressDelete() {
        try{
            $tag=$this->action->id;
//             $id = $this->getParam('id');
            
            $this->params = array('token','id');
            $requiredFields = array('token','id');
            $decryptFields = array('token','id');
            $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
            $id = $post['id'];
            if(!is_numeric($id) && !is_int($id))
                $this->_error(Yii::t('member','非法参数'));

            $model = Address::model()->findByPk($id);

            $this->_chenck($model->member_id);

            if($model){
                if($model->delete()){
                    $this->_success(Yii::t('member','成功删除'),$tag);
                }else{
                    $this->_error(Yii::t('member','删除失败'),null,$tag);
                }
            }else{
                $this->_error(Yii::t('member','数据不存在'),null,$tag);
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }

    /**
     * 注销
     * 清除token 和 缓存
     */
    public function actionLogout(){
        try{
            $delete = OpenClientToken::destoryToken($this->member);
            if(!empty($delete)){
                $this->_success(Yii::t('member','注销成功'));
            }else{
                $this->_error(Yii::t('member','注销失败'));
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }










}