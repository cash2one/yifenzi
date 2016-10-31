<?php

/**
 * 管理员模型
 * @author wanyun.liu <wanyun_liu@163.com>
 * 
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property integer $status
 * @property string $real_name
 * @property string $mobile
 * @property string $email
 * @property integer $sex
 * @property string $logins
 * @property string $create_time
 */
class User extends CActiveRecord {

    const STATUS_DISABLED = 0;
    const STATUS_ENABLE = 1;
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    public $originalPassword, $confirmPassword, $role;
    private $_oldPassword;

    /**
     * 获取管理员状态
     * @return array
     */
    public static function getStatus() {
        return array(
            self::STATUS_ENABLE => Yii::t('user', '启用'),
            self::STATUS_DISABLED => Yii::t('user', '禁用')
        );
    }

    /**
     * 获取管理员性别
     * @return array
     */
    public static function getSex() {
        return array(
            self::SEX_MALE => Yii::t('user', '男'),
            self::SEX_FEMALE => Yii::t('user', '女')
        );
    }

    /**
     * GridView中显示状态信息
     * @param int $key
     */
    public static function showStatus($key) {
        $status = self::getStatus();
        return isset($status[$key]) ? $status[$key] : '';
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{user}}';
    }

    public function rules() {
        return array(
            array('username, password, mobile', 'required', 'on' => 'create'),
            array('password', 'required', 'on' => 'modify'),
            array('username, real_name, mobile, email', 'unique', 'on' => 'create'),
            array('username, password', 'length', 'max' => 128),
            array('password', 'length', 'min' => 6, 'message' => Yii::t('user', '密码位数不够,长度范围为7-20位'), 'on' => 'create,modify'),
            array('password', 'length', 'max' => 20, 'message' => Yii::t('user', '密码位数过长,长度范围为7-20位'), 'on' => 'create,modify'),
            array('password', 'ext.validators.passwordStrength', 'message' => Yii::t('user', '密码强度不够, 必须是字母加数字组成'), 'on' => 'create,modify'),
            array('status', 'in', 'range' => array(0, 1)),
            array('sex', 'in', 'range' => array(1, 2)),
            array('mobile', 'required', 'on' => 'update'),
            array('mobile', 'ext.validators.isMobile', 'errMsg' => Yii::t('user', '请输入正确的手机号码'), 'on' => 'create, update'),
            array('email', 'email'),
            array('logins', 'safe'),
            array('real_name, role', 'safe'),
            array('real_name, username, mobile', 'safe', 'on' => 'search'),
            array('originalPassword, confirmPassword, password', 'required', 'on' => 'modify'),
            array('confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('user', '确认密码不正确'), 'on' => 'modify'),
            array('originalPassword', 'checkPassword', 'on' => 'modify'),
        );
    }

    /**
     * 验证旧密码是否输入正确
     * @param type $attribute
     * @param type $params
     */
    public function checkPassword($attribute, $params) {
        if (!CPasswordHelper::verifyPassword($this->originalPassword, $this->_oldPassword))
            $this->addError($attribute, Yii::t('user', '原始密码不正确'));
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'username' => Yii::t('user', '用户名'),
            'password' => Yii::t('user', '新设密码'),
            'status' => Yii::t('user', '状态'),
            'real_name' => Yii::t('user', '真实姓名'),
            'mobile' => Yii::t('user', '手机'),
            'sex' => Yii::t('user', '性别'),
            'email' => Yii::t('user', '邮箱'),
            'originalPassword' => Yii::t('user', '旧密码'),
            'confirmPassword' => Yii::t('user', '确认密码'),
            'role' => Yii::t('user', '角色'),
        );
    }

    public function search() {
        $criteria = new CDbCriteria;
        if ($this->role) {
            $ids = array();
            $authItems = Yii::app()->db->createCommand()
                    ->select('userid')
                    ->from('{{auth_assignment}}')
                    ->where('itemname=:itemname', array(':itemname' => $this->role))
                    ->queryAll();
            foreach ($authItems as $auth)
                array_push($ids, $auth['userid']);
            $criteria->addInCondition('id', $ids);
        }
        $criteria->compare('username', $this->username, true);
        $criteria->compare('real_name', $this->real_name, true);
        $criteria->compare('mobile', $this->mobile);
        $criteria->order = 'id desc';
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * 检测输入的密码是否正确
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password) {
        return CPasswordHelper::verifyPassword($password, $this->password);
    }

    /**
     * 生成的密码哈希.
     * @param string $password
     * @return string $hash
     */
    public function hashPassword($password) {
        return CPasswordHelper::hashPassword($password);
    }

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_time = time();
                $this->password = $this->hashPassword($this->password);
            } else {
                if ($this->getScenario() == 'modify')
                    $this->password = $this->hashPassword($this->password);
            }
            return true;
        } else
            return false;
    }

    protected function afterFind() {
        parent::afterFind();
        $this->_oldPassword = $this->password;
    }

}
