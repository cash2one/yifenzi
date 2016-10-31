<?php

/**
 * 余额控制器 
 * 操作 (列表)
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class AccountBalanceController extends MController {

    public function filters() {
        return array(
            'rights',
        );
    }

    /**
     * 不作权限控制的action
     * @return string
     */
    public function allowedActions() {
        return 'admin';
    }

    public function actionAdmin() {
        $model = new AccountBalance('search');
        $model->unsetAttributes();
        if (isset($_GET['AccountBalance']))
            $model->attributes = $this->getParam('AccountBalance');

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * 检查hash是否正确
     * @param $id
     */
    public function actionCheckHash($id){

        $account = $this->loadModel($id);
        $data = array($account['sku_number'], $account['account_id'], $account['today_amount'], $account['amount_salt'], AMOUNT_SIGN_KEY);
        $hash = sha1(implode('', $data));
        if ($hash != $account['amount_hash']) {
            $msg = array('icon'=>'error','content'=>'校验失败','lock'=>true);
        }else{
            $msg = array('icon'=>'succeed','content'=>'校验成功','lock'=>true);
        }
        echo json_encode($msg);
    }

    /**
     * 重置hash
     */
    public function actionResetHash(){
        $account = $this->loadModel($this->getPost('id'));
        $salt = md5(uniqid());
        $data = array($account['sku_number'], $account['account_id'], $account['today_amount'], $salt, AMOUNT_SIGN_KEY);
        $hash = sha1(implode('', $data));
        $sql = 'UPDATE ' . ACCOUNT . '.' . "{{account_balance}}" . ' SET amount_salt="' . $salt . '",amount_hash="' . $hash . '" WHERE id =' . $account['id'];
        Yii::app()->db->createCommand($sql)->execute();
        @SystemLog::record($this->getUser()->name . "重设hash new：" . $account['id']);
        $msg = array('icon'=>'succeed','content'=>'重置成功','lock'=>true);
        echo json_encode($msg);
    }

}
