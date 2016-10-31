<?php

/**
 * 管理员控制器
 * 操作(删除，修改密码，创建，列表)
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class UserController extends MController {

    public function filters() {
        return array(
            'rights',
        );
    }
    
    public function allowedActions(){
    	return 'modifyPassword';
    } 

    /**
     * 创建管理员
     */
    public function actionCreate() {
        $model = new User('create');
        $this->performAjaxValidation($model);

        if (isset($_POST['User'])) {
            $model->attributes = $_POST['User'];
            if ($model->save()) {
                $roles = $this->getPost('roles');
                if ($roles) {
                    foreach ($roles as $role) {
                        Yii::app()->db->createCommand()->insert('{{auth_assignment}}', array(
                            'itemname' => $role,
                            'userid' => $model->id
                        ));
                    }
                }
                @SystemLog::record(Yii::app()->user->name . "添加管理员：" . $model->username);
                $this->setFlash('success', Yii::t('user', '添加管理员成功，用户名：') . $model->username);
                $this->redirect(array('admin'));
            }
        }

        $roles = Yii::app()->db->createCommand()
                ->select(array('name', 'description'))
                ->from('{{auth_item}}')
                ->where('type=:type', array(':type' => AuthItem::TYPE_ADMIN))
                ->queryAll();
        $this->render('create', array(
            'model' => $model,
            'roles' => $roles
        ));
    }

    /**
     * 修改密码
     */
    public function actionModifyPassword() {
        $model = $this->loadModel($this->getUser()->id);
        $model->scenario = 'modify';

        $this->performAjaxValidation($model);
        $model->password = '';
        if (isset($_POST['User'])) {
            $model->attributes = $_POST['User'];
            if ($model->save()) {
                @SystemLog::record(Yii::app()->user->name . "修改管理员密码：" . $model->username);
                $this->setFlash('success', Yii::t('user', '密码修改成功'));
                $this->redirect(array('admin'));
            }
        }
        $this->render('modifypassword', array(
            'model' => $model,
        ));
    }

    /**
     * 删除管理员
     * @param int $id
     */
    public function actionDelete($id) {
        $this->loadModel($id)->delete();
        @SystemLog::record(Yii::app()->user->name . "删除管理员：" . $id);
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * 管理员列表
     */
    public function actionAdmin() {
        $model = new User('search');
        $model->unsetAttributes();
        if (isset($_GET['User']))
            $model->attributes = $_GET['User'];

        $roles = array();
        $authItems = AuthItem::model()->findAll('type=:type', array(':type' => AuthItem::TYPE_ADMIN));
        foreach ($authItems as $auth)
            $roles[$auth->name] = $auth->description;
        
        $this->render('admin', array(
            'model' => $model,
            'roles' => $roles
        ));
    }

    /**
     * 创建管理员
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $model->setScenario('update');
        $this->performAjaxValidation($model);
        if (isset($_POST['User'])) {
            $model->attributes = $_POST['User'];
            if ($model->save()) {
                $roles = $this->getPost('roles');
                Yii::app()->db->createCommand()->delete('{{auth_assignment}}', 'userid=:userid', array(':userid' => $model->id));
                if ($roles) {
                    foreach ($roles as $role) {
                        Yii::app()->db->createCommand()->insert('{{auth_assignment}}', array(
                            'itemname' => $role,
                            'userid' => $model->id
                        ));
                    }
                }
                @SystemLog::record(Yii::app()->user->name . "修改管理员：" . $model->username);
                $this->setFlash('success', Yii::t('user', '修改管理员信息成功'));
                $this->redirect(array('admin'));
            }
        }

        $roles = Yii::app()->db->createCommand()
                ->select(array('name', 'description'))
                ->from('{{auth_item}}')
                ->where('type=:type', array(':type' => AuthItem::TYPE_ADMIN))
                ->queryAll();
        $auths = Yii::app()->db->createCommand()
                ->select('itemname')
                ->from('{{auth_assignment}}')
                ->where('userid=:userid', array(':userid' => $id))
                ->queryAll();
        $array = array();
        foreach ($auths as $a)
            array_push($array, $a['itemname']);

        $this->render('update', array(
            'model' => $model,
            'roles' => $roles,
            'array' => $array
        ));
    }

    public function actionReset() {
        if ($this->isAjax()) {
            $model = User::model()->findByPk(intval($_GET['id']));
            if ($model === null)
                exit;
            if ($model->mobile != '') {
                $password = rand(11111111, 99999999);
                $model->password = CPasswordHelper::hashPassword($password);
                if ($model->save()) {
                    @SystemLog::record(Yii::app()->user->name . "重置管理员密码：" . $model->username);
                    $content = '尊敬的' . $model->username . '，你是盖网通的系统管理员，您的新密码是：' . $password;
                    
                    //通过接口发短信
                    $apiMember = new ApiMember();
                    $apiMember->sendSms($model->mobile, $content);
//                     SmsLog::addSmsLog($model->mobile,$content,$model->id,SmsLog::TYPE_OTHER);
                    echo 1;
                }
            }
        }
    }

    public function actionLog() {
        $model = new SystemLog('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['SystemLog']))
            $model->attributes = $_GET['SystemLog'];

//        $model = $model->search();
        $this->render('log', array(
            'model' => $model,
        ));
    }

}
