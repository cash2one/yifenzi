<?php

/**
 * 后台模块
 * @author leo8705
 */

Yii::import('manage.components.*');
Yii::import('manage.components.rights.*');
Yii::import('manage.components.rights.components.*');

class ManageModule extends RightsModule {

    public $defaultController = 'site';
    public $baseUrl = DOMAIN_M;

    public function init() {
        $this->setImport(array(
        	'manage.models.*',
        	'application.widgets.*',
        	'manage.components.rights.components.*',
        	'manage.components.rights.components.behaviors.*',
        	'manage.components.rights.components.dataproviders.*',
        	'manage.components.rights.controllers.*',
        	'manage.components.rights.models.*',
            'manage.components.manage.models.*',
        	'manage.extensions.*',
        ));
        parent::init();
        return true;
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            return true;
        } else
            return false;
    }
    
    
}
	