<?php

/**
 *  {{goods_picture}} 模型
 *
 * The followings are the available columns in table '{{goods_picture}}':
 * @property string $id
 * @property string $path
 * @property integer $sort
 * @property string $target_id
 */
class GoodsPicture extends CActiveRecord {

    public function tableName() {
        return '{{goods_picture}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('path, goods_id', 'required'),
            array('sort', 'numerical', 'integerOnly' => true),
//            array('path', 'length', 'max' => 128),
            array('goods_id', 'length', 'max' => 11),
            array('id, path, sort, goods_id', 'safe', 'on' => 'search'),
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
            'id' => Yii::t('goodsPicture', 'id'),
            'path' => Yii::t('goodsPicture', '图片列表'),
            'sort' => Yii::t('goodsPicture', '排序'),
            'goods_id' => Yii::t('goodsPicture', '所属id'),
        );
    }

    public function search() {

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('path', $this->path, true);
        $criteria->compare('sort', $this->sort);
        $criteria->compare('goods_id', $this->goods_id, true);

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
     * 20151110 qiuye.xu 添加可以选择每次查询组图的个数
     * 根据商品id  查询对应的组图数据
     * @param int $target_id 商品id
     */
    public static function getImgList($goods_id,$limit='') {
        $condition = array(
            'condition' => 'goods_id=:gid',
            'params' => array(':gid' => $goods_id),
        );
        if(!empty($limit)){
            $condition['limit'] = $limit;
        }
        $imgData = self::model()->findAll($condition);
        return $imgData;
    }

    /**
     * 批量添加
     * @param array $imgList
     * @param int $goodsId
     * @return bool
     *  @author zhenjun_xu <412530435@qq.com>
     */
    public static function addArray(Array $imgList,$goodsId){
        if(empty($goodsId)) return false;
        foreach ($imgList as $v) {
            Yii::app()->db->createCommand()->insert('{{goods_picture}}', array(
                'path' =>$v,
                'goods_id' => $goodsId,
            ));
        }
        return true;
    }

}
