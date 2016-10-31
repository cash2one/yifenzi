<?php

/**
 * APP版本管理模型
 * @author qinghao.ye <qinghaoye@sina.com>
 * The followings are the available columns in table '{{app}}':
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $system_type
 * @property string $url
 * @property string $size
 * @property string $version
 * @property string $version_name
 * @property string $web_log
 * @property string $mobile_log
 * @property string $remark
 * @property integer $is_visible
 * @property integer $is_published
 * @property string $user_id
 * @property string $create_time
 * @property string $update_time
 * @property string $ip
 * @property string $apk_name
 * @property integer $is_auto_download
 */
class AppVersion extends CActiveRecord {

    public $game_type; //游戏类型

    public $ios_select;                     //苹果安装文件获取类型
    public $ios_ipd;                        //苹果安装文件

    const IOS_SELECT_URL = 0;               //IOS文件选择填写APP Store URL
    const IOS_SELECT_IPD = 1;               //IOS文件选择从后台上传

    /**
     * ios安装文件来源  自己上传ipd安装包/填写appstrom URL
     * @param bool|false $key
     * @return array
     */
    public static function getIosSelect($key = false){
        $status = array(
            self::IOS_SELECT_URL => 'APP Store URL',
            self::IOS_SELECT_IPD => '请上传ipd文件',
        );
        if ($key === false)
            return $status;
        return $status[$key];
    }

    /**
     * 获取系统标识
     */
    const FLAG_TYPE_ALL = 0;     //未知类型
    const FLAG_TYPE_SOFTWARE = 1;//软件
    const FLAG_TYPE_GAME = 2; //游戏

    //以常量区分何种标识
    const SOFTWARE_DIVIDING_LINE_GAME = 20; //小于或等于20的为软件分类，大于20为游戏分类

    public static function getFlag($key = false) {
        $status = array(
            0=>'未知',
            self::FLAG_TYPE_SOFTWARE => '软件',
            self::FLAG_TYPE_GAME => '游戏',
        );
        if ($key === false)
            return $status;
        return $status[$key];
    }

	/**
	 * 获取系统类型
	 * @return array
	 */
	const SYSTEM_TYPE_ANDROID = 1;
	const SYSTEM_TYPE_IOS = 2;
	
	public static function getSystemType($key = false) {
		$status = array(
				0=>'其他',
				self::SYSTEM_TYPE_ANDROID => 'Android',
				self::SYSTEM_TYPE_IOS => 'IOS',
		);
		if ($key === false)
			return $status;
		return $status[$key];
	}
	
	/**
	 * 获取APP类型
	 * @return array
	 */
	const APP_TYPE_TOKEN = 1;
	const APP_TYPE_SHOPKEEPER= 2;
	const APP_TYPE_GAIWANGAPP = 3;						//盖象商城APP
	const APP_TYPE_SKU_SHOPKEEPER = 4;					//SKU盖掌柜
	const APP_TYPE_SKU_VENDING_MACHINE = 5;			    //SKU售货机
	const APP_TYPE_SKU_FRESH_MACHINE = 6;				//SKU生鲜机
    const APP_TYPE_GT_INVENTORY_APP = 7;               //盖网通盘点
    const APP_TYPE_SKU_FANHE_MACHINE = 8;               //饭盒机
    const APP_TYPE_GAME_SANGUORUN = 21;                //三国跑跑游戏
    const APP_TYPE_GAME_PAIPAIMENG = 22;               //啪啪萌僵尸游戏
    const APP_TYPE_GAME_GOLDENMINER = 23;              //盖付通黄金矿工游戏
    const APP_TYPE_GAME_SHENTOULILI = 24;              //神偷莉莉游戏
    const APP_TYPE_GAME_PANZHIHUA = 25;                //攀枝花抢水果游戏

	public static function getAppType($key = false) {
		$status = array(
				0 =>'其他',
				self::APP_TYPE_TOKEN => '盖付通',
				self::APP_TYPE_SHOPKEEPER => '掌柜',
				self::APP_TYPE_GAIWANGAPP => '盖象商城APP',
				self::APP_TYPE_SKU_SHOPKEEPER => 'SKU盖掌柜',
				self::APP_TYPE_SKU_VENDING_MACHINE => 'SKU售货机',
				self::APP_TYPE_SKU_FRESH_MACHINE => 'SKU生鲜机',
				self::APP_TYPE_SKU_FANHE_MACHINE => 'SKU饭盒机',
                self::APP_TYPE_GT_INVENTORY_APP => '盖网通盘点',
                self::APP_TYPE_GAME_SANGUORUN => '三国跑跑游戏',
                self::APP_TYPE_GAME_PAIPAIMENG => '啪啪萌僵尸游戏',
                self::APP_TYPE_GAME_GOLDENMINER => '盖付通黄金矿工游戏',
                self::APP_TYPE_GAME_SHENTOULILI => '神偷莉莉游戏',
                self::APP_TYPE_GAME_PANZHIHUA => '攀枝花抢水果游戏',
		);
		if ($key === false)
			return $status;
		return $status[$key];
	}


    /**
     * 根据不同的标识显示不同的列表
     * @param int $flag
     * @param bool $key
     * @return array
     */
    public static function getAppList($flag = self::FLAG_TYPE_ALL, $key = false){
        $array = self::getAppType();
        $list = array();
        if(empty($array))return $list;
        foreach($array as $k => $v){
            if($flag == self::FLAG_TYPE_SOFTWARE){
                if($k > self::SOFTWARE_DIVIDING_LINE_GAME)continue;
                $list[$k] = $v;
            }elseif($flag == self::FLAG_TYPE_GAME){
                if($k <= self::SOFTWARE_DIVIDING_LINE_GAME)continue;
                $list[$k] = $v;
            }else{
                $list = $array;
            }
        }
        if($key === false)
            return $list;
        return $list[$key];
    }

    public function tableName() {
        return '{{app}}';
    }

    public function getDbConnection() {
        return Yii::app()->gw;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('system,system_type,type,app_type, name, version, version_name, web_log, mobile_log, is_visible, is_published, is_auto_download', 'required'),
            array('is_visible, is_published, is_auto_download', 'numerical', 'integerOnly' => true),
            array('name, url,img_url, version, version_name, apk_name', 'length', 'max' => 128),
            array('size', 'length', 'max' => 14),
            array('user_id, create_time, update_time, ip', 'length', 'max' => 11),
            array(
                'apk_name', 'file', 'types' => 'apk,ipa', 'maxSize' => 1024 * 1024 * 50, 'allowEmpty' => true,
                'tooLarge' => Yii::t('appVersion', Yii::t('appVersion', '文件 最大不超过50MB，请重新上传!'))
            ),
            array(
                'ios_ipd', 'file', 'types' => 'apk,ipa', 'maxSize' => 1024 * 1024 * 50, 'allowEmpty' => true,
                'tooLarge' => Yii::t('appVersion', Yii::t('appVersion', '文件 最大不超过50MB，请重新上传!'))
            ),
            array('id, name, url,img_url,ios_img_url, size, version, version_name, web_log, mobile_log, remark, is_visible, is_published, user_id, create_time, update_time, ip, apk_name, is_auto_download,ios_ipd', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', '主键'),
            'type' => Yii::t('app', '标识'),
            'system' => Yii::t('app', '系统类型'),
        	'system_type' =>Yii::t('app','系统类型'),
        	'app_type' =>Yii::t('app','APP类型'),
            'name' => Yii::t('app', '名称'),
            'url' => Yii::t('app', '链接'),
        	'img_url'=>Yii::t('app', '图片链接'),
            'ios_img_url'=>Yii::t('app','IOS上传安装包所需的图片'),
            'size' => Yii::t('app', '大小(KB)'),
            'version' => Yii::t('app', '版本号'),
            'version_name' => Yii::t('app', '版本名称'),
            'web_log' => Yii::t('app', 'WEB更新日志'),
            'mobile_log' => Yii::t('app', '移动端更新日志'),
            'remark' => Yii::t('app', '备注'),
            'is_visible' => Yii::t('app', '显示'),
            'is_published' => Yii::t('app', '发布'),
            'user_id' => Yii::t('app', '发布者'),
            'create_time' => Yii::t('app', '发布时间'),
            'update_time' => Yii::t('app', '更新时间'),
            'ip' => Yii::t('app', '发布者IP'),
            'apk_name' => Yii::t('app', 'APK文件名'),
            'is_auto_download' => Yii::t('app', '自动下载'),
            'ios_select' => Yii::t('app','IOS安装包上传类型（后台上传/填写app Store URL）'),
            'ios_ipd' => Yii::t('app','IOS安装文件')
        );
    }

    public function search() {

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('system_type', $this->system_type, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('url', $this->url, true);
        $criteria->compare('size', $this->size, true);
        $criteria->compare('version', $this->version, true);
        $criteria->compare('version_name', $this->version_name, true);
        $criteria->compare('web_log', $this->web_log, true);
        $criteria->compare('mobile_log', $this->mobile_log, true);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('is_visible', $this->is_visible);
        $criteria->compare('is_published', $this->is_published);
        $criteria->compare('user_id', $this->user_id, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_time', $this->update_time, true);
        $criteria->compare('ip', $this->ip, true);
        $criteria->compare('apk_name', $this->apk_name, true);
        $criteria->compare('is_auto_download', $this->is_auto_download);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20, //分页
            ),
            'sort' => array(
            //'defaultOrder'=>' DESC', //设置默认排序
            ),
        ));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 获取系统
     * @return array
     */
    const SYSTEM_ANDROID = 1;
    const SYSTEM_IOS = 2;
    const SYSTEM_TOKEN_IOS = 3;
    const SYSTEM_TOKEN_ANDROID = 4;
    const SYSTEM_SHOPKEEPER_IOS = 5;		//掌柜(ios)
    const SYSTEM_SHOPKEEPER_ANDROID = 6;		//掌柜(android)
    const TYPE_ANDROID = 1;
    const TYPE_IOS = 2;
    public static function getSystem($key = false) {
        $status = array(
        	0=>'其他',
            self::SYSTEM_ANDROID => 'Android',
            self::SYSTEM_IOS => 'IOS',
            self::SYSTEM_TOKEN_IOS => 'TOKEN_IOS',
            self::SYSTEM_TOKEN_ANDROID => 'TOKEN_ANDROID',
            self::SYSTEM_SHOPKEEPER_IOS => 'SHOPKEEPER_IOS',
            self::SYSTEM_SHOPKEEPER_ANDROID => 'SHOPKEEPER_ANDROID',
        );
        if ($key === false)
            return $status;
        return $status[$key];
    }
    
    public static function getType($key = false) {
    	$status = array(
    			0=>'其他',
    			self::TYPE_ANDROID => 'Android',
    			self::TYPE_IOS => 'IOS',
    	);
    	if ($key === false)
    		return $status;
    	return $status[$key];
    }

    /**
     * 获取状态
     * @return array
     */
    const VISIBLE_NO = 0;
    const VISIBLE_YES = 1;
    public static function getVisible($key = false) {
        $status = array(
            self::VISIBLE_YES => Yii::t('appVersion', '是'),
            self::VISIBLE_NO => Yii::t('appVersion', '否')
        );
        if ($key === false)
            return $status;
        return $status[$key];
    }
    /**
     * 获取状态
     * @return array
     */
    const PUBLISHED_NO = 0;
    const PUBLISHED_YES = 1;
    public static function getPublished($key = false) {
        $status = array(
            self::PUBLISHED_YES => Yii::t('appVersion', '是'),
            self::PUBLISHED_NO => Yii::t('appVersion', '否')
        );
        if ($key === false)
            return $status;
        return $status[$key];
    }
    /**
     * 获取状态
     * @return array
     */
    const AUTODOWNLOAD_NO = 0;
    const AUTODOWNLOAD_YES = 1;
    public static function getAutoDownload($key = false) {
        $status = array(
            self::AUTODOWNLOAD_YES => Yii::t('appVersion', '是'),
            self::AUTODOWNLOAD_NO => Yii::t('appVersion', '否')
        );
        if ($key === false)
            return $status;
        return $status[$key];
    }
    
    public static function getUserNameById($userId){
        $user = User::model()->findByPk($userId);
        if(!empty($user)){
            return $user->username;
        }
    }
    /**
     * 删除后的操作
     */
    protected function afterDelete() {
        parent::afterDelete();
        if ($this->apk_name)
            UploadedFile::delete(Yii::getPathOfAlias('att') . DS . $this->apk_name);
        if($this->img_url)
        	UploadedFile::delete(Yii::getPathOfAlias('att') . DS . $this->img_url);
    }
    
}
