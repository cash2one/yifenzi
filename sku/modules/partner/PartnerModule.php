<?php

/**
 * 合作商家模块
 * @author leo8705
 */
class PartnerModule extends CWebModule {

    public $defaultController = 'home';

    public function init() {
        $this->setImport(array(
            'partner.components.*',
        	'partner.models.*',
        	'application.widgets.*',
        	'partner.extensions.*',
            'game.models.*',
            'manage.models.GameStoreMember',
            'manage.models.GameStore',
            'manage.models.GameStoreItems',
            'manage.models.GameStoreDelivery',
        ));
    }

    public function beforeControllerAction($controller, $action) {
        if (parent::beforeControllerAction($controller, $action)) {
            return true;
        } else
            return false;
    }
}
	