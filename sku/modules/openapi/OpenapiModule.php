<?php

/**
 * 开放api模块
 * @author leo8705
 */
class OpenapiModule extends CWebModule {

    public $defaultController = 'index';

    public function init() {
        $this->setImport(array(
            'openapi.components.*',
        	'openapi.models.*',
            'api.models.*'
        ));
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            return true;
        } else
            return false;
    }
}
