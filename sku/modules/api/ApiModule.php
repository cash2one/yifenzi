<?php

/**
 * api模块
 * @author leo8705
 */
class ApiModule extends CWebModule {

    public $defaultController = 'index';

    public function init() {
        $this->setImport(array(
            'api.components.*',
        	'api.models.*',
        ));
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            return true;
        } else
            return false;
    }
}
