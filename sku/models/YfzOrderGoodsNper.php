<?php

/**
 * This is the model class for table "{{order_goods_nper}}".
 *
 * The followings are the available columns in table '{{order_goods_nper}}':
 * @property string $id
 * @property string $order_id
 * @property string $goods_id
 * @property string $member_id
 * @property string $goods_name
 * @property string $winning_code
 * @property integer $current_nper
 */
class YfzOrderGoodsNper extends CActiveRecord
{

    const STATUS_TURE = 0; // 未开奖
    const STATUS_ING = 1; //正在进行
    const STATUS_FALSE = 2; // 已开奖

    /**
     * @return string the associated database table name
     */

    public function tableName()
    {
        return '{{order_goods_nper}}';
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
            array('current_nper', 'numerical', 'integerOnly' => true),
            array('order_id, goods_id, member_id', 'length', 'max' => 10),
            array('goods_name', 'length', 'max' => 120),
            array('winning_code', 'length', 'max' => 8),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, order_id, goods_id, member_id, goods_name, winning_code, current_nper,order_sn,order_status', 'safe', 'on' => 'search'),
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
            'member_id' => 'Member',
            'goods_name' => 'Goods Name',
            'winning_code' => 'Winning Code',
            'current_nper' => 'Current Nper',
        );
    }

    public $order_status;
    public $is_address;
    public $invoice_no;
    public $order_sn;
	public $is_delivery;
	public $max_nper;
	public $announced_time;
	public $sort_order;
	public $sort;


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

        $criteria->select = 't.goods_id,t.order_id,t.current_nper,t.goods_name,t.member_id,o.order_status,o.is_address,o.invoice_no,o.order_sn';
        $criteria->join = "left join {{order}} o on o.order_id=t.order_id";
        if($this->goods_id) $criteria->compare('t.goods_id', $this->goods_id);
        if($this->order_status || $this->order_status == YfzOrder::STATUS_PAY_NO) $criteria->compare('o.order_status', $this->order_status);
        if($this->member_id) $criteria->compare('t.member_id', $this->member_id);
        $criteria->compare('o.order_sn', $this->order_sn);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return YzfOrderGoodsNper the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     *
     * @param type $id 产品id
     * @return type
     */
    public static function getResult($id,$nper)
    {
        $sql = "select * from {{order_goods_nper}} where goods_id=:id and current_nper=:nper";
        $command = Yii::app()->gwpart->createCommand($sql);
        $command->bindParam(':id', $id);
        $command->bindParam(':nper', $nper);
        return $command->queryRow();
    }

    //public $shop_price,$goods_thumb; //商品价格
    public static function getAnnounced($limit, $page = 0)
    {
        //中奖数
        $offer = ($page - 1) * $limit;
        $time = time();

        $_query = array(
            'select' => 'id,goods_id,member_id,current_nper,FROM_UNIXTIME(sumlotterytime,"%Y-%m-%d %H:%i:%s") as sumlotterytime,status',
            'condition'=> "sumlotterytime > :time and status=:status",
            'params'=> array(':time'=>$time,'status'=>  YfzOrderGoodsNper::STATUS_ING),
            'limit' => $limit,
            'offset' => $offer,
            'order' => 'sumlotterytime asc'
        );

        $announces = self::model()->findAll($_query);
        // 重组，获取商品信息
        $announce = self::_rebindAnnounce($announces);
        return $announce;
    }

	
    /**
     * 开奖操作
     * @param type $id 中奖id
     */
    public static function goAnnounced($id)
    {
        $connection = Yii::app()->gwpart;
        $result = $connection->createCommand()
                ->update('{{order_goods_nper}}', array('status' => self::STATUS_FALSE), 'id=:id and status!=:s and sumlotterytime < :time', array(':id' => $id, ':s' => self::STATUS_FALSE, ':time' => time())
        );
        return $result;
    }
    /**
     * 长轮询 返回新增的即将揭晓的产品 (未揭晓)
     * @param int $id //当前页面最大的id
     * @return array $announce
     */
    public static function getSendAnnounce($sumlotterytime)
    {
        //            $sql = 'select * from {{order_goods_nper}} where id > :id and sumlotterytime > :time and status != :status';
        $time = time();
        $npers = self::model()->findAll(array(
                        'select' => 'id,goods_id,member_id,current_nper,FROM_UNIXTIME(sumlotterytime,"%Y-%m-%d %H:%i:%s") as sumlotterytime,status',
                        'condition'=> 'sumlotterytime > :startTime and sumlotterytime < :endTime and status=:status',
                        'params' => array(':startTime'=> $time,':endTime'=>$sumlotterytime,":status"=>self::STATUS_TURE)
                    ));
//        return array();
        if(empty($npers)) return array(); //为空 返回空数组
        $announce = self::_rebindAnnounce($npers);
        return $announce;
    }


    /**
     * 重组即将揭晓产品
     * @param array $announce
     * @return array
     */
    protected static function _rebindAnnounce($announces)
    {
        $column_id = 0;
        //看用户是否为点击栏目再点击揭晓进来
//        print_r(Yii::app()->request->getParam('retUrl'));exit;
        if (Yii::app()->request->getParam('retUrl')){
            if (stripos(Yii::app()->request->getParam('retUrl'),'?column_id')){
                list($str,$column_id) = @explode('=',Yii::app()->request->getParam('retUrl'));
            }else{
                $column_id = 0;
            }
        }
        $announce = array();
        foreach ($announces as $k => $an) {
            $goods = YfzGoods::model()->findByPk($an->goods_id);
            $goods = $goods ? $goods : YfzGoods::model();
            if ( $column_id && $goods->column_id != $column_id && !is_numeric($column_id) ){
                continue;
            }
            //if(!$goods) continue;
            $announce[$k] = $an->attributes;
            $announce[$k]['thumb'] = ATTR_DOMAIN . '/' . YfzGoodsImage::getThumb($an->goods_id);
            $announce[$k]['price'] = $goods->shop_price;
            $announce[$k]['views'] = GoodsStatistics::getField($an->goods_id,"views");
            $announce[$k]['name'] = $goods->goods_name;
        }

        //对数据排序
        if ( Yii::app()->request->getParam('type')){
            switch(Yii::app()->request->getParam('type')){
                case 'hot':
                    if (!$column_id) break;
                    usort($announce, function($a, $b){
                        $a_num = $a['views'];
                        $b_num = $b['views'];

                        if ($a_num == $b_num)
                            return 0;
                        return ($a_num > $b_num) ? -1 : 1;
                    });
                    break;
                case 'pricex':
                    usort($announce, function($a, $b){
                        $a_num = $a['price'];
                        $b_num = $b['price'];

                        if ($a_num == $b_num)
                            return 0;
                        return ($a_num > $b_num) ? -1 : 1;
                    });
                    break;
                case 'pricem':
                    usort($announce, function($a, $b){
                        $a_num = $a['price'];
                        $b_num = $b['price'];

                        if ($a_num == $b_num)
                            return 0;
                        return ($a_num > $b_num) ? 1 : -1;
                    });
                    break;
                case 'news':
                    usort($announce, function($a, $b){
                        $a_num = strtotime($a['sumlotterytime']);
                        $b_num = strtotime($b['sumlotterytime']);

                        if ($a_num == $b_num)
                            return 0;
                        return ($a_num > $b_num) ? 1 : -1;
                    });
                    break;
            }
        }
//        print_r($announce);exit;
        return $announce;
    }

    public $shop_price;
    public $single_price;
    /**
     * 获取中奖订单的详细信息
     * @param int $id 产品id
     * @param int $currentNper 产品期数
     * @return object
     */
    public function getDetail($id,$currentNper)
    {
        $model = $this->model()->find(array(
                    'select'=>'t.current_nper,t.goods_name,t.member_id,t.winning_code,t.order_id,t.goods_id,g.shop_price,g.single_price',
                    'join' => 'left join {{yfzgoods}} g on g.goods_id=t.goods_id',
                    'condition'=> 't.goods_id=:gid and t.current_nper=:nper',
                    'params' => array(':gid'=>$id,':nper'=>$currentNper)
                ));
        if(!$model) { //无中奖信息 退出
            throw new CHttpException('404',"ID为{$id}的产品第{$currentNper}期暂无中奖信息");
            Yii::log("ID为{$id}的产品第{$currentNper}期暂无中奖信息",  CLogger::LEVEL_INFO);
        }
        return $model;
    }

}
