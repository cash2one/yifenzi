<?php
/**
   * 购物车单体类：
   * 商品添加、商品列表、商品验证、商品数量、商品各类、商品总金额等
   * ====================================================================================
   * 编码时间:2016年4月5日 
   * ------------------------------------------------------------------------------------
   * 公司源码文件，未经授权不许任何使用和传播。
   * ====================================================================================
   * @date: 2016年4月5日
   * @author: Derek
   * @version: G-emall child One Parts 1.0.0
   **/
class YfzCart{
    static public $ins = null;
    public $rand = 0;
    public $item = array();
    static public $member_id = null;

    public function __construct(){
        $this->rand = mt_rand(0,9999);
    }
    
    public static function getIns(){
        if(!(self::$ins instanceof self)){
            self::$ins = new self();
            return self::$ins;
        }
        return self::$ins;
    }
    
    /**
     * 对面进行获取实例(公开)
     */
    public static function getCart(){
//         self::$member_id = Yii::app()->user->id;
//         if ( Yii::app()->user->id ){
//             $yfzcart = new YfzSessionCart(); 
//             return $yfzcart;
//         }else{
//             if(!isset($_SESSION['yfzcart']) || !($_SESSION['yfzcart'] instanceof self)){
//                 return $_SESSION['yfzcart'] = self::getIns();
//             }
//             return $_SESSION['yfzcart'];
//         }
        self::$member_id = Yii::app()->user->id;
        if ( Yii::app()->user->id ){
            $yfzcart = new YfzSessionCart();
            return $yfzcart;
        }else{
            $a = Yii::app()->user->getState('yfzcart');
            if(!$a || !($a instanceof self)){
//                 return $_SESSION['yfzcart'] = self::getIns();
                Yii::app()->user->setState('yfzcart',self::getIns());
            }
            return Yii::app()->user->getState('yfzcart');
        }
        
    }
    
    /**
     * 往购物车中添加 一个商品、如果此商品在购物车中存大那么且加购买数量
     * @param array $data 商品数据
     * @param Number $goods_id 商品ID
     * @param number $nums 购物数量
     * @return boolean
     */
    public function addItem(array $data, $goods_id, $nums=1){
        //验证此商品是不是有限购
        $sql = "select limit_number,shop_price,single_price,goods_number from gw_yifenzi_yfzgoods where goods_id=".$goods_id;
        $GoodsData = Yii::app()->gwpart->createCommand( $sql )->queryRow();
        
        if (!($GoodsData['goods_number'])){
            echo json_encode(array("status"=>1,"msg"=>"购买数量不能大于库存"));
            exit;
        }

        //购物车中有此商品
        if ( $this->Initem( $goods_id ) != false ){
            
            //用这个商品的市场价格 “/”每人次得出来库存，注意二者取余不能大于0
            $_goods_number = ($GoodsData['shop_price'] * 1) / ($GoodsData['single_price'] * 1);

            //如是是手动添加的购买数量那行进行减除以前的数量
            if ($GoodsData['limit_number']) {
                if ($nums > 1 && $nums > ($GoodsData['limit_number'] * 1)) {
                    echo json_encode(array("status" => 1, "msg" => "不能大于限购数"));
                    exit;
                }
            }
            if ( $nums > 1 && $nums > $_goods_number ){
                echo json_encode(array("status"=>1,"msg"=>"购买数量不能大于库存"));
                exit;
            }

            if (($nums*1) > 1){
                $this->reduceItem($goods_id, $this->item[$goods_id]['num']);
            }

//            echo json_encode(array("status"=>1,"msg"=>$this->item[$goods_id]['num']));
//            exit;
            if ($GoodsData['limit_number']){
                if ((($this->item[$goods_id]['num']*1) + $nums) > $GoodsData['limit_number']){
                    //echo json_encode(array("status"=>1,"msg"=>"每人只能买".$this->item[$goods_id]['num'].'份'));
                    echo json_encode(array("status"=>1,"msg"=>"不能大于限购数"));
                    exit;
                }
            }


            //判断购买数量是否大于商品库存
            if (($this->item[$goods_id]['num'] + $nums) > $_goods_number){
                echo json_encode(array("status"=>1,"msg"=>"购买数量不能大于库存"));
                exit;
            }
            $this->item[$goods_id]['num'] += $nums;
            return true;
        }

        $this->item[$goods_id] = array();
        $this->item[$goods_id] = $data;
        $this->item[$goods_id]['num'] = $nums;
        return true;
    }
    
    /**
     * 购物车中商品列表
     */
    public function itemList(){
        return $this->item ? $this->item : array();
    }
    
    /**
     * 购物车中检验是否存在此商品
     * @param Number $goods_id
     * @return boolean
     */
    public function Initem( $goods_id ){
        //先判断是否有类型商品
        if ($this->getType() <= 0 ) return false;
        
        if ( !array_key_exists($goods_id, $this->item) ){
            return false;
        }
        return true;
    }
    
    /**
     * 计数购物车中所有商品总金额
     * @return void|number
     */
    public function getItemPrice(){
        if ( count($this->item) <= 0 ) return ;
        
        $tmpPrice = array();
        foreach ( $this->item as $k=>$v ){
            array_push($tmpPrice, ((($v['num'] * 100 ) * ( $v['single_price'] * 100 )) / 10000));
        }
        return array_sum($tmpPrice);
    }
    
    /**
     * 返回一个商品类型的购物买数量相减操作
     * @param Number $goods_id
     * @param Number $num
     */
    public function reduceItem( $goods_id, $num=1 ){
        //如果这个商品没有购物车中，直接以False退出
        if( $this->Initem($goods_id) == false ){
            return;
        }
        
        if( $num > $this->getNum($goods_id) || $this->getNum($goods_id) == 1 ){
//             unset($this->item[$goods_id]);
            return false;
        }else{
            return $this->item[$goods_id]['num'] -=$num;
        }
    }
	
	/**
     * 输入购买份额
     * @param Number $goods_id
     * @param Number $num
     */
    public function inputItem( $goods_id, $num ){
         //验证此商品是不是有限购
        if ( $this->Initem( $goods_id ) != false ){
            $this->item[$goods_id]['num'] = $num;
            return true;
        }
    }
    
    /**
     * 删除一个商品
     * @param Number $goods_id
     */
    public function delGoods( $goods_id ){
        if($this->Initem( $goods_id )){
            unset($this->item[$goods_id]);
            return true;
        }
    }
    
    /**
     * 返回一个商品类型的购物买数量
     * @param Number $goods_id
     */
    public function getNum( $goods_id ){
        return $this->item[$goods_id]['num'];
    }
    
    //清空购物车
    public function Emptyitem(){
        $this->item = array();
    }
    
    /**
     * 返回购物车中商品种类
     */
    public function getType(){
        return count( $this->item );
    }
    
    /**
     * 用户在登陆成功之后的一个数据同步操作
     * @return boolean
     */
    static public function syncData(){
        self::$member_id = Yii::app()->user->id;
        if (!self::$member_id) return false;
//         $yfzcart = isset($_SESSION['yfzcart']) ? $_SESSION['yfzcart'] : false;
        $yfzcart = Yii::app()->user->getState('yfzcart');
        
        if ( !$yfzcart ) return false;
        $cartData = $yfzcart->itemList();
        
        $yfzcartModel = new Cart();
        
        foreach ( $cartData as $k=>$v ){
            //拿session中的商品对比数据表中的商品看是否有存大的。如果没有那么session中的这个商品insert到数据表中,否则其它数据不作处理
            $tcartData = Cart::model()->find(array("condition"=>"goods_id={$k} and member_id=".self::$member_id)); 
            if ( !$tcartData ){
                $_data = array();
                $_data['goods_id'] = $v['goods_id'];
                $_data['member_id'] = self::$member_id;
                $_data['goods_sn'] = $v['goods_sn'];
                $_data['goods_name'] = $v['goods_name'];
                $_data['single_price'] = $v['single_price'];
                $_data['num'] = $v['num'];
                $_data['cart_type'] = 1;
                $_data['goods_image'] = $v['goods_thumb'];
                $_data['add_time'] = time();
                $_data['current_nper'] = $v['current_nper'];
                $cart_id = Yii::app()->gwpart->createCommand()->insert("gw_yifenzi_cart", $_data);
                
                //加购物车，进行商品统计
                if ( $cart_id ){
                    $yfzcartModel->cartLog($v['goods_id']);
                }
            }
        }
        
        //清空数据
        $yfzcart->Emptyitem();
    }
}