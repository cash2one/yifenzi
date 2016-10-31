<?php

/**
 * 程序日志控制器
 * 操作（列表，详情查看）
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class JpushlogController extends MController {

    public function actionView($id) {
        $this->render('view', array(
            'model' =>  JpushLog::model()->findByPk($id),
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
        $model = new JpushLog('search');
        $model->unsetAttributes();
        if (isset($_GET['JpushLog']))
            $model->attributes = $_GET['JpushLog'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

}
