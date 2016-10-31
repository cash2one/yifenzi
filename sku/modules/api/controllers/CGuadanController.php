<?php
/**
 * 用户端挂单接口控制器
 *
 * @author leo8705
 *
 */

class CGuadanController extends CAPIController {

    /**
     * 官方充值列表
     *
     * 就是政策列表
     *
     */
    function actionOfficialScoreList(){
        $tag = 'officialScoreList';
        $time = time();
         $list = Yii::app()->db->createCommand()
             ->select("t.*")
             ->from(GuadanRule::model()->tableName().' as t')
             ->leftJoin(GuadanCollect::model()->tableName().' as c', 't.collect_id=c.id')
             ->where("t.status =".GuadanRule::STATUS_ENABLE.' AND c.status='.GuadanCollect::STATUS_ENABLE.' AND c.time_start<='.$time.' AND c.time_end>='.$time)
             ->queryAll();
//        $list = GuadanJifenGoods::getOfficleGoods();
        $rs = array();
        if (!empty($list)) {
            foreach ($list as $val){
            	$val['point_give'] = CashHistory::getPoint($val['amount_give']);
            	$val['point'] = CashHistory::getPoint($val['amount']);
                $val['percent'] = ($val['point_give']/$val['point']);
                if ($val['type']==GuadanRule::NEW_MEMBER) {
                    $rs['new'][] = $val;
                }else{
                    $rs['old'][] = $val;
                }
            }

            //查询用户状态
            $is_old = GuadanJifenOrder::getOrderNums($this->member);

            if ($is_old) {
                unset($rs['new']);
            }else{
                unset($rs['old']);
            }

        }

        $data_rs = array();
        if(!empty($rs)) $data_rs['pointGoodsList'] = $rs;
        $this->_success($data_rs,$tag);

    }

    /**
     * 商家充值列表
     */
    function actionPartnerScoreList(){
        $tag = 'partnerScoreList';
        $partner_member_id = $this->getParam('partnerMemberId');
        $list = GuadanJifenGoods::getFromatListByMemberId($partner_member_id);

        if (empty($list)) {
            $this->_error('当前商家没有出售积分');
        }else{
            //查询用户状态
            $is_old = GuadanJifenOrder::getOrderNums($this->member);

            if ($is_old) {
                unset($list['new']);
            }else{
                unset($list['old']);
            }

            //获取商家可卖的积分余额
            $list['partner_amount'] = AccountBalance::getPartnerGuadanBalance($partner_member_id)*1;
        }

        $this->_success($list,$tag);
    }


    /**
     * 下单购买商家积分接口
     */
    function actionBuyPoint(){
        $goodsId = $this->getParam('goodsId')*1;					//积分商品id
        $quantity = $this->getParam('quantity',1)*1;			//数量
        $type = $this->getParam('type',GuadanJifenOrder::TYPE_OFFICAL)*1;
        
        //判断是否购买自己的积分商品
        $goods = GuadanJifenGoods::model()->findByPk($goodsId);
        $member_id = $goods['member_id'];
        if($member_id ==$this->member){
            $this->_error(Yii::t('apiModule.guadan','不能购买自己的积分商品'));
        }
        if ($type==GuadanJifenOrder::TYPE_OFFICAL) {
            //获取商品政策规则
            $rule = Yii::app()->db->createCommand()
                ->from(GuadanRule::model()->tableName().' as t')
                ->select('t.*,t.id as rule_id')
                ->where('t.id=:t_id AND t.status=:status',array(':t_id'=>$goodsId,':status'=>GuadanRule::STATUS_ENABLE))
                ->queryRow();
            $collect = GuadanCollect::model()->findByPk($rule['collect_id']);
        }elseif ($type==GuadanJifenOrder::TYPE_PARTNER){
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



        if (empty($rule)) {
            $this->_error(Yii::t('apiModule.guadan','积分商品不存在'));
        }
        //判断金额余额
        if($rule['amount_limit']!=0 && $rule['amount_limit'] <= $rule['sale_amount']){
            $sql = "update {{guadan_rule}} set status=".GuadanRule::STAUS_FINISHED." where id ={$rule['rule_id']}";
            Yii::app()->db->createCommand($sql)->execute();
            $this->_error(Yii::t('apiModule.guadan','积分包限额已用完'));
        }
        if($type==GuadanJifenOrder::TYPE_OFFICAL){
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
        }
        if ($rule['amount_limit']!=0 && ($rule['amount_limit']-$rule['sale_amount'])<$rule['amount']*$quantity) {
            $this->_error(Yii::t('apiModule.guadan','可售余额不足'));
        }

        //查询用户月度个人购买限额  AdjustCredits
        $AdjustCredits = WebConfig::model()->find('name=:name' ,array(':name'=>'AdjustCredits'));
        $AdjustCredits = $AdjustCredits['value'];
		
        if($AdjustCredits>0){
        	$total_amount_rs = Yii::app()->db->createCommand()
        	->select('sum(buy_amount) as total_amount')
        	->from(GuadanJifenOrder::model()->tableName())
        	->where('member_id='.$this->member .' AND status='.GuadanJifenOrder::STATUS_PAY )
        	->queryRow();
        	;
        	$total_amount = $total_amount_rs['total_amount'];

        	if(($total_amount+$rule['amount']*$quantity)>$AdjustCredits){
        		$this->_error(Yii::t('apiModule.guadan','已超过月度个人购买限额'));
        	}

        }

        //判断是否新用户，新用户只能买一个新用户专享商品
        $is_old = GuadanJifenOrder::model()->count('member_id=:member_id AND status=:status',array(':member_id'=>$this->member,':status'=>GuadanJifenOrder::STATUS_PAY));

        if ($is_old && $rule['type']==GuadanRule::NEW_MEMBER) {
            $this->_error(Yii::t('apiModule.guadan','老用户不能购买此积分商品'));
        }

        if (!$is_old && $rule['type']==GuadanRule::OLD_MEMBER) {
            $this->_error(Yii::t('apiModule.guadan','新用户不能购买此积分商品'));
        }

        if ($rule['type']==GuadanRule::NEW_MEMBER && $quantity>1){
            $this->_error(Yii::t('apiModule.guadan','新用户专享积分只能购买一个'));
        }

        if ($type==GuadanJifenOrder::TYPE_PARTNER) {
            //查询商家资金池
            $seller_amount  = AccountBalance::getPartnerGuadanScorePoolBalance($rule['member_id']);

            if ($seller_amount<($rule['amount']*$quantity)) {
                $this->_error(Yii::t('apiModule.guadan','此商家可售积分不足'));
            }

            $order = GuadanJifenOrder::_createOrder($this->member, $goodsId, $rule, $quantity);

        }elseif ($type==GuadanJifenOrder::TYPE_OFFICAL){
            //官方直接下单
            $order = GuadanJifenOrder::_createOfficalOrder($this->member, $rule, $quantity);
        }
        if ($order['success']==true) {
             $rs_arr['first_consume'] = 0; 
            if($type==GuadanJifenOrder::TYPE_PARTNER){
                 $sql_guadan = 'SELECT id FROM {{guadan_jifen_order}} WHERE member_id = ' . intval($order['data']['member_id']) . ' AND type='.GuadanJifenOrder::TYPE_PARTNER . ' AND code != ' . $order['data']['code'] . ' AND status = ' . GuadanJifenOrder::STATUS_PAY . '  LIMIT 1';
                $ConsumeHistory_guadan = Yii::app()->db->createCommand($sql_guadan)->queryAll();
                
                $sql_order = 'SELECT id FROM {{orders}} WHERE member_id = ' . intval($order['data']['member_id']) .' AND status = ' . Order::STATUS_COMPLETE . '  LIMIT 1';
                $ConsumeHistory_order = Yii::app()->db->createCommand($sql_order)->queryAll();
                 if(empty($ConsumeHistory_guadan)&&empty($ConsumeHistory_order)){
                        $rs_arr['first_consume'] = 1; //首次消费
                 }
            }
                        
            $rs_arr['code'] = $order['data']['code'];
            $rs_arr['id'] = $order['data']['id'];
            $rs_arr['total_price'] = $order['data']['total_price'];
            $rs_arr['amount'] = $order['data']['buy_amount'];
            $rs_arr['point'] = CashHistory::getPoint($rs_arr['amount']);
            $this->_success($rs_arr);
        }else{
            $this->_error(Yii::t('apiModule.guadan','下单失败'));
        }

    }




    /**
     * 商家批发下单积分支付接口
     */

    public function actionPointPay(){
        try{
//            $codeId = $this->rsaObj->decrypt($this->getParam('codeId'))*1;//订单id号
            $code = $this->getParam('code');//订单编号

//            $passWord = $this->rsaObj->decrypt($this->getParam('passWord'))*1;//会员积分支付密码
            $meberId = $this->member;
            $meber = Member::model()->findByPk($meberId);
//            if(!$meber->validatePassword3($passWord)) $this->_error("支付密码错误！");
//            if(!is_numeric($codeId) || $codeId<=0) $this->_error('codeId要大于0');
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
    public function requestSku($params,$url,$project = '105',$api = DOMAIN_API)
    {
        $json = json_encode($params);
        $private_key = $this->_getApiKeys('gw_project',$project);
        $code = md5($json.$private_key);//校验
        $url = $api.'/'.$url;
        $data = array(
            'project'=>$project,
            'data'=>$json,
            'encryptCode'=>$code
        );
        $ch = curl_init() ;
        curl_setopt($ch, CURLOPT_URL,$url) ;
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data); // 在HTTP中的“POST”操作。如果要传送一个文件，需要一个@开头的文件名
        ob_start();
        curl_exec($ch);
        $response = ob_get_contents() ;
        ob_end_clean();
        curl_close($ch) ;
        $res = json_decode($response,true);
        if($res == null)
            throw new Exception($response);
        return $res;
    }

}