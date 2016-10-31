<?php
return array(
    "busi_code" =>  "PAY",      //业务代码
    "merchant_no"   =>  "549440153990197",     //商户号
    'terminal_no'   => "20000151",      //终端号
    'key'   =>  "d9aacf8109b15926202e0f9b69072290",         //商户密钥KEY
    'return_url'    =>  DOMAIN_YIFENZI.'/order/returnurl/',       //交易完成后页面即时通知跳转的URL
    'notify_url'    =>  DOMAIN_YIFENZI.'/order/notifyurl/',     //接收后台通知的URL
    'currency_type'  =>  'CNY',     //货币代码，人民币：CNY
    'client_ip'     =>  '',       //创建订单的客户端IP（消费者电脑公网IP，用于防钓鱼支付）
    'sign_type'     =>  'SHA256',       //签名算法（暂时只支持MD5）
    'bank_code'     =>  '',     //直连银行参数
    'amount'        =>  '0.00',      //订单金额
    'base64_memo'   =>  '',     //商品备注
    'order_no'      =>  '',     //订单号
    'product_name'  =>  '',
//    'url'       =>  'http://120.31.132.114:8080/entry.do',     //支付域
    'url'   =>  'https://epay.gaohuitong.com:8443/entry.do',
);