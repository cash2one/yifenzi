<?php

/**
 * 邮箱验证类
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class isEmail extends CValidator {

    public $errMsg;
    private $_pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";

    protected function validateAttribute($object, $attribute) {
        if (!preg_match($this->_pattern, $object->$attribute))
            $this->addError($object, $attribute, $this->errMsg);
    }

    /**
     * 实现客户端验证
     * @param CModel $object
     * @param string  $attribute
     * @return string 
     */
    public function clientValidateAttribute($object, $attribute) {
        $result = "!value.match({$this->_pattern})";
        return "if(" . $result . "){messages.push(" . CJSON::encode($this->errMsg) . ");}";
    }

}
