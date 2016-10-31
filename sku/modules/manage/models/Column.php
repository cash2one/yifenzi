<?php 
/**
   * Column数据表对象换
   * ==============================================
   * 编码时间:2016年3月25日 
   * ------------------------------------------------------------------------------------
   * 公司源码文件，未经授权不许任何使用和传播。
   * ==============================================
   * @date: 2016年3月25日
   * @author: Derek
   * @version: G-emall child One Parts 1.0.0
   * @return: Object
   **/

class Column extends CActiveRecord{
    
    #数据表名
    public function tableName()
    {
        return '{{column}}';
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
            'id'            => 'ID',
            'column_name'   => '栏目名称',
            'column_type'   => '所属模型',
            'column_att'    => '栏目类型',
            'tourl'         => '访问地址',
            'parent_id'     => '栏目的父亲ID',
            'sort_order'    => '排序',
            'is_show'       => '是否显示此栏目',
            'column_desc'   => '栏目简介',
            'addtime'       => '添加时间',
            'altertime'     => '修改时间',
            'is_zone'       => '是否为首页专区显示',
            'zone_thumb'    => '专区图片',
			'column_logo'   => '栏目LOGO',

        );
    }

    public function rules(){
        return array(
            array('column_name,column_logo', 'required'),
        );
    }
    public function search()
    {
        $criteria = new CDbCriteria;
        
        $criteria->compare( 'id'  , $this->id );
        $criteria->compare( 'column_name'  , $this->column_name );
        $criteria->compare( 'column_type'  , $this->column_type );
        $criteria->compare( 'column_att'  , $this->column_att );
        $criteria->compare( 'tourl'  , $this->tourl );
        $criteria->compare( 'parent_id'  , $this->parent_id );
        $criteria->compare( 'sort_order'  , $this->sort_order );
        $criteria->compare( 'is_show'  , $this->is_show );
        $criteria->compare( 'column_desc'  , $this->column_desc );
        $criteria->compare( 'addtime'  , $this->addtime );
        $criteria->compare( 'altertime'  , $this->altertime );

        $criteria->order ="sort_order desc";

        
        return new CActiveDataProvider( $this, array(
            'criteria'  =>  $criteria,
        ) );
    }
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    /**
     * 根据栏目id，获取栏目名称
     * @param type $id
     */
    public static function getColumnbyId($id)
    {
        if(is_numeric($id)){
            //应该缓存所有的栏目  //具体怎么设置呢
            $column = self::model()->findByPk($id); 
            return $column ? $column->column_name : '无栏目名称';
        }
        return false;
    }
	
	const YUN_GOU_TYPE = 0;
	const OTHER_TYPE = 1;
	const INNER_ATT = 0;
	const OTHER_ATT = 1;
	
	/**
     * 获取所属模型
     * @return array 
     */
    public static function getColumType($key = null)
    {
        $type = array(
            self::OTHER_TYPE => Yii::t('colum', '其它模型'),
			self::YUN_GOU_TYPE => Yii::t('colum', '云购模型'),
        );
        return $key !== null ? (isset($type[$key]) ? $type[$key] : '其它模型') : $type;
    }
	
	/**
     * 获取栏目类型
     * @return array 
     */
    public static function getColumAtt($key = null)
    {
        $type = array(
            self::OTHER_ATT => Yii::t('colum', '其它栏目'),
			self::INNER_ATT => Yii::t('colum', '内部栏目'),
        );
        return $key !== null ? (isset($type[$key]) ? $type[$key] : '其它栏目') : $type;
    }
}
