<?php

/**
 * required 的扩展
 * 有条件地确保特性不为空
 * @author zhenjun_xu <412530435@qq.com>
 */
class requiredExt extends CRequiredValidator {

    /**
     * 必须验证的条件，如果为true,则不检查
     * @var boolean 
     */
    public $allowEmpty=false;

    /**
     * 服务端验证，如果$this->allowEmpty 为真，则直接返回
     * @param CModel $object
     * @param string  $attribute
     * @return string 
     */
    protected function validateAttribute($object, $attribute) {
        $value = $object->$attribute;
        if ($this->allowEmpty && $this->isEmpty($value))
            return;
        parent::validateAttribute($object, $attribute);
    }

    /**
     * 实现客户端验证
     * @param CModel $object
     * @param string  $attribute
     * @return string 
     */
    public function clientValidateAttribute($object, $attribute) {
        $js = parent::clientValidateAttribute($object, $attribute);
        if ($this->allowEmpty) {
            $js = "
if(jQuery.trim(value)!='') {
	$js
}
";
        }
        return $js;
    }

}
