<?php
/**
 * Advertising模型
 * ==============================================
 * 编码时间:2016年4月6日
 * ------------------------------------------------------------------------------------
 * 公司源码文件，未经授权不许任何使用和传播。
 * ==============================================
 * @date: 2016年4月6日
 * @author:  deng
 * @version: G-emall child One Parts 1.0.0
 * @return: Object
 **/

class Advertising extends CActiveRecord{
    const YFZ_ADVERTISING_IS_SHOW = 1; //为上架
    const YFZ_ADVERTISING_IS_SHOW_NO = 0; //为上架
    const YFZ_ADVERTISING_TYPE_SLIDE = 2; //首页轮播图
    #数据表名
    public function tableName()
    {
        return '{{advertising}}';
    }
    public function rules() {
        return array(
            array('advertising_name', 'required','message' => '品牌名称必填！'),
            array('img_h', 'required','message' => '图片高度必填！'),
            array('img_w', 'required','message' => '图片宽度必填！'),

            // array('file', 'file', 'types' => 'jpg, gif, png,jpeg'),
        );
    }

    #数据库连接实例
    public function getDbConnection()
    {
        return Yii::app()->gwpart;
    }

    /**
     * 根据类型读取数据
     * @param int $types 广告类型
     * @return Array
     */
    public  function getTypesData( $types=self::YFZ_ADVERTISING_TYPE_SLIDE ){
        $sql = "select tourl,img from ".$this->tableName()." where is_show=".self::YFZ_ADVERTISING_IS_SHOW." and types=".self::YFZ_ADVERTISING_TYPE_SLIDE ." order by addtime desc limit 3";
//       return $sql;
        return $this->getDbConnection()->createCommand( $sql )->queryAll();
    }

    #数据表的字段注释attributeLabels
    public function attributeLabels()
    {
        return array(
            'id'     => 'ID',
            'advertising_name'  => '广告名称',
            'img'               =>'图片',
            'tourl'             =>'链接地址',
            'addtime'           =>'添加时间',
            'types'             =>'类型',
            'is_show'           =>'是否显示该广告',
            'img_h'             =>'图片高度',
            'img_w'             =>'图片宽度',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare( 'id'  , $this->id );
        $criteria->compare( 'advertising_name'  , $this->advertising_name );
        $criteria->compare( 'img'  , $this->img );
        $criteria->compare( 'tourl'  , $this->tourl );
        $criteria->compare( 'addtime'  , $this->addtime );
        $criteria->compare( 'types'  , $this->types );
        $criteria->compare( 'is_show'  , $this->is_show );
        $criteria->compare( 'img_h'  , $this->img_h );
        $criteria->compare( 'img_w'  , $this->img_w );
        return new CActiveDataProvider( $this, array(
            'criteria'  =>  $criteria,
        ) );
    }
    #类名
    public static function model($className = __CLASS__) {
        return parent::model($className);
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
            self::TYPE_IMAGE => Yii::t('advertising', '图片'),
            self::TYPE_TEXT => Yii::t('advertising', '文字'),
            self::TYPE_SLIDE => Yii::t('advertising', '幻灯')
        );
        if ($key === false)
            return $type;
        return $type[$key];
    }

}
