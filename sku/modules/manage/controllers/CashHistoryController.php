<?php

/**
 * 积分兑现、企业提现申请列表
 *
 * 操作(兑现、提现的审核,批量操作)
 * @author zhenjun_xu <412530435@qq.com>
 */
class CashHistoryController extends MController
{
    public $showExport = false;
    public $showBack = false; //右上角 是否显示 “返回列表”
    public $exportAction;

    public function filters() {
        return array(
            'rights',
        );
    }
    /**
     * 不作权限控制的action
     * @return string
     */
    public function allowedActions()
    {
        return 'franchiseeApplyCashExport,setReview';
    }

    /**
     * 提现申请列表
     */
    public function actionApplyCash()
    {
        $this->exportAction = 'applyCashExport';
        $this->breadcrumbs = array(Yii::t('member', '提现管理 '), Yii::t('member', '提现申请单'));
        $model = new CashHistory('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['CashHistory']))
            $model->attributes = $_GET['CashHistory'];

        $c = $model->search();
        $count = CashHistory::model()->count($c);
        CashHistory::model()->find();
        $pages = new CPagination($count);
        $pages->applyLimit($c);
        if(Yii::app()->user->checkAccess('Manage.CashHistory.ApplyCashExport')){
        	$this->showExport = true;
        }
        $exportPage = new CPagination($count);
        $exportPage->route = '/cashHistory/applyCashExport';
        $exportPage->params = array_merge(array('exportType' => 'Excel5', 'grid_mode' => 'export'), $_GET);
        $exportPage->pageSize = $model->exportLimit;


        $log = CashHistory::model()->findAll($c);
        $this->render('applycash', array('model' => $model, 'pages' => $pages, 'log' => $log, 'exportPage' => $exportPage, 'totalCount' => $count));
    }

    /**
     * 提现申请列表导出
     */
    public function actionApplyCashExport()
    {
        @ini_set('memory_limit', '2048M');
        $model = new CashHistory('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['CashHistory']))
            $model->attributes = $_GET['CashHistory'];
        
        @SystemLog::record(Yii::app()->user->name . "导出积分兑现申请列表");

        $model->isExport = 1;
        $this->render('applycashExport', array('model' => $model));
    }


    /**
     * 提现申请编辑
     * @param $id
     * @throws CHttpException
     */
    public function actionApplyCashDetail($id)
    {
        $this->showBack = true;
        $this->breadcrumbs = array(Yii::t('member', '兑现管理 '), Yii::t('member', '积分申请兑现编辑'));
        /** @var $model CashHistory */
        $model = $this->loadModel($id);
        /** @var $memberModel Member */
        $memberModel = Member::model()->findByPk($model->member_id);
        if (isset($_POST['CashHistory'])) {
            $this->checkPostRequest();
            $model->attributes = $_POST['CashHistory'];
            //当前控制器不可以修改到审核状态
            if($model->status==$model::STATUS_CHECKED){
                $this->setFlash('error', Yii::t('cashHistory', '请选择其他状态'));
                $this->refresh();
            }
            $flag = false;
            //其他状态
            if($model->status != $model::STATUS_TRANSFERED && $model->status != $model::STATUS_FAIL){
                $flag = Yii::app()->db->createCommand()->update('{{cash_history}}', array('update_time'=>time(),'reason' => $model->reason, 'status' => $model->status), "id='{$model->id}'");
            }
            //成功
            if ($model->status == $model::STATUS_TRANSFERED) {
                if($model->type==$model::TYPE_COMPANY_CASH){ //商家提现处理
                    $flag = CashHistoryProcess::enterpriseCashEnd($model->attributes, $memberModel->attributes);
                }else{
                    throw new CHttpException(403,'无法处理其他提现');
                }
            }
            //失败，发送短信提醒，积分回滚
            if ($model->status == $model::STATUS_FAIL) {
                if($model->type==$model::TYPE_COMPANY_CASH){
                    $flag = CashHistoryProcess::enterpriseCashFailed($model->attributes, $memberModel->attributes);
                }else{
                    throw new CHttpException(403,'无法处理其他提现');
                }
            }
            if (!$flag) {
                $this->setFlash('error', Yii::t('cashHistory', '操作失败'));
                $this->refresh();
            }else{
                $this->setFlash('success', Yii::t('cashHistory', '操作成功'));
            }
            SystemLog::record(Yii::app()->user->name . "提现申请编辑：{$memberModel->sku_number}|{$model->reason}|{$model::status($model->status)}");
            $this->refresh();
        }
        $this->render('applycashdetail', array('model' => $model, 'memberModel' => $memberModel));
    }


    /**
     * ajax 批量操作 提现申请
     */
    public function actionCashBatchUpdate()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $this->checkPostRequest();
            $idArr = explode(',', $this->getPost('idArr'));
            $reason = $this->getPost('reason');
            $status = $this->getPost('status');
            if (empty($idArr))
                return false;
            foreach ($idArr as $id) {
                /** @var $model  CashHistory */
                $model = CashHistory::model()->findByPk($id);
                /** @var $memberModel Member */
                $memberModel = Member::model()->findByPk($model->member_id);
                if (!$model)
                    continue;
                $model->reason = $reason;
                $flag = true;
                //批量转账中
                if ($status == 'transfering') {
                    $model->status = $model::STATUS_TRANSFERING;
                }
                //批量转账成功
                if ($status == 'transfered') {
                    $model->status = $model::STATUS_TRANSFERED;
                }
                //批量转账失败
                if ($status == 'fail') {
                    $model->status = $model::STATUS_FAIL;
                }
                //其他状态
                if ($model->status != $model::STATUS_FAIL && $model->status != $model::STATUS_TRANSFERED) {
                    Yii::app()->db->createCommand()->update('{{cash_history}}', array('update_time'=>time(),'reason' => $model->reason, 'status' => $model->status), "id='{$model->id}'");
                }
                //成功
                if ($model->status == $model::STATUS_TRANSFERED) {
                    if($model->type==$model::TYPE_COMPANY_CASH){ //商家提现处理
                        $flag = CashHistoryProcess::enterpriseCashEnd($model->attributes, $memberModel->attributes);
                    }else{
                        throw new CHttpException(403,'无法处理其他提现');
                    }
                }
                //失败，发送短信提醒，积分回滚
                if ($model->status == $model::STATUS_FAIL) {
                    if($model->type==$model::TYPE_COMPANY_CASH){ //商家提现处理
                        $flag = CashHistoryProcess::enterpriseCashFailed($model->attributes, $memberModel->attributes);
                    }else{
                        throw new CHttpException(403,'无法处理其他提现');
                    }
                }
                if (!$flag) {
                    $msg =  Yii::t('cashHistory', '操作失败');
                }else{
                    $msg =  Yii::t('cashHistory', '操作成功');
                }
                echo $msg;
                @SystemLog::record(Yii::app()->user->name . "批量操作 积分兑换：" . $this->getPost('idArr'));
            }
        }
    }


    /**
     * ajax 修改审阅状态
     */
    public function actionSetReview(){
        $this->_setReview();
    }

    private function _setReview(){
        $id = $this->getPost('id');
        $type = $this->getPost('status');
        if ($type == CashHistory::REVIEW_NO){
            CashHistory::model()->updateByPk($id, array('is_review' => CashHistory::REVIEW_YES));
        }
        elseif ($type == CashHistory::REVIEW_YES){
//            CashHistory::model()->updateByPk($id, array('is_review' => CashHistory::REVIEW_NO));
        }
        @SystemLog::record(Yii::app()->user->name . "修改审阅状态：" . $id . '|' . CashHistory::reviewStatus($type));
        echo CJSON::encode(array('status' => 1));
        Yii::app()->end();
    }

    /**
     * ajax 批量修改兑现审核状态
     */
    public function actionCheckedBatch(){
        $this->_checkedBatch();
    }


    /**
     * 批量操作审核
     */
    private function _checkedBatch(){
        if (Yii::app()->request->isAjaxRequest) {
            $ids = $this->getPost('idArr');
            $check = (int)$this->getPost('check');
            if($check===CashHistory::CHECK_YES){
                $flag = Yii::app()->db->createCommand()->update('{{cash_history}}',array('is_check'=>$check),'id in('.$ids.')');
            }else{
                $flag = false;
            }
            if (!$flag) {
                $msg =  Yii::t('cashHistory', '操作失败');
            }else{
                $msg =  Yii::t('cashHistory', '操作成功');
            }
            echo $msg;
            @SystemLog::record(Yii::app()->user->name . "批量操作兑现审核：" . $this->getPost('idArr'));
        }
    }

}
