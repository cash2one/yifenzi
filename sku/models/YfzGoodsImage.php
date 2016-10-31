<?php

/**
 * 一份子图片模型 
 */
class YfzGoodsImage extends CActiveRecord
{
    /**
     * 数据表
     * @return string
     */
    public function tableName()
    {
        return '{{goods_image}}';
    }
    
    public function getDbConnection()
    {
        return Yii::app()->gwpart;
    }

    public function rules()
    {
        return array(
            array('goods_id,goods_thumb,show_image1','required'),
            array('sort_order', 'numerical', 'integerOnly' => true),
            array('goods_id', 'length', 'max' => 10),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('image_id, goods_id, goods_thumb, show_image1, show_image2, show_image3, show_image4, show_image5, sort_order', 'safe', 'on' => 'search'),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'goods_thumb' => '缩略图',
            'show_image1' => '展示图'
        );
    }
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    /**
     * 获取产品的缩略图
     * @param type $id
     */
    public static function getThumb($id)
    {
        $model = self::model()->find("goods_id=:id",array(':id'=>$id));
        return $model ? $model->goods_thumb : null;
    }
}


