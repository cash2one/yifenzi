<?php
/**
 * Brand模型
 * ==============================================
 * 编码时间:2016年4月5日
 * ------------------------------------------------------------------------------------
 * 公司源码文件，未经授权不许任何使用和传播。
 * ==============================================
 * @date: 2016年4月5日
 * @author: xian shi deng
 * @version: G-emall child One Parts 1.0.0
 * @return: Object
 **/

class Brand extends CActiveRecord{
    public $file;
    #数据表名
    public function tableName()
    {
        return '{{brand}}';
    }
    public function rules() {
        return array(
            array('brand_name', 'required','message' => '品牌名称必填！'),

           // array('file', 'file', 'types' => 'jpg, gif, png,jpeg'),
        );
    }

    #数据库连接实例
    public function getDbConnection()
    {
        return Yii::app()->gwpart;
    }

    #数据表的字段注释attributeLabels
    public function attributeLabels()
    {
        return array(
            'brand_id'     => 'ID',
            'brand_name'   => '品牌名称',
            'brand_logo'    =>'品牌图片(Logo)',
            'brand_desc'     =>'品牌描述',
            'site_url'      =>'品牌网址',
            'sort_order'     =>'排序',
            'is_show'        =>'是否显示该品牌',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare( 'brand_id'  , $this->brand_id );
        $criteria->compare( 'brand_name'  , $this->brand_name );
        $criteria->compare( 'brand_logo'  , $this->brand_logo );
        $criteria->compare( 'brand_desc'  , $this->brand_desc );
        $criteria->compare( 'site_url'  , $this->site_url );
        $criteria->compare( 'sort_order'  , $this->sort_order );
        $criteria->compare( 'is_show'  , $this->is_show );
        return new CActiveDataProvider( $this, array(
            'criteria'  =>  $criteria,
        ) );
    }
    #类名
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


}
