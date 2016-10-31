<?php

class HomeController extends Controller {

    public function actions() {
         return array(
             'captcha' => array(
                 'class' => 'CaptchaAction',
                 'height' => '35',
                 'width' => '70',
                 'minLength' => 4,
                 'maxLength' => 4,
                 'offset' => 2,

             ),
         );
    }

    public function actionIndex() {
        if($this->getSession('assistantId')){
            $this->redirect(array('/partner/assistantManage/defaultShow'));
        }else{
            $this->redirect(array('/partner/store/change'));
        }

    }

    /**
     * 登录
     */
    public function actionLogin() {
        if (Yii::app()->user->id)
            $this->redirect(Yii::app()->homeUrl);
        $this->layout = false;
        $model = new LoginForm;
        $this->performAjaxValidation($model);
        $users = array();
        $msg = '';
        if (isset($_POST['LoginForm'])) {
            $aMember = new ApiMember();
            $logRs = $aMember->login($_POST['LoginForm']['username'],$_POST['LoginForm']['password']);
            if($logRs['success']==true){
            	$memberRs = $logRs['memberInfo'];
            	$memberInfo = Member::getMemberInfoByGaiNumber($memberRs['gai_number']);
                if($memberInfo){
                	$lang = isset($_POST['select_language']) ? $_POST['select_language'] : HtmlHelper::LANG_ZH_CN;
                	switch($lang){
                		case HtmlHelper::LANG_ZH_CN : $lang= 'zh_cn';break;
                		case HtmlHelper::LANG_ZH_TW : $lang= 'zh_tw';break;
                		case HtmlHelper::LANG_EN : $lang= 'en';break;
                	}
                	Yii::app()->user->setState('selectLanguage',$lang);
                	
                	//判断是否企业用户
//                 	if (empty($memberRs['enterprise_id'])) {
// //                 		Yii::app()->user->logout();
//                 		$this->setFlash('error',Yii::t('partnerModule.home', '非盖网企业用户不能使用本平台'));
//                 		$this->redirect(Yii::app()->homeUrl);
//                 	}
                	
                	$this->getUser()->Login($memberInfo['id'],$memberInfo);
                	
                    foreach($memberInfo as $key =>$v){
                        $this->getUser()->setState($key,$v);
                    }

                    $this->setFlash('success',Yii::t('partnerModule.home', '登录成功'));
                    ParnetLog::create(ParnetLog::CAT_LOGIN,ParnetLog::logTypeUpdate,0,'登录成功');
//                     $this->redirect(Yii::app()->homeUrl);
                    $this->redirect($this->createAbsoluteUrl('partner/operChange'));
                }else{
                	$msg = Yii::t('partnerModule.home','登录失败,获取用户信息失败');
                }
            }else{
                $msg = Yii::t('partnerModule.home','登录失败,'.isset($logRs['msg'])?$logRs['msg']:Yii::t('partnerModule.home','账号或用户名错误'));
//                $this->setFlash('error',$msg);
            }
        }
        $this->render('login', array(
            'model' => $model,
            'users' => $users,
        	'msg'=>$msg,
        ));
    }

    public function actionError() {
        $this->layout = 'seller';
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * 退出登录
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(array('/partner/home/login'));
    }

    /**
     * 屏蔽ie6-7
     */
    public function actionNotSupported(){
        $this->layout = false;
        $this->renderPartial('notsupported');
    }
    
    
// 	/**
// 	 * 自动取消售货机超时未提货订单
// 	 *
// 	 * 取消售货机未出货订单
// 	 *
// 	 */
//     public $cparams;
// 	public function autoCancelUnTakeMachineOrder($id=0){
// 				$this->cparams = require_once ConfigDir.DS.'params.php';
// 		$cri = new CDbCriteria();
// 		$cri->select = 't.id,t.code,t.machine_id,t.type';
// 		$cri->with = 'ordersGoods';
	
// 		$cri->compare('t.type', Order::TYPE_MACHINE);
// 		$cri->compare('t.machine_status', Order::MACHINE_STATUS_YES);
// 		$cri->compare('t.pay_status', Order::PAY_STATUS_YES);
// 		$cri->compare('t.status', Order::STATUS_PAY);
// 		$cri->compare('t.is_auto_cancel', Order::IS_AUTO_CANCEL_NO);
	
// 		$cri->addCondition(' t.id>'.$id);
			
// 		$order_config = $this->cparams['order'];
// 		$cri->addCondition(' t.create_time<= '.(time()-$order_config['machineUnTakeAutoCancelTime']));
// 		$cri->order = 't.id';
// 		$order = Order::model()->find($cri);
	
// 		if (!empty($order)) {
// 			$ids = array();
// 			$nums = array();
// 			$project_id = API_PARTNER_VENDING_MACHINE_MODULES_PROJECT_ID;
// 			$outlets = $order->machine_id;
	
// 			foreach ($order->ordersGoods as $g){
// 				$ids[] = $g->gid;
// 				$nums[] = $g->num;
// 			}
	
// 			$cancel_rs = Order::orderCancel($order->code,true,'售货机备货失败，自动取消订单');				//取消订单
// 			$apiMember = new ApiMember();
// 			$member_info = $apiMember->getInfo($order['member_id']);
// 			if ($cancel_rs['success']!=true) {
// 				echo 'error: '.$order->code.' | ';
// 				$rs = Yii::app()->db->createCommand('UPDATE '.Order::model()->tableName().' SET  is_auto_cancel='.Order::IS_AUTO_CANCEL_YES.' , remark= "'.$order->remark.'  用户超时未到售货机取货，自动取消订单失败。"
// 					 WHERE id='.$order->id)->execute();
// 				//发送短信
// 				$apiMember->sendSms($member_info['mobile'], ' 由于您超时未到售货机取货，自动取消订单失败，请联系客服。');
// 			}else{
// 				$rs = Yii::app()->db->createCommand('UPDATE '.Order::model()->tableName().' SET  is_auto_cancel='.Order::IS_AUTO_CANCEL_YES.' , remark= "'.$order->remark.'   用户超时未到售货机取货，自动取消订单成功，货款已退还到您的账户"
// 					 WHERE id='.$order->id)->execute();
	
// 				//通知售货机退货
// 				$order_info = array();
// 				$order_info['orderID'] = $order->code;
// 				$order_info['time'] = time()*1000;
// 				$store = VendingMachine::model()->findByPk($order['machine_id']);
// 				$push_rs = JPushTool::vendingMachinePush($store->device_id,'pushOrderCancel',$order_info);
	
// 				//发送短信
// 				$apiMember->sendSms($member_info['mobile'], ' 由于您超时未到售货机取货，订单自动取消成功，货款已退还到您的账户');
// 			}
	
// 			$this->autoCancelUnTakeMachineOrder($order->id);
	
// 		}else{
// 			return true;
// 		}
			
// 	}
	
	
// 	/**
// 	 * 自动取消售货机未提货订单
// 	 *
// 	 * 取消售货机未出货订单
// 	 *
// 	 */
	public function actionTest(){
		$this->setSession('testKey','testValue');
		
		echo $this->getSession('testKey');
		
	}
    
}
