<?php

/**
 * 商家客户端账户接口控制器
 * 
 * @author leo8705
 *
 */
class PAccountController extends PAPIController {

    /**
     * 申请提现
     * @param array stock
     * @param array $goods_id
     */
    public function actionApplyCash() {
    	if ($this->getParam('onlyTest')==1) {
//     		$accountId = $this->getParam('accountId');
    		$money = $this->getParam('money');
    	}else{
//     		$accountId = $this->rsaObj->decrypt($this->getParam('accountId'));
    		$money = $this->rsaObj->decrypt($this->getParam('money'));
    		
    	}
    	
    	if (empty($money) || $money<=0) {
    		$this->_error(Yii::t('apiModule.member','金额错误'));
    	}
    	
    	
    	if (empty($this->partnerInfo['bank_account_name']) || empty($this->partnerInfo['bank_account']) || empty($this->partnerInfo['bank_name']) || empty($this->partnerInfo['bank_area'])) {
    		$this->_error(Yii::t('apiModule.member','提现账户资料不足，请联系客服。'));
    	}

    	//查询现金余额
    	$cash = AccountBalance::getShangJiaCashBalance($this->member);;

    	if ($cash<$money) {
    		$this->_error(Yii::t('apiModule.member','可提现金额不足'));
    	}
    	
    	$trsns = Yii::app()->db->beginTransaction();
    	
        $bankAddress  = $this->partnerInfo['bank_area'];
        $bankAddress  = !empty($bankAddress)?$bankAddress:'';
    	$ch = new CashHistory();
    	$ch->code = CashHistory::getCode();
    	$ch->member_id = $this->member;
    	$ch->account_name = $this->partnerInfo['bank_account_name'].' - '.$this->partnerInfo['bank_account_branch'];
    	$ch->applyer = $this->partnerInfo['name'];
    	$ch->bank_name = $this->partnerInfo['bank_name'];
    	$ch->bank_address = $bankAddress;
    	$ch->account = $this->partnerInfo['bank_account'];
    	$ch->score = $money;		//还没转换
    	$ch->money = $money;
    	$ch->apply_time = time();
    	$ch->status = CashHistory::STATUS_APPLYING;
    	$ch->type = CashHistory::TYPE_COMPANY_CASH;
    	$ch->factorage = 0;			//未知
    	$ch->ip = Fun::ip2int($_SERVER["REMOTE_ADDR"]);

    	if ($ch->save()) {
    		$record_id = Yii::app()->db->getLastInsertID();
    		//扣除余额，进入冻结账户
    		$acc_rs = AccountBalance::applyCash($ch->money, $ch->member_id,$ch->code,'商家申请提现');
    		
    		if (!$acc_rs) {
    			$trsns->rollback();
    			$this->_error(Yii::t('apiModule.member','申请失败'));
    		}
    		
    		$trsns->commit();
    		$this->_success(Yii::t('apiModule.member','申请成功'));
    	}else{
//            var_dump($ch->getErrors());exit;
    		$trsns->rollback();
    		$this->_error(Yii::t('apiModule.member','申请失败，请重试'.$ch->getErrors()));
    	}
    	
    }
    
    
    /**
     * 申请提现记录
     * @param array stock
     * @param array $goods_id
     */
    public function actionApplyCashRecord() {
    	if ($this->getParam('onlyTest')==1) {
    		$page = $this->getParam('page');
    		$pageSize = $this->getParam('pageSize');
    	}else{
    		$page = $this->rsaObj->decrypt($this->getParam('page'));
    		$pageSize = $this->rsaObj->decrypt($this->getParam('pageSize'));
    	}
    	
    	$page = $page?$page:1;
    	$pageSize = $pageSize?$pageSize:10;
    	
    	
    	$record = Yii::app()->db->createCommand()
    	->from(CashHistory::model()->tableName())
    	->where('member_id=:member_id',array(':member_id'=>$this->member))
    	->limit($pageSize)
    	->offset($pageSize*($page-1))
    	->queryAll();
    	
    	if (!empty($record)) {
    		$this->_success($record);
    	}else{
    		$this->_error('暂无数据');
    	}
    }
    
    
    
    /**
     * 获取账户余额信息
     * @param array stock
     * @param array $goods_id
     */
    public function actionAccountInfo() {
    	$rs = array();
    	$rs['cash'] = AccountBalance::getShangJiaCashBalance($this->member);
    	$rs['money'] = AccountBalance::getMemberXiaofeiAmount($this->member);
        $rs['jiaoyi'] = AccountBalance::getPartnerGuadanScorePoolBalance($this->member);
        $data = Yii::app()->db->createCommand()
            ->select('id')
            ->from(BankAccount::model()->tableName())
            ->where('member_id = :id', array(':id' => $this->member))
            ->queryRow();

        if(!empty($data)){
            $rs['isBound'] = 1;
        }else{
            $rs['isBound'] = 0;
        }
    	$rs['cash_point'] = CashHistory::getPoint($rs['cash']);
    	$rs['money_point'] = CashHistory::getPoint($rs['money']);
        $rs["total_point"] = $rs['cash_point'] + $rs['money_point'] + $rs['jiaoyi'];
    	$this->_success($rs);
    }
    
    
    /**
     * 商家绑定银行卡
     */
    public function actionBindBankAccount(){
        try{
        	if (isset($_REQUEST['onlyTest'])) {
        		$account_name =$this->getParam('accountName');
        		$mobile = $this->getParam('mobile');
        		$bank_name = $this->getParam('bankName');
                $type = $this->getParam('type');
        		$account = $this->getParam('account');
        		$cardno = $this->getParam('cardno');
        		$sister_bank_name = $this->getParam('sisterBankName');
        		$expire_year = $this->getParam('expireYear');
        		$expire_month = $this->getParam('expireMonth');
                $province_id = $this->getParam('provinceId');
                $city_id = $this->getParam('cityId');
                $district_id = $this->getParam('districtId');
        	}else{
        		$account_name = $this->rsaObj->decrypt($this->getParam('accountName'));
        		$mobile = $this->rsaObj->decrypt($this->getParam('mobile'));
        		$bank_name = $this->rsaObj->decrypt($this->getParam('bankName'));
                $type = $this->rsaObj->decrypt($this->getParam('type'));
        		$account = $this->rsaObj->decrypt($this->getParam('account'));
        		$cardno = $this->rsaObj->decrypt($this->getParam('cardno'));
        		$sister_bank_name = $this->rsaObj->decrypt($this->getParam('sisterBankName'));
        		$expire_year = $this->rsaObj->decrypt($this->getParam('expireYear'));
        		$expire_month = $this->rsaObj->decrypt($this->getParam('expireMonth'));
                $province_id = $this->rsaObj->decrypt($this->getParam('provinceId'));
                $city_id = $this->rsaObj->decrypt($this->getParam('cityId'));
                $district_id = $this->rsaObj->decrypt($this->getParam('districtId'));
        	}
        	
        	
            //判断银行卡是否重复绑定
            $data = Yii::app()->db->createCommand()
                ->select('id')
                ->from(BankAccount::model()->tableName())
                ->where('account = :account AND member_id = :id', array(':account' => $account,':id' => $this->member))
                ->queryRow();
            if($data){
                $this->_error(Yii::t('apiModule.member','银行卡不能重复绑定'));
            }
//             if($data['account_name'] != $account_name){
//                 $this->_error("绑定多张银行卡必须为同一用户名！");
//             }

            $count = Yii::app()->db->createCommand()
                ->select('count(1) as count')
                ->from(BankAccount::model()->tableName())
                ->where('member_id = :id', array(':id' => $this->member))
                ->queryRow();
            
            $model = new BankAccount();
            $model->member_id = $this->member;
            $model->account_name = $account_name;
            $model->mobile = $mobile;
            $model->bank_name = $bank_name;
            $model->type = $type;
            $model->account = $account;
            $model->cardno = $cardno;
            $model->sister_bank_name = $sister_bank_name;
            $model->expire_year = $expire_year;
            $model->expire_month = $expire_month;
            $model->province_id = $province_id;
            $model->city_id = $city_id;
            $model->district_id = $district_id;
            if ($count['count']==0) {
            	$model->is_default = BankAccount::DEFAULT_YES;
            }
            
            $_FILES = Tool::appUploadPic($model);
            if(isset($_POST['licence_image'])){
                $saveDir = 'partnerAccount/' . date('Y/n/j');
                $model = UploadedFile::uploadFile($model, 'licence_image', $saveDir, Yii::getPathOfAlias('att'));
            }
            
            //接口验证银行卡
            $bank_code = ApiBank::getBankCodeByName($model->bank_name);
            if (!empty($bank_code[0])) {
            	$apiBank = new ApiBank();
            	 $check_rs = $apiBank->auth($bank_code[0], $model->account, $model->account_name, $model->mobile, $model->cardno);

            	 if ($check_rs['status']==true) {
            	 	$model->status =  BankAccount::STATUS_PASS;
            	 	$model->auto_status =  BankAccount::AUTO_STATUS_PASS;
            	 }
            }
            
            $rs = $model->save();
//             var_dump($rs,$model->getErrors());exit();
            
            if($model->save()){
                if(isset($_POST['licence_image'])){
                    UploadedFile::saveFile('licence_image', $model->licence_image);
                }


                $this->_success(Yii::t('apiModule.member','添加银行卡成功！'));
            }else{
				$error  = '';
				foreach ($model->getErrors() as $val){
					$error .= $val['0'].' ';
				}
                $this->_error(Yii::t('apiModule.member','添加失败！'.$error));
            }
    
    }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }
    
    /**
     * 解绑银行卡
     */
    public function actionUnbundlingBankAccount(){
    	try{
//     		$id = $this->getParam('id');
            if ($this->getParam('onlyTest')==1) {
                $id = $this->getParam('id');
                $passWord = $this->getParam('passWord');
            }else{
                $id = $this->rsaObj->decrypt($this->getParam('id'));
                $passWord = $this->rsaObj->decrypt($this->getParam('passWord'));
            }
            $meberId = $this->member;
            $member = Member::model()->findByPk($meberId);
            $gaiNumber = isset($member['gai_number'])?$member['gai_number']:'';
            Member::syncPassword($gaiNumber);
            $member = Member::model()->findByPk($meberId);
            if(!$member->validatePassword3($passWord)) $this->_error("支付密码错误！");
    		$model = BankAccount::model()->findByPk($id);
            if($model){
                if($model->delete()){
                    $this->_success(Yii::t('apiModule.member','银行卡解绑成功！'));
                }else{
                    $this->_error(Yii::t('apiModule.member','银行卡解绑失败！'));
                }
            }else{
                $this->_error( Yii::t('apiModule.member','银行卡不存在或已解绑！'));
            }

    	}catch (Exception $e){
    		$this->_error($e->getMessage());
    	}
    
    }
    
    /**
     * 银行卡列表
     */
    public function actionBankAccountList(){
    	try{
    		$list = Yii::app()->db->createCommand()
    		->select('id,bank_name,account,sister_bank_name,is_default,status,type')
    		->from(BankAccount::model()->tableName())
    		->where('member_id= :id', array(':id' => $this->member))
    		->order('is_default DESC')
    		->queryAll();
            if(!empty($list)) {
                foreach($list as $k => $v){
                    $list[$k]['type'] = BankAccount::getType($list[$k]['type']);
                }
            }
    		$this->_success($list);
    	}catch (Exception $e){
    		$this->_error($e->getMessage());
    	}
    
    }
    
    /**
     * 设置默认银行卡
     */
    public function actionSetDefaultBankAccount(){
    	try{
            if ($this->getParam('onlyTest')==1) {
                $id = $this->getParam('id');
            }else{
                $id = $this->rsaObj->decrypt($this->getParam('id'));
            }
    		BankAccount::model()->updateAll(array('is_default' => BankAccount::DEFAULT_NO), 'member_id = :mid AND `is_default` = ' . BankAccount::DEFAULT_YES, array('mid' =>$this->member ));
    		if(BankAccount::model()->updateByPk($id,array('is_default'=>BankAccount::DEFAULT_YES))){

    			$this->_success(Yii::t('apiModule.member','设置成功!'));
    		}else{
    			$this->_error(Yii::t('apiModule.member','设置失败!'));
    		}
    
    	}catch (Exception $e){
    		$this->_error($e->getMessage());
    	}
    }
    
    /**
     * 银行卡详情
     */
    public function actionBankAccountDetails(){
    	try{
            if ($this->getParam('onlyTest')==1) {
                $id = $this->getParam('id');
            }else{
                $id = $this->rsaObj->decrypt($this->getParam('id'));
            }
    		$list = Yii::app()->db->createCommand()
    		->select('id,bank_name,account,sister_bank_name,is_default,type')
    		->from(BankAccount::model()->tableName())
    		->where('id= :id', array(':id' => $id))
    		->queryRow();
            if(!empty($list)) $list['type'] = BankAccount::getType($list['type']);
    		$this->_success($list);
    	}catch (Exception $e){
    		$this->_error($e->getMessage());
    	}
    
    }
    
    
    /**
     * 银行列表
     */
    public function actionBankList(){
    	$list = BankAccount::getBankList();
    	$this->_success($list);
    }
    
    /**
     * 银行卡信息
     */
    public function actionBankCardInfo(){
        if ($this->getParam('onlyTest')==1) {
            $card = $this->getParam('card');
        }else{
            $card = $this->rsaObj->decrypt($this->getParam('card'));
        }
    	$info = BankAccount::getBankCardInfo($card);
    	if ($info) {
    		$this->_success($info);
    	}else {
    		$this->_error('银行卡无效');
    	}
    	
    }
    

    /**
     * 可执行个人认证的银行列表
     */
    public function actionSignableBankList(){
    	$list = ApiBank::getBank();
    	$list = array_values($list);
    	$this->_success($list);
    }
    
    

    /**
     * 商家现金账户变动表
     */
    public function actionCashAmountMonthFlow(){
    	if ($this->getParam('onlyTest')==1) {
    		$date = $this->getParam('date','');
    	}else{
    		$date = $this->rsaObj->decrypt($this->getParam('date'));
    	}
    	
    	$time = strtotime($date.'01');
    	$time = !empty($time)?$time:time();
    	 
    	$page = $this->getParam('page',1)*1;
    	$pageSize = $this->getParam('pageSize',10)*1;
    	 
    	$list = Yii::app()->ac->createCommand()
    	->select('sku_number,date,create_time,debit_amount,credit_amount,order_code,remark,current_balance,flag,transaction_type,operate_type')
    	->from(AccountFlow::monthTable($time))
    	->where('type='.AccountFlow::TYPE_MERCHANT.' AND account_id='.$this->member.' AND flag=0 ')
    	->limit($pageSize)
    	->offset(($page-1)*$pageSize)
    	->order('id DESC')
    	->queryAll()
    	;
    	 
    	 
    	if (!empty($list)) {
    		foreach ($list as $key=>$val){
    			$amount = $val['debit_amount']=='0.00'?$val['credit_amount']:-$val['debit_amount'];
    			$list[$key]['amount'] = $amount;
    			 
    			$list[$key]['transaction'] = AccountFlow::showTransactinnType($val['transaction_type']);
    			$list[$key]['operate'] = AccountFlow::getOperateType($val['operate_type']);
    		}
    	}
    	 
    	$rs['dataList'] = $list;
//     	$rs['thisMonthBalance']=0;
//     	$rs['lastMonthBalance']=0;
    	 
    	$this->_success($rs,'cashAmountMonthFlow');
    	 
    	 
    }


}
