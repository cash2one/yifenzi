<?php

/**
 * This is the model class for table "{{order_goods}}".
 *
 * The followings are the available columns in table '{{order_goods}}':
 * @property string $id
 * @property string $order_id
 * @property string $goods_id
 * @property string $goods_name
 * @property string $winning_code
 * @property string $goods_price
 * @property string $goods_image
 * @property integer $goods_number
 * @property string $addtime
 * @property string $single_price
 * @property integer $current_nper
 * @author qiuye.xu <qiuye.xu@g-mall.com>
 */
class YfzOrderGoods extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{order_goods}}';
    }

    #数据库连接

    public function getDbConnection()
    {
        return Yii::app()->gwpart;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('goods_number, current_nper', 'numerical', 'integerOnly' => true),
            array('order_id, goods_id, goods_price, addtime', 'length', 'max' => 10),
            array('goods_name', 'length', 'max' => 120),
            array('winning_code', 'length', 'max' => 8),
            array('goods_image', 'length', 'max' => 60),
            array('single_price', 'length', 'max' => 5),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, order_id, goods_id, goods_name, winning_code, goods_price, goods_image, goods_number, addtime, single_price, current_nper', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'order_id' => 'Order',
            'goods_id' => 'Goods',
            'goods_name' => 'Goods Name',
            'winning_code' => 'Winning Code',
            'goods_price' => 'Goods Price',
            'goods_image' => 'Goods Image',
            'goods_number' => 'Goods Number',
            'addtime' => 'Addtime',
            'single_price' => 'Single Price',
            'current_nper' => 'Current Nper',
        );
    }

    /**
     * 产品字段
     */
    public $max_nper, $goods_name, $column_id, $shop_price, $single_price, $recommended, $announced_time,$order_id;
	public $win_codes;

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
        $criteria = new CDbCriteria;
        $criteria->select = 'sum(t.goods_number) as goods_number,t.goods_id,t.current_nper,g.max_nper,g.goods_name,g.column_id,g.shop_price,g.single_price,g.recommended,g.announced_time,ogn.status';
        $criteria->join = "left join {{order}} o on o.order_id=t.order_id
                                left join {{yfzgoods}} g on g.goods_id=t.goods_id
								left join {{order_goods_nper}} ogn on t.goods_id=ogn.goods_id and t.current_nper=ogn.current_nper and t.order_id =ogn.order_id";
        $criteria->compare('t.goods_id', $this->goods_id);
        $criteria->compare('o.order_status', YfzOrder::STATUS_PAY_SUCCESS);
        $criteria->group = 't.current_nper';
        return new CActiveDataProvider($this, array(
            'pagination' => array(
                'pageSize' => 10,
            ),
            'criteria' => $criteria,
        ));
    }
	
	public function Sec2Time($time)
    {
        $value = array("days" => 0, "hours" => 0,"minutes" => 0);
  
        if($time >= 86400){
            $value["days"] = floor($time/86400);
            $time = ($time%86400);
        }
        if($time >= 3600){
            $value["hours"] = floor($time/3600);
            $time = ($time%3600);
        }
        if($time >= 60){
            $value["minutes"] = floor($time/60);
            $time = ($time%60);
        }
   
        $t=$value["days"] ."天"." ". $value["hours"] ."小时". $value["minutes"] ."分";
        return $t;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OrderGoods the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 获取 某个商品的某期的已购买数
     * @param type $goods_id
     * @param type $nper
     * @return object Description
     * @author qiuye.xu
     */
    public static function getGoodsNumber($goods_id, $nper)
    {
       //此处优化坐下数据缓存，
       $model = self::model()->find(array(
           'select' => 'sum(t.goods_number) as goods_number',
           'join' => 'left join {{order}} o on o.order_id=t.order_id',
           'condition'=>'t.goods_id=:id and t.current_nper =:nper and o.order_status=:status',
           'params' => array(':id'=>$goods_id,':nper'=>$nper,':status'=>  YfzOrder::STATUS_PAY_SUCCESS)
       ));
       return $model ? $model : self::model();
    }

	public static function getGoodsNumbers($goods_id, $nper)
    {
       //此处优化坐下数据缓存，
       $model = self::model()->find(array(
           'select' => 'sum(t.goods_number) as goods_number',
           'join' => 'left join {{order}} o on o.order_id=t.order_id',
           'condition'=>'t.goods_id=:id and t.current_nper =:nper and o.order_status=:status',
           'params' => array(':id'=>$goods_id,':nper'=>$nper,':status'=>  YfzOrder::STATUS_PAY_SUCCESS)
       ));
       if($model) return $model->goods_number;
    }
    /**
     * 产品某期的购买详情
     */
    public $memberId, $finishedTime, $orderSn;

    public function searchNper()
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.goods_id,t.current_nper,o.member_id as memberId,t.goods_number,o.finished_time as finishedTime,o.order_sn as orderSn,t.winning_code';
        $criteria->compare('t.goods_id', $this->goods_id);
        $criteria->compare('t.current_nper', $this->current_nper);
        $criteria->join = ' left join {{order}} o on t.order_id=o.order_id';
        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria
        ));
    }

    /**
     * 获取 某个用的某个商品的某期购买详情
     * @param type $mid
     */
    public function getWinningCode($mid)
    {
        $model = $this->find(array(
                    'select' => 't.goods_id,t.winning_code,t.addtime,ogn.winning_code as win_codes,g.single_price',
                    'join' => ' left join {{order}} o on o.order_id=t.order_id left join {{order_goods_nper}} ogn on ogn.order_id=o.order_id left join {{yfzgoods}} g on g.goods_id = t.goods_id',
                    'condition' => 't.goods_id=:id and t.current_nper=:nper and o.member_id=:mid and ogn.order_id=:order_id',
                    'params' => array(":id" => $this->goods_id, ":nper" => $this->current_nper, ":mid" => $mid, ':order_id'=>$this->order_id)
        ));
        return $model ? $model : $this;
    }
	/**
     * 获取 某个用的某个商品的某期购买详情
     * @param type $mid
     */
    /*public function getWinningCode($mid)
    {
        $model = $this->find(array(
                    'select' => 't.goods_id,t.winning_code,t.addtime',
                    'join' => ' left join {{order}} o on o.order_id=t.order_id',
                    'condition' => 't.goods_id=:id and t.current_nper=:nper and o.member_id=:mid',
                    'params' => array(":id" => $this->goods_id, ":nper" => $this->current_nper, ":mid" => $mid)
        ));
        return $model ? $model : $this;
    }*/
    /**
     * 根据订单id 获取单个用户购买单个产品的个数
     * @param type $order_id
     * @param type $good_id
     */
    public static function getNumberByOrderId($order_id,$goods_id)
    {
        return self::model()->find('order_id=:oid and goods_id=:gid',array(':oid'=>$order_id,'gid'=>$goods_id));
    }
	
	/**
     * 根据订单id 获取单个用户购买单个产品的个数
     * @param type $order_id
     * @param type $good_id
     */
    public static function getNumberByOrderIds($order_id,$goods_id)
    {
        return self::model()->find('order_id=:oid and goods_id=:gid',array(':oid'=>$order_id,'gid'=>$goods_id))->addtime;
    }
    /**
     * 统计每期产品购买次数
     * @param int $goodId 产品ID
     * @param int $currentNper 期数
     */
    public static function countGoods($goodsId,$currentNper)
    {
        $model = self::model()->find(array(
                    'select'=>'sum(t.goods_number) as goods_number',
                    'join' => "left join {{order}} o on o.order_id=t.order_id",
                    'condition'=>'t.goods_id=:id and t.current_nper=:nper and o.order_status=:status',
                    'params' => array(':id'=>$goodsId,':nper'=>$currentNper,':status'=>  YfzOrder::STATUS_PAY_SUCCESS)
                ));
        if($model) return $model->goods_number;
        else return 0;
    }

    /**
     * 根据订单括号获取当前订单的所有商品
     * @param $order_sn 订单括号
     * @return mixed|array
     */
    public function ordersnToGoods($order_id){
        $model = self::model()->findAll(array("condition"=>"order_id={$order_id}"));

        $str = '';
        foreach( $model as $k=>$v ){
            $str .= $v->goods_name;
        }

        if (strlen($str) > 30){
            $str = mb_substr($str,0,30,'utf-8');
            $str .= '...';
        }

        if($str) return $str;
        else return '';
    }
}
