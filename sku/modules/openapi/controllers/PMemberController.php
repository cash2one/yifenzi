<?php
/**
 * 用户控制器
 *
 * 包含用户地址管理、订单列表、订单管理等功能
 *
 * @author leo8705
 *
 */

class PMemberController extends POpenAPIController{

    /**
     * 登录接口
     * @param array stock
     * @param array $goods_id
     */
    public function actionLogin() {

        $this->params = array('userName','password','source');
        $requiredFields = array('userName','password');
        $decryptFields = array('userName','password');
        $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields);
        $gai_number =$post['userName'];
        $password =$post['password'];
        if (!empty($gai_number) && !empty($password)) {
            $model = new ApiMember();
            $memberRs = $model->login($gai_number, $password);

            if ($memberRs['success']==true) {
                //生成盖机公钥和密钥
                $keyArr =Fun::createRsaKey($gai_number);
                $publicKey = $keyArr['publicKey'];
                $privateKey = $keyArr['privateKey'];
                $publicKeyApk = $keyArr['publicKeyApk'];
                //删除token
                OpenPartnerToken::destoryToken($memberRs['memberId']);
                $rs = Partners::model()->find('member_id=:mid', array(':mid' => $memberRs['memberId']));


                if (!empty($rs) && $rs->status == Partners::STATUS_ENABLE) {
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
                    $partner_token = new OpenPartnerToken();
                    $partner_token->member_id = $memberRs['memberId'];
                    $partner_token->gai_number = $memberInfo['gai_number'];
                    $partner_token->token = $token;
                    $partner_token->create_time = time();
                    $partner_token->expir_time = strtotime("1 month");
                    $partner_token->lang = $lang;
                    $partner_token->private_key = $privateKey;
                    $partner_token->public_key = $publicKey;
                    if($partner_token->save()){
                        $this->_success(array('key'=>$publicKeyApk,'publicKey'=>$publicKey,'token'=>$token),'GetToken');
                    }

                } elseif (!empty($rs)  && $rs->status != Partners::STATUS_ENABLE) {
                    $this->_error(Yii::t('member','商家正在审核中或审核不通过'));
                } else {
                    $this->_error(Yii::t('member','该用户下不存在商家！'));
                }
            } else {
                $this->_error(isset($memberRs['error'])?$memberRs['error']:Yii::t('apiModule.member','用户名或密码错误'));
            }
        }else{
            $this->_error(Yii::t('member','参数错误'));
        }
    }

    /**
     * 商家申请注册接口
     */
    public function actionRegister(){
        try{
            $this->params = array('userName','password','mobile','province_id','head','city_id','district_id','street','zip_code','bank_account_name','bank_card_img','bank_name','bank_account_branch',
                'bank_area','idcard','idcard_img_font','idcard_img_back','bank_account','name','bank_province_id','bank_city_id','license_img','license_expired_time','meat_inspection_certificate_img','meat_inspection_expired_time','health_permit_certificate_img','health_permit_expired_time','food_circulation_permit_certificate_img','food_circulation_expired_time','stock_source_certificate_img','stock_source_expired_time');
            $requiredFields = array('userName','password','mobile','province_id','head','city_id','district_id','street','zip_code','bank_account_name','bank_account_branch',
              'idcard','bank_account','name','bank_province_id','bank_city_id');
                $decryptFields = array('userName','password');
                if (isset($_REQUEST['onlyTest']) && $_REQUEST['onlyTest']==1) {
                    $post = $_REQUEST;
                }else{
                    $post = $this->decrypt($_REQUEST,$requiredFields,$decryptFields,true);

                }

                $gai_number =$post['userName'];
                $res = Partners::model()->find('gai_number = :num',array(':num'=>$gai_number));
               if($res){
                   $this->_error('此盖网号已提交商家申请');
               }
                $password =$post['password'];
            if (!empty($gai_number) && !empty($password)) {

                $ApiModel = new ApiMember();

                $memberRs = $ApiModel->login($gai_number, $password);



                if ($memberRs['success']==true) {
                    $memberInfo = $ApiModel->getInfo($memberRs['memberId']);
                    $model = new Partners();
                    $model->scenario = 'sellerSign';
                    $model->attributes = $post;
                    $model->member_id = $memberRs['memberId'];
                    $model->gai_number = $memberInfo['gai_number'];
                    $model->status = Partners::STATUS_APPLY;
                    $model->create_time = time();
                    $saveDir = 'partners/' . date('Y/n/j');
                    $signSaveDir = 'sellerSign/' . date('Y/n/j');
                    $model = UploadedFile::uploadFile($model, 'head', $saveDir, Yii::getPathOfAlias('att'));
                    $model = UploadedFile::uploadFile($model, 'bank_card_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    $model = UploadedFile::uploadFile($model, 'idcard_img_font', $signSaveDir, Yii::getPathOfAlias('att'));
                    $model = UploadedFile::uploadFile($model, 'idcard_img_back', $signSaveDir, Yii::getPathOfAlias('att'));
                    $model = UploadedFile::uploadFile($model, 'license_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    $model = UploadedFile::uploadFile($model, 'meat_inspection_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    $model = UploadedFile::uploadFile($model, 'health_permit_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    $model = UploadedFile::uploadFile($model, 'food_circulation_permit_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    $model = UploadedFile::uploadFile($model, 'stock_source_certificate_img', $signSaveDir, Yii::getPathOfAlias('att'));
                    $model->bank_area = Region::getName($model->bank_province_id,$model->bank_city_id);
                    $trans = Yii::app()->db->beginTransaction();
                    if ($model->save()){
                        UploadedFile::saveFile('head', $model->head);
                        UploadedFile::saveFile('bank_card_img', $model->bank_card_img);
                        UploadedFile::saveFile('idcard_img_font', $model->idcard_img_font);
                        UploadedFile::saveFile('idcard_img_back', $model->idcard_img_back);
                        UploadedFile::saveFile('license_img', $model->license_img);
                        UploadedFile::saveFile('meat_inspection_certificate_img', $model->meat_inspection_certificate_img);
                        UploadedFile::saveFile('health_permit_certificate_img', $model->health_permit_certificate_img);
                        UploadedFile::saveFile('food_circulation_permit_certificate_img', $model->food_circulation_permit_certificate_img);
                        UploadedFile::saveFile('stock_source_certificate_img', $model->stock_source_certificate_img);
                        ParnetLog::create(ParnetLog::CAT_COMPANY,ParnetLog::logTypeInsert,$model->id,'申请成为网签商家:'.$model->name.'| id->'.$model->id);
                        $trans->commit();
                        $this->_success( Yii::t('partner','商家申请提交成功'));
                    }else{
                       var_dump($model->geterrors()) ;
                        $trans->rollback();
                        $this->_error(Yii::t('partner','商家申请提交失败'));
                    }

                }else{
                    $this->_error('商户网签申请登录失败');
                }
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
        $delete = OpenPartnerToken::destoryToken($this->member);
        if(!empty($delete)){
            $this->_success(Yii::t('member','注销成功'));
        }else{
            $this->_error(Yii::t('member','注销失败'));
        }
    }

}