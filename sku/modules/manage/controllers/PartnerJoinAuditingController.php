<?php

class PartnerJoinAuditingController extends MController {

    public function actionIndex() {
        $model = new PartnerJoinAuditing();
        if (isset($_GET['PartnerJoinAuditing']))
            $model->attributes = $_GET['PartnerJoinAuditing'];
        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $this->performAjaxValidation($model);
        $this->render('apply', array(
            'model' => $model,
        ));
    }

    public function actionApply() {
        $id = $this->getParam('id');
        $model = $this->loadModel($id);
        $gw = $this->getParam('gw');
        $gw = empty($gw) ? '' : $gw;



        $apimodel = new ApiMember();
        //审核不通过
        if ($this->getParam('apply') == 'unpass') {
            $status = PartnerJoinAuditing::STATUS_UNPASS;
            $message = "审核加盟商家 {$model->name} 不通过 ";
            $recordmessage = "审核加盟商家 {$model->name} 不通过 成功：";
            $msg = "尊敬的" . $model->name . "用户,您提交的SKU商户加盟审核不通过!";
            $rong = array($model->name);
            $tmpId = ApiMember::JMS_APPLY_NO;
        }

        //审核通过
        if ($this->getParam('apply') == 'pass') {

            $status = PartnerJoinAuditing::STATUS_ENABLE;
            $message = "审核加盟商家 {$model->name} 通过 ";
            $recordmessage = "审核加盟商家 {$model->name} 通过操作成功：";
            if (isset($_GET['gw']) && empty($gw)) {
                $this->setFlash('error', Yii::t('PartnerJoinAuditing', "请选择需要申请的GW号"));
                $this->redirect(Yii::app()->createUrl("partnerJoinAuditing/update", array("id" => $id)));
                exit;
            }
            //不能直接读取gw的库，以后会做分离，直接用接口类获取信息即可
//             $sql = "select * from " . Member::model()->tableName() . " where mobile = '{$model->mobile}'";
//             $sqlResult = Yii::app()->gw->createCommand($sql)->queryRow();
            //------------------------------------------------------------------------------------------------
            if (!empty($gw)) {
                if($gw =='请选择GW号'){
                    $this->setFlash('error', Yii::t('PartnerJoinAuditing', "请选择需要申请的GW号"));
                $this->redirect(Yii::app()->createUrl("partnerJoinAuditing/update", array("id" => $id)));
                exit;
                }
                $gai_member_info = $apimodel->getInfo($gw);
                if ($gai_member_info['status'] == 2 || $gai_member_info['status'] == 3) {
                    $this->setFlash('error', Yii::t('PartnerJoinAuditing', "该手机号码绑定的GW号已除名或禁用！请联系商城客服！"));
                    $this->redirect(Yii::app()->createUrl("partnerJoinAuditing/index"));
                    exit;
                }
            } else {
                $gai_member_info = $apimodel->getInfo($model->mobile);
                //手机号存在除名 或者删除 的GW号时
                if (isset($gai_member_info['0'])) {
                    $this->setFlash('error', Yii::t('PartnerJoinAuditing', "该手机号码绑定的GW号已除名或禁用！请联系商城客服！"));
                    $this->redirect(Yii::app()->createUrl("partnerJoinAuditing/index"));
                    exit;
                }
                elseif ($gai_member_info['status'] == 2 || $gai_member_info['status'] == 3) {
                    $this->setFlash('error', Yii::t('PartnerJoinAuditing', "该手机号码绑定的GW号已除名或禁用！请联系商城客服！"));
                    $this->redirect(Yii::app()->createUrl("partnerJoinAuditing/index"));
                    exit;
                }
            }

            //未注册过  快速注册
            if (!$gai_member_info) {
                $data["mobile"] = $model->mobile;
                $data["password"] = mt_rand(100000, 999999);
                $data["captcha"] = "000000";
                $data["source"] = 0;
                $data["check"] = true; //如果不需要验证验证码 
                //注册
                $registerRes = $apimodel->register($data);

                if ($registerRes["success"] == false) {
                    $this->setFlash('success', Yii::t('PartnerJoinAuditing', $registerRes["msg"]));
                    $this->redirect(array('partnerJoinAuditing/index'));
                    exit;
                }

                //注册时已经同步了，而且这样的同步写法不对！
//                 //同步gW
//                 $sql = "select * from " . Member::model()->tableName() . " where mobile = '{$model->mobile}'";
//                 $sqlResult = Yii::app()->gw->createCommand($sql)->queryRow();
//                 Member::syncFromGw($sqlResult);
//                 $Select = "select id from " . Member::model()->tableName() . " where gai_member_id = '{$sqlResult['id']}'";
//                 $SelectID = Yii::app()->db->createCommand($Select)->queryScalar();
//------------------------------------------------------------------------------------------------



                $member_info = Member::getMemberInfoByGaiId($registerRes['memberId']);    //获取用户信息，且同时同步的方法  			sku库的用户信息

                $partnersmodel = self::registerPartners($model, $registerRes, $member_info['id']);

//                  if($partnersmodel == FALSE){
//                         $this->setFlash('success', Yii::t('PartnerJoinAuditing', "商家插入失败！"));
//                         $this->redirect(array('partnerJoinAuditing/index'));
//                     }
                $marketmodel = self::registerSupermarkets($model, $registerRes, $partnersmodel, $member_info['id']);
//                if($marketmodel == FALSE){
//                    $this->setFlash('success', Yii::t('PartnerJoinAuditing', "超市门店插入失败！"));
//                         $this->redirect(array('partnerJoinAuditing/index'));
//                }
                if ((count($partnersmodel->errors) > 0) || (count($marketmodel->errors) > 0)) {
                    $deleteM = "delete from " . Member::model()->tableName() . " where id = '{$member_info["id"]}'";
                    $deleteP = "delete from " . Partners::model()->tableName() . " where id = '{$partnersmodel->id}'";
                    $deleteS = "delete from " . Supermarkets::model()->tableName() . " where id = '{$marketmodel->id}'";

                    Yii::app()->gw->createCommand($deleteM)->execute();
                    Yii::app()->db->createCommand($deleteP)->execute();
                    Yii::app()->db->createCommand($deleteS)->execute();

                    $this->setFlash('success', Yii::t('PartnerJoinAuditing', "数据库异常请重新审核"));
                    $this->redirect(array('partnerJoinAuditing/index'));
                    exit;
                }
                $msg = "尊敬的" . $model->name . "用户,您提交的SKU商户加盟审核已通过!您的盖网号为" . $registerRes["memberInfo"]["gai_number"] . "初始登录密码为" . $data["password"] . "!请注意保管好您的密码或到盖象商城重置密码!";
                $rong = array($model->name, $registerRes["memberInfo"]["gai_number"], $data["password"]);
                $tmpId = ApiMember::JMS_APPLY_CHU;
            }
            //原有的GW用户审核
            else {

                $member_info = Member::getMemberInfoByGaiNumber($gai_member_info['gai_number']);   //sku库的用户信息

                $registerRes["memberInfo"]["id"] = $member_info['id'];
                $registerRes["memberInfo"]["gai_number"] = $member_info['gai_number'];
                $registerRes["memberInfo"]["mobile"] = $model->mobile;

//                 //同步gW
//                 Member::syncFromGw($sqlResult);
//                 $Select = "select id from " . Member::model()->tableName() . " where gai_member_id = '{$sqlResult['id']}'";
//                 $SelectID = Yii::app()->db->createCommand($Select)->queryScalar();
                //判断有无注册过商户和超市
                $SelP = "select * from " . Partners::model()->tableName() . " where member_id = '{$member_info['id']}'";
                $SelPResult = Yii::app()->db->createCommand($SelP)->queryRow();
                if (!$SelPResult) {
                    $partnersmodel = self::registerPartners($model, $registerRes, $member_info['id']);
                    $p_errors = '';
                    $m_errors = '';
                    if (count($partnersmodel->errors) > 0) {
                        foreach ($partnersmodel->errors as $v) {
                            $p_errors .= implode(',', $v);
                        }
                        $this->setFlash('error', Yii::t('PartnerJoinAuditing', $p_errors));
                        $this->redirect(array('partnerJoinAuditing/index'));
                    }
                    $marketmodel = self::registerSupermarkets($model, $registerRes, $partnersmodel, $member_info['id']);
                    if (count($marketmodel->errors) > 0) {
                        foreach ($marketmodel->errors as $v) {
                            $m_errors .= implode(',', $v);
                        }
                        $this->setFlash('error', Yii::t('PartnerJoinAuditing', $m_errors));
                        $this->redirect(array('partnerJoinAuditing/index'));
                    }
                } else {
                    $partnersmodel = Partners::model()->findbypk($SelPResult['id']);
                    $SelM = "select * from " . Supermarkets::model()->tableName() . " where member_id = '{$member_info['id']}'";
                    $SelMResult = Yii::app()->db->createCommand($SelM)->queryRow();

                    if (!($SelMResult)) {
                        $errors = '';
                        $marketmodel = self::registerSupermarkets($model, $registerRes, $partnersmodel, $member_info['id']);
                        if (count($marketmodel->errors) > 0) {
                            foreach ($marketmodel->errors as $v) {
                                $errors .= implode(',', $v);
                            }
                            $this->setFlash('error', Yii::t('PartnerJoinAuditing', $errors));
                            $this->redirect(array('partnerJoinAuditing/index'));
                        }
                    }else{
                        $this->setFlash('error', Yii::t('PartnerJoinAuditing', '已审核过的商户，不可重复审核！'));
                            $this->redirect(array('partnerJoinAuditing/index'));
                    }
                }

                $msg = "尊敬的" . $model->name . "用户,您的盖网号" . $registerRes["memberInfo"]["gai_number"] . "通过了SKU商户加盟审核!请沿用此盖网号的密码登录盖象商城!";
                $rong = array($model->name, $registerRes["memberInfo"]["gai_number"]);
                $tmpId = ApiMember::JMS_APPLY;
            }

            self::BindOperatorRelation($model, $partnersmodel, $member_info['id']);
        }



        $updateSql = "update " . PartnerJoinAuditing::model()->tablename() . " set status = '{$status}' where id = {$id}";
        Yii::app()->db->createCommand($updateSql)->execute();

        $apimodel->sendSms($model->mobile, $msg, ApiMember::SMS_TYPE_ONLINE_ORDER, 0, ApiMember::SKU_SEND_SMS, $rong, $tmpId);
        @SystemLog::record(Yii::app()->user->name . Yii::t('PartnerJoinAuditing', $recordmessage) . Yii::t('PartnerJoinAuditing', $model->name));
        $this->setFlash('success', $message);
        $this->redirect(array('partnerJoinAuditing/index'));
    }

    /**
     * 插入商户
     * @param unknown $model
     * @param unknown $registerRes
     * @return Partners
     */
    public static function registerPartners($model, $registerRes, $SelectID) {
        //插入店铺
        $partnersmodel = new Partners();
        $partnersmodel->zip_code = "510000";
        $partnersmodel->create_time = time();
        $partnersmodel->idcard_img_back = "";
        $partnersmodel->private_key = "";
        $partnersmodel->public_key = "";
        $partnersmodel->status = Partners::STATUS_ENABLE;
        $partnersmodel->name = $model->store_name;
        $partnersmodel->street = $model->store_address;
        $partnersmodel->member_id = $SelectID;
        $partnersmodel->gai_number = $registerRes["memberInfo"]["gai_number"];
        $partnersmodel->mobile = $registerRes["memberInfo"]["mobile"];

        $partnersmodel->idcard = $model->id_card;
        $partnersmodel->province_id = $model->store_province_id;
        $partnersmodel->city_id = $model->store_city_id;
        $partnersmodel->district_id = $model->store_district_id;
        $partnersmodel->bank_area = Region::getName($model->bank_province_id, $model->bank_city_id, $model->bank_district_id);
//        $partnersmodel->bank_province_id = $model->bank_province_id;
//        $partnersmodel->bank_city_id = $model->bank_city_id;
//        $partnersmodel->bank_district_id = $model->bank_district_id;
        $partnersmodel->bank_account = $model->bank_account;
        $partnersmodel->bank_name = $model->bank;
        $partnersmodel->bank_account_name = $model->bank_account_name;
        $partnersmodel->bank_account_branch = $model->bank_branch;
        $partnersmodel->bank_card_img = $model->bank_img;
        $partnersmodel->idcard_img_font = $model->id_card_font_img;
        $partnersmodel->idcard_img_back = $model->id_card_back_img;
        $partnersmodel->license_img = $model->license_img;
        $partnersmodel->license_expired_time = $model->license_to_time;

        $partnersmodel->real_name = $model->name;
        $partnersmodel->head = $model->head;
        $partnersmodel->save();
        return $partnersmodel;
    }

    /**
     * 插入超市
     * @param unknown $model
     * @param unknown $registerRes
     * @param unknown $partnersmodel
     * @return Supermarkets
     */
    public static function registerSupermarkets($model, $registerRes, $partnersmodel, $SelectID) {
        //插入超市
        //获取推荐人ID
        $referrals_id = $model->referrals_gai_number == '' ? array() : Member::getMemberInfoByGaiNumber($model->referrals_gai_number);
        $marketmodel = new Supermarkets();
        $marketmodel->partner_id = $partnersmodel->id;
        $marketmodel->zip_code = "510000";
        $marketmodel->open_time = "06:00-24:00";
        $marketmodel->create_time = time();
        $marketmodel->status = Supermarkets::STATUS_ENABLE;
        $marketmodel->mobile = $registerRes["memberInfo"]["mobile"];
        $marketmodel->member_id = $SelectID;
        $marketmodel->name = $model->store_name;
        $marketmodel->street = $model->store_address;
        $marketmodel->temp_goods = $registerRes["memberInfo"]["gai_number"] . "的商品";
        $marketmodel->referrals_id = count($referrals_id) == 0 ? '' : $referrals_id['id'];

        $marketmodel->province_id = $model->store_province_id;
        $marketmodel->city_id = $model->store_city_id;
        $marketmodel->district_id = $model->store_district_id;

        $marketmodel->logo = $model->head;

        $marketmodel->save();
        return $marketmodel;
    }

    /**
     * 审核成功后绑定推荐人关系
     * @param unknown $model=>PartnerJoinAuditing model
     * @param unknown $SelectID 商家会员ID
     */
    public static function BindOperatorRelation($model, $partnersmodel, $SelectID) {
        //获取运营方会员ID和运营方商家ID
        if ($model->gai_number == "") {
            $operator_member_id = 0;
            $operator_partner_id = 0;
        } else {
            $sql = "select id from " . Member::model()->tableName() . " where gai_number = '{$model->gai_number}'";
            $sqlResult = Yii::app()->gw->createCommand($sql)->queryRow();
            if (!($sqlResult)) {
                $operator_member_id = 0;
                $operator_partner_id = 0;
            } else {
                $Select = "select id from " . Member::model()->tableName() . " where gai_member_id = '{$sqlResult['id']}'";
                $operator_member_id = Yii::app()->db->createCommand($Select)->queryScalar();

                $SelP = "select id from " . Partners::model()->tableName() . " where member_id = '{$operator_member_id}'";
                $operator_partner_id = Yii::app()->db->createCommand($SelP)->queryScalar();
            }
        }
        $time = time();

        $insert = "INSERT INTO gw_sku_operator_relation(member_id,partner_id,operator_member_id,operator_partner_id,create_time)
VALUES('{$SelectID}','{$partnersmodel->id}','{$operator_member_id}','{$operator_partner_id}','{$time}')";
        Yii::app()->db->createCommand($insert)->execute();
    }

}
