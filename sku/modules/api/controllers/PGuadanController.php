<?php

/**
 * 商家客户端挂单业务控制器
 * 
 * @author leo8705
 *
 */
class PGuadanController extends PAPIController {

	/**
	 * 获取商家交易积分信息
	 */
	function actionGetAmount(){
		$rs['amount']  = AccountBalance::getPartnerGuadanScorePoolBalance($this->member)*1;
		$rs['point'] = CashHistory::getPoint($rs['amount']);
		
		$rs['amount_xiaofei']  = AccountBalance::getMemberXiaofeiAmount($this->member);
		$rs['point_xiaofei'] = CashHistory::getPoint($rs['amount_xiaofei']);
		
		
		//自动下架售卖积分
		$update_sql = 'UPDATE '.GuadanJifenGoods::model()->tableName().' as t , '.GuadanRule::model()->tableName() .'  as r 
								  SET t.status='.GuadanJifenGoods::STATUS_DISABLE.' 
								  WHERE t.member_id='.$this->member.' AND t.status='.GuadanJifenGoods::STATUS_ENABLE.' AND t.rule_id=r.id AND r.amount>'.$rs['amount'];

		Yii::app()->db->createCommand($update_sql)->execute();
// 		Yii::app()->db->createCommand()->update(GuadanJifenGoods::model()->tableName().' as t , '.GuadanRule::model()->tableName() .'  as r ',
// 				array('t.status'=>GuadanJifenGoods::STATUS_DISABLE),
// 				't.member_id=:member_id AND t.status='.GuadanJifenGoods::STATUS_ENABLE.' AND t.rule_id=r.id AND r.amount>'.$rs['amount'],
// 				array(':member_id'=>$this->member));
		
		GuadanJifenGoods::clearCache($this->member);

		
		$this->_success($rs);
	}

	protected function _checkPoolAmount($amount){
		//绑定和非绑定，优先绑定，不够扣就从非绑定出，两个都没钱就不能买
		$gaiAmount = 0;
		$BindingAccount = AccountBalance::getGuadanCommonAmount(CommonAccount::TYPE_GUADAN_SALE_BINDING);
		if ($BindingAccount<$amount) {
			$gaiAmount =  AccountBalance::getGuadanCommonAmount(CommonAccount::TYPE_GUADAN_SALE_UNBUNDLING);
		}else{
			$gaiAmount = $BindingAccount;
		}
		
		if ($gaiAmount<$amount) {
			$this->_error('挂单资金池不足');
		}
	}

    /**
     * 商家批发下单接口
     */
    public function actionCreateOrder(){
        
        try{
            if ($this->getParam('onlyTest')==1) {
                $score = $this->getParam('score');
            }else{
                $score = $this->rsaObj->decrypt($this->getParam('score'))*1;//购买积分
            }
//             self::_checkPoolAmount(CashHistory::getMoney($score));//检查挂单积分池资金是否充足
            //获取当前积分批发规则详情
            $data = Yii::app()->db->createCommand()
                ->select("gpcd.min_score,gpcd.max_score,gpcd.ratio,gpc.limit_score")
                ->from("{{guadan_partner_config_detail}} gpcd")
                ->leftjoin("{{guadan_partner_config}} gpc","gpc.id = gpcd.partner_config_id")
                ->order("gpcd.min_score ASC")
                ->where("gpc.status = ".GuadanPartnerConfig::STATUS_ENABLE)
                ->queryAll();

            $ruleInfo = Yii::app()->db->createCommand()
                ->select("*")
                ->from("{{guadan_collect}} gc")
                ->where("gc.status = ".GuadanCollect::STATUS_ENABLE)
                ->queryRow();
            if(empty($ruleInfo)) $this->_error("当前未有启用挂单政策！");

            if(!empty($data)){

                foreach($data as $k =>$v){
                    $limitScore = $v['limit_score'];
                    if($v['max_score'] == 0) $v['max_score']=$limitScore;
                  if($score>$v['min_score']  && $score<= $v['max_score']){
                      $cash = CashHistory::getMoney($score);
                      $amount = bcdiv(bcmul($cash,$v['ratio'], 5),100,2);

                  }
                }
                if($limitScore < $score){
                    $this->_error("购买积分已超过商家限额积分");
                }

            }else{
                $this->_error('当前尚无已启用的积分批发政策！');
            }
            $member = $this->member;
            if(empty($member)) $this->_error('用户无效！');

                //售卖积分额度
                $sale_amount = $ruleInfo['sale_amount_bind'] + $ruleInfo['sale_amount_unbind'];
                //总售卖额度
                $amount_tol = $ruleInfo['amount_bind'] + $ruleInfo['amount_unbind'];
                if($amount_tol <= $sale_amount){
                     $this->_error(Yii::t('apiModule.guadan','积分已售完'));
                }
                 if($ruleInfo['amount_unbind'] < ($ruleInfo['sale_amount_unbind'] + $score) && $ruleInfo['amount_bind'] < ($ruleInfo['sale_amount_bind'] + $score)){
                     $this->_error(Yii::t('apiModule.guadan','可售积分不足'));
                }
            //创建订单 
            $res = PifaOrder::_createOrder($member,$amount,$score,$ruleInfo['id']);
           
            if(isset($res['success']) && $res['success'] ){
                $order = $res['data'];
                $this->_success(array('id'=>$order['id'],'code'=>$order['code'],'create_time'=>$order['create_time'],'money'=>$order['amount']));
            }else{
                $this->_error('下单失败！');
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }

    /**
     * 商家批发下单积分支付接口
     */

    public function actionPointPay(){
        try{
//            $codeId = $this->rsaObj->decrypt($this->getParam('codeId'))*1;//订单id号
            $code = $this->rsaObj->decrypt($this->getParam('code'));//订单编号
//            $passWord = $this->rsaObj->decrypt($this->getParam('passWord'))*1;//会员积分支付密码
            $meberId = $this->member;
            $meber = Member::model()->findByPk($meberId);
//            if(!$meber->validatePassword3($passWord)) $this->_error("支付密码错误！");
//            if(!is_numeric($codeId) || $codeId<=0) $this->_error('codeId要大于0');
            $params = array('code'=>$code);
            $res = $this->requestSku($params,'sOrder/guadanPifaPay/',105);
           if($res['resultCode'] == 1){
               $this->_success('支付成功！');
           }else{
               $this->_error($res['resultDesc']);
           }

        }catch (Exception $e){
            $this->_error($e->getMessage());
        }

    }
    
    
    /**
     * 设置积分售卖折扣
     * 
     */
    public function actionSetSellingDiscount(){
    	if ($this->getParam('onlyTest')==1) {
    		$discount = $this->getParam('discount')*1;
    	}else{
    		$discount = $this->rsaObj->decrypt($this->getParam('discount'))*1;
    	}
    	if ($discount>100 && $discount<1) {
    		$this->_error('数值错误');
    	}
        $rs =  Yii::app()->db->createCommand()
            ->select("selling_discount")
            ->from("{{guadan_partner_setting}}")
            ->where("member_id =".$this->member)
            ->queryRow();
        if(empty($rs)){
            $sql = "INSERT INTO {{guadan_partner_setting}} ( member_id,selling_discount) VALUES (".$this->member.",".$discount.")";
            Yii::app()->db->createCommand($sql)->execute();
        }else{
            $update_rs = Yii::app()->db->createCommand()->update(GuadanPartnerSetting::model()->tableName(),
                array('selling_discount'=>$discount),
                'member_id=:member_id',
                array(':member_id'=>$this->member));
        }
        
        GuadanJifenGoods::clearCache($this->member);

    	$this->_success('设置成功');
    }

    /**
     * 积分商品列表
     *
     */
    public function actionJifenGoodsList(){

    	$list = GuadanJifenGoods::getPartnerListByMemberId($this->member);

        //获取商家折扣
        $discount = Yii::app()->db->createCommand()
            ->select("selling_discount")
            ->from("{{guadan_partner_setting}}")
            ->where("member_id =".$this->member)
            ->queryRow();
        $data = array();
        $data['selling_discount'] = $discount['selling_discount']?$discount['selling_discount']:100;
    	if (!empty($list)) {
    		foreach ($list as $k=>$v){
                $v['status_name'] = GuadanJifenGoods::getStatus($v['status']);
                $v['amount_pay'] = bcdiv(bcmul($v['amount_pay'],$data['selling_discount'],5),100,2);
//                $v['amount_sell'] = $v['amount_pay']*$data['selling_discount']/100;//实际售价
                $v['point'] = CashHistory::getPoint($v['amount']);
                $v['point_give'] = CashHistory::getPoint($v['amount_give']);
                if($v['type'] == GuadanRule::NEW_MEMBER){
                    $data['new'][] = $v;
                }else{
                    $data['old'][] = $v;
                }
    		}
    	}
    	
    	$this->_success($data);
    }

    /**
     * 积分商品上架
     */
    public function actionJifenGoodsEnable(){
    	if ($this->getParam('onlyTest')==1) {
        	$id = $this->getParam('id');
        }else{
        	$id = $this->rsaObj->decrypt($this->getParam('id'))*1;//挂单积分商品表id
        }
        if(!is_numeric($id) || empty($id)) $this->_error("参数错误");
        //上架商品前判断交易积分是否足够
        $TradingPoints = AccountBalance::getPartnerGuadanScorePoolBalance($this->member);//查询商家当前的交易积分
        $point = Yii::app()->db->createCommand()
            ->select("gr.amount")
            ->from("{{guadan_jifen_goods}} g")
            ->leftjoin("{{guadan_rule}} gr","g.rule_id = gr.id")
            ->where("g.id = ".$id)
            ->queryRow();
        if($TradingPoints < $point['amount']) $this->_error('交易积分余额不足，积分商品不能上架！');
        $res = Yii::app()->db->createCommand()->update(GuadanJifenGoods::model()->tableName(),
            array('status'=>GuadanJifenGoods::STATUS_ENABLE),
            'member_id=:member_id AND id=:id',
            array(':member_id'=>$this->member,"id"=>$id));
        
        GuadanJifenGoods::clearCache($this->member);
        
     if ($res) {
        	$this->_success('设置成功！');
        }else{
        	$this->_error('设置失败！');
        }


    }

    /**
     * 积分商品下架
     */
    public function actionJifenGoodsDisable(){
        if ($this->getParam('onlyTest')==1) {
        	$id = $this->getParam('id');
        }else{
        	$id = $this->rsaObj->decrypt($this->getParam('id'))*1;//挂单积分商品表id
        }
        if(!is_numeric($id) || empty($id)) $this->_error("参数错误");
        $res = Yii::app()->db->createCommand()->update(GuadanJifenGoods::model()->tableName(),
            array('status'=>GuadanJifenGoods::STATUS_DISABLE),
            'member_id=:member_id AND id=:id',
            array(':member_id'=>$this->member,":id"=>$id));
        
        GuadanJifenGoods::clearCache($this->member);
        if ($res) {
        	$this->_success('设置成功！');
        }else{
        	$this->_error('设置失败！');
        }
        

    }

    /**
     * 当期挂单积分批发规则详情
     */
    public function actionRuleInfo(){
        try{
            //获取当前积分批发规则详情
            $data = Yii::app()->db->createCommand()
                ->select("gpcd.min_score,gpcd.max_score,gpcd.ratio,gpc.limit_score")
                ->from("{{guadan_partner_config}} gpc")
                ->leftjoin("{{guadan_partner_config_detail}} gpcd","gpc.id = gpcd.partner_config_id")
                ->order("gpcd.min_score ASC")
                ->where("gpc.status = ".GuadanPartnerConfig::STATUS_ENABLE)
                ->queryAll();
            if(!empty($data)){
                foreach($data as $k => $v){
                    if($v['max_score'] == 0){
                        $data[$k]['max_score'] = $v['limit_score'];
                    }
                }
                $this->_success($data);
            }else{
                $this->_error('当前尚未开启积分批发规则政策');
            }
        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }

    /**
     *空中充值列表
     */
    public function actionAirRechargeList(){
        $tag = 'officialScoreList';
        if ($this->getParam('onlyTest')==1) {
            $skuNumber = $this->getParam('skuNumber');
        }else{
            $skuNumber = $this->rsaObj->decrypt($this->getParam('skuNumber'));//会员号
        }

        $skuNumber = trim($skuNumber);
        
        //获取当前积分批发规则详情
        $member = Yii::app()->db->createCommand()
            ->select("id")
            ->from("{{member}}")
            ->where("sku_number = '".$skuNumber."' OR gai_number ='".$skuNumber."' OR mobile ='".$skuNumber."'")
            ->queryRow();

        if(empty($member)) $this->_error("会员号错误");
        //查询商家交易积分，如果有则显示商家的积分商品，没有则显示官方的积分商品
        $TradingPoints = AccountBalance::getPartnerGuadanScorePoolBalance($this->member);
        $time = time();
        $officalList = Yii::app()->db->createCommand()
            ->select("t.*")
            ->from(GuadanRule::model()->tableName()." as t")
            ->leftJoin(GuadanCollect::model()->tableName().' as g', 't.collect_id=g.id')
            ->where("t.status =".GuadanRule::STATUS_ENABLE.' AND g.status='.GuadanCollect::STATUS_ENABLE.' AND g.time_start<='.$time.' AND g.time_end>='.$time)
            ->order("amount")
            ->queryAll();

        if(empty($officalList)) $this->_error("当前没有可用的积分购买规则");
        //如果有可用的积分购买规则且商家交易积分大于商家最小积分商品的积分值，显示商家积分商品 否则显示官方积分商品
         if($TradingPoints >= $officalList[0]['amount']){
            $rs = GuadanJifenGoods::getFromatListByMemberId($this->member);
            if(empty($rs)) {
                $list = $officalList;
                if (!empty($list)) {
                    foreach ($list as $val){
                        $val['point'] = CashHistory::getPoint($val['amount']);
                        $val['point_give'] = CashHistory::getPoint($val['amount_give']);
                        if ($val['type']==GuadanRule::NEW_MEMBER) {
                            $rs['new'][] = $val;
                        }else{
                            $rs['old'][] = $val;
                        }
                    }
                    $rs['type'] = GuadanJifenOrder::TYPE_AIR_RECHARGE_OFFICAL;
                }
            }else{
                $rs['type'] = GuadanJifenOrder::TYPE_AIR_RECHARGE_PARTNER;
            }
         }else{
            $list = $officalList;
            if (!empty($list)) {
                foreach ($list as $val){
                	$val['point'] = CashHistory::getPoint($val['amount']);
                    $val['point_give'] = CashHistory::getPoint($val['amount_give']);
                    if ($val['type']==GuadanRule::NEW_MEMBER) {
                        $rs['new'][] = $val;
                    }else{
                        $rs['old'][] = $val;
                    }
                }
                $rs['type'] = GuadanJifenOrder::TYPE_AIR_RECHARGE_OFFICAL;
          }
        }
        //查询用户状态
        $is_old = GuadanJifenOrder::getOrderNums($member['id']);
        $data = array();
        $data['selling_discount'] =  isset($rs['selling_discount'])?$rs['selling_discount']:100;

        if ($is_old) {
            isset($rs['old'])?$rs['old']:$rs['old']=array();
            $data['data'] = $rs['old'];
            unset($rs['new']);
            if(empty($rs['old'])){
                $list = $officalList;
                if (!empty($list)) {
                    foreach ($list as $val){
                        $val['point'] = CashHistory::getPoint($val['amount']);
                        $val['point_give'] = CashHistory::getPoint($val['amount_give']);
                        if ($val['type']==GuadanRule::NEW_MEMBER) {
                            $rs['new'][] = $val;
                        }else{
                            $rs['old'][] = $val;
                        }
                    }
                    $rs['type'] = GuadanJifenOrder::TYPE_AIR_RECHARGE_OFFICAL;
                }
                unset($rs['new']);
                $data['data'] = $rs['old'];
            }
        }else{
            isset($rs['new'])?$rs['new']:$rs['new']=array();
            $data['data'] = $rs['new'];
            unset($rs['old']);
            if(empty($rs['new'])){
                $list = $officalList;
                if (!empty($list)) {
                    foreach ($list as $val){
                        $val['point'] = CashHistory::getPoint($val['amount']);
                        $val['point_give'] = CashHistory::getPoint($val['amount_give']);
                        if ($val['type']==GuadanRule::NEW_MEMBER) {
                            $rs['new'][] = $val;
                        }else{
                            $rs['old'][] = $val;
                        }
                    }
                    $rs['type'] = GuadanJifenOrder::TYPE_AIR_RECHARGE_OFFICAL;
                }
                unset($rs['old']);
                $data['data'] = $rs['new'];
            }
        }
        $data['type'] = $rs['type'];
        $data_rs['pointGoodsList'] = $data;
        $data_rs['cash'] = AccountBalance::getMemberBalance($this->member);
        if(empty($data_rs['cash'])) $data_rs['cash']= 0;
        $this->_success($data_rs,$tag);
    }

    /**
     * 空中充值下单接口
     */
    public function actionAirRechargeOrder(){
        try{
            if ($this->getParam('onlyTest')==1) {
                $goodsId = $this->getParam('goodsId');
                $quantity = $this->getParam('quantity');
                $type = $this->getParam('type');
                $skuNumber = $this->getParam('skuNumber');
            }else{
                $goodsId = $this->rsaObj->decrypt($this->getParam('goodsId'));					//积分商品id
                $quantity = $this->rsaObj->decrypt($this->getParam('quantity'));			//数量
                $type = $this->rsaObj->decrypt($this->getParam('type'));
                $skuNumber = $this->rsaObj->decrypt($this->getParam('skuNumber'));//被充值的消费者会员号
            }


            //获取当前积分批发规则详情
            $member = Yii::app()->db->createCommand()
                ->select("id,gai_number")
                ->from("{{member}}")
                ->where("sku_number = '".$skuNumber."' OR gai_number ='".$skuNumber."' OR mobile ='".$skuNumber."'")
                ->queryRow();
            if(empty($member)) $this->_error("会员号错误");
            if ($type==GuadanJifenOrder::TYPE_AIR_RECHARGE_OFFICAL) {
                //获取商品政策规则
                $rule = Yii::app()->db->createCommand()
                    ->from(GuadanRule::model()->tableName().' as t')
                    ->select('t.*,t.id as rule_id')
                    ->where('t.id=:t_id AND t.status=:status',array(':t_id'=>$goodsId,':status'=>GuadanRule::STATUS_ENABLE))
                    ->queryRow();
            }elseif ($type==GuadanJifenOrder::TYPE_AIR_RECHARGE_PARTNER){
                //获取商品政策规则
                $rule = Yii::app()->db->createCommand()
                    ->from(GuadanJifenGoods::model()->tableName().' as t')
                    ->select('r.*,t.member_id,t.partner_id,t.id as id,t.rule_id')
                    ->leftJoin(GuadanRule::model()->tableName().' as r', 't.rule_id=r.id')
                    ->where('t.id=:t_id',array(':t_id'=>$goodsId))
                    ->queryRow();

            }else{
                $this->_error(Yii::t('apiModule.guadan','错误类型'));
            }
             $collect = GuadanCollect::model()->findByPk($rule['collect_id']);
            if (empty($rule)) {
                $this->_error(Yii::t('apiModule.guadan','积分商品不存在'));
            }

            //判断金额余额
            if ($rule['amount_limit']!=0 && ($rule['amount_limit']-$rule['sale_amount'])<$rule['amount']*$quantity) {
                $this->_error(Yii::t('apiModule.guadan','可售余额不足'));
            }
             //售卖积分额度
        $sale_amount = $collect['sale_amount_bind'] + $collect['sale_amount_unbind'];
        //总售卖额度
        $amount = $collect['amount_bind'] + $collect['amount_unbind'];
        if($amount <= $sale_amount){
             $this->_error(Yii::t('apiModule.guadan','积分已售完'));
        }
        if($collect['amount_unbind'] < ($collect['sale_amount_unbind'] + $rule['amount']*$quantity) && $collect['amount_bind'] < ($collect['sale_amount_bind'] + $rule['amount']*$quantity)){
             $this->_error(Yii::t('apiModule.guadan','可售积分不足'));
        }


            //判断是否新用户，新用户只能买一个新用户专享商品
            $is_old = GuadanJifenOrder::model()->count('member_id=:member_id AND status=:status',array(':member_id'=>$member['id'],':status'=>GuadanJifenOrder::STATUS_PAY));

            if ($is_old && $rule['type']==GuadanRule::NEW_MEMBER) {
                $this->_error(Yii::t('apiModule.guadan','老用户不能购买此积分商品'));
            }

            if (!$is_old && $rule['type']==GuadanRule::OLD_MEMBER) {
                $this->_error(Yii::t('apiModule.guadan','新用户不能购买此积分商品'));
            }

            if ($rule['type']==GuadanRule::NEW_MEMBER && $quantity>1){
                $this->_error(Yii::t('apiModule.guadan','新用户专享积分只能购买一个'));
            }

            if ($type==GuadanJifenOrder::TYPE_AIR_RECHARGE_PARTNER) {
                //查询商家资金池
                $seller_amount  = AccountBalance::getPartnerGuadanScorePoolBalance($rule['member_id']);

                if ($seller_amount<($rule['amount']*$quantity)) {
                    $this->_error(Yii::t('apiModule.guadan','此商家可售积分不足'));
                }

                $order = GuadanJifenOrder::_createOrder($member['id'], $goodsId, $rule, $quantity,$type,$this->member);

            }elseif ($type==GuadanJifenOrder::TYPE_AIR_RECHARGE_OFFICAL){
                //官方直接下单
                $order = GuadanJifenOrder::_createOfficalOrder($member['id'], $rule, $quantity,$type,$this->member);
            }
            if ($order['success']==true) {
                $rs_arr['code'] = $order['data']['code'];
                $rs_arr['id'] = $order['data']['id'];
                $rs_arr['total_price'] = $order['data']['total_price'];
                $rs_arr['amount'] = $order['data']['buy_amount'];
                $rs_arr['point'] = CashHistory::getPoint($rs_arr['amount']);
                $rs_arr['gai_number'] = $member['gai_number'];
                $this->_success($rs_arr);
            }else{
                $this->_error(Yii::t('apiModule.guadan','下单失败'));
            }
        }catch (Exception $e){
         $this->_error($e->getMessage());
       }


    }

    /**
     * 空中充值充值积分为商家积分商品时支付调用积分转赠接口
     */
    public function actionPointsDonation(){
        try{
            if ($this->getParam('onlyTest')==1) {
                $code = $this->getParam('code');
                $passWord = $this->getParam('passWord');
            }else{
                $code = $this->rsaObj->decrypt($this->getParam('code'));//订单编号
                $passWord = $this->rsaObj->decrypt($this->getParam('passWord'))*1;//会员积分支付密码
            }

            $meberId = $this->member;
            $member = Member::model()->findByPk($meberId);
            $gaiNumber = isset($member['gai_number'])?$member['gai_number']:'';
            Member::syncPassword($gaiNumber);
            $member = Member::model()->findByPk($meberId);
            if(!$member->validatePassword3($passWord)) $this->_error("支付密码错误！");
            $params = array('code'=>$code);
            $res = $this->requestSku($params,'sOrder/guadanPointOrderPay/',105);
            if($res['resultCode'] == 1){
                $this->_success('支付成功！');
            }else{
                $this->_error($res['resultDesc']);
            }

        }catch (Exception $e){
            $this->_error($e->getMessage());
        }
    }
}
