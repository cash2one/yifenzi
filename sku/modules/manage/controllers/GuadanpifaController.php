<?php
/**
 * Created by PhpStorm.
 * User: zsj
 * Date: 2016/1/13
 * Time: 18:04
 */
class GuadanpifaController extends MController{
    public function filters()
    {
        return array(
            'rights',
        );
    }
    
    public function allowedActions()
    {
    	return 'ajaxCheck';
    }
    

    /**
     * 积分批发规则列表
     */
    public function  actionAdmin(){
        //从积分挂单提取表中获取当前提取表已卖出的待绑定积分
        $saleAmountBind= Yii::app()->db->createCommand()
            ->select("sale_amount_bind")
            ->from("{{guadan_collect}}")
            ->where("status = ".GuadanCollect::STATUS_ENABLE)
            ->queryRow();
        if(empty($saleAmountBind)) {
            $this->setFlash('error', Yii::t('FreshMachine', "当前还未有启用的积分挂单提取表"));
            $this->redirect(array("guadan/guadanAdmin"));
        }

        //获取当前积分批发规则详情
        $data = Yii::app()->db->createCommand()
            ->select("gpcd.*,gpc.limit_score,gpc.distribution_ratio,gpc.id")
            ->from("{{guadan_partner_config}} gpc")
            ->leftjoin("{{guadan_partner_config_detail}} gpcd","gpc.id = gpcd.partner_config_id")
            ->order("gpcd.min_score ASC")
            ->where("gpc.status = 1")
            ->queryAll();

            $ratio = !empty($data)?$data[0]['distribution_ratio']:0;
            $limitScore = !empty($data)?$data[0]['limit_score']*1:0;
            $id = !empty($data)?$data[0]['id']:0;
            $saleAmountBind = $saleAmountBind['sale_amount_bind']*1;
            $num = !empty($data)?count($data)-2:0;


        $this->render("admin",array(
            'rule'=>$data,
            'saleAmountBind'=>$saleAmountBind,
            'ratio'=>$ratio,
            'num'=>$num,
            'limitScore'=> $limitScore,
            'id'=>$id
        ));

    }

    /**
     * 新增商户批发积分政策
     */
    public function actionCreate(){
        $model = new GuadanPartnerConfig();

        if(isset($_POST['GuadanPartnerConfig']) && isset($_POST['GuadanPartnerConfigDetail'])){
            $data = $this->getParam('GuadanPartnerConfigDetail');
            //判断积分范围是否有交叉
            $array = array();
            foreach($data as $k => $v){
                $list = array();
                $v =explode(',',$v);
                $list['min'] = $v[0];
                $list['max'] = $v[1];
                $list['radio'] = $v[2];
                $array[] = $list;
            }
            //开启事务
            $transaction = Yii::app()->db->beginTransaction();
            //将当前启用的积分批发政策删除
//            Yii::app()->db->createCommand()->update(GuadanPartnerConfig::model()->tableName(), array('status'=>GuadanPartnerConfig::STATUS_END),'status=:status',array(':status'=>GuadanPartnerConfig::STATUS_ENABLE));
            $sql = "UPDATE {{guadan_partner_config}} SET `status`=".GuadanPartnerConfig::STATUS_END." WHERE (`status`=".GuadanPartnerConfig::STATUS_ENABLE.")";
            Yii::app()->db->createCommand($sql)->execute();
            $model->attributes = $this->getParam('GuadanPartnerConfig');
            $model->explain = $this->getParam("explain");
            $model->create_time = time();
            $model->status = GuadanPartnerConfig::STATUS_ENABLE;
            if($model->save()){
                $lastId = Yii::app()->db->getLastInsertID();
                foreach($array as $k =>$v){
                    $DetailModel = new GuadanPartnerConfigDetail();
                    $DetailModel->unsetAttributes();
                    $DetailModel->partner_config_id = $lastId;
                    $DetailModel->min_score = $v['min'];
                    $DetailModel->max_score = $v['max'];
                    $DetailModel->ratio = $v['radio'];
                    $DetailModel->save();
                }
                $transaction->commit();
                $this->setFlash('success', "添加积分批发政策成功");
                $this->redirect(array("guadanpifa/admin"));
            }else{
                $transaction->rollBack();
                $this->setFlash('error', "添加积分批发政策失败");
                $this->redirect(array("guadanpifa/admin"));
            }

        }
       $this->render('create',array(
           'model'=>$model
       ));

    }


    public function actionUpdate($id){
        $model = GuadanPartnerConfig::model()->findByPk($id);
        //从积分挂单提取表中获取当前提取表已卖出的待绑定积分
        $saleAmountBind= Yii::app()->db->createCommand()
            ->select("sale_amount_bind")
            ->from("{{guadan_collect}}")
            ->where("status = ".GuadanCollect::STATUS_ENABLE)
            ->queryRow();

        if(empty($saleAmountBind)) {
            $this->setFlash('error', Yii::t('FreshMachine', "当前还未有启用的积分挂单提取表"));
            $this->redirect(array("guadan/guadanAdmin"));
        }
        //获取当前积分批发规则详情
        $rule = Yii::app()->db->createCommand()
            ->select("gpcd.*,gpc.limit_score,gpc.distribution_ratio,gpc.id")
            ->from("{{guadan_partner_config}} gpc")
            ->leftjoin("{{guadan_partner_config_detail}} gpcd","gpc.id = gpcd.partner_config_id")
            ->order("gpcd.min_score ASC")
            ->where("gpc.status = ".GuadanPartnerConfig::STATUS_ENABLE)
            ->queryAll();
        if(!empty($rule)){
            $ratio = $rule[0]['distribution_ratio'];
            $limitScore = $rule[0]['limit_score']*1;
            $id = $rule[0]['id'];
            $saleAmountBind = $saleAmountBind['sale_amount_bind']*1;
        }else{
            $this->setFlash('error', Yii::t('FreshMachine', "当前还未有启用的积分批发规则"));
            $this->redirect(array("guadan/guadanAdmin"));
        }
        if(isset($_POST['GuadanPartnerConfig']) && isset($_POST['GuadanPartnerConfigDetail']) && isset($_POST['hiddenId'])){
            $hiddenId = $this->getParam('hiddenId');
            $data = $this->getParam('GuadanPartnerConfigDetail');
            //判断积分范围是否有交叉
            $array = array();
            foreach($data as $k => $v){
                $list = array();
                $v =explode(',',$v);
                $list['min'] = (int)$v[0];
                $list['max'] = (int)$v[1];
                $list['radio'] = (int)$v[2];
                $array[] = $list;
            }
            //开启事务
            $transaction = Yii::app()->db->beginTransaction();
            //将当前启用的积分批发政策删除
//            Yii::app()->db->createCommand()->update(GuadanPartnerConfig::model()->tableName(), array('status'=>GuadanPartnerConfig::STATUS_END),'status=:status',array(':status'=>GuadanPartnerConfig::STATUS_ENABLE));
            $sql = "UPDATE {{guadan_partner_config}} SET `status`=".GuadanPartnerConfig::STATUS_END." WHERE (`status`=".GuadanPartnerConfig::STATUS_ENABLE.")";
            Yii::app()->db->createCommand($sql)->execute();
            $model->attributes = $this->getParam('GuadanPartnerConfig');
            $model->explain = $this->getParam("explain");
            $model->update_time = time();
            $model->status = GuadanPartnerConfig::STATUS_ENABLE;
            if($model->save()){
                GuadanPartnerConfigDetail::model()->deleteAll("partner_config_id = :id",array(':id'=>$hiddenId));
                foreach($array as $k =>$v){
                    $DetailModel = new GuadanPartnerConfigDetail();
                    $DetailModel->unsetAttributes();
                    $DetailModel->partner_config_id = $hiddenId;
                    $DetailModel->min_score = $v['min'];
                    $DetailModel->max_score = $v['max'];
                    $DetailModel->ratio = $v['radio'];
                    $DetailModel->save();
                }
                $transaction->commit();
                $this->setFlash('success', "编辑积分批发政策成功");
                $this->redirect(array("guadanpifa/admin"));
            }else{
                $transaction->rollBack();
                $this->setFlash('error', "编辑积分批发政策失败");
                $this->redirect(array("guadanpifa/admin"));
            }

        }

        $this->render('update',array(
            'model'=>$model,
            'rule'=>$rule,
            'saleAmountBind'=>$saleAmountBind,
            'ratio'=>$ratio,
            'id'=>$id,
        ));


    }

    /**
     * ajax验证提交数据是否重复及完整
     */
    public function actionAjaxCheck(){
        if($this->isAjax()){
            $data = $this->getParam('data');
            $json = stripslashes($data);
            $data =json_decode($json,true);
            //判断积分范围是否有交叉
            $array = array();
            foreach($data as $k => $v){
                $list = array();
                $v =explode(',',$v);
                $list['min'] = $v[0];
                $list['max'] = $v[1];
                $list['radio'] = $v[2];
                $array[] = $list;
            }
            //对数组按min字段进行排序
            foreach ($array as $k => $v) {
                $sort[$k]=$v['min'];
            }
            if(empty($sort)){
                $sort =array();
            }
            if(empty($array)){
                $array =array();
            }

            array_multisort($sort, SORT_ASC, $array);

            if($array[0]['min'] != 0){
                exit(json_encode(array('error' => '取值范围不完整1')));
            }
            $last = end($array);

            if($last['max']>0){
                exit(json_encode(array('error' => '取值范围不完整2')));
            }
            foreach($array as $k =>$v){
                if($k>0){
                    if($array[$k-1]['max'] != $v['min']){
                        exit(json_encode(array('error' => '取值范围不完整3')));
                    }
                    if($v['max'] != "" && $v['max'] != 0){
                        if($v['min'] >= $v['max']){
                            exit(json_encode(array('error' => '取值范围错误4')));
                        }
                    }
                }
            }
            exit(json_encode(array('success' => true)));
        }
    }

    /**
     * 删除积分批发政策
     */
    public function actionDelete($id){
        $sql = "UPDATE {{guadan_partner_config}} SET `status`=".GuadanPartnerConfig::STATUS_END." WHERE (`id`=".$id.")";
        Yii::app()->db->createCommand($sql)->execute();
        $this->setFlash('success', "添加积分批发政策成功");
        $this->redirect(array("guadanpifa/admin"));
    }

}
