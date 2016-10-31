<?php

/**
 * orderapiapi模块
 * @author qinghaoye
 */
class OrderapiModule extends CWebModule {

    public $defaultController = 'index';

    public function init() {
        $this->setImport(array(
            'orderapi.components.*',
        	'orderapi.models.*',
        ));
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            return true;
        } else
            return false;
    }
}
