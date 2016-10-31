<?php

/**
 * 角色模板
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class RoleForm extends CFormModel {

    public $name;
    public $description;
    public $type;
    public $bizRule;
    public $data;

    /**
     * Declares the validation rules.
     */
    public function rules() {
        return array(
            array('name, description', 'required'),
            array('name, description', 'unique'),
//            array('name', 'nameIsAvailable', 'on' => 'create'),
            array('name', 'newNameIsAvailable', 'on' => 'update'),
            array('name', 'isSuperuser', 'on' => 'update'),
//            array('data', 'bizRuleNotEmpty'),
            array('bizRule, data', 'safe'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'name' => '角色编号',
            'description' => '角色名称',
            'bizRule' => Rights::t('core', 'Business rule'),
            'data' => Rights::t('core', 'Data'),
        );
    }

    /**
     * Makes sure that the name is available.
     * This is the 'nameIsAvailable' validator as declared in rules().
     */
    public function nameIsAvailable($attribute, $params) {
        // Make sure that an authorization item with the name does not already exist
        if (Rights::getAuthorizer()->authManager->getAuthItem($this->name) !== null)
            $this->addError('name', Rights::t('core', 'An item with this name already exists.', array(':name' => $this->name)));
    }

    /**
     * Makes sure that the new name is available if the name been has changed.
     * This is the 'newNameIsAvailable' validator as declared in rules().
     */
    public function newNameIsAvailable($attribute, $params) {
        if (strtolower(urldecode($_GET['name'])) !== strtolower($this->name))
            $this->nameIsAvailable($attribute, $params);
    }

    /**
     * Makes sure that the superuser roles name is not changed.
     * This is the 'isSuperuser' validator as declared in rules().
     */
    public function isSuperuser($attribute, $params) {
        if (strtolower($_GET['name']) !== strtolower($this->name) && strtolower($_GET['name']) === strtolower(Rights::module()->superuserName))
            $this->addError('name', Rights::t('core', 'Name of the superuser cannot be changed.'));
    }

    /**
     * Makes sure that the business rule is not empty when data is specified.
     * This is the 'bizRuleNotEmpty' validator as declared in rules().
     */
    public function bizRuleNotEmpty($attribute, $params) {
        if (empty($this->data) === false && empty($this->bizRule) === true)
            $this->addError('data', Rights::t('core', 'Business rule cannot be empty.'));
    }

}
