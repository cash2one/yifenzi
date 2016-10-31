<?php

/**
 * This is the model class for table "{{parnet_log}}".
 *
 * The followings are the available columns in table '{{parnet_log}}':
 * @property string $id
 * @property integer $category_id
 * @property string $title
 * @property integer $type_id
 * @property string $create_time
 * @property string $source
 * @property string $source_id
 * @property string $member_id
 * @property string $member_name
 * @property string $ip
 * @property integer $is_admin
 */
class ParnetLog extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{parnet_log}}';
    }

     /**
     * 所属分类
     */
    const CAT_LOGIN = 1; //登录  login-succ
    const CAT_MEMBERS = 2; //修改密码
    const CAT_BIZ = 3;  //加盟商相关
    const CAT_MALL = 4; //订单相关 IntegrealMall
    const CAT_COMPANY = 5; //店铺、商品相关  StoreMana
    //是否管理员
    const ADMIN_NO = 0;
    const ADMIN_YES = 1;
     //操作类型
    const logTypeInsert = 1;   //插入操作类型
    const logTypeUpdate = 2;   //更新操作类型
    const logTypeDel = 3;    //删除操作类型

    public static function showType($k = null) {
        $arr = array(
            self::logTypeInsert => 'insert',
            self::logTypeUpdate => 'update',
            self::logTypeDel => 'delete',
        );
        return is_numeric($k) ? (isset($arr[$k]) ? $arr[$k] : null) : $arr;
    }
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('category_id, title, type_id, create_time, source, source_id, member_id, member_name, ip, is_admin', 'required'),
            array('category_id, type_id, is_admin', 'numerical', 'integerOnly' => true),
            array(' source, member_name', 'length', 'max' => 128),
            array('create_time, source_id, member_id, ip', 'length', 'max' => 11),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, category_id, title, type_id, create_time, source, source_id, member_id, member_name, ip, is_admin', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('parnetLog','主键'),
            'category_id' => Yii::t('parnetLog','所属分类'),
            'title' => Yii::t('parnetLog','标题'),
            'type_id' => Yii::t('parnetLog','所属类型'),
            'create_time' => Yii::t('parnetLog','创建时间'),
            'source' => Yii::t('parnetLog','操作对象'),
            'source_id' => Yii::t('parnetLog','操作对象ID'),
            'member_id' => Yii::t('parnetLog','所属管理'),
            'member_name' => Yii::t('parnetLog','管理员名称'),
            'ip' =>  Yii::t('parnetLog','IP'),
            'is_admin' => Yii::t('parnetLog','是否管理员（0否，1是）'),
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('category_id', $this->category_id);
        $criteria->compare('title', $this->title);
        $criteria->compare('type_id', $this->type_id);
        $criteria->compare('create_time', $this->create_time);
        $criteria->compare('source', $this->source);
        $criteria->compare('source_id', $this->source_id);
        $criteria->compare('member_id', $this->member_id);
        $criteria->compare('member_name', $this->member_name);
        $criteria->compare('ip', $this->ip);
        $criteria->compare('is_admin', $this->is_admin);
        $criteria->order = 'create_time DESC';
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ParnetLog the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    
     /**
     * 添加卖家平台操作记录
     * @param int $category_id 所属分类
     * @param int $type_id 操作类型
     * @param int $source_id 操作对象ID
     * @param string $sub_title 标题
     * @return bool
     */
    public static function create($category_id = 0, $type_id = 0, $source_id = 0, $sub_title = '') {
        $assistantId = Yii::app()->user->getState('assistantId'); //店小二id
        //添加操作日志
        $user_type = empty($assistantId) ? '商家用户' : '店小二';
        $logArr = array();
        $logArr['category_id'] = $category_id;
        $logArr['type_id'] = $type_id;
        $logArr['create_time'] = time();
        $logArr['source'] = ucwords(Yii::app()->controller->id) . ucwords(Yii::app()->controller->action->id);
        $logArr['source_id'] = $source_id;
        $logArr['member_id'] = empty($assistantId) ? Yii::app()->user->id : $assistantId;
        $user_name  = Yii::app()->user->getState('username');
        $gai_number  = Yii::app()->user->getState('gai_number');
        $logArr['member_name'] = !empty($user_name) ? $user_name: (!empty($gai_number)?$gai_number:'');
        $logArr['ip'] = Tool::ip2int(Yii::app()->request->userHostAddress);
        $logArr['is_admin'] = empty($assistantId) ? self::ADMIN_YES : self::ADMIN_NO;
        $logArr['title'] = $user_type . '(' . $logArr['member_name'] . ')' . $sub_title;
        return Yii::app()->db->createCommand()->insert('{{parnet_log}}', $logArr);
    }

}
