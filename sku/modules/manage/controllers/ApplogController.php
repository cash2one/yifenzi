<?php

/**
 * 程序日志控制器
 * 操作（列表，详情查看）
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class ApplogController extends MController {

    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * 不作权限控制的action
     * @return string
     */
    public function allowedActions() {
        return 'admin';
    }

    public function actionAdmin() {
        $model = new Applog('search');
        $model->unsetAttributes();
        if (isset($_GET['Applog']))
            $model->attributes = $_GET['Applog'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

}
