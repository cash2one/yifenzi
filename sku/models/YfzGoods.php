<?php 
/**
   * Goods数据表对象换
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

class YfzGoods extends CActiveRecord
{
    const IS_CLOSED_FALSE = 0; //
    const IS_CLOSED_TRUE = 1; //删除商品
    const IS_SALES_FALSE = 0; //停售
    const IS_SALES_TRUE = 1; // 在售
    const IS_RECOMMENDED_FASE=0;
    const IS_RECOMMENDED_TRUE=1;
    
    #数据表名
    public function tableName()
    {
        return '{{yfzgoods}}';
    }
    
    ##数据库连接
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
            array('column_id,goods_name,shop_price, single_price, max_nper', 'required'),
            array('column_id, max_nper, current_nper, brand_id, goods_number, is_on_sale, is_closed, is_hot, recommended, sort_order, limit_number', 'numerical', 'integerOnly' => true),
            array('goods_sn, after_name_style', 'length', 'max' => 60),
            array('goods_name', 'length', 'max' => 80),
            array('after_name','length','max'=>50),
            array('keywords', 'length', 'max' => 255),
            array('shop_price, integral, add_time, last_update, sales_time', 'length', 'max' => 200),
            array('single_price', 'length', 'max' => 6),
//            array('shop_price','compare', 'compareValue'=>35,'operator'=>'>'),
            array('single_price,shop_price,max_nper','compare', 'compareValue'=>0,'operator'=>'>'),
            array('single_price','compare','compareAttribute'=>'shop_price','operator'=>'<='),
            array('limit_number','compare', 'compareValue'=>0,'operator'=>'>='),
            array('close_reason', 'length', 'max' => 200),
            array('announced_time', 'length', 'max' => 11),
            array('goods_desc','length','max'=>100000),
            array('max_nper','numerical','max'=>60000),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('goods_id, column_id, goods_sn, goods_name, after_name, after_name_style, keywords, shop_price, single_price, max_nper, current_nper, brand_id, goods_number, goods_desc, is_on_sale, integral, is_closed, close_reason, is_hot, recommended, sort_order, add_time, last_update, announced_time, limit_number, sales_time,startTime,endTime', 'safe', 'on' => 'search'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'goods_id'          =>  '商品ID',
            'column_id'         =>  '所属栏目',
            'brand_id'          =>  '所属品牌',
            'goods_name'        =>  '商品标题',
            'after_name'        =>  '副标题',
            'keywords'          =>  '关键字',
            'shop_price'        =>  '商品总价',
            'single_price'      =>  '商品单次价格',
            'max_nper'          =>  '最大期数',
            'goods_desc'        =>  '商品内容详情',
            'current_nper'      =>  '当前期数',
            'recommended'       =>  '人气推荐',
            'announced_time'    =>  '限时揭晓设置',
            'limit_number'      => '限购数量',
            'sales_time'        => '设置上架时间',
            'sort_order'        => '排序'
        );
    }
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public $startTime,$endTime;
    
    public function search()
    {
        $criteria=new CDbCriteria;
        $criteria->compare('t.goods_id', $this->goods_id);
        //$criteria->compare('t.add_time','>='.  strtotime($this->startTime));
        //$criteria->compare('t.add_time','<='.  strtotime($this->endTime));
		$searchDate = Tool::searchDateFormat($this->startTime, $this->endTime);
        $criteria->compare('t.add_time', ">=" . $searchDate['start']);
        $criteria->compare('t.add_time', "<=" . $searchDate['end']);
        $criteria->compare('goods_name', $this->goods_name,true);
        $criteria->compare('is_closed', self::IS_CLOSED_FALSE);
		$criteria->order = 't.add_time DESC' ;
        return new CActiveDataProvider($this,array(
            'pagination'=>array(
//                'pageSize'=>20
            ),
            'criteria'=>$criteria,
        ));
    }
    
	/*最新揭晓*/
	public static function getAnnouncedNew(){
		$time = time();
		$criteria = new CDbCriteria;
        $criteria->select = 't.goods_id,ogn.current_nper,FROM_UNIXTIME(ogn.sumlotterytime,"%Y-%m-%d %H:%i:%s") as sumlotterytime,
		                    ogn.status,t.goods_name,t.shop_price,t.single_price,t.max_nper,t.announced_time,t.sort_order';
        $criteria->join = "left join {{order_goods_nper}} ogn on ogn.goods_id = t.goods_id";
	    $criteria->compare('sumlotterytime', ">" .$time);
		$criteria->compare('ogn.status', YfzOrderGoodsNpers::STATUS_ING);
        return new CActiveDataProvider('yfzgoods', array(
            'pagination' => array(
                'pageSize' => 10,
            ),
            'criteria' => $criteria,
        ));
	}
    
    /**
     * 返回默认时间的 天 时 分
     * @param int $timestring 时间戳
     */
    public static function getTime($timestring)
    {
        if(!$timestring) return array('day'=>0,'hour'=>0,'minute'=>0);
        $time['day'] = floor($timestring/86400);
        $time['hour'] = floor(($timestring-$time['day']*86400)/3600);
        $time['minute'] =floor(($timestring-$time['day']*86400-$time['hour']*3600)/60);
        return $time;
    }
    
    /**
     * 根据期数，获取产品的售卖情况
     */
    public static function getCurrentSales($id,$nper)
    {
        $sql = "select sum(g.goods_number) as goods_number from {{order}} as o 
            left join {{order_goods}} as g on g.order_id=o.order_id 
            where g.goods_id={$id} and g.current_nper={$nper} and o.order_status=".YfzOrder::STATUS_PAY_SUCCESS;
        $result = Yii::app()->gwpart->createCommand($sql)->queryRow();
        return empty($result['goods_number']) ? 0 : $result['goods_number'];   
    }
    
    public $goods_thumb;
    /**
     * 首页推荐产品
     * @param type $limit init
     * @return object
     */
    public static function getRecommendedGoods($limit)
    {
        $recommended = self::model()->findAll(array(
                        'select'=>'goods_name,t.goods_id,single_price,shop_price,t.sort_order,g.goods_thumb as goods_thumb,t.current_nper',
                        'join' => 'left join {{goods_image}} g on g.goods_id=t.goods_id',
                        'condition'=>'is_closed=:is and recommended=:rec and is_on_sale=:on',
                        'params'=>array(
                            ':is'=>self::IS_CLOSED_FALSE,
                            ':rec'=> self::IS_RECOMMENDED_TRUE,
                            ':on'=> self::IS_SALES_TRUE
                        ),
                        'limit'=>$limit,
                        'order'=> 't.sort_order desc,t.add_time desc'
                    ));
        return $recommended;
    }
    
    /**
     * 根据栏目或者当价格获取商品
     * @return \CActiveDataProvider
     */
    public function searchAnnoucned($limit= CPagination::DEFAULT_PAGE_SIZE,$limit_number=false)
    {
        $page = Yii::app()->request->getParam('page',1);
        $offer = ($page-1)*$limit;
        $find = array(
            'select' => 't.goods_id,t.goods_name,g.goods_thumb,t.shop_price,t.single_price,t.current_nper,t.add_time,t.sort_order',
            'join'  => 'left join {{goods_image}} as g on g.goods_id = t.goods_id',
            'limit' => $limit,
            'offset'=> $offer,
            'order'=> 't.sort_order desc,t.add_time desc'
        );
        $find['condition'] = 't.is_closed=:closed and t.is_on_sale=:sale';
        if ($limit_number){
            $find['condition'] = 't.is_closed=:closed and t.is_on_sale=:sale and t.limit_number > 0';
        }
        $find['params'] = array(':closed'=>self::IS_CLOSED_FALSE,':sale'=>self::IS_SALES_TRUE);

        if((int)$this->column_id){
            $find['condition'] .= ' and t.column_id=:column';
            $find['params'][':column'] = $this->column_id;
        }
        if((float)$this->single_price){
            $find['condition'] .= ' and t.single_price=:single';
            $find['params'][':single'] = $this->single_price;
        }
        return self::model()->findAll($find);
    }
    /**
     * 产品名称
     * @param type $id
     * @return string
     */
    public static function getName($id)
    {
        $model = self::model()->findByPk($id);
        return $model->goods_name;
    }
    
    public static function getGoods($id)
    {
        $model = self::model()->findByPk($id);
        return $model ? $model : self::model();
    }
	
	/*购买总价*/
	public static function getGoodsy($id)
    {
        $model = self::model()->findByPk($id);
        return $model->shop_price;
    }
}   
    
    
 
