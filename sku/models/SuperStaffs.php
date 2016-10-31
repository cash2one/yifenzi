<?php

/**
 * This is the model class for table "{{super_staffs}}".
 *
 * The followings are the available columns in table '{{super_staffs}}':
 * @property integer $id
 * @property string $name
 * @property string $nick_name
 * @property string $head
 * @property string $password
 * @property string $salt
 * @property string $super_id
 * @property string $mobile
 * @property integer $role
 * @property string $rights
 * @property integer $status
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Supermarkets $super
 */
class SuperStaffs extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{super_staffs}}';
	}
	
	
	const STATUS_ENABLE = 1;
	const STATUS_DISABLE = 2;
	const STATUS_DELETE = 3;
	
	/**
	 * 状态用文字标示
	 * @param null|int $status 查询出来的状态
	 * @return array|null
	 */
	
	public static function getStatus($status = null) {
		$arr = array(
				self::STATUS_ENABLE => Yii::t('superStaffs', '启用'),
				self::STATUS_DISABLE => Yii::t('superStaffs', '禁用'),
				self::STATUS_DELETE => Yii::t('superStaffs', '已删除'),
		);
		if (is_numeric($status)) {
			return isset($arr[$status]) ? $arr[$status] : Yii::t('superStaffs', '未知状态');
		} else {
			return $arr;
		}
	}
	

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,password', 'required','on'=>'add'),
			array('name,salt,nick_name,status,mobile', 'required'),
			array('role, status, create_time', 'numerical', 'integerOnly'=>true),
			array(' password, salt', 'length', 'max'=>128),
                                                    array('name,nick_name', 'length','max'=>11),
			array('head, rights', 'length', 'max'=>256),
			array('super_id', 'length', 'max'=>11),
			array('mobile', 'length', 'max'=>16),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, nick_name, head, password, salt, super_id, mobile, role, rights, status, create_time', 'safe', 'on'=>'search'),
                                                    array('mobile','unique'),
                                                    array('mobile', 'match', 'pattern' => '/^(13[0-9]|15[7-9]|153|156|18[7-9])[0-9]{8}|[0-9]{8}$/', 'message' =>Yii::t('superStaffs', '请填写正确的手机号码或电话号码')),
                                                    array('name','checkName','on'=>'add'),
                                                     array('head', 'required', 'on' => 'add', 'message' => Yii::t('superStaffs', '请选择上传图片')),
                                                    array('head', 'file', 'types' => 'jpg,gif,png','maxSize' => 1024*1024, 'on' => 'add' ,'tooLarge' => Yii::t('superStaffs', '文件大于1M，上传失败！请上传小于1M的文件！'), 'allowEmpty' => true),
		);
	}
      /**
     * 员工不能重复
     * @param type $attribute
     * @param type $params
     */
            public function checkName($attribute,$params){
                        $name = Yii::app()->db->createCommand()
                         ->select('name')
                         ->from('{{super_staffs}}')
                         ->where('name=:name and super_id=:sid', array(':name'=>$this->name,':sid'=>$this->super_id))
                         ->queryRow();
                     if (!empty($name)) {
                         $this->addError('name', Yii::t('superStaffs', '员工{name}已存在',array('{name}'=>$this->name)));
                     }
                   
            }
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'super' => array(self::BELONGS_TO, 'Supermarkets', 'super_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('superStaffs', 'ID'),
			'name' => Yii::t('superStaffs', '名称'),
			'nick_name' =>Yii::t('superStaffs', '昵称'),
			'head' => Yii::t('superStaffs', '头像'),
			'password' => Yii::t('superStaffs', '密码'),
			'salt' => 'salt',
			'super_id' => Yii::t('superStaffs', '所属超市'),
			'mobile' => Yii::t('superStaffs', '电话'),
			'role' => Yii::t('superStaffs', '角色'),
			'rights' => Yii::t('superStaffs', '权限列表'),
			'status' => Yii::t('superStaffs', '状态'),
			'create_time' => 'Create Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('nick_name',$this->nick_name,true);
		$criteria->compare('head',$this->head,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('salt',$this->salt,true);
		$criteria->compare('super_id',$this->super_id,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('role',$this->role);
		$criteria->compare('rights',$this->rights,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time);
                                   
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function superSearch()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
	
		$criteria=new CDbCriteria;
	
		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true,'or');
		$criteria->compare('nick_name',$this->nick_name,true,'or');
		$criteria->compare('nick_name',$this->name,true,'or');
		$criteria->compare('super_id',$this->super_id,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('status',$this->status);
                                    $criteria->order = 'create_time DESC' ;
	
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SuperStaffs the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
