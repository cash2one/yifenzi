<?php

/**
 * 后台模块
 * @author leo8705
 */

class Yifenzi2Module extends CWebModule {

    public $defaultController = 'site';

    public function init() {
        $this->setImport(array(
			'yifenzi2.models.*',
			'yifenzi2.components.*',
		));
        Yii::app()->errorHandler->errorAction = 'yifenzi2/site/error';
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            return true;
        } else
            return false;
    }


}
