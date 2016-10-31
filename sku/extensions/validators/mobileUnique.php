<?php

/**
 * 手机号唯一性验证,应用于member表
 * 1.前台会员注册全部唯一
 * 2.盖网后台添加，企业会员可以不唯一，普通会员唯一
 * 3.会员中心，手机号码修改时才检查唯一性，因为企业会员有副账号的情况下，不唯一
 *
 * @author zhenjun_xu <412530435@qq.com>
 */
class mobileUnique extends CUniqueValidator
{

    /**
     * 客户端验证
     * @param CModel $object
     * @param string $attribute
     */
    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        if ($this->allowEmpty && $this->isEmpty($value))
            return;
        if (is_array($value)) {
            $this->addError($object, $attribute, Yii::t('yii', '{attribute} is invalid.'));
            return;
        }
        /** @var $object Member */
        //前台
        if (stripos(Yii::app()->basePath, 'frontend') !== false) {
            if ($object->isNewRecord) {
                if($object->scenario!='resetPassword'){
                    parent::validateAttribute($object, $attribute); //注册时候做全局唯一性验证
                }
            } else {
                //企业会员修改了手机号码才做唯一性验证
                if ($object->is_enterprise) {
                    $member = Yii::app()->db->createCommand('select mobile from {{member}} where id=:id')
                        ->bindValue(':id', $object->id)->queryRow();
                    if ($member['mobile'] != $value) {
                        parent::validateAttribute($object, $attribute);
                    }
                } else {
                    $actions = array(
                        'resetPassword','resetPassword2','resetPassword3','update_avatar'
                    );
                    if(!in_array($object->scenario,$actions)){
                        parent::validateAttribute($object, $attribute);
                    }

                }
            }
            //后台企业会员的增改，不做唯一性验证
        } else {
//            if($object->is_enterprise!=Member::ENTERPRISE_YES && $object->scenario != 'enterpriseUpdate'){
//                parent::validateAttribute($object, $attribute);
//            }
            if ($object->isNewRecord) {
                if($object->scenario!='updatePassword'){
                    parent::validateAttribute($object, $attribute); //添加企业会员时候做全局唯一性验证
                }
            } else {
                if (!$object->is_enterprise) {
                    parent::validateAttribute($object, $attribute);
                }
            }
        }
    }

}
