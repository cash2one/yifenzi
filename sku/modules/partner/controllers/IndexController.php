<?php

class IndexController extends PController {

    public function actionIndex() {
        $this->render('index');
    }

    public function actionError() {
        $this->layout = 'app';
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

}