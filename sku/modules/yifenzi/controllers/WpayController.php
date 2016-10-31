<?php 
/**
   * 微信支付
   * ==============================================
   * 编码时间:2016年4月26日 
   * ------------------------------------------------------------------------------------
   * 公司源码文件，未经授权不许任何使用和传播。
   * ==============================================
   * @date: 2016年4月26日
   * @author: Derek
   * @version: G-emall child One Parts 1.0.0
   * @return:
   **/
class WpayController extends YfzController{
    public function actionIndex(){
//        Yii::app()->user->setState("openID","dsdsds");
//        print_r(Yii::app()->user->getState("openID"));exit;
//        $this->layout = false;
//        print_r(DOMAIN_YIFENZI.Yii::app()->createUrl('/yifenzi/order/wxnotifyurl'));exit;
//        Tool::post('http://www.g1fz.com/order/wxnotifyurl',array("dadfas"=>"dafsdfa"));exit;
//        $a = file_get_contents('http://www.g1fz.com/order/wxnotifyurl');
//        print_r($a);exit;
//
//        $d = DOMAIN_YIFENZI."/order/wxnotifyurl";
//        $sql = "insert into ".YIFENZI.".gw_yifenzi_order_log (remark) values('{$d}')";
//        Yii::app()->db->createCommand($sql)->execute();exit;
//
//        try {
//            Yii::import('comext.WxpayAPI_php_v3.lib.*',1);
//            Yii::import('comext.WxpayAPI_php_v3.cert.*',1);
//            require_once "WxPay.Api.php";
//            require_once "WxPay.JsApiPay.php";
//            $jsApiParameters = "";
//            //①、获取用户openid
//            $tools = new JsApiPay();
//            $openId = $tools->GetOpenid();
//            //         //②、统一下单
//            $input = new WxPayUnifiedOrder();
//            $input->SetBody("test");
//            $input->SetAttach("test");
//            $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
//            $input->SetTotal_fee("1");
//            $input->SetTime_start(date("YmdHis"));
//            $input->SetTime_expire(date("YmdHis", time() + 600));
//            $input->SetGoods_tag("test");
//            $input->SetNotify_url("http://www.gnet-mall.net/reslog/log");
//            $input->SetTrade_type("JSAPI");
//            $input->SetOpenid($openId);
//            $order = WxPayApi::unifiedOrder($input);
//            $this->printf_info($order);
//            $jsApiParameters = $tools->GetJsApiParameters($order);
//            print_r($jsApiParameters);
//        } catch (Exception $e) {
//            print_r($e->getMessage());exit;
//        }
//
//        $this->render('index', array('jsApiParameters' => $jsApiParameters));
    }
    
    //打印输出数组信息
    function printf_info($data)
    {
        foreach($data as $key=>$value){
            echo "<font color='#00ff55;'>$key</font> : $value <br/>";
        }
    }

    /**
     * 生成订单号检验订单号唯一性
     *
     * @return string
     */
    protected function get_order_sn()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        $timestamp = time() - date('Z');
        $y = date('y', $timestamp);
        $z = date('z', $timestamp);
        $order_sn = $y . str_pad($z, 3, '0', STR_PAD_LEFT) . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        // 检验是否有此单号
        $sql = "SELECT order_sn

                FROM gw_yifenzi_order

                WHERE order_sn='{$order_sn}'";

        $boolear = Yii::app()->gwpart->createCommand($sql)->execute();

        if ($boolear) {
            return $this->get_order_sn();
        }

        return $order_sn;
    }


    public function actionCz(){

    }
}