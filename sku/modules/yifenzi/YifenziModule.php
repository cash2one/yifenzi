<?php

/**
 * 后台模块
 * @author leo8705
 */

class YifenziModule extends CWebModule {

    public $defaultController = 'site';

    public function init() {
        $this->setImport(array(
			'yifenzi.models.*',
			'yifenzi.components.*',
		));
        Yii::app()->errorHandler->errorAction = 'yifenzi/site/error';
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            return true;
        } else
            return false;
    }


}
