<?php

/**
 * 商家客户端账户接口控制器
 * 
 * @author leo8705
 *
 */
class CAccountController extends CAPIController {

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
    	$this->_success($list);
    }
    
    
    /**
     * 个人消费金额变动记录表
     */
    public function actionConsumeAmountMonthFlow(){
    	$date = $this->getParam('date','');
    	$time = strtotime($date.'01');
    	$time = !empty($time)?$time:time();
    	
    	$page = $this->getParam('page',1)*1;
    	$pageSize = $this->getParam('pageSize',10)*1;
    	
    	$list = Yii::app()->ac->createCommand()
    	->select('sku_number,date,create_time,debit_amount,credit_amount,order_code,remark,current_balance,flag,transaction_type,operate_type')
    	->from(AccountFlow::monthTable($time))
    	->where('type='.AccountFlow::TYPE_CONSUME.' AND account_id='.$this->member.' AND flag=0 ')
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
    	$rs['thisMonthBalance']=0;
    	$rs['lastMonthBalance']=0;
    	
    	$this->_success($rs,'pointsDetailsList');
    	
    }
    
    
    
    
}
