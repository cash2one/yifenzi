<?php
/**
   * 购物车页面
   * ==============================================
   * 编码时间:2016年4月5日 
   * ------------------------------------------------------------------------------------
   * 公司源码文件，未经授权不许任何使用和传播。
   * ==============================================
   * @author: Derek
   * @version: G-emall child One Parts 1.0.0
   **/
class CartsController extends YfzController
{
    public function actionIndex(){
        $yfzCart = YfzCart::getCart();
        $goodsList = array();
        $this->footerPage = 4;
        if ( $yfzCart->itemList() ){
//             //得到购物车中单个商品的当前期数
            $goodsList=$yfzCart->itemList();
            if ($goodsList) {
                foreach ( $goodsList as $k=>$v ){
                    $goodsData =  YfzGoods::model()->find(array(
                        "select"    =>  array('goods_id','current_nper','goods_number'),
                        "condition" =>  "goods_id=$k",
                    ));

                    if ( !$goodsData ) continue;
                    $goodsData = json_decode(CJSON::encode($goodsData), true);
                
                    
                    if (isset($goodsList[$k]['goods_thumb'])){
                        //商品图片
                        $goodsList[$k]['goods_thumb'] = ATTR_DOMAIN . '/' . $goodsList[$k]['goods_thumb'];
                    }
                    else{
                        $goodsList[$k]['goods_image'] = ATTR_DOMAIN . '/' . $goodsList[$k]['goods_image'];
                    }
                    //更新此商品剩余库存
                    $goodsList[$k]['goods_number'] = $goodsData['goods_number'];
                
                    //判断购物车中的数据对比原数据的期数
                    $goodsList[$k]['current_nper_desc'] = "[第{$v['current_nper']}期]";
                
                    if ( $goodsData['current_nper'] != $v['current_nper']){
                        $goodsList[$k]['current_nper_desc'] = "[已更新至{$goodsData['current_nper']}期]";
                    }
                }
            }
        }
//       print_r($goodsList);exit;
        $this->render("index", array("goodsdata"=>$goodsList));
    }
    
    
    public function actionAddgoods(){
        $goodsObj = Yfzgoods::model()->findAll(array("condition"=>"is_hot=1"));
        $products = json_decode(CJSON::encode($goodsObj),TRUE);
        $this->render('addgoods',array("goodsData"=>$products));
    }
    
    
    /**
     * 前台ajax添加一个商品，兼容登陆与否二种状态
     */
    public function actionAjaxadd(){
//         $yfzCart = YfzCart::getCart();
//         print_r($yfzCart);
        //过滤是否为Ajax请求,验证商品
        if ( Yii::app()->request->isAjaxRequest){
//            $starttime = explode(' ',microtime());
            $goods_id = Yii::app()->request->getParam('goods_id') ? (int)Yii::app()->request->getParam('goods_id') : 0;
            
            //拿ID得出商品数据
//            $sql = "select g.*,gm.goods_thumb,gm.goods_id as gm_goods_id from gw_yifenzi_yfzgoods as g left join gw_yifenzi_goods_image as gm on g.goods_id=gm.goods_id where g.goods_id = {$goods_id}";
            $sql = "select g.goods_id,g.goods_sn,g.limit_number,g.goods_name,shop_price,single_price,current_nper,goods_number,gm.goods_thumb,gm.goods_id as gm_goods_id ";
            $sql .= "from gw_yifenzi_yfzgoods as g left join gw_yifenzi_goods_image as gm on g.goods_id=gm.goods_id where g.goods_id = {$goods_id}";
            $goodsData = Yii::app()->gwpart->createCommand($sql)->queryRow();

            if ( !$goodsData ){
                echo json_encode(array("status"=>1,"msg"=>"添加失败!"));
                exit;
            }
            
            $yfzCart = YfzCart::getCart();
            
            if (!$yfzCart->addItem($goodsData, $goods_id) ){
                echo json_encode(array("status"=>1,"msg"=>"添加失败"));
                exit;
            }
            echo json_encode(array("status"=>2,"msg"=>"添加成功"));
            exit;
        }
    }
    
    /**
     * ajax相减购物车中一个商品
     */
    public function actionAjaxdel(){
        if ( Yii::app()->request->isAjaxRequest ){

            $goods_id = Yii::app()->request->getParam('goods_id') ? (int)Yii::app()->request->getParam('goods_id') : 0;
            $types = Yii::app()->request->getParam("types") ? Yii::app()->request->getParam("types") : false;
            
            $goodsData = YfzGoods::model()->find(array(
                "select"    =>  "goods_id",
                "condition" =>  "goods_id={$goods_id}"
            ));
            
            if ( !$goodsData ) die(false);
            
            $yfzCart = YfzCart::getCart();
            
            if ($types == "goods"){
                if ($yfzCart->delGoods($goods_id)) die(true);
                die(false);
            }else{
                if ( $yfzCart->reduceItem($goods_id)) die(true);
                die(false);
            }
        }
    }
    /**
     * ajax下单前检查商品数量
     */
    public function actionAjaxGoodsnum(){
        if ( Yii::app()->request->isAjaxRequest ){
            $goods_id = Yii::app()->request->getParam('goods_id') ? (int)Yii::app()->request->getParam('goods_id') : 0;
            $carr_goods_num = Yii::app()->request->getParam('carr_goods_num') ? (int)Yii::app()->request->getParam('carr_goods_num') : 0;

            $sql = "select goods_number from gw_yifenzi_yfzgoods where goods_id = {$goods_id}";
            $goodsData = Yii::app()->gwpart->createCommand($sql)->queryRow();
            if ( !$goodsData ){
                echo json_encode(array("status"=>1,"msg"=>"添加失败"));
                exit;
            }
            if($carr_goods_num > $goodsData['goods_number']){
                $user_id = Yii::app()->user->id;
                file_put_contents("ttxt.php",$carr_goods_num.'//'.$goodsData['goods_number']);
                //更新购物车的数量
                $_cModel = Cart::model()->find(array("condition"=>"goods_id={$goods_id} and member_id={$user_id}"));
                $_cModel->num = $goodsData['goods_number'];
                if($_cModel->save()) {
                    echo json_encode(array("status" => 404, "msg" => "购买数量大于库存", "num" => $goodsData['goods_number']));
                    exit;
                }else{
                    echo json_encode(array("status" => 1, "msg" => "更新购物车失败"));
                    exit;
                }
            }else{
                echo json_encode(array("status"=>4,"msg"=>"检查通过"));
                exit;
            }
        }
    }
	/**
	*输入购买份额
	**/
	public function actionInput(){

			$goods_id = Yii::app()->request->getParam('goods_id') ? (int)Yii::app()->request->getParam('goods_id') : 0;
			$input_num = Yii::app()->request->getParam('input_num') ? (int)Yii::app()->request->getParam('input_num') : 0;
			//拿ID得出商品数据
            $sql = "select limit_number,shop_price,single_price,goods_number from gw_yifenzi_yfzgoods where goods_id=".$goods_id;
            $goodsData = Yii::app()->gwpart->createCommand( $sql )->queryRow();
            $_goods_number = ($goodsData['shop_price'] * 1) / ($goodsData['single_price'] * 1);
			$limit_number = $goodsData['limit_number'];

            if (empty($goodsData)){
                return false;
            }
             //判断输入购买份额是否大于商品库存
			 $yfzCart = YfzCart::getCart();
           if ($input_num > $_goods_number){
                echo json_encode(array("status"=>3,"msg"=>"输入购买份额不能大于库存"));
                exit;
				
            }
            if (isset($limit_number) && $limit_number > 0){
                if ($input_num > $limit_number || $input_num > $_goods_number){
                    echo json_encode(array("status"=>5,"msg"=>"输入购买份额不能大于限购量以及库存"));
                    exit;
                }
            }

			if ($yfzCart->inputItem($goods_id,$input_num)){
                echo json_encode(array("status"=>6,"msg"=>""));
//                exit;
            }

	}

    public function actionInputgoodsnums(){
        if ( Yii::app()->request->isAjaxRequest ){
            $goods_id = Yii::app()->request->getParam('goods_id') ? (int)Yii::app()->request->getParam('goods_id') : 0;
            $input_num = Yii::app()->request->getParam('input_num') ? (int)Yii::app()->request->getParam('input_num') : 0;

            //拿ID得出商品数据
            $sql = "select g.*,gm.goods_thumb,gm.goods_id as gm_goods_id from";
            $sql .= " gw_yifenzi_yfzgoods as g left join gw_yifenzi_goods_image as gm on g.goods_id=gm.goods_id where g.goods_id = {$goods_id}";
            $goodsData = Yii::app()->gwpart->createCommand($sql)->queryRow();

            if ( !$goodsData ){
                echo json_encode(array("status"=>1,"msg"=>"添加失败"));
                exit;
            }

            $yfzCart = YfzCart::getCart();

            if (!$yfzCart->addItem($goodsData, $goods_id, $input_num) ){
                echo json_encode(array("status"=>1,"msg"=>"添加失败"));
                exit;
            }

            echo json_encode(array("status"=>2,"msg"=>"添加成功"));
            exit;
        }
    }
	

    
    /**
      * 一份子低部通栏中关于购物车中数量的显示。根据用户在登陆状态来得到不同情况下购物车中所选商品个数（以单个商品为单位）
      * ====================================================================================
      * 编码时间:2016年4月25日 
      * ------------------------------------------------------------------------------------
      * 公司源码文件，未经授权不许任何使用和传播。
      * ====================================================================================
      * @author: Derek
      * @version: G-emall child One Parts 1.0.0
      * @return:
      **/
    public function actionGetcartnums(){
        $yfzCart = YfzCart::getCart();
            $spec = $yfzCart->getType();
        
        if (!$spec) die(0);
        echo($spec);
    }
}