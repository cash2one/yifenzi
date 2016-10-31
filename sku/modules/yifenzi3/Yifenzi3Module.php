<?php

/**
 * 后台模块
 * @author leo8705
 */

class Yifenzi3Module extends CWebModule {

    public $defaultController = 'site';

    public function init() {
        $this->setImport(array(
			'yifenzi3.models.*',
			'yifenzi3.components.*',
		));
        Yii::app()->errorHandler->errorAction = 'yifenzi3/site/error';
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            return true;
        } else
            return false;
    }


}
