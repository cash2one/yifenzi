<?php
/**
 * APP 广告位管理模型
 * @author qinghao.ye <qinghaoye@sina.com>
 * @property string $id
 * @property string $name
 * @property string $code
 * @property string $content
 * @property integer $type
 * @property integer $status
 * @property integer $width
 * @property integer $height
 */
class AppAdvert extends CActiveRecord {

    public function tableName() {
        return '{{app_advert}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('name, code, type, width,height,status,app_id', 'required'),
            array('type, status, width, height,click,app_id', 'numerical', 'integerOnly' => true),
            array('name, code', 'length', 'max' => 128),
            array('content', 'safe'),
            array('code', 'unique', 'caseSensitive' => false, 'className' => 'appAdvert', 'message' => '编码 "{value}" 已经被注册，请更换'),
            array('id, name, code, content, type, status, width, height,app_id,click', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'pictureCount' => array(self::STAT, 'AppAdvertPicture', 'advert_id'),
            'pictures'=>array(self::HAS_MANY,'AppAdvertPicture', 'advert_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('appAdvert', '主键'),
            'name' => Yii::t('appAdvert', '名称'),
            'code' => Yii::t('appAdvert', '编码'),
            'content' => Yii::t('appAdvert', '内容'),
            'type' => Yii::t('appAdvert', '类型'),
            'status' => Yii::t('appAdvert', '状态'),
            'width' => Yii::t('appAdvert', '图片宽度'),
            'height' => Yii::t('appAdvert', '图片高度'),
        );
    }

    public function search() {

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('status', $this->status);
        $criteria->compare('width', $this->width);
        $criteria->compare('height', $this->height);

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
     * 保存之后处理 
     */
    protected function afterSave() {
        parent::afterSave();
        AppAdvert::clearAdCache($this->code);
        self::generateRelevantCache($this); //生成广告缓存
    }
    
    
    /**
     * 删除后的操作
     * 删除当前广告位下的的广告图片
     */
    protected function afterDelete() {
        parent::afterDelete();
        $advertPictures = AppAdvertPicture::model()->findAll('advert_id=:aid', array(':aid' => $this->id));
        foreach ($advertPictures as $ap)
            $ap->delete();
        AppAdvert::clearAdCache($this->code);
    }

    /**
     * 获取状态
     * @return array
     */
    const STATUS_ENABLE = 1;
    const STATUS_DISABLED = 0;
    public static function getAppAdvertStatus($key = false) {
        $status = array(
            self::STATUS_ENABLE => Yii::t('appAdvert', '启用'),
            self::STATUS_DISABLED => Yii::t('appAdvert', '禁用')
        );
        if ($key === false)
            return $status;
        return $status[$key];
    }
    
    /**
     * 获取广告类型
     * @param int $key
     * @return string|array
     */
    const TYPE_IMAGE = 1;
    const TYPE_SLIDE = 2;
    const TYPE_TEXT = 3;
    public static function getAppAdvertType($key = false) {
        $type = array(
            self::TYPE_IMAGE => Yii::t('appAdvert', '图片'),
            self::TYPE_TEXT => Yii::t('appAdvert', '文字'),
            self::TYPE_SLIDE => Yii::t('appAdvert', '幻灯')
        );
        if ($key === false)
            return $type;
        return $type[$key];
    }

    /**
     * 差异广告位编码
     * @return array
     */
    public static function differenceCode() {
//        return array('INDEX_HEADER_SLIDE_LEFT', 'INDEX_SLIDE_UP', 'INDEX_SLIDE_LEFT', 'HOLTEL_SLIDE_LEFT');
    }

    /**
     * 获取常规广告缓存数据
     * @param string $code 广告位编码
     */
    const CACHEDIR = 'appAdverts';
    public static function getConventionalAdCache($code) {
        if (!$advert = Tool::cache(self::CACHEDIR)->get($code)) // 获取缓存数据
            $advert = self::generateConventionalAd($code);
        return $advert;
    }
    
    public static function clearAdCache($code) {
    	return Tool::cache(self::CACHEDIR)->set($code, null);
    }
    
    /**
     * 根据广告位更新相应缓存
     * @param mixed $aid        对象实例或广告位ID
     */
    public static function generateRelevantCache($aid) {
        $advert = is_object($aid) && get_class($aid) == 'AppAdvert' ? $aid 
                : AppAdvert::model()->findByPk($aid, 'status = :status', array(':status' => AppAdvert::STATUS_ENABLE));
        if (!empty($advert) && $advert->status == AppAdvert::STATUS_ENABLE) {
                self::generateConventionalAd($advert->code);
        }else{
            Tool::cache(AppAdvert::CACHEDIR)->delete($advert->code);
        }
    }
    /**
     * 生成所有广告缓存
     */
    public static function generateAllAppAdvertCache(){
        self::generateConventionalAd();        // 生成常规广告
    }
    
    /**
     * 生成广告
     */
    public static function generateConventionalAd($code = null) {
        $where = '';
        $whereParam = array();
        $now = time();
        if (!is_null($code)){
            $where = 't.code=:code AND ';
            $whereParam = array(':code'=>$code);
        }
        $where .= 't.status = :status And ap.status = :ap_status';
        $whereParam[':status'] = AppAdvert::STATUS_ENABLE;
        $whereParam[':ap_status'] = AppAdvertPicture::STATUS_ENABLE;
        
        $ap = Yii::app()->db->createCommand()->from('{{app_advert}} as t')->select('t.code,t.type,t.status as p_status ,ap.*,ap.status')
                ->join('{{app_advert_picture}} as ap', 't.id = ap.advert_id AND ap.status='. AppAdvertPicture::STATUS_ENABLE
                        .' AND ap.start_time<="'.$now.'" AND (ap.end_time=0 or ap.end_time>="'.$now.'")'
                        )
                ->where($where,$whereParam)
                ->order('t.code, ap.group, ap.seat, ap.sort DESC, ap.id DESC')
                ->queryAll();
        $data = array();
        if(!empty($ap)){
            foreach ($ap as $k=>$v) {
                $data[$v['code']][$k] = $v;
                $data[$v['code']][$k]['picture'] =ATTR_DOMAIN . '/' .$v['picture'];
            }
            foreach ($data as $key => $val){
                Tool::cache(self::CACHEDIR)->set($key, $val);
            }
        }
        if (isset($data[$code]))
            $data = $data[$code];
        return $data;
    }
    
    
}
