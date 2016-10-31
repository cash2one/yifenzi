<?php

/**
 * 商家订单管理模块
 * @author leo8705
 */
class OrderController extends PController {

    public $showExport = false;
    public $exportAction;

    public function init() {
        $this->pageTitle = Yii::t('partnerModule.order', '小微企业联盟');
        $this->curr_menu_name = '/partner/order/index';
    }

    /**
     * 订单列表
     */
    public function actionIndex() {
        $this->pageTitle = Yii::t('partnerModule.order', '订单管理 _ ') . $this->pageTitle;
        $model = new Order('search');
        $model->unsetAttributes();

        if (isset($_GET['Order'])) {
            $model->attributes = $this->getQuery('Order');
        } elseif (isset($_GET['Refund'])) {
            $model->refund_status = $this->getQuery('Refund');
        }


        $c = $model->searchSold($this->curr_act_partner_id);
        $count = $model->count($c);

        $pages = new CPagination($count);
//        $pages->pageSize = 1;
        $pages->applyLimit($c);
        /* $model->machine;
          $model->store;
          $model->freshMachine; */
        $orders = $model->findAll($c);
        //全部
        $allNum = $model->countByAttributes(array('partner_id' => $this->curr_act_partner_id));
        //新订单
        $newNum = $model->countByAttributes(array('partner_id' => $this->curr_act_partner_id, 'status' => $model::STATUS_NEW));

        //已支付
        $payNum = $model->countByAttributes(array('partner_id' => $this->curr_act_partner_id, 'status' => $model::STATUS_PAY));

        //已发货
        $sendNum = $model->countByAttributes(array('partner_id' => $this->curr_act_partner_id, 'status' => $model::STATUS_SEND));

//        //申请退款
//        $refundingNum =  $model->countByAttributes(array('partner_id' => $this->curr_act_partner_id, 'status' => $model::STATUS_REFUNDING));
//        
        //已退款
        $refundedNum = $model->countByAttributes(array('partner_id' => $this->curr_act_partner_id, 'refund_status' => $model::REFUND_STATUS_SUCCESS));

        //完成
        $completeNum = $model->countByAttributes(array('partner_id' => $this->curr_act_partner_id, 'status' => $model::STATUS_COMPLETE));

        //已取消
        $cancelNum = $model->countByAttributes(array('partner_id' => $this->curr_act_partner_id, 'status' => $model::STATUS_CANCEL));

        //订单失效
//        $invalidNum = $model->countByAttributes(array('partner_id'=>$this->curr_act_partner_id,'status'=>$model::STATUS_INVALID));
        //订单冻结 
        $frozenNum = $model->countByAttributes(array('partner_id' => $this->curr_act_partner_id, 'status' => $model::STATUS_FROZEN));


        $this->showExport = false;
        $this->exportAction = 'export';
        $totalCount = $count;
        $exportPage = new CPagination($totalCount);
        $exportPage->route = 'order/export';
        $exportPage->params = array_merge(array('grid_mode' => 'export'), $_GET);
        $exportPage->pageSize = 5000;
        $this->render('index', array(
            'orders' => $orders,
            'model' => $model,
            'newNum' => $newNum,
            'sendNum' => $sendNum,
            'payNum' => $payNum,
            'refundedNum' => $refundedNum,
            'completeNum' => $completeNum,
            'cancelNum' => $cancelNum,
            'allNum' => $allNum,
//            'invalidNum'=>$invalidNum,
            'frozenNum' => $frozenNum,
            'pages' => $pages,
            'totalCount' => $totalCount,
            'exportPage' => $exportPage
        ));
    }

    /**
     * 发货
     */
    public function actionSend() {
        $code = $this->getPost('code');
        $msg = array(); //返回json状态
        /** @var $model Order */
        $model = Order::model()->find('code=:code', array(':code' => $code));

        if ($model->partner_id != $this->curr_act_partner_id) {
            $msg['error'] = Yii::t('partnerModule.order', '非法操作！');
            exit(CJSON::encode($msg));
        }

        if ($model->status != Order::STATUS_PAY) {
            $msg['error'] = Yii::t('partnerModule.order', '该订单不允许发货！');
            exit(CJSON::encode($msg));
        }

        //更新发货状态
        $rs = Yii::app()->db->createCommand()->update(Order::tableName(), array('status' => Order::STATUS_SEND), 'id=:id', array(':id' => $model->id));

        if ($rs) {
            //添加操作日志
            ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '发货:' . $model->code);

            //发送短信
            $apiMember = new ApiMember();
            $mobile = $model['mobile'];
            if (empty($mobile)) {
                //$memberInfo = $apiMember->getInfo($model['member_id']);
                $memberInfo = Member::model()->findByPk($model['member_id']);
                $mobile = $memberInfo['mobile'];
            }

            if (!empty($mobile)) {
//            	$apiMember->sendSms($memberInfo['mobile'], '您好，你的微小企订单['.$model['code'].']已发货，请准备收货。');
                $msg = '您好，你的微小企订单[' . $model['code'] . ']已发货，请准备收货。';
                $apiMember->sendSms($memberInfo['mobile'], $msg, ApiMember::SMS_TYPE_ONLINE_ORDER, 0, ApiMember::SKU_SEND_SMS, array($model['code']), ApiMember::SEND_SUCCESS);
            }


            $msg['success'] = Yii::t('partnerModule.order', '发货成功');
        } else {
            $msg['error'] = Yii::t('partnerModule.order', '发货失败');
        }

        exit(CJSON::encode($msg));
    }

    /**
     * 订单详情
     */
    public function actionDetail() {
        $this->pageTitle = Yii::t('partnerModule.order', '小微企业联盟');
        $id = $this->getParam('id');

        $model = Order::model()->findByPk($id);
        if ($model->partner_id != $this->curr_act_partner_id) {
            $msg['error'] = Yii::t('partnerModule.order', '非法操作！');
            exit(CJSON::encode($msg));
        }
        $address = OrderAddress::model()->findByPk($model['address_id']);
        $orders = $model->ordersGoods;


        $this->render('detail', array('orders' => $orders, 'model' => $model, 'address' => $address));
    }

    /**
     * ajax 关闭交易
     */
    public function actionClose() {

        $code = $this->getPost('code');
        $msg = array(); //返回json状态
        /** @var $model Order */
        $model = Order::model()->find('code=:code', array(':code' => $code));

        if ($model->partner_id != $this->curr_act_partner_id) {
            $msg['error'] = Yii::t('partnerModule.order', '非法操作！');
            exit(CJSON::encode($msg));
        }

        if ($model->status != Order::STATUS_NEW) {
            $msg['error'] = Yii::t('partnerModule.order', '该订单不允许关闭！');
            exit(CJSON::encode($msg));
        }

        //更新发货状态
        $rs = Yii::app()->db->createCommand()->update(Order::tableName(), array('status' => Order::STATUS_CANCEL), 'id=:id', array(':id' => $model->id));

        if ($rs) {
            //添加操作日志
            ParnetLog::create(ParnetLog::CAT_COMPANY, ParnetLog::logTypeUpdate, $model->id, '关闭订单:' . $model->code);
            $msg['success'] = Yii::t('partnerModule.order', '关闭成功');
        } else {
            $msg['error'] = Yii::t('partnerModule.order', '关闭失败');
        }

        exit(CJSON::encode($msg));
    }

    /**
     * 导出订单商品excel
     *
     */
    public function actionExport() {
        ini_set("memory_limit", "1024M");
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        Yii::import('comext.PHPExcel.*');


        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()
                ->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

        $model = new Order('search');
        $model->unsetAttributes();

        if (isset($_GET['Order'])) {
            $model->attributes = $this->getQuery('Order');
        }

        $c = $model->searchSold($this->curr_act_partner_id);
        $model->ordersGoods;
        $count = $model->count($c);
        $pages = new CPagination($count);
        $pages->setPageSize(5000);
//        if(isset($_GET['page'])){
//            $pages->setCurrentPage($_GET['page']-1);
//        }
//        $pages->setCurrentPage();
        $pages->applyLimit($c);
        $orders = $model->findAll($c);
//          var_dump($_GET['page']);die;
        //输出表头
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '订单编号')
                ->setCellValue('B1', '商品名称')
                ->setCellValue('C1', '条形码')
                ->setCellValue('D1', '订单类型')
                ->setCellValue('E1', '网点')
                ->setCellValue('F1', '订单金额')
                ->setCellValue('G1', '状态')
                ->setCellValue('H1', '支付状态')
                ->setCellValue('I1', '下单时间')
                ->setCellValue('J1', '支付方式')
//        ->setCellValue('I1', '机器id')
                ->setCellValue('K1', '货道编号')
                ->setCellValue('L1', '送货方式')
                ->setCellValue('M1', '联系电话')
        ;

        $num = 2;
        $n = 1;
        foreach ($orders as $key => $row) {
            $s = '';
            $gname = '';
            $data = $row->ordersGoods;
            if ($data) {
                foreach ($data as $v) {
                    if ($v->sg_outlets) {
                        $s .=$v->sg_outlets . ',';
                    }
                    if ($v->name) {
                        $gname .=$v->name . ',';
                    }
                }
                $s = rtrim($s, ',');
                $gname = rtrim($gname, ',');
            }
            if (empty($s)) {
                $s = '无';
            }
             if (empty($gname)) {
                $s = '无';
            }
            $store = $row->store;
            $machine = $row->machine;
            $freshMachine = $row->freshMachine;
            $goods_name = $row->ordersGoods;
            //处理未支付的支付方式
            $pay_type = $row['status'] == Order::PAY_STATUS_NO ? ' ' : Order::getPayType($row['pay_type']);

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $num, ' ' . $row['code'])
                    ->setCellValue('B' . $num, ' ' . $gname)
                    ->setCellValue('C' . $num, ' ' . $row['goods_code'])
                    ->setCellValue('D' . $num, Order::type($row['type']))
                    ->setCellValue('E' . $num, ' ' . $row['type'] == Order::TYPE_SUPERMARK ? $store['name'] : ($row['type'] == Order::TYPE_MACHINE ? $machine['name'] : ($row['type'] == Order::TYPE_FRESH_MACHINE ? $freshMachine['name'] : "未知")))
                    ->setCellValue('F' . $num, ' ' . $row['total_price'])
                    ->setCellValue('G' . $num, ' ' . Order::status($row['status']))
                    ->setCellValue('H' . $num, ' ' . Order::payStatus($row['pay_status']))
                    ->setCellValue('I' . $num, date('Y-m-d G:i:s', $row['create_time']))
                    ->setCellValue('J' . $num, ' ' . $pay_type)
//        		->setCellValue('I' . $num, ' ' .  $row['machine_id'])
                    ->setCellValue('K' . $num, ' ' . $s)
                    ->setCellValue('L' . $num, ' ' . Order::shippingType($row['shipping_type']))
                    ->setCellValue('M' . $num, ' ' . $row['mobile']);
            $n++;
            $num++;
        }
        unset($orders);
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle("订单列表");

        $name = date('YmdHis' . rand(0, 99999));
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

}

?>
