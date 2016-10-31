<?php

/**
 * compare 的扩展重写
 * 用于 Y-m-d H:i:s 格式时间的对比
 * @author zhenjun_xu <412530435@qq.com>
 */
class compareDatetime extends CCompareValidator
{


    /**
     * 服务端验证，
     * @param CModel $object
     * @param string $attribute
     * @return string
     */
    protected function validateAttribute($object, $attribute)
    {
        parent::validateAttribute($object, $attribute);
    }

    /**
     * 实现客户端验证
     * @param CModel $object
     * @param string $attribute
     * @return string
     * @throws CException
     */
    public function clientValidateAttribute($object, $attribute)
    {
        if ($this->compareValue !== null) {
            $compareTo = $this->compareValue;
            $compareValue = CJSON::encode($this->compareValue);
        } else {
            $compareAttribute = $this->compareAttribute === null ? $attribute . '_repeat' : $this->compareAttribute;
            $compareValue = "jQuery('#" . (CHtml::activeId($object, $compareAttribute)) . "').val()";
            $compareTo = $object->getAttributeLabel($compareAttribute);
        }

        $message = $this->message;
        switch ($this->operator) {
            case '=':
            case '==':
                if ($message === null)
                    $message = Yii::t('yii', '{attribute} must be repeated exactly.');
                $condition = 'value!=' . $compareValue;
                break;
            case '!=':
                if ($message === null)
                    $message = Yii::t('yii', '{attribute} must not be equal to "{compareValue}".');
                $condition = 'value==' . $compareValue;
                break;
            case '>':
                if ($message === null)
                    $message = Yii::t('yii', '{attribute} must be greater than "{compareValue}".');
                $condition = '(value)<=(' . $compareValue . ')';
                break;
            case '>=':
                if ($message === null)
                    $message = Yii::t('yii', '{attribute} must be greater than or equal to "{compareValue}".');
                $condition = '(value)<(' . $compareValue . ')';
                break;
            case '<':
                if ($message === null)
                    $message = Yii::t('yii', '{attribute} must be less than "{compareValue}".');
                $condition = '(value)>=(' . $compareValue . ')';
                break;
            case '<=':
                if ($message === null)
                    $message = Yii::t('yii', '{attribute} must be less than or equal to "{compareValue}".');
                $condition = '(value)>(' . $compareValue . ')';
                break;
            default:
                throw new CException(Yii::t('yii', 'Invalid operator "{operator}".', array('{operator}' => $this->operator)));
        }

        $message = strtr($message, array(
            '{attribute}' => $object->getAttributeLabel($attribute),
            '{compareAttribute}' => $compareTo,
        ));

        return "
if(" . ($this->allowEmpty ? "jQuery.trim(value)!='' && " : '') . $condition . ") {
	messages.push(" . CJSON::encode($message) . ".replace('{compareValue}', " . $compareValue . "));
}
";
    }

}
