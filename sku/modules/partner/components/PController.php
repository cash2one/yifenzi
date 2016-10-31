<?php

/**
 * 合作商模块控制器父类
 * @author leo8705
 */
class PController extends Controller {

    public $layout = 'main';
    public $menu = array();
    public $breadcrumbs = array();
    public $assistantId;   //店员id
    public $partner_id;
    public $partnerInfo;
//     public $member_id;
//     public $memberInfo;
    public $fresh_machine_list;
    public $fresh_machine_line;
    public $gameStoreId;//游戏店铺ID

    public $curr_menu_name;   //当前菜单名
    
    public $curr_act_partner_id;		//当前操作商家id
    public $curr_act_member_id;		//当期操作商家的会员id
    
    public function beforeAction($action) {
        parent::beforeAction($action);
        $lang =Yii::app()->user->getState('selectLanguage');
        if(empty($lang)){
            Yii::app()->user->setState('selectLanguage','zh_cn');
            $lang =Yii::app()->user->getState('selectLanguage');
        }
        Yii::app()->language = $lang;
        //判断登录
        if (empty($this->user->id)) {

            $noLogin = $this->params('noLogin');
            if (!in_array($this->id . '/' . $action->id, $noLogin)) {
                $this->redirect('/home/login');
            }
            return;
        }

        $this->partner_id = $this->getSession('partner_id');

        //判断是否合作商
        $this->_checkPartner($action);
        
        //设置当前操作商家
        $this->_setCurrPartner();

        $this->fresh_machine_line = empty($this->fresh_machine_line)?FreshMachine::getLineByPartnerId($this->curr_act_partner_id):$this->fresh_machine_line;
		$this->fresh_machine_list = empty($this->fresh_machine_list)?FreshMachine::getListByPartnerId($this->curr_act_partner_id):$this->fresh_machine_list;
		
        return true;
    }
    
    
    /**
     * 获取运营方下的当前商家
     * 
     * 没有记录则设置当前操作商家为自己
     * 
     */
    protected function _setCurrPartner(){
    	$this->curr_act_partner_id = $this->getSession('curr_act_partner_id');
    	$this->curr_act_member_id = $this->getSession('curr_act_member_id');
    	
    	if (empty($this->curr_act_partner_id) || empty($this->curr_act_member_id)) {
    		
    		$this->curr_act_partner_id = $this->partner_id;
    		$this->curr_act_member_id = $this->partnerInfo['member_id'];
    		
//     		$list = Yii::app()->db->createCommand()
//     		->from(OperatorRelation::model()->tableName().' as t')
//     		->where('t.operator_partner_id='.$this->partner_id)
//     		->order('id DESC')
//     		->queryRow()
//     		;
    		
//     		if (!empty($list)) {
//     			$this->curr_act_partner_id = $list['partner_id'];
//     			$this->curr_act_member_id = $list['member_id'];
//     		}else{
//     			$this->curr_act_partner_id = $this->partner_id;
//     			$this->curr_act_member_id = $this->partnerInfo['member_id'];
//     		}
    		
//     		$this->setSession('curr_act_partner_id',$this->curr_act_partner_id);
//     		$this->setSession('curr_act_member_id',$this->curr_act_member_id);
    		
    	}
    	
    }

    private function _checkPartner($action) {
//     	$partnera = $this->getSession('partnerInfo');
    	$partner = Tool::cache('partnerCache')->get('partnerInfo_'.$this->getUser()->id);
            
        $partner = !empty($partner)?$partner:Partners::model()->find(' member_id=:member_id  ', array(':member_id' => $this->getUser()->id));
        if (!empty($partner)){
            $this->setSession('partner_id', $partner->id);
//            $model = GameStore::model()->findByAttributes(array('gai_number' => $partner->member->gai_number));
//            if($model){
//                $this->setSession('gameStoreId',$model->id);
//                $this->gameStoreId = $this->getSession('gameStoreId');
//            }
        }

        if (!empty($partner) && $partner->status == Partners::STATUS_ENABLE) {
            $this->setSession('partner_id', $partner->id);
//             $this->setSession('partnerInfo', $partner);
            Tool::cache('partnerCache')->set('partnerInfo_'.$this->getUser()->id,$partner);
            $this->partner_id = $partner->id;
            $this->partnerInfo = $partner;
//            $model = GameStore::model()->findByAttributes(array('gai_number' => $partner->member->gai_number));
//            if($model){
//                $this->setSession('gameStoreId',$model->id);
//                $this->gameStoreId = $this->getSession('gameStoreId');
//            }
            return true;
        }

        $noPartner = $this->params('noPartner');
        if (!in_array($this->id . '/' . $action->id, $noPartner)) {
            if (!empty($partner) && $partner->status == Partners::STATUS_APPLY) {
                $this->setFlash('error', Yii::t('partner', '商家资料正在审核中！'));
                $this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/view'));
            } elseif (!empty($partner) && $partner->status == Partners::STATUS_DISABLE) {
                $this->setFlash('error', Yii::t('partner', '商家已禁用！'));
                $this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/view'));
            } elseif (!empty($partner) &&$partner->status == Partners::STATUS_UNPASS) {
                $this->setFlash('error', Yii::t('partner', '商家审核不通过，请重新编辑信息！'));
                 $this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/update'));
            } else {
            	$personalMerchant = $this->getUser()->getState('personalMerchant');
            	if (!empty($personalMerchant)) {
            		$this->setFlash('error', Yii::t('partner', '请先申请成为合作商家！'));
            		$this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/apply'));
            	}else{
            		$this->setFlash('error', Yii::t('partner', '请先申请网签成为合作商家！'));
            		$this->redirect(Yii::app()->createAbsoluteUrl('/partner/partner/sellerSign'));
            	}
            	
                
            }
        }
    }

    /**
     * 超市平台，根据当前控制器，判断右侧菜单的显示与隐藏
     * @param $menuArr
     * @return string
     */
    public function showMenu($menuArr) {
        foreach ($menuArr as $v) {
            $actionArr = explode('/', is_array($v) ? $v['value'] : $v);
            if (isset($actionArr[2]) && $actionArr[2] == $this->id)
                return true;
            if (isset($v['actions']) && is_array($v['actions'])) {
                foreach ($v['actions'] as $k => $action) {
                    $actionArr = explode('/', $k);
                    if ($this->id == $actionArr[0])
                        return true;
                }
            }
        }
        return false;
    }

    /*
     * 游戏店铺权限检查
     */
    public function gameStoreCheck($storeId){
        $model = GameStore::model()->findByPk($this->gameStoreId);
        if(!$model){
            $this->redirect(array('/partner/GameStore/invalid'));
        }
        if($storeId != $this->gameStoreId){
            $this->redirect(array('/partner/GameStore/illegal'));
        }
    }

}
