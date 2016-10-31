<?php

/**
 * 手机号码验证类
 * @author wanyun.liu <wanyun_liu@163.com>
 */
class isMobile extends CValidator {

    public $errMsg;
//    private $_pattern = "/^13[0-9]{9}$|^15[012356789]{1}[0-9]{8}$|^18[0-9]{9}$|^170[0-9]{8}$|^852\d{8}$/"; 太复杂，每隔一段时间就要改代码
    private $_pattern = "/(^1[34578]{1}\d{9}$)|(^852\d{8}$)/";

    protected function validateAttribute($object, $attribute) {
        if (!empty($object->$attribute) && !preg_match($this->_pattern, $object->$attribute))
            $this->addError($object, $attribute, $this->errMsg);
    }

    /**
     * 实现客户端验证
     * @param CModel $object
     * @param string  $attribute
     * @return string 
     */
    public function clientValidateAttribute($object, $attribute) {
        $result = "value.length>0 && !value.match({$this->_pattern})";
        return "if(" . $result . "){messages.push(" . CJSON::encode($this->errMsg) . ");}";
    }

}
