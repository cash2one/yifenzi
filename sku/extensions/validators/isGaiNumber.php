<?php

/**
 * 盖网会员编号验证类
 *
 * @author zhenjun_xu<412530435@qq.com>
 */
class isGaiNumber extends CValidator {

    /**
     * 验证是否盖网编号，默认是, false 时候，字段不能是盖网编号
     * @var bool
     */
    public $isGaiNumber = true;
    public $message;
    private $_pattern = "/^GW[0-9]{7,15}$/";

    protected function validateAttribute($object, $attribute) {
        if($this->isGaiNumber){
            if (!preg_match($this->_pattern, $object->$attribute) && !empty($object->$attribute))
                $this->addError($object, $attribute, $this->message);
        }else{
            if (preg_match($this->_pattern, $object->$attribute) && !empty($object->$attribute))
                $this->addError($object, $attribute, $this->message);
        }
    }

    /**
     * 实现客户端验证
     * @param CModel $object
     * @param string  $attribute
     * @return string 
     */
    public function clientValidateAttribute($object, $attribute) {
        if(empty($this->message)){
            $this->message = strtr(Yii::t('member', '{attribute} 格式不正确'), array(
            '{attribute}' => $object->getAttributeLabel($attribute),
        ));
        }
        $result = $this->isGaiNumber ? "!value.match({$this->_pattern})" : "value.match({$this->_pattern})";
        return "if(" . $result . " && value.length>0){messages.push(" . CJSON::encode($this->message) . ");}";
    }

}
