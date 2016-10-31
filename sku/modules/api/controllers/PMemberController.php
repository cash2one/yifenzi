<?php
/**
 * 商家客户端专用接口控制器
 * 
 * @author leo8705
 *
 */
class PMemberController extends PAPIController {

    /**
     * 登录接口
     * @param array stock
     * @param array $goods_id
     */
    public function actionLogin() {
//        $gai_number = $this->rsaObj->decrypt($this->getParam('userName'));    //登录名
//        $password = $this->rsaObj->decrypt($this->getParam('password'));    //密码

       if ($this->getParam('onlyTest')==1) {
       	    $gai_number = $this->getParam('userName');
       	    $password = $this->getParam('password');
       	    $deviceId = $this->getParam('deviceId');
             $version  =  $this->getParam('version'); 
              $deviceId = !empty($deviceId) ?  $deviceId : '0';
             $version = !empty($version) ?  $version : '3.1.2';
       }else{
	       	$gai_number = $this->rsaObj->decrypt($this->getParam('userName'));    //登录名
	       	$password = $this->rsaObj->decrypt($this->getParam('password'));    //密码
	       	$deviceId = $this->rsaObj->decrypt($this->getParam('deviceId'));    //密码
            $version  =  $this->getParam('version');                            //版本号
            $version = !empty($version) ?  $this->rsaObj->decrypt($version) : '3.1.2';


       }
       
        if (!empty($gai_number) && !empty($password)) {
            $model = new ApiMember();
            $memberRs = $model->login($gai_number, $password);
            
            if ($memberRs['success']==true) {
            	
            	$memberInfo = Member::getMemberInfoByGaiNumber($memberRs['memberInfo']['gai_number']);
//             	$memberInfo = Member::model()->find('gai_number=:gai_number',array(':gai_number'=>$memberRs['memberInfo']['gai_number']));
            	if (empty($memberInfo)) {
            		$this->_error('用户不存在，或者用户资料不完善，禁止使用。');
            	}
            	
            	//删除token
            	PartnerToken::destoryToken($memberInfo['id']);
            	
                $rs = Partners::model()->find('member_id=:mid', array(':mid' => $memberInfo['id']));
                
                if ($this->getParam('onlyTest')==1 && !empty($rs)) {
                	var_dump($rs['member_id']);
                }
                
                if (!empty($rs) && $rs->status == Partners::STATUS_ENABLE) {
                    $lang = isset($_POST['Language'])?$this->getParam('Language'):HtmlHelper::LANG_ZH_CN;
                    switch($lang){
                        case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
                        case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
                        case HtmlHelper::LANG_EN : $lang= 'en';break;
                    }
                    $token = md5($memberInfo['id'] + time() + mt_rand(0, 9));
                    $partner_token = new PartnerToken();
                    $partner_token->member_id = $memberInfo['id'];
                    $partner_token->gai_number = $memberInfo['gai_number'];
                    $partner_token->token = $token;
                    $partner_token->create_time = time();
                    $partner_token->expir_time = strtotime("1 month");
                    $partner_token->lang = $lang;
                    $partner_token->version = $version;
                    $partner_token->device_id = $deviceId;
                    if($partner_token->save()){
                    	ClientToken::destoryToken($partner_token->member_id);
                    	
                    	//盖付通同步登陆   获取token
                    	$apitoken = new ApiToken();
                    	$ctoken = $apitoken->getTokenByGaiNumber($memberInfo['gai_number']);
                    	if ($ctoken) {
                    		$client_token = new ClientToken();
                    		$client_token->member_id = $memberInfo['id'];
                    		$client_token->gai_number = $memberInfo['gai_number'];
                    		$client_token->token = $ctoken;
                    		$client_token->create_time = time();
                    		$client_token->expir_time = strtotime("1 month");
                    		$client_token->device_id = $deviceId;
                    		$client_token->save();
                    	}else{
                    		$client_token = new ClientToken();
                    	}
                    	
                    	$this->_success(array('token'=>$token,'gaiNumber'=>$memberInfo['gai_number'],'skuNumber'=>$memberInfo['sku_number'],'mobile'=>$memberInfo['mobile'],'username'=>$memberInfo['username'],'gaiToken'=>$client_token['token']),'GetToken');
                    }
                    
                } elseif (!empty($rs)  && $rs->status != Partners::STATUS_ENABLE) {
                    $this->_error(Yii::t('apiModule.member','商家正在审核中或审核不通过'));
                } else {
                    $this->_error(Yii::t('apiModule.member','该用户下不存在商家！'));
                }
            } else {
                $this->_error(isset($memberRs['error'])?$memberRs['error']:Yii::t('apiModule.member','用户名或密码错误'));
            }
        }else{
            $this->_error(Yii::t('apiModule.member','参数错误'));
        }
    }


    /**
     * 商家申请注册接口
     * 添加合作商家及网签申请
     * userName,password,token，mobile，head，province_id，city_id，district_id，street，zip_code，bank_account_name，bank_card_img，bank_name，bank_account_branch，
     * bank_area，idcard，idcard_img_font，idcard_img_back，bank_account，name，bank_province_id，bank_city_id
    */
    public function actionRegister(){
        try{
            $post = $this->getParams();
            if ($this->getParam('onlyTest')==1) {
                $gai_number = $this->getParam('userName');
                $password = $this->getParam('password');
            }else{
                $gai_number = $this->rsaObj->decrypt($this->getParam('userName'));
                $password = $this->rsaObj->decrypt($this->getParam('password'));
            }

            $res = Partners::model()->find('gai_number = :num',array(':num'=>$gai_number));
            if($res){
                $this->_error('此盖网号已提交商家申请');
            }

            if (!empty($gai_number) && !empty($password)) {
                $ApiModel = new ApiMember();
                $memberRs = $ApiModel->login($gai_number, $password);
                if ($memberRs['success']==true) {
                    $memberInfo = Member::getMemberInfoByGaiId($memberRs['memberId']);
                    
                    $model = new Partners();
                    $model->scenario = 'sellerSign';
                    $model->attributes = $post;
                    $model->member_id = $memberInfo['id'];
                    $model->gai_number = $memberInfo['gai_number'];
                    $model->status = Partners::STATUS_APPLY;
                    $model->create_time = time();
                    if(isset($post ['license_expired_time'])) $model->license_expired_time = strtotime($post ['license_expired_time']);
                    if(isset($post ['meat_inspection_expired_time'])) $model->meat_inspection_expired_time = strtotime($post ['meat_inspection_expired_time']);
                    if(isset($post ['health_permit_expired_time'])) $model->health_permit_expired_time = strtotime($post ['health_permit_expired_time']);
                    if(isset($post ['food_circulation_expired_time'])) $model->food_circulation_expired_time = strtotime($post ['food_circulation_expired_time']);
                    if(isset($post ['stock_source_expired_time'])) $model->stock_source_expired_time = strtotime($post ['stock_source_expired_time']);
                    $saveDir = 'partners/' . date('Y/n/j');
                    $signSaveDir = 'sellerSign/' . date('Y/n/j');
                    $model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'));
                    $model = UploadedFile::uploadFile($model, 'bank_card_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    $model = UploadedFile::uploadFile($model, 'idcard_img_font', $signSaveDir, Yii::getPathOfAlias('att'));
                    $model = UploadedFile::uploadFile($model, 'idcard_img_back', $signSaveDir, Yii::getPathOfAlias('att'));
                    if(isset($post ['license_img'])) $model = UploadedFile::uploadFile($model, 'license_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    if(isset($post ['meat_inspection_certificate_img'])) $model = UploadedFile::uploadFile($model, 'meat_inspection_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    if(isset($post ['health_permit_certificate_img'])) $model = UploadedFile::uploadFile($model, 'health_permit_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    if(isset($post ['food_circulation_permit_certificate_img'])) $model = UploadedFile::uploadFile($model, 'food_circulation_permit_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    if(isset($post ['stock_source_certificate_img'])) $model = UploadedFile::uploadFile($model, 'stock_source_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    $model->bank_area = Region::getName($model->bank_province_id,$model->bank_city_id);
                    $trans = Yii::app()->db->beginTransaction();
                    if ($model->save()){
                        UploadedFile::saveFile('head', $model->head);
                        UploadedFile::saveFile('bank_card_img', $model->bank_card_img);
                        UploadedFile::saveFile('idcard_img_font', $model->idcard_img_font);
                        UploadedFile::saveFile('idcard_img_back', $model->idcard_img_back);
                        if(isset($post ['license_img'])) UploadedFile::saveFile('license_img', $model->license_img);
                        if(isset($post ['meat_inspection_certificate_img'])) UploadedFile::saveFile('meat_inspection_certificate_img', $model->meat_inspection_certificate_img);
                        if(isset($post ['health_permit_certificate_img'])) UploadedFile::saveFile('health_permit_certificate_img', $model->health_permit_certificate_img);
                        if(isset($post ['food_circulation_permit_certificate_img'])) UploadedFile::saveFile('food_circulation_permit_certificate_img', $model->food_circulation_permit_certificate_img);
                        if(isset($post ['stock_source_certificate_img'])) UploadedFile::saveFile('stock_source_certificate_img', $model->stock_source_certificate_img);
                        ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'申请成为网签商家:'.$model->name.'| id->'.$model->id);
                        $trans->commit();
                        $this->_success( Yii::t('partner','商家申请提交成功'));
                    }else{

                        $trans->rollback();
                        $this->_error(Yii::t('partner','商家申请提交失败'));
                    }

                }else{
                    $this->_error('商户网签申请登录失败');
                }
            }else{
                $this->_error('参数错误！');
            }

        }catch (Exception $e){
            $this->_error($e->getMessage());
        };

    }
    
    /**
     * 注销
     * 清除token 和 缓存
     */
    public function actionLogout(){
    	Tool::cache($this->partner_cache_by_partner_id_path)->set($this->partner,null);
    	Tool::cache($this->partner_cache_path)->set($this->member,null);
    	
           $delete = PartnerToken::destoryToken($this->member);
           if(!empty($delete)){
               $this->_success(Yii::t('apiModule.member','注销成功'));
           }else{
              $this->_error(Yii::t('apiModule.member','注销失败'));
           }
    }

    /**
     * 商家登录设置语言
     */
    public function actionSetLanguage(){
        if($_POST['Language']){
            $this->_success(Yii::t('apiModule.member','设置语言成功'));
        }

    }
    
    /**
     * 商家登录设置语言
     */
    public function actionGetGaiToken(){
    	//查询盖付通token
        $gaiNumber = $this->getParam('gaiNumber');//空中充值传入用户的盖网号，获取用户盖付通token
        if(!empty($gaiNumber)) {
            $apitoken = new ApiToken();
            $ctoken = $apitoken->getTokenByGaiNumber($gaiNumber);
            if($ctoken){
                $rs = array();
                $rs['token'] = $ctoken;
                $this->_success($rs['token']);
            }else{
                $this->_error('获取失败');
            }
        }
    	$client_token = ClientToken::model()->find('member_id=:member_id',array(':member_id'=>$this->member));
    	if (empty($client_token)) {
    		//盖付通同步登陆   获取token
    		$apitoken = new ApiToken();
    		$ctoken = $apitoken->getTokenByGaiNumber($this->userInfo['gai_number']);
    		if ($ctoken) {
    			ClientToken::destoryToken($this->member);
    			$client_token = new ClientToken();
    			$client_token->member_id = $this->member;
    			$client_token->gai_number = $this->userInfo['gai_number'];
    			$client_token->token = $ctoken;
    			$client_token->create_time = time();
    			$client_token->expir_time = strtotime("1 month");
    			$client_token->save();
    		}else{
    			$this->_error('获取失败');
    		}
    	}
    	
    	$this->_success($client_token['token']);
    }
    
    

    /**
     * 获取商家信息
     */
    public function actionGetInfo(){
    	$member_info = array();
    	$member_info['name'] = $this->partnerInfo['name'];
    	$member_info['gai_number'] = $this->partnerInfo['gai_number'];
    	$member_info['mobile'] = $this->partnerInfo['mobile'];
    	$member_info['head'] = ATTR_DOMAIN.DS. $this->partnerInfo['head'];
    	$member_info['status'] = $this->partnerInfo['status'];
    	$member_info['score'] = $this->partnerInfo['score'];
    	$member_info['bank_account'] = $this->partnerInfo['bank_account'];
    	$member_info['bank_account_name'] = $this->partnerInfo['bank_account_name'];
    	$member_info['bank_name'] = $this->partnerInfo['bank_name'];
    	$member_info['bank_account_branch'] = $this->partnerInfo['bank_account_branch'];
    	$member_info['bank_area'] = $this->partnerInfo['bank_area'];
    	$member_info['idcard'] = $this->partnerInfo['idcard'];
    	
    	
    	
    	 
    	$this->_success($member_info);
    	
    }

}
