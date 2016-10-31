<?php

class GuadanCollectController extends MController
{
    //权限管理
    public function filters()
    {
        return array(
            'rights',
        );
    }
    
    // 售卖管理
    public function actionSellAdmin()
    {
        $model = new GuadanCollect('search');
        //获取商品分类
        $model->unsetAttributes();
        if (isset($_GET['Guadan']))
            $model->attributes = $_GET['Guadan'];
        $this->render('salesAdmin', array(
            'model' => $model,
        ));
    }
    
    /**
     * 挂单详情
     * @param type $id
     */
    public function actionView($id){
        if(is_numeric($id)){
            $collect = GuadanCollect::model()->findByPk($id);
            if(!$collect) throw new CHttpException('404','找不到该挂单');
            $this->render('views',array('model'=>$collect));
        }
    }
    
    public function actionStop($id)
    {
        if(is_numeric($id)){
            $model = GuadanCollect::model()->findByPk($id); //模型
            echo $this->renderPartial('_stop',array('model'=>$model),true);
        }
        //exit(CJSON::encode(array($this->isAjax())));
        //throw new CHttpException('404','找不到页面');
    }
    /**
     * 中止挂单
     */
    public function actionStopSales($id)
    {
        $result = true;
        if(is_numeric($id) && $this->isAjax()){
            //是否存在该挂单
            /** @var GuadanCollect $collect */
            $collect = GuadanCollect::model()->findByPk($id);
            if($collect){
                $this->checkPostRequest();
                //查relation中此售卖的用户信息
                $db = Yii::app()->db;
                $trans = $db->beginTransaction(); //开启事物

                $sql = "SELECT * FROM {{guadan_relation}} WHERE collect_id=" . $collect->id .' FOR UPDATE';
                
                //计算总额  非绑定积分总额
                $amount_unbind_total  = Yii::app()->db->createCommand()
                        ->from('{{guadan_relation}}')
                        ->select('sum(amount) as amount')
                        ->where('collect_id=:collect_id AND type='.GuadanRelation::TYPE_UNBIND,array(':collect_id'=>$collect['id']))
                        ->queryRow();
                $amount_unbind_total = $amount_unbind_total['amount']*1;
                
                //带绑定积分总额
                $amount_tobind_total  = Yii::app()->db->createCommand()
                ->from('{{guadan_relation}}')
                ->select('sum(amount) as amount')
                ->where('collect_id=:collect_id AND type='.GuadanRelation::TYPE_TOBIND,array(':collect_id'=>$collect['id']))
                ->queryRow();
                $amount_tobind_total = $amount_tobind_total['amount']*1;
                
                //批发已售总额
                $pifa_amout = Yii::app()->db->createCommand()
                ->from(PifaOrder::model()->tableName())
                ->select('sum(buy_amount) as amount')
                ->where('collect_id=:collect_id AND status='.PifaOrder::STATUS_PAY,array(':collect_id'=>$collect['id']))
                ->queryRow();
                $amout_tobing = $pifa_amout['amount']*1;
                 
                //官方充值消费总额
                $officel_sell_amout = Yii::app()->db->createCommand()
                ->from(GuadanJifenOrder::model()->tableName().' as t')
                ->leftJoin(GuadanRule::model()->tableName().' as r', 't.rule_id=r.id')
                ->select('sum(t.buy_amount*t.quantity) as amount')
                ->where('r.collect_id=:collect_id AND t.type='.GuadanJifenOrder::TYPE_OFFICAL.' AND t.status='.GuadanJifenOrder::STATUS_PAY,array(':collect_id'=>$collect['id']))
                ->queryRow();
                $amout_unbing = $officel_sell_amout['amount']*1;
                
                $relation = $db->createCommand($sql)->queryAll();
                if(is_array($relation) && !empty($relation)){
                    foreach ($relation as $r){
                        //获取挂单模型
                        $sql = 'SELECT member_id,gai_number FROM {{guadan}} where id='.$r['guadan_id'];
                        $guadan = $db->createCommand($sql)->queryRow();
                        // 更新关系表，余额清零
                        $sql = 'UPDATE {{guadan_relation}} SET amount_remain = 0 WHERE collect_id = ' . $r['collect_id'] .' AND guadan_id = ' . $r['guadan_id'];
                        $db->createCommand($sql)->execute();
                       
                        //积分返回挂单表
                        //中止后的售卖计划不可再次启动，未出售的积分将按出资比例返回至会员挂单中。
                        //已批发积分 为带绑定积分    官方充值的为非绑定积分
                        $return_amount = 0;
                        if ($r['type']==GuadanRelation::TYPE_TOBIND) {
                        	$return_amount = ($amount_tobind_total-$amout_tobing)*($r['amount']/$amount_tobind_total);
                        }elseif ($r['type']==GuadanRelation::TYPE_UNBIND){
                        	$return_amount = ($amount_unbind_total-$amout_unbing)*($r['amount']/$amount_unbind_total);
                        }

                        $sql = 'UPDATE {{guadan}} set amount_remain = amount_remain+ '. $return_amount  .' WHERE id=' .$r['guadan_id'];
                        $db->createCommand($sql)->execute();
                        //流水//////
                        if($r['amount_remain']>0){
                         $collect->stopFlow($guadan['gai_number'],$r['amount_remain'],$r['type']);
                        }
                    }
                    $collect->status = GuadanCollect::STATUS_DISABLE;

                    if($result && $collect->save(false)){
                    	//更新规则表及商品表  清除缓存
                    	$db->createCommand()->update(GuadanRule::model()->tableName(),array('status'=>GuadanRule::STATUS_DISABLE),'collect_id='.$collect->id);
                    	
                    	$update_sql = 'UPDATE '.GuadanRule::model()->tableName().' as r  ,  '.GuadanJifenGoods::model()->tableName().' as g 
                    			 SET g.status='.GuadanJifenGoods::STATUS_DISABLE.' WHERE r.collect_id = ' . $collect->id .' AND g.rule_id = r.id ';
                    	$db->createCommand($update_sql)->execute();
                    	
                    	GuadanJifenGoods::clearCache();
                    	$collect_code = $collect->code;
                        @SystemLog::record("管理员(".$this->getUser()->name.")中止售卖计划<{$collect_code}>",  SystemLog::LOG_TYPE_SHOUMAI);
                        $trans->commit();
                    } else {
                        $result = false;                  
                        $trans->rollback();
                    }                   
                }
                exit(CJSON::encode(array('result'=>$result)));
            }
        }
        throw new CHttpException('403','拒绝访问该页面');
    }
    
    const ADJUST_NAME = 'AdjustCredits';
    
    /**
     * 调整月充值额度
     */
    public function actionAdjust(){
        $config = WebConfig::model()->find('name=:name' ,array(':name'=>  self::ADJUST_NAME));
        if(empty($config)) $config = new WebConfig('adjust');
        if(isset($_POST['adjust'])){
            $config->name = self::ADJUST_NAME;
            $adjust = $_POST['adjust'];
            if($adjust == 0){
               $config->value = 0;
            } else {
               $config->attributes = $_POST['WebConfig'];
            }
            if($config->save()){
                $this->setFlash('success','调整成功');
            } else {
                $this->setFlash('error','调整失败');
            }
            $this->redirect(array('guadanCollect/sellAdmin'));
        }
        echo $this->renderPartial('_adjust',array('model'=>$config));
    }
    
    
    
    
    
    
    public function actionEnablezc($id)
    {
    	//是否存在该挂单
    	$gd = GuadanCollect::model()->find('id=:id',array(':id'=>$id));
    	if (empty($gd)) {
    		$this->setFlash('error','销售政策不存在');
    		$this->redirect(array('guadanCollect/sellAdmin'));
    	}


    	 //启用挂单政策时须检查是否有同时段的已启用的挂单政策

        $startTime = $gd->time_start;
        $endTime = $gd->time_end;
        $data = Yii::app()->db->createCommand()
            ->select("*")
            ->from("{{guadan_collect}}")
            ->where("status =".GuadanCollect::STATUS_ENABLE)
            ->queryAll();
        $status = false;
        if(!empty($data)){
            foreach($data as $k=>$v){
                if(($v['time_start']>=$startTime && $v['time_start']<= $endTime) || ($v['time_end']>=$startTime && $v['time_end']<= $endTime) ||($v['time_start'] <= $startTime && $v['time_end'] >= $startTime) || ($v['time_start'] <= $endTime && $v['time_start'] >= $startTime )){
                    $status = true;
                }
            }
        }
        if($status){
            $this->setFlash('error','该时间段已启用一条售卖计划');
            $this->redirect(array('guadanCollect/sellAdmin'));
        }else{
            if($endTime <= time()){
                $this->setFlash('error','该政策已过期不能启用！');
                $this->redirect(array('guadanCollect/sellAdmin'));
            }
            if($startTime > time()){
                $status = false;
                foreach($data as $k=>$v){
                    if(($v['time_start'] >= time() && $v['time_start'] <= $endTime) || ($v['time_end'] >= time() && $v['time_end']<= $endTime) ||($v['time_start'] <= time() && $v['time_end'] >= time()) || ($v['time_start'] <= $endTime && $v['time_start'] >= time() )){
                        $status = true;
                    }
                }
                if($status){
                    $this->setFlash('error','该时间段已启用一条售卖计划');
                    $this->redirect(array('guadanCollect/sellAdmin'));
                }
            }
            $trans = Yii::app()->db->beginTransaction();
            if($startTime > time()) {
                $rs1 = Yii::app()->db->createCommand()->update(GuadanCollect::model()->tableName(), array('status'=>GuadanCollect::STATUS_ENABLE,'time_start'=>time()),'id='.$gd['id']);
            }else{
                $rs1 = Yii::app()->db->createCommand()->update(GuadanCollect::model()->tableName(), array('status'=>GuadanCollect::STATUS_ENABLE),'id='.$gd['id']);
            }
            if ($rs1) {
            	Yii::app()->db->createCommand()->update(GuadanRule::model()->tableName(),array('status'=>GuadanRule::STATUS_ENABLE),'collect_id='.$gd['id']);
            	 
                $trans->commit();
                $collect_code = $gd->code;
                @SystemLog::record("管理员(" . $this->getUser()->name . ")启动售卖计划<{$collect_code}>", SystemLog::LOG_TYPE_SHOUMAI);
                $this->setFlash('success','启用成功');
                
                GuadanJifenGoods::clearCache();

            }else {
                $trans->rollback();
                $this->setFlash('error','撤销挂单失败');
            }
            $this->redirect(array('guadanCollect/sellAdmin'));
        }


//     	$rs2 = Yii::app()->db->createCommand()->update(GuadanCollect::model()->tableName(), array('status'=>GuadanCollect::STATUS_DISABLE),'id!='.$gd['id'.' AND status='.GuadanCollect::STATUS_ENABLE]);


    	
    }
    
    
    
    
}


?>