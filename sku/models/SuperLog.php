<?php

/**
 * 商家后台操作日志模型
 * @author leo8705
 * 
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
class SuperLog extends CActiveRecord {

    const logCateFranchiseeChange = 1;    //切换加盟商操作
    const logCateFranchiseeUpdate = 2;    //更新加盟商信息操作
    const logCateFranchiseePwd = 3;     //修改加盟商密码操作
    const logCateFranchiseeArtileEdit = 4;   //修改加盟商文章操作
    const logCateFranchiseeArtileAdd = 5;   //添加加盟商文章操作
    const logCateFranchiseeArtileDel = 6;   //删除加盟商文章操作
    const logCateFranchiseeCustomerCreate = 7;  //添加加盟商客服操作
    const logCateFranchiseeCustomerUpdate = 8;  //更新加盟商客服信息操作
    const logCateFranchiseeCustomerDel = 9;   //删除加盟商客服操作
    const logCateFranchiseeUploadUpload = 10;  //上传加盟商图片操作
    const logCateFranchiseeUploadDel = 11;   //删除加盟商图片操作
    const logCateBrandCreate = 12;     //删除加盟商图片操作
    const logCateBrandUpdate = 13;     //删除加盟商图片操作
    const logCateStoreAddressCreate = 14;   //删除加盟商图片操作
    const logCateStoreAddressUpdate = 15;   //删除加盟商图片操作
    const logCateStoreAddressDelete = 16;   //删除加盟商图片操作
    const logCateStoreAddressSet = 17;   //删除加盟商图片操作
    const logCateFreightAreaCreate = 18;   //删除加盟商图片操作
    const logCateFreightAreaUpdate = 19;   //删除加盟商图片操作
    const logCateFreightTemplateCreate = 20;  //删除加盟商图片操作
    const logCateFreightTemplateUpdate = 21;  //删除加盟商图片操作
    const logCateFreightTemplateDelete = 22;  //删除加盟商图片操作
    const logCateGoodsCreate = 23;     //删除加盟商图片操作
    const logCateGoodsUpdateBase = 24;     //删除加盟商图片操作
    const logCateGoodsUpdateImportant = 25;     //删除加盟商图片操作
    const logCateGoodsDelete = 26;     //删除加盟商图片操作
    const logCateGoodsAdGoods = 27;     //删除加盟商图片操作
    const logCateOrderStockUp = 28;     //删除加盟商图片操作
    const logCateOrderCloseOrder = 29;     //删除加盟商图片操作
    const logCateOrderReturn = 30;     //删除加盟商图片操作
    const logCateOrderSignReturn = 31;     //删除加盟商图片操作
    const logCateOrderRefund = 32;     //删除加盟商图片操作
    const logCateScategoryCreate = 33;     //删除加盟商图片操作
    const logCateScategoryUpdate = 34;     //删除加盟商图片操作
    const logCateScategorySetStatus = 35;     //删除加盟商图片操作
    const logCateSlideCreate = 36;     //删除加盟商图片操作
    const logCateSlideUpdate = 37;     //删除加盟商图片操作
    const logCateSlideDelete = 38;     //删除加盟商图片操作
    const logCateStoreApply = 39;     //删除加盟商图片操作
    const logCateStoreUpdate = 40;     //删除加盟商图片操作
    const logCateFreightTypeUpdate = 41;
    const logMachineProductOrderDetailVerify = 42;    //加盟商盖网通订单验证消费

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
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{super_log}}';
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
            array('title, source, member_name', 'length', 'max' => 128),
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
            'id' => Yii::t('superLog','主键'),
            'category_id' => Yii::t('superLog','所属分类'),
            'title' => Yii::t('superLog','标题'),
            'type_id' => Yii::t('superLog','所属类型'),
            'create_time' => Yii::t('superLog','创建时间'),
            'source' => Yii::t('superLog','操作对象'),
            'source_id' => Yii::t('superLog','操作对象ID'),
            'member_id' => Yii::t('superLog','所属管理'), //会员id或者店小二id
            'member_name' => Yii::t('superLog','会员名称'),
            'ip' => 'IP',
            'is_admin' => Yii::t('superLog','是否管理员'),
        );
    }

    /**
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search($memberId = null) {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('category_id', $this->category_id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('type_id', $this->type_id);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('source', $this->source, true);
        $criteria->compare('source_id', $this->source_id, true);
        $criteria->compare('member_name', $this->member_name, true);
        $criteria->compare('ip', $this->ip, true);
//        $criteria->compare('is_admin', $this->is_admin);
        if ($memberId) {
            $criteria->compare('member_id', $memberId);
        }
        //店小二
        if (Yii::app()->user->getState('assistantId')) {
            $criteria->compare('is_admin', self::ADMIN_NO);
        } else {
            $assistantIds = Assistant::getAssistantIds($memberId);
            if (!empty($assistantIds))
                $criteria->addInCondition('member_id', $assistantIds, 'or');
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => 'id DESC', //设置默认排序
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SellerLog the static model class
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
        $logArr['category_id']= $category_id;
        $logArr['type_id']= $type_id;
        $logArr['create_time']= time();
        $logArr['source']= ucwords(Yii::app()->controller->id) . ucwords(Yii::app()->controller->action->id);
        $logArr['source_id']= $source_id;
        $logArr['member_id']= empty($assistantId) ? Yii::app()->user->id : $assistantId;
        $logArr['member_name']= !empty(Yii::app()->user->name) ? Yii::app()->user->name : Yii::app()->user->getState('gw');
        $logArr['ip']= Tool::ip2int(Yii::app()->request->userHostAddress);
        $logArr['is_admin']= empty($assistantId) ? self::ADMIN_YES : self::ADMIN_NO;
        $logArr['title']= $user_type . '(' . $logArr['member_name'] . ')' . $sub_title;
        return Yii::app()->db->createCommand()->insert('{{super_log}}',$logArr);
    }

}
