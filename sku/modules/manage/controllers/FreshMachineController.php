<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FreshMachine
 *
 * @author zehui.hong
 */
class FreshMachineController extends MController {

    //put your code here

    /*
     * 列表页
     */
    public function actionAdmin() {
        $model = new FreshMachine('search');
        $model->unsetAttributes();
        if (isset($_GET['FreshMachine'])) {
            $model->attributes = $_GET['FreshMachine'];
        }
        $this->render('admin', array('model' => $model));
    }

    /**
     * 创建生鲜机
     */
    public function actionCreate() {
        $model = new FreshMachine;
        $model->scenario = 'create';
        $fee_config = $this->getConfig('assign');
        $model->fee = isset($fee_config['storeDefaultFee'])?$fee_config['storeDefaultFee']:8;
        $this->performAjaxValidation($model);
        if (isset($_POST['FreshMachine'])) {
            $count = $model->count('create_time>:time', array(':time' => strtotime(date('Y-m-d'))));          
            $activation_code = '';
            for ($i = 0; $i < 5; $i++) {
                $activation_code .=mt_rand(1, 9);
            }
            $model->code = date('Ymd').$activation_code. $count + 1;
            $model->activation_code = $activation_code;
            $model->attributes = $_POST['FreshMachine'];
            $partners = Partners::model()->find('gai_number=:gw', array(':gw' => $_POST['FreshMachine']['gai_number']));
            $partners_id = $partners->id;
            $member_id = $partners->member_id;
            $model->partner_id = $partners_id;
            $model->member_id = $member_id;
            $model->create_time = time();
            $memberTotalPayPreStoreLimit = Tool::getConfig('amountlimit', 'memberTotalPayPreStoreLimit');
            $model->max_amount_preday = isset($_POST['FreshMachine']['max_amount_preday'])&&$_POST['FreshMachine']['max_amount_preday']>0?$_POST['FreshMachine']['max_amount_preday']:$memberTotalPayPreStoreLimit;

            if ($model->save()) {
            	//清空缓存
            	FreshMachine::clearListInfo($model->partner_id);
                $this->setFlash('success', Yii::t('FreshMachine', '添加生鲜机成功'));
                //组装自动生成货道的数组
                $lasterId = $model->attributes['id'];
                $line = array(
                    'machine_id' => $lasterId,
                    'gai_number' => $_POST['FreshMachine']['gai_number']
                );

                $res  = FreshMachine::autoGenerateGoodsLine($line);
                if($res != true){
                    $this->setFlash('error', Yii::t('FreshMachine', '添加生鲜机货道失败'));
                }
                @SystemLog::record(Yii::app()->user->name . "添加生鲜机成功：" . $model->name);
                $this->redirect(array('admin'));
            } else {
                $this->setFlash('error', Yii::t('FreshMachine', '添加生鲜机失败'));
            }
        }

        $this->render('create', array('model' => $model));
    }

    /**
     * 编辑售货机信息
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $model->scenario = 'update';
        $gai_number = Partners::model()->find('id=:id',array(':id'=>$model->partner_id))->gai_number;
        $model->gai_number = $gai_number;
        $o_partner_id = $model->partner_id;
        $this->performAjaxValidation($model);
        if (isset($_POST['FreshMachine'])) {
            $model->attributes = $_POST['FreshMachine'];
            
            $partners = Partners::model()->find('gai_number=:gw', array(':gw' => $_POST['FreshMachine']['gai_number']));
            if (empty($partners)) {
            	$this->setFlash('error', Yii::t('FreshMachine', '商家不存在'));
            	$this->redirect(array('admin'));
            }
            $partners_id = $partners->id;
            $member_id = $partners->member_id;
            $model->partner_id = $partners_id;
            $model->member_id = $member_id;
            
            $model->update_time = time();
            if ($model->save()) {
            	//清空缓存
            	FreshMachine::clearListInfo($o_partner_id);
            	FreshMachine::clearListInfo($model->partner_id);
            	@SystemLog::record(Yii::app()->user->name . "编辑生鲜机成功：" . $model->name);
                $this->setFlash('success', Yii::t('FreshMachine', '编辑成功'));
                $this->redirect(array('admin'));
            }
        }
        $this->render('update', array('model' => $model));
    }
    
   /**
    * 总签到记录
    * @author zehui.hong
    */
    public function actionRecord(){
       $model = new Record('search');
        $model->unsetAttributes();

        $this->render('record', array('model' => $model));
    }
    
    /**
     *生鲜机签到列表
     * @author zehui.hong
     */
    public function actionRecordOne($id){
        $model = new Record('search');  
        $model->unsetAttributes();
        $model->machine_id = $id;
        $this->render('record_one', array('model' => $model));

    }

}
