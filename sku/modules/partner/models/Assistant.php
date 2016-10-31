<?php

/**
 *  助手、店小二 模型
 *  @author zhenjun_xu <412530435@qq.com>
 * The followings are the available columns in table '{{assistant}}':
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property string $member_id
 * @property string $real_name
 * @property string $avatar
 * @property integer $sex
 * @property string $mobile
 * @property string $email
 * @property integer $status
 * @property string $logins
 * @property string $description
 * @property integer $sort
 * @property string $create_time
 * @property string $update_time
 */
class Assistant extends CActiveRecord
{
    /**
     * @var 确认密码
     */
    public $confirmPassword;
    /**
     * @var 旧密码
     */
    public $oldPassword;
    /** @var  新密码 */
    public $newPassword;

    const STATUS_NO = 0;
    const STATUS_YES = 1;

    /**
     * 状态
     * @param null $k
     * @return array
     */
    public static function status($k = null)
    {
        $arr = array(
            self::STATUS_NO =>Yii::t('partnerModule.assistant', '未启用'),
            self::STATUS_YES =>Yii::t('partnerModule.assistant', '启用'),
        );
        return isset($arr[$k]) ? $arr[$k] : $arr;
    }
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    /**
     * 状态
     * @param null $k
     * @return array
     */
    public static function sex($k = null)
    {
        $arr = array(
            self::SEX_MALE =>Yii::t('partnerModule.assistant', '男'),
            self::SEX_FEMALE =>Yii::t('partnerModule.assistant', '女'),
        );
        return isset($arr[$k]) ? $arr[$k] : $arr;
    }

	public function tableName()
	{
		return '{{assistant}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(

            array('confirmPassword','compare','compareAttribute'=>'newPassword','on'=>'changePw'),
            array('oldPassword,newPassword,confirmPassword','required','on'=>'changePw'),
            array('newPassword','length','min'=>6),
			array('username, member_id, real_name, sex, mobile, status', 'required'),
            array('password,confirmPassword,','required','on'=>'insert'),
			array('sex, status, sort,mobile', 'numerical', 'integerOnly'=>true),
			array('username, password, salt, real_name, avatar, email', 'length', 'max'=>128),
			array('member_id, logins, create_time, update_time', 'length', 'max'=>11),
			array('mobile', 'length', 'max'=>16),
            array('password','length','min'=>6,'allowEmpty'=>true),
            array('username,mobile,email','unique'),
            array('email','email'),
            array('avatar', 'file', 'types' => 'jpg,gif,png', 'maxSize' => 1024 * 1024 * 5,
                'tooLarge' => Yii::t('partnerModule.assistant', '文件大于5M，上传失败！请上传小于5M的文件！'), 'allowEmpty' => true),
            array('username', 'match', 'pattern' => '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u',
                'message' => Yii::t('partnerModule.assistant', '公司名称 只能由中文、英文、数字及下划线组成')),
            array('confirmPassword','compare','compareAttribute'=>'password','on'=>'insert,update'),

            array('mobile', 'comext.validators.isMobile', 'errMsg' => Yii::t('partnerModule.assistant', '请输入正确的手机号码')),
			array('id, username, password, salt, member_id, real_name, avatar, sex, mobile, email,
			 status, logins, description, sort, create_time, update_time', 'safe', 'on'=>'search'),
            array('description,newPassword,confirmPassword,oldPassword','safe'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('partnerModule.assistant','主键'),
			'username' => Yii::t('partnerModule.assistant','用户名'),
			'password' => Yii::t('partnerModule.assistant','密码'),
			'salt' => Yii::t('partnerModule.assistant','密钥'),
			'member_id' => Yii::t('partnerModule.assistant','所属会员'),
			'real_name' => Yii::t('partnerModule.assistant','真实姓名'),
			'avatar' => Yii::t('partnerModule.assistant','头像'),
			'sex' => Yii::t('partnerModule.assistant','性别'),
			'mobile' => Yii::t('partnerModule.assistant','手机号码'),
			'email' => Yii::t('partnerModule.assistant','邮箱'),
			'status' => Yii::t('partnerModule.assistant','状态'),
			'logins' => Yii::t('partnerModule.assistant','登录次数'),
			'description' => Yii::t('partnerModule.assistant','说明'),
			'sort' => Yii::t('partnerModule.assistant','排序'),
			'create_time' => Yii::t('partnerModule.assistant','创建时间'),
			'update_time' => Yii::t('partnerModule.assistant','更新时间'),
			'confirmPassword' => Yii::t('partnerModule.assistant','确认密码'),
			'oldPassword' => Yii::t('partnerModule.assistant','旧密码'),
			'newPassword' => Yii::t('partnerModule.assistant','新密码'),
		);
	}

	public function search($member_id)
	{

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('salt',$this->salt,true);
		$criteria->compare('member_id',$member_id);
		$criteria->compare('real_name',$this->real_name,true);
		$criteria->compare('avatar',$this->avatar,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('logins',$this->logins,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('sort',$this->sort);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>10, //分页
            ),
            'sort'=>array(
                'defaultOrder'=>'sort DESC', //设置默认排序
            ),
		));
	}


	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * 检测输入的密码是否正确
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password) {
        return CPasswordHelper::verifyPassword($password . $this->salt, $this->password);
    }

    /**
     * 生成的密码哈希.
     * @param string $password
     * @return string $hash
     */
    public function hashPassword($password) {
        return CPasswordHelper::hashPassword($password . $this->salt);
    }

    public function afterFind(){
        $this->oldPassword = $this->password;
    }
    public function beforeSave(){
        if(parent::beforeSave()){
            if($this->isNewRecord){
                $this->create_time = time();
                $this->update_time = time();
                $this->salt = Tool::generateSalt();
                $this->password = self::hashPassword($this->password);
            }
            //修改详细资料
            if($this->scenario=='change'){
                $this->update_time = time();
                $this->password = empty($this->password) ? $this->oldPassword : self::hashPassword($this->password);
            }
            //修改密码
            if($this->scenario=='changePw'){
                $this->password = self::hashPassword($this->newPassword);
            }
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取卖家的所有店小二id
     * @param $member_id
     * @return array
     */
    public static function getAssistantIds($member_id)
    {
        $data =  Yii::app()->db->createCommand()
            ->select('id')->from(self::tableName())
            ->where('member_id='.$member_id)->queryAll();
        if(empty($data)){
            return null;
        }else{
            $ids = array();
            foreach($data as $v){
                $ids[] = $v['id'];
            }
            return $ids;
        }
    }
}
