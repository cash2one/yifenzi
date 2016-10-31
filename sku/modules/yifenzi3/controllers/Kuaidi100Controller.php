<?php

/**
 * 快递100 回调数据处理
 */
class Kuaidi100Controller extends Controller
{

    public function actionIndex()
    {
        $code = $this->getParam('code');
        if (!$code) {
            throw new CHttpException(403, '没有订单号');
        }
        $params = str_replace('\\','',$this->getPost('param'));
        //测试返回数据
        //$params = '{"status":"polling","billstatus":"sending","message":"正在派件","lastResult":{"message":"ok","nu":"933346116516","ischeck":"0","condition":"H100","com":"shunfeng","status":"200","state":"5","data":[{"time":"2015-12-30 10:31:47","ftime":"2015-12-30 10:31:47","context":"正在派送途中,请您准备签收(派件人:叶剑锋,电话:18381880068)"},{"time":"2015-12-30 10:04:14","ftime":"2015-12-30 10:04:14","context":"快件到达 【达州市大竹县竹海路营业点】"},{"time":"2015-12-29 21:30:20","ftime":"2015-12-29 21:30:20","context":"快件离开【达州杨柳集散中心】,正发往 【达州市大竹县竹海路营业点】"},{"time":"2015-12-29 19:36:49","ftime":"2015-12-29 19:36:49","context":"快件到达 【达州杨柳集散中心】"},{"time":"2015-12-29 15:36:19","ftime":"2015-12-29 15:36:19","context":"快件离开【重庆渝北集散中心】,正发往 【达州杨柳集散中心】"},{"time":"2015-12-29 14:33:47","ftime":"2015-12-29 14:33:47","context":"快件到达 【重庆渝北集散中心】"},{"time":"2015-12-29 13:05:28","ftime":"2015-12-29 13:05:28","context":"快件离开【重庆江北尚品路营业点】,正发往 【重庆渝北集散中心】"},{"time":"2015-12-29 10:01:20","ftime":"2015-12-29 10:01:20","context":"顺丰速运 已收取快件"}]}}';
        if ($params) {
            //保存推送数据
            $data = json_decode($params, true);
            $insert = array();
            $insert['state'] = $data['lastResult']['state'];
            $insert['status'] = YfzOrderExpress::toStatus($data['status']);
            $insert['message'] = $data['message'];
            $insert['data'] = json_encode($data['lastResult']);
            $insert['update_time'] = time();
            $result = Yii::app()->db->createCommand()->update('gw_order_express', $insert, 'order_code=' . $code);
             echo  '{"result":"true","returnCode":"200","message":"成功"}';
          }else{
        	echo  '{"result":"false","returnCode":"500","message":"失败"}';
       }
    }


}