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
        $this->layout = false;

        try {
            Yii::import('comext.WxpayAPI_php_v3.lib.*',1);
            Yii::import('comext.WxpayAPI_php_v3.cert.*',1);
            require_once "WxPay.Api.php";
            require_once "WxPay.JsApiPay.php";
            $jsApiParameters = "";
            //①、获取用户openid
            $tools = new JsApiPay();
            $openId = $tools->GetOpen2id();
            //         //②、统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody("test");
            $input->SetAttach("test");
            $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
            $input->SetTotal_fee("1");
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");
            $input->SetNotify_url("http://www.gnet-mall.net/reslog/log");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            $this->printf_info($order);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            print_r($jsApiParameters);
        } catch (Exception $e) {
            print_r($e->getMessage());exit;
        }

        $this->render('index', array('jsApiParameters' => $jsApiParameters));
    }
    
    //打印输出数组信息
    function printf_info($data)
    {
        foreach($data as $key=>$value){
            echo "<font color='#00ff55;'>$key</font> : $value <br/>";
        }
    }
    
}