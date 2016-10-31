<?php

/**
 * 密码强度验证
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class passwordStrength extends CValidator {

    public $message;
//    private $_pattern = '/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z])).{6,}$/';
//    private $_pattern = '/^[0-9a-zA-Z]{6,}$/';		//修改密码规则  by csj @2013/12/10
    private $_pattern = '/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/'; //修改密码规则 by zsj @2014/11/4

    protected function validateAttribute($object, $attribute) {
        if (!preg_match($this->_pattern, $object->$attribute))
            $this->addError($object, $attribute, $this->message);
    }

    /**
     * 实现客户端验证
     * @param CModel $object
     * @param string  $attribute
     * @return string 
     */
    public function clientValidateAttribute($object, $attribute) {
        $result = "!value.match({$this->_pattern})";
        return "if(" . $result . "){messages.push(" . CJSON::encode($this->message) . ");}";
    }

}