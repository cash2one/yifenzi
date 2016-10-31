<?php

/**
 * 用户登陆之后实例化的购物车类
 * ==============================================
 * 编码时间:2016年4月6日 
 * ------------------------------------------------------------------------------------
 * 公司源码文件，未经授权不许任何使用和传播。
 * ==============================================
 * @author: Derek
 * @version: G-emall child One Parts 1.0.0
 **/
class YfzSessionCart
{

    protected $member_id = null;

    protected $yfzgoodsModel = null;

    protected $cartModel = null;

    public function __construct()
    {
        $this->member_id = Yii::app()->user->id;
        $this->yfzgoodsModel = new YfzGoods();
        $this->cartModel = new Cart();
    }

    /**
     * 往购物车数据表里添加一个商品
     *
     * @param array $data
     *            (传输过来的商品数据)
     * @param Number $goods_id
     *            (商品ID)
     * @param number $nums
     *            (购物数量默认为1)
     * @return boolean
     */
    public function addItem(array $data, $goods_id, $nums = 1)
    {
        if (! $data)
            return false;
        $_num = $this->Initem($goods_id);
        
        //验证此商品是不是有限购
//        $sql = "select limit_number,shop_price,single_price,goods_number from gw_yifenzi_yfzgoods where goods_id=".$goods_id;
//        $GoodsData = Yii::app()->gwpart->createCommand( $sql )->queryRow();
        
        if ($data['goods_number']*1 < 1)
            return false;
        
        if ($_num == false) {

            $_data = array();
            $_data['goods_id'] = $data['goods_id'];
            $_data['member_id'] = $this->member_id;
            $_data['goods_sn'] = $data['goods_sn'];
            $_data['goods_name'] = $data['goods_name'];
            $_data['single_price'] = $data['single_price'];
            $_data['num'] = $nums;
            
            $_data['cart_type'] = 1;
            $_data['goods_image'] = $data['goods_thumb'];
            $_data['add_time'] = time();
            $_data['current_nper']  = $data['current_nper'];
            
            //入库之前如果有限购先判断
            if ($data['limit_number']){
                //当期商品的限购进行处理
//                $sql = "select og.goods_number,sum(og.goods_number) as nums from {{order}} as o left join {{order_goods}} as og on o.order_id = og.order_id ";
//                $sql .= " where o.member_id={$this->member_id} and og.goods_id={$goods_id} and og.current_nper={$data['current_nper']} and o.order_status=1";
//                $numberData  = Yii::app()->gwpart->createCommand($sql)->queryRow();          
                $numberData = $this->getLimitNums($goods_id, $data['current_nper']);

                if ( $numberData ){
//                    $tmpNum = $data['limit_number'] - $numberData['nums'];
                    $tmpNum = $data['limit_number'] - $numberData;

                    if ( ($_num + $nums) > $tmpNum )
                        die(json_encode(array("status"=>1,"msg"=>"不能大于商品限购量")));
                }
            }
            $cart_id = Yii::app()->gwpart->createCommand()->insert("gw_yifenzi_cart", $_data);
            
            // 如果insert成功那么我们进行商品统计
            if ($cart_id) {
                // 判断商品统计表中是否有此商品，如果没有直接insert一条否则update
                $goods_stat = Goods_statistics::model()->find(array(
                    "condition" => "goods_id={$goods_id}"
                ));
                if (! $goods_stat) {
                    Yii::app()->gwpart->createCommand()->insert("gw_yifenzi_goods_statistics", array(
                        "goods_id" => $goods_id,
                        "carts" => 1
                    ));
                } else {
                    Yii::app()->gwpart->createCommand()->update("gw_yifenzi_goods_statistics", array(
                        "carts" => ($goods_stat['carts'] + $nums)
                    ), "goods_id=:goods_id", array(
                        "goods_id" => $goods_id
                    ));
                }
                return true;
            } else {
                return false;
            }
        } else {
            //用这个商品的市场价格 “/”每人次得出来库存，注意二者取余不能大于0
            $_goods_number = ($data['shop_price'] * 1) / ($data['single_price'] * 1);

            //购物车中输入购买次数

            //如是是手动添加的购买数量那行进行减除以前的数量
            if ($data['limit_number']) {
                if ($nums > 1 && $nums > ($data['limit_number'] * 1)) {
                    echo json_encode(array("status" => 1, "msg" => "不能大于限购数"));
                    exit;
                }
            }
            if ( $nums > 1 && $nums > $_goods_number ){
                echo json_encode(array("status"=>1,"msg"=>"购买数量不能大于库存"));
                exit;
            }

            if (($nums*1) > 1){
//                $this->reduceItem($goods_id, $_num);
                $this->inputItem($goods_id, $_num);
                $_num = 0;
            }

            
            if ($data['limit_number']){
                if (($_num + $nums) > ($data['limit_number']*1))
                    die(json_encode(array("status"=>1,"msg"=>"不能大于商品限购量")));

                //当期商品的限购进行处理
//                $sql = "select og.goods_number,sum(og.goods_number) as nums from {{order}} as o left join {{order_goods}} as og on o.order_id = og.order_id ";
//                $sql .= " where o.member_id={$this->member_id} and og.goods_id={$goods_id} and og.current_nper={$data['current_nper']} and o.order_status=1";
//                $numberData  = Yii::app()->gwpart->createCommand($sql)->queryRow();

                $numberData = $this->getLimitNums($goods_id, $data['current_nper']);

                if ( $numberData ){
//                    $tmpNum = $data['limit_number'] - $numberData['nums'];
                    $tmpNum = $data['limit_number'] - $numberData;

                    if ( ($_num + $nums) > $tmpNum )
                        die(json_encode(array("status"=>1,"msg"=>"不能大于商品限购量")));
                }
            }
            //不能大于库存
            if (($_num + 1) > ($data['goods_number']*1)){
                die(json_encode(array("status"=>1,"msg"=>"不能大于商品库存")));
            }

            // 流程跑到这里，证明购物车有此商品。只要进行购物数量累计就可以了。
            return Yii::app()->gwpart->createCommand()->update("gw_yifenzi_cart", array(
                "num" => ($_num + $nums)
            ), "goods_id=:goods_id and member_id=:member_id", array(
                "goods_id" => $goods_id,
                "member_id"=>$this->member_id
            ));
        }
    }
    
    public function getLimitNums($goods_id, $current_nper){
        
        $sql = "select order_id from {{order}} where member_id={$this->member_id} and order_status = 1";
        $memberOrderData = Yii::app()->gwpart->createCommand($sql)->queryAll();

        $goodsNumber = 0;
        foreach ($memberOrderData as $k=>$v){
            $sql = "select goods_number from {{order_goods}} where order_id={$v['order_id']} and goods_id={$goods_id} and current_nper={$current_nper}";
            $memberOrderGoodsData = Yii::app()->gwpart->createCommand($sql)->queryRow();
            
            if ( $memberOrderGoodsData ){
                $goodsNumber += $memberOrderGoodsData['goods_number'];
            }
        }
        
        return $goodsNumber;
    }

    /**
     * 验证这个商品是否在购物车中存在
     *
     * @param Number $goods_id            
     */
    public function Initem($goods_id)
    {
        $goods_id = $goods_id ? intval($goods_id) : 0;
        $cartData = $this->cartModel->model()->find(array(
            "condition" => "goods_id={$goods_id} and member_id={$this->member_id}"
        ));
        
        if ($cartData)
            return $cartData["num"];
        return false;
    }
    
    public function itemList(){
        $cartData = $this->cartModel->model()->findAll(array("condition"=>"member_id={$this->member_id}"));
        if (!$cartData) return false;
        
        $cartData = json_decode(CJSON::encode($cartData), true);
        
        $tmpArr = array();
        foreach ($cartData as $k=>$v){
            $tmpArr[$v['goods_id']] = $v;
        }
        return $tmpArr;
    }

    /**
     * 删除单个商品
     * 
     * @param Number $goods_id            
     * @return bool
     */
    public function delGoods($goods_id)
    {
        return $this->cartModel->delonegoods($goods_id);
    }

    /**
     * 对已经添加到购物车中的商品进行-1操作
     * @param Number $goods_id
     * @param number $num
     */
    public function reduceItem($goods_id, $num = 1)
    {
        if ($this->getNum($goods_id) == 1 ) {
//             $this->delGoods($goods_id);
            return false;
        } else {
            $_cModel = Cart::model()->find(array("condition"=>"goods_id={$goods_id} and member_id={$this->member_id}"));
            $_cModel->num -= $num;
            return $_cModel->save();
        }
    }

    /**
     * 输入购买份额
     * @param $goods_id
     * @param int $num
     * @return bool
     */
    public function inputItem($goods_id, $num)
    {
        $_cModel = Cart::model()->find(array("condition"=>"goods_id={$goods_id}"));
        $_cModel->num = $num;
        $_cModel->save();
        return true;
    }

    /**
     * 得到一个商品的购物数量
     * @param Number $goods_id            
     * @return Number
     */
    public function getNum($goods_id)
    {
        $cartData = Cart::model()->find(array(
            "select" => "num",
            "condition" => "goods_id={$goods_id} and member_id={$this->member_id}"
        ));
        
        $cartData = json_decode(CJSON::encode($cartData), true);
        return $cartData['num'];
    }
    
    /**
     * 得到当前用户的购物车中商品种类
     * @return number 
     */
    public function getType(){
        $sql = "select count(member_id) as sepc from gw_yifenzi_cart where member_id=".Yii::app()->user->id;
        $cartData = Yii::app()->gwpart->createCommand($sql)->queryRow();
        
        if (!$cartData) $cartData['sepc'] = 0;
        return $cartData['sepc'];
    }
}